<header class="bg-white shadow-sm border-b border-gray-200">
  <div class="flex items-center justify-between px-6 py-4">
    <button @click="mobileMenu = !mobileMenu" class="lg:hidden text-gray-700">
      <i class="fas fa-bars text-2xl"></i>
    </button>
    <div class="flex items-center gap-4">
      <span class="text-gray-700">Hello, <strong><?= $_SESSION['firstname'] ?? 'Admin' ?></strong></span>
      <div class="w-10 h-10 bg-green-600 rounded-full flex items-center justify-center text-white font-bold text-lg">
        <?= strtoupper(substr($_SESSION['firstname'] ?? 'A', 0, 1)) ?>
      </div>
    </div>
  </div>
</header>