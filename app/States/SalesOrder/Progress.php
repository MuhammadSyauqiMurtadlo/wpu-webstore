<?php

declare(strict_types=1);

namespace App\States\SalesOrder;

class Progress extends SalesOrderState
{
    protected static ?string $name = 'progress';

    public function label(): string
    {
        return 'Proses';
    }
}
