<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Biblioteca UNICAB</title>
    <link rel="stylesheet" href="../assets/css/bucv_biblioteca.css">
</head>
<body>
    <?php include '../components/bucv_header.php'; ?>
    
    <div class="search-section">
        <h1>¿Qué buscas?</h1>
        <div class="search-box">
            <input type="text" id="searchInput" placeholder="Buscar por título, autor, tema...">
            <button onclick="buscar()">Buscar</button>
        </div>
    </div>

    <div class="container">
        <?php include '../components/bucv_sidebar.php'; ?>
        
        <main class="results">
            <div class="results-header">
                <h2>Resultados de búsqueda</h2>
                <span id="resultCount">12 documentos encontrados</span>
            </div>
            <div class="book-grid" id="bookGrid"></div>
        </main>
    </div>

    <div class="modal" id="bookModal">
        <div class="modal-content">
            <div class="modal-header">
                <h2 id="modalTitle">Título del documento</h2>
                <span class="close-btn" onclick="cerrarModal()">&times;</span>
            </div>
            <div class="modal-body" id="modalBody"></div>
            <button class="btn-primary">Consultar documento</button>
        </div>
    </div>

    <?php include '../components/bucv_footer.php'; ?>
    
    <script src="../assets/js/bucv_biblioteca.js"></script>
</body>
</html>