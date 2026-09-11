<?php

declare(strict_types=1);

namespace App\Service;

use App\Data\CheckoutData;
use App\Data\SalesOrderData;

class CheckoutService
{
    public function makeAnOrder(CheckoutData $checkout_data): SalesOrderData
    {
        return new SalesOrderData;
    }
}
