<?php

namespace Tests\Browser;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Color;
use App\Models\Image;
use App\Models\Product;
use App\Models\Size;
use App\Models\Subcategory;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class NavigationMenuCartTest extends DuskTestCase
{
    use DatabaseMigrations;

    /** @test */
    public function it_adds_products_to_the_navigation_menu_cart()
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
        $product3 = Product::factory()->create([
            'subcategory_id' => $subcategory->id,
            'brand_id' => $brand->id
        ]);

        for ($i = 1; $i <= 3; $i++) {
            Image::factory()->create([
                'imageable_id' => $i,
                'imageable_type' => Product::class
            ]);
        }

        $this->browse(function (Browser $browser) use ($product1, $product2, $product3) {

            $browser->visit('/products/' . $product1->slug)
                ->pause(1000)
                ->press('AGREGAR AL CARRITO DE COMPRAS')
                ->pause(300);

            $browser->visit('/products/' . $product2->slug)
                ->pause(1000)
                ->press('AGREGAR AL CARRITO DE COMPRAS')
                ->pause(300);

            $browser->visit('/products/' . $product3->slug)
                ->pause(1000)
                ->click('@navigation_menu_cart')
                ->pause(300)
                ->assertSee($product1->name)
                ->assertSee($product2->name)
                ->screenshot('s3-t2');
        });
    }

    /** @test */
    public function the_red_circle_changes_when_adding_products()
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

            $browser->visit('/products/' . $product1->slug)
                ->pause(1000)
                ->press('AGREGAR AL CARRITO DE COMPRAS')
                ->pause(1000)
                ->assertSeeIn('@cart_red_circle', '1');

            $browser->visit('/products/' . $product2->slug)
                ->pause(1000)
                ->press('AGREGAR AL CARRITO DE COMPRAS')
                ->pause(1000)
                ->assertSeeIn('@cart_red_circle', '2')
                ->screenshot('s3-t3');
        });
    }
}
