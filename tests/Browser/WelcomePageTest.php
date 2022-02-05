<?php

namespace Tests\Browser;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class WelcomePageTest extends DuskTestCase
{
    /** @test */
    function can_see_categories_from_nav()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/')
                    ->clickLink('Categorías')
                    ->assertSee('Celulares y tablets');
        });
    }

    /** @test */
    function can_see_subcategories_from_nav()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/')
                ->clickLink('Categorías')
                ->assertSee('Smartwatches');
        });
    }
}
