<?php
/**
 * bucv_admin_auth.php
 * Verificación de autenticación para el panel admin
 */

session_start();

// Verificar si el usuario está autenticado
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: login.php');
    exit;
}

// Obtener datos del usuario
$admin_usuario = $_SESSION['admin_usuario'] ?? 'Administrador';
$admin_id = $_SESSION['admin_id'] ?? 0;
