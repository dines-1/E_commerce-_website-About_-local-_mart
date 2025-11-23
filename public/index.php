<?php include 'includes/header.php'; ?>
<?php include 'includes/navbar.php'; ?>

<div id="app">
  <section class="relative h-screen bg-cover bg-center" style="background-image: url('assets/hero-market.jpg')">
    <div class="absolute inset-0 bg-black/50"></div>
    <div class="relative max-w-7xl mx-auto px-4 h-full flex items-center text-white">
      <div>
        <h1 class="text-5xl md:text-6xl font-bold mb-6">Fresh & Local,<br>Delivered Daily</h1>
        <p class="text-xl mb-8">Your trusted neighborhood store for groceries and daily needs.</p>
        <a href="products.php" class="bg-green-600 text-white px-8 py-4 rounded-lg mr-4 hover:bg-green-700">Shop Now</a>
        <a href="#categories" class="border-2 border-white px-8 py-4 rounded-lg hover:bg-white hover:text-gray-900 transition">Browse Categories</a>
      </div>
    </div>
  </section>

  <section id="categories" class="py-16 bg-white">
    <div class="max-w-7xl mx-auto px-4">
      <h2 class="text-4xl font-bold text-center mb-12">Shop by Category</h2>
      <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
        <template v-for="cat in categories" :key="cat.id">
          <a :href="'category.php?id=' + cat.id" class="group relative rounded-xl overflow-hidden shadow-lg hover:shadow-xl transition">
            <img :src="cat.image_url || 'assets/default-cat.jpg'" :alt="cat.category_name" class="w-full h-48 object-cover group-hover:scale-110 transition duration-500">
            <div class="absolute inset-0 bg-gradient-to-t from-black/70"></div>
            <div class="absolute bottom-4 left-4 text-white text-xl font-semibold">{{ cat.category_name }}</div>
          </a>
        </template>
      </div>
    </div>
  </section>

  <section id="products" class="py-16 bg-gray-100">
    <div class="max-w-7xl mx-auto px-4">
      <h2 class="text-4xl font-bold text-center mb-12">Featured Products</h2>
      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8">
        <template v-for="p in products" :key="p.id">
          <div class="bg-white rounded-lg shadow overflow-hidden hover:shadow-lg transition">
            <a :href="'product.php?id=' + p.id">
              <img :src="firstImage(p.images)" :alt="p.product_name" class="w-full h-56 object-cover">
            </a>
            <div class="p-4">
              <p class="text-sm text-gray-500">{{ p.category_name }}</p>
              <h3 class="font-semibold text-lg mb-2">{{ p.product_name }}</h3>
              <div class="flex justify-between items-center mt-4">
                <span class="text-2xl font-bold text-green-600">₹{{ p.price }}</span>
                <a :href="'add-to-cart.php?id=' + p.id" class="bg-green-600 text-white px-4 py-2 rounded text-sm hover:bg-green-700 transition">
                  Add to Cart
                </a>
              </div>
            </div>
          </div>
        </template>
      </div>
      <div class="text-center mt-12">
        <a href="products.php" class="bg-green-600 text-white px-8 py-3 rounded-lg hover:bg-green-700 transition">
          View All Products
        </a>
      </div>
    </div>
  </section>

  <section id="about" class="py-16 bg-white">
    <div class="max-w-7xl mx-auto px-4 grid md:grid-cols-2 gap-12 items-center">
      <div>
        <h2 class="text-4xl font-bold mb-6">About Local Mart</h2>
        <p class="text-gray-600 mb-6">Serving our community since 2015 with fresh groceries and daily essentials. We partner with local farmers and producers to bring you the best quality products.</p>
        <div class="grid grid-cols-3 gap-6 text-center">
          <div><h3 class="text-3xl font-bold text-green-600">500+</h3><p>Happy Customers</p></div>
          <div><h3 class="text-3xl font-bold text-green-600">50+</h3><p>Local Suppliers</p></div>
          <div><h3 class="text-3xl font-bold text-green-600">24/7</h3><p>Fast Delivery</p></div>
        </div>
      </div>
      <img src="assets/about-store.jpg" alt="Local Mart Store" class="rounded-xl shadow-lg">
    </div>
  </section>

  <!-- Reviews Section -->
  <section id="reviews" class="py-16 bg-gray-100">
    <div class="max-w-7xl mx-auto px-4">
      <h2 class="text-4xl font-bold text-center mb-12">Customer Reviews</h2>
      <div class="grid md:grid-cols-3 gap-8">
        <template v-for="r in reviews" :key="r.id">
          <div class="bg-white p-6 rounded-lg shadow hover:shadow-lg transition">
            <div class="flex items-center mb-4">
              <div class="w-12 h-12 bg-green-100 text-green-600 rounded-full flex items-center justify-center font-bold">
                {{ r.firstname[0] }}
              </div>
              <div class="ml-4">
                <h4 class="font-semibold">{{ r.firstname }}</h4>
                <p class="text-sm text-gray-500">on {{ r.product_name }}</p>
              </div>
            </div>
            <div class="flex mb-3">
              <i v-for="n in 5" :key="n" :class="n <= r.rating ? 'fas text-yellow-400' : 'far text-gray-300'" class="fa-star"></i>
            </div>
            <p class="text-gray-700">{{ r.review_text || 'Great product!' }}</p>
          </div>
        </template>
      </div>
    </div>
  </section>
</div>

<script>
createApp({
  extends: createCommonApp(),
  setup(props, context) {
    const { user, cartCount, logout } = Vue.inject('common') || createCommonApp().setup();
    
    const categories = Vue.ref([]);
    const products = Vue.ref([]);
    const reviews = Vue.ref([]);

    const firstImage = (imagesJson) => {
      if (!imagesJson) return 'assets/default-product.jpg';
      try {
        const imgs = JSON.parse(imagesJson);
        return imgs[0] || 'assets/default-product.jpg';
      } catch {
        return 'assets/default-product.jpg';
      }
    };

    const loadData = async () => {
      try {
        const [catRes, prodRes, revRes] = await Promise.all([
          axios.get('api/categories.php'),
          axios.get('api/products.php?limit=8'),
          axios.get('api/reviews.php?limit=6')
        ]);

        categories.value = catRes.data;
        products.value = prodRes.data;
        reviews.value = revRes.data;
      } catch (e) {
        console.error('Error loading page data:', e);
      }
    };

    Vue.onMounted(loadData);

    return { user, cartCount, categories, products, reviews, firstImage, logout };
  }
}).mount('#app');
</script>

<?php include 'includes/footer.php'; ?>