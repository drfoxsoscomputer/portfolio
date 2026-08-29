# Portfolio — Denis Piña

Portfolio personal construido con **Laravel 13**. Incluye panel de administración Filament v5, página pública con Livewire, capa de datos con 10 modelos, imágenes polimórficas y cobertura completa de tests.

## Stack

- **Framework**: Laravel 13.22.0
- **PHP**: 8.3+
- **Base de datos**: SQLite (desarrollo/testing)
- **Testing**: PHPUnit 12 (142 tests)
- **Admin Panel**: Filament v5 (español)
- **Frontend**: Livewire 4 + Tailwind CSS v4 + Alpine.js

## Panel de Administración

El panel está en `/admin` con interfaz en español.

Antes de ejecutar el seeder, definí las credenciales del administrador en tu `.env` (ver `.env.example`):

```env
ADMIN_EMAIL=tu-email@ejemplo.com
ADMIN_PASSWORD=una-clave-segura
```

```
php artisan migrate --seed
```

El seeder usa esos valores para crear el usuario administrador del panel.

### Recursos del panel

| Grupo | Recursos |
|-------|----------|
| Dashboard | Estadísticas del portafolio |
| Perfil | Edición del perfil · Enlaces de Contacto · Habilidades · Idiomas |

## Modelo de Datos

```
User
 ├── hasMany → Link          (redes sociales)
 ├── hasMany → Project       (proyectos, tech_stack JSON, is_featured)
 ├── hasMany → Experience    (experiencia laboral)
 ├── hasMany → Skill         (habilidades por categoría)
 ├── hasMany → Education     (formación académica)
 ├── hasMany → Language      (idiomas)
 ├── hasMany → Course        (cursos y certificaciones)
 └── morphMany → Image       (avatar, capturas, logos, certificados)

Project   → morphMany → Image
Education → morphMany → Image
```

### Ordenamiento por defecto

| Modelo | Orden |
|--------|-------|
| Project | `start_date` DESC |
| Experience | `start_date` DESC |
| Education | `sort_order` ASC |
| Link, Skill, Language, Image | `sort_order` ASC |

## CV descargable (`GET /cv`)

`/cv` descarga el currículo como PDF (`CV_DenisPina_FullStack.pdf`), generado por dompdf desde `resources/views/cv.blade.php`. Es el entregable ATS del proyecto. Conducta y convenciones:

- **Formato**: una columna, headings en MAYÚSCULAS azul `#1e40af` con línea inferior, fechas en gris itálica, bullets de texto, links como URL visible. Sin tablas, sin imágenes, sin información en header/footer de página (los parsers ATS la descartan).
- **Orden de secciones**: PERFIL PROFESIONAL → PROYECTOS → EXPERIENCIA PROFESIONAL → TECNOLOGÍAS → EDUCACIÓN → FORMACIÓN COMPLEMENTARIA → IDIOMAS. Límites por sección: proyectos 4, experiencia 3, educación 3, cursos 5, idiomas 3.
- **EDUCACIÓN vs FORMACIÓN COMPLEMENTARIA**: en Educación van los títulos/tecnicaturas *y los bootcamps* (programas estructurados de larga duración, p. ej. Henry · 800 horas). En Formación Complementaria van los cursos cortos sueltos (Laracasts, Platzi, etc.). Un bootcamp **nunca** se clasifica como curso.
- **Orden dentro de EDUCACIÓN**: por `sort_order` (editables en el panel admin), **no por fecha**. Se respeta el peso académico: el título universitario queda antes que el bootcamp.
- **FORMACIÓN COMPLEMENTARIA**: orden cronológico inverso por `date`.
- **Fechas**: mes abreviado en español (`Nov. 2023`), `Presente` cuando `is_current`; aquí además escribe solo el año cuando el mes es enero o se trata de educación. Formateador en `app/Support/CvFormatter.php`.
- **Versión visible**: última línea "Última actualización: {Mes AAAA}", calculada como el `updated_at` más reciente del perfil y sus relaciones.

## Setup

```bash
php artisan migrate --seed
php artisan test        # 142 tests, todos verdes
php artisan serve       # Servidor de desarrollo en localhost:8000
```

El seeder crea el usuario administrador con todos los datos del CV (proyectos, experiencia, skills, educación, idiomas, enlaces y cursos) usando las credenciales definidas en el `.env`.

## SDD (Spec-Driven Development)

Este proyecto usa SDD. Los artifacts están en `openspec/`:

```
openspec/
├── changes/
│   ├── archive/2026-07-26-cv-models/      ← CV models (archivado)
│   ├── public-portfolio-page/             ← Página pública (PR #8 en progreso)
│   └── admin-panel/                       ← Admin panel (mergeado)
│       ├── proposal.md
│       ├── design.md
│       ├── tasks.md
│       └── specs/
└── specs/
    ├── cv-data-models/
    ├── singleton-profile-enforcement/
    ├── polymorphic-images/
    └── tech-stack-json/
```

## Licencia

MIT
