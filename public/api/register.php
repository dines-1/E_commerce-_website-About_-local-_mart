<?php
require_once '../Database.php';
$conn = dbconnect();

header('Content-Type: application/json');

$firstname = trim($_POST['firstname']);
$lastname = trim($_POST['lastname']);
$email = trim($_POST['email']);
$phone = $_POST['phone'] ?? null;
$password = password_hash($_POST['password'], PASSWORD_DEFAULT);
$role = 'customer'; // only allow customer signup

if (!$firstname || !$email || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['success' => false, 'message' => 'Invalid data']);
    exit;
}

try {
    $stmt = $conn->prepare("INSERT INTO users (firstname, lastname, email, phone, password, role) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->execute([$firstname, $lastname, $email, $phone, $password, $role]);
    echo json_encode(['success' => true]);
} catch(PDOException $e) {
    if ($e->getCode() == 23000) {
        echo json_encode(['success' => false, 'message' => 'Email already registered']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Database error']);
    }
}