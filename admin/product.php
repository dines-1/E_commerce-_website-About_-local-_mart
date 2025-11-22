<div class="space-y-6">
  <div class="flex justify-between items-center">
    <h2 class="text-3xl font-bold text-gray-800">Products Management</h2>
    <button @click="openAddModal" class="bg-green-600 hover:bg-green-700 text-white px-6 py-3 rounded-lg font-semibold flex items-center gap-2">
      <i class="fas fa-plus"></i> Add Product
    </button>
  </div>

  <div class="bg-white p-4 rounded-xl shadow-md flex flex-col sm:flex-row gap-4">
    <input v-model="searchQuery" @input="filterProducts" placeholder="Search products..." class="flex-1 px-4 py-3 border rounded-lg">
    <select v-model="selectedCategory" @change="filterProducts" class="px-4 py-3 border rounded-lg">
      <option value="">All Categories</option>
      <option v-for="c in categories" :value="c.id">{{ c.category_name }}</option>
    </select>
  </div>

  <div class="bg-white rounded-xl shadow-md overflow-hidden">
    <table class="w-full">
      <thead class="bg-gray-50">
        <tr>
          <th class="px-6 py-4 text-left">Image</th>
          <th class="px-6 py-4 text-left">Name</th>
          <th class="px-6 py-4 text-left">Category</th>
          <th class="px-6 py-4 text-left">Price</th>
          <th class="px-6 py-4 text-left">Stock</th>
          <th class="px-6 py-4 text-left">Status</th>
          <th class="px-6 py-4 text-right">Actions</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="p in filteredProducts" :key="p.id" class="border-b hover:bg-gray-50">
          <td class="px-6 py-4"><img :src="firstImage(p.images)" class="w-16 h-16 object-cover rounded"></td>
          <td class="px-6 py-4 font-medium">{{ p.product_name }}</td>
          <td class="px-6 py-4">{{ categoryName(p.category_id) }}</td>
          <td class="px-6 py-4 font-bold text-green-600">₹{{ Number(p.price).toFixed(2) }}</td>
          <td class="px-6 py-4" :class="p.stock_quantity < 10 ? 'text-red-600 font-bold' : ''">{{ p.stock_quantity }}</td>
          <td class="px-6 py-4">
            <span :class="p.stock_quantity > 0 ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'" class="px-3 py-1 rounded-full text-xs">
              {{ p.stock_quantity > 0 ? 'In Stock' : 'Out of Stock' }}
            </span>
          </td>
          <td class="px-6 py-4 text-right">
            <button @click="editProduct(p)" class="text-blue-600 mr-3"><i class="fas fa-edit"></i></button>
            <button @click="deleteProduct(p.id)" class="text-red-600"><i class="fas fa-trash"></i></button>
          </td>
        </tr>
      </tbody>
    </table>
  </div>
</div>

