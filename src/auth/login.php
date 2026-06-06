<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (isset($_SESSION['id_usuario'])) {
    header("Location: ../index.php");
    exit;
}

$error = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    include "../config/db_connect.php";

    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if (empty($username) || empty($password)) {
        $error = "Por favor completa todos los campos.";
    } else {
        $username_escaped = $conn->real_escape_string($username);

        $sql = "SELECT id_usuario, nombre, username, password, rol
                FROM usuario
                WHERE username = '$username_escaped'
                AND estado = 1";

        $result = $conn->query($sql);

        if ($result && $result->num_rows === 1) {
            $usuario = $result->fetch_assoc();

            if (password_verify($password, $usuario['password'])) {
                // Regenerar ID de sesión por seguridad
                session_regenerate_id(true);

                $_SESSION['id_usuario'] = $usuario['id_usuario'];
                $_SESSION['nombre']     = $usuario['nombre'];
                $_SESSION['username']   = $usuario['username'];
                $_SESSION['rol']        = $usuario['rol'];

                header("Location: ../index.php");
                exit;
            } else {
                $error = "Usuario o contraseña incorrectos.";
            }
        } else {
            $error = "Usuario o contraseña incorrectos.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es" data-bs-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión — Citas Médicas</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <link rel="stylesheet" href="../../public/assets/css/estilos.css">
    <style>
        body {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background-color: var(--bg-main, #0f1117);
        }

        .login-card {
            width: 100%;
            max-width: 420px;
        }

        .login-logo {
            font-size: 2.5rem;
            color: var(--accent, #4f98a3);
        }
    </style>
</head>
<body>

<div class="login-card card shadow-lg border-0 p-4">
    <div class="card-body">

        <!-- Logo e Título -->
        <div class="text-center mb-4">
            <div class="login-logo mb-2">
                <i class="bi bi-hospital"></i>
            </div>
            <h4 class="fw-bold">Sistema de Citas Médicas</h4>
            <p class="text-muted small">Acceso exclusivo para operadores</p>
        </div>

        <!-- Mensaje de error -->
        <?php if ($error): ?>
            <div class="alert alert-danger d-flex align-items-center gap-2" role="alert">
                <i class="bi bi-exclamation-triangle-fill"></i>
                <span><?= htmlspecialchars($error) ?></span>
            </div>
        <?php endif; ?>

        <!-- Formulario -->
        <form method="POST" novalidate>

            <div class="mb-3">
                <label for="username" class="form-label">
                    <i class="bi bi-person me-1"></i> Usuario
                </label>
                <input
                    type="text"
                    id="username"
                    name="username"
                    class="form-control"
                    placeholder="Ingresa tu usuario"
                    value="<?= htmlspecialchars($_POST['username'] ?? '') ?>"
                    required
                    autofocus
                >
            </div>

            <div class="mb-4">
                <label for="password" class="form-label">
                    <i class="bi bi-lock me-1"></i> Contraseña
                </label>
                <div class="input-group">
                    <input
                        type="password"
                        id="password"
                        name="password"
                        class="form-control"
                        placeholder="Ingresa tu contraseña"
                        required
                    >
                    <button
                        type="button"
                        class="btn btn-outline-secondary"
                        id="togglePassword"
                        title="Mostrar/ocultar contraseña"
                    >
                        <i class="bi bi-eye" id="eyeIcon"></i>
                    </button>
                </div>
            </div>

            <div class="d-grid">
                <button type="submit" class="btn btn-primary btn-lg">
                    <i class="bi bi-box-arrow-in-right me-1"></i> Ingresar
                </button>
            </div>

        </form>

    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // Toggle mostrar/ocultar contraseña
    document.getElementById('togglePassword').addEventListener('click', function () {
        const input = document.getElementById('password');
        const icon = document.getElementById('eyeIcon');
        if (input.type === 'password') {
            input.type = 'text';
            icon.classList.replace('bi-eye', 'bi-eye-slash');
        } else {
            input.type = 'password';
            icon.classList.replace('bi-eye-slash', 'bi-eye');
        }
    });
</script>
</body>
</html>

