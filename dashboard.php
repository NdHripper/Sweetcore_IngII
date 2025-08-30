<?php
include 'config.php';

if (!isLoggedIn()) {
    header("Location: login.html");
    exit();
}

$user = getUserInfo();
?>
<!DOCTYPE html>
<html>
<head>
    <title>Dashboard - SweetCore</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <header>
        <!-- Mismo header que index.html -->
    </header>

    <main>
        <section class="dashboard-section">
            <div class="section-content">
                <h2>Bienvenido, <?php echo $user['username']; ?>!</h2>
                <p>Email: <?php echo $user['email']; ?></p>
                <a href="logout.php">Cerrar sesión</a>
            </div>
        </section>
    </main>
</body>
</html>