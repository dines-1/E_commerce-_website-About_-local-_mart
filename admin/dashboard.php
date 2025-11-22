<?php session_start(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Dashboard • Local Mart Admin</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
  <script src="https://unpkg.com/axios/dist/axios.min.js"></script>
  <style>
    .sidebar { transition: transform 0.3s ease; }
    @media (max-width: 1024px) {
      #sidebar { transform: translateX(-100%); }
      #sidebar:not(.-translate-x-full) { transform: translateX(0); }
    }
  </style>
</head>
<body class="bg-gray-100">

<div class="flex h-screen overflow-hidden">

  <!-- Sidebar -->
  <div id="sidebar-app">
    <?php include 'includes/sidebar.php'; ?>
  </div>

  <!-- Main Content -->
  <div class="flex-1 flex flex-col overflow-hidden">
    <?php include 'includes/header.php'; ?>

    <main class="flex-1 overflow-auto p-6 bg-gray-50">
      <h2 class="text-3xl font-bold text-gray-800 mb-8">Dashboard Overview</h2>

      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        
        <!-- Total Products -->
        <div class="bg-white p-6 rounded-xl shadow-md">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-gray-500 text-sm">Total Products</p>
              <p id="total-products" class="text-3xl font-bold text-gray-800">
                <i class="fas fa-spinner fa-spin"></i>
              </p>
            </div>
            <i class="fas fa-box text-4xl text-green-500 opacity-20"></i>
          </div>
        </div>

        <!-- Total Orders -->
        <div class="bg-white p-6 rounded-xl shadow-md">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-gray-500 text-sm">Total Orders</p>
              <p id="total-orders" class="text-3xl font-bold text-gray-800">
                <i class="fas fa-spinner fa-spin"></i>
              </p>
            </div>
            <i class="fas fa-shopping-cart text-4xl text-blue-500 opacity-20"></i>
          </div>
        </div>

        <!-- Pending Orders -->
        <div class="bg-white p-6 rounded-xl shadow-md">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-gray-500 text-sm">Pending Orders</p>
              <p id="pending-orders" class="text-3xl font-bold text-orange-600">
                <i class="fas fa-spinner fa-spin"></i>
              </p>
            </div>
            <i class="fas fa-clock text-4xl text-orange-500 opacity-20"></i>
          </div>
        </div>

        <!-- Total Revenue -->
        <div class="bg-white p-6 rounded-xl shadow-md">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-gray-500 text-sm">Total Revenue</p>
              <p id="total-revenue" class="text-3xl font-bold text-green-600">
                <i class="fas fa-spinner fa-spin"></i>
              </p>
            </div>
            <i class="fas fa-rupee-sign text-4xl text-green-500 opacity-20"></i>
          </div>
        </div>

      </div>
    </main>
  </div>
</div>

<script>
async function loadStats() {
  try {
    const res = await axios.get('includes/stats.php');
    const data = res.data;

    document.getElementById('total-products').textContent = data.totalProducts;
    document.getElementById('total-orders').textContent = data.totalOrders;
    document.getElementById('pending-orders').textContent = data.pendingOrders;
    document.getElementById('total-revenue').textContent = 'Rs.' + data.totalRevenue;
  } catch (err) {
    document.getElementById('total-products').textContent = 'Error';
    document.getElementById('total-orders').textContent = 'Error';
    document.getElementById('pending-orders').textContent = 'Error';
    document.getElementById('total-revenue').textContent = 'Error';
  }
}

// Load on page load
loadStats();
</script>

</body>
</html>