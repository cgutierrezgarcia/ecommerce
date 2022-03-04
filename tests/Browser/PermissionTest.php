<?php

namespace Tests\Browser;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\CreateData;
use Tests\DuskTestCase;

class PermissionTest extends DuskTestCase
{
    use DatabaseMigrations;
    use CreateData;

    /** @test */
    public function its_verifies_that_we_can_access_only_where_we_have_permissions()
    {
        $user = $this->createUser();

        $this->createRole();
        $user2 = $this->createUser();
        $this->assignRole($user2->id, 'admin');

        $category = $this->createCategory();

        $brand = $this->createBrand();
        $this->attachBrandToCategory($category->id, $brand->id);

        $subcategory = $this->createSubcategory($category->id);

        $product1 = $this->createProduct($subcategory->id, $brand->id);
        $product2 = $this->createProduct($subcategory->id, $brand->id);


        $this->browse(function (Browser $browser) use ($user, $user2) {
            $browser->loginAs(User::find($user->id))
                ->pause(1000)
                ->visit('/admin')
                ->pause(1000)
                ->assertSourceHas('User does not have the right roles.')
                ->visit('/admin/orders')
                ->pause(1000)
                ->assertSourceHas('User does not have the right roles.')
                ->visit('/admin/categories')
                ->pause(1000)
                ->assertSourceHas('User does not have the right roles.')
                ->visit('/admin/brands')
                ->pause(1000)
                ->assertSourceHas('User does not have the right roles.')
                ->visit('/admin/departments')
                ->pause(1000)
                ->assertSourceHas('User does not have the right roles.')
                ->visit('/admin/users')
                ->pause(1000)
                ->assertSourceHas('User does not have the right roles.')
                ->pause(300);

            $browser->loginAs(User::find($user2->id))
                ->pause(1000)
                ->visit('/admin')
                ->pause(1000)
                ->assertSourceMissing('User does not have the right roles.')
                ->visit('/admin/orders')
                ->pause(1000)
                ->assertSourceMissing('User does not have the right roles.')
                ->visit('/admin/categories')
                ->pause(1000)
                ->assertSourceMissing('User does not have the right roles.')
                ->visit('/admin/brands')
                ->pause(1000)
                ->assertSourceMissing('User does not have the right roles.')
                ->visit('/admin/departments')
                ->pause(1000)
                ->assertSourceMissing('User does not have the right roles.')
                ->visit('/admin/users')
                ->pause(1000)
                ->assertSourceMissing('User does not have the right roles.')
                ->pause(300);
        });


    }
}
