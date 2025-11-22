<?php session_start(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Customers • Local Mart Admin</title>
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
        <div class="p-6 border-b">
          <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <h1 class="text-3xl font-bold text-gray-800">Customers Management</h1>
            
            <!-- Search Bar -->
            <div class="relative w-full sm:w-80">
              <input type="text" id="search-input" placeholder="Search by name, email or phone..." 
                     class="w-full pl-10 pr-4 py-3 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
              <i class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
            </div>
          </div>
        </div>

        <div class="overflow-x-auto">
          <table class="min-w-full">
            <thead>
              <tr class="text-left text-sm font-semibold text-gray-700 bg-gray-50">
                <th class="px-6 py-4">Name</th>
                <th class="px-6 py-4">Email</th>
                <th class="px-6 py-4">Phone</th>
                <th class="px-6 py-4">Joined</th>
                <th class="px-6 py-4">Actions</th>
              </tr>
            </thead>
            <tbody id="users-table">
              <tr><td colspan="5" class="text-center py-16 text-gray-500">
                <i class="fas fa-spinner fa-spin text-4xl"></i><br>Loading customers...
              </td></tr>
            </tbody>
          </table>
        </div>
      </div>
    </main>
  </div>
</div>

<!-- Edit Modal -->
<div id="edit-modal" class="fixed inset-0 bg-black bg-opacity-50 hidden flex items-center justify-center z-50 p-4">
  <div class="bg-white rounded-xl w-full max-w-md p-8">
    <h3 class="text-2xl font-bold mb-6">Edit Customer</h3>
    <form id="edit-form">
      <input type="hidden" id="edit-id">
      <input type="text" id="edit-firstname" required placeholder="First Name" class="w-full px-4 py-3 border rounded-lg mb-4">
      <input type="text" id="edit-lastname" required placeholder="Last Name" class="w-full px-4 py-3 border rounded-lg mb-4">
      <input type="email" id="edit-email" required placeholder="Email" class="w-full px-4 py-3 border rounded-lg mb-4">
      <input type="text" id="edit-phone" placeholder="Phone" class="w-full px-4 py-3 border rounded-lg mb-6">
      <div class="flex gap-4">
        <button type="button" onclick="closeEditModal()" class="flex-1 py-3 border rounded-lg">Cancel</button>
        <button type="submit" class="flex-1 bg-blue-600 text-white py-3 rounded-lg hover:bg-blue-700">Update</button>
      </div>
    </form>
  </div>
</div>

<!-- Change Password Modal -->
<div id="password-modal" class="fixed inset-0 bg-black bg-opacity-50 hidden flex items-center justify-center z-50 p-4">
  <div class="bg-white rounded-xl w-full max-w-md p-8">
    <h3 class="text-2xl font-bold mb-6">Change Password</h3>
    <form id="password-form">
      <input type="hidden" id="pw-user-id">
      <input type="password" id="new-password" required placeholder="New Password" class="w-full px-4 py-3 border rounded-lg mb-4">
      <input type="password" id="confirm-password" required placeholder="Confirm Password" class="w-full px-4 py-3 border rounded-lg mb-6">
      <div class="flex gap-4">
        <button type="button" onclick="closePasswordModal()" class="flex-1 py-3 border rounded-lg">Cancel</button>
        <button type="submit" class="flex-1 bg-green-600 text-white py-3 rounded-lg hover:bg-green-700">Update Password</button>
      </div>
    </form>
  </div>
</div>

<script>
let allUsers = [];

async function loadUsers() {
  try {
    const res = await axios.get('includes/users.php?action=get_all');
    if (res.data && Array.isArray(res.data)) {
      allUsers = res.data;
      renderUsers(allUsers);
    } else {
      throw new Error('Invalid response format');
    }
  } catch (err) {
    console.error('Error loading users:', err);
    document.getElementById('users-table').innerHTML = `
      <tr>
        <td colspan="5" class="text-center py-20 text-red-600">
          <i class="fas fa-exclamation-triangle text-4xl mb-4 block"></i>
          Failed to load customers. Please check console for details.
        </td>
      </tr>`;
  }
}

function renderUsers(users) {
  const tbody = document.getElementById('users-table');
  if (users.length === 0) {
    tbody.innerHTML = `
      <tr>
        <td colspan="5" class="text-center py-20 text-gray-500 text-xl">
          <i class="fas fa-users text-6xl mb-4 block opacity-30"></i>
          No customers found
        </td>
      </tr>`;
    return;
  }

  tbody.innerHTML = users.map(u => `
    <tr class="border-t hover:bg-gray-50">
      <td class="px-6 py-4 font-medium">${escapeHtml(u.firstname)} ${escapeHtml(u.lastname)}</td>
      <td class="px-6 py-4 text-gray-600">${escapeHtml(u.email)}</td>
      <td class="px-6 py-4">${u.phone ? escapeHtml(u.phone) : '—'}</td>
      <td class="px-6 py-4 text-sm text-gray-500">${new Date(u.created_at).toLocaleDateString()}</td>
      <td class="px-6 py-4 flex gap-3 text-sm">
        <button onclick="openEditModal(${u.id}, '${escapeHtml(u.firstname)}', '${escapeHtml(u.lastname)}', '${escapeHtml(u.email)}', '${u.phone ? escapeHtml(u.phone) : ''}')" 
                class="text-blue-600 hover:underline">Edit</button>
        <button onclick="changePassword(${u.id})" 
                class="text-orange-600 hover:underline">Change Password</button>
        <button onclick="deleteUser(${u.id})" 
                class="text-red-600 hover:underline">Delete</button>
      </td>
    </tr>
  `).join('');
}

function escapeHtml(unsafe) {
  return unsafe
    .replace(/&/g, "&amp;")
    .replace(/</g, "&lt;")
    .replace(/>/g, "&gt;")
    .replace(/"/g, "&quot;")
    .replace(/'/g, "&#039;");
}

document.getElementById('search-input').addEventListener('input', function() {
  const query = this.value.toLowerCase();
  const filtered = allUsers.filter(u => 
    u.firstname.toLowerCase().includes(query) ||
    u.lastname.toLowerCase().includes(query) ||
    u.email.toLowerCase().includes(query) ||
    (u.phone && u.phone.includes(query))
  );
  renderUsers(filtered);
});

function openEditModal(id, firstname, lastname, email, phone) {
  document.getElementById('edit-id').value = id;
  document.getElementById('edit-firstname').value = firstname;
  document.getElementById('edit-lastname').value = lastname;
  document.getElementById('edit-email').value = email;
  document.getElementById('edit-phone').value = phone;
  document.getElementById('edit-modal').classList.remove('hidden');
}

function closeEditModal() {
  document.getElementById('edit-modal').classList.add('hidden');
}

document.getElementById('edit-form').onsubmit = async function(e) {
  e.preventDefault();
  try {
    const data = {
      action: 'update',
      id: document.getElementById('edit-id').value,
      firstname: document.getElementById('edit-firstname').value,
      lastname: document.getElementById('edit-lastname').value,
      email: document.getElementById('edit-email').value,
      phone: document.getElementById('edit-phone').value
    };
    await axios.post('includes/users.php', data);
    closeEditModal();
    loadUsers();
  } catch (err) {
    alert('Error updating user: ' + (err.response?.data?.message || err.message));
  }
};

function changePassword(id) {
  document.getElementById('pw-user-id').value = id;
  document.getElementById('password-modal').classList.remove('hidden');
}

function closePasswordModal() {
  document.getElementById('password-modal').classList.add('hidden');
  document.getElementById('password-form').reset();
}

document.getElementById('password-form').onsubmit = async function(e) {
  e.preventDefault();
  try {
    const newPass = document.getElementById('new-password').value;
    const confirmPass = document.getElementById('confirm-password').value;
    if (newPass !== confirmPass) {
      alert('Passwords do not match!');
      return;
    }
    if (newPass.length < 6) {
      alert('Password must be at least 6 characters long!');
      return;
    }
    await axios.post('includes/users.php', {
      action: 'change_password',
      id: document.getElementById('pw-user-id').value,
      password: newPass
    });
    closePasswordModal();
    alert('Password updated successfully');
  } catch (err) {
    alert('Error updating password: ' + (err.response?.data?.message || err.message));
  }
};

async function deleteUser(id) {
  if (!confirm('Are you sure you want to delete this customer permanently?')) return;
  try {
    await axios.post('includes/users.php', { action: 'delete', id });
    loadUsers();
  } catch (err) {
    alert('Error deleting user: ' + (err.response?.data?.message || err.message));
  }
}

loadUsers();
</script>

</body>
</html>