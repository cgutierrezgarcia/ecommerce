<?php

namespace Tests\Browser;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\CreateData;
use Tests\DuskTestCase;

class ShoppingCartPageTest extends DuskTestCase
{
    use DatabaseMigrations;
    use CreateData;

    /** @test
     *
     * comprueba el test 1 y 7 de la semana 3
     *
     */
    public function it_adds_every_product_type_to_the_cart()
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


        $this->browse(function (Browser $browser) use (
            $product, $productWithColor, $productWithColorSize) {

            $browser->visit('/products/' . $product->slug)
                ->pause(1000)
                ->press('AGREGAR AL CARRITO DE COMPRAS')
                ->pause(300);

            $browser->visit('/products/' . $productWithColor->slug)
                ->pause(1000)
                ->select('@porduct_color_select', 1)
                ->pause(300)
                ->press('AGREGAR AL CARRITO DE COMPRAS')
                ->pause(300);

            $browser->visit('/products/' . $productWithColorSize->slug)
                ->pause(1000)
                ->select('@porduct_size_select', 1)
                ->pause(300)
                ->select('@porduct_color_select', 1)
                ->pause(300)
                ->press('AGREGAR AL CARRITO DE COMPRAS')
                ->pause(300);

            $browser->visit('/shopping-cart/')
                ->assertSee($product->name)
                ->assertSee($productWithColor->name)
                ->assertSee($productWithColorSize->name)
                ->screenshot('s3-t1-t7');
        });
    }

    /** @test */
    public function it_can_not_add_more_qty_than_a_product_without_color_or_size_has_in_stock()
    {
        $data = $this->createProducts();

        $this->browse(function (Browser $browser) use ($data) {

            $browser->visit('/products/' . $data['product111slug'])
                ->pause(1000)
                ->resize(560, 1200);

            for ($i = 1; $i < $data['product111quantity'] +5; $i++) {
                $browser->pause(200)
                    ->press('+');
            }

            $browser->pause(200)
                ->press('AGREGAR AL CARRITO DE COMPRAS')
                ->pause(200)
                ->visit('/shopping-cart/')
                ->pause(1000)
                ->assertSeeIn('@shopping_cart_page_product_qty', $data['product111quantity'])
                ->press('+')
                ->pause(200)
                ->assertSeeIn('@shopping_cart_page_product_qty', $data['product111quantity'])
                ->resize(1920, 1080)
                ->screenshot('s3-t4');
        });
    }

    /** @test */
    public function it_can_not_add_more_qty_than_a_product_with_color_has_in_stock()
    {
        $category = $this->createCategory();

        $brand = $this->createBrand();
        $this->attachBrandToCategory($category->id, $brand->id);

        $subcategory = $this->createSubcategory($category->id);

        $subcategoryColor = $this->createSubcategory($category->id, true);
        $productWithColor = $this->createProduct($subcategoryColor->id, $brand->id);

        $color = $this->createColor();
        $this->attachColorToProduct($productWithColor->id, $color->id);


        $this->browse(function (Browser $browser) use ($productWithColor) {

            $browser->visit('/products/' . $productWithColor->slug)
                ->pause(1000)
                ->select('@porduct_color_select', 1)
                ->pause(300)
                ->resize(560, 1200);

            for ($i = 1; $i < $productWithColor->stock +5; $i++) {
                $browser->pause(200)
                    ->press('+');
            }

            $browser->pause(200)
                ->press('AGREGAR AL CARRITO DE COMPRAS')
                ->pause(200)
                ->visit('/shopping-cart/')
                ->pause(1000)
                ->assertSeeIn('@shopping_cart_page_product_with_color_qty', $productWithColor->stock)
                ->press('+')
                ->pause(200)
                ->assertSeeIn('@shopping_cart_page_product_with_color_qty', $productWithColor->stock)
                ->resize(1920, 1080)
                ->screenshot('s3-t4-color');
        });
    }

    /** @test */
    public function it_can_not_add_more_qty_than_a_product_with_size_and_color_has_in_stock()
    {
        $category = $this->createCategory();

        $brand = $this->createBrand();
        $this->attachBrandToCategory($category->id, $brand->id);

        $subcategoryColorSize = $this->createSubcategory($category->id, true, true);
        $productWithColorSize = $this->createProduct($subcategoryColorSize->id, $brand->id);

        $color2 = $this->createColor('Verde');
        $size = $this->createSize($productWithColorSize->id);
        $this->attachSizeToColors($size->id);

        $this->browse(function (Browser $browser) use ($productWithColorSize) {

            $browser->visit('/products/' . $productWithColorSize->slug)
                ->pause(1000)
                ->resize(560, 1200)
                ->pause(300)
                ->select('@porduct_size_select', 1)
                ->pause(300)
                ->select('@porduct_color_select', 1)
                ->pause(300)
                ->resize(500, 1200);

            for ($i = 1; $i < $productWithColorSize->stock +5; $i++) {
                $browser->pause(200)
                    ->press('+');
            }

            $browser->pause(200)
                ->press('AGREGAR AL CARRITO DE COMPRAS')
                ->pause(200)
                ->visit('/shopping-cart/')
                ->pause(1000)
                ->assertSeeIn('@shopping_cart_page_product_with_size_qty', $productWithColorSize->stock)
                ->press('+')
                ->pause(200)
                ->assertSeeIn('@shopping_cart_page_product_with_size_qty', $productWithColorSize->stock)
                ->resize(1920, 1080)
                ->screenshot('s3-t4-size');
        });
    }

    /** @test */
    public function it_changes_the_total_qty_when_the_products_qty_increases_or_decreases()
    {
        $category = $this->createCategory();

        $brand = $this->createBrand();
        $this->attachBrandToCategory($category->id, $brand->id);

        $subcategory = $this->createSubcategory($category->id);
        $product = $this->createProduct($subcategory->id, $brand->id);

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
        $category = $this->createCategory();

        $brand = $this->createBrand();
        $this->attachBrandToCategory($category->id, $brand->id);

        $subcategory = $this->createSubcategory($category->id);
        $product = $this->createProduct($subcategory->id, $brand->id);

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

    // Inicio ejercicio 2
    /** @test */
    public function it_saved_the_cart_in_the_db_when_logout_and_is_retrieved_when_login() {
        $user = $this->createUser();

        $category = $this->createCategory();

        $brand = $this->createBrand();
        $this->attachBrandToCategory($category->id, $brand->id);

        $subcategory1 = $this->createSubcategory($category->id);
        $subcategory2 = $this->createSubcategory($category->id, true);

        $product1 = $this->createProduct($subcategory1->id, $brand->id);

        $product2 = $this->createProduct($subcategory2->id, $brand->id);
        $color = $this->createColor();
        $this->attachColorToProduct($product2->id, $color->id);

        $this->browse(function ($browser) use ($user, $product1, $product2) {
            $browser->loginAs(User::find($user->id))
                ->pause(1000)
                ->visit('/products/' . $product1->slug)
                ->pause(1000)
                ->press('AGREGAR AL CARRITO DE COMPRAS')
                ->pause(300)
                ->visit('/products/' . $product2->slug)
                ->pause(1000)
                ->select('@porduct_color_select', 1)
                ->pause(300)
                ->press('+')
                ->pause(300)
                ->press('AGREGAR AL CARRITO DE COMPRAS')
                ->click('@registered_user_img')
                ->pause(1000)
                ->clickLink('Finalizar sesión')
                ->pause(1000)
                ->visit('/shopping-cart')
                ->pause(1000)
                ->assertDontsee($product1->name)
                ->assertDontsee($product2->name)
                ->screenshot('s3-t11-unregistered-user')
                ->loginAs(User::find($user->id))
                ->pause(1000)
                ->visit('/shopping-cart')
                ->pause(1000)
                ->assertsee($product1->name)
                ->assertSeeIn('@shopping_cart_page_product_qty', 1)
                ->assertSourceHas($product1->price)
                ->assertsee($product2->name)
                ->assertSeeIn('@shopping_cart_page_product_with_color_qty', 2)
                ->assertSourceHas($product2->price)
                ->screenshot('s3-t11-registered-user');
        });
    }
    // Fin ejercicio 2
}
