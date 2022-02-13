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

class CreateProductTest extends DuskTestCase
{
    use DatabaseMigrations;

    /** @test */
    public function it_checks_the_validation_and_creates_a_product()
    {
        $role = Role::create(['name' => 'admin']);
        $user = User::factory()->create()->assignRole('admin');

        $category = Category::factory()->create();

        $brand = Brand::factory()->create();
        $category->brands()->attach($brand->id);

        $subcategory = Subcategory::factory()->create([
            'color' => false,
            'size' => false
        ]);

        Image::factory()->create([
            'imageable_id' => 1,
            'imageable_type' => Product::class
        ]);

        $this->browse(function (Browser $browser) use ($user, $category, $subcategory, $brand) {
            $browser->loginAs(User::find($user->id))
                ->pause(1000)
                ->visit('/admin/products/create')
                ->pause(1000)
                ->press('CREAR PRODUCTO')
                ->pause(500)

                ->assertSourceHas('El campo category id es obligatorio.')
                ->assertSourceHas('El campo subcategory id es obligatorio.')
                ->assertSourceHas('El campo name es obligatorio.')
                ->assertSourceHas('El campo slug es obligatorio.')
                ->assertSourceHas('El campo description es obligatorio.')
                ->assertSourceHas('El campo brand id es obligatorio.')
                ->assertSourceHas('El campo price es obligatorio.')
                ->screenshot('s4-t8-validation')

                ->select('@category', $category->id)
                ->pause(300)
                ->select('@subcategory', $subcategory->id)
                ->pause(300)
                ->select('@brand', $brand->id)
                ->pause(300)

                ->type('@name',  'Producto1')
                ->pause(300)
                ->assertInputValue('@slug', 'producto1')
                ->pause(300)
                ->keys('@name', ['{tab}', 'Descripción del producto1'])
                ->pause(300)
                ->type('@price',  100)
                ->pause(300)
                ->type('@quantity',  5)
                ->pause(300)

                ->press('CREAR PRODUCTO')
                ->pause(1000)
                ->assertPathIs('/admin/products/producto1/edit')

                ->assertSelected('@category', $category->id)
                ->assertSelected('@subcategory', $subcategory->id)
                ->assertSelected('@brand', $brand->id)
                ->assertInputValue('@name', 'Producto1')
                ->assertInputValue('@slug', 'producto1')
                ->assertSourceHas('Descripción del producto1')
                ->assertInputValue('@price', '100')
                ->assertInputValue('@quantity', '5')

                ->resize(1920, 1580)
                ->screenshot('s4-t8-product-created')
                ->resize(1920, 1080);
        });
    }
}
