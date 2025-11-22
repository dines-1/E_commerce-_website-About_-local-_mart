<div class="bg-gray-900 text-white w-64 flex-shrink-0 fixed lg:static inset-y-0 z-50 sidebar transition-transform duration-300" 
     id="sidebar">
  
  <div class="p-6 border-b border-gray-800">
    <h1 class="text-2xl font-bold flex items-center gap-3">
      <i class="fas fa-store text-green-400"></i> Sasto Bazaar
    </h1>
  </div>

  <nav class="mt-6">
    <a href="dashboard.php"    class="menu-item flex items-center px-6 py-4 text-lg transition hover:bg-gray-800">
      <i class="fas fa-tachometer-alt mr-4"></i> Dashboard
    </a>
    <a href="product.php"     class="menu-item flex items-center px-6 py-4 text-lg transition hover:bg-gray-800">
      <i class="fas fa-box mr-4"></i> Products
    </a>
    <a href="category.php"   class="menu-item flex items-center px-6 py-4 text-lg transition hover:bg-gray-800">
      <i class="fas fa-tags mr-4"></i> Categories
    </a>
    <a href="orders.php"       class="menu-item flex items-center px-6 py-4 text-lg transition hover:bg-gray-800">
      <i class="fas fa-shopping-cart mr-4"></i> Orders
    </a>
    <a href="users.php"        class="menu-item flex items-center px-6 py-4 text-lg transition hover:bg-gray-800">
      <i class="fas fa-users mr-4"></i> Customers
    </a>
    <a href="setting.php"      class="menu-item flex items-center px-6 py-4 text-lg transition hover:bg-gray-800">
      <i class="fas fa-cog mr-4"></i> Settings
    </a>
  </nav>

  <div class="absolute bottom-0 w-full p-6 border-t border-gray-800">
    <button id="logout-btn" class="flex items-center text-red-400 hover:text-red-300 text-lg w-full text-left">
      <i class="fas fa-sign-out-alt mr-3"></i> Logout
    </button>
  </div>
</div>

<script>
  const currentPage = window.location.pathname.split('/').pop() || 'dashboard.php';
  document.querySelectorAll('.menu-item').forEach(link => {
    if (link.getAttribute('href') === currentPage) {
      link.classList.add('bg-green-600', 'border-l-4', 'border-green-400');
    }
  });

  const sidebar = document.getElementById('sidebar');
  const mobileBtn = document.getElementById('mobile-menu-btn');

  if (mobileBtn) {
    mobileBtn.addEventListener('click', () => {
      sidebar.classList.toggle('-translate-x-full');
    });
  }

  document.getElementById('logout-btn').addEventListener('click', () => {
    if (confirm('Logout from admin panel?')) {
      fetch('logout.php')
        .then(() => location.href = '../public/auth.html')
        .catch(() => location.href = '../public/auth.html');
    }
  });

  document.addEventListener('click', (e) => {
    if (window.innerWidth < 1024 && !sidebar.contains(e.target) && !e.target.closest('#mobile-menu-btn')) {
      sidebar.classList.add('-translate-x-full');
    }
  });
</script>

<style>
  @media (max-width: 1024px) {
    #sidebar {
      transform: translateX(-100%);
    }
    #sidebar.-translate-x-full {
      transform: translateX(-100%);
    }
    #sidebar:not(.-translate-x-full) {
      transform: translateX(0);
    }
  }
</style>