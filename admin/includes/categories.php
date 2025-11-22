<?php
session_start();
require_once '../../config/db.php';

header('Content-Type: application/json');

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

$action = $_POST['action'] ?? $_GET['action'] ?? '';

try {
    switch ($action) {
        case 'get_all':
            $search = $_GET['search'] ?? '';
            
            if (!empty($search)) {
                $stmt = $conn->prepare("SELECT * FROM categories WHERE category_name LIKE ? ORDER BY display_order, created_at DESC");
                $stmt->execute(["%$search%"]);
            } else {
                $stmt = $conn->query("SELECT * FROM categories ORDER BY display_order, created_at DESC");
            }
            
            $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Convert image paths to full URLs
            foreach ($categories as &$category) {
                if (!empty($category['image_url']) && !filter_var($category['image_url'], FILTER_VALIDATE_URL)) {
                    $category['image_url'] = getFullImageUrl($category['image_url']);
                }
            }
            
            echo json_encode($categories);
            break;

        case 'get_one':
            $id = intval($_GET['id']);
            $stmt = $conn->prepare("SELECT * FROM categories WHERE id = ?");
            $stmt->execute([$id]);
            $category = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($category && !empty($category['image_url']) && !filter_var($category['image_url'], FILTER_VALIDATE_URL)) {
                $category['image_url'] = getFullImageUrl($category['image_url']);
            }
            
            echo json_encode($category);
            break;

        case 'create':
            $category_name = trim($_POST['category_name']);
            $display_order = intval($_POST['display_order'] ?? 0);

            if (empty($category_name)) {
                throw new Exception('Category name is required');
            }

            // Handle file upload
            $image_url = '';
            if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
                $image_url = uploadImage($_FILES['image']);
            }

            $stmt = $conn->prepare("INSERT INTO categories (category_name, image_url, display_order) VALUES (?, ?, ?)");
            $stmt->execute([$category_name, $image_url, $display_order]);
            
            $category_id = $conn->lastInsertId();
            
            // Get the created category with full image URL
            $stmt = $conn->prepare("SELECT * FROM categories WHERE id = ?");
            $stmt->execute([$category_id]);
            $category = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($category && !empty($category['image_url']) && !filter_var($category['image_url'], FILTER_VALIDATE_URL)) {
                $category['image_url'] = getFullImageUrl($category['image_url']);
            }
            
            echo json_encode([
                'success' => true, 
                'message' => 'Category created successfully',
                'category' => $category
            ]);
            break;

        case 'update':
            $id = intval($_POST['id']);
            $category_name = trim($_POST['category_name']);
            $display_order = intval($_POST['display_order'] ?? 0);

            if (empty($category_name)) {
                throw new Exception('Category name is required');
            }

            // Get current category data
            $stmt = $conn->prepare("SELECT image_url FROM categories WHERE id = ?");
            $stmt->execute([$id]);
            $current_category = $stmt->fetch(PDO::FETCH_ASSOC);
            
            $image_url = $current_category['image_url'] ?? '';

            // Handle file upload if new image is provided
            if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
                // Delete old image if exists
                if (!empty($image_url)) {
                    deleteImage($image_url);
                }
                $image_url = uploadImage($_FILES['image']);
            }

            $stmt = $conn->prepare("UPDATE categories SET category_name = ?, image_url = ?, display_order = ? WHERE id = ?");
            $stmt->execute([$category_name, $image_url, $display_order, $id]);
            
            // Get the updated category with full image URL
            $stmt = $conn->prepare("SELECT * FROM categories WHERE id = ?");
            $stmt->execute([$id]);
            $category = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($category && !empty($category['image_url']) && !filter_var($category['image_url'], FILTER_VALIDATE_URL)) {
                $category['image_url'] = getFullImageUrl($category['image_url']);
            }
            
            echo json_encode([
                'success' => true, 
                'message' => 'Category updated successfully',
                'category' => $category
            ]);
            break;

        case 'delete':
            $id = intval($_POST['id']);
            
            // Check if category has products
            $stmt = $conn->prepare("SELECT COUNT(*) FROM products WHERE category_id = ?");
            $stmt->execute([$id]);
            $productCount = $stmt->fetchColumn();

            if ($productCount > 0) {
                echo json_encode(['success' => false, 'message' => 'Cannot delete category with existing products']);
                exit;
            }

            // Get category image before deletion
            $stmt = $conn->prepare("SELECT image_url FROM categories WHERE id = ?");
            $stmt->execute([$id]);
            $category = $stmt->fetch(PDO::FETCH_ASSOC);
            
            // Delete category
            $stmt = $conn->prepare("DELETE FROM categories WHERE id = ?");
            $stmt->execute([$id]);
            
            // Delete associated image
            if ($category && !empty($category['image_url'])) {
                deleteImage($category['image_url']);
            }
            
            echo json_encode(['success' => true, 'message' => 'Category deleted successfully']);
            break;

        default:
            echo json_encode(['success' => false, 'message' => 'Invalid action']);
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}

function uploadImage($file) {
    $uploadDir = '../uploads/categories/';
    
    // Create directory if it doesn't exist
    if (!file_exists($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }
    
    // Validate file type
    $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
    $fileType = mime_content_type($file['tmp_name']);
    
    if (!in_array($fileType, $allowedTypes)) {
        throw new Exception('Invalid file type. Only JPEG, PNG, GIF, and WebP images are allowed.');
    }
    
    // Validate file size (max 5MB)
    if ($file['size'] > 5 * 1024 * 1024) {
        throw new Exception('File size too large. Maximum size is 5MB.');
    }
    
    // Generate unique filename
    $fileExtension = pathinfo($file['name'], PATHINFO_EXTENSION);
    $fileName = uniqid() . '_' . time() . '.' . $fileExtension;
    $filePath = $uploadDir . $fileName;
    
    if (move_uploaded_file($file['tmp_name'], $filePath)) {
        return 'uploads/categories/' . $fileName; // Return relative path
    } else {
        throw new Exception('Failed to upload image');
    }
}

function deleteImage($imagePath) {
    // Remove the base URL if it's a full URL
    $relativePath = str_replace(getBaseUrl(), '', $imagePath);
    $fullPath = '../' . ltrim($relativePath, '/');
    
    if (file_exists($fullPath) && is_file($fullPath)) {
        unlink($fullPath);
    }
}

function getFullImageUrl($relativePath) {
    return getBaseUrl() . $relativePath;
}

function getBaseUrl() {
    $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'];
    $scriptPath = dirname($_SERVER['SCRIPT_NAME']);
    
    return $protocol . '://' . $host . $scriptPath . '/';
}
?>