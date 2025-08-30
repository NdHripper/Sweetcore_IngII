<?php
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Incluir la clase de base de datos
    require_once 'database.php';
    
    // Obtener y validar datos
    $username = trim($_POST['username']);
    $email = filter_var(trim($_POST['email']), FILTER_VALIDATE_EMAIL);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm-password'];
    
    // Validaciones
    if (empty($username) || empty($email) || empty($password) || empty($confirm_password)) {
        echo json_encode(['success' => false, 'message' => 'Todos los campos son obligatorios.']);
        exit;
    }
    
    if (!$email) {
        echo json_encode(['success' => false, 'message' => 'El formato del email no es válido.']);
        exit;
    }
    
    if ($password !== $confirm_password) {
        echo json_encode(['success' => false, 'message' => 'Las contraseñas no coinciden.']);
        exit;
    }
    
    if (strlen($password) < 6) {
        echo json_encode(['success' => false, 'message' => 'La contraseña debe tener al menos 6 caracteres.']);
        exit;
    }
    
    try {
        $db = new Database();
        $conn = $db->getConnection();
        
        // Verificar si el usuario o email ya existen
        $checkStmt = $conn->prepare("SELECT id FROM users WHERE username = :username OR email = :email");
        $checkStmt->execute([':username' => $username, ':email' => $email]);
        
        if ($checkStmt->fetch()) {
            echo json_encode(['success' => false, 'message' => 'El usuario o email ya existe.']);
            exit;
        }
        
        // Hash de la contraseña
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        
        // Insertar nuevo usuario
        $stmt = $conn->prepare("INSERT INTO users (username, email, password) VALUES (:username, :email, :password)");
        $stmt->execute([
            ':username' => $username,
            ':email' => $email,
            ':password' => $hashedPassword
        ]);
        
        echo json_encode(['success' => true, 'message' => 'Usuario registrado con éxito.']);
        
    } catch(PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Error en el registro: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Método no permitido.']);
}
?>