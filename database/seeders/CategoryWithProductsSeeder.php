<?php


namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategoryWithProductsSeeder extends Seeder
{

    public function run()
    {
        $menu = [
            'Appetizers' => [
                [
                    'name' => 'Caprese Salad',
                    'description' => 'Fresh mozzarella, tomatoes, and basil drizzled with balsamic glaze.',
                    'price' => 9.99,
                ],
                [
                    'name' => 'Garlic Shrimp Skewers',
                    'description' => 'Grilled shrimp skewers seasoned with garlic and herbs.',
                    'price' => 12.99,
                ],
                [
                    'name' => 'Bruschetta',
                    'description' => 'Toasted baguette slices topped with diced tomatoes, garlic, and basil.',
                    'price' => 8.99,
                ],
            ],
            'Main Courses' => [
                [
                    'name' => 'Grilled Chicken Alfredo',
                    'description' => 'Grilled chicken breast served with fettuccine pasta and creamy Alfredo sauce.',
                    'price' => 16.99,
                ],
                [
                    'name' => 'Vegetarian Pizza',
                    'description' => 'Thin crust pizza with a medley of fresh vegetables and melted mozzarella.',
                    'price' => 14.99,
                ],
                [
                    'name' => 'Pan-Seared Salmon',
                    'description' => 'Fresh salmon fillet pan-seared to perfection, served with lemon butter sauce.',
                    'price' => 19.99,
                ],
                [
                    'name' => 'Classic Cheeseburger',
                    'description' => 'Angus beef patty with melted cheddar, lettuce, tomato, and pickles on a brioche bun.',
                    'price' => 11.99,
                ],
            ],
            'Desserts' => [
                [
                    'name' => 'Chocolate Lava Cake',
                    'description' => 'Warm chocolate cake with a gooey, molten center, served with vanilla ice cream.',
                    'price' => 8.99,
                ],
                [
                    'name' => 'Tiramisu',
                    'description' => 'Layers of coffee-soaked ladyfingers and mascarpone cream, dusted with cocoa.',
                    'price' => 10.99,
                ],
                [
                    'name' => 'Fruit Sorbet',
                    'description' => 'A refreshing mix of seasonal fruit sorbet.',
                    'price' => 6.99,
                ],
            ],
            'Beverages' => [
                [
                    'name' => 'Signature Cocktail - Tropical Bliss',
                    'description' => 'A delightful blend of tropical fruits and spirits.',
                    'price' => 14.99,
                ],
                [
                    'name' => 'Freshly Squeezed Orange Juice',
                    'description' => 'Cold-pressed orange juice served with ice.',
                    'price' => 4.99,
                ],
            ],
            'Specials' => [
                [
                    'name' => "Chef's Special - Surf and Turf",
                    'description' => 'A combination of grilled lobster tail and filet mignon.',
                    'price' => 29.99,
                ],
                [
                    'name' => 'Vegetarian Delight Platter',
                    'description' => 'A selection of the chef\'s recommended vegetarian dishes.',
                    'price' => 18.99,
                ],
            ],
        ];

        foreach ($menu as $category_name => $products) {
            $category = Category::create([
                'name' => $category_name,
                'description' => $category_name,
                'rank' => rand(1, 20),
            ]);

            foreach ($products as $p) {
                $category->products()->create([
                    'name' => $p['name'],
                    'rank' => rand(1, 20),
                    'description' => $p['description'],
                    'price' => $p['price'],
                ]);
            }
        }
    }
}
