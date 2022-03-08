<?php

namespace App\Filters;

use Illuminate\Database\Eloquent\Builder;

class ProductFilter extends QueryFilter
{
    public function rules(): array
    {
        return [
            'search' => 'filled',
            'categorySearch' => 'filled',
            'subcategorySearch' => 'filled',
            'brandSearch' => 'filled',
            'status' => 'in:1,2',
            'colorsFilter' => 'filled',
            'sizeFilter' => 'filled',
        ];
    }

    public function search($query, $search) {
        return $query->where('name', 'LIKE', "%{$search}%");
    }

    public function categorySearch($query, $categorySearch) {
        return $query->whereHas('subcategory', function ($query) use ($categorySearch) {
            $query->whereHas('category', function ($query) use ($categorySearch) {
                $query->where('name', 'LIKE', "%{$categorySearch}%");
            });
        });
    }

    public function subcategorySearch($query, $subcategorySearch) {
        return $query->whereHas('subcategory', function ($query) use ($subcategorySearch) {
            $query->where('name', 'LIKE', "%{$subcategorySearch}%");
        });
    }

    public function brandSearch($query, $brandSearch) {
        return $query->whereHas('brand', function ($query) use ($brandSearch) {
            $query->where('name', 'LIKE', "%{$brandSearch}%");
        });
    }

    public function status($query, $status) {
        return $query->where('status', $status);
    }

    public function colorsFilter($query) {
        return $query->whereHas('colors');
    }

    public function sizeFilter($query) {
        return $query->whereHas('sizes');
    }

    public function minPriceSearch($query, $minPriceSearch, $maxPriceSearch) {
        return $query->whereBetween('price', [$minPriceSearch, $maxPriceSearch]);
    }
}
