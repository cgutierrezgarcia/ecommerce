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
use Spatie\Permission\Models\Role;

trait CreateData
{
    public function createUser()
    {
        return User::factory()->create();
    }

    public function createRole($name = 'admin')
    {
        Role::create(['name' => $name]);
    }

    public function assignRole($userId = 1, $roleName = 'admin')
    {
        User::find($userId)->assignRole($roleName);
    }

    public function createUserWithRole($roleName)
    {
        Role::create(['name' => $roleName]);

        $user = User::factory()->create();

        User::find($user->id)->assignRole($roleName);

        return $user;
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

    public function createCategories($categoryNum = 1, $brandId = null)
    {
        $data = [];

        for ($i = 1; $i <= $categoryNum; $i++) {
            if (is_null($brandId)) {
                $category = $this->createCategory();
            } else {
                $category = $this->createCategory($brandId);
            }

            $data += [
                'category'. $category->id => $category->id,
                'category'. $category->id . 'name' => $category->name,
            ];
        }

        return $data;
    }

    public function createCategoriesAndUsers($categoryNum = 1, $usersNum = 1, $brandId = null)
    {
        $data = [];

        for ($i = 1; $i <= $categoryNum; $i++) {
            if (is_null($brandId)) {
                $category = $this->createCategory();
            } else {
                $category = $this->createCategory($brandId);
            }

            $data += [
                'category'. $category->id => $category->id,
                'category'. $category->id . 'name' => $category->name,
            ];
        }

        for ($i = 1; $i <= $usersNum; $i++) {
            $user = $this->createUser();

            $data += [
                'user'. $user->id => $user->id,
                'user'. $user->id . 'name' => $user->name,
                'user'. $user->id . 'email' => $user->email,
            ];
        }

        return $data;
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

    public function createSubategories($subcategoryNum = 1, $categoryNum = 1, $color = false, $size = false)
    {
        $data = [];

        for ($i = 1; $i <= $categoryNum; $i++) {

            $category = Category::factory()->create();

            $data += [
                'category' . $i => $category->id,
                'category' . $i . 'name' => $category->name,
            ];

            for ($j = 1; $j <= $subcategoryNum; $j++) {

                $subcategory = Subcategory::factory()->create([
                    'category_id' => $category->id,
                    'color' => $color,
                    'size' => $size,
                ]);

                $data += [
                    'subcategory' . '' . $category->id . $j => $subcategory->id,
                    'subcategory' . '' . $category->id . $j . 'name' => $subcategory->name,
                ];
            }
        }

        return $data;
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

    public function createProducts($productsNum = 1, $subcategoryNum = 1, $categoryNum = 1,
                                   $status = true, $imageNum = 1, $productQuantity = 5,
                                   $color = false, $size = false)
    {
        $data = [];

        for ($i = 1; $i <= $categoryNum; $i++) {

            $category = Category::factory()->create();
            $brand = $this->createBrand();
            $this->attachBrandToCategory($category->id, $brand->id);

            $data += [
                'category' . $i => $category->id,
                'category' . $i . 'name' => $category->name,
                'brand' . $i . 'name' => $brand->name,
            ];

            for ($j = 1; $j <= $subcategoryNum; $j++) {

                $subcategory = Subcategory::factory()->create([
                    'category_id' => $category->id,
                    'color' => $color,
                    'size' => $size,
                ]);

                $data += [
                    'subcategory' . '' . $category->id . $j => $subcategory->id,
                    'subcategory' . '' . $category->id . $j . 'name' => $subcategory->name,
                ];

                for ($k = 1; $k <= $productsNum; $k++) {

                    if (!$status) {
                        $product = Product::factory()->create([
                            'subcategory_id' => $category->id,
                            'brand_id' => $brand->id,
                            'status' => 1,
                            'quantity' => $productQuantity,
                        ]);

                        Image::factory()->create([
                            'imageable_id' => $product->id,
                            'imageable_type' => Product::class
                        ]);

                        $data += [
                            'product' . '' . $i . '' . $j . '' . $k => $product->id,
                            'product' . '' . $i . '' . $j . '' . $k . 'name' => $product->name,
                            'product' . '' . $i . '' . $j . '' . $k . 'slug' => $product->slug,
                            'product' . '' . $i . '' . $j . '' . $k . 'subcategory_id' => $product->subcategory_id,
                            'product' . '' . $i . '' . $j . '' . $k . 'brand_id' => $product->brand_id,
                            'product' . '' . $i . '' . $j . '' . $k . 'status' => $product->status,
                            'product' . '' . $i . '' . $j . '' . $k . 'quantity' => $product->quantity,
                        ];

                        $status = true;
                    } else {
                        $product = Product::factory()->create([
                            'subcategory_id' => $category->id,
                            'brand_id' => $brand->id,
                            'status' => 2,
                            'quantity' => $productQuantity,
                        ]);

                        $data += [
                            'product' . '' . $i . '' . $j . '' . $k => $product->id,
                            'product' . '' . $i . '' . $j . '' . $k . 'name' => $product->name,
                            'product' . '' . $i . '' . $j . '' . $k . 'slug' => $product->slug,
                            'product' . '' . $i . '' . $j . '' . $k . 'description' => $product->description,
                            'product' . '' . $i . '' . $j . '' . $k . 'price' => $product->price,
                            'product' . '' . $i . '' . $j . '' . $k . 'subcategory_id' => $product->subcategory_id,
                            'product' . '' . $i . '' . $j . '' . $k . 'brand_id' => $product->brand_id,
                            'product' . '' . $i . '' . $j . '' . $k . 'status' => $product->status,
                            'product' . '' . $i . '' . $j . '' . $k . 'quantity' => $product->quantity,
                        ];

                        for ($l = 1; $l <= $imageNum; $l++) {
                            $image = Image::factory()->create([
                                'imageable_id' => $product->id,
                                'imageable_type' => Product::class
                            ]);

                            $data += [
                                'product' . '' . $i . '' . $j . '' . $k . 'image' . $l => $image->url,
                            ];
                        }
                    }
                }
            }
        }

        return $data;
    }

    public function createProducts2($status = 2, $quantity = 5, $withColor = false, $withSize = false, $colorName = 'Azul', $sizeName = 'Talla XL') {
        $category = $this->createCategory();

        $brand = $this->createBrand();
        $this->attachBrandToCategory($category->id, $brand->id);

        $subcategory = $this->createSubcategory($category->id, $withColor, $withSize);

        $product = $this->createProduct($subcategory->id, $brand->id, $status, $quantity);

        if ($withColor) {
            $color = $this->createColor($colorName);
            $this->attachColorToProduct($product->id, $color->id, $quantity);
        }

        if ($withSize && $withColor) {
            $this->createColor($colorName);
            $size = $this->createSize($product->id, $sizeName);
            $this->attachSizeToColors($size->id);
        }

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

    public function createDepartmentWithCityAndDistrict()
    {
        $department = Department::factory()->create();

        $city = City::factory()->create([
            'department_id' => $department->id,
        ]);

        District::factory()->create([
            'city_id' => $city->id,
        ]);

        return $department;
    }
}
