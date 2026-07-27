# Tasks: Admin Panel Filament

## Review Workload Forecast

| Field | Value |
|-------|-------|
| Estimated changed lines | ~1700 (5 PRs) |
| 400-line budget risk | Low |
| Chained PRs recommended | Yes |
| Suggested split | PR #1 → PR #2 → PR #3 → PR #4 → PR #5 |
| Delivery strategy | force-chained |
| Chain strategy | feature-branch-chain |

Decision needed before apply: No
Chained PRs recommended: Yes
Chain strategy: feature-branch-chain
400-line budget risk: Low

### Current Status

## PR #1: Foundation (~280 lines) - IN PROGRESS

- [x] 1.1 Add `filament/filament:"~5.0"` to `composer.json`, run `composer update`
- [ ] 1.2 Run `php artisan filament:install --panels` to scaffold `app/Providers/Filament/AdminPanelProvider.php` + config
- [ ] 1.3 Configure `AdminPanelProvider`: path `/admin`, Spanish locale (`es`), disable registration, branding "Portafolio"
- [ ] 1.4 Create `app/Filament/Pages/Dashboard.php` — custom dashboard with StatsWidget (projects, skills, education, experience, languages counts + profile status)
- [ ] 1.5 Update `database/seeders/DatabaseSeeder.php` — ensure admin User exists with known credentials (email: admin@portfolio.com, password: password)
- [ ] 1.6 Write `tests/Feature/AdminPanelTest.php` — panel access, auth flow, dashboard stats render in Spanish
- [ ] 1.7 **TDD**: test login → implement → test dashboard stats → implement → test Spanish labels

### Suggested Work Units

| Unit | Scope | Branch Base | Est. Lines |
|------|-------|-------------|------------|
| PR #1 | Foundation: install, panel, dashboard, auth, seeder, test | `develop` | ~280 |
| PR #2 | Profile Page: ManageProfile custom page, singleton, test | PR #1 branch | ~300 |
| PR #3 | Links + Skills + Languages: 3 Resources + tests | PR #2 branch | ~380 |
| PR #4 | Projects + Experience: 2 Resources + ImageRelationManager + tests | PR #3 branch | ~390 |
| PR #5 | Education + Avatar: 1 Resource + ImageRelationManager + profile avatar + tests | PR #4 branch | ~350 |

## PR #1: Foundation (~280 lines)

- [ ] 1.1 Add `filament/filament:"~5.0"` to `composer.json`, run `composer update`
- [ ] 1.2 Run `php artisan filament:install --panels` to scaffold `app/Providers/Filament/AdminPanelProvider.php` + config
- [ ] 1.3 Configure `AdminPanelProvider`: path `/admin`, Spanish locale, disable registration, branding "Portafolio"
- [ ] 1.4 Create `app/Filament/Pages/Dashboard.php` — custom dashboard with StatsWidget (projects, skills, education, experience, languages counts + profile status)
- [ ] 1.5 Update `database/seeders/DatabaseSeeder.php` — ensure admin User exists with known credentials
- [ ] 1.6 Write `tests/Feature/AdminPanelTest.php` — panel access, auth flow, dashboard stats render in Spanish
- [ ] 1.7 **TDD**: test login → implement → test dashboard stats → implement → test Spanish labels

## PR #2: Profile Page (~300 lines)

- [ ] 2.1 Create `app/Filament/Pages/ManageProfile.php` — custom page extending `Page`, navigation group "Perfil"
- [ ] 2.2 Add form schema: TextInput name, title, location, phone, email; Textarea summary; FileUpload avatar
- [ ] 2.3 Implement singleton logic: `getRecord()` fetches first Profile, form uses `update()` not `create()`, handle missing Profile gracefully
- [ ] 2.4 Register navigation item "Mi Perfil" → `/admin/profile` in Spanish
- [ ] 2.5 Write `tests/Feature/ProfileAdminTest.php` — form renders with all fields, save updates profile, singleton enforcement shows Spanish error
- [ ] 2.6 **TDD**: test form renders → implement → test update succeeds → implement → test singleton creation blocked

## PR #3: Links + Skills + Languages (~380 lines)

- [ ] 3.1 Create `app/Filament/Resources/LinkResource.php` — fields: label (required), url (URL validation), icon, sort_order; Spanish labels: "Etiqueta", "URL", "Icono", "Orden"
- [ ] 3.2 Create `app/Filament/Resources/SkillResource.php` — fields: name (required), Select category (Lenguajes, Frameworks, Bases de Datos, Herramientas, Metodologías), sort_order; group by category in table
- [ ] 3.3 Create `app/Filament/Resources/LanguageResource.php` — fields: name (required), Select level (Básico, Intermedio, Avanzado, Nativo), sort_order; group by level in table
- [ ] 3.4 Configure all 3 resources with `shouldRegisterNavigation(true)`, navigation groups: Links→"Enlaces", Skills+Languages→"Habilidades"
- [ ] 3.5 Write `tests/Feature/LinkAdminTest.php` — CRUD, URL validation in Spanish
- [ ] 3.6 Write `tests/Feature/SkillAdminTest.php` — CRUD, category grouping, sort_order ordering
- [ ] 3.7 Write `tests/Feature/LanguageAdminTest.php` — CRUD, level selection, sort_order ordering
- [ ] 3.8 **TDD**: per resource — test create form renders → implement → test validation → implement → test list renders

## PR #4: Projects + Experience (~390 lines)

- [ ] 4.1 Create `app/Filament/Resources/ProjectResource.php` — fields: name (required), description (textarea), TagsInput tech_stack, role, team_size (numeric), url, repo_url, DatePicker start_date (required), DatePicker end_date, Toggle is_current, Toggle is_featured
- [ ] 4.2 Add `ImageRelationManager` to ProjectResource — manage images (type=screenshot/logo), morphMany to Image, Spanish labels
- [ ] 4.3 Create `app/Filament/Resources/ExperienceResource.php` — fields: company (required), role (required), description (textarea), location, DatePicker start_date (required), DatePicker end_date, Toggle is_current; badge "Experiencia Actual" when is_current
- [ ] 4.4 Implement Experience date validation: end_date must not be before start_date unless is_current
- [ ] 4.5 Write `tests/Feature/ProjectAdminTest.php` — CRUD, image attachment, image type validation
- [ ] 4.6 Write `tests/Feature/ExperienceAdminTest.php` — CRUD, date validation, is_current badge
- [ ] 4.7 **TDD**: per resource — test CRUD → implement → test validation rules → implement

## PR #5: Education + Avatar (~350 lines)

- [x] 5.1 Create `app/Filament/Resources/EducationResource.php` — fields: institution (required), degree (required), field, description (textarea), DatePicker start_date, DatePicker end_date, sort_order (numeric)
- [x] 5.2 Add `ImageRelationManager` to EducationResource — manage certificates (type=certificate), labeled "Certificados" in Spanish
- [ ] 5.3 Update `ManageProfilePage` — integrate FileUpload avatar field saves to storage, displays avatar preview in page header (pendiente — implementación con FileUpload rota, se revirtió a TextInput URL)
- [x] 5.4 Write `tests/Feature/EducationAdminTest.php` — CRUD, certificate image attachment, image type validation
- [x] 5.5 **TDD**: test education CRUD → implement → test image attachment → implement
