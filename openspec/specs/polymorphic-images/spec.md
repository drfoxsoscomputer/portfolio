# polymorphic-images Specification

## Purpose

Implement polymorphic image attachment system allowing images to be attached to Profile, Project, and Education entities. This enables flexible media management across different CV content types (avatar, screenshots, logos, certificates) with a unified storage and relationship structure.

## Requirements

### Functional Requirements

#### Requirement: Polymorphic Image Model

The system MUST provide a polymorphic Image model that can attach to multiple entity types.

##### Scenario: Image model definition

- GIVEN an Image model is created
- WHEN Image::create() with valid data
- THEN the image can be attached to Profile, Project, or Education entities

##### Scenario: Morph map registration

- GIVEN the polymorphic Image model
- WHEN AppServiceProvider is configured
- THEN morph map registration allows `$image->imageable()` to work correctly

#### Requirement: Entity-Specific Image Attachments

The system MUST support image attachments for Profile (avatar), Project (screenshots/logos), and Education (certificates).

##### Scenario: Profile images

- GIVEN a Profile exists
- WHEN Image::create() with imageable_type="Profile"
- THEN `$profile->morphManyImages` returns the attached images

##### Scenario: Project images

- GIVEN a Project exists
- WHEN Image::create() with imageable_type="Project"
- THEN `$project->morphManyImages` returns the attached images

##### Scenario: Education images

- GIVEN an Education record exists
- WHEN Image::create() with imageable_type="Education"
- THEN `$education->morphManyImages` returns the attached images

#### Requirement: Image Type Classification

The system MUST classify images by type for UI/behavioral differentiation.

##### Scenario: Avatar images

- GIVEN an image with type="avatar"
- WHEN accessed through Profile
- THEN the image should be treated as profile picture

##### Scenario: Screenshot images

- GIVEN an image with type="screenshot"
- WHEN accessed through Project
- THEN the image should be treated as project demonstration

##### Scenario: Logo images

- GIVEN an image with type="logo"
- WHEN accessed through Project
- THEN the image should be treated as brand/logo

##### Scenario: Certificate images

- GIVEN an image with type="certificate"
- WHEN accessed through Education
- THEN the image should be treated as credential proof

### Non-Functional Requirements

#### Requirement: Morph Map Reliability

The system SHOULD ensure morph map registration is robust and discoverable.

##### Scenario: Morph map validation

- GIVEN the AppServiceProvider
- WHEN morph map is inspected
- THEN it SHOULD contain all three entity types (Profile, Project, Education)

##### Scenario: Morph map persistence

- GIVEN the application is deployed
- WHEN the morph map configuration is reloaded
- THEN the configuration SHOULD persist across restarts

#### Requirement: Image Query Performance

The system SHOULD provide efficient querying for polymorphic relationships.

##### Scenario: Profile image queries

- GIVEN a Profile with multiple images
- WHEN querying `$profile->images()->where('type', 'avatar')`
- THEN results SHOULD be returned quickly with proper indexing

##### Scenario: Cross-entity image queries

- GIVEN multiple entity types with images
- WHEN searching for all 'screenshot' images
- THEN the query SHOULD efficiently filter across entity types

## Acceptance Criteria

### Technical Implementation

- [ ] Image model has morphMany/morphTo relationships
- [ ] AppServiceProvider morph map is correctly configured
- [ ] All three entity types (Profile, Project, Education) support polymorphic images
- [ ] Image type classification works correctly
- [ ] Database schema supports polymorphic relationships
- [ ] Image factory attaches to multiple entity types
- [ ] Query performance is acceptable for image retrieval
- [ ] Migration correctly adds polymorphic columns

### Functional Testing

- [ ] Images can be attached to all supported entity types
- [ ] `$image->imageable()` returns correct parent entity
- [ ] `$entity->morphManyImages` returns attached images
- [ ] Image type filtering works correctly
- [ ] Morph map registration is validated
- [ ] Cross-entity image queries work
- [ ] CRUD operations work for polymorphic images
- [ ] Soft deletion of parent entities handles child images correctly

## Validation Rules

### Image Model Validation Rules

- **imageable_id**: Must exist in the referenced table
- **imageable_type**: Must be one of: "Profile", "Project", "Education"
- **url**: Must be valid URL/path
- **type**: Must be one of: "avatar", "screenshot", "logo", "certificate" (nullable)
- **sort_order**: Must be unsigned integer, default 0

### Polymorphic Relationship Rules

- **Rule 1: Morph Map Completeness**
  - Profile, Project, Education must be registered
  - Morph map should be discoverable via `Image::getMorphedModels()`
  - Each entity should be accessible via morphTo

- **Rule 2: Type Consistency**
  - imageable_type must match registered morph model
  - imageable_id must be valid foreign key in referenced table
  - Type and ID must be referenced together (cannot exist separately)

- **Rule 3: Image Classification**
  - type field is optional but useful for UI/behavior
  - Type should be validated against allowed enum
  - Type should be searchable for filtering

### Morph Map Registration Rules

1. **In AppServiceProvider**
   ```php
   protected function registerMorphMap(): void {
       Image::morphTo('imageable', 'imageable_type', 'imageable_id');
   }
   ```

2. **Entity Registration**
   - Profile must be registered as 'profile'
   - Project must be registered as 'project'
   - Education must be registered as 'education'

3. **Validation**
   - Morph map should be accessible for inspection
   - All registered models should be valid Eloquent models

## Edge Cases

### Morph Map Edge Cases

- Morph map registration missing (automation failure)
- Unregistered model with valid imageable_type (should fail validation)
- Morph map corrupted during deployment
- Multiple morph map registrations (duplicate)

### Image Attachment Edge Cases

- Image attached to non-existent entity (foreign key constraint)
- imageable_type registered but entity deleted (orphaned image)
- imageable_id valid but type mismatched
- Multiple images with same details (should be allowed)

### Query Performance Edge Cases

- Large number of images across multiple entities
- Complex queries filtering by type and entity
- Race conditions creating images for same entity
- Pagination across all images

### Storage Edge Cases

- Invalid image URLs (external links)
- Large image files exceeding storage limits
- Corrupted image metadata
- Unicode characters in image paths

## Schema / Field Definitions

### Images Table Schema

| Field | Type | Constraints | Purpose |
|-------|------|-------------|---------|
| id | id() | PK | Unique identifier |
| imageable_id | foreignId() | FK, required | Target entity ID |
| imageable_type | string() | required, max 255 | Target entity class |
| url | string() | required | Image path/URL |
| alt_text | string() | nullable | Accessibility text |
| type | string() | nullable, enum | "avatar", "screenshot", "logo", "certificate" |
| sort_order | unsignedInteger() | default 0 | Sorting order |
| created_at | timestamp | | Creation timestamp |
| updated_at | timestamp | | Last update timestamp |

### Morph Map Registration

**In AppServiceProvider::boot()**:

```php
protected function boot(): void {
    // Other boot configurations...
    
    // Register polymorphic relationships for Image model
    Image::morphTo('imageable', 'imageable_type', 'imageable_id');
    
    // Additional morph map registration for other polymorphic models if needed
}
```

### Index Strategy

- id (PK)
- imageable_type + imageable_id (composite index for morph queries)
- type (index for image type filtering)
- sort_order (index for default ordering)

## Relationship Definitions

### Image Polymorphic Relationships

```
Image (Polymorphic)
└── morphTo → Profile | Project | Education
```

### Target Entity Relationships

```
Profile
└── morphMany → Image (as imageable)

Project
└── morphMany → Image (as imageable)

Education
└º morphMany → Image (as imageable)
```

### Key Relationship Methods

```php
// From Image model
public function imageable() {
    return $this->morphTo('imageable', 'imageable_type', 'imageable_id');
}

// From entity models
public function morphManyImages() {
    return $this->morphMany(Image::class, 'imageable');
}
```

## Implementation Details

### Morph Map Registration

1. **Registration Location**
   - Must be in AppServiceProvider::boot()
   - Should be documented in migration comments
   - Should be test-covered

2. **Registration Format**
   ```php
   Image::morphTo('imageable', 'imageable_type', 'imageable_id');
   ```

3. **Validation**
   - Should validate morph map is complete
   - Should ensure all models exist
   - Should test entity retrieval

### Image Model Implementation

1. **Model Relationships**
   ```php
   class Image extends Model {
       public function imageable() {
           return $this->morphTo('imageable', 'imageable_type', 'imageable_id');
       }
   }
   ```

2. **Entity Model Extensions**
   ```php
   class Profile extends Model {
       public function morphManyImages() {
           return $this->morphMany(Image::class, 'imageable');
       }
   }
   ```

### Entity-Specific Access Patterns

1. **Through Imageable**
   ```php
   // From Image
   $image->imageable; // Returns Profile | Project | Education
   ```

2. **From Entity**
   ```php
   // From Profile
   $profile->morphManyImages(); // Returns collection of images for this profile
   
   // From Project
   $project->morphManyImages(); // Returns collection of images for this project
   
   // From Education
   $education->morphManyImages(); // Returns collection of images for this education
   ```

### Query Patterns

1. **Entity Image Query**
   ```php
   // Get all images for a specific entity
   $images = $imageable->morphManyImages()->get();
   
   // Get images by type
   $avatarImages = $imageable->morphManyImages()
       ->where('type', 'avatar')
       ->get();
   ```

2. **Cross-Entity Image Query**
   ```php
   // Get all avatar images across all entities
   $allAvatars = Image::where('type', 'avatar')
       ->whereIn('imageable_type', ['Profile', 'Project', 'Education'])
       ->get();
   ```

## Testing Strategy

### Unit Tests

1. **Morph Map Tests**
   - Test morph map registration completeness
   - Test morph map accessibility
   - Test morph map validation

2. **Image Model Tests**
   - Test image creation and attachment
   - Test morphTo relationship
   - Test image validation

3. **Entity Image Tests**
   - Test profile image attachment
   - Test project image attachment
   - Test education image attachment

### Integration Tests

1. **Polymorphic CRUD**
   - Test creating images for all entity types
   - Test reading images through entities
   - Test updating image properties
   - Test deleting images with entities

2. **Query Performance Tests**
   - Test morph query performance
   - Test cross-entity queries
   - Test image type filtering

3. **Edge Case Tests**
   - Test orphaned images
   - Test morph map corruption
   - Test image with invalid entity type

## Performance Considerations

### Query Optimization

1. **Indexing**
   - Composite index on imageable_type + imageable_id
   - Index on imageable_type for entity-specific queries
   - Index on type for image type filtering

2. **Eager Loading**
   ```php
   // Eager load images with entities
   $images = Image::with('imageable')
       ->where('type', 'avatar')
       ->get();
   ```

3. **Query Caching**
   - Cache frequently used image collections
   - Cache morph map lookups
   - Cache entity-image relationships

### Memory Considerations

- Profile images (avatars): Small, frequent access
- Project images (screenshots/logos): Medium size, moderate access
- Education images (certificates): Variable size, occasional access
- Cross-entity queries: Consider pagination and lazy loading

## Security Considerations

### Access Control

- Images attached to sensitive entities (Profile, Education) should be access-controlled
- Type-based authorization (avatars vs certificates)
- Image URL validation (prevent external injection)

### Data Validation

- Validate imageable_type against allowed values
- Validate imageable_id exists in referenced table
- Validate URL format and content type
- Sanitize alt_text for XSS prevention

## Error Handling

### Common Errors

1. **Foreign Key Violation**
   ```
   Error: Cannot add or update a child row: a foreign key constraint fails
   Solution: Ensure target entity exists before attaching image
   ```

2. **Morph Type Validation**
   ```
   Error: Invalid morph type provided
   Solution: Validate imageable_type against registered models
   ```

3. **Image Upload Issues**
   ```
   Error: File size exceeds limit or invalid file type
   Solution: Validate image properties before saving
   ```

## Recovery Procedures

### Morph Map Issues

- If morph map is corrupted, reinstall AppServiceProvider
- Test morph map registration after deployment
- Keep morph map backup in version control

### Image Attachment Issues

- If image cannot be attached, check entity existence
- If morph relationship fails, validate morph map registration
- Test image attachment with each entity type

### Data Loss

- Always backup database before major morph operations
- Test image migration scenarios
- Implement soft delete for critical images

## Success Criteria

### Technical Implementation

- [ ] Morph map registration is correct and complete
- [ ] Image model has proper morph relationships
- [ ] All three entity types support polymorphic images
- [ ] Image type classification works
- [ ] Database schema supports polymorphic structure
- [ ] Performance is acceptable for image operations
- [ ] Error handling is robust
- [ ] Security measures protect image access

### Functional Testing

- [ ] Images can be attached to all entity types
- [ ] Entity can retrieve attached images
- [ ] Image can resolve to correct parent entity
- [ ] Type-based filtering works
- [ ] Cross-entity queries function correctly
- [ ] Morph map registration is validated
- [ ] CRUD operations work for polymorphic images
- [ ] Soft deletion scenarios are handled

### Operational Readiness

- [ ] Morph map configuration is documented
- [ ] Image attachment rules are clearly defined
- [ ] Query performance is monitored
- [ ] Recovery procedures are tested
- [ ] Error messages are user-friendly
- [ ] Logging captures image operations
- [ ] Security measures protect image access
- [ ] Documentation covers polymorphic images