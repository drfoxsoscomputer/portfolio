# Portfolio — Denis Piña

Personal portfolio webapp built with **Laravel 13**. CV data layer with 8 models, polymorphic image support, and full test coverage.

## Stack

- **Framework**: Laravel 13.22.0
- **PHP**: 8.3+
- **Database**: SQLite (dev/testing)
- **Testing**: PHPUnit 12 (43 tests)
- **Frontend**: Vite (ready for Tailwind/Livewire)

## Data Model

```
Profile (singleton)
 ├── hasMany → Link          (redes sociales)
 ├── hasMany → Project       (proyectos, tech_stack JSON, is_featured)
 ├── hasMany → Experience    (experiencia laboral)
 ├── hasMany → Skill         (skills por categoría)
 ├── hasMany → Education     (formación académica)
 ├── hasMany → Language      (idiomas)
 └── morphMany → Image       (avatar, capturas, logos, certificados)

Project  → morphMany → Image
Education → morphMany → Image
```

### Ordenamiento

| Modelo | Orden |
|--------|-------|
| Project | `start_date` DESC |
| Experience | `start_date` DESC |
| Education | `sort_order` ASC (drag & drop) |
| Link, Skill, Language, Image | `sort_order` ASC |

## Setup

```bash
php artisan migrate --seed
php artisan test        # 43 tests, all green
```

El seeder crea un Profile con todos los datos reales del CV (proyectos, experiencia, skills, educación, idiomas, enlaces).

## SDD

Este proyecto usa **Spec-Driven Development**. Los artifacts están en `openspec/`:

```
openspec/
├── changes/archive/2026-07-26-cv-models/   ← CV models (archived, complete)
│   ├── exploration.md
│   ├── proposal.md
│   ├── design.md
│   └── tasks.md
└── specs/
    ├── cv-data-models/
    ├── singleton-profile-enforcement/
    ├── polymorphic-images/
    └── tech-stack-json/
```

> **Estado**: El cambio `cv-models` está archivado. 8 modelos, 43 tests, 142 aserciones — todo verde.

## License

MIT
