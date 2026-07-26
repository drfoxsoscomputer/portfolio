# Exploration: CV Data Models

## Current State

The project is a **fresh Laravel 13.22.0** install running on **PHP 8.3.31** with SQLite (dev) and SQLite in-memory (testing via PHPUnit 12.5). There are zero custom models — only the stock `User` model exists under `app/Models/User.php`, which uses Laravel 13's PHP 8 attribute convention (`#[Fillable]`, `#[Hidden]`).

**Existing artifacts:**
- **Models (1):** `app/Models/User.php` — stock Authenticatable with `HasFactory` + `Notifiable`
- **Migrations (3):** `users`, `cache`, `jobs` — all Laravel defaults
- **Factories (1):** `Database\Factories\UserFactory.php`
- **Seeders:** empty directory
- **Tests:** `tests/Unit/ExampleTest.php`, `tests/Feature/ExampleTest.php` — both boilerplate

**Conventions observed:**
- Plural snake_case table names (Laravel default)
- `HasFactory` trait on models
- `Database\Factories` namespace for factories
- PHP 8 attributes for fillable/hidden instead of `$fillable`/`$hidden` properties
- SQLite with foreign key constraints enabled
- `php artisan test` runs both Unit and Feature suites

## Affected Areas

The following files and directories will be created (no existing files modified):

```
app/Models/
├── Profile.php          # Personal info (singleton)
├── Link.php             # Social/profile links
├── Project.php          # Portfolio projects
├── Experience.php       # Professional experience
├── Skill.php            # Technical skills
├── Education.php        # Education entries
├── Language.php         # Languages spoken
└── Image.php            # Polymorphic images (avatar, screenshots, logos, certificates)

database/migrations/
├── xxxx_xx_xx_xxxxxx_create_profiles_table.php
├── xxxx_xx_xx_xxxxxx_create_links_table.php
├── xxxx_xx_xx_xxxxxx_create_projects_table.php
├── xxxx_xx_xx_xxxxxx_create_experiences_table.php
├── xxxx_xx_xx_xxxxxx_create_skills_table.php
├── xxxx_xx_xx_xxxxxx_create_education_table.php
├── xxxx_xx_xx_xxxxxx_create_languages_table.php
└── xxxx_xx_xx_xxxxxx_create_images_table.php

database/factories/
├── ProfileFactory.php
├── LinkFactory.php
├── ProjectFactory.php
├── ExperienceFactory.php
├── SkillFactory.php
├── EducationFactory.php
├── LanguageFactory.php
└── ImageFactory.php

database/seeders/
└── DatabaseSeeder.php   # Updated to call CV seeders

tests/Unit/
├── ProfileTest.php
├── LinkTest.php
├── ProjectTest.php
├── ExperienceTest.php
├── SkillTest.php
├── EducationTest.php
├── LanguageTest.php
└── ImageTest.php
```

**No controllers, routes, or views are affected** — this change is purely the data layer (models + migrations + factories + unit tests).

## Approaches Compared

### Approach A (Flat/FK) — Simple foreign keys, no polymorphism

Each entity has a direct `profile_id` foreign key to `Profile`. Links belong only to Profile. Tech stack is JSON on Project. Skill category is a plain string.

| Pros | Cons | Effort |
|------|------|--------|
| Simple, explicit queries with `WHERE profile_id = ?` | Links can't attach to projects/experience without extra columns | **Low** |
| No morph map to maintain | Slightly less flexible for hypothetical multi-entity links | |
| Straightforward migrations and seeders | | |
| Easy for Laravel newcomers to understand | | |
| SQLite-compatible (JSON columns work fine) | | |

### Approach B (Polymorphic) — morphMany for links

`Link` uses a polymorphic `morphs('linkable')` so it can attach to Profile, Project, Experience, and Education. Everything else is the same as Approach A.

| Pros | Cons | Effort |
|------|------|--------|
| Links can attach to any model | More complex queries (`morphTo` eager loading) | **Medium** |
| Single `links` table for everything | Morph map maintenance required | |
| Flexible if the same link URL applies to multiple entity types | SQLite handles polymorphic nullable morph columns but indexing is trickier | |
| | Over-engineered for a personal portfolio with one profile | |
| | `linkable_id` + `linkable_type` are nullable + wider columns | |

### Key Design Decisions Explored

| Decision | Option A | Option B | Recommendation |
|----------|----------|----------|----------------|
| **Link polymorphism** | Simple FK to profile | `morphs('linkable')` | **A (Simple FK)** — social links belong to profile; project URLs are separate `url`/`repo_url` fields on Project |
| **Image polymorphism** | `morphs('imageable')` | Simple FK per entity | **A (Polymorphic)** — images need to attach to Profile (avatar), Project (screenshots/logos), and Education (certificates). Polymorphism avoids separate tables per entity |
| **Skill categories** | Plain string `category` column | Separate `skill_categories` table or backed enum | **String** — simple, readable, no migration overhead for a portfolio |
| **Profile multiplicity** | Singleton (single record enforced via logic/seed) | Support multiple profiles (user_id FK) | **Singleton** — personal portfolio, one profile |
| **Project tech_stack** | JSON column (array of strings) | Separate `project_technology` pivot table | **JSON** — SQLite supports JSON, simpler queries, good enough |
| **Date fields** | `date` type columns | `datetime` or string | **`date`** — CV data is month/year resolution; time is irrelevant |

## Recommendation

### Adopt Approach A (Flat/FK)

A clean, explicit set of 7 models with simple `belongsTo`/`hasMany` relationships. The data is inherently hierarchical (one profile owns everything), so polymorphism adds complexity with zero practical gain for this use case.

### Model Definitions

#### 1. Profile (`profiles` table)
| Field | Type | Rules | Notes |
|-------|------|-------|-------|
| `id` | `id()` | PK | |
| `name` | `string()` | required | "Denis Piña" |
| `title` | `string()` | nullable | "Desarrollador Full Stack" |
| `location` | `string()` | nullable | "Venezuela, Lara" |
| `phone` | `string()` | nullable | |
| `email` | `string()` | nullable | |
| `summary` | `text()` | nullable | Bio paragraph |
| `avatar` | `string()` | nullable | Photo path/URL |
| `timestamps` | | | |

**Relationships:** `hasMany(Link)`, `hasMany(Project)`, `hasMany(Experience)`, `hasMany(Skill)`, `hasMany(Education)`, `hasMany(Language)`

**Singleton enforcement:** via seeder (create single record) + validation in `ProfileObserver` or service layer.

#### 2. Link (`links` table)
| Field | Type | Rules | Notes |
|-------|------|-------|-------|
| `id` | `id()` | PK | |
| `profile_id` | `foreignId()->constrained()` | required | FK to profiles |
| `label` | `string()` | required | "LinkedIn", "GitHub" |
| `url` | `string()` | required | Full URL |
| `icon` | `string()` | nullable | Icon name for UI |
| `sort_order` | `unsignedInteger()->default(0)` | | Ordering |
| `timestamps` | | | |

**Relationships:** `belongsTo(Profile)`

#### 3. Project (`projects` table)
| Field | Type | Rules | Notes |
|-------|------|-------|-------|
| `id` | `id()` | PK | |
| `profile_id` | `foreignId()->constrained()` | required | FK to profiles |
| `name` | `string()` | required | "GameWorld Ecommerce" |
| `description` | `text()` | nullable | |
| `tech_stack` | `json()` | nullable | `["React","Redux","Laravel"]` |
| `role` | `string()` | nullable | "Full Stack Developer" |
| `team_size` | `unsignedTinyInteger()` | nullable | |
| `url` | `string()` | nullable | Live demo URL |
| `repo_url` | `string()` | nullable | GitHub repo |
| `start_date` | `date()` | nullable | |
| `end_date` | `date()` | nullable | |
| `is_current` | `boolean()->default(false)` | | Still active? |
| `is_featured` | `boolean()->default(false)` | | Shows prominently on portfolio |
| `timestamps` | | | |

**Relationships:** `belongsTo(Profile)`
**Default order:** `start_date` DESC (newest first)
**Cast:** `tech_stack` → `array`

#### 4. Experience (`experiences` table)
| Field | Type | Rules | Notes |
|-------|------|-------|-------|
| `id` | `id()` | PK | |
| `profile_id` | `foreignId()->constrained()` | required | FK to profiles |
| `company` | `string()` | required | |
| `role` | `string()` | required | Job title |
| `description` | `text()` | nullable | |
| `location` | `string()` | nullable | |
| `start_date` | `date()` | required | |
| `end_date` | `date()` | nullable | null = current |
| `is_current` | `boolean()->default(false)` | |
| `timestamps` | | | |

**Relationships:** `belongsTo(Profile)`
**Default order:** `start_date` DESC (newest first)

#### 5. Skill (`skills` table)
| Field | Type | Rules | Notes |
|-------|------|-------|-------|
| `id` | `id()` | PK | |
| `profile_id` | `foreignId()->constrained()` | required | FK to profiles |
| `name` | `string()` | required | "PHP", "Laravel" |
| `category` | `string()` | required | "Lenguajes", "Frameworks" |
| `sort_order` | `unsignedInteger()->default(0)` | | Order within category |
| `timestamps` | | | |

**Relationships:** `belongsTo(Profile)`

#### 6. Education (`education` table)
| Field | Type | Rules | Notes |
|-------|------|-------|-------|
| `id` | `id()` | PK | |
| `profile_id` | `foreignId()->constrained()` | required | FK to profiles |
| `institution` | `string()` | required | |
| `degree` | `string()` | required | "TSU", "Full Stack Web Developer" |
| `field` | `string()` | nullable | "Información y Documentación" |
| `description` | `text()` | nullable | |
| `start_date` | `date()` | nullable | |
| `end_date` | `date()` | nullable | |
| `is_current` | `boolean()->default(false)` | | |
| `sort_order` | `unsignedInteger()->default(0)` | | Custom ordering (weight) |
| `timestamps` | | | |

**Relationships:** `belongsTo(Profile)`
**Default order:** `sort_order` ASC (custom weight-based ordering)

#### 7. Language (`languages` table)
| Field | Type | Rules | Notes |
|-------|------|-------|-------|
| `id` | `id()` | PK | |
| `profile_id` | `foreignId()->constrained()` | required | FK to profiles |
| `name` | `string()` | required | "Español", "Inglés" |
| `level` | `string()` | nullable | "Nativo", "Básico" |
| `sort_order` | `unsignedInteger()->default(0)` | | |
| `timestamps` | | | |

**Relationships:** `belongsTo(Profile)`

#### 8. Image (`images` table) — Polymorphic
| Field | Type | Rules | Notes |
|-------|------|-------|-------|
| `id` | `id()` | PK | |
| `imageable_id` | `morphs()->constrained()` | required | Morph FK |
| `imageable_type` | `morphs()->constrained()` | required | Morph type |
| `url` | `string()` | required | Image path/URL |
| `alt_text` | `string()` | nullable | Accessibility |
| `type` | `string()` | nullable | "avatar", "screenshot", "logo", "certificate" |
| `sort_order` | `unsignedInteger()->default(0)` | | |
| `timestamps` | | | |

**Relationships:** `morphTo(Image)` — called `imageable()`
**Imageable models:** Profile (avatar), Project (screenshots/logos), Education (certificates)

### Relationships Summary

```
Profile
 ├── hasMany → Link
 ├── hasMany → Project       (tech_stack cast as array)
 ├── hasMany → Experience
 ├── hasMany → Skill
 ├── hasMany → Education
 ├── hasMany → Language
 └── morphMany → Image        (as imageable)

Project → morphMany → Image   (as imageable)
Education → morphMany → Image (as imageable)
```

Child models with FK `belongsTo(Profile)`. Image uses **polymorphic** `morphMany`/`morphTo`. One migration per model (8 total).

### Factory Strategy

Each model gets a factory. Use `for()` to associate with a profile that's created via `Profile::factory()`, or faker-generated data for testing:
- **ProfileFactory:** fake name, title, location
- **LinkFactory:** fake label ("GitHub", "LinkedIn") + URL
- **ProjectFactory:** fake project name, random tech stack array
- **ExperienceFactory:** fake company, role, date range
- **SkillFactory:** predefined list of skills with categories
- **EducationFactory:** fake institution, degree
- **LanguageFactory:** static set (Español-Nativo, Inglés-Básico)
- **ImageFactory:** Fake URL, alt text, attaches via `imageable()` to a random Profile/Project/Education

### Seeder

`DatabaseSeeder.php` will call:
```
$this->call([
    ProfileSeeder::class,    // Creates one profile with all CV data
]);
```

The `ProfileSeeder` creates the singleton profile and attaches all CV data from the PDF, making the portfolio immediately usable after `php artisan migrate --seed`.

## Risks

1. **No risk** — models are additive, no existing code is modified, no migration conflicts with the three stock tables.
2. **SQLite JSON support** — SQLite has supported JSON natively since 3.38.0 (2022). Since Laravel 13 requires a recent PHP/SQLite version, this is a non-issue, but worth verifying the installed SQLite version supports `json()` column type.
3. **Profile singleton leak** — if a second Profile record is accidentally created, queries may return unexpected results. Mitigation: service layer or observer to prevent multiple profiles, enforced at the application level.
4. **`date` vs `datetime` for CV dates** — CV data has month/year resolution. Using `date` is appropriate but means no time precision. If dates need to be more precise later, a migration can change the column type.
5. **Polymorphic Image morph map** — requires an `AppServiceProvider` registration to use model class names instead of full namespace in `imageable_type`. Easy to forget.

## Ready for Proposal

**Yes.** The data model design is straightforward and well-understood. The orchestrator should tell the user:

- 8 models, 8 migrations, 8 factories, 1 seeder, 8 unit tests
- Simple `belongsTo`/`hasMany` relationships for Profile children
- **Image** uses polymorphic `morphMany` (attaches to Profile, Project, Education)
- Profile is a singleton with all child entities FK'd to it
- JSON for tech_stack on projects, string for skill categories
- No controllers, routes, or views affected
- All data can be seeded from the extracted PDF content
