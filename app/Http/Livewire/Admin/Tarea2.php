<?php

namespace App\Http\Livewire\Admin;

use App\Models\Product;
use Livewire\Component;
use Livewire\WithPagination;

class Tarea2 extends Component
{
    use WithPagination;

    public $search;
    public $paginate = 10;

    public $order = 'asc';
    public $orderColumn = 'id';

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

    public function render()
    {
        $products = Product::where('name', 'LIKE', "%{$this->search}%")->orderBy($this->orderColumn, $this->order)->paginate($this->paginate);

        return view('livewire.admin.tarea2', compact('products'))
            ->layout('layouts.admin');
    }
}
