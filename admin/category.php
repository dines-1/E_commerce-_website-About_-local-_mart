<div class="space-y-6" id="categories-section">
  <!-- Header -->
  <div class="flex justify-between items-center">
    <h2 class="text-3xl font-bold text-gray-800">Categories Management</h2>
    <button @click="openAddCategoryModal" class="bg-green-600 hover:bg-green-700 text-white px-6 py-3 rounded-lg font-semibold flex items-center gap-2 transition-colors">
      <i class="fas fa-plus"></i> Add Category
    </button>
  </div>

  <!-- Categories Grid -->
  <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
    <div v-for="category in categories" :key="category.id" 
         class="bg-white rounded-xl shadow-md overflow-hidden hover:shadow-lg transition-shadow">
      <!-- Category Image -->
      <div class="h-48 bg-gray-200 relative">
        <img v-if="category.image_url" :src="category.image_url" :alt="category.category_name" 
             class="w-full h-full object-cover">
        <div v-else class="w-full h-full flex items-center justify-center bg-gray-100">
          <i class="fas fa-tag text-4xl text-gray-400"></i>
        </div>
        <div class="absolute top-4 right-4 flex gap-2">
          <button @click="editCategory(category)" 
                  class="bg-blue-600 hover:bg-blue-700 text-white p-2 rounded-full transition-colors">
            <i class="fas fa-edit text-sm"></i>
          </button>
          <button @click="confirmDeleteCategory(category)" 
                  class="bg-red-600 hover:bg-red-700 text-white p-2 rounded-full transition-colors">
            <i class="fas fa-trash text-sm"></i>
          </button>
        </div>
      </div>
      
      <!-- Category Info -->
      <div class="p-4">
        <h3 class="text-lg font-semibold text-gray-800 mb-2">{{ category.category_name }}</h3>
        <div class="flex justify-between items-center text-sm text-gray-600">
          <span>Order: {{ category.display_order }}</span>
          <span>{{ formatDate(category.created_at) }}</span>
        </div>
      </div>
    </div>
  </div>

  <!-- Empty State -->
  <div v-if="categories.length === 0" class="text-center py-12 bg-white rounded-xl shadow-md">
    <i class="fas fa-tags text-6xl text-gray-300 mb-4"></i>
    <p class="text-xl text-gray-500 mb-2">No categories found</p>
    <p class="text-gray-400 mb-6">Get started by creating your first category</p>
    <button @click="openAddCategoryModal" class="bg-green-600 hover:bg-green-700 text-white px-6 py-2 rounded-lg">
      Add Category
    </button>
  </div>
</div>

<!-- Add/Edit Category Modal -->
<div v-if="showCategoryModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50">
  <div class="bg-white rounded-xl shadow-2xl w-full max-w-md">
    <!-- Modal Header -->
    <div class="flex justify-between items-center p-6 border-b">
      <h3 class="text-xl font-semibold text-gray-800">
        {{ isEditingCategory ? 'Edit Category' : 'Add New Category' }}
      </h3>
      <button @click="closeCategoryModal" class="text-gray-400 hover:text-gray-600">
        <i class="fas fa-times text-xl"></i>
      </button>
    </div>

    <!-- Modal Form -->
    <form @submit.prevent="saveCategory" class="p-6 space-y-4">
      <!-- Category Name -->
      <div>
        <label class="block text-sm font-medium text-gray-700 mb-2">Category Name *</label>
        <input v-model="categoryForm.category_name" type="text" required
               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
               placeholder="Enter category name">
      </div>

      <!-- Image URL -->
      <div>
        <label class="block text-sm font-medium text-gray-700 mb-2">Image URL</label>
        <input v-model="categoryForm.image_url" type="url"
               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
               placeholder="https://example.com/image.jpg">
        <p class="text-xs text-gray-500 mt-1">Optional: Provide a direct image URL</p>
      </div>

      <!-- Display Order -->
      <div>
        <label class="block text-sm font-medium text-gray-700 mb-2">Display Order</label>
        <input v-model="categoryForm.display_order" type="number" min="0"
               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
               placeholder="0">
        <p class="text-xs text-gray-500 mt-1">Lower numbers appear first</p>
      </div>

      <!-- Image Preview -->
      <div v-if="categoryForm.image_url" class="border rounded-lg p-4">
        <p class="text-sm font-medium text-gray-700 mb-2">Image Preview:</p>
        <img :src="categoryForm.image_url" :alt="categoryForm.category_name" 
             class="w-32 h-32 object-cover rounded-lg mx-auto border">
      </div>

      <!-- Modal Actions -->
      <div class="flex gap-3 pt-4">
        <button type="button" @click="closeCategoryModal"
                class="flex-1 px-4 py-3 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors">
          Cancel
        </button>
        <button type="submit" 
                class="flex-1 px-4 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors font-semibold">
          {{ isEditingCategory ? 'Update Category' : 'Create Category' }}
        </button>
      </div>
    </form>
  </div>
</div>

<!-- Delete Confirmation Modal -->
<div v-if="showDeleteCategoryModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50">
  <div class="bg-white rounded-xl shadow-2xl w-full max-w-md">
    <div class="p-6 text-center">
      <!-- Warning Icon -->
      <div class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4">
        <i class="fas fa-exclamation-triangle text-2xl text-red-600"></i>
      </div>
      
      <h3 class="text-xl font-semibold text-gray-800 mb-2">Delete Category</h3>
      <p class="text-gray-600 mb-6">
        Are you sure you want to delete <strong>"{{ categoryToDelete?.category_name }}"</strong>? 
        This action cannot be undone.
      </p>

      <div class="flex gap-3">
        <button @click="closeDeleteCategoryModal"
                class="flex-1 px-4 py-3 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors">
          Cancel
        </button>
        <button @click="deleteCategory" 
                class="flex-1 px-4 py-3 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors font-semibold">
          Delete Category
        </button>
      </div>
    </div>
  </div>
</div>

<!-- Toaster Notification -->
<div v-if="toaster.show" :class="toasterClass" 
     class="fixed top-4 right-4 p-4 rounded-lg shadow-lg z-50 max-w-sm transition-all duration-300">
  <div class="flex items-center gap-3">
    <i :class="toasterIcon" class="text-xl"></i>
    <div>
      <p class="font-semibold">{{ toaster.title }}</p>
      <p class="text-sm opacity-90">{{ toaster.message }}</p>
    </div>
    <button @click="hideToaster" class="ml-auto opacity-70 hover:opacity-100">
      <i class="fas fa-times"></i>
    </button>
  </div>
</div>