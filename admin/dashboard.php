<?php
session_start();
// Uncomment when ready
// if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
//     header("Location: ../public/auth.html");
//     exit;
// }
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Dashboard - Local Mart Admin</title>
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

<div class="flex h-screen overflow-hidden">

  <!-- Sidebar (shared + auto-highlights current page) -->
  <div id="sidebar-app">
    <?php include 'includes/sidebar.php'; ?>
  </div>

  <!-- Main Content -->
  <div class="flex-1 flex flex-col overflow-hidden">
    <?php include 'includes/header.php'; ?>

    <main class="flex-1 overflow-auto p-6 bg-gray-50">
      <div id="dashboard-app">
        <h2 class="text-3xl font-bold text-gray-800 mb-8">Dashboard Overview</h2>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
          <div class="bg-white p-6 rounded-xl shadow-md">
            <div class="flex items-center justify-between">
              <div>
                <p class="text-gray-500 text-sm">Total Products</p>
                <p class="text-3xl font-bold text-gray-800">{{ stats.totalProducts }}</p>
              </div>
              <i class="fas fa-box text-4xl text-green-500 opacity-20"></i>
            </div>
          </div>

          <div class="bg-white p-6 rounded-xl shadow-md">
            <div class="flex items-center justify-between">
              <div>
                <p class="text-gray-500 text-sm">Total Orders</p>
                <p class="text-3xl font-bold text-gray-800">{{ stats.totalOrders }}</p>
              </div>
              <i class="fas fa-shopping-cart text-4xl text-blue-500 opacity-20"></i>
            </div>
          </div>

          <div class="bg-white p-6 rounded-xl shadow-md">
            <div class="flex items-center justify-between">
              <div>
                <p class="text-gray-500 text-sm">Pending Orders</p>
                <p class="text-3xl font-bold text-orange-600">{{ stats.pendingOrders }}</p>
              </div>
              <i class="fas fa-clock text-4xl text-orange-500 opacity-20"></i>
            </div>
          </div>

          <div class="bg-white p-6 rounded-xl shadow-md">
            <div class="flex items-center justify-between">
              <div>
                <p class="text-gray-500 text-sm">Total Revenue</p>
                <p class="text-3xl font-bold text-green-600">Rs.{{ stats.totalRevenue }}</p>
              </div>
              <i class="fas fa-rupee-sign text-4xl text-green-500 opacity-20"></i>
            </div>
          </div>
        </div>
      </div>
    </main>
  </div>
</div>

<script>
  const { createApp, reactive, onMounted } = Vue;

  createApp({
    setup() {
      const stats = reactive({
        totalProducts: 'Loading...',
        totalOrders: 'Loading...',
        pendingOrders: 'Loading...',
        totalRevenue: '0.00'
      });

      const loadStats = async () => {
        try {
          const res = await axios.get('includes/stats.php');
          Object.assign(stats, res.data);
        } catch (err) {
          console.error('Failed to load stats');
          stats.totalProducts = stats.totalOrders = stats.pendingOrders = 'Error';
        }
      };

      onMounted(loadStats);

      return { stats };
    }
  }).mount('#dashboard-app');
</script>

</body>
</html>