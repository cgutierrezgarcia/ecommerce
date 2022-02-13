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
        $category = Category::factory()->create();

        $brand = Brand::factory()->create();
        $category->brands()->attach($brand->id);

        Subcategory::factory()->create([
            'category_id' => $category->id,
            'color' => false,
            'size' => false
        ]);
        Subcategory::factory()->create([
            'category_id' => $category->id,
            'color' => false,
            'size' => false
        ]);

        $this->browse(function (Browser $browser) {
            $browser->visit('/orders/create')
                ->pause(1000)
                ->assertPathIs('/login')
                ->assertPathIsNot('/orders/create')
                ->screenshot('s3-t10-unregistered-user');
        });

        $user = User::factory()->create();

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
        $user = User::factory()->create();

        $category = Category::factory()->create();

        $brand = Brand::factory()->create();
        $category->brands()->attach($brand->id);

        Subcategory::factory()->create([
            'category_id' => $category->id,
            'color' => false,
            'size' => false
        ]);
        Subcategory::factory()->create([
            'category_id' => $category->id,
            'color' => false,
            'size' => false
        ]);

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
        $user = User::factory()->create();

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
        $user = User::factory()->create();

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

        $department = Department::factory()->create();
        $city = City::factory()->create([
            'department_id' => $department->id
        ]);
        $district = District::factory()->create([
            'city_id' => $city->id
        ]);

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
