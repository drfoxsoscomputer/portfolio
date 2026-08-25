# ESTADO — Portfolio

> Una pagina que cualquier sesion futura lee primero. Si esto no esta
> actualizado, es un bug mio, no del usuario.

## Que es
Portfolio personal (Laravel 13 + Filament v5) con panel admin y pagina publica Livewire para buscar trabajo.

## Como correr
```bash
composer install && npm install && npm run build
cp .env.example .env   # definir ADMIN_EMAIL y ADMIN_PASSWORD antes de seedear
php artisan migrate --seed
php artisan serve      # / (publico) y /admin (panel)
php artisan test       # 115 tests
```

## Estructura
- `app/Livewire/Portfolio/` — PortfolioPage (full-page) + 9 secciones + ContactForm
- `resources/views/layouts/portfolio.blade.php` — layout publico (dark mode, Alpine, print CSS)
- `app/Services/NotificationContactNotifier.php` + `app/Contracts/ContactNotifierInterface.php` — contacto → notificacion DB al admin
- `config/portfolio.php` — credenciales admin via env (ADMIN_EMAIL / ADMIN_PASSWORD)
- `app/Filament/Resources/` — CRUDs del panel (Course, Education, Experience, Project, Link, Skill, Language)
- `database/seeders/ProfileSeeder.php` — datos reales del CV + usuario admin
- `openspec/changes/public-portfolio-page/tasks.md` — tracker del ciclo SDD actual (3 PRs)

## Estado actual
- Cadena de PRs feature-branch-chain sobre `feature/portfolio-tracker`.
- PR #7 (GitHub #6): Spatie Media Library + Course CRUD — MERGEADO.
- PR #8 (GitHub #7): pagina publica + ContactForm — ABIERTO, esperando review/merge.
- Seguridad: email/password del admin ya no viven en codigo ni README; van por `.env`.

## Pendientes / ToDo
- [ ] Merge del PR #8 y continuar cadena hacia PR #9
- [ ] PR #9: GoogleTranslateService (API key configurable, NO OpenAI), lang/es.json + en.json, toggle idioma, boton "Ver portafolio" en admin, PortfolioUpdated + polling 30s, SEO meta + Open Graph + Twitter Card + JSON-LD Person
- [ ] Follow-up: tests Spatie pendientes 7.7/7.8 (validacion uploads, factory media)
- [ ] Commit chore aparte: config AI (.agents/, .claude/, .mcp.json, AGENTS.md, CLAUDE.md, opencode.json, boost.json, laravel/boost en composer) — decidir si se versiona o se ignora

## Decisiones recientes
- Credenciales admin a `.env` via `config/portfolio.php` (nunca `env()` en runtime: rompe con config:cache). Password vieja `asdf1234` quedo expuesta en historial publico — usuario decidio no rotarla por ahora.
- Contacto sin servicios de email: guarda en DB + notificacion Filament al admin ($0 budget).
- Multi-idioma futuro: traduccion automatica desde espanol + edicion manual.
- $0 budget absoluto: todo servicio debe ser gratuito.
- Config AI fuera del PR #8: commit chore futuro sobre tracker.

## Tests / verificacion
```bash
php artisan test --compact          # suite completa: 115 tests, 370 assertions
vendor/bin/pint app config database tests --format agent
```
