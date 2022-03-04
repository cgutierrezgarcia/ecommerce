<?php

namespace Tests;

use App\Models\Brand;
use App\Models\Category;
use App\Models\City;
use App\Models\Color;
use App\Models\Department;
use App\Models\District;
use App\Models\Image;
use App\Models\Product;
use App\Models\Size;
use App\Models\Subcategory;
use App\Models\User;
use Facebook\WebDriver\Chrome\ChromeOptions;
use Facebook\WebDriver\Remote\DesiredCapabilities;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use Laravel\Dusk\TestCase as BaseTestCase;

abstract class DuskTestCase extends BaseTestCase
{
    use CreatesApplication;

    /**
     * Prepare for Dusk test execution.
     *
     * @beforeClass
     * @return void
     */
    public static function prepare()
    {
        if (! static::runningInSail()) {
            static::startChromeDriver();
        }
    }

    /**
     * Create the RemoteWebDriver instance.
     *
     * @return \Facebook\WebDriver\Remote\RemoteWebDriver
     */
    protected function driver()
    {
        $options = (new ChromeOptions)->addArguments(collect([
            '--window-size=1920,1080',
        ])->unless($this->hasHeadlessDisabled(), function ($items) {
            return $items->merge([
                '--disable-gpu',
                '--headless',
                '--no-sandbox'
            ]);
        })->all());

        return RemoteWebDriver::create(
            $_ENV['DUSK_DRIVER_URL'] ?? 'http://localhost:9515',
            DesiredCapabilities::chrome()->setCapability(
                ChromeOptions::CAPABILITY, $options
            )
        );
    }

    /**
     * Determine whether the Dusk command has disabled headless mode.
     *
     * @return bool
     */
    protected function hasHeadlessDisabled()
    {
        return isset($_SERVER['DUSK_HEADLESS_DISABLED']) ||
               isset($_ENV['DUSK_HEADLESS_DISABLED']);
    }





    public function createUser()
    {
        return User::factory()->create();
    }

    public function createBrand()
    {
        return Brand::factory()->create();
    }

    public function attachBrandToCategory($categoryId = 1, $brandId = 1)
    {
        Category::find($categoryId)->brands()->attach($brandId);
    }

    public function createCategory($brandId = null)
    {
        if (is_null($brandId)) {
            return Category::factory()->create();
        }
        return Category::factory()->create([
            'brand_id' => $brandId,
        ]);
    }

    public function createSubcategory($categoryId = null, $color = false, $size = false)
    {
        if (is_null($categoryId)) {
            return Subcategory::factory()->create();
        }
        return Subcategory::factory()->create([
            'category_id' => $categoryId,
            'color' => $color,
            'size' => $size,
        ]);
    }

    public function createProduct($subcategoryId = 1, $brandId = 1, $status = 2, $quantity = 5)
    {
        $product = Product::factory()->create([
            'subcategory_id' => $subcategoryId,
            'brand_id' => $brandId,
            'status' => $status,
            'quantity' => $quantity,
        ]);

        Image::factory()->create([
            'imageable_id' => $product->id,
            'imageable_type' => Product::class
        ]);

        return $product;
    }

    public function createColor($name = 'Azul')
    {
        return Color::create(['name' => $name]);
    }

    public function attachColorToProduct($producId = 1, $colorId = 1, $quantity = 10)
    {
        Product::find($producId)->colors()->attach([
            $colorId => ['quantity' => $quantity,]
        ]);
    }

    public function createSize($productId = 1, $name = 'Talla XL')
    {
        return Size::create([
            'product_id' => $productId,
            'name' => $name,
        ]);
    }

    public function createSizeFromProduct($producId = 1, $sizeName = 'Talla XL')
    {
        Product::find($producId)->sizes()->create(['name' => $sizeName,]);
    }

    public function attachSizeToColors($sizeId = 1, $colorQuantity = 15,  $colorId = 1)
    {
        Size::find($sizeId)->colors()->attach([
            $colorId => ['quantity' => $colorQuantity]
        ]);
    }

    public function createImage($productId = 1)
    {
        return Image::factory()->create([
            'imageable_id' => $productId,
            'imageable_type' => Product::class
        ]);
    }

    public function createDepartment()
    {
        return Department::factory()->create();
    }

    public function createCity($departmentId = 1)
    {
        return City::factory()->create([
            'department_id' => $departmentId,
        ]);
    }

    public function createDistrict($cityId = 1)
    {
        return District::factory()->create([
            'city_id' => $cityId,
        ]);
    }
}
