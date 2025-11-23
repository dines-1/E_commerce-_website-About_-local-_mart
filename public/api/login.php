<?php
session_start();
require_once '../config/db.php';
header('Content-Type: application/json');

try {
    $conn = dbconnect();
    
    // Handle different actions
    $action = $_GET['action'] ?? '';
    
    if ($action === 'check') {
        // Check if user is logged in
        if (isset($_SESSION['user_id'])) {
            $stmt = $conn->prepare("SELECT id, firstname, lastname, email, role FROM users WHERE id = ?");
            $stmt->execute([$_SESSION['user_id']]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($user) {
                echo json_encode([
                    'loggedin' => true,
                    'user' => $user
                ]);
            } else {
                session_destroy();
                echo json_encode(['loggedin' => false]);
            }
        } else {
            echo json_encode(['loggedin' => false]);
        }
    }
    else if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Handle login
        $input = json_decode(file_get_contents('php://input'), true);
        $email = $input['email'] ?? $_POST['email'] ?? '';
        $password = $input['password'] ?? $_POST['password'] ?? '';

        if (empty($email) || empty($password)) {
            echo json_encode(['success' => false, 'message' => 'Email and password are required']);
            exit;
        }

        $stmt = $conn->prepare("SELECT id, firstname, lastname, email, password, role FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['firstname'] = $user['firstname'];
            $_SESSION['role'] = $user['role'];
            $_SESSION['loggedin'] = true;

            echo json_encode([
                'success' => true, 
                'role' => $user['role'],
                'message' => 'Login successful'
            ]);
        } else {
            echo json_encode([
                'success' => false, 
                'message' => 'Invalid email or password'
            ]);
        }
    }
    else {
        echo json_encode(['success' => false, 'message' => 'Invalid request']);
    }
    
} catch (Exception $e) {
    error_log("Auth error: " . $e->getMessage());
    echo json_encode([
        'success' => false, 
        'message' => 'Database error occurred'
    ]);
}
?>