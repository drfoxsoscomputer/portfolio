# Delta for admin-panel-dashboard

## ADDED Requirements

### Requirement: Dashboard Overview

The system MUST provide an admin dashboard with portfolio statistics and key metrics.

#### Scenario: Dashboard access with portfolio overview

- GIVEN an authenticated admin user
- WHEN accessing `/admin`
- THEN they SHALL see dashboard with portfolio statistics
- AND the dashboard MUST display:
  - Profile name and status
  - Projects count
  - Experiences count  
  - Skills count
  - Education count
  - Languages count
  - Recent activity timeline

#### Scenario: Dashboard statistics accuracy

- GIVEN a portfolio with:
  - 1 Profile, 3 Projects, 2 Experiences, 5 Skills, 3 Educations, 2 Languages
- WHEN dashboard is viewed
- THEN statistics MUST reflect:
  - Profile: "1"
  - Projects: "3"
  - Experiences: "2"
  - Skills: "5" (grouped by category)
  - Educations: "3"
  - Languages: "2"

#### Scenario: Dashboard language localization

- GIVEN Spanish-language admin panel
- WHEN viewing dashboard elements
- THEN all labels MUST be displayed in Spanish:
  - "Panel de Control"
  - "Resumen del Portafolio"
  - "Estadísticas"
  - "Proyectos"

---

# Delta for admin-panel-profile-management

## ADDED Requirements

### Requirement: Singleton Profile Management

The system MUST provide a singular editable Profile page that respects the singleton pattern.

#### Scenario: Profile access and edit permission

- GIVEN a portfolio with exactly one Profile
- WHEN authenticated admin navigates to `/admin/profile`
- THEN they SHALL see editable Profile form
- AND the form MUST show all Profile fields: name, title, location, phone, email, summary, avatar

#### Scenario: Profile validation on edit

- GIVEN valid Profile data with name, title
- WHEN Profile form is submitted
- THEN validation MUST allow the update
- AND the Profile record SHOULD be updated in database

#### Scenario: Singleton enforcement UI

- GIVEN an attempt to create a second Profile
- WHEN admin tries to access profile creation
- THEN the system MUST prevent duplicate creation
- AND display appropriate error in Spanish: "Un perfil ya existe en este portafolio"

---

# Delta for admin-panel-projects-management

## ADDED Requirements

### Requirement: Project Management with Image Relations

The system MUST provide Project CRUD with embedded Image RelationManager for project screenshots/logos.

#### Scenario: Project list with thumbnail preview

- GIVEN Projects exist with attached images
- WHEN admin views Projects list
- THEN each project MUST display thumbnail of first image
- AND the thumbnail MUST show image type indicator (screenshot/logo)

#### Scenario: Project form with image attachment

- GIVEN admin creating/editing a Project
- WHEN accessing Project form
- THEN the form MUST include Image RelationManager section
- AND admin MUST be able to attach images of type "screenshot" or "logo"

#### Scenario: Image validation for projects

- GIVEN Project with image of type "invalid_type"
- WHEN attempting to save Project
- THEN validation MUST reject the image type
- AND error message SHOULD specify allowed types: "avatar", "screenshot", "logo", "certificate"

---

# Delta for admin-panel-experiences-management

## ADDED Requirements

### Requirement: Experience Management with Date Logic

The system MUST provide Experience CRUD with start/end date validation and current indicator.

#### Scenario: Current experience handling

- GIVEN an Experience marked as is_current = true
- WHEN displaying Experience list
- THEN the current experience MUST be highlighted or marked
- AND UI MUST show "Experiencia Actual" indicator

#### Scenario: Date validation logic

- GIVEN Experience with end_date before start_date and is_current = false
- WHEN attempting to save Experience
- THEN validation MUST prevent the contradiction
- AND error SHOULD indicate: "End date cannot be before start date for completed experience"

---

# Delta for admin-panel-skills-management

## ADDED Requirements

### Requirement: Skills Management with Categories

The system MUST provide Skills CRUD with categorization and sorting.

#### Scenario: Skills listing by category

- GIVEN Skills exist in categories "Lenguajes", "Frameworks", "DB", "Tools", "Metodologías"
- WHEN admin views Skills list
- THEN skills MUST be grouped and displayed by category
- AND each category heading SHOULD be in Spanish: "Lenguajes", "Frameworks", "DB", "Tools", "Metodologías"

#### Scenario: Skills sorting functionality

- GIVEN Skills with sort_order values
- WHEN Skills are displayed
- THEN they MUST appear in ascending order by sort_order
- AND drag-and-drop sorter MUST update sort_order values accordingly

---

# Delta for admin-panel-educations-management

## ADDED Requirements

### Requirement: Education Management with Image Attachments

The system MUST provide Education CRUD with embedded Image RelationManager for certificates.

#### Scenario: Education form with certificate attachment

- GIVEN admin creating/editing an Education
- WHEN accessing Education form
- THEN the form MUST include Image RelationManager for certificates
- AND cert images MUST be labeled "Certificados" in Spanish

#### Scenario: Certificate image validation

- GIVEN Education image of type "avatar" attached
- WHEN attempting to save Education
- THEN validation MUST warn that avatar type may be inappropriate for Education
- AND alternative SHOULD suggest using "certificate" type for this entity

---

# Delta for admin-panel-links-management

## ADDED Requirements

### Requirement: Links Management with URL Validation

The system MUST provide Links CRUD with Spanish labels and URL validation.

#### Scenario: Links form with Spanish labels

- GIVEN admin accessing Links form
- WHEN form fields are rendered
- THEN labels MUST be:
  - "Etiqueta" for label field
  - "URL" for url field
  - "Icono" for icon field
  - "Orden" for sort_order field

#### Scenario: URL validation

- GIVEN invalid URL like "not-a-url"
- WHEN attempting to save Link
- THEN validation MUST reject with error in Spanish
- AND error SHOULD specify format: "Debe ser una URL válida (ejemplo: https://example.com)"

---

# Delta for admin-panel-languages-management

## ADDED Requirements

### Requirement: Languages Management with Proficiency Levels

The system MUST provide Languages CRUD with Spanish labels and proficiency levels.

#### Scenario: Language proficiency selection

- GIVEN admin creating a new Language
- WHEN language form is displayed
- THEN proficiency level select MUST show options in Spanish
- AND options SHOULD include: "Básico", "Intermedio", "Avanzado", "Nativo"

#### Scenario: Languages sorting and display

- GIVEN Languages exist for a Profile
- WHEN admin views Languages list
- THEN languages MUST be displayed with:
  - Language name
  - Level in Spanish
  - Sort order indicator
- AND they SHOULD be grouped by proficiency level