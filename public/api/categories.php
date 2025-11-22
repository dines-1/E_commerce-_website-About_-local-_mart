<?php
require_once '../config/db.php';
header('Content-Type: application/json');

try {
    $conn = dbconnect();
    
    $stmt = $conn->query("
        SELECT id, category_name, image_url, display_order, created_at 
        FROM categories 
        ORDER BY display_order, category_name
    ");
    
    $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    foreach ($categories as &$category) {
        if (!empty($category['image_url']) && !filter_var($category['image_url'], FILTER_VALIDATE_URL)) {
            $category['image_url'] = getBaseUrl() . $category['image_url'];
        }
    }
    
    echo json_encode($categories);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Failed to load categories']);
}

function getBaseUrl() {
    $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'];
    $scriptPath = dirname($_SERVER['SCRIPT_NAME']);
    return $protocol . '://' . $host . $scriptPath . '/../';
}
?>