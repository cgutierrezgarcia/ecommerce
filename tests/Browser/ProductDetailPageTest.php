<?php

namespace Tests\Browser;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Image;
use App\Models\Product;
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
                ->screenshot('s2-t7');
        });
    }
}
