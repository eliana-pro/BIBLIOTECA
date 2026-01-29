<?php
/**
 * login.php
 * Página de inicio de sesión del panel admin
 */

session_start();

// Si ya está logueado, redirigir al dashboard
if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true) {
    header('Location: index.php');
    exit;
}

require_once(__DIR__ . '/../repositories/bucv_1cc2s4B3.php');

$error = '';
$page_title = 'Iniciar Sesión - Admin Biblioteca UNICAB';

// Procesar login
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $correo = trim($_POST['correo'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($correo) || empty($password)) {
        $error = 'Por favor complete todos los campos';
    } else {
        $repo = new BucvRepository();
        $usuario = $repo->validarUsuario($correo, $password);

        if ($usuario) {
            $_SESSION['admin_logged_in'] = true;
            $_SESSION['admin_id'] = $usuario['id_usuarios'];
            $_SESSION['admin_usuario'] = $usuario['nombre'];
            $_SESSION['admin_correo'] = $usuario['correo'];

            header('Location: index.php');
            exit;
        } else {
            $error = 'Credenciales incorrectas';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title; ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/bucv_variables.css">
    <link rel="stylesheet" href="../assets/css/bucv_admin.css">
</head>
<body>
    <div class="login-page">
        <div class="login-box">
            <h1>Biblioteca UNICAB</h1>
            <p class="subtitle">Panel de Administración</p>

            <?php if ($error): ?>
                <div class="alert alert-error">
                    <?php echo htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>

            <form method="POST" action="" class="login-form">
                <div class="form-group">
                    <label for="correo">Correo electrónico</label>
                    <input type="email" id="correo" name="correo" required
                           value="<?php echo htmlspecialchars($_POST['correo'] ?? ''); ?>"
                           placeholder="admin@unicab.edu">
                </div>

                <div class="form-group">
                    <label for="password">Contraseña</label>
                    <input type="password" id="password" name="password" required
                           placeholder="Ingrese su contraseña">
                </div>

                <button type="submit" class="btn-admin primary">
                    Iniciar Sesión
                </button>
            </form>
        </div>
    </div>
</body>
</html>
