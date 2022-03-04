<?php

namespace Tests\Browser;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Str;
use Laravel\Dusk\Browser;
use Tests\CreateData;
use Tests\DuskTestCase;

class CategoryDetailPageTest extends DuskTestCase
{
    use DatabaseMigrations;
    use CreateData;

    /** @test */
    public function it_shows_the_category_details()
    {
        $category1 = $this->createCategory();

        $brand1a = $this->createBrand();
        $brand1b = $this->createBrand();
        $this->attachBrandToCategory($category1->id, $brand1a->id);
        $this->attachBrandToCategory($category1->id, $brand1b->id);

        $subcategory1a = $this->createSubcategory($category1->id);
        $subcategory1b = $this->createSubcategory($category1->id);

        $product1a = $this->createProduct($subcategory1a->id);
        $product1b = $this->createProduct($subcategory1b->id);


        $category2 = $this->createCategory();

        $brand2 = $this->createBrand();
        $this->attachBrandToCategory($category2->id, $brand2->id);

        $subcategory2 = $this->createSubcategory($category2->id);

        $product2 = $this->createProduct($subcategory2->id);


        $this->browse(function (Browser $browser) use (
            $category1, $subcategory1a, $subcategory1b, $brand1a, $brand1b, $product1a, $product1b,
            $category2, $subcategory2, $brand2, $product2) {

            $browser->visit('/')
                ->clickLink('Ver más')
                ->pause(1000)
                ->assertPathIs('/categories/' . $category1->slug)

                ->assertSourceHas($category1->name . '</h1>')
                ->assertSourceHas($subcategory1a->name . '</a>')
                ->assertSourceHas($subcategory1b->name . '</a>')
                ->assertSourceHas($brand1a->name . '</a>')
                ->assertSourceHas($brand1b->name . '</a>')
                ->assertSee(Str::limit($product1a->name, 20))
                ->assertSee(Str::limit($product1b->name, 20))

                ->assertSourceMissing($category2->name . '</h1>')
                ->assertSourceMissing($subcategory2->name . '</a>')
                ->assertSourceMissing($brand2->name . '</a>')
                ->assertDontSee(Str::limit($product2->name, 20))
                ->screenshot('s2-t4');
        });
    }

    /** @test */
    public function it_filter_the_products_on_the_category_details()
    {
        $category = $this->createCategory();

        $brand1 = $this->createBrand();
        $brand2 = $this->createBrand();
        $this->attachBrandToCategory($category->id, $brand1->id);
        $this->attachBrandToCategory($category->id, $brand2->id);

        $subcategory1 = $this->createSubcategory($category->id);
        $subcategory2 = $this->createSubcategory($category->id);

        $productSub1Brand1 = $this->createProduct($subcategory1->id, $brand1->id);
        $productSub1Brand2 = $this->createProduct($subcategory1->id, $brand2->id);
        $productSubcategory2 = $this->createProduct($subcategory2->id);

        $this->browse(function (Browser $browser) use (
            $category, $subcategory1, $subcategory2, $brand1, $brand2,
            $productSub1Brand1, $productSub1Brand2, $productSubcategory2) {

            $browser->visit('/categories/' . $category->slug)
                ->pause(1000)

                ->clickLink($subcategory1->name)
                ->pause(1000)

                ->clickLink($brand1->name)
                ->assertSee(Str::limit($productSub1Brand1->name, 20))
                ->assertDontSee(Str::limit($productSub1Brand2->name, 20))
                ->screenshot('s2-t5-product-sub1-brand1')

                ->clickLink($brand2->name)
                ->pause(1000)
                ->assertSee(Str::limit($productSub1Brand2->name, 20))
                ->assertDontSee(Str::limit($productSub1Brand1->name, 20))
                ->screenshot('s2-t5-product-sub1-brand2')

                ->press('ELIMINAR FILTROS')
                ->clickLink($subcategory2->name)
                ->pause(1000)
                ->assertSee(Str::limit($productSubcategory2->name, 20))
                ->assertDontSee(Str::limit($productSub1Brand1->name, 20))
                ->assertDontSee(Str::limit($productSub1Brand2->name, 20))
                ->screenshot('s2-t5-product-subcategory2');
        });
    }
}
