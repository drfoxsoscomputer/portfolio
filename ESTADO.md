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
php artisan test       # 125 tests
```

## Estructura
- `app/Livewire/Portfolio/` — PortfolioPage (full-page) + 9 secciones + ContactForm
- `resources/views/layouts/portfolio.blade.php` — layout publico (dark mode, Alpine, print CSS)
- `app/Services/NotificationContactNotifier.php` + `app/Contracts/ContactNotifierInterface.php` — contacto → notificacion DB al admin
- `config/portfolio.php` — credenciales admin via env (ADMIN_EMAIL / ADMIN_PASSWORD)
- `app/Filament/Resources/ContactRequests/` — seccion "Mensajes" del admin (solo lectura, badge de no leidos, marcar como leido)
- `app/Filament/Resources/` — CRUDs del panel (Course, Education, Experience, Project, Link, Skill, Language)
- `database/seeders/ProfileSeeder.php` — datos reales del CV + usuario admin
- `openspec/changes/public-portfolio-page/tasks.md` — tracker del ciclo SDD actual (3 PRs)

## Estado actual
- Cadena de PRs feature-branch-chain sobre `feature/portfolio-tracker`.
- PR #7 (GitHub #6): Spatie Media Library + Course CRUD — MERGEADO.
- PR #8 (GitHub #7): pagina publica + ContactForm — MERGEADO (2edde43), con fixes de review 4R incluidos.
- Rama `feature/portfolio-phone-contact` (pull/8) extendida con fixes de la prueba visual: telefono abre WhatsApp (wa.me), filtro de skills con indices numericos, toggle sol/luna dinamico, databaseNotifications() en panel, recurso Mensajes para ver contactos, bloque print ATS. Pendiente push+review del usuario.
- Seguridad: email/password del admin ya no viven en codigo ni README; van por `.env`.

## Pendientes / ToDo
- [ ] PR #9: GoogleTranslateService (API key configurable, NO OpenAI), lang/es.json + en.json, toggle idioma, boton "Ver portafolio" en admin, PortfolioUpdated + polling 30s, SEO meta + Open Graph + Twitter Card + JSON-LD Person
- [ ] Follow-up: tests Spatie pendientes 7.7/7.8 (validacion uploads, factory media)
- [ ] Commit chore aparte: config AI (.agents/, .claude/, .mcp.json, AGENTS.md, CLAUDE.md, opencode.json, boost.json, laravel/boost en composer) — decidir si se versiona o se ignora
- [ ] Merge del pull/8 (telefono + fixes UI + admin mensajes) y continuar con PR #9

## Decisiones recientes
- Prueba visual del usuario (agosto 2026): tel: reemplazado por https://wa.me/{digits} (abre WhatsApp directo); filtro de skills reescrito con indices numericos porque @js() en atributos single-quote rompia el HTML; toggle de tema muestra sol/luna + label Light/Dark segun estado.
- Admin puede ver mensajes: ->databaseNotifications() en AdminPanelProvider (campanita) + ContactRequestResource read-only (canCreate false, ViewAction + markAsRead visible solo si unread, badge nav con conteo unread, poll 30s).
- Print ATS: bloque print:block al inicio de portfolio-page con nombre/titulo/telefono(WhatsApp)/ubicacion/email/links como texto plano para parseo por ATS.
- Review 4R del PR #8 (hecho por agente principal tras fallo de subagents): arreglado filtro de skills, rate limit 5/min por IP en ContactForm, eager load de media. MINOR documentados: fallback asdf1234 en config (decision del usuario), FQCN inline en languages blade.
- Credenciales admin a `.env` via `config/portfolio.php` (nunca `env()` en runtime: rompe con config:cache). Password vieja `asdf1234` quedo expuesta en historial publico — usuario decidio no rotarla por ahora.
- Contacto sin servicios de email: guarda en DB + notificacion Filament al admin ($0 budget).
- Multi-idioma futuro: traduccion automatica desde espanol + edicion manual.
- $0 budget absoluto: todo servicio debe ser gratuito.
- Config AI fuera del PR #8: commit chore futuro sobre tracker.

## Tests / verificacion
```bash
php artisan test --compact          # suite completa: 125 tests, 400 assertions
vendor/bin/pint app config database tests --format agent
```
