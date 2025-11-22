<?php
session_start();
require_once '../../config/db.php'; // your PDO connection

header('Content-Type: application/json');

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

$uploadDir = '../upload/products/';
if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);

$action = $_POST['action'] ?? $_GET['action'] ?? '';

try {
    switch ($action) {
        case 'get_all':
            $stmt = $conn->query("SELECT p.*, c.category_name FROM products p LEFT JOIN categories c ON p.category_id = c.id ORDER BY p.created_at DESC");
            $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
            echo json_encode($products);
            break;
         case 'get_one':
            $id = (int)$_GET['id'];
            $stmt = $conn->prepare("SELECT p.*, c.category_name FROM products p LEFT JOIN categories c ON p.category_id = c.id WHERE p.id = ?");
            $stmt->execute([$id]);
            echo json_encode($stmt->fetch(PDO::FETCH_ASSOC));
    break;
        case 'create':
        case 'update':
            $name = trim($_POST['product_name']);
            $desc = $_POST['product_description'] ?? '';
            $cat = (int)$_POST['category_id'];
            $price = (float)$_POST['price'];
            $stock = (int)$_POST['stock_quantity'];
            $brand = trim($_POST['brand'] ?? '');

            if (empty($name) || $cat <= 0 || $price <= 0 || $stock < 0) {
                throw new Exception('Invalid input');
            }

            $imagePaths = '';
            if (!empty($_FILES['images']['name'][0])) {
                $paths = [];
                foreach ($_FILES['images']['tmp_name'] as $k => $tmp) {
                    $ext = pathinfo($_FILES['images']['name'][$k], PATHINFO_EXTENSION);
                    $filename = uniqid() . '.' . $ext;
                    $dest = $uploadDir . $filename;
                    if (move_uploaded_file($tmp, $dest)) {
                        $paths[] = "upload/products/" . $filename;
                    }
                }
                $imagePaths = implode(',', $paths);
            }

            if ($action === 'create') {
                $stmt = $conn->prepare("INSERT INTO products (product_name, product_description, category_id, price, stock_quantity, brand, images) VALUES (?, ?, ?, ?, ?, ?, ?)");
                $stmt->execute([$name, $desc, $cat, $price, $stock, $brand, $imagePaths]);
            } else {
                $id = (int)$_POST['id'];
                $old = $conn->query("SELECT images FROM products WHERE id = $id")->fetchColumn();
                $newImages = $old ? $old . ',' . $imagePaths : $imagePaths;
                $stmt = $conn->prepare("UPDATE products SET product_name=?, product_description=?, category_id=?, price=?, stock_quantity=?, brand=?, images=? WHERE id=?");
                $stmt->execute([$name, $desc, $cat, $price, $stock, $brand, trim($newImages, ','), $id]);
            }

            echo json_encode(['success' => true]);
            break;
          

        case 'delete':
            $id = (int)$_POST['id'];
            $stmt = $conn->prepare("DELETE FROM products WHERE id = ?");
            $stmt->execute([$id]);
            echo json_encode(['success' => true]);
            break;

        default:
            echo json_encode(['success' => false, 'message' => 'Invalid action']);
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>