<?php

namespace Tests\Unit;

use App\Models\Experience;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExperienceTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test que el modelo Experience puede crearse
     */
    public function test_experience_can_be_created(): void
    {
        $user = User::factory()->create();
        $experience = Experience::factory()->create(['user_id' => $user->id]);

        $this->assertModelExists($experience);
        $this->assertInstanceOf(Experience::class, $experience);
        $this->assertEquals($user->id, $experience->user_id);
        $this->assertNotEmpty($experience->company);
        $this->assertNotEmpty($experience->role);
    }

    /**
     * Test que Experience pertenece a la relación User
     */
    public function test_experience_belongs_to_user(): void
    {
        $user = User::factory()->create();
        $experience = Experience::factory()->create(['user_id' => $user->id]);

        $this->assertInstanceOf(User::class, $experience->user);
        $this->assertEquals($user->id, $experience->user->id);
    }

    /**
     * Test que Experience tiene los atributos fillable correctos
     */
    public function test_experience_has_fillable_attributes(): void
    {
        $user = User::factory()->create();
        $experience = Experience::factory()->create(['user_id' => $user->id]);

        $experience->update([
            'company' => 'Test Company',
            'role' => 'Test Role',
            'location' => 'Test Location',
        ]);

        $this->assertEquals('Test Company', $experience->company);
        $this->assertEquals('Test Role', $experience->role);
        $this->assertEquals('Test Location', $experience->location);
    }

    /**
     * Test que Experience tiene el ordenamiento correcto por start_date (descendente)
     */
    public function test_experience_default_ordering(): void
    {
        $user = User::factory()->create();

        // Crear experiencias con diferentes valores de start_date
        Experience::factory()->create([
            'user_id' => $user->id,
            'company' => 'Company A',
            'role' => 'Role A',
            'start_date' => '2024-01-15',
        ]);

        Experience::factory()->create([
            'user_id' => $user->id,
            'company' => 'Company B',
            'role' => 'Role B',
            'start_date' => '2023-06-20',
        ]);

        Experience::factory()->create([
            'user_id' => $user->id,
            'company' => 'Company C',
            'role' => 'Role C',
            'start_date' => '2024-03-10',
        ]);

        $experiences = Experience::all();

        // Orden descendente por start_date: Company C (2024-03-10) primero, luego Company A (2024-01-15), luego Company B (2023-06-20)
        $this->assertEquals('Company C', $experiences[0]->company);
        $this->assertEquals('Company A', $experiences[1]->company);
        $this->assertEquals('Company B', $experiences[2]->company);
    }

    /**
     * Test que el factory crea datos realistas
     */
    public function test_experience_factory_creates_realistic_data(): void
    {
        $user = User::factory()->create();
        $experience = Experience::factory()->create(['user_id' => $user->id]);

        $this->assertNotEmpty($experience->company);
        $this->assertNotEmpty($experience->role);
        $this->assertIsString($experience->company);
        $this->assertIsString($experience->role);
        $this->assertInstanceOf(\DateTimeInterface::class, $experience->start_date);
        $this->assertIsBool($experience->is_current);
    }
}