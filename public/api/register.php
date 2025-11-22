<?php
require_once '../../config/db.php';
$conn = dbconnect();

header('Content-Type: application/json');

$input = json_decode(file_get_contents('php://input'), true) ?? $_POST;

$firstname = trim($input['firstname'] ?? '');
$lastname = trim($input['lastname'] ?? '');
$email = trim($input['email'] ?? '');
$phone = $input['phone'] ?? null;
$password = $input['password'] ?? '';
$role = 'customer'; 

$errors = [];

if (empty($firstname)) {
    $errors[] = 'First name is required';
}

if (empty($email)) {
    $errors[] = 'Email is required';
} elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors[] = 'Invalid email format';
}

if (empty($password)) {
    $errors[] = 'Password is required';
} elseif (strlen($password) < 6) {
    $errors[] = 'Password must be at least 6 characters';
}

if (!empty($errors)) {
    echo json_encode([
        'success' => false, 
        'message' => implode(', ', $errors)
    ]);
    exit;
}

try {
    // Check if email already exists
    $checkStmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $checkStmt->execute([$email]);
    
    if ($checkStmt->fetch()) {
        echo json_encode([
            'success' => false, 
            'message' => 'Email already registered'
        ]);
        exit;
    }

    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    $stmt = $conn->prepare("INSERT INTO users (firstname, lastname, email, phone, password, role) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->execute([$firstname, $lastname, $email, $phone, $hashedPassword, $role]);
    
    echo json_encode([
        'success' => true, 
        'message' => 'Account created successfully'
    ]);
    
} catch(PDOException $e) {
    error_log("Registration error: " . $e->getMessage());
    echo json_encode([
        'success' => false, 
        'message' => 'Registration failed. Please try again.'
    ]);
}
?>