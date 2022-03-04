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
        $category = $this->createCategory();

        $brand = $this->createBrand();
        $this->attachBrandToCategory($category->id, $brand->id);

        $subcategory = $this->createSubcategory($category->id);

        $product = $this->createProduct($subcategory->id, $brand->id);

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
        $category = $this->createCategory();

        $brand = $this->createBrand();
        $this->attachBrandToCategory($category->id, $brand->id);

        $subcategory = $this->createSubcategory($category->id);

        $product = $this->createProduct($subcategory->id, $brand->id);

        $image1 = $this->createImage($product->id);
        $image2 = $this->createImage($product->id);

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
        $category = $this->createCategory();

        $brand = $this->createBrand();
        $this->attachBrandToCategory($category->id, $brand->id);

        $subcategory = $this->createSubcategory($category->id);

        $product = $this->createProduct($subcategory->id, $brand->id);

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
        $category = $this->createCategory();

        $brand = $this->createBrand();
        $this->attachBrandToCategory($category->id, $brand->id);

        $subcategory = $this->createSubcategory($category->id, false, false);
        $product = $this->createProduct($subcategory->id, $brand->id);

        $subcategoryColor = $this->createSubcategory($category->id, true);
        $productColor = $this->createProduct($subcategoryColor->id, $brand->id);

        $subcategoryColorSize = $this->createSubcategory($category->id, true, true);
        $productColorSize = $this->createProduct($subcategoryColorSize->id, $brand->id);

        $this->browse(function (Browser $browser) use ($product, $productColor, $productColorSize) {

            $browser->visit('/products/' . $product->slug)
                ->pause(1000)
                ->assertSourceMissing('Seleccionar un talla</option>')
                ->assertSourceMissing('Seleccionar un color</option>')
                ->screenshot('s2-t9-product');

            $browser->visit('/products/' . $productColor->slug)
                ->pause(1000)
                ->assertSourceMissing('Seleccionar un talla</option>')
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
        $category = $this->createCategory();

        $brand = $this->createBrand();
        $this->attachBrandToCategory($category->id, $brand->id);

        $subcategory = $this->createSubcategory($category->id);
        $product = $this->createProduct($subcategory->id, $brand->id);


        $subcategoryColor = $this->createSubcategory($category->id, true);
        $productWithColor = $this->createProduct($subcategoryColor->id, $brand->id);

        $color = $this->createColor();
        $this->attachColorToProduct($productWithColor->id, $color->id);


        $subcategoryColorSize = $this->createSubcategory($category->id, true, true);
        $productWithColorSize = $this->createProduct($subcategoryColorSize->id, $brand->id);

        $color2 = $this->createColor('Verde');
        $size = $this->createSize($productWithColorSize->id);
        $this->attachSizeToColors($size->id);

        $this->browse(function (Browser $browser) use ($product, $productWithColor, $productWithColorSize) {

            $browser->visit('/products/' . $product->slug)
                ->pause(1000)
                ->assertSeeIn('@product_stock', $product->stock)
                ->screenshot('s3-t5-product')
                ->pause(300);

            $browser->visit('/products/' . $productWithColor->slug)
                ->pause(1000)
                ->assertSeeIn('@product_stock', $productWithColor->stock)
                ->screenshot('s3-t5-product-color')
                ->pause(300);

            $browser->visit('/products/' . $productWithColorSize->slug)
                ->pause(1000)
                ->assertSeeIn('@product_stock', $productWithColorSize->stock)
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
