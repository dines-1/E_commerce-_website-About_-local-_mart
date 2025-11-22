<?php
session_start();
require_once '../Database.php';
$conn = dbconnect();

header('Content-Type: application/json');

if ($_POST['email'] && $_POST['password']) {
    $stmt = $conn->prepare("SELECT id, firstname, password, role FROM users WHERE email = ?");
    $stmt->execute([$_POST['email']]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($_POST['password'], $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['firstname'] = $user['firstname'];
        $_SESSION['role'] = $user['role'];
        $_SESSION['loggedin'] = true;

        echo json_encode(['success' => true, 'role' => $user['role']]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Invalid credentials']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Missing data']);
}