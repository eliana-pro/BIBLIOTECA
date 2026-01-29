/**
 * bucv_biblioteca.js
 * JavaScript principal para la Biblioteca Digital UNICAB
 * Vanilla JavaScript (sin dependencias)
 */

// ========================================
// CONFIGURACIÓN
// ========================================
// Detectar si estamos en pages/ o en raíz
const isInPages = window.location.pathname.includes('/pages/');
const API_URL = isInPages ? '../ajax/bucv_recursos.php' : 'ajax/bucv_recursos.php';

// ========================================
// ESTADO DE LA APLICACIÓN
// ========================================
let recursos = [];
let recursosFiltrados = [];
let paginaActual = 1;
const recursosPorPagina = 6;
let vistaActual = 'lista';
let filtrosActivos = {};
let recursoActual = null;

// ========================================
// INICIALIZACIÓN
// ========================================
document.addEventListener('DOMContentLoaded', function() {
    // Verificar parámetros URL
    const urlParams = new URLSearchParams(window.location.search);
    const searchParam = urlParams.get('q');

    // Si hay búsqueda en URL, ponerla en el input
    const searchInput = document.getElementById('searchInput');
    if (searchParam && searchInput) {
        searchInput.value = searchParam;
    }

    // Cargar datos iniciales
    if (searchParam) {
        buscarEnAPI(searchParam, filtrosActivos);
    } else {
        cargarRecursos();
    }
    cargarEstadisticas();
    cargarAutoresParaFiltro();

    // Eventos de búsqueda
    const btnBuscar = document.getElementById('btnBuscar');

    if (btnBuscar) {
        btnBuscar.addEventListener('click', buscar);
    }

    if (searchInput) {
        searchInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') buscar();
        });
    }

    // Búsquedas rápidas
    document.querySelectorAll('.btn-busqueda-rapida').forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            const termino = this.getAttribute('data-busqueda');
            if (searchInput) {
                searchInput.value = termino;
            }
            buscar();
        });
    });

    // Cambio de vista
    const btnVistaLista = document.getElementById('btnVistaLista');
    const btnVistaGrid = document.getElementById('btnVistaGrid');

    if (btnVistaLista) {
        btnVistaLista.addEventListener('click', () => cambiarVista('lista'));
    }
    if (btnVistaGrid) {
        btnVistaGrid.addEventListener('click', () => cambiarVista('grid'));
    }

    // Filtros
    const selectOrden = document.getElementById('selectOrden');
    if (selectOrden) {
        selectOrden.addEventListener('change', function() {
            filtrosActivos.orden = this.value;
            buscar();
        });
    }

    document.querySelectorAll('.checkbox-group input[type="checkbox"]').forEach(checkbox => {
        checkbox.addEventListener('change', aplicarFiltrosDesdeUI);
    });

    // Limpiar filtros
    const btnLimpiarFiltros = document.getElementById('btnLimpiarFiltros');
    if (btnLimpiarFiltros) {
        btnLimpiarFiltros.addEventListener('click', limpiarFiltros);
    }

    // Acciones del modal
    const btnFavoritos = document.getElementById('btnFavoritos');
    const btnConsultar = document.getElementById('btnConsultar');

    if (btnFavoritos) {
        btnFavoritos.addEventListener('click', agregarFavoritos);
    }
    if (btnConsultar) {
        btnConsultar.addEventListener('click', consultarDocumento);
    }

    // Cerrar modal al hacer clic fuera
    const modal = document.getElementById('bookModal');
    if (modal) {
        modal.addEventListener('click', function(e) {
            if (e.target === this) {
                cerrarModal();
            }
        });
    }
});

// ========================================
// CARGA DE DATOS DESDE API
// ========================================
function cargarRecursos() {
    mostrarLoading(true);

    fetch(`${API_URL}?accion=listar`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                recursos = data.data;
                recursosFiltrados = [...recursos];
                renderRecursos(recursosFiltrados);
            } else {
                mostrarError('No se pudieron cargar los recursos');
            }
        })
        .catch(() => {
            mostrarError('Error de conexión con el servidor');
        })
        .finally(() => {
            mostrarLoading(false);
        });
}

function cargarEstadisticas() {
    fetch(`${API_URL}?accion=estadisticas`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                actualizarEstadisticas(data.data);
            }
        });
}

function buscarEnAPI(termino, filtros = {}) {
    mostrarLoading(true);

    let params = new URLSearchParams({ accion: 'buscar', q: termino });

    // Agregar filtros
    if (filtros.tipo_recurso && filtros.tipo_recurso.length > 0) {
        params.append('tipo_recurso', filtros.tipo_recurso.join(','));
    }
    if (filtros.pensamiento && filtros.pensamiento.length > 0) {
        params.append('pensamiento', filtros.pensamiento.join(','));
    }
    if (filtros.idioma && filtros.idioma.length > 0) {
        params.append('idioma', filtros.idioma.join(','));
    }
    if (filtros.año_min) params.append('año_min', filtros.año_min);
    if (filtros.año_max) params.append('año_max', filtros.año_max);
    if (filtros.orden) params.append('orden', filtros.orden);
    if (filtros.autor && filtros.autor.length > 0) {
        params.append('autor', filtros.autor.join(','));
    }

    fetch(`${API_URL}?${params.toString()}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                recursosFiltrados = data.data;
                paginaActual = 1;
                renderRecursos(recursosFiltrados);
                actualizarFiltrosActivos();
            }
        })
        .catch(() => {
            mostrarError('Error en la búsqueda');
        })
        .finally(() => {
            mostrarLoading(false);
        });
}

// ========================================
// ESTADÍSTICAS
// ========================================
function actualizarEstadisticas(stats) {
    const porTipo = stats.por_tipo || {};

    animarNumero('totalLibros', porTipo['Libro'] || 0);

    const articulos = (porTipo['Artículo científico'] || 0) + (porTipo['Artículo no científico'] || 0);
    animarNumero('totalArticulos', articulos);

    animarNumero('totalTesis', porTipo['Tesis'] || 0);
    animarNumero('totalAutores', stats.total || 0);
}

function animarNumero(elementId, valorFinal) {
    const elemento = document.getElementById(elementId);
    if (!elemento) return;

    let valorActual = 0;
    const incremento = Math.ceil(valorFinal / 50);
    const intervalo = setInterval(() => {
        valorActual += incremento;
        if (valorActual >= valorFinal) {
            valorActual = valorFinal;
            clearInterval(intervalo);
        }
        elemento.textContent = valorActual;
    }, 20);
}

// ========================================
// RENDERIZADO DE RECURSOS
// ========================================
function renderRecursos(recursosArray) {
    const grid = document.getElementById('bookGrid');
    if (!grid) return;

    grid.innerHTML = '';

    // Paginación
    const inicio = (paginaActual - 1) * recursosPorPagina;
    const fin = inicio + recursosPorPagina;
    const recursosPagina = recursosArray.slice(inicio, fin);

    if (recursosPagina.length === 0) {
        grid.innerHTML = `
            <div class="no-results">
                <span style="font-size: 64px;">&#128269;</span>
                <h3>No se encontraron resultados</h3>
                <p>Intenta con otros términos de búsqueda o ajusta los filtros</p>
            </div>
        `;
        const paginacion = document.getElementById('pagination');
        if (paginacion) paginacion.innerHTML = '';
        return;
    }

    // Aplicar clase de vista
    grid.className = vistaActual === 'grid' ? 'book-grid grid-view' : 'book-grid';

    recursosPagina.forEach(recurso => {
        const iconoTipo = obtenerIconoTipo(recurso.tipo_recurso);

        const card = document.createElement('div');
        card.className = 'book-card';
        card.setAttribute('data-id', recurso.id_recursos);

        card.innerHTML = `
            <div class="book-cover">
                <span>${iconoTipo}</span>
            </div>
            <div class="book-info">
                <span class="book-type">${recurso.tipo_recurso || 'Recurso'}</span>
                <h3 class="book-title">${recurso.titulo}</h3>
                ${recurso.autor ? `<p class="book-author">${recurso.autor}</p>` : ''}
                <p class="book-meta">${recurso.año || 'S/F'} ${recurso.pais ? '• ' + recurso.pais : ''}</p>
                <p class="book-description">${recurso.resumen || 'Sin descripción disponible'}</p>
                <div class="book-tags">
                    ${recurso.pensamiento ? `<span class="tag">${recurso.pensamiento}</span>` : ''}
                    ${recurso.idioma ? `<span class="tag">${recurso.idioma}</span>` : ''}
                </div>
            </div>
        `;

        card.addEventListener('click', function() {
            const id = this.getAttribute('data-id');
            const recursoSeleccionado = recursosArray.find(r => r.id_recursos == id);
            if (recursoSeleccionado) abrirModal(recursoSeleccionado);
        });

        grid.appendChild(card);
    });

    // Actualizar contador
    const resultCount = document.getElementById('resultCount');
    if (resultCount) {
        resultCount.textContent = `${recursosArray.length} recurso(s) encontrado(s)`;
    }

    renderPaginacion(recursosArray.length);
}

function obtenerIconoTipo(tipo) {
    const iconos = {
        'Libro': '&#128213;',
        'Artículo científico': '&#128196;',
        'Artículo no científico': '&#128240;',
        'Tesis': '&#127891;',
        'Tesina': '&#128203;',
        'Proyecto de aula': '&#128194;',
        'Ensayo': '&#128221;',
        'Video': '&#127916;',
        'Audio': '&#127911;',
        'Póster': '&#128444;'
    };
    return iconos[tipo] || '&#128218;';
}

function mostrarError(mensaje) {
    const grid = document.getElementById('bookGrid');
    if (grid) {
        grid.innerHTML = `
            <div class="error-message">
                <span style="font-size: 64px;">&#9888;</span>
                <h3>Error</h3>
                <p>${mensaje}</p>
                <button class="btn-primary" onclick="cargarRecursos()">Reintentar</button>
            </div>
        `;
    }
}

function mostrarLoading(show) {
    const loading = document.getElementById('loadingSpinner');
    const grid = document.getElementById('bookGrid');

    if (loading) {
        loading.style.display = show ? 'block' : 'none';
    }
    if (grid) {
        grid.style.display = show ? 'none' : '';
    }
}

// ========================================
// PAGINACIÓN
// ========================================
function renderPaginacion(totalRecursos) {
    const paginacion = document.getElementById('pagination');
    if (!paginacion) return;

    paginacion.innerHTML = '';

    const totalPaginas = Math.ceil(totalRecursos / recursosPorPagina);
    if (totalPaginas <= 1) return;

    // Botón anterior
    const btnAnterior = document.createElement('button');
    btnAnterior.innerHTML = '&laquo;';
    btnAnterior.disabled = paginaActual === 1;
    btnAnterior.addEventListener('click', () => cambiarPagina(paginaActual - 1));
    paginacion.appendChild(btnAnterior);

    // Números de página
    for (let i = 1; i <= totalPaginas; i++) {
        const btn = document.createElement('button');
        btn.textContent = i;
        if (i === paginaActual) btn.classList.add('active');
        btn.addEventListener('click', () => cambiarPagina(i));
        paginacion.appendChild(btn);
    }

    // Botón siguiente
    const btnSiguiente = document.createElement('button');
    btnSiguiente.innerHTML = '&raquo;';
    btnSiguiente.disabled = paginaActual === totalPaginas;
    btnSiguiente.addEventListener('click', () => cambiarPagina(paginaActual + 1));
    paginacion.appendChild(btnSiguiente);
}

function cambiarPagina(pagina) {
    paginaActual = pagina;
    renderRecursos(recursosFiltrados);
    window.scrollTo({ top: document.getElementById('bookGrid').offsetTop - 100, behavior: 'smooth' });
}

// ========================================
// BÚSQUEDA
// ========================================
function buscar() {
    const searchInput = document.getElementById('searchInput');
    const searchTerm = searchInput ? searchInput.value.trim() : '';
    buscarEnAPI(searchTerm, filtrosActivos);
}

// ========================================
// FILTROS
// ========================================
function aplicarFiltrosDesdeUI() {
    // Obtener filtros seleccionados
    const tipoRecurso = obtenerFiltrosSeleccionados('[data-filter="genero"]');
    const pensamientos = obtenerFiltrosSeleccionados('[data-filter="pensamiento"]');
    const años = obtenerFiltrosSeleccionados('[data-filter="año"]');
    const idiomas = obtenerFiltrosSeleccionados('[data-filter="idioma"]');
    const autores = obtenerFiltrosSeleccionados('[data-filter="autor"]');

    filtrosActivos = {
        ...filtrosActivos,
        tipo_recurso: mapearTiposRecurso(tipoRecurso),
        pensamiento: pensamientos,
        idioma: idiomas,
        autor: autores
    };

    // Procesar rangos de años
    if (años.length > 0) {
        let minYear = 9999, maxYear = 0;
        años.forEach(rango => {
            const [min, max] = rango.split('-').map(Number);
            if (min < minYear) minYear = min;
            if (max > maxYear) maxYear = max;
        });
        filtrosActivos.año_min = minYear;
        filtrosActivos.año_max = maxYear;
    } else {
        delete filtrosActivos.año_min;
        delete filtrosActivos.año_max;
    }

    buscar();
}

function obtenerFiltrosSeleccionados(selector) {
    const valores = [];
    document.querySelectorAll(`${selector} input[type="checkbox"]:checked`).forEach(checkbox => {
        valores.push(checkbox.value);
    });
    return valores;
}

function mapearTiposRecurso(tipos) {
    const mapa = {
        'articulo': ['Artículo científico', 'Artículo no científico'],
        'libro': ['Libro'],
        'tesis': ['Tesis', 'Tesina'],
        'ensayo': ['Ensayo'],
        'video': ['Video']
    };

    let resultado = [];
    tipos.forEach(tipo => {
        if (mapa[tipo]) {
            resultado = resultado.concat(mapa[tipo]);
        }
    });
    return resultado;
}

function actualizarFiltrosActivos() {
    const container = document.getElementById('activeFilters');
    if (!container) return;

    container.innerHTML = '';

    const searchInput = document.getElementById('searchInput');
    const searchTerm = searchInput ? searchInput.value.trim() : '';

    if (searchTerm) {
        const tag = document.createElement('span');
        tag.className = 'filter-tag';
        tag.innerHTML = `Búsqueda: "${searchTerm}" <button onclick="limpiarBusqueda()">&times;</button>`;
        container.appendChild(tag);
    }

    // Tags de filtros activos
    if (filtrosActivos.tipo_recurso) {
        filtrosActivos.tipo_recurso.forEach(tipo => {
            const tag = document.createElement('span');
            tag.className = 'filter-tag';
            tag.textContent = tipo;
            container.appendChild(tag);
        });
    }

    if (filtrosActivos.pensamiento) {
        filtrosActivos.pensamiento.forEach(pens => {
            const tag = document.createElement('span');
            tag.className = 'filter-tag';
            tag.textContent = pens;
            container.appendChild(tag);
        });
    }

    if (filtrosActivos.idioma) {
        filtrosActivos.idioma.forEach(idioma => {
            const tag = document.createElement('span');
            tag.className = 'filter-tag';
            tag.textContent = idioma;
            container.appendChild(tag);
        });
    }
}

function limpiarBusqueda() {
    const searchInput = document.getElementById('searchInput');
    if (searchInput) searchInput.value = '';
    buscar();
}

function limpiarFiltros() {
    // Desmarcar todos los checkboxes
    document.querySelectorAll('.checkbox-group input[type="checkbox"]').forEach(checkbox => {
        checkbox.checked = false;
    });

    const selectOrden = document.getElementById('selectOrden');
    if (selectOrden) selectOrden.value = 'relevancia';

    const searchInput = document.getElementById('searchInput');
    if (searchInput) searchInput.value = '';

    filtrosActivos = {};
    buscar();
}

// ========================================
// CAMBIO DE VISTA
// ========================================
function cambiarVista(tipo) {
    vistaActual = tipo;

    const btnVistaLista = document.getElementById('btnVistaLista');
    const btnVistaGrid = document.getElementById('btnVistaGrid');

    if (btnVistaLista) btnVistaLista.classList.remove('active');
    if (btnVistaGrid) btnVistaGrid.classList.remove('active');

    if (tipo === 'grid' && btnVistaGrid) {
        btnVistaGrid.classList.add('active');
    } else if (btnVistaLista) {
        btnVistaLista.classList.add('active');
    }

    renderRecursos(recursosFiltrados);
}

// ========================================
// MODAL
// ========================================
function abrirModal(recurso) {
    recursoActual = recurso;

    const modalTitle = document.getElementById('modalTitle');
    const modalBadge = document.getElementById('modalBadge');
    const modalBody = document.getElementById('modalBody');
    const modal = document.getElementById('bookModal');

    if (modalTitle) modalTitle.textContent = recurso.titulo;
    if (modalBadge) modalBadge.textContent = recurso.tipo_recurso || 'Recurso';

    if (modalBody) {
        modalBody.innerHTML = `
            <div class="detail-row">
                <span class="detail-label">Autor</span>
                <span class="detail-value">${recurso.autor || 'No especificado'}</span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Año</span>
                <span class="detail-value">${recurso.año || 'Sin fecha'}</span>
            </div>
            <div class="detail-row">
                <span class="detail-label">ISBN/ISSN/DOI</span>
                <span class="detail-value">${recurso.isbn_issn_doi || 'No disponible'}</span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Idioma</span>
                <span class="detail-value">${recurso.idioma || 'No especificado'}</span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Pensamiento</span>
                <span class="detail-value">${recurso.pensamiento || 'No especificado'}</span>
            </div>
            <div class="detail-row">
                <span class="detail-label">País</span>
                <span class="detail-value">${recurso.pais || 'No especificado'}</span>
            </div>
            <div class="description-section">
                <h4>Resumen</h4>
                <p>${recurso.resumen || 'Sin resumen disponible'}</p>
            </div>
        `;
    }

    if (modal) {
        modal.style.display = 'block';
        document.body.style.overflow = 'hidden';
    }
}

function cerrarModal() {
    const modal = document.getElementById('bookModal');
    if (modal) {
        modal.style.display = 'none';
        document.body.style.overflow = '';
    }
}

// ========================================
// ACCIONES DEL MODAL
// ========================================
function agregarFavoritos() {
    if (recursoActual) {
        alert(`"${recursoActual.titulo}" agregado a favoritos`);
    }
}

function consultarDocumento() {
    if (recursoActual) {
        const basePath = isInPages ? '' : 'pages/';
        window.location.href = `${basePath}bucv_detalle_recurso.php?id=${recursoActual.id_recursos}`;
    }
}

// ========================================
// CARGA DE AUTORES PARA FILTRO
// ========================================
function cargarAutoresParaFiltro() {
    const container = document.getElementById('filtroAutores');
    if (!container) return;

    fetch(`${API_URL}?accion=autores`)
        .then(response => response.json())
        .then(data => {
            if (data.success && data.data.length > 0) {
                container.innerHTML = '';
                data.data.forEach(autor => {
                    const label = document.createElement('label');
                    label.innerHTML = `
                        <input type="checkbox" value="${autor.nombre}"> ${autor.nombre}
                    `;
                    container.appendChild(label);
                });
                // Agregar evento change a los nuevos checkboxes
                container.querySelectorAll('input[type="checkbox"]').forEach(checkbox => {
                    checkbox.addEventListener('change', aplicarFiltrosDesdeUI);
                });
            } else {
                container.innerHTML = '<p class="no-data">No hay autores disponibles</p>';
            }
        })
        .catch(() => {
            container.innerHTML = '<p class="error-text">Error al cargar autores</p>';
        });
}
