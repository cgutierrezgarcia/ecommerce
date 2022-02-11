<?php

namespace Tests\Browser;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Image;
use App\Models\Product;
use App\Models\Subcategory;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Str;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class CategoryDetailPageTest extends DuskTestCase
{
    use DatabaseMigrations;

    /** @test */
    public function it_shows_the_category_details()
    {
        $category = Category::factory()->create();

        $brand1 = Brand::factory()->create([
            'name' => 'Marca1'
        ]);
        $brand2 = Brand::factory()->create([
            'name' => 'Marca2'
        ]);
        $category->brands()->attach($brand1->id);
        $category->brands()->attach($brand2->id);

        $subcategory = Subcategory::factory()->create();

        $product1 = Product::factory()->create([
            'name' => 'producto1',
            'subcategory_id' => $subcategory->id
        ]);
        $product2 = Product::factory()->create([
            'name' => 'producto2',
            'subcategory_id' => $subcategory->id
        ]);
        for ($i = 1; $i <= 2; $i++) {
            Image::factory()->create([
                'imageable_id' => $i,
                'imageable_type' => Product::class
            ]);
        }
        $this->browse(function (Browser $browser) use (
            $category, $subcategory, $brand1, $brand2, $product1, $product2) {

            $browser->visit('/')
                ->clickLink('Ver más')
                ->pause(1000)
                ->assertPathIs('/categories/' . $category->slug)
                ->assertSourceHas($category->name)
                ->assertSourceHas($brand1->name)
                ->assertSourceHas($brand2->name)
                ->assertSee(Str::limit($product1->name, 20))
                ->assertSee(Str::limit($product2->name, 20))
                ->screenshot('s2-t4');
        });
    }

    /** @test */
    public function it_filter_the_products_on_the_category_details()
    {
        $category = Category::factory()->create();

        $brand1 = Brand::factory()->create([
            'name' => 'Marca1'
        ]);
        $brand2 = Brand::factory()->create([
            'name' => 'Marca2'
        ]);
        $category->brands()->attach($brand1->id);
        $category->brands()->attach($brand2->id);

        $subcategory1 = Subcategory::factory()->create();
        $subcategory2 = Subcategory::factory()->create();

        $productSub1Brand1 = Product::factory()->create([
            'name' => 'producto1',
            'subcategory_id' => $subcategory1->id,
            'brand_id' => $brand1->id
        ]);
        $productSub1Brand2 = Product::factory()->create([
            'name' => 'producto2',
            'subcategory_id' => $subcategory1->id,
            'brand_id' => $brand2->id

        ]);
        $productSubcategory2 = Product::factory()->create([
            'name' => 'producto3',
            'subcategory_id' => $subcategory2->id,
            'brand_id' => $brand2->id

        ]);
        for ($i = 1; $i <= 3; $i++) {
            Image::factory()->create([
                'imageable_id' => $i,
                'imageable_type' => Product::class
            ]);
        }

        $this->browse(function (Browser $browser) use (
            $category, $subcategory1, $subcategory2, $brand1, $brand2,
            $productSub1Brand1, $productSub1Brand2, $productSubcategory2) {

            $browser->visit('/categories/' . $category->slug)
                ->pause(1000)
                ->clickLink($subcategory1->name)
                ->pause(1000)
                ->clickLink($brand1->name)
                ->assertSee(Str::limit($productSub1Brand1->name, 20))
                ->assertDontSee(Str::limit($productSub1Brand2->name, 20))
                ->screenshot('s2-t5-product-sub1-brand1')
                ->clickLink($brand2->name)
                ->pause(1000)
                ->assertSee(Str::limit($productSub1Brand2->name, 20))
                ->assertDontSee(Str::limit($productSub1Brand1->name, 20))
                ->screenshot('s2-t5-product-sub1-brand2')
                ->press('ELIMINAR FILTROS')
                ->clickLink($subcategory2->name)
                ->pause(1000)
                ->assertSee(Str::limit($productSubcategory2->name, 20))
                ->assertDontSee(Str::limit($productSub1Brand1->name, 20))
                ->screenshot('s2-t5-product-subcategory2');
        });
    }
}
