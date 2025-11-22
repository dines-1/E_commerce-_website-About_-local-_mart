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
            $stmt = $conn->query("SELECT id, firstname, lastname, email, phone, created_at FROM users WHERE role = 'customer' ORDER BY created_at DESC");
            $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
            echo json_encode($users);
            break;

        case 'update':
            $id = (int)$_POST['id'];
            $stmt = $conn->prepare("UPDATE users SET firstname=?, lastname=?, email=?, phone=? WHERE id=?");
            $stmt->execute([
                $_POST['firstname'],
                $_POST['lastname'],
                $_POST['email'],
                $_POST['phone'] ?: null,
                $id
            ]);
            echo json_encode(['success' => true, 'message' => 'User updated successfully']);
            break;

        case 'change_password':
            $id = (int)$_POST['id'];
            $hash = password_hash($_POST['password'], PASSWORD_DEFAULT);
            $stmt = $conn->prepare("UPDATE users SET password=? WHERE id=?");
            $stmt->execute([$hash, $id]);
            echo json_encode(['success' => true, 'message' => 'Password updated successfully']);
            break;

        case 'delete':
            $id = (int)$_POST['id'];
            $stmt = $conn->prepare("DELETE FROM users WHERE id=?");
            $stmt->execute([$id]);
            echo json_encode(['success' => true, 'message' => 'User deleted successfully']);
            break;

        default:
            echo json_encode(['success' => false, 'message' => 'Invalid action']);
    }
} catch (Exception $e) {
    error_log("Users API Error: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Database error occurred']);
}
?>