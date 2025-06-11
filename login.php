<?php session_start(); ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Login - Panadería</title>
    <link rel="stylesheet" href="estilo.css">
</head>
<body>
    <video autoplay muted loop id="videoFondo">
        <source src="fondo.mp4" type="video/mp4">
        Tu navegador no soporta video HTML5.
    </video>

    <div class="form-container">
        <form method="POST" action="auth.php">
            <h2>LA TRADICION</h2>
            <?php if (isset($_SESSION['error'])): ?>
                <p><?php echo $_SESSION['error']; unset($_SESSION['error']); ?></p>
            <?php endif; ?>
            <label>Usuario:</label>
            <input type="text" name="username" required>

            <label>Contraseña:</label>
            <input type="password" name="password" required>

            <button type="submit">Entrar</button>
        </form>
    </div>
</body>
</html>
