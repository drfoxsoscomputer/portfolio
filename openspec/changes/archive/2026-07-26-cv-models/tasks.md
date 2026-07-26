# Tasks: CV Data Models

## Review Workload Forecast

| Field | Value |
|-------|-------|
| Estimated changed lines | ~900–1200 |
| 400-line budget risk | High |
| Chained PRs recommended | Yes |
| Suggested split | PR 1 (Foundation) → PR 2 (FK Entities) → PR 3 (Images + Testing + Seeder) |
| Delivery strategy | force-chained |
| Chain strategy | feature-branch-chain |

Decision needed before apply: No
Chained PRs recommended: Yes
Chain strategy: feature-branch-chain
400-line budget risk: High

### Suggested Work Units

| Unit | Goal | Likely PR | Notes |
|------|------|-----------|-------|
| 1 | Foundation (Profile migration, model, factory, observer, morph map) | PR 1 | Base = feature/cv-models tracker branch |
| 2 | FK entities (Link, Project, Experience, Skill, Education, Language — migrations + models + factories) | PR 2 | Base = PR 1 branch |
| 3 | Polymorphic Image + Testing + ProfileSeeder | PR 3 | Base = PR 2 branch |

## Phase 1: Foundation — Profile + Provider

- [x] 1.1 Create `create_profiles_table` migration with all Profile fields (name, title, location, phone, email, summary, avatar, timestamps)
- [x] 1.2 Create `app/Models/Profile.php` with `#[Fillable]`, `HasFactory`, casts, and relationship methods (hasMany x6 + morphMany Images)
- [x] 1.3 Create `database/factories/ProfileFactory.php` with realistic fake CV data
- [x] 1.4 Create `app/Observers/ProfileObserver.php` with `creating()` event that checks `Profile::exists()` and throws on duplicate
- [x] 1.5 Register ProfileObserver in `AppServiceProvider::boot()` via `Profile::observe()`
- [x] 1.6 Register Image morph map in `AppServiceProvider::boot()`: `Relation::morphMap(['profile' => Profile::class, 'project' => Project::class, 'education' => Education::class])`

## Phase 2: FK Entities — 6 Models

- [x] 2.1 Create `create_links_table` migration (profile_id FK, label, url, icon, sort_order)
- [x] 2.2 Create `app/Models/Link.php` with `belongsTo(Profile)`, `#[Fillable]`, `sort_order` default ordering
- [x] 2.3 Create `database/factories/LinkFactory.php`
- [x] 2.4 Create `create_projects_table` migration (profile_id FK, name, description, tech_stack json, role, team_size, url, repo_url, start_date, end_date, is_current, is_featured)
- [x] 2.5 Create `app/Models/Project.php` with `belongsTo(Profile)`, `tech_stack` array cast, `start_date DESC` ordering, `#[Fillable]`
- [x] 2.6 Create `database/factories/ProjectFactory.php` with random tech_stack arrays
- [x] 2.7 Create `create_experiences_table` migration (profile_id FK, company, role, description, location, start_date, end_date, is_current)
- [x] 2.8 Create `app/Models/Experience.php` with `belongsTo(Profile)`, `start_date DESC` ordering, `#[Fillable]`
- [x] 2.9 Create `database/factories/ExperienceFactory.php`
- [x] 2.10 Create `create_skills_table` migration (profile_id FK, name, category, sort_order)
- [x] 2.11 Create `app/Models/Skill.php` with `belongsTo(Profile)`, `sort_order ASC` ordering, `#[Fillable]`
- [x] 2.12 Create `database/factories/SkillFactory.php` with predefined skill list
- [x] 2.13 Create `create_education_table` migration (profile_id FK, institution, degree, field, description, start_date, end_date, is_current, sort_order)
- [x] 2.14 Create `app/Models/Education.php` with `belongsTo(Profile)`, `morphMany Images`, `sort_order ASC` ordering
- [x] 2.15 Create `database/factories/EducationFactory.php`
- [x] 2.16 Create `create_languages_table` migration (profile_id FK, name, level, sort_order)
- [x] 2.17 Create `app/Models/Language.php` with `belongsTo(Profile)`, `sort_order ASC` ordering, `#[Fillable]`
- [x] 2.18 Create `database/factories/LanguageFactory.php`

## Phase 3: Polymorphic Image + Seeder

- [x] 3.1 Create `create_images_table` migration (morphs imageable, url, alt_text, type, sort_order)
- [x] 3.2 Create `app/Models/Image.php` with `morphTo('imageable')`, `type` string, `sort_order ASC` ordering
- [x] 3.3 Create `database/factories/ImageFactory.php` that attaches to Profile/Project/Education randomly
- [x] 3.4 Create `database/seeders/ProfileSeeder.php` — singleton profile + all CV child records with complete real data
- [x] 3.5 Update `database/seeders/DatabaseSeeder.php` to call `ProfileSeeder::class`

## Phase 4: Unit Tests

- [x] 4.1 Write `tests/Unit/ProfileTest.php` — singleton enforcement, relationships, factory creation
- [x] 4.2 Write `tests/Unit/LinkTest.php` — belongsTo Profile, sort_order default, factory
- [x] 4.3 Write `tests/Unit/ProjectTest.php` — belongsTo Profile, tech_stack array cast, date ordering, factory
- [x] 4.4 Write `tests/Unit/ExperienceTest.php` — belongsTo Profile, date ordering, factory
- [x] 4.5 Write `tests/Unit/SkillTest.php` — belongsTo Profile, category validation, sort_order, factory
- [x] 4.6 Write `tests/Unit/EducationTest.php` — belongsTo Profile, morphMany Images, sort_order, factory
- [x] 4.7 Write `tests/Unit/LanguageTest.php` — belongsTo Profile, sort_order, factory
- [x] 4.8 Write `tests/Unit/ImageTest.php` — morphTo imageable, type string, attach to all 3 entities, factory
