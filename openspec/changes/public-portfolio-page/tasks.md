# Tasks: Página Portafolio Público

## Review Workload Forecast

| Field | Value |
|-------|-------|
| Estimated changed lines | ~1,600 (PR#7: ~450, PR#8: ~800, PR#9: ~350) |
| 400-line budget risk | High |
| Chained PRs recommended | Yes |
| Suggested split | PR #7 → PR #8 → PR #9 (feature-branch-chain) |
| Delivery strategy | auto-chain |
| Chain strategy | feature-branch-chain |

```
Decision needed before apply: No
Chained PRs recommended: Yes
Chain strategy: feature-branch-chain
400-line budget risk: High
```

### Suggested Work Units

| Unit | Goal | Likely PR | Notes |
|------|------|-----------|-------|
| 1 | Spatie Media Library + Admin Uploads | PR #7 | Base: `feature/portfolio-pr7-spatie` → feature/tracker |
| 2 | Public Portfolio Page (Livewire + Tailwind) | PR #8 | Base: PR #7 branch |
| 3 | Preview + Refresco + Multi-idioma + SEO | PR #9 | Base: PR #8 branch |

## PR #7: Spatie Media Library + Admin Uploads

- [ ] 7.1 Install `filament/spatie-laravel-media-library-plugin:^5.0` + `spatie/laravel-medialibrary:^11.0`
- [ ] 7.2 Publish Spatie migration, run `php artisan migrate`
- [ ] 7.3 Create storage symlink if not present
- [ ] 7.4 Update `User.php`: add `HasMedia` trait, `registerMediaCollections` for 'avatar' (singleFile), `getFirstMediaUrl` fallback
- [ ] 7.5 Update `Project.php`: add `HasMedia`, `registerMediaCollections` for 'screenshots' (multi, 20 max, jpeg/png/webp)
- [ ] 7.6 Update `Education.php`: add `HasMedia`, `registerMediaCollections` for 'certificates' (multi, 5 max, jpeg/png/pdf)
- [ ] 7.7 **CREATE** `Course` model (`user_id`, name, institution, date, description, url_certificate, sort_order) + migration + `HasMedia` + 'certificates' collection
- [ ] 7.8 **CREATE** `CourseResource` in Filament admin with `SpatieMediaLibraryFileUpload` for certificates
- [ ] 7.9 Update `EditProfile` avatar field: replace with `SpatieMediaLibraryFileUpload`
- [ ] 7.10 Update `ManageProjects`/`ListProjects`: replace images with `SpatieMediaLibraryFileUpload`
- [ ] 7.11 Update `ManageEducation`/`ListEducation`: replace images with `SpatieMediaLibraryFileUpload`
- [ ] 7.12 Update `UserFactory` + create `CourseFactory`
- [ ] 7.13 Update `ProfileSeeder` to handle Spatie media
- [ ] 7.14 Write tests for Course CRUD + media uploads
- [ ] 7.15 Clean cached schema: delete `database/schema/sqlite-schema.sql`

## PR #8: Public Portfolio Page (Livewire + Tailwind)

- [ ] 8.1 Create layout `resources/views/layouts/portfolio.blade.php` (meta, dark mode, fonts, Tailwind, Alpine, print CSS)
- [ ] 8.2 Create `PortfolioPage` Full Page Livewire component (loads User with eager loading)
- [ ] 8.3 Create `HeroSection` component (avatar from Spatie, name, title, summary, social links)
- [ ] 8.4 Create `SkillsSection` component (grouped by category, Alpine filter)
- [ ] 8.5 Create `ProjectsSection` component (grid cards, Spatie screenshots, tech_stack chips, links)
- [ ] 8.6 Create `ExperienceSection` component (vertical timeline)
- [ ] 8.7 Create `EducationSection` component (cards with Spatie certificates)
- [ ] 8.8 Create `CoursesSection` component (cards with certificate images from Spatie)
- [ ] 8.9 Create `LinktreeSection` component (social/professional links as button cards)
- [ ] 8.10 Create `ServicesSection` component (services offered, config or new model)
- [ ] 8.11 Create `ContactForm` Livewire component (name, email, message, stores in DB)
- [ ] 8.12 Create `LanguagesSection` component (level bars)
- [ ] 8.13 Create `StatsSection` component (animated counters, Alpine Intersection Observer)
- [ ] 8.14 Update `routes/web.php`: `GET /` → `PortfolioPage` (replace welcome view)
- [ ] 8.15 Implement dark mode toggle (Alpine + localStorage, default dark)
- [ ] 8.16 Implement responsive design (mobile-first Tailwind)
- [ ] 8.17 Add scroll animations (Intersection Observer, fade-in)
- [ ] 8.18 Add print CSS for CV printing
- [ ] 8.19 Write/update tests for all portfolio components

## PR #9: Preview + Refresco + Multi-idioma + SEO

- [ ] 9.1 Add "Ver portafolio" button in admin header (opens new tab)
- [ ] 9.2 Add modal preview option in admin panel
- [ ] 9.3 Create `PortfolioUpdated` event + listener (updates timestamp when admin modifies public data)
- [ ] 9.4 Implement refresh check in `PortfolioPage`
- [ ] 9.5 Create lang files: `lang/es.json`, `lang/en.json`
- [ ] 9.6 Add language toggle in portfolio layout (Alpine + localStorage)
- [ ] 9.7 Add SEO meta tags (title, description, keywords)
- [ ] 9.8 Add Open Graph meta tags (og:title, og:description, og:image, og:url, og:type)
- [ ] 9.9 Add Twitter Card meta tags (card, title, description, image, site)
- [ ] 9.10 Add Schema.org JSON-LD for Person
- [ ] 9.11 Update/regression tests for preview, i18n, SEO, refresh
