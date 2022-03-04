<?php

namespace Tests\Browser;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class SearchPageTest extends DuskTestCase
{
    use DatabaseMigrations;

    /** @test */
    public function the_search_filter_the_products_or_show_them_all_when_empty()
    {
        $category = $this->createCategory();

        $brand = $this->createBrand();
        $this->attachBrandToCategory($category->id, $brand->id);

        $subcategory = $this->createSubcategory($category->id);

        $product1 = $this->createProduct($subcategory->id, $brand->id);
        $product2 = $this->createProduct($subcategory->id, $brand->id);

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
