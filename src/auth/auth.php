<?php
// Iniciar sesión si no está iniciada
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Si no hay sesión activa, redirigir al login
if (!isset($_SESSION['id_usuario'])) {
    header("Location: login.php");
    exit;
}

/**
 * Restringe el acceso a la página actual solo a administradores.
 * Si el usuario tiene rol 'empleado', lo redirige al inicio con mensaje de error.
 * Uso: llamar soloAdmin() al inicio de cualquier archivo exclusivo del admin.
 */
function soloAdmin() {
    if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'admin') {
        header("Location: index.php?error=acceso_denegado");
        exit;
    }
}


