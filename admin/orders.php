<?php session_start(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Orders • Local Mart Admin</title>
  <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/vue@3/dist/vue.global.js"></script>
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
  <script src="https://unpkg.com/axios/dist/axios.min.js"></script>
  <style>
    .sidebar { transition: transform 0.3s ease; }
    @media (max-width: 1024px) {
      .sidebar-open { transform: translateX(0); }
      .sidebar-closed { transform: translateX(-100%); }
    }
    table { width: 100%; border-collapse: separate; border-spacing: 0; }
    th { background: #f8f9fa; position: sticky; top: 0; z-index: 10; }
    tr:hover { background: #f1f5f9; }
    .status-select { min-width: 140px; }
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
      <div class="bg-white rounded-xl shadow-md overflow-hidden">
        <div class="p-6 border-b">
          <h1 class="text-3xl font-bold text-gray-800">Orders Management</h1>
        </div>

        <div class="overflow-x-auto">
          <table class="min-w-full">
            <thead>
              <tr class="text-left text-sm font-semibold text-gray-700">
                <th class="px-6 py-4">Order #</th>
                <th class="px-6 py-4">Date</th>
                <th class="px-6 py-4">Customer</th>
                <th class="px-6 py-4">Phone</th>
                <th class="px-6 py-4">Total</th>
                <th class="px-6 py-4">Status</th>
                <th class="px-6 py-4">Action</th>
              </tr>
            </thead>
            <tbody id="orders-table" class="text-sm">
              <!-- Filled by JavaScript -->
              <tr><td colspan="7" class="text-center py-12 text-gray-500">
                <i class="fas fa-spinner fa-spin text-3xl"></i><br>Loading orders...
              </td></tr>
            </tbody>
          </table>
        </div>
      </div>
    </main>
  </div>
</div>

<script>
// Simple raw JS (no Vue needed)
async function loadOrders() {
  try {
    const res = await axios.get('includes/order.php?action=get_all');
    const orders = res.data;
    const tbody = document.getElementById('orders-table');
    
    if (orders.length === 0) {
      tbody.innerHTML = `<tr><td colspan="7" class="text-center py-20 text-gray-500 text-xl">
        <i class="fas fa-shopping-cart text-6xl mb-4 block opacity-30"></i>
        No orders found
      </td></tr>`;
      return;
    }

    tbody.innerHTML = orders.map(order => `
      <tr class="border-t">
        <td class="px-6 py-5 font-medium">#${order.order_number}</td>
        <td class="px-6 py-5 text-gray-600">${new Date(order.created_at).toLocaleString()}</td>
        <td class="px-6 py-5">${order.customer_name}</td>
        <td class="px-6 py-5">${order.customer_phone}</td>
        <td class="px-6 py-5 font-bold text-green-600">Rs.${parseFloat(order.total_amount).toFixed(2)}</td>
        <td class="px-6 py-5">
          <span class="px-3 py-1 rounded-full text-xs font-medium
            ${order.status === 'completed' ? 'bg-green-100 text-green-800' :
              order.status === 'pending' ? 'bg-yellow-100 text-yellow-800' :
              order.status === 'cancelled' ? 'bg-red-100 text-red-800' :
              order.status === 'confirmed' ? 'bg-blue-100 text-blue-800' :
              'bg-purple-100 text-purple-800'}">
            ${order.status.toUpperCase()}
          </span>
        </td>
        <td class="px-6 py-5">
          <select onchange="updateStatus(${order.id}, this.value)" 
                  class="status-select px-3 py-2 border rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
            <option value="pending" ${order.status==='pending'?'selected':''}>Pending</option>
            <option value="confirmed" ${order.status==='confirmed'?'selected':''}>Confirmed</option>
            <option value="ready" ${order.status==='ready'?'selected':''}>Ready</option>
            <option value="completed" ${order.status==='completed'?'selected':''}>Completed</option>
            <option value="cancelled" ${order.status==='cancelled'?'selected':''}>Cancelled</option>
          </select>
        </td>
      </tr>
      <tr class="bg-gray-50">
        <td colspan="7" class="px-6 py-3 text-xs text-gray-600">
          <details>
            <summary class="cursor-pointer font-medium text-blue-600 hover:underline">
              View Items (${order.items?.length || 0})
            </summary>
            <div class="mt-3 pl-6">
              ${order.items?.map(item => `
                <div class="flex justify-between py-1">
                  <span>• ${item.product_name} × ${item.quantity}</span>
                  <span>Rs.${parseFloat(item.total_price).toFixed(2)}</span>
                </div>
              `).join('') || '<em>No items</em>'}
            </div>
          </details>
        </td>
      </tr>
    `).join('');
  } catch (err) {
    document.getElementById('orders-table').innerHTML = 
      `<tr><td colspan="7" class="text-center py-20 text-red-600">Failed to load orders</td></tr>`;
  }
}

async function updateStatus(orderId, newStatus) {
  if (!confirm(`Change order status to "${newStatus.toUpperCase()}"?`)) {
    loadOrders(); 
    return;
  }

  try {
    await axios.post('includes/order.php', {
      action: 'update_status',
      order_id: orderId,
      status: newStatus
    });
    loadOrders();
  } catch (err) {
    alert('Failed to update status');
    loadOrders();
  }
}

loadOrders();
</script>

</body>
</html>