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
php artisan test       # 142 tests
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
- Rama `feature/portfolio-phone-contact` (pull/8) extendida con fixes de la prueba visual, CV ATS y telefono WhatsApp. **MERGEADA en `feature/portfolio-tracker` (PR #8, 276223a)** — verificacion 142 tests sobre el estado mergeado.
- ATS real: el usuario reporto que el bloque `print:block` no cumplia (Ctrl+P segula imprimiendo la pagina visual). Se resolvio por la via estandar Laravel: `barryvdh/laravel-dompdf` (v3.1) genera un PDF real desde `resources/views/cv.blade.php` — CV una columna ATS-compatible con formato convencional (Times/serif, headings azul #1e40af, bullets de texto, sin tablas) — servido por `GET /cv` (CvController, `CV_DenisPina_FullStack.pdf`) con boton "Download CV" en el hero. Formateo en `app/Support/CvFormatter.php`. 142 tests verdes.
- Convenciones del CV (fuente de verdad en README, seccion "CV descargable"): Educacion ordenada por `sort_order` (peso academico: titulo antes que bootcamp, NO por fecha); bootcamps van en EDUCACION, los cursos cortos en FORMACION COMPLEMENTARIA; limites por seccion (4/3/3/5/3); linea final "Ultima actualizacion" = max updated_at del perfil + relaciones.
- Seguridad: email/password del admin ya no viven en codigo ni README; van por `.env`.

## Pendientes / ToDo
- [ ] PR #9: GoogleTranslateService (API key configurable, NO OpenAI), lang/es.json + en.json, toggle idioma, boton "Ver portafolio" en admin, PortfolioUpdated + polling 30s, SEO meta + Open Graph + Twitter Card + JSON-LD Person
- [ ] Follow-up: tests Spatie pendientes 7.7/7.8 (validacion uploads, factory media)
- [ ] Commit chore aparte: config AI (.agents/, .claude/, .mcp.json, AGENTS.md, CLAUDE.md, opencode.json, boost.json, laravel/boost en composer) — decidir si se versiona o se ignora
- [ ] Merge del pull/8 (telefono + fixes UI + admin mensajes) y continuar con PR #9

## Decisiones recientes
- Prueba visual del usuario (agosto 2026): tel: reemplazado por https://wa.me/{digits} (abre WhatsApp directo); filtro de skills reescrito con indices numericos porque @js() en atributos single-quote rompia el HTML; toggle de tema muestra sol/luna + label Light/Dark segun estado.
- Admin puede ver mensajes: ->databaseNotifications() en AdminPanelProvider (campanita) + ContactRequestResource read-only (canCreate false, ViewAction + markAsRead visible solo si unread, badge nav con conteo unread, poll 30s).
- Print ATS → reemplazado por PDF real: `barryvdh/laravel-dompdf` renderiza `cv.blade.php` (una columna, B/N, secciones estandar, links como texto) en `GET /cv`. El bloque `print:block` de portfolio-page quedo como bonus, pero el entregable ATS es el PDF descargable (`cv-denis-pina.pdf` → luego `CV_DenisPina_FullStack.pdf`). Dato: `Pdf::download()` usa BinaryFileResponse (temp file); se cambio a `response()->streamDownload` para tests limpios y sin archivos temporales.
- CV convencional (agosto 2026, decision del usuario "formato por convencion ATS, no inventar"): reescrito `cv.blade.php` — serif (guia es serif, verificado por vision), nombre/headings azul #1e40af, fechas gris itálica inline, bullets disc. Peso academico en Educacion: orden por `sort_order` no por fecha (bootcamp Henry NO va en Formacion Complementaria — son 800h estructuradas, es educacion). Vision dio 90% fiel vs guia y se aplicaron 4 ajustes. PDF final 5KB (fuente core Times sin incrustar — mejora para uploads ATS).
- Review 4R del PR #8 (hecho por agente principal tras fallo de subagents): arreglado filtro de skills, rate limit 5/min por IP en ContactForm, eager load de media. MINOR documentados: fallback asdf1234 en config (decision del usuario), FQCN inline en languages blade.
- Credenciales admin a `.env` via `config/portfolio.php` (nunca `env()` en runtime: rompe con config:cache). Password vieja `asdf1234` quedo expuesta en historial publico — usuario decidio no rotarla por ahora.
- Contacto sin servicios de email: guarda en DB + notificacion Filament al admin ($0 budget).
- Multi-idioma futuro: traduccion automatica desde espanol + edicion manual.
- $0 budget absoluto: todo servicio debe ser gratuito.
- Config AI fuera del PR #8: commit chore futuro sobre tracker.

## Tests / verificacion
```bash
php artisan test --compact          # suite completa: 142 tests, 442 assertions
vendor/bin/pint app config database tests --format agent
```
