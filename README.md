# 📚 Biblioteca Digital UNICAB

Sistema de gestión integral de recursos académicos para la institución educativa UNICAB. Permite catalogar, buscar y administrar libros, artículos científicos, tesis y otros recursos educativos.

## 🗂️ Estructura del Proyecto

BIBLIOTECA/
├── 📄 index.php                    ← Página de inicio
├── 📄 README.md                    ← Documentación
├── 📄 .env                         ← Variables de entorno
├── .gitignore                      
│
├── 📁 admin/                       ← Panel administrativo
│   ├── index.php                   ← Dashboard admin
│   ├── autores.php                 ← Gestión de autores
│   ├── autores_form.php            ← Formulario de autores
│   ├── recursos.php                ← Gestión de recursos
│   ├── recursos_form.php           ← Formulario de recursos
│   ├── login.php                   ← Autenticación
│   ├── logout.php                  ← Cierre de sesión
│   └── components/                 ← Componentes admin
│       ├── bucv_admin_auth.php
│       ├── bucv_admin_header.php
│       ├── bucv_admin_sidebar.php
│       └── ...
│
├── 📁 pages/                       ← Páginas públicas
│   ├── bucv_catalogo.php           ← Catálogo de recursos
│   ├── bucv_detalle_recurso.php    ← Detalle de un recurso
│   ├── bucv_autores.php            ← Listado de autores
│   ├── bucv_editarlibro.php        ← Edición de recursos
│   └── ...
│
├── 📁 ajax/                        ← Endpoints AJAX
│   ├── bucv_recursos.php           ← API de recursos
│   ├── bucv_autores.php            ← API de autores
│   └── bucv_usuarios.php           ← API de usuarios
│
├── 📁 components/                  ← Componentes reutilizables
│   ├── bucv_header.php             ← Encabezado
│   ├── bucv_footer.php             ← Pie de página
│   ├── bucv_head.php               ← Meta tags y CSS
│   ├── bucv_scripts.php            ← Scripts JS
│   ├── bucv_sidebar.php            ← Barra lateral
│   └── ...
│
├── 📁 assets/                      ← Recursos estáticos
│   ├── css/                        ← Estilos
│   │   ├── bucv_base.css
│   │   ├── bucv_variables.css
│   │   ├── bucv_header.css
│   │   ├── bucv_hero.css
│   │   ├── bucv_footer.css
│   │   ├── bucv_components.css
│   │   ├── bucv_forms.css
│   │   ├── bucv_admin.css
│   │   └── bucv_responsive.css
│   ├── js/                        ← Scripts
│   │   ├── bucv_biblioteca.js
│   │   └── bucv_admin.js
│   └── images/                    ← Imágenes
│
├── 📁 config/                      ← Configuración
│   └── DotEnv.php                  ← Gestor de variables .env
│
└── 📁 repositories/                ← Acceso a datos
    ├── bucv_1cc2s4B3.php           ← Clase principal de BD
    └── DER.sql                     ← Esquema de BD

## 🎨 Tecnologías Utilizadas

| Tecnología | Versión | Uso |
|------------|---------|-----|
| PHP | 8.2.12 | Backend |
| MySQL/MariaDB | 8.0 / 10.4 | Base de Datos |
| HTML5 | - | Estructura |
| CSS3 | - | Estilos (Variables CSS) |
| JavaScript | ES6+ | Interactividad AJAX |
| XAMPP | 8.2.12 | Servidor Local |
