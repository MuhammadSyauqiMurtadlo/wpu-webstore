<?php

namespace App\Listeners;

use App\Events\SalesOrderCreatedEvent;
use App\Service\PaymentMethodQueryService;

class GeneratePaymentLinkListener
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(SalesOrderCreatedEvent $event): void
    {
        app(PaymentMethodQueryService::class)
            ->getDriver(
                $event->sales_order->payment
            )->process(
                $event->sales_order
            );
    }
}
