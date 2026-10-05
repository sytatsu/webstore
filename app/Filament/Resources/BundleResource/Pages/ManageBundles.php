<?php

namespace App\Filament\Resources\BundleResource\Pages;

use App\Filament\Resources\BundleResource;
use App\Models\Bundle;
use App\Services\BundleService;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;
use Illuminate\Database\Eloquent\Model;

class ManageBundles extends ManageRecords
{
    protected static string $resource = BundleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->using(function (array $data): Model {
                    $tiers = $data['tiers'] ?? [];
                    unset($data['tiers']);

                    $record = Bundle::create($data);
                    app(BundleService::class)->syncPurchasable($record, $tiers);

                    return $record;
                }),
        ];
    }
}
