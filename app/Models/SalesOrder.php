<?php

namespace App\Models;

use App\States\SalesOrder\SalesOrderState;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Activitylog\Models\Concerns\LogsActivity;
use Spatie\Activitylog\Support\LogOptions;
use Spatie\ModelStates\HasStates;

class SalesOrder extends Model
{
    use HasStates, LogsActivity;

    protected $with = [
        'items',
    ];

    protected $casts = [
        'status' => SalesOrderState::class,
        'payment_payload' => 'json',
        'payment_paid_at' => 'datetime',
    ];

    public function items(): HasMany
    {
        return $this->hasMany(SalesOrderItem::class);
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['status', 'total']);
    }
}
