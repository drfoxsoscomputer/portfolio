<?php

namespace Tests\Unit;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test that the User model can be created with profile-style fields
     */
    public function test_user_can_be_created(): void
    {
        $user = User::factory()->create([
            'title' => 'Desarrollador Full Stack',
            'summary' => 'Resumen profesional',
            'location' => 'Venezuela',
            'phone' => '+58 414-516-9484',
        ]);

        $this->assertModelExists($user);
        $this->assertInstanceOf(User::class, $user);
        $this->assertNotEmpty($user->name);
        $this->assertNotEmpty($user->email);
        $this->assertSame('Desarrollador Full Stack', $user->title);
        $this->assertSame('Resumen profesional', $user->summary);
        $this->assertSame('Venezuela', $user->location);
        $this->assertSame('+58 414-516-9484', $user->phone);
    }

    /**
     * Test that User has the correct fillable attributes (including profile fields)
     */
    public function test_user_has_fillable_attributes(): void
    {
        $user = User::factory()->create();

        $user->update([
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'title' => 'Developer',
            'summary' => 'Summary test',
            'location' => 'Test Location',
            'phone' => '+1234567890',
        ]);

        $this->assertEquals('John Doe', $user->name);
        $this->assertEquals('john@example.com', $user->email);
        $this->assertEquals('Developer', $user->title);
        $this->assertEquals('Summary test', $user->summary);
        $this->assertEquals('Test Location', $user->location);
        $this->assertEquals('+1234567890', $user->phone);
    }

    /**
     * Test that User has all relationship methods (formerly profile relationships)
     */
    public function test_user_has_relationship_methods(): void
    {
        $user = User::factory()->create();

        $this->assertTrue(method_exists($user, 'links'));
        $this->assertTrue(method_exists($user, 'projects'));
        $this->assertTrue(method_exists($user, 'experiences'));
        $this->assertTrue(method_exists($user, 'skills'));
        $this->assertTrue(method_exists($user, 'educations'));
        $this->assertTrue(method_exists($user, 'languages'));
        $this->assertTrue(method_exists($user, 'images'));
    }
}
