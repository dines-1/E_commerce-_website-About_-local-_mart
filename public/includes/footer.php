<footer class="bg-gray-900 text-white py-12">
  <div class="max-w-7xl mx-auto px-4 grid md:grid-cols-4 gap-8">
    <div>
      <h3 class="text-2xl font-bold mb-4">Sasto Bazaar</h3>
      <p class="text-gray-400">Your trusted local store.</p>
    </div>
    <div>
      <h4 class="font-semibold mb-4">Quick Links</h4>
      <ul class="space-y-2 text-gray-400">
        <li><a href="index.php#about" class="hover:text-white">About</a></li>
        <li><a href="index.php#contact" class="hover:text-white">Contact</a></li>
        <li><a href="#" class="hover:text-white">Privacy</a></li>
      </ul>
    </div>
    <div>
      <h4 class="font-semibold mb-4">Contact</h4>
      <p class="text-gray-400">98765 43210<br>info@sastobazaar.com</p>
    </div>
    <div>
      <h4 class="font-semibold mb-4">Follow Us</h4>
      <div class="flex space-x-4">
        <a href="#" class="text-gray-400 hover:text-white"><i class="fab fa-facebook"></i></a>
        <a href="#" class="text-gray-400 hover:text-white"><i class="fab fa-twitter"></i></a>
        <a href="#" class="text-gray-400 hover:text-white"><i class="fab fa-instagram"></i></a>
      </div>
    </div>
  </div>
  <div class="text-center text-gray-500 text-sm mt-8">
    © 2025 Local Mart. All rights reserved.
  </div>
</footer>

<script>
const { createApp, ref, onMounted } = Vue;

function createCommonApp() {
  return {
    setup() {
      const user = ref(null);
      const cartCount = ref(0);

      const loadUserData = async () => {
        try {
          const userRes = await axios.get('api/check-login.php');
          if (userRes.data.loggedin) {
            user.value = userRes.data.user;
          }
          
          if (user.value) {
            const cartRes = await axios.get('api/cart-count.php');
            cartCount.value = cartRes.data.count || 0;
          }
        } catch (e) {
          console.error('Error loading user data:', e);
        }
      };

      const logout = async () => {
        try {
          await axios.get('api/logout.php');
          window.location.reload();
        } catch (e) {
          console.error('Logout error:', e);
        }
      };

      onMounted(loadUserData);

      return { user, cartCount, logout };
    }
  };
}
</script>
</body>
</html>