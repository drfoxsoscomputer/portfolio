# Portfolio — Denis Piña

Portfolio personal construido con **Laravel 13**, panel de administración **Filament v5**, y gestión de assets multimedia con **Spatie Media Library v11**.

## Stack

| Capa | Tecnología |
|------|-----------|
| **Framework** | Laravel 13 |
| **PHP** | 8.3+ |
| **Base de datos** | SQLite (desarrollo/testing) |
| **Admin Panel** | Filament v5 (español) |
| **Media Library** | Spatie Media Library v11 |
| **Frontend** | Vite + Tailwind CSS + Livewire |
| **Testing** | PHPUnit 12 (92 tests — 316 assertions) |
| **ID** | OpenCode + Engram |

## Requisitos

- PHP 8.3+
- Composer
- Node.js 20+ (para frontend)
- SQLite (incluido con PHP)

## Instalación

```bash
# 1. Clonar el repositorio
git clone https://github.com/drfoxsoscomputer/portfolio.git
cd portfolio

# 2. Instalar dependencias PHP
composer install

# 3. Instalar dependencias frontend
npm install

# 4. Configurar entorno
cp .env.example .env
php artisan key:generate

# 5. Ejecutar migraciones + seeders
php artisan migrate --seed

# 6. Compilar assets
npm run build

# 7. Iniciar servidor de desarrollo
php artisan serve
# => http://localhost:8000
```

## Panel de Administración

El panel está en `/admin` con interfaz en español.

**Primer acceso:**

```bash
# Opción A — Usar el seeder (crea usuario administrador con datos del CV)
php artisan migrate --seed
# El seeder crea un usuario con todos los datos y relaciones precargadas

# Opción B — Crear un usuario desde cero
php artisan make:filament-user
# Te solicitará interactivamente:
#   Name:  (tu nombre)
#   Email: (tu email)
#   Password: (mínimo 8 caracteres)
```

También puedes pasar los datos directamente:
```bash
php artisan make:filament-user \
    --name="Tu Nombre" \
    --email="tu@email.com" \
    --password="tu-contraseña"
```

Una vez dentro del panel, puedes editar los datos del perfil desde el menú superior derecho → **Profile**.

### Recursos del panel

| Grupo | Recursos |
|-------|----------|
| Dashboard | Estadísticas del portafolio |
| Perfil | Edición del perfil · Enlaces de Contacto |
| Habilidades | Skills, Languages |
| Experiencia | Projects, Experiences, Education |
| Administración | Courses |

## Testing

```bash
# Ejecutar toda la suite
php artisan test

# Ejecutar con cobertura
php artisan test --coverage

# Tests específicos
php artisan test --filter=SkillResource
php artisan test --filter=Course
```

**Estado actual: 92 tests, 316 assertions — todos verdes.**

## Modelo de Datos

```
User (singleton — único usuario del portfolio)
├── hasMany → Link          (redes sociales)
├── hasMany → Project       (proyectos, tech_stack JSON)
├── hasMany → Experience    (experiencia laboral)
├── hasMany → Skill         (habilidades con iconos)
├── hasMany → Education     (formación académica)
├── hasMany → Language      (idiomas con banderas)
└── hasMany → Course        (cursos y certificaciones)

Spatie Media Collections:
├── User        → avatar (singleFile)
├── Skill       → icons  (singleFile)
├── Language    → flags  (singleFile)
├── Experience  → logos  (singleFile)
└── Education   → certificates (múltiples archivos)
```

### Ordenamiento por defecto

| Modelo | Orden |
|--------|-------|
| Project | `start_date` DESC |
| Experience | `start_date` DESC |
| Education | `sort_order` ASC |
| Skill, Language, Link | `sort_order` ASC |

## Arquitectura

```
app/
├── Filament/
│   ├── Pages/               ← Páginas standalone del panel
│   ├── Resources/           ← CRUD resources por modelo
│   │   ├── Course/
│   │   ├── Education/
│   │   ├── Experiences/
│   │   ├── Languages/
│   │   ├── Links/
│   │   ├── Projects/
│   │   └── Skills/
│   └── Widgets/             ← Dashboard widgets
├── Models/                  ← Eloquent models + Spatie HasMedia
├── Providers/               ← Service providers
├── ...
database/
├── factories/               ← Model factories con soporte para media
├── migrations/              ← Migraciones
└── seeders/                 ← Seeders con datos del CV real
```

## Características

- ✅ Panel admin completo con Filament v5 en español
- ✅ CRUD completo para todos los modelos del portfolio
- ✅ Upload de imágenes por recurso (iconos, banderas, logos, certificados)
- ✅ Spatie Media Library para gestión de archivos multimedia
- ✅ Datos del CV real poblados via seeder
- ✅ 92 tests de cobertura (unit + feature)
- ✅ Polimorfismo: Imágenes asociables a múltiples modelos
- 🔄 Portafolio público (en desarrollo — Livewire + Tailwind)
- 🔄 Formulario de contacto (en desarrollo)
- 🔄 Multi-idioma (en desarrollo — LibreTranslate + edición manual)

## SDD (Spec-Driven Development)

Este proyecto usa SDD para cambios significativos. Los artifacts están en `openspec/`:

```
openspec/
├── config.yaml
└── changes/
    ├── admin-panel/              ← Admin panel (completado)
    └── public-portfolio-page/    ← Página pública (en desarrollo)
        ├── proposal.md
        ├── spec.md
        ├── design.md
        └── tasks.md
```

## Licencia

MIT
