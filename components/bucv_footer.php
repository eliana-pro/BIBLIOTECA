<?php
/**
 * bucv_footer.php
 * Componente del pie de página
 */

$base_path = isset($nivel) && $nivel === 'pages' ? '../' : '';
?>
<footer>
    <div class="footer-content">
        <div class="footer-section">
            <h4>Biblioteca UNICAB</h4>
            <p>Centro de recursos académicos y científicos al servicio de la comunidad estudiantil.</p>
        </div>

        <div class="footer-section">
            <h4>Enlaces rápidos</h4>
            <nav>
                <a href="<?php echo $base_path; ?>index.php">Catálogo</a>
                <a href="#">Blog</a>
                <a href="#">Ayuda</a>
            </nav>
        </div>

        <div class="footer-section">
            <h4>Recursos</h4>
            <nav>
                <a href="#">Libros</a>
                <a href="#">Artículos</a>
                <a href="#">Tesis</a>
            </nav>
        </div>

        <div class="footer-section">
            <h4>Contacto</h4>
            <p>biblioteca@unicab.edu</p>
            <p>(123) 456-7890</p>
            <p>Lun - Vie: 8:00 - 20:00</p>
        </div>
    </div>

    <div class="footer-divider">
        <p>&copy; <?php echo date('Y'); ?> Biblioteca UNICAB - Todos los derechos reservados</p>
    </div>
</footer>
