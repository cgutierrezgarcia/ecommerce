<?php

namespace Tests\Browser;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Image;
use App\Models\Product;
use App\Models\Subcategory;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class SearchPageTest extends DuskTestCase
{
    use DatabaseMigrations;

    /** @test */
    public function the_search_filter_the_products_or_show_them_all_when_empty()
    {
        $category = Category::factory()->create();

        $brand = Brand::factory()->create();
        $category->brands()->attach($brand->id);

        $subcategory = Subcategory::factory()->create([
            'color' => false,
            'size' => false
        ]);

        $product1 = Product::factory()->create([
            'subcategory_id' => $subcategory->id,
            'brand_id' => $brand->id
        ]);
        $product2 = Product::factory()->create([
            'subcategory_id' => $subcategory->id,
            'brand_id' => $brand->id
        ]);

        for ($i = 1; $i <= 2; $i++) {
            Image::factory()->create([
                'imageable_id' => $i,
                'imageable_type' => Product::class
            ]);
        }

        $this->browse(function (Browser $browser) use ($product1, $product2) {
            $browser->visit('/search?name=' . $product1->name)
                ->pause(1000)
                ->assertSee($product1->name)
                ->assertDontSee($product2->name)
                ->screenshot('s3-t6-filter')
                ->pause(300);

            $browser->visit('/search?name=')
                ->pause(1000)
                ->assertSee($product1->name)
                ->assertSee($product2->name)
                ->screenshot('s3-t6-empty');
        });
    }
}
