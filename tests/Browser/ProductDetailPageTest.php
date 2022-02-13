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
use Illuminate\Support\Str;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class ProductDetailPageTest extends DuskTestCase
{
    use DatabaseMigrations;

    /** @test */
    public function it_can_access_the_product_details()
    {
        $category = Category::factory()->create();

        $brand = Brand::factory()->create();
        $category->brands()->attach($brand->id);

        $subcategory = Subcategory::factory()->create();

        $product = Product::factory()->create([
            'subcategory_id' => $subcategory->id,
            'brand_id' => $brand->id
        ]);
        Image::factory()->create([
            'imageable_id' => $product->id,
            'imageable_type' => Product::class
        ]);

        $this->browse(function (Browser $browser) use (
            $category, $subcategory, $brand, $product) {

            $browser->visit('/')
                ->pause(1000)
                ->clickLink(Str::limit($product->name, 20))
                ->pause(1000)
                ->assertPathIs('/products/' . $product->slug)
                ->screenshot('s2-t6');
        });
    }

    /** @test */
    public function it_shows_the_product_details()
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
            'brand_id' => $brand->id
        ]);
        $image1 = Image::factory()->create([
            'imageable_id' => $product->id,
            'imageable_type' => Product::class
        ]);
        $image2 = Image::factory()->create([
            'imageable_id' => $product->id,
            'imageable_type' => Product::class
        ]);

        $this->browse(function (Browser $browser) use (
            $category, $subcategory, $brand, $product, $image1, $image2) {

            $browser->visit('/products/' . $product->slug)
                ->pause(1000)
                ->resize(500, 1200)
                ->pause(1000)
                ->assertSee($product->name)
                ->assertSee($product->description)
                ->assertSee($product->price)
                ->assertSee($product->quantity)
                ->assertSourceHas($image1->url)
                ->assertSourceHas($image2->url)
                ->press('+')
                ->pause(1000)
                ->press('-')
                ->assertSee('AGREGAR AL CARRITO DE COMPRAS')
                ->resize(1920, 1080)
                ->screenshot('s2-t7');
        });
    }

    /** @test */
    public function the_decrement_and_increment_btns_works_as_expected()
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
                $browser->pause(100)
                    ->press('+');
            }

            $browser->pause(1000)
                ->assertSeeIn('@product_qty', $product->quantity)
                ->resize(1920, 1080)
                ->screenshot('s2-t8');
        });
    }

    /** @test */
    public function it_shows_the_product_color_and_size_selects()
    {
        $category = Category::factory()->create();

        $brand = Brand::factory()->create();
        $category->brands()->attach($brand->id);

        $subcategoryColor = Subcategory::factory()->create([
            'color' => true,
            'size' => false
        ]);
        $subcategoryColorSize = Subcategory::factory()->create([
            'color' => true,
            'size' => true
        ]);

        $productColor = Product::factory()->create([
            'subcategory_id' => $subcategoryColor->id,
            'brand_id' => $brand->id
        ]);
        $productColorSize = Product::factory()->create([
            'subcategory_id' => $subcategoryColorSize->id,
            'brand_id' => $brand->id
        ]);
        for ($i = 1; $i <= 2; $i++) {
            Image::factory()->create([
                'imageable_id' => $i,
                'imageable_type' => Product::class
            ]);
        }

        $this->browse(function (Browser $browser) use ($productColor, $productColorSize) {

            $browser->visit('/products/' . $productColor->slug)
                ->pause(1000)
                ->assertSourceHas('Seleccionar un color</option>')
                ->screenshot('s2-t9-product-color');

            $browser->visit('/products/' . $productColorSize->slug)
                ->pause(1000)
                ->assertSourceHas('Seleccione una talla</option>')
                ->assertSourceHas('Seleccione un color</option>')
                ->screenshot('s2-t9-product-color-size');
        });
    }

    /** @test */
    public function it_shows_the_stock_of_every_product_type()
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
            'brand_id' => $brand->id,
            'quantity' => 3
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
                'quantity' => 5
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
        $product3Color2 = Color::create(['name' => 'Azul']);
        $product3->colors()->attach([
            $product3Color->id => [
                'quantity' => 10
            ],
            $product3Color2->id => [
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
        $product3Size->colors()->attach([
            1 => ['quantity' => 10],
            2 => ['quantity' => 10],
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
                ->assertSeeIn('@product_stock', $product1->stock)
                ->screenshot('s3-t5-product')
                ->pause(300);

            $browser->visit('/products/' . $product2->slug)
                ->pause(1000)
                ->assertSeeIn('@product_stock', $product2->stock)
                ->screenshot('s3-t5-product-color')
                ->pause(300);

            $browser->visit('/products/' . $product3->slug)
                ->pause(1000)
                ->assertSeeIn('@product_stock', $product3->stock)
                ->screenshot('s3-t5-product-color-size')
                ->pause(300);
        });
    }

    /** @test */
    public function it_changes_the_product_stock_when_adding_a_product_to_the_cart()
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
            'quantity' => 10
        ]);
        Image::factory()->create([
            'imageable_id' => $product->id,
            'imageable_type' => Product::class
        ]);

        $this->browse(function (Browser $browser) use ($product) {

            $browser->visit('/products/' . $product->slug)
                ->pause(1000)
                ->assertSeeIn('@product_stock', 10)
                ->press('AGREGAR AL CARRITO DE COMPRAS')
                ->pause(300)
                ->assertSeeIn('@product_stock', 10 -1)
                ->screenshot('s4-t4');
        });
    }
}
