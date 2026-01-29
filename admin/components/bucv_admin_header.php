<?php
/**
 * bucv_admin_header.php
 * Header del panel admin
 */
?>
<header class="admin-header">
    <h1><?php echo $page_header ?? 'Dashboard'; ?></h1>
    <div class="admin-user">
        <span class="admin-user-name"><?php echo htmlspecialchars($admin_usuario); ?></span>
        <a href="logout.php" class="btn-logout">Cerrar sesión</a>
    </div>
</header>
