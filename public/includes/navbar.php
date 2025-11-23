<?php
session_start();
$loggedIn = isset($_SESSION['user_id']);
$userName = $loggedIn ? $_SESSION['firstname'] : '';
?>
<nav class="bg-white shadow-lg fixed top-0 w-full z-50">
  <div class="max-w-7xl mx-auto px-4 flex justify-between items-center h-24"> <!-- Increased height to h-24 -->
    <a href="index.php" class="text-4xl font-bold text-green-600">Sasto Bazaar</a> <!-- Increased to text-4xl -->

    <div class="hidden md:flex space-x-12"> <!-- Increased spacing -->
      <a href="index.php#categories" class="text-xl text-gray-700 hover:text-green-600 transition duration-200">Categories</a> <!-- text-xl -->
      <a href="products.php" class="text-xl text-gray-700 hover:text-green-600 transition duration-200">Products</a> <!-- text-xl -->
      <a href="index.php#about" class="text-xl text-gray-700 hover:text-green-600 transition duration-200">About</a> <!-- text-xl -->
      <a href="index.php#reviews" class="text-xl text-gray-700 hover:text-green-600 transition duration-200">Reviews</a> <!-- text-xl -->
    </div>

    <div class="flex items-center space-x-8"> <!-- Increased spacing -->
      <?php if ($loggedIn): ?>
        <span class="text-xl text-gray-700 font-medium"><?php echo htmlspecialchars($userName); ?></span> <!-- text-xl -->
        <a href="cart.php" class="relative group">
          <i class="fas fa-shopping-cart text-3xl text-gray-700 group-hover:text-green-600 transition duration-200"></i> <!-- text-3xl -->
          <span id="cart-count" class="absolute -top-2 -right-2 bg-red-500 text-white text-base rounded-full h-7 w-7 flex items-center justify-center font-semibold">0</span> <!-- Increased size -->
        </a>
        <button onclick="logout()" class="text-xl text-red-600 hover:text-red-700 font-medium transition duration-200">Logout</button> <!-- text-xl -->
      <?php else: ?>
        <a href="auth.html" class="text-xl text-gray-700 hover:text-green-600 transition duration-200 font-medium">Login</a> <!-- text-xl -->
        <a href="auth.html" class="bg-green-600 text-white px-8 py-4 rounded-lg hover:bg-green-700 transition duration-200 font-medium text-xl"> <!-- text-xl and increased padding -->
          Sign Up
        </a>
      <?php endif; ?>
    </div>
  </div>
</nav>

<script>
function logout() {
  fetch('api/logout.php')
    .then(() => window.location.reload());
}

<?php if ($loggedIn): ?>
document.addEventListener('DOMContentLoaded', function() {
  fetch('api/cart-count.php')
    .then(response => response.json())
    .then(data => {
      const cartCount = document.getElementById('cart-count');
      if (cartCount && data.count > 0) {
        cartCount.textContent = data.count;
      } else if (cartCount && data.count === 0) {
        cartCount.style.display = 'none';
      }
    })
    .catch(error => console.error('Error loading cart count:', error));
});
<?php endif; ?>
</script>