# tech-stack-json Specification

## Purpose

Implement JSON casting for Project technology stack storage, enabling efficient storage and retrieval of project technology arrays. This provides a structured approach to managing Project tech stacks with proper validation and query capabilities.

## Requirements

### Functional Requirements

#### Requirement: JSON Tech Stack Column

The system MUST provide a JSON column in the Project table for storing technology stack information.

##### Scenario: Tech stack creation

- GIVEN a Project is created
- WHEN tech_stack data is provided as JSON array
- THEN the tech_stack column MUST store the data as valid JSON

##### Scenario: Tech stack retrieval

- GIVEN a Project with tech_stack is saved
- WHEN the project is retrieved from database
- THEN tech_stack MUST be cast to PHP array for easy access

#### Requirement: JSON Array Casting

The system MUST automatically cast JSON tech_stack data to PHP arrays.

##### Scenario: JSON to array casting

- GIVEN a Project with tech_stack ["PHP", "Laravel", "Vue"] stored as JSON
- WHEN the project data is accessed via `$project->tech_stack`
- THEN tech_stack MUST return ["PHP", "Laravel", "Vue"] as PHP array

##### Scenario: Null tech stack handling

- GIVEN a Project is created without tech_stack
- WHEN project data is retrieved
- THEN tech_stack MUST remain null

#### Requirement: Tech Stack Validation

The system MUST validate tech_stack content for valid technology entries.

##### Scenario: Valid tech stack

- GIVEN tech_stack array ["React", "Node.js", "PostgreSQL"]
- WHEN validation runs
- THEN validation MUST pass with array intact

##### Scenario: Invalid tech stack

- GIVEN tech_stack contains null values or invalid data types
- WHEN validation runs
- THEN system MUST handle validation errors appropriately

### Non-Functional Requirements

#### Requirement: SQLite JSON Compatibility

The system SHOULD ensure SQLite JSON compatibility for tech_stack column.

##### Scenario: SQLite version check

- GIVEN the application requires JSON support
- WHEN SQLite version is checked
- THEN version MUST be >= 3.38.0 for full JSON support

##### Scenario: JSON function availability

- GIVEN the tech_stack column uses JSON functions
- WHEN JSON queries are executed
- THEN SQLite MUST support required JSON functions

#### Requirement: Performance Considerations

The system SHOULD maintain reasonable performance for tech_stack operations.

##### Scenario: Tech stack query performance

- GIVEN a Portfolio with many projects
- WHEN filtering projects by tech stack
- THEN queries SHOULD return results efficiently

##### Scenario: Tech stack array operations

- GIVEN tech_stack arrays with many entries
- WHEN performing array operations
- THEN operations SHOULD perform well

## Acceptance Criteria

### Technical Implementation

- [ ] Project model has tech_stack json column
- [ ] tech_stack casts to array automatically
- [ ] Null tech_stack values are preserved
- [ ] JSON casting works with SQLite backend
- [ ] tech_stack validates as appropriate JSON data type
- [ ] Array operations can be performed on tech_stack
- [ ] tech_stack column is properly indexed
- [ ] Migration correctly adds JSON column

### Functional Testing

- [ ] JSON tech_stack creation works
- [ ] tech_stack casting to array works
- [ ] Null tech_stack is handled correctly
- [ ] SQLite compatibility is ensured
- [ ] Array operations on tech_stack work
- [ ] Database queries with tech_stack work
- [ ] Tech stack validation works
- [ ] Tech stack updates work correctly

## Validation Rules

### Tech Stack Column Validation Rules

- **Field Type**
  - tech_stack: json, nullable
  - Should store as JSON string in database
  - Should cast to array in application

- **Content Rules**
  - Valid entries: strings representing technology names
  - Null values: allowed (nullable)
  - Empty array: allowed (represented as [])
  - Nested structures: allowed (but out of scope for basic implementation)

- **Constraints**
  - maxItems: 20 (limit for tech stack size)
  - items: string (each tech stack item must be string)

### Casting Rules

- **Database to Application**
  - JSON string -> PHP array
  - Null -> null
  - Empty object -> array (with warnings)

- **Application to Database**
  - PHP array -> JSON string
  - null -> null
  - Mixed types -> standardized to strings

### Query Rules

- **JSON Functions**
  - Support for JSON_EXISTS, JSON_TYPE, JSON_STORAGE
  - Support for array operations (length, indexing)
  - Support for string operations on array elements

- **Index Strategy**
  - The tech_stack column may be indexed for faster queries
  - Consider materialized paths for complex tech stack queries

## Edge Cases

### Tech Stack Creation Edge Cases

- Empty array tech_stack ([])
- Single-item tech_stack ["PHP"]
- Large tech_stack array (near max items)
- Tech stack with duplicate entries
- Tech stack with null values within array
- Tech stack with numeric entries (should be strings)
- Tech stack with special characters (Unicode support)

### Tech Stack Query Edge Cases

- Tech stack contains exact matches
- Tech stack array operations (length, union, intersection)
- Tech stack filtering with wildcards
- Tech stack queries with null values
- Tech stack filtering with complex conditions

### Database Edge Cases

- SQLite version incompatible with JSON
- JSON storage limits
- Database connection failures during JSON operations
- Migration failures with existing data

### Transaction Edge Cases

- Tech stack update with concurrent modifications
- Transaction rollback with tech_stack changes
- Partial updates to tech_stack

## Schema / Field Definitions

### Projects Table Tech Stack Schema

| Field | Type | Constraints | Purpose |
|-------|------|-------------|---------|
| tech_stack | json | nullable | Technology stack as JSON |
| tech_stack_index | json | for indexing | Optional index for queries |

### Tech Stack Table Schema (Alternative Approach)

| Field | Type | Constraints | Purpose |
|-------|------|-------------|---------|
| tech_stack_id | id() | PK | Unique identifier |
| project_id | foreignId() | FK, required | Reference to projects |
| technology | string() | required | Technology name |
| category | string() | nullable | Technology category |
| created_at | timestamp | | Creation timestamp |
| updated_at | timestamp | | Last update timestamp |

### Migration Strategy

**Primary Approach (JSON Column)**:

```php
// In migration
$table->json('tech_stack')->nullable();
```

**Alternative Approach (Pivot Table)**:

```php
// Create tech_stacks table
$table->id();
$table->foreignId('project_id')->constrained();
$table->string('technology');
$table->string('category')->nullable();
$table->timestamps();
```

## Relationship Definitions

### Project Tech Stack Relationships

```
Project
├── hasMany -> TechStackEntry (pivot-based approach)
└── tech_stack_json -> JSON column (direct approach)
```

### Tech Stack Entry Relationships

```
TechStackEntry (pivot model)
├── belongsTo -> Project
└── Contains: technology, category
```

### JSON Tech Stack Relationships

```
Project (direct JSON column)
├── tech_stack: array[string] (JSON column)
└── Additional relationships remain unchanged
```

## Implementation Details

### JSON Column Approach

1. **Model Definition**
   ```php
   class Project extends Model {
       protected $casts = [
           'tech_stack' => 'array',
           'updated_at' => 'datetime',
       ];
   }
   ```

2. **Validation**
   ```php
   class ProjectRequest extends Request {
       public function rules() {
           return [
               'tech_stack' => [
                   'sometimes',
                   'nullable',
                   'array',
                   'max:20',
                   'array:string', // Each item must be string
               ],
           ];
       }
   }
   ```

3. **Accessor/Attribute**
   ```php
   class Project extends Model {
       public function getTechStackAttribute($value) {
           return $value ? json_decode($value, true) : null;
       }
       
       public function setTechStackAttribute($value) {
           $this->attributes['tech_stack'] = json_encode($value);
       }
   }
   ```

### Pivot Table Approach

1. **Model Definition**
   ```php
   class TechStack extends Model {
       public function project() {
           return $this->belongsTo(Project::class);
       }
   }
   ```

2. **Project Relationships**
   ```php
   class Project extends Model {
       public function techStacks() {
           return $this->hasMany(TechStack::class);
       }
       
       public function getTechStackArrayAttribute() {
           return $this->techStacks->pluck('technology')->toArray();
       }
   }
   ```

### Query Patterns

1. **JSON Column Queries**
   ```php
   // Get projects with specific technology
   $projects = Project::whereJsonContains('tech_stack', ['Laravel'])
       ->get();
   
   // Get projects by tech stack length
   $projects = Project::where(function ($query) {
       $query->whereJsonLength('tech_stack', 3);
   })->get();
   ```

2. **Pivot Table Queries**
   ```php
   // Get projects with specific technology
   $projects = Project::whereHas('techStacks', function ($query) {
       $query->where('technology', 'Laravel');
   })->get();
   
   // Get projects by tech stack count
   $projects = Project::withCount('techStacks')
       ->having('tech_stacks_count', 3)
       ->get();
   ```

## Testing Strategy

### Unit Tests

1. **JSON Casting Tests**
   - Test JSON to array casting
   - Test array to JSON casting
   - Test null value handling
   - Test empty array handling

2. **Validation Tests**
   - Test valid tech stack arrays
   - Test invalid tech stack values
   - Test tech stack size limits
   - Test tech stack content validation

3. **Query Tests**
   - Test tech stack queries
   - Test array operations
   - Test JSON function usage
   - Test index performance

### Integration Tests

1. **CRUD Tests**
   - Test tech stack creation
   - Test tech stack updates
   - Test tech stack retrieval
   - Test tech stack deletion

2. **Relationship Tests**
   - Test tech stack with projects
   - Test tech stack queries across projects
   - Test tech stack integration with other entities

3. **Performance Tests**
   - Test tech stack query performance
   - Test tech stack array operations
   - Test large tech stack handling

## Performance Considerations

### JSON Column Performance

1. **Query Optimization**
   - Use `whereJsonContains` for exact matches
   - Use `whereJsonLength` for array size queries
   - Consider materialized views for complex queries

2. **Indexing**
   - JSON column indexes can be expensive
   - Consider partial indexes for frequently used queries
   - Use JSON path indexes for specific patterns

### Pivot Table Performance

1. **Query Optimization**
   - Use eager loading for tech stack data
   - Index on technology and project_id
   - Consider composite indexes for complex queries

2. **Memory Usage**
   - Pivot tables can use more memory
   - Consider caching frequently accessed tech stack data
   - Use lazy loading for large datasets

## Security Considerations

### Data Validation

- Validate tech stack array contents
- Sanitize technology names
- Prevent SQL injection through JSON data
- Validate JSON structure

### Access Control

- Tech stack access control based on project permissions
- Sensitive technology information protection
- Audit trails for tech stack changes

## Error Handling

### Common Errors

1. **JSON Syntax Errors**
   ```
   Error: Invalid JSON format
   Solution: Validate JSON before storing
   ```

2. **Casting Errors**
   ```
   Error: Cannot cast tech_stack to array
   Solution: Use try-catch in casting logic
   ```

3. **Validation Errors**
   ```
   Error: Tech stack validation failed
   Solution: Return clear validation error messages
   ```

### Error Handling Patterns

```php
// In model casting
try {
    $decoded = json_decode($value, true);
    if (json_last_error() !== JSON_ERROR_NONE) {
        throw new \Exception('Invalid JSON in tech_stack');
    }
    return $decoded;
} catch (\\Exception $e) {
    // Log error and return null
    Log::error('Tech stack casting error: ' . $e->getMessage());
    return null;
}
```

## Recovery Procedures

### JSON Casting Issues

- If tech_stack casting fails, test with sample data
- Validate JSON syntax with JSON validator
- Check model casting configuration
- Test with different array structures

### Tech Stack Data Issues

- If tech stack data is corrupted, restore from backup
- Test tech stack recovery scenarios
- Validate tech stack consistency
- Test tech stack migration

### Performance Issues

- If tech_stack queries are slow, check for indexes
- Optimize tech_stack queries with proper conditions
- Consider caching for frequently accessed tech stack data
- Monitor tech_stack performance over time

## Success Criteria

### Technical Implementation

- [ ] tech_stack column is correctly typed as JSON
- [ ] tech_stack casting works seamlessly
- [ ] SQLite compatibility is ensured
- [ ] Tech stack validation is comprehensive
- [ ] Performance is acceptable for tech_stack operations
- [ ] Error handling is robust
- [ ] Security measures protect tech_stack data
- [ ] Migration correctly adds tech_stack column

### Functional Testing

- [ ] JSON tech_stack creation works
- [ ] tech_stack casting to array works
- [ ] Null tech_stack is handled correctly
- [ ] SQLite compatibility is ensured
- [ ] Array operations on tech_stack work
- [ ] Database queries with tech_stack work
- [ ] Tech stack validation works
- [ ] Tech stack updates work correctly

### Operational Readiness

- [ ] Tech stack queries are optimized
- [ ] Performance is monitored
- [ ] Recovery procedures are tested
- [ ] Error messages are user-friendly
- [ ] Security measures protect tech_stack data
- [ ] Documentation covers tech_stack behavior
- [ ] Training materials cover tech_stack constraints
- [ ] Rollback procedures are tested and documented
