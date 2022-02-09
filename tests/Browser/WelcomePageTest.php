<?php

namespace Tests\Browser;

use App\Models\Category;
use App\Models\Subcategory;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class WelcomePageTest extends DuskTestCase
{
    use DatabaseMigrations;

    /** @test */
    function can_see_categories_from_nav()
    {
        Category::factory()->create([
            'name' => 'Categoría s1 t1'
        ]);

        $this->browse(function (Browser $browser) {
            $browser->visit('/')
                    ->clickLink('Categorías')
                    ->assertSee('Categoría s1 t1')
                    ->screenshot('s1 t1');
        });
    }

    /** @test */
    function can_see_subcategories_from_nav()
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
                    ->screenshot('s1 t2');
        });
    }
}
