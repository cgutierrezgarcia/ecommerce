<?php

namespace Tests\Browser;


use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\CreateData;
use Tests\DuskTestCase;

class AdminPageTest extends DuskTestCase
{
    use DatabaseMigrations;
    use CreateData;

    /** @test */
    public function the_search_input_filter_the_products_or_show_them_all_when_empty()
    {
        $user = $this->createUserWithRole('admin');

        $product1 = $this->createProducts2();
        $product2 = $this->createProducts2();

        $this->browse(function (Browser $browser) use ($user, $product1, $product2) {
            $browser->loginAs(User::find($user->id))
                ->pause(1000)
                ->visit('/admin')
                ->pause(1000)
                ->assertSee($product1->name)
                ->assertSee($product2->name)
                ->screenshot('s4-t7-all')
                ->type('@search',  $product1->name)
                ->pause(300)
                ->assertSee($product1->name)
                ->assertDontSee($product2->name)
                ->screenshot('s4-t7-filter');
        });
    }
}
