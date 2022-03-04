<?php

namespace Tests\Browser;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\CreateData;
use Tests\DuskTestCase;

class SearchPageTest extends DuskTestCase
{
    use DatabaseMigrations;
    use CreateData;

    /** @test */
    public function the_search_filter_the_products_or_show_them_all_when_empty()
    {
        $data = $this->createProducts(2);

        $this->browse(function (Browser $browser) use ($data) {
            $browser->visit('/search?name=' . $data['product111name'])
                ->pause(1000)
                ->assertSee($data['product111name'])
                ->assertDontSee($data['product112name'])
                ->screenshot('s3-t6-filter')
                ->pause(300);

            $browser->visit('/search?name=')
                ->pause(1000)
                ->assertSee($data['product111name'])
                ->assertSee($data['product112name'])
                ->screenshot('s3-t6-empty');
        });
    }
}
