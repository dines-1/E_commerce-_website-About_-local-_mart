<?php
session_start();
header('Content-Type: application/json');

require_once '../Database.php'; // your DB connection
$conn = dbconnect();

$totalProducts = $conn->query("SELECT COUNT(*) FROM products")->fetchColumn();
$totalOrders = $conn->query("SELECT COUNT(*) FROM orders")->fetchColumn();
$pendingOrders = $conn->query("SELECT COUNT(*) FROM orders WHERE status = 'pending'")->fetchColumn();
$totalRevenue = $conn->query("SELECT COALESCE(SUM(total_amount), 0) FROM orders WHERE status = 'completed'")->fetchColumn();

echo json_encode([
  'totalProducts' => (int)$totalProducts,
  'totalOrders' => (int)$totalOrders,
  'pendingOrders' => (int)$pendingOrders,
  'totalRevenue' => number_format((float)$totalRevenue, 2)
]);