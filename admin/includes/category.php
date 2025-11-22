<?php
session_start();
require_once 'db.php';

header('Content-Type: application/json');

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

$action = $_POST['action'] ?? $_GET['action'] ?? '';

try {
    switch ($action) {
        case 'get_all':
            $stmt = $conn->query("SELECT * FROM categories ORDER BY display_order, created_at DESC");
            $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
            echo json_encode($categories);
            break;

        case 'create':
            $category_name = trim($_POST['category_name']);
            $image_url = trim($_POST['image_url']);
            $display_order = intval($_POST['display_order']);

            if (empty($category_name)) {
                throw new Exception('Category name is required');
            }

            $stmt = $conn->prepare("INSERT INTO categories (category_name, image_url, display_order) VALUES (?, ?, ?)");
            $stmt->execute([$category_name, $image_url, $display_order]);
            
            echo json_encode(['success' => true, 'message' => 'Category created successfully']);
            break;

        case 'update':
            $id = intval($_POST['id']);
            $category_name = trim($_POST['category_name']);
            $image_url = trim($_POST['image_url']);
            $display_order = intval($_POST['display_order']);

            if (empty($category_name)) {
                throw new Exception('Category name is required');
            }

            $stmt = $conn->prepare("UPDATE categories SET category_name = ?, image_url = ?, display_order = ? WHERE id = ?");
            $stmt->execute([$category_name, $image_url, $display_order, $id]);
            
            echo json_encode(['success' => true, 'message' => 'Category updated successfully']);
            break;

        case 'delete':
            $id = intval($_POST['id']);
            
            $stmt = $conn->prepare("SELECT COUNT(*) FROM products WHERE category_id = ?");
            $stmt->execute([$id]);
            $productCount = $stmt->fetchColumn();

            if ($productCount > 0) {
                echo json_encode(['success' => false, 'message' => 'Cannot delete category with existing products']);
                exit;
            }

            $stmt = $conn->prepare("DELETE FROM categories WHERE id = ?");
            $stmt->execute([$id]);
            
            echo json_encode(['success' => true, 'message' => 'Category deleted successfully']);
            break;

        default:
            echo json_encode(['success' => false, 'message' => 'Invalid action']);
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>