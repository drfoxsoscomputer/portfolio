<?php

namespace Tests\Concerns;

use App\Models\Course;
use App\Models\Education;
use App\Models\Experience;
use App\Models\Language;
use App\Models\Link;
use App\Models\Project;
use App\Models\Skill;
use App\Models\User;

trait CreatesPortfolioUser
{
    /**
     * Create a user with one record for every portfolio section.
     */
    private function createUserWithRelations(): User
    {
        $user = User::factory()->create([
            'name' => 'Denis Pina Test',
            'title' => 'Full Stack Developer',
            'summary' => 'Building things with Laravel.',
            'location' => 'Caracas, Venezuela',
            'phone' => '+58 414-516-9484',
        ]);

        Link::factory()->create([
            'user_id' => $user->id,
            'label' => 'GitHub',
            'url' => 'https://github.com/drfoxsoscomputer',
            'icon' => 'github',
            'sort_order' => 1,
        ]);

        Skill::factory()->create([
            'user_id' => $user->id,
            'name' => 'Laravel',
            'category' => 'Frameworks',
            'sort_order' => 1,
        ]);

        Project::factory()->create([
            'user_id' => $user->id,
            'name' => 'GameWorld Ecommerce',
            'description' => 'An online store platform.',
            'tech_stack' => ['PHP', 'Laravel'],
            'url' => 'https://gameworld.example',
            'repo_url' => 'https://github.com/drfoxsoscomputer/gameworld',
        ]);

        Experience::factory()->create([
            'user_id' => $user->id,
            'company' => 'Acme Corp',
            'role' => 'Senior Developer',
            'location' => 'Remote',
            'start_date' => '2020-01-01',
            'end_date' => null,
            'is_current' => true,
        ]);

        Education::factory()->create([
            'user_id' => $user->id,
            'institution' => 'Central University',
            'degree' => 'Bachelor',
            'field' => 'Computer Science',
            'sort_order' => 1,
        ]);

        Language::factory()->create([
            'user_id' => $user->id,
            'name' => 'Spanish',
            'level' => 'Native',
            'sort_order' => 1,
        ]);

        Course::factory()->create([
            'user_id' => $user->id,
            'name' => 'Advanced Laravel',
            'institution' => 'Laracasts',
            'date' => '2023-01-15',
            'sort_order' => 1,
        ]);

        return $user;
    }
}
