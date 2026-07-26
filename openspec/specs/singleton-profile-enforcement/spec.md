# singleton-profile-enforcement Specification

## Purpose

Implement Profile singleton pattern ensuring exactly one CV record per portfolio. This prevents data fragmentation and ensures consistent portfolio identity across all related entities.

## Requirements

### Functional Requirements

#### Requirement: Single Profile Record

The system MUST allow only one Profile record to exist in the entire database at any time.

##### Scenario: Initial Profile creation

- GIVEN the system is empty (no profiles)
- WHEN Profile::create() with complete CV data
- THEN exactly one Profile record is created with all data intact

##### Scenario: Duplicate Profile prevention

- GIVEN a Profile already exists with name "Alice Johnson"
- WHEN attempting to create a second Profile with name "Bob Smith"
- THEN the second creation MUST be prevented with validation error

#### Requirement: Singleton Enforcement Mechanism

The system MUST enforce singleton behavior through multiple layers of protection.

##### Scenario: Profile seeder creates singleton

- GIVEN the ProfileSeeder runs
- WHEN multiple profiles could be seeded from PDF data
- THEN only the first profile is created, others are discarded

##### Scenario: Service layer validation

- GIVEN a service method `ProfileService::createCV()`
- WHEN called twice with different data
- THEN only the first call succeeds, second call fails with error

##### Scenario: Database constraint enforcement

- GIVEN the application layer allows multiple Profile insertions
- WHEN raw SQL or direct database access attempts bypass validation
- THEN database-level uniqueness constraints SHOULD prevent duplicates

### Non-Functional Requirements

#### Requirement: Performance Isolation

The system SHOULD ensure that singleton enforcement does not create performance bottlenecks.

##### Scenario: Profile retrieval performance

- GIVEN a portfolio with many related records
- WHEN retrieving the Profile
- THEN the query SHOULD complete quickly despite enforced singleton constraints

##### Scenario: Singleton check performance

- GIVEN frequent operations that need to verify singleton status
- WHEN running is_singleton() checks
- THEN these checks SHOULD be cached or indexed for performance

#### Requirement: Data Consistency

The system SHOULD maintain data consistency across all services and operations.

##### Scenario: Transaction isolation

- GIVEN a Profile creation transaction
- WHEN concurrent operations attempt Profile creation
- THEN transactions SHOULD be isolated to prevent partial duplicates

## Acceptance Criteria

### Technical Implementation

- [ ] Profile model has singleton enforcement logic
- [ ] ProfileObserver prevents duplicate creation
- [ ] Service layer validates singleton constraints
- [ ] Seeder ensures only one profile is created
- [ ] Database schema supports singleton (optional unique constraint)
- [ ] Validation errors are clear and actionable
- [ ] Performance monitoring exists for singleton operations
- [ ] Transaction rollback works correctly with singleton failures

### Functional Testing

- [ ] Attempts to create Profile when one exists fail
- [ ] Existing Profile cannot be overwritten (update requires special handling)
- [ ] System gracefully handles "create if not exists" scenarios
- [ ] Error messages clearly explain singleton constraint
- [ ] Admin UI prevents duplicate Profile creation
- [ ] API endpoints enforce singleton rules

## Validation Rules

### Profile Singleton Rules

- **Rule 1: Creation Barrier**
  - When no Profile exists → MUST allow creation
  - When Profile exists → MUST prevent creation
  - Exception: System reset/restore scenarios may allow recreation

- **Rule 2: Update Behavior**
  - When exactly one Profile exists → MUST allow updates to existing record
  - When multiple Profiles exist (system error) → SHOULD restrict all operations
  - Migration: Existing records cannot be merged automatically

- **Rule 3: Soft Delete vs Hard Delete**
  - Soft delete (marked deleted) → System SHOULD allow new Profile creation
  - Hard delete (record removed) → System MAY allow new Profile creation
  - Full deletion → Should be handled with caution in production

### Implementation Validation

- **Database Level**
  - Unique constraints MAY be added on profile-specific unique fields
  - Profile status field (active/inactive) for soft singleton management

- **Application Level**
  - Before creation: check count(Profile::all()) == 0
  - After creation: verify only one record exists
  - Race condition handling for concurrent operations

## Edge Cases

### Creation Scenarios

- System initialization with no data
- Seeder runs multiple times
- Manual database seeding
- API mass creation endpoint
- CSV import with multiple profiles

### Operational Scenarios

- Database seed reset and re-seeding
- Profile partial deletion (soft delete)
- Data migration between portfolios
- Testing environment isolation
- Concurrent access from multiple services

### Error Scenarios

- Attempting to create Profile when one exists
- Attempting to delete the last Profile
- Attempting to reset portfolio while Profile exists
- Race condition creating multiple Profiles simultaneously

## Schema / Field Definitions

### Profile Singleton Fields

| Field | Type | Constraints | Purpose |
|-------|------|-------------|---------|
| id | id() | PK | Unique identifier |
| name | string() | Required, UNIQUE | Primary identity (enforces singleton by content) |
| title | string() | Nullable | Professional title |
| is_active | boolean | default true | Singleton operational status |
| created_at | timestamp | | Creation timestamp |
| updated_at | timestamp | | Last update timestamp |

### Singleton Enforcement Indexes

- id (PK)
- name (UNIQUE - alternative to count-based enforcement)
- is_active (index for soft singleton searches)

## Relationship Definitions

### Profile as Root Entity

```
Profile (Singleton)
├── hasMany(Link) through "links" table
├── hasMany(Project) through "projects" table
├── hasMany(Experience) through "experiences" table
├── hasMany(Skill) through "skills" table
├── hasMany(Education) through "education" table
├── hasMany(Language) through "languages" table
└── morphMany(Image) through "images" table
```

### Singleton Relationship Characteristics

- **One-to-Many**: Profile → All other entities (7 different relationships)
- **Uniqueness**: Profile represents the entire portfolio (conceptual uniqueness)
- **Access**: All child entities reference the singleton via foreign key
- **Management**: Singleton requires special handling in admin interfaces

## Singleton Enforcement Strategies

### Application Layer Strategy

1. **Count-Based Enforcement**
   ```php
   class ProfileController {
       public function store(Request $request) {
           if (Profile::count() > 0) {
               return response()->json(['error' => 'Profile already exists'], 409);
           }
           return Profile::create($request->all());
       }
   }
   ```

2. **Service Layer Validation**
   ```php
   class ProfileService {
       public static function createWithSingletonCheck($data) {
           if (self::exists()) {
               throw new SingletonViolationException('Profile already exists');
           }
           return Profile::create($data);
       }
   }
   ```

### Database Level Strategy

1. **Unique Name Constraint**
   ```php
   // In migration
   $table->string('name')->unique();
   ```

2. **Profile Status Singleton**
   ```php
   class Profile extends Model {
       protected static function boot() {
           static::creating(function ($profile) {
               if (static::where('is_active', true)->exists()) {
                   throw new SingletonException('Active profile already exists');
               }
           });
       }
   }
   ```

### Observer Pattern

```php
class ProfileObserver {
    public function creating(Profile $profile) {
        if (Profile::count() > 0) {
            throw new DuplicateProfileException('Profile already exists');
        }
    }
}
```

## Implementation Notes

### Migration Considerations

- No profile_id foreign keys needed (singleton concept)
- Consider adding `is_active` field for soft singleton management
- Unique constraints on name field as alternative to count checking

### Performance Considerations

- Add index on Profile name for fast singleton lookup
- Cache Profile data in Redis for high-performance scenarios
- Implement singleton checks at infrastructure level

### Testing Strategy

1. **Unit Tests**
   - Test singleton creation
   - Test duplicate prevention
   - Test singleton update behavior

2. **Integration Tests**
   - Test with related entities
   - Test transaction scenarios
   - Test race conditions

3. **Edge Cases**
   - Test with empty database
   - Test with existing data
   - Test concurrent operations

## Singleton Best Practices

1. **Clear Documentation**
   - Document singleton constraint in model comments
   - Document admin interface behavior
   - Document API error responses

2. **Graceful Degradation**
   - Provide fallback mechanisms for edge cases
   - Implement clear error messages
   - Log singleton violations for audit

3. **Testing Coverage**
   - Comprehensive singleton enforcement tests
   - Performance testing for singleton operations
   - Failure scenario testing

## Recovery and Maintenance

### Recovery Procedures

1. **Emergency Singleton Reset**
   - Can delete existing Profile under controlled conditions
   - Should backup portfolio data first
   - Requires explicit admin authorization

2. **Migration Considerations**
   - Never merge existing singleton profiles automatically
   - Should use migration with data export/import
   - Document all singleton changes for audit

### Maintenance

1. **Monitoring**
   - Monitor singleton creation attempts
   - Log all singleton enforcement activities
   - Track profile creation/deletion events

2. **Validation**
   - Regular backup of singleton profile data
   - Validation of singleton constraints
   - Testing of singleton enforcement mechanisms

## Success Criteria

### Technical Implementation

- [ ] Profile singleton pattern is enforced
- [ ] Duplicate prevention works reliably
- [ ] Service layer validates singleton constraints
- [ ] Database constraints supplement application validation
- [ ] All child entities reference the singleton correctly
- [ ] System handles edge cases gracefully
- [ ] Performance is maintained with singleton enforcement
- [ ] Testing thoroughly validates singleton behavior

### Operational Readiness

- [ ] Clear error messages for singleton violations
- [ ] Admin interface prevents duplicate creation
- [ ] API endpoints enforce singleton rules
- [ ] Logging and monitoring are in place
- [ ] Recovery procedures are documented
- [ ] Documentation covers singleton behavior
- [ ] Training materials cover singleton constraints
- [ ] Rollback procedures are tested and documented