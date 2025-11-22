<?php session_start(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Categories • Local Mart Admin</title>
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

  <div id="sidebar-app">
    <?php include 'includes/sidebar.php'; ?>
  </div>
  <div class="flex-1 flex flex-col overflow-hidden">
    <?php include 'includes/header.php'; ?>

    <main class="flex-1 overflow-auto p-6 bg-gray-50">
      <div id="categories-app">
        <div class="text-center py-32 text-gray-500">
          <i class="fas fa-spinner fa-spin text-5xl mb-4"></i>
          <p class="text-xl">Loading categories...</p>
        </div>
      </div>
    </main>
  </div>
</div>

<script src="includes/category.js"></script>

<script>
  document.addEventListener('DOMContentLoaded', () => {
    if (typeof categoriesSection !== 'undefined') {
      const { createApp } = Vue;
      createApp(categoriesSection).mount('#categories-app');
    } else {
      console.error('categoriesSection not loaded. Check category.js');
    }
  });
</script>

</body>
</html>