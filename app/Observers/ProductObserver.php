<?php

namespace App\Observers;

use App\Models\Product;
use App\Models\Subcategory;

class ProductObserver
{
    public function updated(Product $product)
    {
        //$subcategory = $product->subcategory;
        $subcategory_id = $product->subcategory_id;
        $subcategory = Subcategory::find($subcategory_id);

        if ($subcategory->size) {

            $product->colors()->detach();

        } elseif ($subcategory->color) {

            foreach ($product->sizes as $size) {
                $size->delete();
            }

        } else {
            $product->colors()->detach();

            foreach ($product->sizes as $size) {
                $size->delete();
            }
        }
    }
}
