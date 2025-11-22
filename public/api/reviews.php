<?php
require_once '../config/db.php';
header('Content-Type: application/json');

try {
    $conn = dbconnect();
    
    $limit = isset($_GET['limit']) ? intval($_GET['limit']) : 6;
    
    $sql = "
        SELECT 
            r.id,
            r.rating,
            r.review_text,
            r.created_at,
            u.firstname,
            u.lastname,
            p.product_name
        FROM reviews r
        JOIN users u ON r.user_id = u.id
        JOIN products p ON r.product_id = p.id
        ORDER BY r.created_at DESC
        LIMIT ?
    ";
    
    $stmt = $conn->prepare($sql);
    $stmt->execute([$limit]);
    $reviews = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode($reviews);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Failed to load reviews']);
}
?>