# Exploration: Admin Panel for Portfolio

**Date**: 2026-07-26
**Status**: Complete
**Prepared for**: SDD Proposal Phase

---

## Current State

8 Eloquent models exist in `app/Models/` with full Factory + migration + test coverage. Profile is singleton-enforced via ProfileObserver. All child entities (Link, Project, Experience, Skill, Education, Language, Image) belong to Profile via `belongsTo` (or `morphTo` for Image). Filament is **not installed yet** — fresh Laravel 13.22.0 project on PHP 8.3.31, SQLite database.

### Verified Model Fields (actual code vs task description)

| Model | Actual Fields | Task Description Differences |
|-------|--------------|----------------------------|
| **Profile** | name, title, location, phone, email, summary, avatar | No `avatar_url`, `resume_url`, or `social_links` JSON |
| **Link** | label, url, icon (nullable), sort_order | Matches |
| **Project** | name, description, tech_stack (JSON→array), role, team_size, url, repo_url, start_date, end_date, is_current, is_featured | Has `name` not `title`; has `role`, `team_size`, `is_current` extra; no `github_url` (it's `repo_url`); no `sort_order` |
| **Experience** | company, role, description, location, start_date, end_date, is_current | Has `role` not `position`; no `sort_order` |
| **Skill** | name, category, sort_order | Matches |
| **Education** | institution, degree, field, description, start_date, end_date, is_current, sort_order | Has `field` not `field_of_study` |
| **Language** | name, level (string, nullable), sort_order | Has `level` not `proficiency` (not enum, just string) |
| **Image** | url, alt_text, type, sort_order. Polymorphic `morphTo imageable` | Has `type` not `is_primary` |

### Relationship Summary

```
Profile (singleton via Observer)
├── hasMany → Link (sort_order ASC)
├── hasMany → Project (start_date DESC)
├── hasMany → Experience (start_date DESC)
├── hasMany → Skill (sort_order ASC)
├── hasMany → Education (sort_order ASC)
├── hasMany → Language (sort_order ASC)
└── morphMany → Image (sort_order ASC)

Project → morphMany → Image
Education → morphMany → Image
```

### Test Coverage (8 Unit Tests, all pass)

| Test | Key Validations |
|------|----------------|
| ProfileTest | Creation, singleton enforcement, fillable attrs, relationships, factory |
| LinkTest | Creation, belongsTo, fillable, ordering (sort_order ASC), factory |
| ProjectTest | Creation, belongsTo, fillable, ordering (start_date DESC), tech_stack casting, images rel |
| ExperienceTest | Creation, belongsTo, fillable, ordering (start_date DESC), factory |
| SkillTest | Creation, belongsTo, fillable, ordering (sort_order ASC), category validation |
| EducationTest | Creation, belongsTo, fillable, ordering (sort_order ASC), images rel |
| LanguageTest | Creation, belongsTo, fillable, ordering (sort_order ASC), level validation |
| ImageTest | Creation, morphTo (3 types), fillable, ordering (sort_order ASC), type validation |

Uses `RefreshDatabase` trait, `sqlite::memory:` in tests.

---

## Affected Areas

| Path | What Changes |
|------|-------------|
| `composer.json` | Add `filament/filament:"^5.0"` dependency |
| `app/Providers/Filament/AdminPanelProvider.php` | **New** — Panel config at `/admin`, brand, navigation groups |
| `bootstrap/providers.php` | Register AdminPanelProvider |
| `app/Filament/Pages/ManageProfile.php` | **New** — Singular page for Profile editing |
| `app/Filament/Resources/LinkResource.php` | **New** — With pages (List/Create/Edit) |
| `app/Filament/Resources/ProjectResource.php` | **New** — With pages |
| `app/Filament/Resources/ExperienceResource.php` | **New** — With pages |
| `app/Filament/Resources/SkillResource.php` | **New** — With pages |
| `app/Filament/Resources/EducationResource.php` | **New** — With pages |
| `app/Filament/Resources/LanguageResource.php` | **New** — With pages |
| `app/Filament/Resources/ImageResource.php` | **New** — With pages |
| `resources/css/filapp/admin/theme.css` | **New** — Filament theme CSS |
| `vite.config.js` | Modified — Add Filament CSS entry |
| `package.json` | Modified — Add Tailwind v4 + Filament dependencies |
| `database/seeders/DatabaseSeeder.php` | Add admin user creation |
| `config/filament.php` | Panel branding, timezone, locale |
| `tests/Feature/Filament/AdminPanelTest.php` | **New** — Panel loads, auth works |
| `tests/Feature/Filament/<Resource>Test.php` | **New** — CRUD tests per resource (7 files) |

---

## Approaches

### Option A: All Standard Resources (Profile as singular page)

Profile managed via custom Filament page (singular record pattern — no index, no table). All 7 children as standard Resources with full CRUD. Navigation grouped by section.

**Pros:**
- Each entity independently manageable
- Follows standard Filament conventions
- Clean separation of concerns
- Easy to test individually
- Easy to split into chained PRs

**Cons:**
- 8 navigation items (though minimal with proper grouping)
- Profile needs non-standard page implementation

**Effort:** Medium

---

### Option B: Profile Hub with RelationManagers

Profile as custom singular page with inline RelationManagers for all 7 children (links, projects, experiences, skills, educations, languages, images). Single navigation item "Profile".

**Pros:**
- Everything in one place
- Only 1 navigation item
- Profile's hasMany relationships map perfectly to RelationManagers

**Cons:**
- One massive page with 7 tables — overwhelming UX
- Hard to test each entity independently via Filament
- Violates single-responsibility principle for the Profile page
- Can't split into independent PRs easily (all tied to one page)

**Effort:** Medium-High

---

### Option C: Mixed — Singular Profile + Standard Children Resources (Recommended)

Profile as custom singular page. All 7 children as standard Resources. Navigation organized into 3 logical groups.

**Navigation Structure:**
```
About Me
  └── Profile

Portfolio
  ├── Projects
  ├── Experience
  ├── Skills
  └── Education

Social & Media
  ├── Links
  ├── Languages
  └── Images
```

**Pros:**
- Best UX balance — not too few, not too many items
- Each entity independently CRUDable and testable
- Clean navigation grouping
- Fits 400-line PR budget naturally
- Follows Filament conventions for 7/8 components

**Cons:**
- More navigation items than Option B

**Effort:** Medium

---

## Recommendation

**Option C (Mixed approach)** is recommended. Profile gets a custom singular page (per Filament's documented pattern for singleton records). All children get standard Resources with Filament's auto-generated pages.

Key technical decisions:
- Profile avatar: Use Filament's `SpatieMediaLibraryPlugin` or simple `FileUpload` → `storage/app/public/`
- Project tech_stack: `TagsInput` component (tags mode) for JSON array editing
- Image polymorphic: `Select` with fixed options `[Profile, Project, Education]` for imageable_type
- Language level: `Select` with predefined options `[Beginner, Intermediate, Advanced, Fluent, Native]`
- Sort order: `TextInput` with `numeric()` + `defaultValue(0)`, sort by model's `newQuery()` default
- Navigation icons: `heroicon-o-user`, `heroicon-o-briefcase`, `heroicon-o-cog`, etc.

---

## Filament v5 Compatibility

| Concern | Status |
|---------|--------|
| Filament latest | v5.7.3 (2026-07-22) |
| Laravel 13 support | Added in v5.7.0 (PR #20023 by pranab-acharya) |
| PHP 8.3 | Supported (requires PHP 8.2+) |
| SQLite | Supported |
| Tailwind CSS | Requires v4.1+ (fresh Laravel 13 uses v4) |
| Windows install | Use `~5.0` instead of `^5.0` in PowerShell |

**Install command**: `composer require filament/filament:"~5.0"` (PowerShell) then `php artisan filament:install --panels`

---

## Chained PR Breakdown (feature-branch-chain, 400-line budget)

### PR 1: Foundation (~250-300 changed lines)
**Branch**: `feature/admin-panel` (tracker branch from `develop`)

```
pr/1-foundation
  ├── composer.json                    (+1 line)
  ├── app/Providers/Filament/AdminPanelProvider.php  (+80 lines)
  ├── bootstrap/providers.php          (+1 line)
  ├── database/seeders/DatabaseSeeder.php  (+10 lines)
  ├── resources/css/filament/admin/theme.css  (+20 lines)
  ├── package.json                     (+10 lines)
  ├── vite.config.js                   (+5 lines)
  └── tests/Feature/Filament/AdminPanelTest.php  (+80 lines)
```

**Deliverables**: Panel at `/admin` loads, user can log in, navigation structure defined (empty), tests pass.

### PR 2: Profile Singular Page (~250-350 lines)
**Branch**: `pr/2-profile-page` (targets `feature/admin-panel`)

```
pr/2-profile-page
  ├── app/Filament/Pages/ManageProfile.php  (+150 lines)
  ├── resources/views/filament/pages/manage-profile.blade.php  (+30 lines)
  ├── config/filament.php                  (+10 lines brand)
  └── tests/Feature/Filament/ProfilePageTest.php  (+120 lines)
```

**Deliverables**: Profile page at `/admin/profile` with form fields, singleton handling (create if not exists, edit if exists), validation, success notification, tests pass.

### PR 3: Lightweight Resources — Links + Skills + Languages (~350-400 lines)
**Branch**: `pr/3-lightweight-resources` (targets `feature/admin-panel`)

```
pr/3-lightweight-resources
  ├── app/Filament/Resources/LinkResource.php  (+80 lines)
  ├── app/Filament/Resources/LinkResource/Pages/ListLinks.php  (+50 lines)
  ├── app/Filament/Resources/LinkResource/Pages/CreateLink.php  (+40 lines)
  ├── app/Filament/Resources/LinkResource/Pages/EditLink.php  (+50 lines)
  ├── app/Filament/Resources/SkillResource.php  (+80 lines)
  ├── app/Filament/Resources/SkillResource/Pages/ListSkills.php  (+50 lines)
  ├── app/Filament/Resources/SkillResource/Pages/CreateSkill.php  (+40 lines)
  ├── app/Filament/Resources/SkillResource/Pages/EditSkill.php  (+50 lines)
  ├── app/Filament/Resources/LanguageResource.php  (+80 lines)
  ├── app/Filament/Resources/LanguageResource/Pages/ListLanguages.php  (+50 lines)
  ├── app/Filament/Resources/LanguageResource/Pages/CreateLanguage.php  (+40 lines)
  ├── app/Filament/Resources/LanguageResource/Pages/EditLanguage.php  (+50 lines)
  ├── tests/Feature/Filament/LinkResourceTest.php  (+80 lines)
  ├── tests/Feature/Filament/SkillResourceTest.php  (+80 lines)
  └── tests/Feature/Filament/LanguageResourceTest.php  (+80 lines)
```

**Deliverables**: 3 Resources with CRUD, sort_order support, category/level filters, tests pass.

### PR 4: Content Resources — Projects + Experience (~350-400 lines)
**Branch**: `pr/4-content-resources` (targets `feature/admin-panel`)

```
pr/4-content-resources
  ├── app/Filament/Resources/ProjectResource.php  (+100 lines)
  ├── app/Filament/Resources/ProjectResource/Pages/ListProjects.php  (+60 lines)
  ├── app/Filament/Resources/ProjectResource/Pages/CreateProject.php  (+50 lines)
  ├── app/Filament/Resources/ProjectResource/Pages/EditProject.php  (+60 lines)
  ├── app/Filament/Resources/ExperienceResource.php  (+90 lines)
  ├── app/Filament/Resources/ExperienceResource/Pages/ListExperiences.php  (+50 lines)
  ├── app/Filament/Resources/ExperienceResource/Pages/CreateExperience.php  (+40 lines)
  ├── app/Filament/Resources/ExperienceResource/Pages/EditExperience.php  (+50 lines)
  ├── tests/Feature/Filament/ProjectResourceTest.php  (+100 lines)
  └── tests/Feature/Filament/ExperienceResourceTest.php  (+80 lines)
```

**Deliverables**: Project (with TagsInput for tech_stack, date pickers for start/end dates), Experience (with is_current toggle, date pickers), tests pass.

### PR 5: Education + Image Resources (~300-350 lines)
**Branch**: `pr/5-remaining-resources` (targets `feature/admin-panel`)

```
pr/5-remaining-resources
  ├── app/Filament/Resources/EducationResource.php  (+90 lines)
  ├── app/Filament/Resources/EducationResource/Pages/ListEducation.php  (+50 lines)
  ├── app/Filament/Resources/EducationResource/Pages/CreateEducation.php  (+40 lines)
  ├── app/Filament/Resources/EducationResource/Pages/EditEducation.php  (+50 lines)
  ├── app/Filament/Resources/ImageResource.php  (+90 lines)
  ├── app/Filament/Resources/ImageResource/Pages/ListImages.php  (+50 lines)
  ├── app/Filament/Resources/ImageResource/Pages/CreateImage.php  (+40 lines)
  ├── app/Filament/Resources/ImageResource/Pages/EditImage.php  (+50 lines)
  ├── tests/Feature/Filament/EducationResourceTest.php  (+80 lines)
  └── tests/Feature/Filament/ImageResourceTest.php  (+80 lines)
```

**Deliverables**: Education (with sort_order), Image (polymorphic, select imageable type from Profile/Project/Education), navigation groups finalized, all tests pass.

### Final Merge
Merge `feature/admin-panel` → `develop`, then `develop` → `main`.

---

## Key Decisions for Proposal

1. **Profile avatar storage**: Local `public` disk or Spatie Media Library? Recommendation: Simple `FileUpload` → `public` disk for v1.
2. **Tech stack editing**: `TagsInput` component or `Repeater` with `TextInput`? Recommendation: `TagsInput` (simpler, fits JSON array).
3. **Image polymorphic scope**: Allow Image attachment to Profile, Project, Education only? Recommendation: Yes, restrict via `Select` options or a custom `GetImageableModels` helper.
4. **Language level enum**: Current `level` is free-text string. Convert to enum or keep as Select? Recommendation: Keep `Select` with hardcoded options for now (no DB migration needed).
5. **Navigation icon set**: Use heroicons throughout (standard Filament).
6. **Admin user convention**: `admin@example.com` with generated password shown in seeder output.

---

## Risks

| Risk | Impact | Mitigation |
|------|--------|------------|
| Field names differ from task description | Medium | Verified actual models vs spec — use real fields |
| ProfileObserver blocks admin page save | High | Admin page must use `firstOrCreate` → `update` pattern, not `create` |
| Tailwind v4 upgrade issues | Medium | Fresh Laravel 13 should be compatible; verify after `filament:install --panels` |
| 400-line PR budget | Low | Each PR planned under budget; may tighten if Resources use more boilerplate |
| Image polymorphic handling in Filament | Medium | Use `Select` for imageable_type, standard RelationManager works with morphMany |
