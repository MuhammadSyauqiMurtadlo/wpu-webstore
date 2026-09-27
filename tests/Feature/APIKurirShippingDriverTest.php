<?php

use App\Data\CartData;
use App\Data\CartItemData;
use App\Data\RegionData;
use App\Data\ShippingServiceData;
use App\Drivers\Shipping\APIKurirShippingDriver;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use Spatie\LaravelData\DataCollection;

function apikurirTestArguments(): array
{
    return [
        new RegionData('32.73.14.1002', 'Jawa Barat', 'Kota Bandung', 'Cibeunying Kidul', 'Cikutra', '40124'),
        new RegionData('11.16.01.2035', 'Aceh', 'Kabupaten Aceh Selatan', 'Bakongan', 'Ujong Mangki', '23773'),
        new CartData(new DataCollection(CartItemData::class, [
            new CartItemData('SKU-1', 1, 100000, 150),
        ])),
        new ShippingServiceData('apikurir', 'jne-reguler', 'JNE', 'Regular'),
    ];
}

test('it maps a successful API Kurir rate response', function () {
    Http::preventStrayRequests();
    Http::fake([
        'sandbox.apikurir.id/*' => Http::response([
            'data' => [
                'rates' => [[
                    'minDuration' => 1,
                    'maxDuration' => 2,
                    'durationType' => 'day',
                    'price' => 18000,
                    'weight' => 150,
                    'logoUrl' => 'https://example.test/jne.png',
                ]],
            ],
        ]),
    ]);

    $rate = app(APIKurirShippingDriver::class)->getRate(...apikurirTestArguments());

    expect($rate)->not->toBeNull()
        ->and($rate->cost)->toBe(18000.0)
        ->and($rate->estimated_delivery)->toBe('1 - 2 - day')
        ->and($rate->weight)->toBe(150);

    Http::assertSent(fn (Request $request): bool => $request->url() === 'https://sandbox.apikurir.id/shipments/v1/open-api/rates'
        && $request['destination']['postalCode'] === '23773'
        && $request['logistics'] === ['JNE']
    );
});

test('it returns no API Kurir rate when the API returns an error', function () {
    Http::preventStrayRequests();
    Http::fake([
        'sandbox.apikurir.id/*' => Http::response(['message' => 'Unavailable'], 503),
    ]);

    $rate = app(APIKurirShippingDriver::class)->getRate(...apikurirTestArguments());

    expect($rate)->toBeNull();
});

test('it returns no API Kurir rate when the connection fails', function () {
    Http::preventStrayRequests();
    Http::fake([
        'sandbox.apikurir.id/*' => Http::failedConnection(),
    ]);

    $rate = app(APIKurirShippingDriver::class)->getRate(...apikurirTestArguments());

    expect($rate)->toBeNull();
});
