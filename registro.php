<?php
session_start();

// Procesar el formulario si se envió
if ($_POST) {
    $host = 'localhost';
    $db   = 'bd_22';
    $user = 'root';
    $pass = '';
    $charset = 'utf8mb4';

    $dsn = "mysql:host=$host;dbname=$db;charset=$charset";
    $options = [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ];

    try {
        $pdo = new PDO($dsn, $user, $pass, $options);
        
        $nombre = trim($_POST['nombre']);
        $email = trim($_POST['email']);
        $password = $_POST['password'];
        $confirm_password = $_POST['confirm_password'];
        
        // Validaciones
        if (empty($nombre) || empty($email) || empty($password)) {
            $_SESSION['error'] = 'Todos los campos son obligatorios';
        } elseif ($password !== $confirm_password) {
            $_SESSION['error'] = 'Las contraseñas no coinciden';
        } elseif (strlen($password) < 6) {
            $_SESSION['error'] = 'La contraseña debe tener al menos 6 caracteres';
        } else {
            // Verificar si el usuario ya existe
            $stmt = $pdo->prepare('SELECT COUNT(*) FROM usuarios WHERE nombre = ? OR email = ?');
            $stmt->execute([$nombre, $email]);
            
            if ($stmt->fetchColumn() > 0) {
                $_SESSION['error'] = 'El usuario o email ya existe';
            } else {
                // Insertar nuevo usuario (contraseña hasheada para seguridad)
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                $stmt = $pdo->prepare('INSERT INTO usuarios (nombre, email, passwor) VALUES (?, ?, ?)');
                
                if ($stmt->execute([$nombre, $email, $hashed_password])) {
                    $_SESSION['success'] = 'Usuario registrado exitosamente. Ya puedes iniciar sesión.';
                    header('Location: login.php');
                    exit;
                } else {
                    $_SESSION['error'] = 'Error al registrar el usuario';
                }
            }
        }
    } catch (PDOException $e) {
        $_SESSION['error'] = 'Error de conexión: ' . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro - Panadería La Tradición</title>
    <link rel="stylesheet" href="estilo.css">
    <style>
        .form-container form {
            width: 400px;
        }
        .success {
            color: green;
            text-align: center;
            margin-top: 1rem;
        }
    </style>
</head>
<body>
    <video autoplay muted loop id="videoFondo">
        <source src="fondo.mp4" type="video/mp4">
        Tu navegador no soporta video HTML5.
    </video>

    <div class="form-container">
        <form method="POST">
            <h2>REGISTRO - LA TRADICIÓN</h2>
            
            <?php if (isset($_SESSION['error'])): ?>
                <p style="color: red;"><?php echo $_SESSION['error']; unset($_SESSION['error']); ?></p>
            <?php endif; ?>
            
            <?php if (isset($_SESSION['success'])): ?>
                <p class="success"><?php echo $_SESSION['success']; unset($_SESSION['success']); ?></p>
            <?php endif; ?>

            <label>Nombre de usuario:</label>
            <input type="text" name="nombre" required>

            <label>Email:</label>
            <input type="email" name="email" required>

            <label>Contraseña:</label>
            <input type="password" name="password" required>

            <label>Confirmar contraseña:</label>
            <input type="password" name="confirm_password" required>

            <button type="submit">Registrarse</button>
            
            <p style="text-align: center; margin-top: 1rem;">
                <a href="login.php" style="color: #d2691e; text-decoration: none;">¿Ya tienes cuenta? Inicia sesión</a>
            </p>
        </form>
    </div>
</body>
</html>