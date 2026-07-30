# Especificación: Página Portafolio Público

## Resumen de la Especificación

Esta especificación detalla la implementación de la página portafolio público que reemplaza la página de bienvenida predeterminada de Laravel en `/`. La implementación está dividida en tres PRs siguiendo la metodología SDD:

- **PR #7 (~350 líneas)**: Instalación y configuración de Spatie Media Library plugin
- **PR #8 (~600 líneas)**: Implementación completa de la página portafolio con componentes Livewire
- **PR #9 (~300 líneas)**: Funcionalidades polish: preview, refresco inteligente, i18n, SEO, tema oscuro

## Especificaciones por PR

---
## PR #7: Spatie Media Library + Admin Uploads

### 7.1 Aceptación (del PR)

- [ ] `filament/spatie-laravel-media-library-plugin:^5.0` y `spatie/laravel-medialibrary:^11.0` están instalados y se pueden ejecutar `composer update`
- [ ] Modelo `User` tiene `HasMedia` trait con colección `avatar`
- [ ] Modelo `Project` tiene `HasMedia` trait con colección `screenshots`
- [ ] Modelo `Education` tiene `HasMedia` trait con colección `certificates`
- [ ] Configuración `SpatieMediaLibraryFileUpload` reemplaza campos de imagen personalizados en:
  - Página de edición de perfil de Usuario (avatar)
  - Páginas de gestión/lista de Proyectos (screenshots)
  - Páginas de gestión/lista de Educación (certificates)
- [ ] Modelos estándar `User`, `Project`, `Education` actualizados con traits y migraciones para Spatie
- [ ] Seeders y factories actualizados para generación de datos de medios de prueba
- [ ] Tests actualizados para manejar subida y gestión de archivos con Spatie Media Library

### 7.2 Requisitos de Datos

- **Tablas de base de datos**: Tablas de Spatie Media Library se crean automáticamente:
  - `media` (archivos multimedia)
  - `media_has_models` (polimórfica)
- **Modelos**: Modelo `User` (colección `avatar:avatar_image`), `Project` (`screenshots:project_screenshot`), `Education` (`certificates:education_certificate`)
- **Relaciones**: One-to-many polimórfica inversa de `Media` a modelos
- **Migraciones**: Migraciones y reconfiguración para Spatie Media Library sin cambios manuales en schema
- **Semántica de colecciones**: Información de colección de modelo definida per colección usando `hasMedia()` y `getMedia('avatar')`
- **Up/Down**: Sin demoliciones necesarias para downgrade de Spatie

### 7.3 Requisitos de UI

- **Uploaders**: Componentes `SpatieMediaLibraryFileUpload` reemplazan formularios `Image` y campos de archivo personalizados
- **Validación de UI**: Min/max de archivos por colección, validación de tipo de archivo, límite de tamaño
- **Previsualización**: Miniaturas del media subido en vista de lista admin
- **Reordenamiento**: Herramientas de reordenamiento disponibles donde collection soporte reordenamiento

### 7.4 Restricciones Técnicas

- Mantener modelo `Image` (polimórfico `imageable`) pero usarlo solo para media legacy; NO usarlo para nuevos uploads
- Todo nuevo upload usa trait `HasMedia` con colecciones appropriadas
- Soporte para imágenes numerosas por colección (ej: muchos certificados)
- Actividad admin: File uploads en admin triggers validation y persistencia de medios de Spatie
- Nivel de linea de ~350 para PR #7

---
## PR #8: Componentes Públicos de Página Portafolio

### 8.1 Aceptación (del PR)

- [ ] Componente Livewire padre `PortfolioPage` bajo `app/Livewire/Portfolio/` implementado con layout `{resources/views/layouts/portfolio.blade.php}`
- [ ] Componente de página Livewire `PortfolioPage` bajo `{resources/views/livewire/portfolio/portfolio-page.blade.php}` implementado
- [ ] Componente hijo `HeroSection` con avatar, nombre, título, resumen, links sociales (usando media de Spatie para avatar)
- [ ] Componente `SkillsSection` con habilidades grupadas por categoría, toggle de filtros Alpine, responsive grid
- [ ] Componente `ProjectsSection` con grid cards con screenshot, tech_stack, links (usando media Spatie para project screenshots)
- [ ] Componente `ExperienceSection` con timeline vertical de experiencias
- [ ] Componente `EducationSection` con cards con certificados (usando media Spatie para education certificates)
- [ ] Componente `LanguagesSection` con barras de nivel
- [ ] Componente `StatsSection` con contadores animados (años experiencia, proyectos, tecnologías)
- [ ] Ruta `GET /` registrada en `routes/web.php` en pointing a `PortfolioPage` Livewire
- [ ] Página visible en `/` reemplaza bienvenida predeterminada
- [ ] Tema oscuro con toggle usando Alpine.js y localStorage persistencia
- [ ] Diseño responsive mobile-first con Tailwind, CSS de impresión
- [ ] Animaciones: fade-in en scroll con Intersection Observer
- [ ] Componentes responsivos en mobile, tablet, desktop (~600 líneas)

### 8.2 Requisitos de Datos

- **Datos del modelo User**: Modelo User debe tener traits `HasMedia` con colecciones apropiadas
- **Relaciones**: Experiencia (via `experiences()`), Proyectos (via `projects()`), Educación (via `education()`), Habilidades (via `skills()`), Idiomas (via `languages()`)
- **Media de Spatie**: Selectores de medios apropiados para:
  - Avatar: `getFirstMediaUrl('avatar')`
  - Project screenshots: `project->getAllMedia('screenshots')`
  - Education certificates: `education->getAllMedia('certificates')`
- **Componentes Livewire**: Componentes livewire leen datos del modelo User con Eager Loading para relaciones
- **Paginación**: No aplica, se carga todo

### 8.3 Requisitos de UI

- **Componentes**: Componentes Livewire responsivos sin lag Visual, contenadores mobile-first
- **Estilo**: Tema oscuro inicial con toggle, comportamiento smooth del tema
- **Animación**: Intersection Observer scroll reveal, contadores animados, hover y transition states smooth
- **Navegación**: Go back to admin panel en UI,
- **Controles**: Toggle de tema claro/oscuro en header, toggle de idioma español/inglés opcionales (préstamo del PR #9)
- **Print CSS**: Optimize para impresión en printers, vector fonts, centrar todo

### 8.4 Restricciones Técnicas

- Solo Alpine.js para tema oscuro y localStorage persistencia
- UI optimizado para móviles primero, ningún layout desktop first
- Intersection Observer for scroll effects, naive fade-in permitido
- Contadores animados usando Alpine.js o temporizadores setInterval
- Camino de rutas: `routes/web.php` (completamente reemplazada)
- Nivel de linea de ~600 para PR #8

---
## PR #9: Funcionalidades Polish, Preview y SEO

### 9.1 Aceptación (del PR)

- [ ] Botón \"Ver portafolio\" en header admin abre en nuevo tab, version livewire is available
- [ ] Modal de preview en admin opcionalmente muestra versión pública del portafolio
- [ ] Refresco: cuando admin guarda, crea, elimina Item de Portafolio (User, Project, Education, etc.), un timestamp `LastUpdated` es actualizado
- [ ] La página pública detecta actualización de timestamp y re-renderiza automáticamente cuando cambio detectado
- [ ] Toggle multilingüe (español/inglés) con sistema de archivos lang plano:
  - `resources/lang/es.json` y `resources/lang/en.json` creados
  - Toggle de Alpine en header de página para cambio de idioma
  - Texto UI usa `{{ __(...) }}`
  - Traducciones de Filament subsisten efectivamente no asignadas
  - Contenido CV stays como-is (user entries in cualquiera)
- [ ] SEO: Meta tags + OpenGraph en layout head, título de página, descripción, imagen OG, Twitter Card
- [ ] CSS de impresión final para print preview
- [ ] Botón de preview del admin panel a la página pública, modal de preview opcional
- [ ] Implementar cuando modelos de portafolio son actualizados un timestamp interno se dispara y la versión pública refresh al detectarlo
- [ ] Nivel de linea de ~300 para PR #9

### 9.2 Requisitos de Datos

- **Timestamps**: `LastUpdated` timestamp agregados a modelos portafolio relevantes:
  - Modelo `User` (portafolio principal)
  - Modelo `Project` (screen shots, medio)
  - Modelo `Education` (certificados, medio)
- **Datos de idioma**: Archivos JSON de archivos plano para traducciones (spanish/inglés)
- **Cache de SEO**: 
  - Meta tags SEO generadas por UI basados en data de modelo (meta title, description, og:image del avatar)
  - Fallback a traducciones por defecto
- **Órdenes de data**: Tal como en spec del PR #8, pero puede agregarse paginación/paginación para larga lista de items

### 9.3 Requisitos de UI

- **Preview**: 
  - Botón del header admin que abre `/` en nuevo tab y opener modal.
  - Modal puede ser primer option o tabbed modal (admin vs public version)
  - El modal puede ser abierto desde admin o triggerada automáticamente si admin&apos;s panel tiene cambios y hay viewer activos
- **Refresco**: 
  - Evento de actualizando/disparando desde admin (API avanzada de Filament) actualiza timestamp.
  - La página pública es un efecto polling de cambio (ej: caché, o vía push/realtime)
- **Idioma**: Toggle en header de página (dropdown o switch) con persistencia localStorage.
- **SEO/Meta**: Cada página y cada componente viéndose have meta tags en la `<head>` (titulo, descripción, og:image, og:description, og:url, og:type, og:locale, og:locale:alternative) y Twitter Card (
  - Twitter Card:
    - card: `summary_large_image`
    - title: `{{ __("portfolio.meta.title") }}`
    - description: `{{ __("portfolio.meta.description") }}`
    - image: avatar mediaUrl from User
    - site: `@Handle` from public link?
)
- **Impresión**: CSS privado para imprimir con colores B/W, separar enlaces si no queridos, imprime avatar, nombre, perfil, social links, bibliografía? Estilo de impresión usable para recruiters.
- **Controles**: Toggle de tema claro/oscuro y toggle de idioma, botón del header admin de preview del portafolio público del la UI.

### 9.4 Restricciones Técnicas

- Idioma: solo sistema de archivo plano JSON, no Laravel Translations(), adecuado para multi-idioma
- Actualización: timestamp cada time a model is saved (records `updated_at` default o oculto timestamp).
- Nuevo modelo `LastUpdated` agregado al modelo User update pipeline y declaración observability events de updates (Filament observes)
- Active polling o use CDN cache invalidation para refrescar versión pública cuando data cambiada.
- Economa de linea: ~300 líneas.

---
## Criterios de Éxito para la Implementación

### 9.1 Validación Técnica

- [ ] Componentes de Livewire renderizan completamente bajo `resources/views/livewire/portfolio/portfolio-page.blade.php`
- [ ] Layout portfolio desde `resources/views/layouts/portfolio.blade.php` contiene slots apropiados para secciones
- [ ] Todas las secciones (Hero, Skills, Projects, Experience, Education, Languages, Stats) muestran data según modelos
- [ ] Todas las colecciones de medios de Spatie se cargan correctamente (User.avatar, Project.screenshots, Education.certificates)
- [ ] Preview del portafolio abre correctamente en nuevo tab desde admin panel
- [ ] Feature de refresh inteligente detecta cambios del admin panel y re-renderiza versión pública
- [ ] Tema claro/oscuro con toggle Alpine y persistencia localStorage funciona
- [ ] Toggle multilingüe español/inglés en página pública con persistencia correcta
- [ ] Meta tags SEO completas y OG/Twitter Card aparecen en head
- [ ] CSS de impresión genera página limpia en print sin sticky elements, breakpoints soportados

### 9.2 Controles de Calidad

- [ ] PR #7: < 350 líneas, cumple niveles de PR
- [ ] PR #8: < 600 líneas, cumple niveles de PR
- [ ] PR #9: < 300 líneas, cumple niveles de PR
- [ ] Todas las instalaciones de plugin son reversibles
- [ ] Plan de recuperación confirmada con respaldo de git (rebasePull)
- [ ] Todas las rutas validan de forma pasajera y aseguran que página pública solo visible cuando modelos de portafolio tienen data

### 9.3 Puntuación de Riesgo

- **Compatibilidad Spatie/Filament**: Riesgo medio → saltar `SpatieMediaLibraryFileUpload` si accordion problematico
- **Sobrecarga de performance con muchas imágenes**: Riesgo Medio → implementar lazy load y caché, recomprobar translations y translations impact
- **Sesgos de diseño entre tema y claro**: Riesgo Bajo → verificar todos los componentes en alternativos del tema
- **Calendario de plan**: Riesgo Medio → planear cuidadosamente el merge, confirmar cascada PRs (PR7 → PR8 → PR9)
- **Traducciones español de Filament**: Riesgo Bajo → usar traducciones neutrales de Filament

---
## Plan de Recuperación

### 10.1 Procedimiento de Reversión

1. **Revertir Composer**:
   - `composer.json` revertido al último commit estable antes del PR (pre-PR)
   - `composer.lock` : eliminar y `composer update`
   - `rm -rf vendor`
2. **Limpiar Assets/Cache**:
   - Eliminar `.vite` y cache de build
   - `rm -rf public/build`
3. **Eliminar Código Nuevo**:
   - `rm -rf resources/views/portafolio` (todos los archivos del layout)
   - `rm -rf app/Http/Livewire/Portfolio` (todos los componentes)
4. **Rutas**: 
   - `routes/web.php` restaurado al original con `Route::view('/','welcome')`
   - `web.php` mejoras excepto restauradas con las nuevas `PortfolioPage` Livewire
5. **Modelos**: 
   - Modelos `User`, `Project`, `Education` restaurados al original;
   - `HasMedia` traits removidos, modelo `SpatieMediaLibraryFileUpload` reemplazado con `"default"` con base de `Image`
6. **Admin Views**: 
   - `resources/views/admin/*` restaurado al original estado
   - Modelos `SpatieMediaLibraryFileUpload` reemplazados con formularios o "file" fields en admin (según original estado)
7. **Traducciones**: 
   - `resources/lang/es.json` y `resources/lang/en.json` eliminados, traducida por defecto en Filament restaruada

### 10.2 Protecciones

- Editar en backend: git diff para cada PR manejado facilmente
- Si un PR está a mitad de camino:
  - Guardar las comitades del PR
  - Fostearlas en una backup branch
- Revenir de un PR a base usando `git reset --hard` si necesario

---
## Consideraciones Futuras

### 11.1 Funcionalidades Postergadas 

- (Postergado explícitamente del scope principal)
  - Próximamente necesarios: Conectores del Social/Location links.
  - Agregado de CV PDF para texto.
  - Implementación de carousel/paginación (screenshots de proyectos, certificados que tienen muchas imágenes)
  - Feature de conversión del portafolio en formato para compartir al celular celular
  - Feature de descargar/copiar CV del portafolio público

### 11.2 Opciones Deshabilitadas

- Registro de Usuarios: fuera del scope (usando Filament admin)
- Autenticación / Suscripción: fuera del scope
- Panel de administración para gestión de portafolio nuevo: [Ya existe]

### 11.3 Migraciones

- Modelo `Image` legacy puede ser migrado en una versión futura, pero sin usarse de nuevo
- Modelo `SpatieMediaLibraryFileUpload` podría poderse utilizado eventualmente para nuevos uploads

---
## Artifactos

### 12.1 Paths de Archivos (OpenSpec)

```
openspec/changes/public-portfolio-page/spec.md
openspec/changes/public-portfolio-page/proposal.md
openspec/config.yaml
```

### 12.2 Pautas de Artefacto

- Todos los artefactos son Español Web
- Pistas de Markdown para futura edición y diferenciación de versiones
- Artefactos son reversibles

---
## Restricciones Técnicas y Direccionamiento

### 13.1 Mapeo de Recursos Estándar

- **Tiempo de Desarrollo**: <= Calendario de 3 weeks para 3 PR (1 week cada PR)
- **Líneas de Código**: Arriba de 350, 600, 300 según línea de PR requerida, de forma POR PR o de forma que cumpla por PR
- **Difusión**: Lanzado en cascada (PR7 → PR8 → PR9), validating PR7 en admin y después PR8 en público,
- **Depuración**: validando cambios PR7, PR8, PR9 con tests y Bitbucket CI

### 13.2 Seguimiento de Monitoreo de Riesgo

- Monitorear para cada PR las líneas de PR
snails
- Documentos: checks adicionales listados en "evaluación de estimación", haciendo clara las aduanas mitigantes
- Controlar en avances focales: futuro reducto, funcionalidad de refresh del portafolio, comentarios del tema de darkmode, tema

---
## Conclusiones y Uso Futuro

El diseño prioriza:

- Paths de migration limpios optimizados para PR cascade sys
- UI de frontend modular y temas dividido en secciones Livewire 
- Uso de colección de media de Spatie optimizado para cascada PR
- Basado en español como base (con traducción para UI también en español),
- Diseño para impresora apropiada para recruiters.

---
## Directrices de Commit y Git

- Commit inicial para cada PR hub: commit de commit de origen simple (ej: "PR #7: Spatie Media Library", "PR #8: Página pública del portafolio", "PR #9: Funcionalidades Polish")
- Commit rebasedPull efectivo al PR base (ej: `feature/admin-panel-pr6` para PR#7, PR#7 rama para PR#8)
- Nada de commits mixto
- Pre-commit hook para test ejecutado apropiadamente
- Usar nombres de archivos finalizados sin adjetivos y sólidas UI
- Nombres de ramas: conforme a `feature/portfolio-prX-*`
- Remover casillas de auxiliar en feature branch PRs, conservar mismo en main/main

---
## Conclusión

La especificación detalla cómo implementar los cambios del portafolio público usando el plugin filament/spatie-laravel-media-library-plugin:^5.0 y componentes Livewire. Las documentación para cada PR incluye aceptación, data, UI, technical constraints, accompanied con planes de rollback y future considerations. La cascada PR implementa el sistema usando cascade de PR7 → PR8 → PR9 como detallado.

Implementar en cliente según spec.
