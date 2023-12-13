<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class ProductTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function a_product_can_be_created()
    {
        $this->withoutExceptionHandling();

        $category = Category::factory()->create();

        $response = $this->post('/api/products', [
            'category_id' => $category->id,
            'name' => 'Appetizers',
            'description' => 'Small portion',
            'rank' => 1,
            'image' => UploadedFile::fake()->image('test_image.jpg'),
        ]);

        $product = Product::first();
        $this->assertCount(1, Product::all());
        $this->assertEquals(1, DB::table('media')->count());
        $response->assertStatus(200)
            ->assertJson([
                'id' => $product->id,
                'category' => [
                    'id' => $category->id,
                    'name' => $category->name,
                ],
                'name' => $product->name,
                'rank' => $product->rank,
                'image_url' => 'http://localhost/storage/1/test_image.jpg',
                'thumbnail_url' => 'http://localhost/storage/1/conversions/test_image-thumb.jpg',
                'description' => $product->description,
            ]);
    }

    /** @test */
    public function category_id_must_belong_to_the_category()
    {
//        $this->withoutExceptionHandling();

        $response = $this->post('/api/products', [
            'category_id' => 1,
            'name' => 'Appetizers',
            'description' => 'Small portion',
            'rank' => 1,
        ]);

        $this->assertCount(0, Product::all());
        $response->assertStatus(422)
            ->assertJson([
                "errors" => [
                    "category_id" => "The selected category id is invalid."
                ]
            ]);
    }

    /** @test */
    public function product_with_duplicate_name_cannot_be_created_for_same_category_id()
    {
//        $this->withoutExceptionHandling();

        $category = Category::factory()->create();

        $product = Product::factory()->create([
            'category_id' => $category->id,
        ]);

        $response = $this->post('/api/products', [
            'category_id' => $category->id,
            'name' => $product->name,
            'description' => 'Small portion',
            'rank' => 1,
        ]);

        $this->assertCount(1, Product::all());
        $response->assertStatus(422)
            ->assertJson([
                "errors" => [
                    "name" => "The product with given name already exists for a given category."
                ]
            ]);
    }

    /** @test */
    public function a_product_can_be_updated()
    {
        $this->withoutExceptionHandling();

        $product = Product::factory()->create();
        $category = Category::factory()->create();
        $image = UploadedFile::fake()->image('test_image1.jpg');
        $product->addMedia($image)
            ->usingName('image')
            ->toMediaCollection();

        $response = $this->post('/api/products/' . $product->id, [
            'category_id' => $category->id,
            'name' => 'Veg Pakora',
            'description' => 'Deep fried veggies in lentil flour',
            'rank' => 2,
            'image' => UploadedFile::fake()->image('test_image2.jpg'),
        ]);

        $updated_product = Product::first();
        $updated_product->refresh();
        $this->assertCount(1, Product::all());
        $response->assertStatus(200)
            ->assertJson([
                'id' => $updated_product->id,
                'category' => [
                    'id' => $category->id,
                    'name' => $category->name,
                ],
                'name' => $updated_product->name,
                'rank' => $updated_product->rank,
                'image_url' => 'http://localhost/storage/2/test_image2.jpg',
                'thumbnail_url' => 'http://localhost/storage/2/conversions/test_image2-thumb.jpg',
                'description' => $updated_product->description,
            ]);
    }

    /** @test */
    public function product_name_cannot_be_updated_to_existing_name_within_same_category()
    {
//        $this->withoutExceptionHandling();

        $category = Category::factory()->create();

        $product1 = Product::factory()->create([
            'category_id' => $category->id,
        ]);
        $name1 = $product1->name;

        $product2 = Product::factory()->create([
            'category_id' => $category->id,
        ]);
        $name2 = $product2->name;

        $response = $this->post('/api/products/' . $product1->id, [
            'category_id' => $category->id,
            'name' => $name2,
            'description' => 'Small portion',
            'rank' => 1,
        ]);

        $this->assertEquals($name1, $product1->name);
        $response->assertStatus(422)
            ->assertJson([
                "errors" => [
                    "name" => "The product with given name already exists for a given category."
                ]
            ]);
    }

    /** @test */
    public function self_product_name_should_be_allowed_while_updating_product()
    {
//        $this->withoutExceptionHandling();

        $category = Category::factory()->create();

        $product1 = Product::factory()->create([
            'category_id' => $category->id,
        ]);
        $name1 = $product1->name;

        $product2 = Product::factory()->create([
            'category_id' => $category->id,
        ]);
        $name2 = $product2->name;

        $response = $this->post('/api/products/' . $product1->id, [
            'category_id' => $category->id,
            'name' => $product1->name,
            'description' => 'Small portion',
            'rank' => 1,
            'image' => UploadedFile::fake()->image('test_image.jpg'),
        ]);
        $product1->refresh();
        $this->assertEquals($name1, $product1->name);
        $this->assertCount(2, Product::all());
        $response->assertStatus(200)
            ->assertJson([
                'id' => $product1->id,
                'category' => [
                    'id' => $category->id,
                    'name' => $category->name,
                ],
                'name' => $product1->name,
                'rank' => $product1->rank,
                'description' => $product1->description,
            ]);
    }

    /** @test */
    public function a_product_can_be_deleted()
    {
//        $this->withoutExceptionHandling();

        $product = Product::factory()->create();

        $response = $this->delete('/api/products/' . $product->id);

        $this->assertCount(0, Product::all());
        $response->assertStatus(200)
            ->assertJson([
                'id' => $product->id,
            ]);
    }

    /** @test */
    public function associated_image_is_also_deleted_while_deleting_product()
    {
//        $this->withoutExceptionHandling();

        $product = Product::factory()->create();

        $image = UploadedFile::fake()->image('test_image.jpg');
        $product->addMedia($image)
            ->usingName('image')
            ->toMediaCollection();

        $this->assertEquals(1, DB::table('media')->count());

        $this->delete('/api/products/' . $product->id);

        $this->assertEquals(0, DB::table('media')->count());
    }

}
