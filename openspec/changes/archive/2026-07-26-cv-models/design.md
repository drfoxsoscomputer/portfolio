# Design: cv-models

## Technical Approach

Create a centralized CV data model with 8 related entities (Profile, Link, Project, Experience, Skill, Education, Language, Image) using flat foreign key relationships. The Profile model is singleton-enforced, and Image uses polymorphic morph relationships across Profile, Project, and Education entities. Project tech_stack is stored as a JSON column with array casting.

**Implementation decision**: Follow Approach A from exploration - simple FK relationships with polymorphic Image model. Profile singleton enforced through Laravel observer pattern and seeder logic.

## Architecture Decisions

| Decision | Choice | Alternatives | Rationale |
|----------|--------|--------------|-----------|
| Profile singleton | Observer (ProfileObserver) + seeder logic | Service layer validation only | Observer provides early validation with Laravel model lifecycle; seeder ensures singleton data consistency |
| Image polymorphism | morphMany/morphTo in AppServiceProvider | Interface-based polymorphic columns (FK per entity) | Single `images` table reduces complexity and follows Laravel's polymorphic best practices |
| Tech stack storage | JSON column with array cast | Pivot table with separate entries | Simpler schema, leverages Laravel 13's native JSON casting, easier filtering and querying |
| Factory strategy | for()/afterCreating() pattern | Manual associations | Consistent with Laravel factory patterns, ensures proper relationships |
| Default ordering | Model-level orderBy() methods | Global scopes | Explicit control, easier to override per use case |
| Image type classification | String enum ("avatar", "screenshot", "logo", "certificate") | Database enum | Simpler validation, flexible for future types |
| Migration order | Profile first, then FK relationships | All migrations simultaneously | Profiles must exist before FK references can be created |

## Data Flow

```
app/Models/
├── Profile (singleton)
│   ├── hasMany(Link)
│   ├── hasMany(Project)     ← projects.tech_stack (JSON array cast)
│   ├── hasMany(Experience)
│   ├── hasMany(Skill)
│   ├── hasMany(Education)
│   ├── hasMany(Language)
│   └── morphMany(Image) ← Imageable: Profile
├── Project
│   ├── belongsTo(Profile)
│   └─ morphMany(Image) ← Imageable: Project
├── Education
│   ├── belongsTo(Profile)
│   └─ morphMany(Image) ← Imageable: Education
├── Link
│   ├── belongsTo(Profile)
├── Experience
│   ├── belongsTo(Profile)
├── Skill
│   ├── belongsTo(Profile)
├── Language
│   └── belongsTo(Profile)
└── Image (polymorphic)
    └── morphTo → Profile | Project | Education
```

## File Changes

| File | Action | Description |
|------|--------|-------------|
| `app/Models/Profile.php` | Create | Singleton profile with validation, relationships, #[Fillable]/#[Hidden] attributes |
| `app/Models/Link.php` | Create | Profile links, belongsTo Profile, sort_order ordering |
| `app/Models/Project.php` | Create | Projects with JSON cast tech_stack, belongsTo Profile, default ordering |
| `app/Models/Experience.php` | Create | Experience entries with start_date ordering, belongsTo Profile |
| `app/Models/Skill.php` | Create | Skills with category enum, sort_order, belongsTo Profile |
| `app/Models/Education.php` | Create | Education with morphMany Images, sort_order, belongsTo Profile |
| `app/Models/Language.php` | Create | Languages with sort_order, belongsTo Profile |
| `app/Models/Image.php` | Create | Polymorphic Image model with morphTo, type enum, belongsTo concept |
| `database/migrations/` | Create 8 | Profile first (seed), others with FK references |
| `database/factories/` | Create 8 | Realistic CV data, proper relationships |
| `database/seeders/ProfileSeeder.php` | Create | Singleton profile + all CV data from PDF |
| `app/Providers/AppServiceProvider.php` | Modify | Register Image morph map: `Image::morphTo('imageable')` |
| `tests/Unit/` | Create 8 | Model relationships, validations, singleton, cast tests |

## Interfaces / Contracts

```php
// Image polymorphic interface
class Image extends Model
{
    public function imageable(): MorphTo
    {
        return $this->morphTo('imageable', 'imageable_type', 'imageable_id');
    }
}

// Profile observer singleton enforcement
class ProfileObserver
{
    public function creating(Profile $profile)
    {
        if (Profile::exists()) {
            throw new DuplicateProfileException('Profile already exists');
        }
    }
}

// Entity with polymorphic images interface
interface HasMorphImages
{
    public function morphManyImages(): MorphMany;
}
```

## Testing Strategy

| Layer | What to Test | Approach |
|-------|-------------|----------|
| Unit | Model validation, relationships, singleton enforcement, JSON casting | PHPUnit with TestCase base class |
| Integration | Factory creation and relationship persistence | Database transactions, model factories |
| E2E | Complete CV data seeding and singleton integrity | Feature tests for workflow validation |

## Migration / Rollout

**Migration sequence:**
1. Profile migration runs first (creates the singleton profile)
2. Other migrations run sequentially (referencing existing Profile)

**Error handling:** Observer prevents duplicate profile creation with exception

**Rollback:** `php artisan migrate:reset` drops all 8 tables

## Open Questions

- [ ] Tech stack JSON validation at model vs controller level?
- [ ] Should Profile observer provide custom validation messages?

## Design Created

**Change**: cv-models
**Location**: `openspec/changes/cv-models/design.md`

### Summary
- **Approach**: Flat FK relationships with polymorphic Image model, singleton Profile enforced via Observer
- **Key Decisions**: JSON tech_stack, morphMany/morphTo for images, Observer singleton enforcement
- **Files Affected**: 8 models, 8 migrations, 8 factories, 1 seeder, 1 provider, 8 tests (44 total file changes)
- **Testing Strategy**: Unit tests for relationships and singleton enforcement

### Open Questions
- [ ] Tech stack JSON validation at model vs controller level?
- [ ] Profile observer custom validation messages?

### Next Step
Ready for tasks (sdd-tasks). Design under 800 words, tables for architecture decisions.