<?php

namespace Tests\Browser;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Color;
use App\Models\Image;
use App\Models\Product;
use App\Models\Size;
use App\Models\Subcategory;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\CreateData;
use Tests\DuskTestCase;

class NavigationMenuCartTest extends DuskTestCase
{
    use DatabaseMigrations;
    use CreateData;

    /** @test */
    public function it_adds_products_to_the_navigation_menu_cart()
    {
        $category = $this->createCategory();

        $brand = $this->createBrand();
        $this->attachBrandToCategory($category->id, $brand->id);

        $subcategory = $this->createSubcategory($category->id);
        $product = $this->createProduct($subcategory->id, $brand->id);


        $subcategoryColor = $this->createSubcategory($category->id, true);
        $productWithColor = $this->createProduct($subcategoryColor->id, $brand->id);

        $color = $this->createColor();
        $this->attachColorToProduct($productWithColor->id, $color->id);


        $subcategoryColorSize = $this->createSubcategory($category->id, true, true);
        $productWithColorSize = $this->createProduct($subcategoryColorSize->id, $brand->id);

        $color2 = $this->createColor('Verde');
        $size = $this->createSize($productWithColorSize->id);
        $this->attachSizeToColors($size->id);

        $product4 = $this->createProduct($subcategory->id, $brand->id);

        $this->browse(function (Browser $browser) use (
            $product, $productWithColor, $productWithColorSize, $product4) {

            $browser->visit('/products/' . $product->slug)
                ->pause(1000)
                ->press('AGREGAR AL CARRITO DE COMPRAS')
                ->pause(300);

            $browser->visit('/products/' . $productWithColor->slug)
                ->pause(1000)
                ->select('@porduct_color_select', 1)
                ->pause(300)
                ->press('AGREGAR AL CARRITO DE COMPRAS')
                ->pause(300);

            $browser->visit('/products/' . $productWithColorSize->slug)
                ->pause(1000)
                ->select('@porduct_size_select', 1)
                ->pause(300)
                ->select('@porduct_color_select', 1)
                ->pause(300)
                ->click('@navigation_menu_cart')
                ->pause(300)
                ->press('AGREGAR AL CARRITO DE COMPRAS')
                ->pause(300);

            $browser->visit('/products/' . $product4->slug)
                ->pause(1000)
                ->click('@navigation_menu_cart')
                ->pause(300)
                ->assertSee($product->name)
                ->assertSee($productWithColor->name)
                ->assertSee($productWithColorSize->name)
                ->screenshot('s3-t2');
        });
    }

    /** @test */
    public function the_red_circle_changes_when_adding_products()
    {
        $data = $this->createProducts(2);

        $this->browse(function (Browser $browser) use ($data) {

            $browser->visit('/products/' . $data['product111slug'])
                ->pause(1000)
                ->pause(1000)
                ->press('AGREGAR AL CARRITO DE COMPRAS')
                ->pause(1000)
                ->assertSeeIn('@cart_red_circle', '1');

            $browser->visit('/products/' . $data['product112slug'])
                ->pause(1000)
                ->press('AGREGAR AL CARRITO DE COMPRAS')
                ->pause(1000)
                ->assertSeeIn('@cart_red_circle', '2')
                ->screenshot('s3-t3');
        });
    }
}
