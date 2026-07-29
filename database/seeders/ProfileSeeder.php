<?php

namespace Database\Seeders;

use App\Models\Education;
use App\Models\Experience;
use App\Models\Language;
use App\Models\Link;
use App\Models\Project;
use App\Models\Course;
use App\Models\Skill;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class ProfileSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $user = User::create([
            'name' => 'Denis Piña',
            'email' => 'daprthefox@gmail.com',
            'password' => Hash::make('asdf1234'),
            'title' => 'Desarrollador Full Stack',
            'location' => 'Venezuela, Lara',
            'phone' => '+58 414-516-9484',
            'summary' => 'Desarrollador con experiencia en múltiples tecnologías y frameworks, especializado en la creación de aplicaciones web escalables y eficientes. Apasionado por la resolución de problemas y el aprendizaje constante.',
            'avatar' => null,
        ]);

        // Create Links
        Link::create([
            'user_id' => $user->id,
            'label' => 'LinkedIn',
            'url' => 'https://linkedin.com/in/denis-drfox-dev',
            'icon' => 'linkedin',
            'sort_order' => 1,
        ]);

        Link::create([
            'user_id' => $user->id,
            'label' => 'GitHub',
            'url' => 'https://github.com/drfoxsoscomputer',
            'icon' => 'github',
            'sort_order' => 2,
        ]);

        Link::create([
            'user_id' => $user->id,
            'label' => 'Portfolio',
            'url' => 'https://drfoxsoscomputer.github.io',
            'icon' => 'globe',
            'sort_order' => 3,
        ]);

        // Create Projects with real data from CV
        Project::create([
            'user_id' => $user->id,
            'name' => 'GameWorld Ecommerce',
            'description' => 'Plataforma completa de comercio electrónico con carrito de compras, procesador de pagos y panel de administración',
            'tech_stack' => ['PHP', 'Laravel', 'MySQL', 'Vue.js', 'Bootstrap'],
            'role' => 'Desarrollador Full Stack',
            'team_size' => 1,
            'url' => null,
            'repo_url' => 'https://github.com',
            'start_date' => '2022-08-01',
            'end_date' => '2023-06-30',
            'is_current' => false,
            'is_featured' => true,
        ]);

        Project::create([
            'user_id' => $user->id,
            'name' => 'Pokedex App',
            'description' => 'Aplicación web para visualizar y buscar Pokémon con API integrada a PokéAPI',
            'tech_stack' => ['React', 'Redux', 'Node.js', 'Express', 'PostgreSQL'],
            'role' => 'Desarrollador Frontend',
            'team_size' => 1,
            'url' => 'https://pokedex-app.com',
            'repo_url' => 'https://github.com/drfoxsoscomputer/pokedex',
            'start_date' => '2023-07-15',
            'end_date' => '2023-12-15',
            'is_current' => false,
            'is_featured' => true,
        ]);

        Project::create([
            'user_id' => $user->id,
            'name' => 'Rick and Morty App',
            'description' => 'Aplicación web interactiva con temática de Rick and Morty, utilizando API pública para datos de personajes',
            'tech_stack' => ['Vue.js', 'Vite', 'JavaScript', 'CSS3', 'HTML5'],
            'role' => 'Desarrollador Frontend',
            'team_size' => 1,
            'url' => 'https://rickandmorty-app.com',
            'repo_url' => 'https://github.com/drfoxsoscomputer/rickandmorty',
            'start_date' => '2024-01-20',
            'end_date' => '2024-06-10',
            'is_current' => false,
            'is_featured' => true,
        ]);

        // Create Experiences
        Experience::create([
            'user_id' => $user->id,
            'company' => 'Soporte Técnico IT',
            'role' => 'Soporte Técnico',
            'description' => 'Soporte técnico de software y hardware, resolución de incidentes, capacitación de usuarios',
            'location' => 'Barquisimeto, Venezuela',
            'start_date' => '2020-01-01',
            'end_date' => null,
            'is_current' => true,
        ]);

        Experience::create([
            'user_id' => $user->id,
            'company' => 'Balanzas América / CALA',
            'role' => 'Analista de Sistemas',
            'description' => 'Desarrollo de sistemas, mantenimiento de aplicaciones, optimización de procesos, análisis de datos',
            'location' => 'Caracas, Venezuela',
            'start_date' => '2016-08-01',
            'end_date' => '2020-04-30',
            'is_current' => false,
        ]);

        // Create Skills organized by category
        $skills = [
            // Lenguajes
            ['name' => 'JavaScript', 'category' => 'Lenguajes', 'sort_order' => 1],
            ['name' => 'PHP', 'category' => 'Lenguajes', 'sort_order' => 2],
            ['name' => 'HTML', 'category' => 'Lenguajes', 'sort_order' => 3],
            ['name' => 'CSS', 'category' => 'Lenguajes', 'sort_order' => 4],
            // Frameworks
            ['name' => 'React', 'category' => 'Frameworks', 'sort_order' => 1],
            ['name' => 'Redux', 'category' => 'Frameworks', 'sort_order' => 2],
            ['name' => 'Laravel', 'category' => 'Frameworks', 'sort_order' => 3],
            ['name' => 'Filament', 'category' => 'Frameworks', 'sort_order' => 4],
            ['name' => 'Vite', 'category' => 'Frameworks', 'sort_order' => 5],
            // DB
            ['name' => 'PostgreSQL', 'category' => 'DB', 'sort_order' => 1],
            ['name' => 'MySQL', 'category' => 'DB', 'sort_order' => 2],
            ['name' => 'Sequelize', 'category' => 'DB', 'sort_order' => 3],
            // Tools
            ['name' => 'Node.js', 'category' => 'Tools', 'sort_order' => 1],
            ['name' => 'Express', 'category' => 'Tools', 'sort_order' => 2],
            ['name' => 'Git/GitHub', 'category' => 'Tools', 'sort_order' => 3],
            // Metodologías
            ['name' => 'SCRUM', 'category' => 'Metodologías', 'sort_order' => 1],
        ];

        foreach ($skills as $skillData) {
            Skill::create(array_merge(['user_id' => $user->id], $skillData));
        }

        // Create Education with Spatie media
        $education = Education::create([
            'user_id' => $user->id,
            'institution' => 'IU "Andrés Eloy Blanco"',
            'degree' => 'TSU',
            'field' => 'Informática',
            'description' => 'Técnico Superior Universitario en Informática',
            'start_date' => '2002-01-01',
            'end_date' => '2005-12-31',
            'is_current' => false,
            'sort_order' => 1,
        ]);
        
        // Add certificate media for education
        $education->addMediaFromString('certificate-data-1')
            ->usingFileName('certificate-1.jpg')
            ->toMediaCollection('certificates');
        
        $education->addMediaFromString('certificate-data-2')
            ->usingFileName('certificate-2.pdf')
            ->toMediaCollection('certificates');
        
        $education->addMediaFromString('certificate-data-3')
            ->usingFileName('certificate-3.png')
            ->toMediaCollection('certificates');

        Education::create([
            'user_id' => $user->id,
            'institution' => 'Henry Bootcamp',
            'degree' => 'Full Stack Developer',
            'field' => 'Programación',
            'description' => 'Bootcamp intensivo de 800 horas en desarrollo full stack',
            'start_date' => '2023-06-01',
            'end_date' => '2024-02-28',
            'is_current' => false,
            'sort_order' => 2,
        ]);

        // Create Languages
        Language::create([
            'user_id' => $user->id,
            'name' => 'Español',
            'level' => 'Nativo',
            'sort_order' => 1,
        ]);

        Language::create([
            'user_id' => $user->id,
            'name' => 'Inglés',
            'level' => 'Básico',
            'sort_order' => 2,
        ]);

        // Create Courses
        Course::create([
            'user_id' => $user->id,
            'name' => 'Técnicas Avanzadas de Laravel',
            'institution' => 'Laracasts',
            'date' => '2024-06-15',
            'description' => 'Curso avanzado sobre Laravel: queues, broadcasting, testing, y optimización.',
            'sort_order' => 1,
        ]);

        Course::create([
            'user_id' => $user->id,
            'name' => 'Desarrollo Full Stack con React y Node.js',
            'institution' => 'Platzi',
            'date' => '2023-11-20',
            'description' => 'Programa completo de desarrollo full stack cubriendo React, Node.js, Express y PostgreSQL.',
            'sort_order' => 2,
        ]);
    }
}
