<?php

namespace App\Filament\Pages;

use Filament\Actions\BulkAction;
use Closure;
use App\Services\InventoryService;
use Filament\Actions\Action;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\SelectColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\TextInputColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Lunar\Admin\Support\Pages\BasePage;
use Lunar\Models\Brand;
use Lunar\Models\ProductVariant;

class BulkInventoryPage extends BasePage implements HasForms, HasTable
{
    use InteractsWithForms;
    use InteractsWithTable;

    protected static ?string $permission = 'catalog:manage-products';

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-archive-box';

    protected static ?string $navigationLabel = 'Inventory';

    protected static ?string $title = 'Inventory';

    protected static ?string $slug = 'inventory';

    protected static ?int $navigationSort = 2;

    protected string $view = 'filament.pages.bulk-inventory';

    public static function getNavigationGroup(): ?string
    {
        return __('lunarpanel::global.sections.catalog');
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(ProductVariant::query()->with(['product.brand', 'values']))
            ->columns([
                TextColumn::make('product_name')
                    ->label('Product')
                    ->state(fn (ProductVariant $record) => $record->product?->translateAttribute('name'))
                    ->description(fn (ProductVariant $record) => $record->getOption() ?: null)
                    ->searchable(query: fn (Builder $query, string $search) => $query->whereHas(
                        'product',
                        fn (Builder $product) => $product->where('attribute_data', 'like', "%{$search}%")
                    )),
                TextColumn::make('sku')
                    ->label('SKU')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('product.brand.name')
                    ->label('Brand')
                    ->placeholder('—'),
                TextColumn::make('product.status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (?string $state) => $state === 'published' ? 'success' : 'gray'),
                TextInputColumn::make('stock')
                    ->type('number')
                    ->rules(['required', 'integer', 'min:0'])
                    ->sortable(),
                TextInputColumn::make('backorder')
                    ->type('number')
                    ->rules(['required', 'integer', 'min:0'])
                    ->sortable(),
                SelectColumn::make('purchasable')
                    ->options(InventoryService::PURCHASABILITY_OPTIONS)
                    ->selectablePlaceholder(false),
            ])
            ->filters([
                SelectFilter::make('brand')
                    ->label('Brand')
                    ->multiple()
                    ->options(fn () => Brand::query()->orderBy('name')->pluck('name', 'id'))
                    ->query(fn (Builder $query, array $data) => $query->when(
                        filled($data['values'] ?? null),
                        fn (Builder $query) => $query->whereHas(
                            'product',
                            fn (Builder $product) => $product->whereIn('brand_id', $data['values'])
                        )
                    )),
                TernaryFilter::make('in_stock')
                    ->label('Stock')
                    ->placeholder('All')
                    ->trueLabel('In stock')
                    ->falseLabel('Out of stock')
                    ->queries(
                        true: fn (Builder $query) => $query->where('stock', '>', 0),
                        false: fn (Builder $query) => $query->where('stock', '<=', 0),
                    ),
            ])
            ->defaultSort('sku')
            ->toolbarActions([
                $this->adjustBulkAction('stock'),
                $this->adjustBulkAction('backorder'),
                BulkAction::make('setPurchasability')
                    ->label('Set purchasability')
                    ->icon('heroicon-o-shopping-cart')
                    ->form([
                        Select::make('purchasable')
                            ->options(InventoryService::PURCHASABILITY_OPTIONS)
                            ->required(),
                    ])
                    ->action(function (Collection $records, array $data) {
                        $updated = app(InventoryService::class)->setPurchasability($records, $data['purchasable']);

                        $this->notifyUpdated($updated, $records->count());
                    })
                    ->deselectRecordsAfterCompletion(),
            ])
            ->paginated([25, 50, 100, 'all'])
            ->defaultPaginationPageOption(50);
    }

    /**
     * Updates every variant of the chosen brand(s) at once, regardless of
     * what the table is currently filtered to or which rows are selected.
     */
    protected function getHeaderActions(): array
    {
        return [
            Action::make('updateByBrand')
                ->label('Update by brand')
                ->icon('heroicon-o-tag')
                ->schema([
                    Select::make('brands')
                        ->label('Brand(s)')
                        ->multiple()
                        ->options(fn () => Brand::query()->orderBy('name')->pluck('name', 'id'))
                        ->searchable()
                        ->required(),
                    Select::make('field')
                        ->label('Update')
                        ->options([
                            'stock' => 'Stock',
                            'backorder' => 'Backorder',
                            'purchasable' => 'Purchasability',
                        ])
                        ->default('stock')
                        ->required()
                        ->live(),
                    ...$this->adjustFormSchema(visible: fn ($get) => $get('field') !== 'purchasable'),
                    Select::make('purchasable')
                        ->label('Purchasability')
                        ->options(InventoryService::PURCHASABILITY_OPTIONS)
                        ->visible(fn ($get) => $get('field') === 'purchasable')
                        ->required(),
                ])
                ->modalDescription('This changes every variant of every product of the selected brand(s), including draft products.')
                ->modalSubmitActionLabel('Update')
                ->action(function (array $data) {
                    $service = app(InventoryService::class);
                    $variants = $service->variantsForBrands($data['brands']);
                    $total = (clone $variants)->count();

                    $updated = $data['field'] === 'purchasable'
                        ? $service->setPurchasability($variants, $data['purchasable'])
                        : $service->adjust($variants, $data['field'], $data['mode'], (int) $data['quantity']);

                    $this->notifyUpdated($updated, $total);
                }),
        ];
    }

    protected function adjustBulkAction(string $field): BulkAction
    {
        $label = ucfirst($field);

        return BulkAction::make("adjust{$label}")
            ->label("Update {$field}")
            ->icon($field === 'stock' ? 'heroicon-o-archive-box' : 'heroicon-o-clock')
            ->form($this->adjustFormSchema())
            ->action(function (Collection $records, array $data) use ($field) {
                $updated = app(InventoryService::class)->adjust($records, $field, $data['mode'], (int) $data['quantity']);

                $this->notifyUpdated($updated, $records->count());
            })
            ->deselectRecordsAfterCompletion();
    }

    protected function adjustFormSchema(?Closure $visible = null): array
    {
        $visible ??= fn () => true;

        return [
            Radio::make('mode')
                ->label('How')
                ->options([
                    InventoryService::MODE_SET => 'Set to',
                    InventoryService::MODE_ADD => 'Increase by',
                    InventoryService::MODE_SUBTRACT => 'Decrease by (never below 0)',
                ])
                ->default(InventoryService::MODE_SET)
                ->inline()
                ->inlineLabel(false)
                ->visible($visible)
                ->required(),
            TextInput::make('quantity')
                ->label('Quantity')
                ->integer()
                ->minValue(0)
                ->default(0)
                ->visible($visible)
                ->required(),
        ];
    }

    protected function notifyUpdated(int $updated, int $total): void
    {
        Notification::make()
            ->title("Updated {$updated} of {$total} variant(s)")
            ->body($updated < $total ? 'The others already had that value.' : null)
            ->success()
            ->send();
    }
}
