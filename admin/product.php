<?php session_start(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Products • Local Mart Admin</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
  <script src="https://unpkg.com/axios/dist/axios.min.js"></script>
  <style>
    .sidebar { transition: transform 0.3s ease; }
    @media (max-width: 1024px) {
      #sidebar { transform: translateX(-100%); }
      #sidebar:not(.-translate-x-full) { transform: translateX(0); }
    }
    table { width: 100%; border-collapse: separate; border-spacing: 0; }
    th { background: #f8f9fa; position: sticky; top: 0; z-index: 10; }
    tr:hover { background: #f8fafc; }
  </style>
</head>
<body class="bg-gray-100">

<div class="flex h-screen overflow-hidden">
  <!-- Sidebar -->
  <div id="sidebar-app">
    <?php include 'includes/sidebar.php'; ?>
  </div>

  <div class="flex-1 flex flex-col overflow-hidden">
    <?php include 'includes/header.php'; ?>

    <main class="flex-1 overflow-auto p-6 bg-gray-50">
      <div class="bg-white rounded-xl shadow-md overflow-hidden">
        <!-- Header with Search & Add Button -->
        <div class="p-6 border-b">
          <div class="flex flex-col sm:flex-row gap-4 justify-between items-start sm:items-center">
            <h1 class="text-3xl font-bold text-gray-800">Products Management</h1>
            
            <div class="flex gap-3 w-full sm:w-auto">
              <input type="text" id="search-input" placeholder="Search by name, brand or category..." 
                     class="px-4 py-2 border rounded-lg w-full sm:w-96 focus:outline-none focus:ring-2 focus:ring-blue-500">
              <button onclick="openAddModal()" class="bg-green-600 hover:bg-green-700 text-white px-5 py-2 rounded-lg flex items-center gap-2 whitespace-nowrap">
                <i class="fas fa-plus"></i> Add Product
              </button>
            </div>
          </div>
        </div>

        <!-- Products Table -->
        <div class="overflow-x-auto">
          <table class="min-w-full">
            <thead>
              <tr class="text-left text-sm font-semibold text-gray-700">
                <th class="px-6 py-4">Image</th>
                <th class="px-6 py-4">Name</th>
                <th class="px-6 py-4">Category</th>
                <th class="px-6 py-4">Price</th>
                <th class="px-6 py-4">Stock</th>
                <th class="px-6 py-4">Status</th>
                <th class="px-6 py-4">Actions</th>
              </tr>
            </thead>
            <tbody id="products-table">
              <tr><td colspan="7" class="text-center py-16 text-gray-500">
                <i class="fas fa-spinner fa-spin text-4xl"></i><br>Loading products...
              </td></tr>
            </tbody>
          </table>
        </div>
      </div>
    </main>
  </div>
</div>

<!-- Add/Edit Modal -->
<div id="product-modal" class="fixed inset-0 bg-black bg-opacity-50 hidden flex items-center justify-center z-50 p-4">
  <div class="bg-white rounded-xl max-w-2xl w-full max-h-screen overflow-y-auto p-8">
    <h3 class="text-2xl font-bold mb-6" id="modal-title">Add Product</h3>
    <form id="product-form" enctype="multipart/form-data">
      <input type="hidden" id="edit-id">
      <div class="grid grid-cols-1 md:grid-cols-2 gap-5 mb-5">
        <input type="text" id="product-name" required placeholder="Product Name" class="px-4 py-3 border rounded-lg w-full">
        <select id="category-id" required class="px-4 py-3 border rounded-lg w-full">
          <option value="">Select Category</option>
        </select>
      </div>
      <textarea id="description" placeholder="Description (optional)" rows="3" class="w-full px-4 py-3 border rounded-lg mb-5"></textarea>
      
      <div class="grid grid-cols-2 gap-5 mb-5">
        <input type="number" step="0.01" id="price" required placeholder="Price" class="px-4 py-3 border rounded-lg">
        <input type="number" id="stock" required placeholder="Stock Quantity" class="px-4 py-3 border rounded-lg">
      </div>

      <input type="text" id="brand" placeholder="Brand (optional)" class="w-full px-4 py-3 border rounded-lg mb-5">

      <div class="mb-6">
        <label class="block text-sm font-medium mb-2">Product Images</label>
        <input type="file" id="images" multiple accept="image/*" class="w-full">
        <div id="image-preview" class="mt-3 flex flex-wrap gap-3"></div>
      </div>

      <div class="flex gap-4">
        <button type="button" onclick="closeModal()" class="flex-1 py-3 border rounded-lg hover:bg-gray-50">Cancel</button>
        <button type="submit" class="flex-1 bg-blue-600 text-white py-3 rounded-lg hover:bg-blue-700">
          <span id="submit-text">Add Product</span>
        </button>
      </div>
    </form>
  </div>
</div>

<script>
let allProducts = [];        // Full list for search
let allCategories = [];      // For category dropdown

async function loadData() {
  try {
    const [prodRes, catRes] = await Promise.all([
      axios.get('includes/product.php?action=get_all'),
      axios.get('includes/categories.php?action=get_all')
    ]);

    allProducts = prodRes.data;
    allCategories = catRes.data;

    renderProducts(allProducts);
    populateCategories();
  } catch (err) {
    document.getElementById('products-table').innerHTML = 
      `<tr><td colspan="7" class="text-center py-20 text-red-600">Failed to load data</td></tr>`;
  }
}

function renderProducts(products) {
  const tbody = document.getElementById('products-table');
  if (!products || products.length === 0) {
    tbody.innerHTML = `<tr><td colspan="7" class="text-center py-20 text-gray-500 text-xl">
      <i class="fas fa-box text-6xl mb-4 block opacity-30"></i>No products found
    </td></tr>`;
    return;
  }

  tbody.innerHTML = products.map(p => `
    <tr class="border-t">
      <td class="px-6 py-4">
        ${p.images ? `<img src="${p.images.split(',')[0]}" class="w-16 h-16 object-cover rounded">` : 
          `<div class="w-16 h-16 bg-gray-200 rounded flex items-center justify-center"><i class="fas fa-image text-gray-400"></i></div>`}
      </td>
      <td class="px-6 py-4 font-medium">${p.product_name}</td>
      <td class="px-6 py-4 text-gray-600">${p.category_name || '—'}</td>
      <td class="px-6 py-4 font-bold text-green-600">Rs.${parseFloat(p.price).toFixed(2)}</td>
      <td class="px-6 py-4">${p.stock_quantity} pcs</td>
      <td class="px-6 py-4">
        <span class="px-3 py-1 rounded-full text-xs ${p.stock_quantity > 0 ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'}">
          ${p.stock_quantity > 0 ? 'In Stock' : 'Out of Stock'}
        </span>
      </td>
      <td class="px-6 py-4">
        <button onclick="editProduct(${p.id})" class="text-blue-600 hover:text-blue-800 mr-4"><i class="fas fa-edit"></i></button>
        <button onclick="deleteProduct(${p.id})" class="text-red-600 hover:text-red-800"><i class="fas fa-trash"></i></button>
      </td>
    </tr>
  `).join('');
}

function populateCategories() {
  const select = document.getElementById('category-id');
  select.innerHTML = '<option value="">Select Category</option>' +
    allCategories.map(c => `<option value="${c.id}">${c.category_name}</option>`).join('');
}

document.getElementById('search-input').addEventListener('input', function () {
  const query = this.value.trim().toLowerCase();
  if (query === '') {
    renderProducts(allProducts);
    return;
  }
  const filtered = allProducts.filter(p =>
    p.product_name.toLowerCase().includes(query) ||
    (p.brand && p.brand.toLowerCase().includes(query)) ||
    (p.category_name && p.category_name.toLowerCase().includes(query))
  );
  renderProducts(filtered);
});

function openAddModal() {
  document.getElementById('modal-title').textContent = 'Add Product';
  document.getElementById('submit-text').textContent = 'Add Product';
  document.getElementById('product-form').reset();
  document.getElementById('edit-id').value = '';
  document.getElementById('image-preview').innerHTML = '';
  document.getElementById('product-modal').classList.remove('hidden');
}

function closeModal() {
  document.getElementById('product-modal').classList.add('hidden');
}

async function editProduct(id) {
  try {
    const res = await axios.get(`includes/product.php?action=get_one&id=${id}`);
    const p = res.data;

    document.getElementById('modal-title').textContent = 'Edit Product';
    document.getElementById('submit-text').textContent = 'Update Product';
    document.getElementById('edit-id').value = p.id;
    document.getElementById('product-name').value = p.product_name;
    document.getElementById('description').value = p.product_description || '';
    document.getElementById('category-id').value = p.category_id;
    document.getElementById('price').value = p.price;
    document.getElementById('stock').value = p.stock_quantity;
    document.getElementById('brand').value = p.brand || '';

    document.getElementById('product-modal').classList.remove('hidden');
  } catch (err) {
    alert('Failed to load product');
  }
}

async function deleteProduct(id) {
  if (!confirm('Delete this product permanently?')) return;
  await axios.post('includes/product.php', { action: 'delete', id });
  loadData();
}

document.getElementById('product-form').onsubmit = async function (e) {
  e.preventDefault();
  const formData = new FormData(this);
  const isEdit = document.getElementById('edit-id').value;
  formData.append('action', isEdit ? 'update' : 'create');

  try {
    await axios.post('includes/product.php', formData);
    closeModal();
    loadData();
  } catch (err) {
    alert('Failed to save product');
  }
};

document.getElementById('images').addEventListener('change', function (e) {
  const preview = document.getElementById('image-preview');
  preview.innerHTML = '';
  Array.from(e.target.files).forEach(file => {
    const reader = new FileReader();
    reader.onload = ev => {
      preview.innerHTML += `<img src="${ev.target.result}" class="w-20 h-20 object-cover rounded border">`;
    };
    reader.readAsDataURL(file);
  });
});

loadData();
</script>

</body>
</html>