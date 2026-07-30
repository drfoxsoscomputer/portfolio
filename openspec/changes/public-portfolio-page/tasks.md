# Tasks: Página Portafolio Público

## Review Workload Forecast

| Campo | Valor |
|-------|-------|
| Líneas estimadas | ~1,500 (PR#7: ~350, PR#8: ~800, PR#9: ~350) |
| Riesgo 400-líneas | Alto |

```
Decision needed before apply: No
Chained PRs recommended: Yes
Chain strategy: feature-branch-chain
400-line budget risk: High
```

### Unidades de Trabajo

| Unidad | Meta | PR | Base |
|--------|------|----|------|
| 1 | Spatie admin uploads + Education collection | PR #7 | `feature/portfolio-pr7-spatie` → tracker |
| 2 | Livewire components + ContactForm + responsive | PR #8 | PR #7 branch |
| 3 | Google Translate + Preview + SEO + i18n | PR #9 | PR #8 branch |

## PR #7: Spatie Admin Uploads + Education Collection

- [x] 7.1 Agregar `registerMediaCollections()` a `Education.php` para colección 'certificates' (multi, 5 max, jpeg/png/pdf)
- [x] 7.2 Agregar `SpatieMediaLibraryFileUpload` a `SkillResource` para colección 'icons' (maxFiles:1)
- [x] 7.3 Agregar `SpatieMediaLibraryFileUpload` a `LanguageResource` para colección 'flags' (maxFiles:1)
- [x] 7.4 Agregar `SpatieMediaLibraryFileUpload` a `ExperienceResource` para colección 'logos' (maxFiles:1)
- [x] 7.5 Actualizar `SkillFactory`, `LanguageFactory`, `ExperienceFactory` para media Spatie
- [x] 7.6 Actualizar `ProfileSeeder` para generar media de prueba con Spatie
- [ ] 7.7 Tests: validación SpatieMediaLibraryFileUpload (tipos, max files)
- [ ] 7.8 Tests: factory/seeders generan media correctamente

## PR #8: Livewire Components + ContactForm

- [ ] 8.1 Crear layout `resources/views/layouts/portfolio.blade.php` (meta, dark mode, Alpine, Tailwind, print CSS)
- [ ] 8.2 Crear `PortfolioPage` Full Page Livewire (`app/Http/Livewire/Portfolio/`) con eager loading de User + relaciones
- [ ] 8.3 Crear `HeroSection` (avatar Spatie, nombre, título, summary, links sociales)
- [ ] 8.4 Crear `SkillsSection` (iconos Spatie, group por categoría, Alpine filter)
- [ ] 8.5 Crear `ProjectsSection` (grid cards + screenshots Spatie, tech_stack, links)
- [ ] 8.6 Crear `ExperienceSection` (timeline vertical + logos Spatie)
- [ ] 8.7 Crear `EducationSection` (cards + certificates Spatie)
- [ ] 8.8 Crear `LanguagesSection` (flags Spatie, nivel con barras)
- [ ] 8.9 Crear `StatsSection` (contadores animados con Alpine Intersection Observer)
- [ ] 8.10 Crear `LinktreeSection` (links sociales como botones)
- [ ] 8.11 Crear `CoursesSection` (cards + certificates Spatie)
- [ ] 8.12 Crear `ContactRequest` model + migration (name, email, message, read_at)
- [ ] 8.13 Crear `ContactNotifierInterface` + `NotificationContactNotifier` (notificación admin Filament). SIN email.
- [ ] 8.14 Crear `ContactForm` Livewire component (formulario + store + notificar admin)
- [ ] 8.15 Actualizar `routes/web.php`: `GET /` → `PortfolioPage`
- [ ] 8.16 Dark mode toggle (Alpine + localStorage, default dark)
- [ ] 8.17 Responsive mobile-first (Tailwind)
- [ ] 8.18 Scroll animations fade-in (Intersection Observer)
- [ ] 8.19 Print CSS para recruiters
- [ ] 8.20 Tests: todos los componentes renderizan datos correctamente
- [ ] 8.21 Tests: ContactForm valida, guarda y notifica

## PR #9: Google Translate + Preview + SEO + i18n

- [ ] 9.1 Crear `GoogleTranslateService` con API key configurable (NO OpenAI)
- [ ] 9.2 Crear `lang/es.json`, `lang/en.json`
- [ ] 9.3 Agregar toggle de idioma en layout (Alpine + localStorage)
- [ ] 9.4 Agregar botón "Ver portafolio" en admin header (nuevo tab)
- [ ] 9.5 Crear `PortfolioUpdated` event + listener (actualiza `last_updated` en User)
- [ ] 9.6 Implementar polling simple en `PortfolioPage` (cada 30s, compara timestamp)
- [ ] 9.7 SEO meta tags (title, description, keywords)
- [ ] 9.8 Open Graph (og:title, og:description, og:image, og:url, og:type)
- [ ] 9.9 Twitter Card (summary_large_image)
- [ ] 9.10 Schema.org JSON-LD para Person
- [ ] 9.11 Tests: Google Translate service con mock HTTP
- [ ] 9.12 Tests: refresh detecta cambios de timestamp
- [ ] 9.13 Tests: meta tags SEO presentes en DOM
