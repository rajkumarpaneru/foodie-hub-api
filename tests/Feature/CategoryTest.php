<?php

namespace Tests\Feature;

use App\Models\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class CategoryTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function a_category_can_be_created()
    {
        $this->withoutExceptionHandling();

        $response = $this->post('/api/categories', [
            'name' => 'Appetizers',
            'description' => 'Small portion',
            'rank' => 1,
        ]);

        $category = Category::first();
        $this->assertCount(1, Category::all());
        $response->assertStatus(200)
            ->assertJson([
                'id' => $category->id,
                'name' => $category->name,
                'rank' => $category->rank,
                'description' => $category->description,
            ]);
    }

    /** @test */
    public function a_category_can_be_retrieved()
    {
        $this->withoutExceptionHandling();

        $category = Category::factory()->create();
        $response = $this->get('/api/categories/' . $category->id);

        $this->assertCount(1, Category::all());
        $response->assertStatus(200)
            ->assertJson([
                'id' => $category->id,
                'name' => $category->name,
                'rank' => $category->rank,
                'description' => $category->description,
            ]);
    }
}
