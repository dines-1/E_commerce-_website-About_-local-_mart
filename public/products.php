<?php include 'includes/header.php'; ?>
<?php include 'includes/navbar.php'; ?>

<div id="app">
  <!-- Products Header -->
  <section class="bg-green-600 text-white py-16 mt-16">
    <div class="max-w-7xl mx-auto px-4 text-center">
      <h1 class="text-4xl font-bold mb-4">All Products</h1>
      <p class="text-xl">Discover our wide range of fresh products</p>
    </div>
  </section>

  <!-- Products Filter & Grid -->
  <section class="py-12">
    <div class="max-w-7xl mx-auto px-4">
      <!-- Search and Filter -->
      <div class="flex flex-col md:flex-row gap-4 mb-8">
        <div class="flex-1">
          <div class="relative">
            <input type="text" v-model="searchQuery" placeholder="Search products..." 
                   class="w-full pl-10 pr-4 py-3 border rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500">
            <i class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
          </div>
        </div>
        <select v-model="selectedCategory" class="px-4 py-3 border rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500">
          <option value="">All Categories</option>
          <option v-for="cat in categories" :value="cat.id">{{ cat.category_name }}</option>
        </select>
        <select v-model="sortBy" class="px-4 py-3 border rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500">
          <option value="newest">Newest First</option>
          <option value="price_low">Price: Low to High</option>
          <option value="price_high">Price: High to Low</option>
          <option value="name">Name: A to Z</option>
        </select>
      </div>

      <!-- Products Grid -->
      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8">
        <template v-for="p in filteredProducts" :key="p.id">
          <div class="bg-white rounded-lg shadow overflow-hidden hover:shadow-lg transition">
            <a :href="'product.php?id=' + p.id">
              <img :src="firstImage(p.images)" :alt="p.product_name" class="w-full h-56 object-cover">
            </a>
            <div class="p-4">
              <p class="text-sm text-gray-500">{{ p.category_name }}</p>
              <h3 class="font-semibold text-lg mb-2">{{ p.product_name }}</h3>
              <p class="text-gray-600 text-sm mb-3">{{ p.description?.substring(0, 60) }}...</p>
              <div class="flex justify-between items-center">
                <span class="text-2xl font-bold text-green-600">₹{{ p.price }}</span>
                <a :href="'add-to-cart.php?id=' + p.id" class="bg-green-600 text-white px-4 py-2 rounded text-sm hover:bg-green-700 transition">
                  Add to Cart
                </a>
              </div>
            </div>
          </div>
        </template>
      </div>

      <!-- No Results -->
      <div v-if="filteredProducts.length === 0" class="text-center py-12">
        <i class="fas fa-search text-6xl text-gray-300 mb-4"></i>
        <h3 class="text-xl text-gray-600">No products found</h3>
        <p class="text-gray-500">Try adjusting your search or filter criteria</p>
      </div>
    </div>
  </section>
</div>

<script>
createApp({
  extends: createCommonApp(),
  setup(props, context) {
    const { user, cartCount, logout } = Vue.inject('common') || createCommonApp().setup();
    
    const products = Vue.ref([]);
    const categories = Vue.ref([]);
    const searchQuery = Vue.ref('');
    const selectedCategory = Vue.ref('');
    const sortBy = Vue.ref('newest');

    const firstImage = (imagesJson) => {
      if (!imagesJson) return 'assets/default-product.jpg';
      try {
        const imgs = JSON.parse(imagesJson);
        return imgs[0] || 'assets/default-product.jpg';
      } catch {
        return 'assets/default-product.jpg';
      }
    };

    const filteredProducts = Vue.computed(() => {
      let filtered = [...products.value];

      // Search filter
      if (searchQuery.value) {
        const query = searchQuery.value.toLowerCase();
        filtered = filtered.filter(p => 
          p.product_name.toLowerCase().includes(query) ||
          p.description?.toLowerCase().includes(query) ||
          p.category_name.toLowerCase().includes(query)
        );
      }

      // Category filter
      if (selectedCategory.value) {
        filtered = filtered.filter(p => p.category_id == selectedCategory.value);
      }

      // Sorting
      switch (sortBy.value) {
        case 'price_low':
          filtered.sort((a, b) => a.price - b.price);
          break;
        case 'price_high':
          filtered.sort((a, b) => b.price - a.price);
          break;
        case 'name':
          filtered.sort((a, b) => a.product_name.localeCompare(b.product_name));
          break;
        case 'newest':
        default:
          filtered.sort((a, b) => new Date(b.created_at) - new Date(a.created_at));
          break;
      }

      return filtered;
    });

    const loadData = async () => {
      try {
        const [prodRes, catRes] = await Promise.all([
          axios.get('api/products.php'),
          axios.get('api/categories.php')
        ]);

        products.value = prodRes.data;
        categories.value = catRes.data;
      } catch (e) {
        console.error('Error loading products:', e);
      }
    };

    Vue.onMounted(loadData);

    return { user, cartCount, products, categories, searchQuery, selectedCategory, sortBy, filteredProducts, firstImage, logout };
  }
}).mount('#app');
</script>

<?php include 'includes/footer.php'; ?>