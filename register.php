<?php
// Configurar sesiones para Windows primero
if (!file_exists('sessions')) {
    mkdir('sessions', 0777, true);
}
ini_set('session.save_path', __DIR__ . '/sessions');

// Debug: mostrar todos los errores
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Verificar si es POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Método no permitido']);
    exit();
}

// Forzar JSON
header('Content-Type: application/json');

try {
    // Incluir configuración
    include 'config.php';
    
    // Obtener datos
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm-password'] ?? '';
    
    // Validaciones
    if (empty($username) || empty($email) || empty($password)) {
        echo json_encode(['success' => false, 'message' => 'Todos los campos son obligatorios']);
        exit();
    }

    if ($password !== $confirm_password) {
        echo json_encode(['success' => false, 'message' => 'Las contraseñas no coinciden']);
        exit();
    }

    if (strlen($password) < 6) {
        echo json_encode(['success' => false, 'message' => 'La contraseña debe tener al menos 6 caracteres']);
        exit();
    }

    // Conectar a la base de datos
    $db = getDB();
    
    // Verificar si usuario existe
    $stmt = $db->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
    $stmt->execute([$username, $email]);
    
    if ($stmt->fetch()) {
        echo json_encode(['success' => false, 'message' => 'El usuario o email ya existe']);
        exit();
    }

    // Insertar nuevo usuario
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    $stmt = $db->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
    
    if ($stmt->execute([$username, $email, $hashedPassword])) {
        echo json_encode(['success' => true, 'message' => 'Usuario registrado exitosamente']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error al registrar el usuario']);
    }
    
} catch (Exception $e) {
    // Log del error técnico
    error_log("Error en register.php: " . $e->getMessage());
    
    // Mensaje amigable para el usuario
    http_response_code(500);
    echo json_encode([
        'success' => false, 
        'message' => 'Error en el sistema. Por favor intenta nuevamente.'
    ]);
}
?>