<?php

namespace Tests\Feature;

use App\Filament\Resources\Education\Pages\CreateEducation;
use App\Filament\Resources\Education\Pages\EditEducation;
use App\Models\Education;
use App\Models\Image;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class EducationAdminTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create([
            'email' => 'daprthefox@gmail.com',
            'password' => 'asdf1234',
        ]);

        User::factory()->create();
    }

    public function test_education_page_is_accessible(): void
    {
        $this->actingAs($this->user)
            ->get('/admin/education')
            ->assertSuccessful()
            ->assertSee('Educación');
    }

    public function test_education_can_be_created(): void
    {
        Livewire::actingAs($this->user)
            ->test(CreateEducation::class)
            ->fillForm([
                'institution' => 'Universidad de Prueba',
                'degree' => 'Licenciatura',
                'field' => 'Informática',
                'description' => 'Descripción de la educación',
                'start_date' => '2020-01-01',
                'end_date' => '2024-01-01',
                'sort_order' => 1,
            ])
            ->call('create')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('education', [
            'institution' => 'Universidad de Prueba',
            'degree' => 'Licenciatura',
            'field' => 'Informática',
            'user_id' => $this->user->id,
        ]);
    }

    public function test_education_form_requires_institution_and_degree(): void
    {
        Livewire::actingAs($this->user)
            ->test(CreateEducation::class)
            ->fillForm([
                'institution' => '',
                'degree' => '',
            ])
            ->call('create');

        $this->assertDatabaseCount('education', 0);
    }

    public function test_education_can_be_updated(): void
    {
        $education = Education::factory()->create();

        Livewire::actingAs($this->user)
            ->test(EditEducation::class, ['record' => $education->getKey()])
            ->fillForm([
                'institution' => 'Universidad Actualizada',
                'degree' => 'Maestría',
                'start_date' => '2023-01-01',
            ])
            ->call('save')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('education', [
            'id' => $education->id,
            'institution' => 'Universidad Actualizada',
            'degree' => 'Maestría',
        ]);
    }

    public function test_education_certificate_image_can_be_attached(): void
    {
        $education = Education::factory()->create();

        $image = Image::factory()->create([
            'imageable_type' => 'App\Models\Education',
            'imageable_id' => $education->id,
            'type' => 'certificate',
            'url' => 'https://example.com/certificate.pdf',
            'alt_text' => 'Certificado de prueba',
        ]);

        $this->assertDatabaseHas('images', [
            'id' => $image->id,
            'imageable_type' => 'App\Models\Education',
            'imageable_id' => $education->id,
            'type' => 'certificate',
        ]);
    }

    public function test_education_images_relationship(): void
    {
        $education = Education::factory()->create();

        $image = Image::factory()->create([
            'imageable_type' => 'App\Models\Education',
            'imageable_id' => $education->id,
            'type' => 'certificate',
        ]);

        $images = $education->images;
        $this->assertCount(1, $images);
        $this->assertEquals($image->id, $images->first()->id);
    }

    public function test_education_default_ordering(): void
    {
        Education::factory()->create([
            'institution' => 'Institution A',
            'sort_order' => 5,
        ]);

        Education::factory()->create([
            'institution' => 'Institution B',
            'sort_order' => 1,
        ]);

        Education::factory()->create([
            'institution' => 'Institution C',
            'sort_order' => 3,
        ]);

        $educations = Education::all();

        $this->assertEquals('Institution B', $educations[0]->institution);
        $this->assertEquals('Institution C', $educations[1]->institution);
        $this->assertEquals('Institution A', $educations[2]->institution);
    }
}
