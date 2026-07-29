<?php

namespace Tests\Unit;

use App\Models\Link;
use App\Models\User;
use Tests\TestCase;

class LinkTest extends TestCase
{
    use \Illuminate\Foundation\Testing\RefreshDatabase;

    /**
     * Test that the Link model can be created
     */
    public function test_link_can_be_created(): void
    {
        $user = User::factory()->create();
        $link = Link::factory()->create(['user_id' => $user->id]);

        $this->assertModelExists($link);
        $this->assertInstanceOf(Link::class, $link);
        $this->assertEquals($user->id, $link->user_id);
        $this->assertNotEmpty($link->label);
    }

    /**
     * Test that Link belongs to User relationship
     */
    public function test_link_belongs_to_user(): void
    {
        $user = User::factory()->create();
        $link = Link::factory()->create(['user_id' => $user->id]);

        $this->assertInstanceOf(User::class, $link->user);
        $this->assertEquals($user->id, $link->user->id);
    }

    /**
     * Test that Link has the correct fillable attributes
     */
    public function test_link_has_fillable_attributes(): void
    {
        $user = User::factory()->create();
        $link = Link::factory()->create(['user_id' => $user->id]);

        $link->update([
            'label' => 'Test Label',
            'url' => 'https://example.com',
            'sort_order' => 5,
        ]);

        $this->assertEquals('Test Label', $link->label);
        $this->assertEquals('https://example.com', $link->url);
        $this->assertEquals(5, $link->sort_order);
    }

    /**
     * Test that Link has the correct default ordering
     */
    public function test_link_default_ordering(): void
    {
        $user = User::factory()->create();

        // Create links with different sort_order values
        Link::factory()->create(['user_id' => $user->id, 'label' => 'Link A', 'sort_order' => 5]);
        Link::factory()->create(['user_id' => $user->id, 'label' => 'Link B', 'sort_order' => 1]);
        Link::factory()->create(['user_id' => $user->id, 'label' => 'Link C', 'sort_order' => 3]);

        $links = Link::all();
        
        $this->assertEquals('Link B', $links[0]->label);
        $this->assertEquals('Link C', $links[1]->label);
        $this->assertEquals('Link A', $links[2]->label);
    }

    /**
     * Test factory creates realistic data
     */
    public function test_link_factory_creates_realistic_data(): void
    {
        $user = User::factory()->create();
        $link = Link::factory()->create(['user_id' => $user->id]);

        $this->assertNotEmpty($link->label);
        $this->assertNotEmpty($link->url);
        $this->assertIsString($link->label);
        $this->assertIsString($link->url);
        $this->assertIsInt($link->sort_order);
    }
}