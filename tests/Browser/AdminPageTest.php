<?php

namespace Tests\Browser;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Image;
use App\Models\Product;
use App\Models\Subcategory;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Spatie\Permission\Models\Role;
use Tests\DuskTestCase;

class AdminPageTest extends DuskTestCase
{
    use DatabaseMigrations;

    /** @test */
    public function the_search_input_filter_the_products_or_show_them_all_when_empty()
    {
        $this->createRole();

        $user = User::factory()->create()->assignRole('admin');

        $category = $this->createCategory();

        $brand = $this->createBrand();
        $this->attachBrandToCategory($category->id, $brand->id);

        $subcategory = $this->createSubcategory($category->id);

        $product1 = $this->createProduct($subcategory->id, $brand->id);
        $product2 = $this->createProduct($subcategory->id, $brand->id);


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
