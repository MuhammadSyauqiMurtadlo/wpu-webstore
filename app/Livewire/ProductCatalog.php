<?php

namespace App\Livewire;

use App\Data\ProductCollectionData;
use App\Data\ProductData;
use App\Models\Product;
use App\Models\Tag;
use Livewire\Component;

class ProductCatalog extends Component
{
    public function render()
    {
        $collection_result = Tag::query()->WithType('collection')->WithCount('products')->get();
        $result = Product::paginate(1); // ORM // Database Query

        $products = ProductData::collect($result); // DTO (Data Transfer Object)
        $collections = ProductCollectionData::collect($collection_result);

        return view('livewire.product-catalog', compact('products', 'collections'));
    }
}
