<?php
/**
 * bucv_head.php
 * Meta tags y estilos CSS
 */

$base_path = isset($nivel) && $nivel === 'pages' ? '../' : '';
?>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?php echo isset($page_title) ? htmlspecialchars($page_title) : 'Biblioteca UNICAB'; ?></title>

<!-- Google Fonts -->
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

<!-- Estilos propios -->
<link rel="stylesheet" href="<?php echo $base_path; ?>assets/css/bucv_biblioteca.css">
