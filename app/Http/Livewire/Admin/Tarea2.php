<?php

namespace App\Http\Livewire\Admin;

use App\Filters\ProductFilter;
use App\Models\Order;
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

    public $showFilters = false;
    public $categorySearch, $subcategorySearch, $brandSearch, $status,
        $minPriceSearch, $maxPriceSearch, $colorsFilter, $sizeFilter,
        $minDateSearch, $maxDateSearch;

    public $orders;

    public function mount() {
        $this->minPriceSearch = Product::all('price')->min()->price;
        $this->maxPriceSearch = Product::all('price')->max()->price;

        $this->minDateSearch = date(Product::all('created_at')->min()->created_at);
        $this->maxDateSearch = date(Product::all('created_at')->max()->created_at);

        $this->orders = Order::all();
    }

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
        $this->reset([
            'search', 'categorySearch', 'subcategorySearch', 'brandSearch',
            'status', 'minPriceSearch', 'maxPriceSearch', 'colorsFilter',
            'sizeFilter', 'minDateSearch', 'maxDateSearch', 'page',
        ]);

        $this->minPriceSearch = Product::all('price')->min()->price;
        $this->maxPriceSearch = Product::all('price')->max()->price;

        $this->minDateSearch = date(Product::all('created_at')->min()->created_at);
        $this->maxDateSearch = date(Product::all('created_at')->max()->created_at);
    }

    public function getProducts(ProductFilter $productFilter) {
        return Product::query()
            ->filterBy($productFilter, [
                'search' => $this->search,
                'categorySearch' => $this->categorySearch,
                'subcategorySearch' => $this->subcategorySearch,
                'brandSearch' => $this->brandSearch,
                'status' => $this->status,
                'colorsFilter' => $this->colorsFilter,
                'sizeFilter' => $this->sizeFilter,
            ])
            ->orderBy($this->orderColumn, $this->order)
            ->paginate($this->paginate);
    }

    public function render(ProductFilter $productFilter)
    {
        return view('livewire.admin.tarea2', ['products' => $this->getProducts($productFilter)])
            ->layout('layouts.admin');
    }
}
