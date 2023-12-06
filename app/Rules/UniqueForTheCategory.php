<?php

namespace App\Rules;

use App\Models\Product;
use Illuminate\Contracts\Validation\Rule;

class UniqueForTheCategory implements Rule
{
    private $category_id;
    private $product;

    /**
     * Create a new rule instance.
     *
     * @param $category_id
     * @param Product $product
     */
    public function __construct($category_id, Product $product = null)
    {
        $this->category_id = $category_id;
        $this->product = $product;
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param string $attribute
     * @param mixed $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        return !Product::where('category_id', $this->category_id)
            ->when(isset($this->product), function ($q) {
                $q->where('id', '!=', $this->product->id);
            })
            ->where('name', $value)->exists();
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'The product with given name already exists for a given category.';
    }
}
