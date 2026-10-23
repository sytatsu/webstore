<?php

namespace App\Filament\Extensions;

use Filament\Schemas\Schema;
use App\Services\InventoryService;
use Filament\Forms\Components\Select;
use Lunar\Admin\Support\Extending\EditPageExtension;

/**
 * Lunar hard-codes its three purchasability options on the inventory page;
 * this swaps in ours so 'in_stock_or_print_on_demand' can be picked per
 * product too, not only through the bulk inventory page. Also covers the
 * single-variant product inventory page, which renders this same form.
 */
class VariantInventoryFormExtension extends EditPageExtension
{
    public function extendForm(Schema $schema): Schema
    {
        foreach ($schema->getFlatComponents(withHidden: true) as $component) {
            if ($component instanceof Select && $component->getName() === 'purchasable') {
                $component->options(InventoryService::PURCHASABILITY_OPTIONS);
            }
        }

        return $schema;
    }
}
