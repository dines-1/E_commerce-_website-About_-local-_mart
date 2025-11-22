<!-- includes/sidebar.php -->
<div :class="['bg-gray-900 text-white w-64 flex-shrink-0 fixed lg:static inset-y-0 z-50 sidebar', 
               mobileMenu ? 'sidebar-open' : 'sidebar-closed lg:translate-x-0']">

  <div class="p-6 border-b border-gray-800">
    <h1 class="text-2xl font-bold flex items-center gap-3">
      <i class="fas fa-store text-green-400"></i> Local Mart
    </h1>
  </div>

  <nav class="mt-6">
    <a href="dashboard.php" :class="['flex items-center px-6 py-4 text-lg transition', current === 'dashboard.php' ? 'bg-green-600 border-l-4 border-green-400' : 'hover:bg-gray-800']">
      <i class="fas fa-tachometer-alt mr-4"></i> Dashboard
    </a>
    <a href="product.php" :class="['flex items-center px-6 py-4 text-lg transition', current === 'products.php' ? 'bg-green-600 border-l-4 border-green-400' : 'hover:bg-gray-800']">
      <i class="fas fa-box mr-4"></i> Products
    </a>
    <a href="category.php" :class="['flex items-center px-6 py-4 text-lg transition', current === 'categories.php' ? 'bg-green-600 border-l-4 border-green-400' : 'hover:bg-gray-800']">
      <i class="fas fa-tags mr-4"></i> Categories
    </a>
    <a href="orders.php" :class="['flex items-center px-6 py-4 text-lg transition', current === 'orders.php' ? 'bg-green-600 border-l-4 border-green-400' : 'hover:bg-gray-800']">
      <i class="fas fa-shopping-cart mr-4"></i> Orders
    </a>
    <a href="users.php" :class="['flex items-center px-6 py-4 text-lg transition', current === 'users.php' ? 'bg-green-600 border-l-4 border-green-400' : 'hover:bg-gray-800']">
      <i class="fas fa-users mr-4"></i> Customers
    </a>
  </nav>

  <div class="absolute bottom-0 w-full p-6 border-t border-gray-800">
    <button @click="logout" class="flex items-center text-red-400 hover:text-red-300 text-lg">
      <i class="fas fa-sign-out-alt mr-3"></i> Logout
    </button>
  </div>
</div>

<script>
  const { createApp, ref } = Vue;
  createApp({
    setup() {
      const mobileMenu = ref(false);
      const current = window.location.pathname.split('/').pop() || 'dashboard.php';

      const logout = () => {
        axios.get('logout.php').finally(() => location.href = '../public/auth.html');
      };

      return { mobileMenu, current, logout };
    }
  }).mount('#sidebar-app');
</script>