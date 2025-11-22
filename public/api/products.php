<?php
require_once '../config/db.php';
header('Content-Type: application/json');

try {
    $conn = dbconnect();
    
    $limit = isset($_GET['limit']) ? intval($_GET['limit']) : 0;
    $category_id = isset($_GET['category_id']) ? intval($_GET['category_id']) : 0;
    
    $sql = "
        SELECT 
            p.id,
            p.product_name,
            p.product_description as description,
            p.price,
            p.images,
            p.stock_quantity,
            p.brand,
            p.created_at,
            c.category_name,
            c.id as category_id
        FROM products p
        LEFT JOIN categories c ON p.category_id = c.id
        WHERE p.stock_quantity > 0
    ";
    
    $params = [];
    
    if ($category_id > 0) {
        $sql .= " AND p.category_id = ?";
        $params[] = $category_id;
    }
    
    $sql .= " ORDER BY p.created_at DESC";
    
    if ($limit > 0) {
        $sql .= " LIMIT ?";
        $params[] = $limit;
    }
    
    $stmt = $conn->prepare($sql);
    $stmt->execute($params);
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    foreach ($products as &$product) {
        if (!empty($product['images'])) {
            $images = json_decode($product['images'], true);
            if (is_array($images)) {
                foreach ($images as &$image) {
                    if (!filter_var($image, FILTER_VALIDATE_URL)) {
                        $image = getBaseUrl() . $image;
                    }
                }
                $product['images'] = json_encode($images);
            }
        }
    }
    
    echo json_encode($products);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Failed to load products: ' . $e->getMessage()]);
}

function getBaseUrl() {
    $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'];
    $scriptPath = dirname($_SERVER['SCRIPT_NAME']);
    return $protocol . '://' . $host . $scriptPath . '/../';
}
?>