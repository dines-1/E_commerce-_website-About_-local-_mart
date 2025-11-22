<?php
session_start();
require_once '../config/db.php';
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode([]);
    exit;
}

try {
    $conn = dbconnect();
    $user_id = $_SESSION['user_id'];
    
    $sql = "
        SELECT 
            c.id,
            c.quantity,
            p.id as product_id,
            p.product_name,
            p.price,
            p.images,
            p.stock_quantity
        FROM cart c
        JOIN products p ON c.product_id = p.id
        WHERE c.user_id = ?
        ORDER BY c.added_at DESC
    ";
    
    $stmt = $conn->prepare($sql);
    $stmt->execute([$user_id]);
    $cartItems = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode($cartItems);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Failed to load cart']);
}
?>