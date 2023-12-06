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
    public function a_categories_can_be_listed()
    {
        $this->withoutExceptionHandling();

        $categories = Category::factory()->count(2)->create();
        $response = $this->get('/api/categories');

        $this->assertCount(2, Category::all());
        $response->assertStatus(200)
            ->assertJson([[
                'id' => $categories->first()->id,
                'name' => $categories->first()->name,
                'rank' => $categories->first()->rank,
                'description' => $categories->first()->description,
            ], [
                'id' => $categories->last()->id,
                'name' => $categories->last()->name,
                'rank' => $categories->last()->rank,
                'description' => $categories->last()->description,
            ]]);
    }

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
    public function a_category_name_is_required()
    {
//        $this->withoutExceptionHandling();

        $response = $this->post('/api/categories', [
            'name' => '',
            'description' => 'Small portion',
            'rank' => 1,
        ]);

        $this->assertCount(0, Category::all());
        $response->assertStatus(422)
            ->assertJson([
                    "errors" => [
                        "name" => "The name field is required."
                    ]
                ]
            );
    }

    /** @test */
    public function a_category_name_cannot_be_duplicated_on_create()
    {
//        $this->withoutExceptionHandling();

        $existing_category = Category::factory()->create();

        $response = $this->post('/api/categories', [
            'name' => $existing_category->name,
            'description' => 'Small portion',
            'rank' => 1,
        ]);

        $this->assertCount(1, Category::all());
        $response->assertStatus(422)
            ->assertJson([
                    "errors" => [
                        "name" => "The name has already been taken."
                    ]
                ]
            );
    }

    /** @test */
    public function a_category_name_cannot_be_duplicated_on_update()
    {
//        $this->withoutExceptionHandling();

        $existing_category1 = Category::factory()->create();

        $existing_category2 = Category::factory()->create();

        $response = $this->post('/api/categories/' . $existing_category1->id, [
            'name' => $existing_category2->name,
            'description' => 'Small portion',
            'rank' => 1,
        ]);

        $this->assertCount(2, Category::all());
        $response->assertStatus(422)
            ->assertJson([
                    "errors" => [
                        "name" => "The name has already been taken."
                    ]
                ]
            );
    }

    /** @test */
    public function a_category_rank_is_required()
    {
//        $this->withoutExceptionHandling();

        $response = $this->post('/api/categories', [
            'name' => 'Dinner',
            'description' => 'Main Course',
            'rank' => '',
        ]);

        $this->assertCount(0, Category::all());
        $response->assertStatus(422)
            ->assertJson([
                    "errors" => [
                        "rank" => "The rank field is required."
                    ]
                ]
            );
    }

    /** @test */
    public function a_category_rank_must_be_a_positive_number()
    {
//        $this->withoutExceptionHandling();

        $response = $this->post('/api/categories', [
            'name' => 'Dinner',
            'description' => 'Main Course',
            'rank' => -1,
        ]);

        $this->assertCount(0, Category::all());
        $response->assertStatus(422)
            ->assertJson([
                    "errors" => [
                        "rank" => "The rank must be at least 1."
                    ]
                ]
            );
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

    /** @test */
    public function a_category_can_be_updated()
    {
        $this->withoutExceptionHandling();

        $category = Category::factory()->create();

        $response = $this->post('/api/categories/' . $category->id, [
            'name' => 'Appetizers',
            'description' => 'Small portion',
            'rank' => 1,
        ]);

        $updated_category = Category::first();
        $this->assertCount(1, Category::all());
        $response->assertStatus(200)
            ->assertJson([
                'id' => $updated_category->id,
                'name' => $updated_category->name,
                'rank' => $updated_category->rank,
                'description' => $updated_category->description,
            ]);
    }

    /** @test */
    public function a_category_can_be_deleted()
    {
//        $this->withoutExceptionHandling();

        $category = Category::factory()->create();

        $response = $this->delete('/api/categories/' . $category->id);

        $this->assertCount(0, Category::all());
        $response->assertStatus(200)
            ->assertJson([
                'id' => $category->id,
            ]);
    }


}
