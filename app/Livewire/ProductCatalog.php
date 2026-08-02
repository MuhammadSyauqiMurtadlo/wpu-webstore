<?php

namespace App\Livewire;

use App\Data\ProductCollectionData;
use App\Data\ProductData;
use App\Models\Product;
use App\Models\Tag;
use Livewire\Component;
use Livewire\WithPagination;

class ProductCatalog extends Component
{
    use WithPagination;

    public $queryString = [
        'select_collections' => ['except' => []],
        'sort_by' => ['except' => 'newest'],
        'search' => ['except' => []],
    ];

    public array $select_collections = [];

    public string $search = '';

    public string $sort_by = 'newest'; // latest, price_asc, price_desc

    public function mount()
    {
        $this->validate();
    }

    public function rules()
    {
        return [
            'select_collections' => 'array',
            'select_collections.*' => 'integer|exists:tags,id',
            'search' => 'nullable|string|min:3|max:30',
            'sort_by' => 'in:newest,latest,price_asc,price_desc',
        ];
    }

    public function applyFilters()
    {
        $this->validate();
        $this->resetPage();
    }

    public function resetFilters()
    {
        $this->select_collections = [];
        $this->search = '';
        $this->sort_by = 'newest';

        $this->resetErrorBag();
        $this->resetPage();
    }

    public function render()
    {
        $collections = ProductCollectionData::collect([]);
        $products = ProductData::collect([]);
        // Early Return
        if ($this->getErrorBag()->isNotEmpty()) {
            return view('livewire.product-catalog', compact('collections', 'products'));
        }

        $collection_result = Tag::query()->WithType('collection')->WithCount('products')->get();
        // $result = Product::paginate(1); // ORM // Database Query

        $query = Product::query();
        if ($this->search) {
            $query->where('name', 'like', "%{$this->search}%");
        }

        if (! empty($this->select_collections)) {
            $query->whereHas('tags', function ($q) {
                $q->whereIn('id', $this->select_collections);
            });
        }

        switch ($this->sort_by) {
            case 'newest':
                $query->latest();
                break;
            case 'price_asc':
                $query->orderBy('price', 'asc');
                break;
            case 'price_desc':
                $query->orderBy('price', 'desc');
                break;
            default:
                $query->latest();
        }

        $products = ProductData::collect(
            $query->paginate(9)
        ); // DTO (Data Transfer Object)
        $collections = ProductCollectionData::collect($collection_result);

        return view('livewire.product-catalog', compact('products', 'collections'));
    }
}
