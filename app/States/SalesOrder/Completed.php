<?php

declare(strict_types=1);

namespace App\States\SalesOrder;

class Completed extends SalesOrderState
{
    protected static ?string $name = 'completed';

    public function label(): string
    {
        return 'Selesai';
    }
}
