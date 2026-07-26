# Proposal: CV Data Models

## Intent
Create the data layer for the personal portfolio — models, migrations, factories, seeders, and unit tests to represent the user's CV data: Profile, Project, Experience, Skill, Education, Language, Link, and polymorphic Image.

## Scope

### In Scope
- 8 Eloquent models with relationships and default ordering
- 8 database migrations
- 8 model factories with realistic CV data
- 1 ProfileSeeder with all CV data from the extracted PDF
- 8 unit tests (model relationships, casts, scopes)
- Morph map registration in AppServiceProvider for Image polymorphic relationship

### Out of Scope
- UI, controllers, routes, views (admin panel, landing page)
- Image upload handling (storage driver, disk config)
- Filament admin panel integration
- Frontend display / portfolio pages
- PDF download / ATS export
- Auth / user accounts

## Capabilities

### New Capabilities
- cv-data-models: Centralized CV/personal portfolio data model with 8 related entities and polymorphic image management
- singleton-profile-enforcement: Profile singleton pattern ensuring one CV record per portfolio
- polymorphic-images: Flexible image attachment across Profile, Project, and Education entities
- tech-stack-json: JSON casting for Project technology stack storage

### Modified Capabilities
- none: No existing capabilities are being modified

## Approach
Flat/FK relationships with singleton Profile and polymorphic Images. Simple foreign keys from all child entities to Profile (7 FKs). Image uses polymorphic morphMany/morphTo to attach to Profile, Project, Education. Tech_stack on Project cast as array. Skill categories as plain strings. Profile is singleton enforced via seeder/observer.

**Ordering:** Project and Experience ordered by `start_date` DESC (newest first). Education, Link, Skill, Language, and Image use `sort_order` for custom drag-and-drop reordering in Filament admin.

## Affected Areas

| Area | Impact | Description |
|------|--------|-------------|
| app/Models/ | New | 8 model files: Profile, Link, Project, Experience, Skill, Education, Language, Image |
| database/migrations/ | New | 8 migration files for each model with fields matching spec |
| database/factories/ | New | 8 factory files for realistic CV data generation |
| database/seeders/ | Modified | DatabaseSeeder updated to call ProfileSeeder |
| tests/Unit/ | New | 8 unit test files for model relationships, casts, scopes |
| app/Providers/AppServiceProvider | Modified | Morph map registration for Image polymorphic relationship |

## Risks

| Risk | Likelihood | Mitigation |
|------|------------|------------|
| Profile singleton leak — accidental second Profile record | Medium | Service layer validation + ProfileObserver to prevent multiple profiles |
| SQLite JSON support issues with tech_stack column | Low | Laravel 13 supports JSON columns, SQLite >=3.38.0 (current) is well-tested |
| Polymorphic morph map forgotten | Low | Explicit AppServiceProvider registration documented in migration notes |
| Date type limited precision for CV history | Low | Using `date` type is appropriate; migration exists to upgrade if time precision needed |
| No existing tables modified — risk of conflicts | None | All new tables, zero conflict with Laravel defaults |

## Rollback Plan
If deployment fails:
1. Drop all new tables (8 migrations) with `php artisan migrate:reset`
2. Remove any added code (models, factories, seeders, tests, provider changes)
3. Restore original state (only Laravel's default 3 tables remain)
4. No data loss as no user data was added

## Dependencies
- SQLite JSON support for tech_stack casting
- PHP 8.3+ with Laravel 13 attributes ($fillable/$hidden removed in favor of #[Fillable]#[Hidden])

## Success Criteria

- [ ] All 8 migrations run without errors
- [ ] `php artisan db:seed` creates Profile with all CV data
- [ ] All 8 model relationships work correctly (tests pass)
- [ ] Image polymorphic relationship works across Profile, Project, Education
- [ ] `php artisan test` passes all existing and new tests