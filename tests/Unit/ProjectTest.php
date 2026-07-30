
namespace Tests\Unit;

use App\Models\Project;
use App\Models\User;
use Tests\TestCase;

class ProjectTest extends TestCase
{
    use \Illuminate\Foundation\Testing\RefreshDatabase;

    /**
     * Test that the Project model can be created
     */
    public function test_project_can_be_created(): void
    {
        $user = User::factory()->create();
        $project = Project::factory()->create(['user_id' => $user->id]);

        $this->assertModelExists($project);
        $this->assertInstanceOf(Project::class, $project);
        $this->assertEquals($user->id, $project->user_id);
        $this->assertNotEmpty($project->name);
        $this->assertIsArray($project->tech_stack);
    }

    /**
     * Test that Project belongs to User relationship
     */
    public function test_project_belongs_to_user(): void
    {
        $user = User::factory()->create();
        $project = Project::factory()->create(['user_id' => $user->id]);

        $this->assertInstanceOf(User::class, $project->user);
        $this->assertEquals($user->id, $project->user->id);
    }

    /**
     * Test that Project has the correct fillable attributes
     */
    public function test_project_has_fillable_attributes(): void
    {
        $user = User::factory()->create();
        $project = Project::factory()->create(['user_id' => $user->id]);

        $project->update([
            'name' => 'Test Project',
            'description' => 'Test Description',
            'is_featured' => true,
            'team_size' => 5,
        ]);

        $this->assertEquals('Test Project', $project->name);
        $this->assertEquals('Test Description', $project->description);
        $this->assertTrue($project->is_featured);
        $this->assertEquals(5, $project->team_size);
    }

    /**
     * Test that Project has the correct default ordering by start_date
     */
    public function test_project_default_ordering(): void
    {
        $user = User::factory()->create();

        // Create projects with different start_date values
        Project::factory()->create([
            'user_id' => $user->id,
            'name' => 'Project A',
            'start_date' => '2024-01-15',
        ]);

        Project::factory()->create([
            'user_id' => $user->id,
            'name' => 'Project B',
            'start_date' => '2023-06-20',
        ]);

        Project::factory()->create([
            'user_id' => $user->id,
            'name' => 'Project C',
            'start_date' => '2024-03-10',
        ]);

        $projects = Project::all();

        // Descending order by start_date: Project C (2024-03-10) first, then Project A (2024-01-15), then Project B (2023-06-20)
        $this->assertEquals('Project C', $projects[0]->name);
        $this->assertEquals('Project A', $projects[1]->name);
        $this->assertEquals('Project B', $projects[2]->name);
    }

    /**
     * Test that Project tech_stack casting works correctly
     */
    public function test_project_tech_stack_casting(): void
    {
        $user = User::factory()->create();
        $project = Project::factory()->create([
            'user_id' => $user->id,
            'tech_stack' => ['PHP', 'Laravel', 'Vue', 'MySQL'],
        ]);

        $this->assertIsArray($project->tech_stack);
        $this->assertContains('PHP', $project->tech_stack);
        $this->assertContains('Laravel', $project->tech_stack);
        $this->assertContains('Vue', $project->tech_stack);
        $this->assertContains('MySQL', $project->tech_stack);
    }

    /**
     * Test that Project has morphMany Images relationship method
     */
    public function test_project_images_relationship(): void
    {
        $user = User::factory()->create();
        $project = Project::factory()->create(['user_id' => $user->id]);

        $this->assertTrue(method_exists($project, 'images'));
    }

    /**
     * Test factory creates realistic data
     */
    public function test_project_factory_creates_realistic_data(): void
    {
        $user = User::factory()->create();
        $project = Project::factory()->create(['user_id' => $user->id]);

        $this->assertNotEmpty($project->name);
        $this->assertNotEmpty($project->role);
        $this->assertIsArray($project->tech_stack);
        $this->assertIsBool($project->is_current);
        $this->assertIsBool($project->is_featured);
    }
}