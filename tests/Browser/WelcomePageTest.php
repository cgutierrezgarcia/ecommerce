<?php

namespace Tests\Browser;

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
        $category1 = $this->createCategory();
        $category2 = $this->createCategory();

        $this->browse(function (Browser $browser) use ($category1, $category2) {
            $browser->visit('/')
                    ->clickLink('Categorías')
                    ->assertSee($category1->name)
                    ->assertSee($category2->name)
                    ->screenshot('s1-t1');
        });
    }

    /** @test */
    function it_shows_the_subcategories_from_nav()
    {
        $category1 = $this->createCategory();
        $subcategory1a = $this->createSubcategory($category1->id);
        $subcategory1b = $this->createSubcategory($category1->id);

        $category2 = $this->createCategory();
        $subcategory2a = $this->createSubcategory($category2->id);
        $subcategory2b = $this->createSubcategory($category2->id);

        $this->browse(function (Browser $browser) use (
            $category1, $subcategory1a, $subcategory1b,
            $category2, $subcategory2a, $subcategory2b) {

            $browser->visit('/')
                    ->clickLink('Categorías')
                    ->assertSee($category1->name)
                    ->assertSee($subcategory1a->name)
                    ->assertSee($subcategory1b->name)
                    ->assertSee($category2->name)
                    ->assertDontSee($subcategory2a->name)
                    ->assertDontSee($subcategory2b->name)
                    ->screenshot('s1-t2');
        });
    }

    /** @test */
    public function it_shows_the_correct_links_when_login_or_logout() {
        $this->createCategory();

        $this->browse(function (Browser $browser) {
            $browser->visit('/')
                ->click('@unregistered_user_img')
                ->pause(1000)
                ->assertSee('Iniciar sesión')
                ->assertSee('Registrarse')
                ->assertDontSee('Perfil')
                ->assertDontSee('Finalizar sesión')
                ->screenshot('s2-t1-unregistered-user');
        });

        $user = $this->createUser();

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
                ->assertDontSee('Iniciar sesión')
                ->assertDontSee('Registrarse')
                ->screenshot('s2-t1-registered-user');
        });
    }

    /** @test */
    public function it_shows_five_products_from_one_category() {
        $category = $this->createCategory();

        $brand = $this->createBrand();
        $this->attachBrandToCategory($category->id, $brand->id);

        $subcategory = $this->createSubcategory($category->id);

        $product1 = $this->createProduct($subcategory->id);
        $product2 = $this->createProduct($subcategory->id);
        $product3 = $this->createProduct($subcategory->id);
        $product4 = $this->createProduct($subcategory->id);
        $product5 = $this->createProduct($subcategory->id);

        $this->browse(function (Browser $browser) use (
            $product1, $product2, $product3, $product4, $product5) {

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
        $category = $this->createCategory();

        $brand = $this->createBrand();
        $this->attachBrandToCategory($category->id, $brand->id);

        $subcategory = $this->createSubcategory($category->id);

        $product1 = $this->createProduct($subcategory->id);
        $product2 = $this->createProduct($subcategory->id);

        $product3 = $this->createProduct($subcategory->id, $brand->id, 1);
        $product4 = $this->createProduct($subcategory->id, $brand->id, 1);

        $this->browse(function (Browser $browser) use (
            $product1, $product2, $product3, $product4) {

            $browser->visit('/')
                ->pause(1000)
                ->assertSee(Str::limit($product1->name, 20))
                ->assertSee(Str::limit($product2->name, 20))
                ->assertDontSee(Str::limit($product3->name, 20))
                ->assertDontSee(Str::limit($product4->name, 20))
                ->screenshot('s2-t3');
        });
    }
}
