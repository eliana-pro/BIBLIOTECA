# 📚 Biblioteca Digital UNICAB

Sistema de gestión integral de recursos académicos para la institución educativa UNICAB. Permite catalogar, buscar y administrar libros, artículos científicos, tesis y otros recursos educativos.

---

## 🚀 Características Principales

- ✅ **Catálogo de Recursos**: Búsqueda avanzada con filtros (autor, tipo, idioma, pensamiento)
- ✅ **Gestión Administrativa**: CRUD completo para recursos y autores
- ✅ **Estadísticas**: Dashboard con métricas de la biblioteca
- ✅ **Sistema de Autenticación**: Panel admin seguro
- ✅ **Diseño Responsivo**: Interfaz adaptable a dispositivos móviles
- ✅ **API AJAX**: Endpoints para operaciones dinámicas
- ✅ **Estructura Modular**: Fácil de mantener y escalar

---

## 📋 Requisitos del Sistema

| Requisito | Versión | Estado |
|-----------|---------|--------|
| XAMPP | 8.2.12 | ✅ |
| PHP | 8.2.12+ | ✅ |
| MySQL | 8.0.44+ | ✅ |
| Navegador | Moderno (Chrome, Firefox, Edge) | ✅ |

---

## 🔧 Instalación

### 1️⃣ Clonar/Descargar el Proyecto
```bash
git clone https://github.com/eliana-pro/BIBLIOTECA.git
cd BIBLIOTECA
```

### 2️⃣ Configurar Base de Datos

**Importar archivo SQL:**
```sql
1. Abrir phpMyAdmin: http://localhost/phpmyadmin
2. Crear nueva base de datos: biblioteca_db
3. Importar: repositories/DER.sql
```

### 3️⃣ Crear archivo `.env`
En la raíz del proyecto (`BIBLIOTECA/.env`):

```env
APP_ENV=production
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=u756063299_biblioteca
DB_USERNAME_L=
DB_PASSWORD_L=
DB_USERNAME_P=
DB_PASSWORD_P=
```

### 4️⃣ Iniciar Servidor
```bash
1. Abrir XAMPP Control Panel
2. Iniciar Apache y MySQL
3. Acceder a: http://localhost/BIBLIOTECA
```

---

## 📖 Uso

### 👤 Usuario Público
- Acceso a catálogo completo
- Búsqueda y filtrado de recursos
- Visualizar detalles de recursos
- Ver autores destacados

**URL:** `http://localhost/BIBLIOTECA/`

### 🔐 Panel Administrativo
- Gestión de recursos (crear, editar, eliminar)
- Gestión de autores
- Gestión de usuarios
- Estadísticas

**URL:** `http://localhost/BIBLIOTECA/admin/`  
**Login:** (Configurar según necesidad)

---

## 🗂️ Estructura del Proyecto

```
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
```

---

## 🔌 API AJAX

### Recursos
**GET** `/ajax/bucv_recursos.php?accion=listar`
```json
{
  "success": true,
  "data": [...recursos...],
  "total": 15
}
```

### Autores
**GET** `/ajax/bucv_autores.php`

### Usuarios
**POST** `/ajax/bucv_usuarios.php`

---

## 🎨 Tecnologías Utilizadas

| Tecnología | Versión | Uso |
|------------|---------|-----|
| PHP | 8.2.12 | Backend |
| MySQL/MariaDB | 8.0 / 10.4 | Base de Datos |
| HTML5 | - | Estructura |
| CSS3 | - | Estilos (Variables CSS) |
| JavaScript | ES6+ | Interactividad AJAX |
| XAMPP | 8.2.12 | Servidor Local |

---

## 🔒 Seguridad

- ✅ Prepared Statements para prevenir SQL Injection
- ✅ Password hashing con `password_hash()` (BCRYPT)
- ✅ Validación de entrada en formularios
- ✅ Control de acceso en panel admin
- ✅ Variables de entorno para credenciales sensibles

---

## 📝 Estándares de Código

- **PSR-4**: Autoloading de clases
- **Namespaces**: Organización de clases
- **Comentarios**: Documentación de métodos
- **Nomenclatura**: camelCase (métodos), UPPER_CASE (constantes)

---

## 🐛 Solución de Problemas

### Error: `.env does not exist`
```
Solución: Crear archivo .env en raíz del proyecto
```

### Error: 404 en peticiones AJAX
```
Solución: Verificar rutas relativas correctas
```

### Error: No conecta a BD
```
Solución: Verificar credenciales en .env y BD activa
```

---

## 👥 Autor

**Desarrollador:** Eliana Gamboa
**Institución:** UNICAB  
**Año:** 2025-2026

---

## 📄 Licencia

Este proyecto es de uso interno de la institución educativa UNICAB.

---

## 📞 Soporte

Para reportar bugs o sugerencias: 1206velandia.g@gmail.com
