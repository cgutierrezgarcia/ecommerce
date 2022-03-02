<?php

namespace App\Http\Livewire\Admin;

use App\Models\Product;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Component;
use Livewire\WithPagination;

class Tarea2 extends Component
{
    use WithPagination;

    public $search;
    public $paginate = 10;

    public $order = 'asc';
    public $orderColumn = 'id';

    public $show = false;
    public $idSH, $nameSH, $slugSH, $descriptionSH, $categorySH,
        $subcategorySH, $brandSH, $statusSH, $priceSH, $colorSH,
        $sizeSH, $stockSH, $createdAtSH, $updatedAtSH, $editSH = true;

    public $showFilters = true;
    public $categorySearch, $subcategorySearch, $brandSearch, $status, $priceSearch, $colorsFilter, $sizeFilter;

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function orderByColumn($column) {
        if ($column === $this->orderColumn) {
            if ($this->order === 'asc') {
                $this->order = 'desc';
            } else {
                $this->order = 'asc';
            }
        } else {
            $this->orderColumn = $column;
            $this->order = 'asc';
        }
    }

    public function resetFilters()
    {
        $this->reset(['search', 'categorySearch', 'subcategorySearch', 'brandSearch', 'status', 'priceSearch', 'colorsFilter', 'sizeFilter', 'page']);
    }

    public function render()
    {
        $products = Product::query()->where('name', 'LIKE', "%{$this->search}%");

        if ($this->categorySearch) {
            $products = $products->whereHas('subcategory', function (Builder $query) {
                $query->whereHas('category', function (Builder $query) {
                    $query->where('name', 'LIKE', "%{$this->categorySearch}%");
                });
            });
        }

        if ($this->subcategorySearch) {
            $products = $products->whereHas('subcategory', function (Builder $query) {
                $query->where('name', 'LIKE', "%{$this->subcategorySearch}%");
            });
        }

        if ($this->brandSearch) {
            $products = $products->whereHas('brand', function (Builder $query) {
                $query->where('name', 'LIKE', "%{$this->brandSearch}%");
            });
        }

        if ($this->status) {
            $products = $products->where('status', $this->status);
        }

        if ($this->priceSearch) {
            $products = $products->where('price', 'LIKE', "%{$this->priceSearch}%");
        }

        if ($this->colorsFilter) {
            $products = $products->whereHas('colors');
        }

        if ($this->sizeFilter) {
            $products = $products->whereHas('sizes');
        }

        $products = $products->orderBy($this->orderColumn, $this->order)->paginate($this->paginate);

        return view('livewire.admin.tarea2', compact('products'))
            ->layout('layouts.admin');
    }
}
