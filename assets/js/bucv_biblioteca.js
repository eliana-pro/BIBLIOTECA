const libros = [
    {id: 1, titulo: "Introducción a la Bioética", autor: "María González", año: 2023, genero: "libro", pensamiento: "bioético", idioma: "español"},
    {id: 2, titulo: "Algoritmos y Estructuras", autor: "Juan Pérez", año: 2022, genero: "articulo", pensamiento: "matemático", idioma: "español"},
    {id: 3, titulo: "Sociedad Digital", autor: "Ana Martínez", año: 2024, genero: "ensayo", pensamiento: "social", idioma: "español"},
    {id: 4, titulo: "Inteligencia Artificial", autor: "Carlos López", año: 2023, genero: "tesis", pensamiento: "tecnológico", idioma: "inglés"},
    {id: 5, titulo: "Ética Médica Contemporánea", autor: "Laura Rodríguez", año: 2021, genero: "libro", pensamiento: "bioético", idioma: "español"},
    {id: 6, titulo: "Cálculo Avanzado", autor: "Roberto Silva", año: 2020, genero: "libro", pensamiento: "matemático", idioma: "español"},
    {id: 7, titulo: "Antropología Cultural", autor: "Sofia Ramírez", año: 2022, genero: "articulo", pensamiento: "social", idioma: "español"},
    {id: 8, titulo: "Redes Neuronales", autor: "Diego Torres", año: 2024, genero: "video", pensamiento: "tecnológico", idioma: "inglés"},
    {id: 9, titulo: "Derechos Humanos", autor: "Patricia Gómez", año: 2023, genero: "ensayo", pensamiento: "social", idioma: "español"},
    {id: 10, titulo: "Estadística Aplicada", autor: "Miguel Ángel", año: 2021, genero: "libro", pensamiento: "matemático", idioma: "español"},
    {id: 11, titulo: "Blockchain y Criptomonedas", autor: "Andrea Morales", año: 2024, genero: "articulo", pensamiento: "tecnológico", idioma: "inglés"},
    {id: 12, titulo: "Genética Humana", autor: "Fernando Castro", año: 2022, genero: "tesis", pensamiento: "bioético", idioma: "español"}
];

function renderLibros(librosArray) {
    const grid = document.getElementById('bookGrid');
    grid.innerHTML = '';
    
    librosArray.forEach(libro => {
        const card = document.createElement('div');
        card.className = 'book-card';
        card.onclick = () => abrirModal(libro);
        
        card.innerHTML = `
            <div class="book-cover">📚</div>
            <div class="book-info">
                <div class="book-title">${libro.titulo}</div>
                <div class="book-meta">Autor: ${libro.autor}</div>
                <div class="book-meta">Año: ${libro.año}</div>
                <div class="book-tags">
                    <span class="tag">${libro.genero}</span>
                    <span class="tag">${libro.pensamiento}</span>
                </div>
            </div>
        `;
        
        grid.appendChild(card);
    });

    document.getElementById('resultCount').textContent = `${librosArray.length} documentos encontrados`;
}

function buscar() {
    const searchTerm = document.getElementById('searchInput').value.toLowerCase();
    const resultados = libros.filter(libro => 
        libro.titulo.toLowerCase().includes(searchTerm) ||
        libro.autor.toLowerCase().includes(searchTerm) ||
        libro.pensamiento.toLowerCase().includes(searchTerm)
    );
    renderLibros(resultados);
}

function ordenar(tipo) {
    let librosOrdenados = [...libros];
    if (tipo === 'asc') {
        librosOrdenados.sort((a, b) => a.año - b.año);
    } else if (tipo === 'desc') {
        librosOrdenados.sort((a, b) => b.año - a.año);
    }
    renderLibros(librosOrdenados);
}

function abrirModal(libro) {
    document.getElementById('modalTitle').textContent = libro.titulo;
    document.getElementById('modalBody').innerHTML = `
        <p><strong>Autor:</strong> ${libro.autor}</p>
        <p><strong>Año:</strong> ${libro.año}</p>
        <p><strong>Género:</strong> ${libro.genero}</p>
        <p><strong>Pensamiento:</strong> ${libro.pensamiento}</p>
        <p><strong>Idioma:</strong> ${libro.idioma}</p>
        <p><strong>ISBN/ISSN:</strong> ${Math.random().toString().substring(2, 15)}</p>
        <br>
        <p><strong>Descripción:</strong></p>
        <p>Este es un documento académico de alta calidad que aborda temáticas relevantes en el campo de ${libro.pensamiento}.</p>
    `;
    document.getElementById('bookModal').style.display = 'block';
}

function cerrarModal() {
    document.getElementById('bookModal').style.display = 'none';
}

window.onclick = function(event) {
    const modal = document.getElementById('bookModal');
    if (event.target === modal) {
        cerrarModal();
    }
}

document.getElementById('searchInput').addEventListener('keypress', function(e) {
    if (e.key === 'Enter') buscar();
});

renderLibros(libros);