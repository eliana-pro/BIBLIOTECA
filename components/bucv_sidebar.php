<?php
/**
 * bucv_sidebar.php
 * Sidebar de filtros
 */
?>
<aside class="sidebar">
    <h3>Filtros</h3>

    <!-- Ordenar -->
    <div class="filter-group">
        <label>Ordenar por</label>
        <select id="selectOrden">
            <option value="relevancia">Mayor relevancia</option>
            <option value="asc">Año ascendente</option>
            <option value="desc">Año descendente</option>
        </select>
    </div>

    <!-- Género -->
    <div class="filter-group">
        <label>Género</label>
        <div class="checkbox-group" data-filter="genero">
            <label>
                <input type="checkbox" value="articulo"> Artículo científico
            </label>
            <label>
                <input type="checkbox" value="libro"> Libro
            </label>
            <label>
                <input type="checkbox" value="tesis"> Tesis
            </label>
            <label>
                <input type="checkbox" value="ensayo"> Ensayo
            </label>
            <label>
                <input type="checkbox" value="video"> Video
            </label>
        </div>
    </div>

    <!-- Pensamiento -->
    <div class="filter-group">
        <label>Pensamiento</label>
        <div class="checkbox-group" data-filter="pensamiento">
            <label>
                <input type="checkbox" value="bioético"> Bioético
            </label>
            <label>
                <input type="checkbox" value="matemático"> Matemático
            </label>
            <label>
                <input type="checkbox" value="social"> Social
            </label>
            <label>
                <input type="checkbox" value="tecnológico"> Tecnológico
            </label>
        </div>
    </div>

    <!-- Año -->
    <div class="filter-group">
        <label>Año de publicación</label>
        <div class="checkbox-group" data-filter="año">
            <label>
                <input type="checkbox" value="2020-2025"> 2020 - 2025
            </label>
            <label>
                <input type="checkbox" value="2015-2020"> 2015 - 2020
            </label>
            <label>
                <input type="checkbox" value="2010-2015"> 2010 - 2015
            </label>
            <label>
                <input type="checkbox" value="2000-2010"> 2000 - 2010
            </label>
        </div>
    </div>

    <!-- Idioma -->
    <div class="filter-group">
        <label>Idioma</label>
        <div class="checkbox-group" data-filter="idioma">
            <label>
                <input type="checkbox" value="español"> Español
            </label>
            <label>
                <input type="checkbox" value="inglés"> Inglés
            </label>
            <label>
                <input type="checkbox" value="italiano"> Italiano
            </label>
        </div>
    </div>

    <!-- Autor (filtro eliminado) -->

    <!-- Botón limpiar filtros -->
    <button class="btn-secondary" id="btnLimpiarFiltros" style="width: 100%;">
        Limpiar filtros
    </button>
</aside>
