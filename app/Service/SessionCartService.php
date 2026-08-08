<?php

declare(strict_types=1);

namespace App\Service;

use App\Contract\CartServiceInterface;
use App\Data\CartData;
use App\Data\CartItemData;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Session;
use Spatie\LaravelData\DataCollection;

class SessionCartService implements CartServiceInterface
{
    protected string $session_key = 'cart';

    protected function load(): DataCollection
    {
        $raw = Session::get($this->session_key, []);

        return new DataCollection(CartItemData::class, $raw);
    }

    /** @param Collection<int, CartItemData> $items */
    protected function save(Collection $collection): void
    {
        Session::put($this->session_key, $collection->toArray());
    }

    public function addOrUpdate(CartItemData $item): void
    {
        // 1. Tarik Data
        $collection = $this->load()->toCollection();
        $updated = false;
        // 2. Mapping Data
        $cart = $collection->map(function (CartItemData $i) use ($item, &$updated) {
            if ($i->sku === $item->sku) {
                $updated = true;

                return $item; // Update the existing item
            }

            return $i; // Keep the existing item
        })->values()->collect(); // Reindex the collection
        // 3. Jika tidak ada data yang diupdate, tambahkan item baru
        if (! $updated) {
            $cart->push($item);
        }

        // 4. Simpan kembali ke session
        $this->save($cart);
    }

    public function remove(string $sku): void
    {
        $cart = $this->load()->toCollection();
        $cart = $cart->reject(fn (CartItemData $i) => $i->sku === $sku)->values()->collect(); // Reindex the collection
        $this->save($cart);
    }

    public function getItemBySku(string $sku): ?CartItemData
    {
        return $this->load()->toCollection()->first(fn (CartItemData $item) => $item->sku === $sku);
    }

    public function all(): CartData
    {
        return new CartData($this->load());
    }
}
