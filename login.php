<?php
session_start();
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Incluir la clase de base de datos
    require_once 'database.php';
    
    // Obtener y validar datos
    $email = filter_var(trim($_POST['email']), FILTER_VALIDATE_EMAIL);
    $password = $_POST['password'];
    
    // Validaciones
    if (empty($email) || empty($password)) {
        echo json_encode(['success' => false, 'message' => 'Todos los campos son obligatorios.']);
        exit;
    }
    
    if (!$email) {
        echo json_encode(['success' => false, 'message' => 'El formato del email no es válido.']);
        exit;
    }
    
    try {
        $db = new Database();
        $conn = $db->getConnection();
        
        // Buscar usuario por email
        $stmt = $conn->prepare("SELECT id, username, email, password FROM users WHERE email = :email");
        $stmt->execute([':email' => $email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($user && password_verify($password, $user['password'])) {
            // Iniciar sesión
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['email'] = $user['email'];
            $_SESSION['logged_in'] = true;
            
            echo json_encode(['success' => true, 'message' => 'Inicio de sesión exitoso.']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Credenciales incorrectas.']);
        }
        
    } catch(PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Error en el inicio de sesión: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Método no permitido.']);
}
?>