<?php

namespace Tests\Browser;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Image;
use App\Models\Product;
use App\Models\Subcategory;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Str;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class WelcomePageTest extends DuskTestCase
{
    use DatabaseMigrations;

    /** @test */
    function it_shows_the_categories_from_nav()
    {
        Category::factory()->create([
            'name' => 'Categoría s1 t1'
        ]);

        $this->browse(function (Browser $browser) {
            $browser->visit('/')
                    ->clickLink('Categorías')
                    ->assertSee('Categoría s1 t1')
                    ->screenshot('s1-t1');
        });
    }

    /** @test */
    function it_shows_the_subcategories_from_nav()
    {
        Category::factory()->create([
            'name' => 'Categoría s1 t2'
        ]);

        Subcategory::factory()->create([
            'name' => 'Subategoría s1 t2'
        ]);

        $this->browse(function (Browser $browser) {
            $browser->visit('/')
                    ->clickLink('Categorías')
                    ->assertSee('Categoría s1 t2')
                    ->assertSee('Subategoría s1 t2')
                    ->screenshot('s1-t2');
        });
    }

    /** @test */
    public function it_shows_the_correct_links_when_login_or_logout() {
        Category::factory()->create();

        $this->browse(function (Browser $browser) {
            $browser->visit('/')
                ->click('@unregistered_user_img')
                ->pause(1000)
                ->assertSee('Iniciar sesión')
                ->assertSee('Registrarse')
                ->screenshot('s2-t1-unregistered-user');
        });

        $user = User::factory()->create();

        $this->browse(function ($browser) use ($user) {
            $browser->visit('/login')
                ->type('email', $user->email)
                ->type('password', 'password')
                ->press('INICIAR SESIÓN')
                ->assertPathIs('/')
                ->click('@registered_user_img')
                ->pause(1000)
                ->assertSee('Perfil')
                ->assertSee('Finalizar sesión')
                ->screenshot('s2-t1-registered-user');
        });
    }

    /** @test */
    public function it_shows_five_products_from_one_category() {
        $category = Category::factory()->create();

        $brand = Brand::factory()->create();
        $category->brands()->attach($brand->id);

        $subcategory = Subcategory::factory()->create();

        $product1 = Product::factory()->create([
            'subcategory_id' => $subcategory->id
        ]);
        $product2 = Product::factory()->create([
            'subcategory_id' => $subcategory->id
        ]);
        $product3 = Product::factory()->create([
            'subcategory_id' => $subcategory->id
        ]);
        $product4 = Product::factory()->create([
            'subcategory_id' => $subcategory->id
        ]);
        $product5 = Product::factory()->create([
            'subcategory_id' => $subcategory->id
        ]);

        for ($i = 1; $i <= 5; $i++) {
            Image::factory()->create([
                'imageable_id' => $i,
                'imageable_type' => Product::class
            ]);
        }

        $this->browse(function (Browser $browser) use ($product1, $product2, $product3, $product4, $product5) {
            $browser->visit('/')
                ->pause(1000)
                ->assertSee(Str::limit($product1->name, 20))
                ->assertSee(Str::limit($product2->name, 20))
                ->assertSee(Str::limit($product3->name, 20))
                ->assertSee(Str::limit($product4->name, 20))
                ->assertSee(Str::limit($product5->name, 20))
                ->screenshot('s2-t2');
        });
    }

    /** @test */
    public function it_only_shows_the_published_products() {
        $category = Category::factory()->create();

        $brand = Brand::factory()->create();
        $category->brands()->attach($brand->id);

        $subcategory = Subcategory::factory()->create();

        $product1 = Product::factory()->create([
            'subcategory_id' => $subcategory->id
        ]);
        $product2 = Product::factory()->create([
            'subcategory_id' => $subcategory->id
        ]);
        $product3 = Product::factory()->create([
            'subcategory_id' => $subcategory->id
        ]);
        $product4 = Product::factory()->create([
            'subcategory_id' => $subcategory->id,
            'status' => 1
        ]);
        $product5 = Product::factory()->create([
            'subcategory_id' => $subcategory->id,
            'status' => 1
        ]);

        for ($i = 1; $i <= 5; $i++) {
            Image::factory()->create([
                'imageable_id' => $i,
                'imageable_type' => Product::class
            ]);
        }

        $this->browse(function (Browser $browser) use ($product1, $product2, $product3, $product4, $product5) {
            $browser->visit('/')
                ->pause(1000)
                ->assertSee(Str::limit($product1->name, 20))
                ->assertSee(Str::limit($product2->name, 20))
                ->assertSee(Str::limit($product3->name, 20))
                ->assertDontSee(Str::limit($product4->name, 20))
                ->assertDontSee(Str::limit($product5->name, 20))
                ->screenshot('s2-t3');
        });
    }
}
