<?php include 'includes/header.php'; ?>
<?php include 'includes/navbar.php'; ?>

<div id="app">
  <!-- Cart Header -->
  <section class="bg-green-600 text-white py-16 mt-16">
    <div class="max-w-7xl mx-auto px-4 text-center">
      <h1 class="text-4xl font-bold mb-4">Shopping Cart</h1>
      <p v-if="user" class="text-xl">Review your items</p>
      <p v-else class="text-xl">Please login to view your cart</p>
    </div>
  </section>

  <!-- Cart Content -->
  <section class="py-12" v-if="user">
    <div class="max-w-7xl mx-auto px-4">
      <div v-if="cartItems.length === 0" class="text-center py-12">
        <i class="fas fa-shopping-cart text-6xl text-gray-300 mb-4"></i>
        <h3 class="text-xl text-gray-600">Your cart is empty</h3>
        <p class="text-gray-500 mb-6">Add some products to get started</p>
        <a href="products.php" class="bg-green-600 text-white px-6 py-3 rounded-lg hover:bg-green-700 transition">
          Continue Shopping
        </a>
      </div>

      <div v-else class="grid lg:grid-cols-3 gap-8">
        <!-- Cart Items -->
        <div class="lg:col-span-2">
          <div class="bg-white rounded-lg shadow">
            <div class="p-6 border-b">
              <h2 class="text-xl font-semibold">Cart Items ({{ cartItems.length }})</h2>
            </div>
            <div class="divide-y">
              <div v-for="item in cartItems" :key="item.id" class="p-6 flex items-center">
                <img :src="firstImage(item.images)" :alt="item.product_name" class="w-20 h-20 object-cover rounded">
                <div class="ml-4 flex-1">
                  <h3 class="font-semibold">{{ item.product_name }}</h3>
                  <p class="text-green-600 font-bold text-lg">₹{{ item.price }}</p>
                </div>
                <div class="flex items-center space-x-3">
                  <button @click="updateQuantity(item.id, item.quantity - 1)" 
                          :disabled="item.quantity <= 1"
                          class="w-8 h-8 rounded-full border flex items-center justify-center hover:bg-gray-100">
                    <i class="fas fa-minus text-sm"></i>
                  </button>
                  <span class="w-8 text-center">{{ item.quantity }}</span>
                  <button @click="updateQuantity(item.id, item.quantity + 1)" 
                          class="w-8 h-8 rounded-full border flex items-center justify-center hover:bg-gray-100">
                    <i class="fas fa-plus text-sm"></i>
                  </button>
                </div>
                <button @click="removeFromCart(item.id)" class="ml-4 text-red-600 hover:text-red-700">
                  <i class="fas fa-trash"></i>
                </button>
              </div>
            </div>
          </div>
        </div>

        <!-- Order Summary -->
        <div class="bg-white rounded-lg shadow h-fit">
          <div class="p-6 border-b">
            <h2 class="text-xl font-semibold">Order Summary</h2>
          </div>
          <div class="p-6 space-y-4">
            <div class="flex justify-between">
              <span>Subtotal</span>
              <span>₹{{ cartTotal }}</span>
            </div>
            <div class="flex justify-between">
              <span>Shipping</span>
              <span>₹{{ shippingCost }}</span>
            </div>
            <div class="flex justify-between text-lg font-bold border-t pt-4">
              <span>Total</span>
              <span>₹{{ totalWithShipping }}</span>
            </div>
            <button @click="checkout" class="w-full bg-green-600 text-white py-3 rounded-lg hover:bg-green-700 transition">
              Proceed to Checkout
            </button>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- Login Prompt -->
  <section v-else class="py-12">
    <div class="max-w-7xl mx-auto px-4 text-center">
      <div class="bg-white rounded-lg shadow p-8 max-w-md mx-auto">
        <i class="fas fa-shopping-cart text-6xl text-gray-300 mb-4"></i>
        <h3 class="text-xl font-semibold mb-4">Login Required</h3>
        <p class="text-gray-600 mb-6">Please login to view your shopping cart</p>
        <a href="auth.html" class="bg-green-600 text-white px-6 py-3 rounded-lg hover:bg-green-700 transition">
          Login to Continue
        </a>
      </div>
    </div>
  </section>
</div>

<script>
createApp({
  extends: createCommonApp(),
  setup(props, context) {
    const { user, cartCount, logout } = Vue.inject('common') || createCommonApp().setup();
    
    const cartItems = Vue.ref([]);

    const firstImage = (imagesJson) => {
      if (!imagesJson) return 'assets/default-product.jpg';
      try {
        const imgs = JSON.parse(imagesJson);
        return imgs[0] || 'assets/default-product.jpg';
      } catch {
        return 'assets/default-product.jpg';
      }
    };

    const cartTotal = Vue.computed(() => {
      return cartItems.value.reduce((total, item) => total + (item.price * item.quantity), 0);
    });

    const shippingCost = Vue.computed(() => {
      return cartTotal.value > 500 ? 0 : 40;
    });

    const totalWithShipping = Vue.computed(() => {
      return cartTotal.value + shippingCost.value;
    });

    const loadCart = async () => {
      if (!user.value) return;
      
      try {
        const res = await axios.get('api/cart.php');
        cartItems.value = res.data;
      } catch (e) {
        console.error('Error loading cart:', e);
      }
    };

    const updateQuantity = async (productId, newQuantity) => {
      if (newQuantity < 1) return;
      
      try {
        await axios.post('api/update-cart.php', {
          product_id: productId,
          quantity: newQuantity
        });
        loadCart();
      } catch (e) {
        console.error('Error updating quantity:', e);
      }
    };

    const removeFromCart = async (productId) => {
      try {
        await axios.post('api/remove-from-cart.php', { product_id: productId });
        loadCart();
      } catch (e) {
        console.error('Error removing from cart:', e);
      }
    };

    const checkout = () => {
      alert('Checkout functionality would go here!');
    };

    Vue.watch(user, (newUser) => {
      if (newUser) {
        loadCart();
      } else {
        cartItems.value = [];
      }
    });

    Vue.onMounted(() => {
      if (user.value) {
        loadCart();
      }
    });

    return { user, cartItems, cartTotal, shippingCost, totalWithShipping, firstImage, updateQuantity, removeFromCart, checkout, logout };
  }
}).mount('#app');
</script>

<?php include 'includes/footer.php'; ?>