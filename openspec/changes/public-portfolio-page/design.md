# Diseño: Página Portafolio Público

## Enfoque Técnico

Implementar un sistema completo de portafolio público usando Laravel 13 + Filament 5 con Livewire Full Page Components. El diseño sigue el enfoque propuesto de 3 PRs (Spatie Media Library, Componentes Livewire, Funcionalidades Polish) y se enfoca en la arquitectura hexagonal con interfaces de dominio claramente definidas. Usar Livewire 3 para componentes reactivos que leen directamente de modelos de Eloquent con relaciones Spatie Media Library para uploads de medios.

## Decisiones de Arquitectura

### Decision: Usar plugin Spatie Media Library en lugar del modelo legacy Image

**Opción**: Plugin Filament Spatie Media Library (`filament/spatie-laravel-media-library-plugin:^5.0`) con colección de medios de Spatie para `User.avatar`, `Project.screenshots`, `Education.certificates`

**Alternativas consideradas**:
- Usar modelo `Image` existente (polimórfico legacy)
- Implementar controlador personalizado de uploads de archivos
- Usar Laravel File System \ imágenes directas

**Racional**: Plugin proporciona validación UI nativa de Filament (`SpatieMediaLibraryFileUpload`), reordenamiento de medios, preview, Y mantiene medialibrary de Spatie como backend. Elimina duplicación del modelo legacy `Image`. Integra seamless con Filament admin existente.

### Decision: Usar patrón Component Livewire Full Page para página pública

**Opción**: `PortfolioPage` Livewire component completando toda la ruta `/`, con secciones hijas (`HeroSection`, `SkillsSection`, etc.) como componentes independientes

**Alternativas consideradas**:
- Controlador PHP + Blade + Alpine.js mixto
- React + Inertia + componente PHP
- Componentes Blade tradicionales con partials

**Racional**: Livewire proporciona automáticamente SSR, poder reactivo de buena experiencia, Y está alineado con stack actual de Filament. Componentes separados permiten testing modular y reutilización. Componente Full Page reemplaza bienvenida predeterminada de Laravel.

### Decision: Implementar Interfaz ContactNotifierInterface para contacto con diferenciación para email

**Opción**: Interfaz `ContactNotifierInterface` con `EmailContactNotifier` (implementado por ahora guarda solo) y `NotificationContactNotifier` (preparado para futuro)

**Alternativas consideradas**:
- Servicio directo de email de Laravel Mailer
- Modelo de eventos con listeners
- Plugin de terceros

**Racional**: Permite facilitar adición de email más tarde, mantiene sin dependencias pagas, limpia separación entre tipos de notificación. Prepara para Mailtrap free tier (1000 emails/mes) que puede ser configurado en admin.

### Decision: Auto-traducción usando tool de OpenAI API (gratuito)

**Opción**: Servicio `AutoTranslationService` usando API de OpenAI GPT-4o free tier (1 millón tokens gratuitos por mes) para traducción Spanish ⇄ inglés, edición manual opcional

**Alternativas consideradas**:
- Google Translate API free tier
- DeepL API gratuito
- Traducción automática local (LibreTranslate)

**Racional**: OpenAI API proporciona mejor calidad, más fácil integración con pattern existente del proyecto, el free tier cubre uso típico. Editar manual opcional de aspecto importante.

### Decision: Estructura de componente de sección para portafolio

**Opción**: `PortfolioPage` como component padre conteniendo secciones como componentes de Livewire separados (`HeroSection`, `SkillsSection`, `ProjectsSection`, `ExperienceSection`, `EducationSection`, `LanguagesSection`, `StatsSection`, `LinktreeSection`, `CoursesSection`)

**Alternativas consideradas**:
- Componente monolítico con todas las secciones
- Filas separadas de Livewire con página común de Blade
- Paginado (no requerido)

**Racional**: Permite testing/UX independiente, reuso, mantenimiento modular. Clear separation of concerns, más fácil para colaboradores individuales trabajar.

## Flujo de Datos

```
Público → ContactForm (Livewire) → ContactRequest (DB) → ContactNotifierInterface → Admin Notification

    ↓
Admin ↔ Portafolio Management (Filament) ↔ User model (HasMedia + Media Collections)

    ↓
Auto Translation:
    Formulario Público (Español) → Traductor de OpenAI → Inglés (almacenado en cache) → Página Vista

    ↓
Portfolio Page:
    User (Eager Load) → User.avatar, User.skills, User.projects, User.experiences, User.educations, User.languages → Media Libraries
        ↓
    Components Livewire:
        HeroSection → Avatar (Spatie)
        SkillsSection → Skills + Icons (Spatie)
        ProjectsSection → Projects + Screenshots (Spatie)
        ExperienceSection → Experiences + Logos (Spatie)
        EducationSection → Education + Certificates (Spatie)
        CoursesSection → Courses + Certificates (Spatie)
        StatsSection → Calcular desde data
        LanguagesSection → Languages + Flags (Spatie)
        LinktreeSection → Links
        ContactForm → Guardar en DB
```

### Datos de Formulario de Contacto
1. Usuario completa formulario (`name`, `email`, `message`)
2. Livewire component valida y guarda en BD
3. `ContactNotificationService` dispara evento
4. Interface `ContactNotifierInterface` `createNotification(ContactRequest)`
5. Admin panel puede ver notificaciones
6. (Futura) Email AI puede ser agregado usando `EmailContactNotifier`

### Flujo de Medios Auto-traducción
1. Admin edita contenido del portafolio público (vía Filament)
2. Websocket/prefix de admin dispara evento `PortfolioContentUpdated`
3. Trasnformador `AutoTranslationService` lee todo content español desde Redis/cache
4. Llama a OpenAI API para generar English (opcional)
5. Guarga translation English en cache (Redis) o guarda como entrada separada
6. Página pública puede detectar toggle de idioma y servir versión apropiada

### Actualización Inteligente de Página
1. Admin edita cualquier dato de portafolio (User, Project, Education, etc.)
2. Filament observa cambios y dispara evento `PortfolioUpdated`
3. Evento provoca `PortfolioPage` a re-renderizar (simulando pull)
4. Versión cacheada del portfolio se actualiza desde BD o invalidación de cache

## Cambios de Archivos por PR

### PR #7: Spatie Media Library + Admin Uploads

| Archivo | Acción | Descripción |
|------|--------|-------------|
| `composer.json` | Modificar | Añadir `filament/spatie-laravel-media-library-plugin:^5.0` + `spatie/laravel-medialibrary:^11.0` |
| `app/Models/User.php` | Modificar | Añadir `HasMedia` trait, `registerMediaCollections()` para `avatar` (singleFile) |
| `app/Models/Project.php` | Modificar | Añadir `HasMedia` trait, `registerMediaCollections()` para `screenshots` (multiple, 20 máx) |
| `app/Models/Education.php` | Modificar | Añadir `HasMedia` trait, `registerMediaCollections()` para `certificates` (multiple, 5 máx) |
| `app/Models/Skill.php` | Modificar | Añadir `HasMedia` trait, `registerMediaCollections()` para `icons` (singleFile) |
| `app/Models/Language.php` | Modificar | Añadir `HasMedia` trait, `registerMediaCollections()` para `flags` (singleFile) |
| `app/Models/Link.php` | Modificar | Añadir `HasMedia` (opcional) |
| `app/Models/Experience.php` | Modificar | Añadir `HasMedia` trait, `registerMediaCollections()` para `logos` (singleFile) |
| `app/Models/Course.php` | Modificar | Ya implementado `HasMedia` (de espec) |

### PR #8: Página Portafolio Público (Livewire + Tailwind)

| Archivo | Acción | Descripción |
|------|--------|-------------|
| `resources/views/layouts/portfolio.blade.php` | Crear | Layout del portafolio con meta tags SEO, tema oscuro, Alpine.js para toggle, Tailwind responsive |
| `app/Http/Livewire/Portfolio/PortfolioPage.php` | Crear | Componente Full Page Livewire principal |
| `resources/views/livewire/portfolio/portfolio-page.blade.php` | Crear | Template del componente PortfolioPage |
| `app/Http/Livewire/Portfolio/HeroSection.php` | Crear | Hero section (avatar, nombre, título, resumen) |
| `app/Http/Livewire/Portfolio/SkillsSection.php` | Crear | Skills agrupados por categoría |
| `app/Http/Livewire/Portfolio/ProjectsSection.php` | Crear | Projects cards con screenshots |
| `app/Http/Livewire/Portfolio/ExperienceSection.php` | Crear | Timeline de experiencia de trabajo |
| `app/Http/Livewire/Portfolio/EducationSection.php` | Crear | Cards de educación con certificates |
| `app/Http/Livewire/Portfolio/LanguagesSection.php` | Crear | Barras de nivel de idioma con flags |
| `app/Http/Livewire/Portfolio/StatsSection.php` | Crear | Contadores animados |
| `app/Http/Livewire/Portfolio/LinktreeSection.php` | Crear | Links sociales/professional |
| `app/Http/Livewire/Portfolio/CoursesSection.php` | Crear | Cards de cursos con certificates |
| `routes/web.php` | Modificar | Reemplazar welcome route con `PortfolioPage` Livewire |
| `resources/views/admin/Pages/Portfolio/portfolio-preview.blade.php` | Nuevo | Componente admin para preview del portafolio público |

### PR #9: Preview + Refresco + Multi-idioma + SEO

| Archivo | Acción | Descripción |
|------|--------|-------------|
| `app/Services/AutoTranslationService.php` | Crear | Servicio de traducción usando OpenAI API |
| `app/Services/ContactNotificationService.php` | Crear | Implementación del servicio de notificación de contacto |
| `app/Contracts/ContactNotifierInterface.php` | Crear | Interface ContactNotifierInterface |
| `app/Services/EmailContactNotifier.php` | Nuevo | Implementación por ahora guarda (prepara para Mailtrap) |
| `app/Services/NotificationContactNotifier.php` | Nuevo | Implementación que utiliza implementación admin de Filament para notificaciones |
| `database/migrations/*` | Nuevo | Migraciones para `last_updated` timestamp en User |
| `lang/es.json` | Nuevo | Traducciones españolas |
| `lang/en.json` | Nuevo | Traducciones inglesas (generado automáticamente) |
| `resources/views/layouts/portfolio.blade.php` | Modificar | Añadir idioma, SEO meta tags, Open Graph, Twitter Cards, Schema.org JSON-LD |
| `app/Models/User.php` | Modificar | Añadir campo `last_updated` timestamp |
| `app/Http/Livewire/Portfolio/PortfolioPage.php` | Modificar | Añadir lógica de refresh y auto-traducción |
| `resources/views/admin/Pages/Portfolio/portfolio-preview.blade.php` | Modificar | Añadir preview modal y toggle de idioma |
| `composer.json` | Modificar | Añadir dependencias de OpenAI API, Abrahamutex "twitter-typeahead" (para tooltips de auto-completado) |

## Interfaces / Contratos

### ContactNotifierInterface

```php
<?php

namespace App\Contracts;

use App\Models\ContactRequest;

interface ContactNotifierInterface
{
    public function createNotification(ContactRequest $request): void;
    public function markAsRead(int $id): bool;
    public function getAllNotifications(): array;
}
```

### AutoTranslationService

```php
<?php

namespace App\Contracts;

interface AutoTranslationService
{
    public function translateToEnglish(string $spanishText): string;
    public function translateToSpanish(string $englishText): string;
    public function shouldAutoTranslate(string $content): bool;
    public function canEditManually(int $id): bool;
}
```

## Estrategia de Testing

| Capa | Qué Probar | Enfoque |
|-------|-------------|----------|
| Unit | Métodos de base de datos Livewire, Services, Utils de traducción | PHPUnit con mocks para External API (OpenAI) |
| Integration | Interacción de Componentes Livewire con modelos, public API del portafolio | Feature tests usandoBrowser-like conLivewire \ visit, fill, wait |
| E2E | Workflow completo público del portafolio (vista, toggles, previews) | Cypress-likePHP testing con Fixtures |

### Casos de Prueba Clave

**PR #7 Tests**:
- Spatie Media Library trait attachment/detachment
- Filament SpatieMediaLibraryFileUpload validation
- Media collection constraints (max files, tipos permitidos)

**PR #8 Tests**:
- Eager loading de User con todas las relaciones
- Selección de medios (avatar, screenshots, certificates, etc.)
- Component responsiveness (mobile-first)
- Alpine.js toggles y persistencia de almacenamiento local

**PR #9 Tests**:
- Auto-translation endpoints
- Refresh system timestamps y eventos
- Traducción de plataformas de lenguajes
- SEO meta tags, Open Graph, Twitter Cards

## Migración / Implementación

**No migration requerido** - El portafolio reemplaza welcome.blade.php, no hay cambios en bd para usuarios existentes (el portafolio usa modelo User existente).

**Consideraciones de Feature Flags**:
- Toggle `portfolio.enabled` (boolean, defecto true) para desactivar portal mientras se desarrolla
- Toggle `portfolio.auto_translation.enabled` (boolean, defecto true) para admin desactivar translation OpenAI
- Toggle `portfolio.contact_form.enabled` (boolean, defecto true)

## Preguntas Abiertas

- [ ] Configuración adecuada de cache para translation (Redis) para rendimiento?
- [ ] Slider adecuado de GitHub Issues para seguimiento de estado del portafolio?
- [ ] Cómo manejar traducciones conflictivas entre cache y admin?
- [ ] Implementación adecuada de inicialización del theme para theme system de Filament?
- [ ] ¿Plan adecuado para categorizar modelos de Reportes\Analytics del portafolio?
- [ ] ¿Verificación correcta del ancho de línea del PR usando Herramientas de Línea de Composer para mantener presupuesto?
- [ ] Opciones UI para administradores para asignar prefización específica de idiomas?
- [ ] Aspecto adecuado para exportación de PDF del portafolio para recruiters?
- [ ] ¿Migración lisa para Assets Media Library de existing Image model a Spatie?
- [ ] Mapeo de migración más sencillo para plugins Spatie con reglas kebab-case correctas?
- [ ] ¿Implementación completa de soporte de Email para ContactNotifier?
- [ ] ¿Locales adecuados y Zona Horaria para translations de Auto-traducción?

## Plan de Recuperación

**Paso 1**: Revertir composer.json y composer.lock si alguna PR falla:
```bash
composer restore
rm -rf vendor
cp openspec/changes/public-portfolio-page/backup/composer.json .
cp openspec/changes/public-portfolio-page/backup/composer.lock .
```

**Paso 2**: Limpiar assets generados:
```bash
rm -rf public/build
rm -rf .vite
rm -rf storage/framework/views
```

**Paso 3**: Eliminar cambios de PR:
```bash
# Para PR #7
rm -rf changes/media-library

# Para PR #8
rm -rf resources/views/portafolio
rm -rf app/Http/Livewire/Portfolio

# Para PR #9
rm -rf resources/lang/es.json
rm -rf resources/lang/en.json
rm -rf app/Services/*
```

**Paso 4**: Restaurar original Git state:
```bash
git checkout HEAD -- all
```

**Paso 5**: Restaurar storage:
```bash
php artisan optimize:clear
```

Si PR a mitad de camino, guardar ramas actuales como backup:
```bash
git push origin feature/portfolio-prX-handoff
```

Luego revertir usando:
```bash
git fetch origin

git checkout feature/portfolio-prX-handoff

git rebasePull origin/feature/portfolio-prX-base
```

**Escenarios de Recuperación**:

- **PR #7 falla**: Restaurar composer, limpiando, eliminados models Media Library
- **PR #8 falla**: Limpiar componentes Livewire, assets del portafolio, restablecer welcome.blade.php
- **PR #9 falla**: Limpiar traducciones, servicios, restaurando admin panel en original
- **Todas las PR fallan**: Restaurar completamente todos los cambios usando backup

[Completado] Diseño creado para Página Portafolio Público implementando Stack Laravel 13 + Filament 5 con componentes Livewire, Media Library de Spatie, auto-traducción, y 3-PR cascade.

**Siguiente paso**: Crear tasks (`sdd-tasks`) para dividir implementación en PR slices para 350, 600, 300 líneas correctamente.