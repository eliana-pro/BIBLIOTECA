<aside class="sidebar">
    <h3>Filtros</h3>
    
    <div class="filter-group">
        <label>Ordenar</label>
        <select onchange="ordenar(this.value)">
            <option value="relevancia">Mayor relevancia</option>
            <option value="asc">Año ascendente</option>
            <option value="desc">Año descendente</option>
        </select>
    </div>

    <div class="filter-group">
        <label>Género</label>
        <div class="checkbox-group">
            <label><input type="checkbox" value="articulo"> Artículo científico</label>
            <label><input type="checkbox" value="libro"> Libro</label>
            <label><input type="checkbox" value="tesis"> Tesis</label>
            <label><input type="checkbox" value="ensayo"> Ensayo</label>
            <label><input type="checkbox" value="video"> Video</label>
        </div>
    </div>

    <div class="filter-group">
        <label>Pensamiento</label>
        <div class="checkbox-group">
            <label><input type="checkbox" value="bioético"> Bioético</label>
            <label><input type="checkbox" value="matemático"> Matemático</label>
            <label><input type="checkbox" value="social"> Social</label>
            <label><input type="checkbox" value="tecnológico"> Tecnológico</label>
        </div>
    </div>

    <div class="filter-group">
        <label>Año</label>
        <div class="checkbox-group">
            <label><input type="checkbox" value="2020-2025"> 2020 - 2025</label>
            <label><input type="checkbox" value="2015-2020"> 2015 - 2020</label>
            <label><input type="checkbox" value="2010-2015"> 2010 - 2015</label>
            <label><input type="checkbox" value="2000-2010"> 2000 - 2010</label>
        </div>
    </div>

    <div class="filter-group">
        <label>Idioma</label>
        <div class="checkbox-group">
            <label><input type="checkbox" value="español"> Español</label>
            <label><input type="checkbox" value="inglés"> Inglés</label>
            <label><input type="checkbox" value="italiano"> Italiano</label>
        </div>
    </div>
</aside>