# Proposal: Administración Panel Filament

## Intención

Implementar un panel de administración Filament para gestionar el portafolio profesional, facilitando el mantenimiento del contenido con interfaces de usuario en español y respetando el patrón singleton del modelo Profile.

## Alcance

### En Alcance
- Panel de administración Filament instalado y configurado en `/admin`
- Página de dashboard con resumen del portafolio
- Recurso singular de Perfil (solo página de edición) con gestión de relaciones para Links, Proyectos, Experiencias, Habilidades, Educaciones, Lenguajes e Imágenes
- Recurso de Projectos con campos de fecha/boolean/text/array y RelationManager para Imágenes
- Recurso de Experiencias con campos similares a Projectos
- Recurso de Habilidades con campos basic
y Management
- Recurso de Educaciones con campos basicos y RelationManager para Imágenes
- Resources de Links y Lenguajes con Basic CRUD, Labels en español
- Configuración de relaciones personalizadas para gestionar imágenes (avatares, screenshots, certificados)
- Autenticación básica del panel (sin registro de usuarios, sin Socialite)
- Seeders y factories de prueba completas
- Traducciones completas del panel a español

### Fuera de Alcance
- Registro de usuarios (sin User Resource)
- Permisos Spatie (básicos solo de Filament)
- Multitenancy
- Auth Socialite o registro de usuarios completo
- Panel de acceso multiple tenant
- Page de index/show para Recursos (excepto Profile singular)
- Pages de index/show para Recursos (excepto Profile singular)

## Capacidades

> Esta sección es el CONTRATO entre propuesta y specs fases.
> El agente sdd-spec lee esto para saber exactamente qué archivos de spec crear o actualizar.
> Investigar `openspec/specs/` antes de rellenar esto.

### Nuevas Capacidades
- admin-panel-dashboard: Dashboard del panel de administración con resumen del portafolio
- admin-panel-profile-management: Resource singular de Profile para edición (solo página edit)
- admin-panel-projects-management: Resource de Projectos con gestión de RelationManager para Imágenes (polimorficas)
- admin-panel-experiences-management: Resource de Experiencias con campos de fecha/boolean
- admin-panel-skills-management: Resource de Habilidades (basic CRUD)
- admin-panel-educations-management: Resource de Educaciones con RelationshipManager de imágenes
- admin-panel-links-management: Resource de Links (idioma español)
- admin-panel-languages-management: Resource de Lenguajes (idioma español)

### Capacidades Modificadas
- portfolios-core-models: Patrón singleton existente respetado sin cambios

## Enfoque

Implementar Filament v5 en Laravel 13 con PHP 8.3, respetando el patrón singleton del modelo Profile (ProfileObserver). Usar la gestión de relaciones nativa de Filament para imágenes polimorficas. Crear recursos con validación y elementos de UI en español. Seguir el patrón feature-branch-chain en develop con máximo 400 líneas por PR.

## Áreas Afectadas

| Área | Impacto | Descripción |
|------|--------|-------------|
| `composer.json` | Nuevo | Dependencia `filament/filament:~5.0` |
| `routes/console` | Nuevo | `php artisan filament:install` |
| `config/filament` | Nuevo | Configuración del panel, tema, branding español |
| `app/Models/*` | Sin cambios | Respetar el patrón singleton de Profile |
| `resources/views/admin/*` | Nuevo | Views del panel completas con diseño responsive |
| `database/migrations/*` | Sin cambios | Mantener schema existente |
| `tests/` | Nuevo | Tests de Filament completas con fixtures de seeders |

## Riesgos

| Riesgo | Probabilidad | Mitigación |
|------|------------|------------|
| Sobrecargar patrón singleton al crear profiles en admin | Medio | Intentar guardar un Profile nuevamente retornará error/saldrá página, sin ruptura DB |
| Saltarse una migración al momento de instalar Filament | Bajo | Usar `php artisan migrate --force` después de instalar Filament |
| Manejar relaciones polimorficas de imágenes con Filament | Medio | Usar RelationManager de Filament para Profile, Project y Education |
| Coordinar atributos con Español con traducciones de Filament | Bajo | Usar translations de Filament `es.json` para ui y inputs |
| Integrar nuevas validaciones y límites en admin con 400 PR | Medio | Usar 7 PRs en cascada como planeado, cada uno bajo presupuesto |

## Plan de Recuperación

1. Revertir `composer.lock` y `composer.json` al último commit estable 
2. Eliminar `vendor/` completamente con `rm -rf vendor` 
3. Eliminar `openspec/` completamente con `rm -rf openspec` 
4. Eliminar `database/seeders/AdminPanel*` si se crearon nuevos 
5. Eliminar `config/filament.php` completo 
6. Revertir cambios en `resources/views/admin/` completo 
7. Eliminar `phpunit.xml` actualizado y restaurar original 
8. Revertir `routes/console` de `filament:install` 
9. Restaurar `routes/web.php` original (sin rutas admin) 
10. Limpiar `database/factories/AdminPanel*` si se crearon 

Si se detiene a mitad de un PR, cada slice debe poder revertarse independientmente.

## Dependencias

- `filament/filament:~5.0` (panel base)
- Laravel 13.22.0 (existente)
- SQLite (existente)
- PHP 8.3.31 (existente)
- Traducciones `filament/filament:es.json` (spanish)

## Criterios de Éxito

- [ ] Panel de administración accesible en `/admin` con UI completamente española
- [ ] Perfil singleton puede ser editado en `/admin/profile`
- [ ] Todas las relaciones (Links, Proyectos, Experiencias, Habilidades, Educaciones, Lenguajes) funcionan con filaments RelationManager
- [ ] Imágenes son gestionables como columns attach de RelationManager en Projects, Educations, y perfiles (avatares)
- [ ] Autenticación básica funciona (login/out, acceso roles)
- [ ] Tests de Filament completas pasan todas las 400+ Features/specs
- [ ] Dashboard muestra resumen de portolfio (stats)
- [ ] Traducciones completas: Labels, msgs, UI en español
- [ ] Tests unitarios para cada Resource (incluido factory, seeder, validacion)
- [ ] PR #1 es debajo de 400 lines, feature-branch-chain en develop

## Metodología

- Implementar en `develop` usando Feature Branch Chain strategy
- PR #1: Installación base del panel + dashboard + auth + User seeder + tests (~280 lineas)
- PR #2: Profile singular page + form + save handler + tests (~300 lineas)
- PR #3: Links + Skills + Languages Resources (~380 lineas)
- PR #4: Projects + Experience Resources (~390 lineas)
- PR #5: Education Resource + Image RelationManager + Profile avatar (~350 lineas)
- Cada PR se revisa independientemente, con posibilidad de rollback a develop si falla
- Solución completa del sistema tal que existe hoy

## Notas Técnicas

- Usar el patrón existente de factories y seeders (ProfileSeeder, ProfileFactory)
- No modificar schema de database, respetar relaciones y columnas existentes
- Mantener patrón singleton: ProfileObserver incrementa error si se intenta crear segundo
- Gestión de imágenes: usar modelo Image existente como morphMany (filament's RelationshipManager)
- Español: no traducir a Rioplatense, usar neutral/professional Spanish como estandar de Filament