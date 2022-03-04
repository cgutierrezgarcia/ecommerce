<?php

namespace Tests\Browser;

use App\Models\Brand;
use App\Models\Category;
use App\Models\City;
use App\Models\Department;
use App\Models\District;
use App\Models\Image;
use App\Models\Product;
use App\Models\Subcategory;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class CreateOrderPageTest extends DuskTestCase
{
    use DatabaseMigrations;

    /** @test */
    public function it_only_shows_create_order_page_to_a_registered_user() {
        $category = $this->createCategory();

        $brand = $this->createBrand();
        $this->attachBrandToCategory($category->id, $brand->id);

        $this->createSubcategory($category->id);
        $this->createSubcategory($category->id);

        $this->browse(function (Browser $browser) {
            $browser->visit('/orders/create')
                ->pause(1000)
                ->assertPathIs('/login')
                ->assertPathIsNot('/orders/create')
                ->screenshot('s3-t10-unregistered-user');
        });

        $user = $this->createUser();

        $this->browse(function ($browser) use ($user) {
            $browser->loginAs(User::find($user->id))
                ->pause(1000)
                ->visit('/orders/create')
                ->pause(1000)
                ->assertPathIs('/orders/create')
                ->assertPathIsNot('/login')
                ->screenshot('s3-t10-registered-user');
        });
    }
    /** @test */
    public function it_shows_the_form_according_to_the_option_chosen_in_the_radio_btn() {
        $user = $this->createUser();

        $category = $this->createCategory();

        $brand = $this->createBrand();
        $this->attachBrandToCategory($category->id, $brand->id);

        $this->createSubcategory($category->id);
        $this->createSubcategory($category->id);

        $this->browse(function ($browser) use ($user) {
            $browser->loginAs(User::find($user->id))
                ->pause(1000)
                ->visit('/orders/create')
                ->pause(1000)
                ->radio('envio_type', '2')
                ->pause(300)
                ->assertSee('Departamento')
                ->assertSee('Referencia')
                ->screenshot('s3-t12-send-to-home')
                ->radio('envio_type', '1')
                ->pause(300)
                ->assertSee('Recojo en tienda')
                ->assertDontSee('Departamento')
                ->assertDontSee('Referencia')
                ->screenshot('s3-t12-send-to-store');
        });
    }

    /** @test */
    public function it_checks_that_the_order_is_created_and_destroys_the_cart_and_redirects_to_payment() {
        $user = $this->createUser();

        $category = $this->createCategory();

        $brand = $this->createBrand();
        $this->attachBrandToCategory($category->id, $brand->id);

        $subcategory = $this->createSubcategory($category->id);

        $product = $this->createProduct($subcategory->id, $brand->id);

        $this->browse(function ($browser) use ($user, $product) {
            $browser->loginAs(User::find($user->id))
                ->pause(1000)
                ->visit('/products/' . $product->slug)
                ->pause(1000)
                ->press('AGREGAR AL CARRITO DE COMPRAS')
                ->pause(300)
                ->visit('/orders/create')
                ->pause(1000)
                ->type('@name', 'Nombre')
                ->type('@phone', '123')
                ->press('CONTINUAR CON LA COMPRA')
                ->pause(1000)
                ->assertPathIs('/orders/1/payment')
                ->screenshot('s3-t13-redirect')
                ->visit('/shopping-cart')
                ->pause(1000)
                ->assertDontSee($product->name)
                ->screenshot('s3-t13-cart-is-empty');
        });
    }

    /** @test */
    public function it_shows_that_the_chained_selects_are_loaded_correctly_depending_on_the_chosen_option() {
        $user = $this->createUser();

        $category = $this->createCategory();

        $brand = $this->createBrand();
        $this->attachBrandToCategory($category->id, $brand->id);

        $subcategory = $this->createSubcategory($category->id);

        $product = $this->createProduct($subcategory->id, $brand->id);

        $department = $this->createDepartment();
        $city = $this->createCity($department->id);
        $district = $this->createDistrict($city->id);

        $this->browse(function ($browser) use ($user, $product, $department, $city, $district) {
            $browser->loginAs(User::find($user->id))
                ->pause(1000)
                ->visit('/products/' . $product->slug)
                ->pause(1000)
                ->press('AGREGAR AL CARRITO DE COMPRAS')
                ->pause(300)
                ->visit('/orders/create')
                ->pause(1000)
                ->radio('envio_type', '2')
                ->pause(300)

                ->assertSelectMissingOption('@city', $city->id)
                ->assertSelectMissingOption('@district', $district->id)
                ->select('@department', 1)
                ->pause(300)
                ->assertSelected('@department', $department->id)

                ->assertSelectMissingOption('@district', $district->id)
                ->select('@city', 1)
                ->pause(300)
                ->assertSelected('@city', $city->id)

                ->select('@district', 1)
                ->pause(300)
                ->assertSelected('@district', $district->id)
                ->screenshot('s3-t14');
        });
    }
}
