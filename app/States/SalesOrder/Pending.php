<?php

declare(strict_types=1);

namespace App\States\SalesOrder;

class Pending extends SalesOrderState
{
    protected static ?string $name = 'pending';

    public function label(): string
    {
        return 'Menunggu Pembayaran';
    }
}
