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