<?php

declare(strict_types=1);

namespace App\States\SalesOrder;

use Laravel\Prompts\Progress;
use Override;
use Spatie\ModelStates\State;
use Spatie\ModelStates\StateConfig;

abstract class SalesOrderState extends State
{
    abstract public function label(): string;

    #[Override]
    public static function config(): StateConfig
    {
        return parent::config()
            ->default(Pending::class)
            ->allowAllTransitions(Pending::class, Progress::class)
            ->allowAllTransitions(Pending::class, Cancel::class)
            ->allowAllTransitions(Pending::class, Completed::class);
    }
}
