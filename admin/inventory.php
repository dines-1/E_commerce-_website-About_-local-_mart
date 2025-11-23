<?php
// session_start();
// if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
//     header('Location: ../public/auth.html');
//     exit;
//}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inventory Management • Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <script src="https://unpkg.com/axios/dist/axios.min.js"></script>
</head>
<body class="bg-gray-100">
    
    <?php include 'includes/sidebar.php'; ?>
    
    <div class="ml-64">
        <?php include 'includes/header.php'; ?>
        
        <main class="p-6">
            <div class="bg-white rounded-xl shadow-md p-6 mb-6">
                <div class="flex justify-between items-center">
                    <h1 class="text-3xl font-bold text-gray-800">Inventory Management</h1>
                    <div class="flex gap-4">
                        <div class="relative">
                            <input type="text" id="search-input" placeholder="Search products..." 
                                   class="pl-10 pr-4 py-2 border rounded-lg w-64 focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <i class="fas fa-search absolute left-3 top-3 text-gray-400"></i>
                        </div>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
                <div class="bg-white rounded-xl shadow p-6">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-blue-100 text-blue-600 mr-4">
                            <i class="fas fa-boxes text-xl"></i>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">Total Products</p>
                            <h3 class="text-2xl font-bold" id="total-products">0</h3>
                        </div>
                    </div>
                </div>
                <div class="bg-white rounded-xl shadow p-6">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-green-100 text-green-600 mr-4">
                            <i class="fas fa-check-circle text-xl"></i>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">In Stock</p>
                            <h3 class="text-2xl font-bold" id="in-stock">0</h3>
                        </div>
                    </div>
                </div>
                <div class="bg-white rounded-xl shadow p-6">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-orange-100 text-orange-600 mr-4">
                            <i class="fas fa-exclamation-triangle text-xl"></i>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">Low Stock</p>
                            <h3 class="text-2xl font-bold" id="low-stock">0</h3>
                        </div>
                    </div>
                </div>
                <div class="bg-white rounded-xl shadow p-6">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-red-100 text-red-600 mr-4">
                            <i class="fas fa-times-circle text-xl"></i>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">Out of Stock</p>
                            <h3 class="text-2xl font-bold" id="out-of-stock">0</h3>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-md overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full">
                        <thead>
                            <tr class="bg-gray-50 border-b">
                                <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700">Product</th>
                                <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700">Category</th>
                                <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700">Current Stock</th>
                                <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700">Min Stock Level</th>
                                <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700">Status</th>
                                <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700">Last Updated</th>
                                <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700">Actions</th>
                            </tr>
                        </thead>
                        <tbody id="inventory-table">
                            <tr>
                                <td colspan="7" class="text-center py-16 text-gray-500">
                                    <i class="fas fa-spinner fa-spin text-4xl"></i><br>
                                    Loading inventory...
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </main>
    </div>

    <!-- Update Stock Modal -->
    <div id="update-stock-modal" class="fixed inset-0 bg-black bg-opacity-50 hidden flex items-center justify-center z-50 p-4">
        <div class="bg-white rounded-xl w-full max-w-md p-8">
            <h3 class="text-2xl font-bold mb-6">Update Stock</h3>
            <form id="update-stock-form">
                <input type="hidden" id="update-product-id">
                
                <div class="mb-4">
                    <label class="block text-sm font-medium mb-2">Product Name</label>
                    <p id="product-name" class="font-semibold text-gray-800"></p>
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium mb-2">Current Stock</label>
                    <p id="current-stock" class="text-gray-600"></p>
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium mb-2">Action</label>
                    <select id="stock-action" class="w-full px-4 py-3 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="add">Add Stock</option>
                        <option value="remove">Remove Stock</option>
                        <option value="set">Set Stock</option>
                    </select>
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium mb-2">Quantity</label>
                    <input type="number" id="stock-quantity" min="1" required 
                           class="w-full px-4 py-3 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>

                <div class="mb-6">
                    <label class="block text-sm font-medium mb-2">Reason</label>
                    <textarea id="stock-reason" rows="3" placeholder="Enter reason for stock update..."
                              class="w-full px-4 py-3 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"></textarea>
                </div>

                <div class="flex gap-4">
                    <button type="button" onclick="closeUpdateModal()" class="flex-1 py-3 border rounded-lg">Cancel</button>
                    <button type="submit" class="flex-1 bg-blue-600 text-white py-3 rounded-lg hover:bg-blue-700">
                        Update Stock
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
    let inventory = [];

    async function loadInventory() {
        try {
            const res = await axios.get('../api/inventory.php?action=get_all');
            inventory = res.data;
            renderInventory(inventory);
            updateStats();
        } catch (err) {
            document.getElementById('inventory-table').innerHTML = `
                <tr>
                    <td colspan="7" class="text-center py-20 text-red-600">
                        <i class="fas fa-exclamation-triangle text-4xl mb-4 block"></i>
                        Failed to load inventory
                    </td>
                </tr>`;
        }
    }

    function renderInventory(products) {
        const tbody = document.getElementById('inventory-table');
        
        if (products.length === 0) {
            tbody.innerHTML = `
                <tr>
                    <td colspan="7" class="text-center py-20 text-gray-500">
                        <i class="fas fa-box-open text-6xl mb-4 block opacity-30"></i>
                        No products found
                    </td>
                </tr>`;
            return;
        }

        tbody.innerHTML = products.map(p => {
            const status = getStockStatus(p.stock_quantity, p.min_stock_level);
            const statusClass = {
                'In Stock': 'bg-green-100 text-green-800',
                'Low Stock': 'bg-orange-100 text-orange-800',
                'Out of Stock': 'bg-red-100 text-red-800'
            }[status];

            return `
                <tr class="border-b hover:bg-gray-50">
                    <td class="px-6 py-4">
                        <div class="flex items-center">
                            <img src="${getFirstImage(p.images)}" class="w-12 h-12 object-cover rounded mr-4">
                            <div>
                                <p class="font-medium">${p.product_name}</p>
                                <p class="text-sm text-gray-500">₹${p.price}</p>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4">${p.category_name}</td>
                    <td class="px-6 py-4 font-semibold">${p.stock_quantity}</td>
                    <td class="px-6 py-4">${p.min_stock_level}</td>
                    <td class="px-6 py-4">
                        <span class="px-3 py-1 rounded-full text-xs font-medium ${statusClass}">
                            ${status}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-500">${formatDate(p.updated_at)}</td>
                    <td class="px-6 py-4">
                        <button onclick="openUpdateModal(${p.id}, '${p.product_name}', ${p.stock_quantity})" 
                                class="text-blue-600 hover:underline mr-4">Update Stock</button>
                        <button onclick="viewStockHistory(${p.id})" 
                                class="text-green-600 hover:underline">History</button>
                    </td>
                </tr>
            `;
        }).join('');
    }

    function getFirstImage(imagesJson) {
        if (!imagesJson) return '../assets/default-product.jpg';
        try {
            const imgs = JSON.parse(imagesJson);
            return imgs[0] || '../assets/default-product.jpg';
        } catch {
            return '../assets/default-product.jpg';
        }
    }

    function getStockStatus(stock, minStock) {
        if (stock === 0) return 'Out of Stock';
        if (stock <= minStock) return 'Low Stock';
        return 'In Stock';
    }

    function formatDate(dateString) {
        return new Date(dateString).toLocaleDateString();
    }

    function updateStats() {
        const total = inventory.length;
        const inStock = inventory.filter(p => p.stock_quantity > p.min_stock_level).length;
        const lowStock = inventory.filter(p => p.stock_quantity > 0 && p.stock_quantity <= p.min_stock_level).length;
        const outOfStock = inventory.filter(p => p.stock_quantity === 0).length;

        document.getElementById('total-products').textContent = total;
        document.getElementById('in-stock').textContent = inStock;
        document.getElementById('low-stock').textContent = lowStock;
        document.getElementById('out-of-stock').textContent = outOfStock;
    }

    function openUpdateModal(productId, productName, currentStock) {
        document.getElementById('update-product-id').value = productId;
        document.getElementById('product-name').textContent = productName;
        document.getElementById('current-stock').textContent = currentStock;
        document.getElementById('stock-quantity').value = '';
        document.getElementById('stock-reason').value = '';
        document.getElementById('update-stock-modal').classList.remove('hidden');
    }

    function closeUpdateModal() {
        document.getElementById('update-stock-modal').classList.add('hidden');
    }

    document.getElementById('update-stock-form').onsubmit = async function(e) {
        e.preventDefault();
        
        const formData = {
            product_id: document.getElementById('update-product-id').value,
            action: document.getElementById('stock-action').value,
            quantity: document.getElementById('stock-quantity').value,
            reason: document.getElementById('stock-reason').value
        };

        try {
            await axios.post('../api/inventory.php?action=update_stock', formData);
            closeUpdateModal();
            loadInventory();
            alert('Stock updated successfully!');
        } catch (err) {
            alert('Error updating stock: ' + (err.response?.data?.message || err.message));
        }
    };

    function viewStockHistory(productId) {
        window.location.href = `stock-history.php?product_id=${productId}`;
    }

    function exportInventory() {
        // Simple CSV export
        const headers = ['Product Name', 'Category', 'Current Stock', 'Min Stock', 'Status', 'Price'];
        const csvData = inventory.map(p => [
            p.product_name,
            p.category_name,
            p.stock_quantity,
            p.min_stock_level,
            getStockStatus(p.stock_quantity, p.min_stock_level),
            p.price
        ]);

        const csvContent = [headers, ...csvData].map(row => row.join(',')).join('\n');
        const blob = new Blob([csvContent], { type: 'text/csv' });
        const url = window.URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.href = url;
        a.download = `inventory-${new Date().toISOString().split('T')[0]}.csv`;
        a.click();
        window.URL.revokeObjectURL(url);
    }

    // Search functionality
    document.getElementById('search-input').addEventListener('input', function() {
        const query = this.value.toLowerCase();
        const filtered = inventory.filter(p => 
            p.product_name.toLowerCase().includes(query) ||
            p.category_name.toLowerCase().includes(query)
        );
        renderInventory(filtered);
    });

    loadInventory();
    </script>
</body>
</html>