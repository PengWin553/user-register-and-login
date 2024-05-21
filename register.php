<?php

include('connection.php');

header('Content-Type: application/json');

$input = json_decode(file_get_contents('php://input'), true);
$email = $input['email'];
$raw_password = $input['password'];

// Check if email and password are provided
if (empty($email) || empty($raw_password)) {
    echo json_encode(['success' => false, 'message' => 'Email and password are required']);
    exit();
}

// Hash the password with a salt
$hashed_password = password_hash($raw_password, PASSWORD_BCRYPT);

try {
    // Insert the new user into the database
    $stmt = $connection->prepare("INSERT INTO users (email, password) VALUES (:email, :password)");
    $stmt->bindParam(':email', $email);
    $stmt->bindParam(':password', $hashed_password);

    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'User registered successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to register user']);
    }
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}

// Close the connectionection
$connection = null;
?>
