# cv-data-models Specification

## Purpose

Create the centralized CV/personal portfolio data model with 8 related entities (Profile, Link, Project, Experience, Skill, Education, Language, Image) and their polymorphic relationships. This provides a complete data structure for storing professional CV information with flexible image attachments and JSON-stored technology stacks.

## Requirements

### Functional Requirements

#### Requirement: Centralized CV Model Structure

The system MUST provide a complete CV data model with 8 related entities to store personal portfolio information.

##### Scenario: Core entity structure exists

- GIVEN an empty Laravel application with SQLite database
- WHEN the cv-models change is applied
- THEN the following entities exist: Profile, Link, Project, Experience, Skill, Education, Language, Image

#### Requirement: Profile Singleton Pattern

The system MUST enforce that only one Profile record exists per portfolio.

##### Scenario: Single profile creation

- GIVEN the system is initialized
- WHEN Profile::create() with name "John Doe"
- THEN exactly one Profile record exists

##### Scenario: Profile duplicate prevention

- GIVEN a Profile already exists with name "Jane Smith"
- WHEN Profile::create() attempts to add another record
- THEN the second record creation SHOULD FAIL or be prevented

#### Requirement: Polymorphic Image Attachments

The system MUST support flexible image attachment across Profile, Project, and Education entities.

##### Scenario: Profile images can be attached

- GIVEN a Profile exists
- WHEN an Image is created with imageable_type="Profile" and imageable_id matching the Profile
- THEN the Image can be accessed via `$profile->morphManyImages`

##### Scenario: Project images can be attached

- GIVEN a Project exists
- WHEN an Image is created with imageable_type="Project" and imageable_id matching the Project
- THEN the Image can be accessed via `$project->morphManyImages`

#### Requirement: JSON Tech Stack Storage

The system MUST provide JSON casting for Project technology stack storage.

##### Scenario: Tech stack as JSON array

- GIVEN a Project is created with tech_stack ["PHP", "Laravel", "Vue"]
- WHEN the project data is retrieved
- THEN the tech_stack property MUST be an array containing ["PHP", "Laravel", "Vue"]

##### Scenario: Null tech stack handled

- GIVEN a Project is created without tech_stack
- WHEN the project data is retrieved
- THEN the tech_stack property SHOULD be null

### Non-Functional Requirements

#### Requirement: Data Integrity Constraints

The system MUST enforce field-level validation rules for all model entities.

##### Scenario: Required fields validation

- GIVEN a Project with required fields (name, profile_id)
- WHEN Project::create() is called
- THEN the project MUST be created successfully

##### Scenario: Missing required fields

- GIVEN a Project without name or profile_id
- WHEN Project::create() is called
- THEN the creation MUST fail with validation error

#### Requirement: Performance Considerations

The system SHOULD maintain reasonable query performance for common use cases.

##### Scenario: Profile relationship queries

- GIVEN a Profile with relationships to Projects and Skills
- WHEN accessing `$profile->projects` and `$profile->skills`
- THEN these queries SHOULD return results efficiently for large CV datasets

##### Scenario: Search capabilities

- GIVEN multiple Skills with category filter
- WHEN filtering by category
- THEN the query SHOULD return matching skills quickly

## Acceptance Criteria

### For cv-data-models

- [ ] All 8 model files exist with proper relationships
- [ ] All 8 migration files can be run successfully
- [ ] Model factories generate realistic CV data
- [ ] Model relationship tests pass for all pairs
- [ ] Profile singleton pattern is enforced
- [ ] Polymorphic images work across three entity types
- [ ] JSON casting for tech_stack returns proper array data
- [ ] All field validations trigger on invalid data

### For singleton-profile-enforcement

- [ ] Exactly one Profile record can exist in database
- [ ] Attempts to create second Profile are blocked
- [ ] Profile observer prevents duplicate creation
- [ ] Service layer validation enforces singleton
- [ ] Database constraints supplement application validation

### For polymorphic-images

- [ ] Image model morphs to Profile, Project, Education
- [ ] Morph map is registered in AppServiceProvider
- [ ] Images can be attached to all three entity types
- [ ] Image relationships work via morphTo/morphMany
- [ ] Image factory attaches to random entity types
- [ ] Database schema supports polymorphic relationships

### For tech-stack-json

- [ ] Project model has tech_stack json column
- [ ] tech_stack casts to array automatically
- [ ] Null tech_stack values are preserved
- [ ] JSON casting works with SQLite backend
- [ ] tech_stack validates as appropriate JSON data type
- [ ] Array operations can be performed on tech_stack

## Validation Rules

### Profile

- name: string, required, max 255 characters
- title: string, nullable, max 255 characters
- location: string, nullable, max 255 characters
- phone: string, nullable, max 50 characters
- email: string, nullable, email format
- summary: text, nullable
- avatar: string, nullable (image path/URL)

### Link

- profile_id: foreign key, required, references profiles.id
- label: string, required, max 100 characters
- url: string, required, URL format
- icon: string, nullable
- sort_order: unsigned integer, default 0

### Project

- profile_id: foreign key, required, references profiles.id
- name: string, required, max 255 characters
- description: text, nullable
- tech_stack: json, nullable (cast to array)
- role: string, nullable
- team_size: unsigned tiny integer, nullable
- url: string, nullable
- repo_url: string, nullable
- start_date: date, nullable
- end_date: date, nullable
- is_current: boolean, default false
- is_featured: boolean, default false

### Experience

- profile_id: foreign key, required, references profiles.id
- company: string, required, max 255 characters
- role: string, required
- description: text, nullable
- location: string, nullable
- start_date: date, required
- end_date: date, nullable
- is_current: boolean, default false

### Skill

- profile_id: foreign key, required, references profiles.id
- name: string, required, max 255 characters
- category: string, required, one of: "Lenguajes", "Frameworks", "DB", "Tools", "Metodologías"
- sort_order: unsigned integer, default 0

### Education

- profile_id: foreign key, required, references profiles.id
- institution: string, required, max 255 characters
- degree: string, required
- field: string, nullable
- description: text, nullable
- start_date: date, nullable
- end_date: date, nullable
- is_current: boolean, default false
- sort_order: unsigned integer, default 0

### Language

- profile_id: foreign key, required, references profiles.id
- name: string, required, max 255 characters
- level: string, nullable
- sort_order: unsigned integer, default 0

### Image

- imageable_id: unsigned big integer, required
- imageable_type: string, required, max 255 characters
- url: string, required
- alt_text: string, nullable
- type: string, nullable, one of: "avatar", "screenshot", "logo", "certificate"
- sort_order: unsigned integer, default 0

## Edge Cases

### For cv-data-models

- Profile with no relationships
- Project with no tech_stack
- Experience with only start_date
- Skill in each of the five categories
- Education with null dates
- Language with various proficiency levels
- Images with different types and null alt_text

### For singleton-profile-enforcement

- Database crash and recovery leading to potential duplicate
- Multiple processes attempting Profile creation
- Migration rollback and re-seed scenarios
- Manual database manipulation (direct SQL)

### For polymorphic-images

- Invalid morph type (e.g., "User")
- Null imageable_id with valid imageable_type
- Multiple images per entity
- Soft deletion scenarios

### For tech-stack-json

- Empty array tech_stack
- tech_stack with nested JSON objects
- Very large tech_stack array
- SQL injection through tech_stack JSON
- Invalid JSON in tech_stack column

## Schema / Field Definitions

See exploration.md for detailed migration specifications.

### Core Tables

| Model | Table | Primary Key | Foreign Keys | Key Constraints |
|-------|-------|-------------|--------------|-----------------|
| Profile | profiles | id | none | Singleton enforced |
| Link | links | id | profile_id → profiles.id | sort_order ASC |
| Project | projects | id | profile_id → profiles.id | start_date DESC |
| Experience | experiences | id | profile_id → profiles.id | start_date DESC |
| Skill | skills | id | profile_id → profiles.id | sort_order ASC |
| Education | education | id | profile_id → profiles.id | sort_order ASC |
| Language | languages | id | profile_id → profiles.id | sort_order ASC |
| Image | images | id | imageable_id/imageable_type | sort_order ASC |

### Indexes

- profiles.id (PK)
- links.profile_id, links.sort_order (composite index)
- projects.profile_id, projects.start_date (composite index)
- experiences.profile_id, experiences.start_date (composite index)
- skills.profile_id (index)
- education.profile_id, education.sort_order (composite index)
- languages.profile_id (index)
- images.imageable_id, images.imageable_type (composite index)

## Relationship Definitions

### Profile Relationships

```
Profile
├── hasMany(Link) through "links" table
├── hasMany(Project) through "projects" table
├── hasMany(Experience) through "experiences" table
├── hasMany(Skill) through "skills" table
├── hasMany(Education) through "education" table
├── hasMany(Language) through "languages" table
└── morphMany(Image) through "images" table
```

### Project Relationships

```
Project
├── belongsTo(Profile)
└── morphMany(Image) through "images" table
```

### Education Relationships

```
Education
├── belongsTo(Profile)
└── morphMany(Image) through "images" table
```

### Image Relationships

```
Image
└── morphTo(imageable) to Profile | Project | Education
```

### Default Orderings

- Profile: N/A (singleton, single record)
- Link: sort_order ASC
- Project: start_date DESC
- Experience: start_date DESC
- Skill: sort_order ASC
- Education: sort_order ASC
- Language: sort_order ASC
- Image: sort_order ASC

### Casts

- Project.tech_stack: array (JSON -> Array cast)

### Morph Map

Image polymorphic relationships require morph map registration in AppServiceProvider:

```php
Image::morphTo('imageable');
```