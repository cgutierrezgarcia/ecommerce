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

class ShoppingCartPageTest extends DuskTestCase
{
    use DatabaseMigrations;

    /** @test
     *
     * comprueba el test 1 y 7 de la semana 3
     *
     */
    public function it_adds_every_product_type_to_the_cart()
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


        $subcategoryColor = Subcategory::factory()->create([
            'color' => true,
            'size' => false
        ]);
        $product2 = Product::factory()->create([
            'subcategory_id' => $subcategoryColor->id,
            'brand_id' => $brand->id
        ]);
        $product2Color = Color::create(['name' => 'Verde']);
        $product2->colors()->attach([
            $product2Color->id => [
                'quantity' => 10
            ]
        ]);


        $subcategoryColorSize = Subcategory::factory()->create([
            'color' => true,
            'size' => true
        ]);
        $product3 = Product::factory()->create([
            'subcategory_id' => $subcategoryColorSize->id,
            'brand_id' => $brand->id
        ]);
        $product3Color = Color::create(['name' => 'Naranja']);
        $product3->colors()->attach([
            $product3Color->id => [
                'quantity' => 10
            ]
        ]);
        $product3Size = Size::create([
            'name' => 'Talla XXL',
            'product_id' => $product3->id
        ]);
        $product3->sizes()->create([
            'name' => $product3Size->name
        ]);
        $product3Size->colors()->attach([1 => ['quantity' => 10]]);

        for ($i = 1; $i <= 3; $i++) {
            Image::factory()->create([
                'imageable_id' => $i,
                'imageable_type' => Product::class
            ]);
        }

        $this->browse(function (Browser $browser) use (
            $product1, $product2, $product3) {

            $browser->visit('/products/' . $product1->slug)
                ->pause(1000)
                ->press('AGREGAR AL CARRITO DE COMPRAS')
                ->pause(300);

            $browser->visit('/products/' . $product2->slug)
                ->pause(1000)
                ->select('@porduct_color_select', 1)
                ->pause(300)
                ->press('AGREGAR AL CARRITO DE COMPRAS')
                ->pause(300);

            $browser->visit('/products/' . $product3->slug)
                ->pause(1000)
                ->select('@porduct_size_select', 1)
                ->pause(300)
                ->select('@porduct_color_select', 1)
                ->pause(300)
                ->press('AGREGAR AL CARRITO DE COMPRAS')
                ->pause(300);

            $browser->visit('/shopping-cart/')
                ->assertSee($product1->name)
                ->assertSee($product2->name)
                ->assertSee($product3->name)
                ->screenshot('s3-t1-t7');
        });
    }

    /** @test */
    public function it_can_not_add_more_qty_than_a_product_has_in_stock()
    {
        $category = Category::factory()->create();

        $brand = Brand::factory()->create();
        $category->brands()->attach($brand->id);

        $subcategory = Subcategory::factory()->create([
            'color' => false,
            'size' => false
        ]);

        $product = Product::factory()->create([
            'subcategory_id' => $subcategory->id,
            'brand_id' => $brand->id,
            'quantity' => 5
        ]);
        Image::factory()->create([
            'imageable_id' => $product->id,
            'imageable_type' => Product::class
        ]);

        $this->browse(function (Browser $browser) use ($product) {

            $browser->visit('/products/' . $product->slug)
                ->pause(1000)
                ->resize(500, 1200);

            for ($i = 1; $i < $product->quantity +5; $i++) {
                $browser->pause(200)
                    ->press('+');
            }

            $browser->pause(200)
                ->press('AGREGAR AL CARRITO DE COMPRAS')
                ->pause(200)
                ->visit('/shopping-cart/')
                ->pause(1000)
                ->assertSeeIn('@shopping_cart_page_product_qty', $product->quantity)
                ->press('+')
                ->pause(200)
                ->assertSeeIn('@shopping_cart_page_product_qty', $product->quantity)
                ->screenshot('s3-t4');
        });
    }

    /** @test */
    public function it_changes_the_total_qty_when_the_products_qty_increases_or_decreases()
    {
        $category = Category::factory()->create();

        $brand = Brand::factory()->create();
        $category->brands()->attach($brand->id);

        $subcategory = Subcategory::factory()->create([
            'color' => false,
            'size' => false
        ]);

        $product = Product::factory()->create([
            'subcategory_id' => $subcategory->id,
            'brand_id' => $brand->id,
            'quantity' => 5,
            'price' => 100
        ]);
        Image::factory()->create([
            'imageable_id' => $product->id,
            'imageable_type' => Product::class
        ]);

        $this->browse(function (Browser $browser) use ($product) {

            $browser->visit('/products/' . $product->slug)
                ->pause(1000)
                ->press('AGREGAR AL CARRITO DE COMPRAS')
                ->pause(300);

            $browser->visit('/shopping-cart/')
                ->pause(1000)
                ->assertSeeIn('@shopping_cart_page_product_qty', 1)
                ->assertSeeIn('@shopping_cart_total', $product->price)
                ->press('+')
                ->pause(400)
                ->press('+')
                ->pause(400)
                ->assertSeeIn('@shopping_cart_page_product_qty', 3)
                ->assertSeeIn('@shopping_cart_total', $product->price * 3)
                ->press('-')
                ->pause(400)
                ->assertSeeIn('@shopping_cart_page_product_qty', 2)
                ->assertSeeIn('@shopping_cart_total', $product->price * 2)
                ->screenshot('s3-t8');
        });
    }

    /** @test */
    public function it_can_empty_the_cart_and_remove_a_product()
    {
        $category = Category::factory()->create();

        $brand = Brand::factory()->create();
        $category->brands()->attach($brand->id);

        $subcategory = Subcategory::factory()->create([
            'color' => false,
            'size' => false
        ]);

        $product = Product::factory()->create([
            'subcategory_id' => $subcategory->id,
            'brand_id' => $brand->id,
            'quantity' => 5,
            'price' => 100
        ]);
        Image::factory()->create([
            'imageable_id' => $product->id,
            'imageable_type' => Product::class
        ]);

        $this->browse(function (Browser $browser) use ($product) {

            $browser->visit('/products/' . $product->slug)
                ->pause(1000)
                ->press('AGREGAR AL CARRITO DE COMPRAS')
                ->pause(300);

            $browser->visit('/shopping-cart/')
                ->pause(1000)
                ->assertSee($product->name)
                ->click('@shopping_cart_trash_btn')
                ->pause(400)
                ->assertDontSee($product->name)
                ->screenshot('s3-t9-remove');

            $browser->visit('/products/' . $product->slug)
                ->pause(1000)
                ->press('AGREGAR AL CARRITO DE COMPRAS')
                ->pause(300);

            $browser->visit('/shopping-cart/')
                ->pause(1000)
                ->assertSee($product->name)
                ->clickLink('Borrar carrito de compras')
                ->pause(400)
                ->assertDontSee($product->name)
                ->screenshot('s3-t9-empty');
        });
    }
}
