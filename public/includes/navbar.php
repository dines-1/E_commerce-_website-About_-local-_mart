<?php
session_start();
$loggedIn = isset($_SESSION['user_id']);
$userName = $loggedIn ? $_SESSION['firstname'] : '';
?>
<nav class="bg-white shadow-lg fixed top-0 w-full z-50">
  <div class="max-w-7xl mx-auto px-4 flex justify-between items-center h-16">
    <a href="index.php" class="text-2xl font-bold text-green-600">Sasto Bazaar</a>

    <div class="hidden md:flex space-x-8">
      <a href="index.php#categories" class="text-gray-700 hover:text-green-600">Categories</a>
      <a href="products.php" class="text-gray-700 hover:text-green-600">Products</a>
      <a href="index.php#about" class="text-gray-700 hover:text-green-600">About</a>
      <a href="index.php#reviews" class="text-gray-700 hover:text-green-600">Reviews</a>
    </div>

    <div class="flex items-center space-x-4">
      <?php if ($loggedIn): ?>
        <span class="text-gray-700"><?php echo htmlspecialchars($userName); ?></span>
        <a href="cart.php" class="relative">
          <i class="fas fa-shopping-cart text-xl text-gray-700"></i>
          <span id="cart-count" class="absolute -top-2 -right-2 bg-red-500 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center">0</span>
        </a>
        <button onclick="logout()" class="text-red-600 hover:text-red-700">Logout</button>
      <?php else: ?>
        <a href="auth.html" class="text-gray-700 hover:text-green-600">Login</a>
        <a href="auth.html" class="bg-green-600 text-white px-5 py-2 rounded-lg hover:bg-green-700">Sign Up</a>
      <?php endif; ?>
    </div>
  </div>
</nav>

<script>
function logout() {
  fetch('api/logout.php')
    .then(() => window.location.reload());
}
</script>