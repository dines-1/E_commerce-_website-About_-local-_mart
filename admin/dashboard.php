<?php
session_start();
// if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
//     header("Location: ../public/auth.html");
//     exit;
//}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin Dashboard - Local Mart</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
  <script src="https://unpkg.com/vue@3/dist/vue.global.js"></script>
  <script src="https://unpkg.com/axios/dist/axios.min.js"></script>
  <style>
    .sidebar { transition: transform 0.3s ease; }
    @media (max-width: 1024px) {
      .sidebar-open { transform: translateX(0); }
      .sidebar-closed { transform: translateX(-100%); }
    }
  </style>
</head>
<body class="bg-gray-100">

<div id="app" class="flex h-screen overflow-hidden">
  <?php include 'includes/sidebar.php'; ?>

  <div class="flex-1 flex flex-col overflow-hidden">
    <?php include 'includes/header.php'; ?>

    <main class="flex-1 overflow-auto p-6 bg-gray-50">
      
      <div v-if="activeTab === 'dashboard'">
        <h2 class="text-3xl font-bold text-gray-800 mb-8">Dashboard Overview</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-10">
          <div class="bg-white p-6 rounded-xl shadow-md">
            <div class="flex items-center justify-between">
              <div>
                <p class="text-gray-500">Total Products</p>
                <p class="text-3xl font-bold text-gray-800">{{ stats.totalProducts }}</p>
              </div>
              <i class="fas fa-box text-4xl text-green-500 opacity-20"></i>
            </div>
          </div>
          <div class="bg-white p-6 rounded-xl shadow-md">
            <div class="flex items-center justify-between">
              <div>
                <p class="text-gray-500">Total Orders</p>
                <p class="text-3xl font-bold text-gray-800">{{ stats.totalOrders }}</p>
              </div>
              <i class="fas fa-shopping-cart text-4xl text-blue-500 opacity-20"></i>
            </div>
          </div>
          <div class="bg-white p-6 rounded-xl shadow-md">
            <div class="flex items-center justify-between">
              <div>
                <p class="text-gray-500">Pending Orders</p>
                <p class="text-3xl font-bold text-orange-600">{{ stats.pendingOrders }}</p>
              </div>
              <i class="fas fa-clock text-4xl text-orange-500 opacity-20"></i>
            </div>
          </div>
          <div class="bg-white p-6 rounded-xl shadow-md">
            <div class="flex items-center justify-between">
              <div>
                <p class="text-gray-500">Total Revenue</p>
                <p class="text-3xl font-bold text-green-600">Rs.{{ stats.totalRevenue }}</p>
              </div>
              <i class="fas fa-rupee-sign text-4xl text-green-500 opacity-20"></i>
            </div>
          </div>
        </div>
      </div>
   <div v-else-if="activeTab === 'categories'" class="text-center py-32">
        <?php include 'category.php'; ?>
      </div>
      <div v-if="activeTab === 'products'">
        <?php include 'product.php'; ?>
      </div>

      <div v-else-if="activeTab === 'orders'" class="text-center py-32">
<p>waiting...</p>
      </div>
      <div v-else-if="activeTab === 'users'" class="text-center py-32">
        <i class="fas fa-users text-6xl text-gray-300 mb-4"></i>
        <p class="text-2xl text-gray-500">Customer Management - Coming Soon</p>
      </div>
    </main>
  </div>
</div>
<script src="includes/category.js"></script>
<script>
const { createApp, ref, onMounted, reactive, watch } = Vue;

createApp({
  setup() {
    const mobileMenu = ref(false);
    const activeTab = ref('dashboard');

    const stats = reactive({
      totalProducts: 'Loading...',
      totalOrders: 'Loading...',
      pendingOrders: 'Loading...',
      totalRevenue: '0.00'
    });

    const loadStats = async () => {
      if (activeTab.value !== 'dashboard') return;
      try {
        const res = await axios.get('includes/stats.php');
        Object.assign(stats, res.data);
      } catch (err) {
        console.error('Failed to load stats:', err);
        stats.totalProducts = stats.totalOrders = stats.pendingOrders = 'Error';
      }
    };

    watch(activeTab, (newTab) => {
      if (newTab === 'dashboard') {
        loadStats();
      }
    });

    onMounted(() => {
      loadStats();
    });

    const logout = () => {
      axios.get('logout.php')
        .finally(() => {
          window.location.href = '../public/auth.html';
        });
    };

    return {
      mobileMenu,
      activeTab,
      stats,
      logout
    };
  }
}).mount('#app');
</script>

</body>
</html>