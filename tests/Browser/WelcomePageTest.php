<?php

namespace Tests\Browser;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Str;
use Laravel\Dusk\Browser;
use Tests\CreateData;
use Tests\DuskTestCase;

class WelcomePageTest extends DuskTestCase
{
    use DatabaseMigrations;
    use CreateData;

    /** @test */
    function it_shows_the_categories_from_nav()
    {
        $data = $this->createCategories(2);

        $this->browse(function (Browser $browser) use ($data) {
            $browser->visit('/')
                    ->clickLink('Categorías')
                    ->assertSee($data['category1name'])
                    ->assertSee($data['category1name'])
                    ->screenshot('s1-t1');
        });
    }

    /** @test */
    function it_shows_the_subcategories_from_nav()
    {
        $data = $this->createSubategories(2, 2);

        $this->browse(function (Browser $browser) use ($data) {

            $browser->visit('/')
                    ->clickLink('Categorías')
                    ->assertSee($data['category1name'])
                    ->assertSee($data['subcategory11name'])
                    ->assertSee($data['subcategory12name'])
                    ->assertSee($data['category2name'])
                    ->assertDontSee($data['subcategory21name'])
                    ->assertDontSee($data['subcategory22name'])
                    ->screenshot('s1-t2');
        });
    }

    /** @test */
    public function it_shows_the_correct_links_when_login_or_logout() {
        $data = $this->createCategoriesAndUsers();

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

        $this->browse(function ($browser) use ($data) {
            $browser->visit('/login')
                ->type('email', $data['user1email'])
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

        $data = $this->createProducts(5);

        $this->browse(function (Browser $browser) use ($data) {

            $browser->visit('/')
                ->pause(1000)
                ->assertSee(Str::limit($data['product111name'], 20))
                ->assertSee(Str::limit($data['product111name'], 20))
                ->assertSee(Str::limit($data['product111name'], 20))
                ->assertSee(Str::limit($data['product111name'], 20))
                ->assertSee(Str::limit($data['product111name'], 20))
                ->screenshot('s2-t2');
        });
    }

    /** @test */
    public function it_only_shows_the_published_products() {
        $data = $this->createProducts(2, 1, 1, false);

        $this->browse(function (Browser $browser) use ($data) {

            $browser->visit('/')
                ->pause(1000)
                ->assertSee(Str::limit($data['product112name'], 20))
                ->assertDontSee(Str::limit($data['product111name'], 20))
                ->screenshot('s2-t3');
        });
    }
}
