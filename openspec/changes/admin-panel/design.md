# Design: Admin Panel Filament para Portfolio

## Architecture Overview

Single Filament v5 panel at `/admin` with Spanish UI, respecting Profile singleton pattern.

### Panel Configuration
- Single panel via `App\Providers\Filament\AdminPanelProvider`
- Path: `/admin`
- Auth: Filament built-in (email/password), no registration
- Language: Spanish (neutral/professional)

### Navigation Groups (Spanish)
| Group | Items |
|-------|-------|
| _(top)_ | Dashboard |
| Perfil | Profile (custom page) |
| Portafolio | Projects, Experience |
| Habilidades | Skills, Education, Languages |
| Enlaces | Links |

## Component Tree

```
Admin Panel (/admin)
├── DashboardPage (custom, with stats)
├── ManageProfilePage (custom page, singleton edit)
├── LinkResource (List, Create, Edit)
├── SkillResource (List, Create, Edit)
├── LanguageResource (List, Create, Edit)
├── ProjectResource (List, Create, Edit)
│   └── ImageRelationManager (screenshots/logos)
├── ExperienceResource (List, Create, Edit)
└── EducationResource (List, Create, Edit)
    └── ImageRelationManager (certificates)
```

**No Image resource** — images managed exclusively via RelationManagers.

## Data Flow

### Profile Singleton Pattern
```
Filament Form Submit
  → Form validation
  → Model::update() (NOT create)
  → ProfileObserver::creating() blocks duplicate creation
  → If no Profile exists → create; if exists → error/redirect
```

### Polymorphic Images via RelationManager
```
ProjectResource → RelationManager
  → Image (morphTo imageable: Project)
  → url, alt_text, type, sort_order

EducationResource → RelationManager
  → Image (morphTo imageable: Education)

ManageProfilePage → RelationManager
  → Image (morphTo imageable: Profile, type=avatar)
```

## Form Schemas

### ManageProfilePage (custom page)
- TextInput: name (required, max 255)
- TextInput: title (required, max 255)
- Textarea: summary (rows 4)
- TextInput: location
- TextInput: phone
- TextInput: email (required, email validation)
- Image RelationManager/FileUpload: avatar

### ProjectResource
- TextInput: name (required)
- Textarea: description
- TagsInput: tech_stack
- TextInput: role
- TextInput: team_size (numeric)
- TextInput: url
- TextInput: repo_url
- DatePicker: start_date (required)
- DatePicker: end_date
- Toggle: is_current
- Toggle: is_featured
- RelationManager: images (screenshots)

### ExperienceResource
- TextInput: company (required)
- TextInput: role (required)
- Textarea: description
- TextInput: location
- DatePicker: start_date (required)
- DatePicker: end_date
- Toggle: is_current

### SkillResource
- TextInput: name (required)
- Select: category (options: Lenguajes, Frameworks, Bases de Datos, Herramientas, Metodologías)
- TextInput: sort_order (numeric, default 0)

### EducationResource
- TextInput: institution (required)
- TextInput: degree (required)
- TextInput: field
- Textarea: description
- DatePicker: start_date
- DatePicker: end_date
- TextInput: sort_order (numeric, default 0)
- RelationManager: images (certificates)

### LinkResource
- TextInput: label (required)
- TextInput: url (required, URL validation)
- TextInput: icon
- TextInput: sort_order (numeric, default 0)

### LanguageResource
- TextInput: name (required)
- Select: level (options: Básico, Conversacional, Profesional, Nativo)
- TextInput: sort_order (numeric, default 0)

## Dashboard Widgets

StatsWidget with cards:
- Total Projects
- Total Skills
- Total Education entries
- Profile status (exists/not exists)

Optional: latest projects list.

## PR Implementation Plan

### PR #1 — Foundation (~280 lines)
**Branch**: PR1 targets `develop`

Files:
- `composer.json` — add `filament/filament:"~5.0"`
- Run `composer require filament/filament:"~5.0"`
- Run `php artisan filament:install --panels`
- `app/Providers/Filament/AdminPanelProvider.php` — configure panel, path, auth, Spanish
- `config/filament.php` (if created) — adjust config
- `database/seeders/DatabaseSeeder.php` — add admin user creation
- `app/Filament/Pages/Dashboard.php` — custom dashboard with stats
- `tests/Feature/AdminPanelTest.php` — auth, dashboard access

### PR #2 — Profile Page (~300 lines)
**Branch**: PR2 targets PR1 branch

Files:
- `app/Filament/Pages/ManageProfile.php` — custom page with form
- Profile form schema (name, title, summary, location, phone, email, avatar)
- Singleton logic (get record, update only)
- Navigation registration
- `tests/Feature/ProfileAdminTest.php`

### PR #3 — Links + Skills + Languages (~380 lines)
**Branch**: PR3 targets PR2 branch

Files:
- `app/Filament/Resources/LinkResource.php`
- `app/Filament/Resources/SkillResource.php`
- `app/Filament/Resources/LanguageResource.php`
- `tests/Feature/LinkAdminTest.php`
- `tests/Feature/SkillAdminTest.php`
- `tests/Feature/LanguageAdminTest.php`

### PR #4 — Projects + Experience (~390 lines)
**Branch**: PR4 targets PR3 branch

Files:
- `app/Filament/Resources/ProjectResource.php` (with ImageRelationManager)
- `app/Filament/Resources/ExperienceResource.php`
- `tests/Feature/ProjectAdminTest.php`
- `tests/Feature/ExperienceAdminTest.php`

### PR #5 — Education + Avatar (~350 lines)
**Branch**: PR5 targets PR4 branch

Files:
- `app/Filament/Resources/EducationResource.php` (with ImageRelationManager for certificates)
- Profile avatar integration
- `tests/Feature/EducationAdminTest.php`

## Test Strategy

Per TDD (Strict TDD active):
- Write test → Run (fails) → Implement → Run (passes)
- Each PR includes feature tests for its Resources
- Tests cover: CRUD operations, validation, navigation access, singleton enforcement

## Open Questions (resolved in decision)
- Image handling: RelationManager only, no independent Resource ✅
- Auth: Filament basic, no Socialite ✅
- Language: Spanish (neutral/professional) ✅
- Profile: Custom page (not Resource) ✅
- Delivery: 5 chained PRs, feature-branch-chain ✅
