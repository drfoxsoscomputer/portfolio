# Portfolio — Denis Piña

Portfolio personal construido con **Laravel 13**. Incluye panel de administración Filament v5, capa de datos con 8 modelos, imágenes polimórficas y cobertura completa de tests.

## Stack

- **Framework**: Laravel 13.22.0
- **PHP**: 8.3+
- **Base de datos**: SQLite (desarrollo/testing)
- **Testing**: PHPUnit 12 (48 tests)
- **Admin Panel**: Filament v5 (español)
- **Frontend**: Vite (listo para Tailwind/Livewire)

## Panel de Administración

El panel está en `/admin` con interfaz en español.

**Credenciales por defecto:**
- **Email**: `daprthefox@gmail.com`
- **Password**: `asdf1234`

```
php artisan migrate --seed
```

### Recursos del panel

| Grupo | Recursos |
|-------|----------|
| Dashboard | Estadísticas del portafolio |
| Perfil | Edición del perfil (singleton) |
| Portafolio | Proyectos, Experiencia |
| Habilidades | Skills, Educación, Idiomas |
| Enlaces | Links |

## Modelo de Datos

```
Profile (singleton)
 ├── hasMany → Link          (redes sociales)
 ├── hasMany → Project       (proyectos, tech_stack JSON, is_featured)
 ├── hasMany → Experience    (experiencia laboral)
 ├── hasMany → Skill         (habilidades por categoría)
 ├── hasMany → Education     (formación académica)
 ├── hasMany → Language      (idiomas)
 └── morphMany → Image       (avatar, capturas, logos, certificados)

Project  → morphMany → Image
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
php artisan test        # 48 tests, todos verdes
php artisan serve       # Servidor de desarrollo en localhost:8000
```

El seeder crea un Profile con todos los datos reales del CV (proyectos, experiencia, skills, educación, idiomas, enlaces) y un usuario administrador para el panel.

## SDD (Spec-Driven Development)

Este proyecto usa SDD. Los artifacts están en `openspec/`:

```
openspec/
├── changes/
│   ├── archive/2026-07-26-cv-models/      ← CV models (archivado)
│   └── admin-panel/                        ← Admin panel (en progreso)
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
