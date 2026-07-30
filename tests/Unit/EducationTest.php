<?php

namespace Tests\Unit;

use App\Models\Education;
use App\Models\Image;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EducationTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test que el modelo Education puede crearse
     */
    public function test_education_can_be_created(): void
    {
        $user = User::factory()->create();
        $education = Education::factory()->create(['user_id' => $user->id]);

        $this->assertModelExists($education);
        $this->assertInstanceOf(Education::class, $education);
        $this->assertEquals($user->id, $education->user_id);
        $this->assertNotEmpty($education->institution);
        $this->assertNotEmpty($education->degree);
        $this->assertIsInt($education->sort_order);
    }

    /**
     * Test que Education pertenece a la relación User
     */
    public function test_education_belongs_to_user(): void
    {
        $user = User::factory()->create();
        $education = Education::factory()->create(['user_id' => $user->id]);

        $this->assertInstanceOf(User::class, $education->user);
        $this->assertEquals($user->id, $education->user->id);
    }

    /**
     * Test que Education tiene los atributos fillable correctos
     */
    public function test_education_has_fillable_attributes(): void
    {
        $user = User::factory()->create();
        $education = Education::factory()->create(['user_id' => $user->id]);

        $education->update([
            'institution' => 'Test Institution',
            'degree' => 'Test Degree',
            'field' => 'Test Field',
            'sort_order' => 5,
        ]);

        $this->assertEquals('Test Institution', $education->institution);
        $this->assertEquals('Test Degree', $education->degree);
        $this->assertEquals('Test Field', $education->field);
        $this->assertEquals(5, $education->sort_order);
    }

    /**
     * Test que Education tiene el ordenamiento correcto por sort_order (ascendente)
     */
    public function test_education_default_ordering(): void
    {
        $user = User::factory()->create();

        // Crear formaciones con diferentes valores de sort_order
        Education::factory()->create([
            'user_id' => $user->id,
            'institution' => 'Institution A',
            'degree' => 'Degree A',
            'sort_order' => 5,
        ]);

        Education::factory()->create([
            'user_id' => $user->id,
            'institution' => 'Institution B',
            'degree' => 'Degree B',
            'sort_order' => 1,
        ]);

        Education::factory()->create([
            'user_id' => $user->id,
            'institution' => 'Institution C',
            'degree' => 'Degree C',
            'sort_order' => 3,
        ]);

        $educations = Education::all();

        $this->assertEquals('Institution B', $educations[0]->institution);
        $this->assertEquals('Institution C', $educations[1]->institution);
        $this->assertEquals('Institution A', $educations[2]->institution);
    }

    /**
     * Test que Education tiene el método de relación morphMany Images
     */
    public function test_education_images_relationship(): void
    {
        $user = User::factory()->create();
        $education = Education::factory()->create(['user_id' => $user->id]);

        $this->assertTrue(method_exists($education, 'images'));

        // Verifica que se puedan cargar imágenes para la formación
        $image = Image::factory()->create([
            'imageable_type' => 'Education',
            'imageable_id' => $education->id,
            'type' => 'certificate',
        ]);

        $this->assertInstanceOf(Image::class, $image);
        $this->assertEquals('Education', $image->imageable_type);
        $this->assertEquals($education->id, $image->imageable_id);
    }

    /**
     * Test que el factory crea datos realistas
     */
    public function test_education_factory_creates_realistic_data(): void
    {
        $user = User::factory()->create();
        $education = Education::factory()->create(['user_id' => $user->id]);

        $this->assertNotEmpty($education->institution);
        $this->assertNotEmpty($education->degree);
        $this->assertIsString($education->institution);
        $this->assertIsString($education->degree);
        $this->assertIsInt($education->sort_order);
        $this->assertIsBool($education->is_current);
    }
}