<?php

namespace Tests\Browser;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Str;
use Laravel\Dusk\Browser;
use Tests\CreateData;
use Tests\DuskTestCase;

class ProductDetailPageTest extends DuskTestCase
{
    use DatabaseMigrations;
    use CreateData;

    /** @test */
    public function it_can_access_the_product_details()
    {
        $data = $this->createProducts();

        $this->browse(function (Browser $browser) use ($data) {

            $browser->visit('/')
                ->pause(1000)
                ->clickLink(Str::limit($data['product111name'], 20))
                ->pause(1000)
                ->assertPathIs('/products/' . $data['product111slug'])
                ->screenshot('s2-t6');
        });
    }

    /** @test */
    public function it_shows_the_product_details()
    {
        $data = $this->createProducts(1, 1, 1, true, 3);

        $this->browse(function (Browser $browser) use ($data) {

            $browser->visit('/products/' . $data['product111slug'])
                ->pause(1000)
                ->resize(500, 1200)
                ->pause(1000)
                ->assertSee($data['product111name'])
                ->assertSee($data['product111description'])
                ->assertSee($data['product111price'])
                ->assertSee($data['product111quantity'])
                ->assertSourceHas($data['product111image1'])
                ->assertSourceHas($data['product111image1'])
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
        $data = $this->createProducts();

        $this->browse(function (Browser $browser) use ($data) {

            $browser->visit('/products/' . $data['product111slug'])
                ->pause(1000)
                ->resize(500, 1200);

            for ($i = 1; $i < $data['product111quantity'] +5; $i++) {
                $browser->pause(100)
                    ->press('+');
            }

            $browser->pause(1000)
                ->assertSeeIn('@product_qty', $data['product111quantity'])
                ->resize(1920, 1080)
                ->screenshot('s2-t8');
        });
    }

    /** @test */
    public function it_shows_the_product_color_and_size_selects()
    {
        $product = $this->createProducts2();
        $productColor = $this->createProducts2(2, 10, true);
        $productColorSize = $this->createProducts2(2, 15, true, true);

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
        $product = $this->createProducts2();
        $productWithColor = $this->createProducts2(2, 10, true);
        $productWithColorSize = $this->createProducts2(2, 15, true, true);

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
        $data = $this->createProducts();

        $this->browse(function (Browser $browser) use ($data) {

            $browser->visit('/products/' . $data['product111slug'])
                ->pause(1000)
                ->assertSeeIn('@product_stock', 5)
                ->press('AGREGAR AL CARRITO DE COMPRAS')
                ->pause(300)
                ->assertSeeIn('@product_stock', 5 -1)
                ->screenshot('s4-t4');
        });
    }
}
