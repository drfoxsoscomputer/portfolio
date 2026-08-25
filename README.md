# Portfolio — Denis Piña

Portfolio personal construido con **Laravel 13**. Incluye panel de administración Filament v5, página pública con Livewire, capa de datos con 10 modelos, imágenes polimórficas y cobertura completa de tests.

## Stack

- **Framework**: Laravel 13.22.0
- **PHP**: 8.3+
- **Base de datos**: SQLite (desarrollo/testing)
- **Testing**: PHPUnit 12 (115 tests)
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

## Setup

```bash
php artisan migrate --seed
php artisan test        # 115 tests, todos verdes
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
