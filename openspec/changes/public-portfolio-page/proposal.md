# Proposal: Página Portafolio Público

## Intención

Crear una página portafolio/CV pública que reemplace la página de bienvenida predeterminada de Laravel en la raíz `/`, permitiendo que el público visite el portafolio profesional del usuario con datos gestionados previamente en el panel de administración Filament.

## Alcance

### En Alcance
- Página pública principal en `/` con visualización completa del portafolio
- Tres PRs planificados siguiendo estándar de 400 líneas por PR:
  - **PR #7 (~350 líneas)**: Instalación y configuración de Spatie Media Library plugin
  - **PR #8 (~600 líneas)**: Implementación completa de la página portafolio con componentes Livewire
  - **PR #9 (~300 líneas)**: Funcionalidades polish: preview, actualización inteligente, i18n, SEO, tema oscuro
- Componentes modulares Livewire: PortfolioPage padre con Hero, Skills, Projects, Experience, Education, Languages y Stats sections
- Integración con Spatie Media Library para avatares, screenshots de proyectos y certificados
- Visualización en nuevo tab + modal de preview en admin
- Actualización automática de página cuando admin modifica/edita/elimina datos (refresco inteligente)
- Toggles multilingüe español/inglés en página pública
- Diseño responsive mobile-first con CSS de impresión para recruiters
- Temas oscuro con persistencia de preferencias Alpine.js + localStorage
- Animaciones de scroll fade-in con Intersection Observer y contadores animados
- Meta tags básicas Open Graph y Twitter Cards para SEO
- Siempre visible cuando hay datos, sin toggle de publicación

### Fuera de Alcance
- Registro de nuevos usuarios
- Implementación de módulos de autenticación adicionales más allá del básico de Filament
- Paneles de administración para gestión del portafolio (ya existen)
- funcionalidad de publicación/ocultación del portafolio
- Feature de suscripción o autenticación adicional a usuario existente

## Capacidades

> Esta sección es el CONTRATO entre propuesta y specs fases.
> El agente sdd-spec lee esto para saber exactamente qué archivos de spec crear o actualizar.
> Investigar `openspec/specs/` antes de rellenar esto.

### Nuevas Capacidades
- public-portfolio-page-media-management: Gestión de imágenes con Spatie Media Library (avatares, screenshots, certificados)
- public-portfolio-page-livewire-components: Componentes Livewire completos para cada sección del portafolio
- public-portfolio-page-responsive-animations: Diseño responsive con animaciones y tema oscuro
- public-portfolio-page-seo-multilingual: Meta tags SEO, Open Graph y soporte multilingüe
- public-portfolio-page-admin-integration: Preview de portafolio e integración de refresco inteligente con admin

### Capacidades Modificadas
- portfolio-core-data-access: Actualizar acceso a datos del modelo User para incluir relaciones Spatie Media Library

## Enfoque

Implementar utilizando Livewire 3 Full Page Components con componentes separados por sección (Hero, Skills, Projects, Experience, Education, Languages, Stats) cada uno leyendo datos del modelo User con sus relaciones Spatie Media. Usar Alpine.js para tema oscuro con persistencia localStorage, Intersection Observer para animaciones de scroll, e implementar SEO con meta tags completas. Autorrecargar la página mediante pub/sub de Alpine cuando datos relevantes son actualizados en admin. Usar rutas Laravel estándar sin middleware adicional.

## Áreas Afectadas

| Área | Impacto | Descripción |
|------|--------|-------------|
| `composer.json` | Nuevo | Dependencia `filament/spatie-laravel-media-library-plugin:^5.0` |
| `app/Models/User.php` | Modificado | Añadir HasMedia trait y define medios para avatar collection |
| `app/Models/Project.php` | Modificado | Añadir HasMedia trait y define medios para screenshots collection |
| `app/Models/Education.php` | Modificado | Añadir HasMedia trait y define medios para certificates collection |
| `routes/web.php` | Modificado | Reemplazar welcome route con puerto de portafolio Livewire |
| `resources/views/` | Nuevo | Componentes Livewire para cada sección del portafolio |

## Riesgos

| Riesgo | Probabilidad | Mitigación |
|------|------------|------------|
| Compatibilidad del plugin Spatie con Filament 5 | Medio | Probar compatibilidad en las primeras fases del PR antes de seguir con otras fases |
| Sobrecarga de performance con múltiples imágenes de medios | Medio | Implementar lazy loading y compressión de imágenes, cachear galerías |
| Sesgos de diseño entre tema claro y oscuro | Bajo | Validar todos los componentes con ambos temas usando inspección de atributo de tema |
| Calendario de desarrollo ajustado a 400 líneas por PR | Medio | Planificar tareas cuidadosamente para cumplir presupuesto, usar PRs en cascada como planeado |
| Diferencias entre traducciones español de Filament | Bajo | Usar traducciones neutrales de Filament, no Rioplatense |

## Plan de Recuperación

1. Revertir todas las dependencias de composer y cambios en C:
   - `composer.json` → último commit estable antes del PR
   - `composer.lock` → eliminar y regenerar
   - Eliminar `vendor/` completamente con `rm -rf vendor`
2. Eliminar todo el código nuevo:
   - `resources/views/portafolio/` completo
   - `app/Http/Livewire/` completo
   - `routes/web.php` - restaurar ruta welcome original
3. Eliminar assets/metadatos generados:
   - `.vite` y cache de build
   - `public/build/` cache
4. Limpiar registration de Spatie Media Library de modelos si se agregaron
5. Eliminar `app/Models/*` modificados si se crearon nuevos traits/models
6. Restaurar `resources/views/admin/*` si se modificaron
7. Restaurar `lang/` del panel de admin a estado anterior

Si se detiene a mitad de un PR, cada slice debe poder revertarse independientemente usando estos pasos.

## Dependencias

- `filament/spatie-laravel-media-library-plugin:^5.0` (nuevo)
- Plugin existente de Filament v5
- Laravel 13 + PHP 8.3 (existente)
- SQLite (existente)

## Criterios de Éxito

- [ ] Página pública en `/` muestra completamente portafolio con todos los datos del usuario
- [ ] Todas las secciones (Hero, Skills, Projects, Experience, Education, Languages, Stats) renderizan correctamente
- [ ] Gestión de imágenes Spatie Media Library funciona (subir, previsualizar, reordenar)
- [ ] Preview de portafolio en admin abre en nuevo tab y funciona modal
- [ ] Refresco inteligente de página funciona cuando datos son actualizados en admin
- [ ] Toggles de tema claro/oscuro y español/inglés funcionan con persistencia
- [ ] SEO meta tags, Open Graph y Twitter Cards están presentes
- [ ] CSS de impresión funciona correctamente para impresión
- [ ] Componentes Livewire están responsivos en móvil, tablet y desktop
- [ ] Animaciones de scroll y contadores funcionan
- [ ] PR #7 está debajo de 350 líneas, PR #8 debajo de 600 líneas, PR #9 debajo de 300 líneas