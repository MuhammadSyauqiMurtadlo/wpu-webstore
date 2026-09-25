<?php

namespace App\Filament\Resources\SalesOrderResource\Pages;

use App\Data\SalesOrderData;
use App\Filament\Resources\SalesOrderResource;
use App\Service\SalesOrderService;
use App\States\SalesOrder\Pending;
use App\States\SalesOrder\Progress;
use Filament\Actions\Action;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Pages\ViewRecord;

class ViewSalesOrder extends ViewRecord
{
    protected static string $resource = SalesOrderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('Process')
                ->icon('heroicon-o-arrow-path-rounded-square')
                ->modalWidth('sm')
                ->visible(fn () => in_array(get_class($this->record->status), [
                    Pending::class,
                    Progress::class,
                ]))
                ->form(function () {
                    $options = collect($this->record->status->transitionableStateInstances())
                        ->mapWithKeys(fn ($state) => [
                            $state->getValue() => $state->label(),
                        ])
                        ->toArray();

                    return [
                        Radio::make('status')
                            ->label('Status')
                            ->options($options)
                            ->required()
                            ->inline(),
                    ];
                })
                ->action(function (array $data) {
                    $this->record->status->transitionTo(data_get($data, 'status'));
                }),
            Action::make('Input Resi Pengiriman')
                ->icon('heroicon-o-truck')
                ->modalWidth('sm')
                ->modalHeading('Input Nomor Resi')
                ->visible(function () {
                    $status = get_class($this->record->status);

                    $valid_statuses = [
                        Progress::class,
                        Pending::class,
                    ];

                    return in_array($status, $valid_statuses) &&
                    empty($this->record->shipping_receipt_number);
                })
                ->form([
                    TextInput::make('shipping_receipt_number')
                        ->label('Nomor Resi')
                        ->required(),
                ])
                ->action(function (array $data) {
                    app(SalesOrderService::class)->updateShippingReceipt(
                        SalesOrderData::fromModel($this->record),
                        data_get($data, 'shipping_receipt_number')
                    );
                }),
        ];
    }
}
