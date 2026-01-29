<?php
/**
 * bucv_header.php
 * Componente del encabezado de la Biblioteca UNICAB
 */

$base_path = isset($nivel) && $nivel === 'pages' ? '../' : '';
$pages_path = isset($nivel) && $nivel === 'pages' ? '' : 'pages/';
?>
<header>
    <div class="logo-section">
        <a href="<?php echo $base_path; ?>index.php" class="logo-link">
            <img src="<?php echo $base_path; ?>assets/images/unicab2.webp" alt="Biblioteca UNICAB" class="logo">
        </a>
        <span class="header-title">Biblioteca UNICAB</span>
    </div>
    <nav>
        <a href="<?php echo $base_path; ?>index.php">Inicio</a>
        <a href="<?php echo $base_path . $pages_path; ?>bucv_catalogo.php">Catálogo</a>
        <a href="<?php echo $base_path . $pages_path; ?>bucv_autores.php">Autores</a>
    </nav>
</header>
