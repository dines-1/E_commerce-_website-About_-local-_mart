<?php session_start(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Categories • Local Mart Admin</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
  <script src="https://unpkg.com/axios/dist/axios.min.js"></script>
</head>
<body class="bg-gray-100">

<div class="flex h-screen overflow-hidden">
  <?php include 'includes/sidebar.php'; ?>
  <div class="flex-1 flex flex-col overflow-hidden">
    <?php include 'includes/header.php'; ?>

    <main class="flex-1 overflow-auto p-6 bg-gray-50">
      <div class="bg-white rounded-xl shadow-md overflow-hidden">
        <div class="p-6 border-b flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
          <h1 class="text-3xl font-bold text-gray-800">Categories Management</h1>
          <div class="flex flex-col md:flex-row gap-4 w-full md:w-auto">
            <div class="relative">
              <input 
                type="text" 
                id="search-input" 
                placeholder="Search categories..." 
                class="w-full md:w-64 px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
              >
              <i class="fas fa-search absolute right-3 top-3 text-gray-400"></i>
            </div>
            <button onclick="openAddModal()" class="bg-green-600 hover:bg-green-700 text-white px-6 py-2 rounded-lg flex items-center gap-2">
              <i class="fas fa-plus"></i> Add Category
            </button>
          </div>
        </div>

        <div class="overflow-x-auto">
          <table class="min-w-full">
            <thead>
              <tr class="text-left text-sm font-semibold text-gray-700 bg-gray-50">
                <th class="px-6 py-4">Image</th>
                <th class="px-6 py-4">Name</th>
                <th class="px-6 py-4">Order</th>
                <th class="px-6 py-4">Created</th>
                <th class="px-6 py-4">Actions</th>
              </tr>
            </thead>
            <tbody id="categories-table">
              <!-- Categories will be loaded here -->
            </tbody>
          </table>
          <div id="no-results" class="hidden text-center py-20 text-gray-500 text-xl">
            <i class="fas fa-search text-6xl mb-4 block opacity-30"></i>
            No categories found matching your search
          </div>
        </div>
      </div>
    </main>
  </div>
</div>

<!-- Modal with File Upload -->
<div id="cat-modal" class="fixed inset-0 bg-black bg-opacity-50 hidden flex items-center justify-center z-50 p-4">
  <div class="bg-white rounded-xl w-full max-w-md p-8">
    <h3 class="text-2xl font-bold mb-6" id="modal-title">Add Category</h3>
    <form id="cat-form" enctype="multipart/form-data">
      <input type="hidden" id="edit-id">

      <div class="mb-4">
        <label class="block text-sm font-medium mb-2">Category Name</label>
        <input type="text" id="cat-name" required class="w-full px-4 py-3 border rounded-lg">
      </div>

      <div class="mb-4">
        <label class="block text-sm font-medium mb-2">Category Image</label>
        <input type="file" id="cat-image" accept="image/*" class="w-full">
        <div id="image-preview" class="mt-3"></div>
      </div>

      <div class="mb-6">
        <label class="block text-sm font-medium mb-2">Display Order</label>
        <input type="number" id="cat-order" value="0" class="w-full px-4 py-3 border rounded-lg">
      </div>

      <div class="flex gap-4">
        <button type="button" onclick="closeModal()" class="flex-1 py-3 border rounded-lg">Cancel</button>
        <button type="submit" class="flex-1 bg-blue-600 text-white py-3 rounded-lg hover:bg-blue-700">
          <span id="submit-btn-text">Save Category</span>
        </button>
      </div>
    </form>
  </div>
</div>

<script>
let allCategories = [];

document.getElementById('cat-image').onchange = function(e) {
  const preview = document.getElementById('image-preview');
  preview.innerHTML = '';
  if (e.target.files[0]) {
    const reader = new FileReader();
    reader.onload = ev => {
      preview.innerHTML = `<img src="${ev.target.result}" class="w-32 h-32 object-cover rounded-lg border">`;
    };
    reader.readAsDataURL(e.target.files[0]);
  }
};

async function loadCategories() {
  const res = await axios.get('includes/categories.php?action=get_all');
  allCategories = res.data;
  displayCategories(allCategories);
}

function displayCategories(categories) {
  const tbody = document.getElementById('categories-table');
  const noResults = document.getElementById('no-results');
  
  if (categories.length === 0) {
    tbody.innerHTML = '';
    noResults.classList.remove('hidden');
    return;
  }
  
  noResults.classList.add('hidden');
  
  tbody.innerHTML = categories.map(c => `
    <tr class="border-t hover:bg-gray-50">
      <td class="px-6 py-4">
        ${c.image_url ? `<img src="${c.image_url}" class="w-16 h-16 object-cover rounded">` :
          `<div class="w-16 h-16 bg-gray-200 rounded flex items-center justify-center"><i class="fas fa-tag text-3xl text-gray-400"></i></div>`}
      </td>
      <td class="px-6 py-4 font-medium">${c.category_name}</td>
      <td class="px-6 py-4 text-gray-600">${c.display_order}</td>
      <td class="px-6 py-4 text-gray-500 text-sm">${new Date(c.created_at).toLocaleDateString()}</td>
      <td class="px-6 py-4">
        <button onclick="editCat(${c.id})" class="text-blue-600 mr-4 hover:underline">Edit</button>
        <button onclick="deleteCat(${c.id})" class="text-red-600 hover:underline">Delete</button>
      </td>
    </tr>
  `).join('');
}

function searchCategories() {
  const searchTerm = document.getElementById('search-input').value.toLowerCase();
  
  if (searchTerm === '') {
    displayCategories(allCategories);
    return;
  }
  
  const filteredCategories = allCategories.filter(category => 
    category.category_name.toLowerCase().includes(searchTerm)
  );
  
  displayCategories(filteredCategories);
}

function openAddModal() {
  document.getElementById('modal-title').textContent = 'Add Category';
  document.getElementById('submit-btn-text').textContent = 'Add Category';
  document.getElementById('cat-form').reset();
  document.getElementById('edit-id').value = '';
  document.getElementById('image-preview').innerHTML = '';
  document.getElementById('cat-modal').classList.remove('hidden');
}

function closeModal() {
  document.getElementById('cat-modal').classList.add('hidden');
}

async function editCat(id) {
  const res = await axios.get(`includes/categories.php?action=get_one&id=${id}`);
  const c = res.data;
  document.getElementById('modal-title').textContent = 'Edit Category';
  document.getElementById('submit-btn-text').textContent = 'Update Category';
  document.getElementById('edit-id').value = c.id;
  document.getElementById('cat-name').value = c.category_name;
  document.getElementById('cat-order').value = c.display_order;
  document.getElementById('image-preview').innerHTML = c.image_url 
    ? `<img src="${c.image_url}" class="w-32 h-32 object-cover rounded-lg border">` 
    : '';
  document.getElementById('cat-modal').classList.remove('hidden');
}

async function deleteCat(id) {
  if (!confirm('Delete this category?')) return;
  await axios.post('includes/categories.php', { action: 'delete', id });
  loadCategories();
}

document.getElementById('cat-form').onsubmit = async function(e) {
  e.preventDefault();
  
  const formData = new FormData();
  const id = document.getElementById('edit-id').value;
  formData.append('action', id ? 'update' : 'create');
  formData.append('category_name', document.getElementById('cat-name').value);
  formData.append('display_order', document.getElementById('cat-order').value);
  if (id) formData.append('id', id);
  
  const fileInput = document.getElementById('cat-image');
  if (fileInput.files[0]) {
    formData.append('image', fileInput.files[0]);
  }

  await axios.post('includes/categories.php', formData, {
    headers: { 'Content-Type': 'multipart/form-data' }
  });
  
  closeModal();
  loadCategories();
};

document.getElementById('search-input').addEventListener('input', searchCategories);

loadCategories();
</script>

</body>
</html>