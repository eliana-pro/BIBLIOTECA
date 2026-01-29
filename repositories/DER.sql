
CREATE TABLE tbl_usuarios (
    id_usuario INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(150) NOT NULL,
    correo VARCHAR(150) UNIQUE NOT NULL,
    contraseña_hash VARCHAR(255) NOT NULL,
    tipo_usuario ENUM('lector','admin') DEFAULT 'lector',
    fecha_registro DATE,
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    fecha_modificacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE tbl_recursos (
    id_recurso INT AUTO_INCREMENT PRIMARY KEY,
    titulo VARCHAR(255) NOT NULL,
    año INT,
    resumen TEXT,
    isbn_issn_doi VARCHAR(100),
    tipo_recurso ENUM(
        'Libro',
        'Artículo no científico',
        'Artículo científico',
        'Tesis',
        'Proyecto de aula',
        'Tesina',
        'Póster',
        'Audio',
        'Video',
        'Ensayo'
    ) NOT NULL,
    id_idioma INT,
    id_genero INT,
    id_pensamiento INT,
    id_pais INT,
    id_autor INT,
    FOREIGN KEY (id_idioma) REFERENCES tbl_idiomas(id_idioma)
        ON DELETE SET NULL ON UPDATE CASCADE,
    FOREIGN KEY (id_genero) REFERENCES tbl_generos(id_genero)
        ON DELETE SET NULL ON UPDATE CASCADE,
    FOREIGN KEY (id_pensamiento) REFERENCES tbl_pensamientos(id_pensamiento)
        ON DELETE SET NULL ON UPDATE CASCADE,
    FOREIGN KEY (id_pais) REFERENCES tbl_paises(id_pais)
        ON DELETE SET NULL ON UPDATE CASCADE,
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    fecha_modificacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE tbl_usuarios_recursos (
    id_usuario INT,
    id_recurso INT,
    PRIMARY KEY (id_usuario, id_recurso),
    FOREIGN KEY (id_usuario) REFERENCES tbl_usuarios(id_usuario)
        ON DELETE CASCADE ON UPDATE CASCADE,
    FOREIGN KEY (id_recurso) REFERENCES tbl_recursos(id_recurso)
        ON DELETE CASCADE ON UPDATE CASCADE
);

CREATE TABLE tbl_idiomas (
    id_idioma INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL
);

CREATE TABLE tbl_generos (
    id_genero INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL
);

CREATE TABLE tbl_pensamientos (
    id_pensamiento INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL
);

CREATE TABLE tbl_paises (
    id_pais INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL
);

CREATE TABLE tbl_tipo_recursos(
	id_tipo_recurso INT AUTO_INCREMENT PRIMARY KEY,
    nombre varchar(128) NOT NULL
);



CREATE TABLE tbl_comentarios (
    id_comentario INT AUTO_INCREMENT PRIMARY KEY,
    id_usuario INT NOT NULL,
    id_recurso INT NOT NULL,
    comentario TEXT NOT NULL,
    fecha DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_usuario) REFERENCES tbl_usuarios(id_usuario)
        ON DELETE CASCADE ON UPDATE CASCADE,
    FOREIGN KEY (id_recurso) REFERENCES tbl_recursos(id_recurso)
        ON DELETE CASCADE ON UPDATE CASCADE,
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    fecha_modificacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);
