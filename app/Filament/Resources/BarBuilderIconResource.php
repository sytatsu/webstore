<?php

namespace App\Filament\Resources;

use App\Filament\Clusters\BarBuilder;
use App\Filament\Resources\BarBuilderIconResource\Pages;
use App\Filament\Resources\Concerns\HasTranslatableName;
use App\Models\BarBuilderIcon;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Tables\Columns\ToggleColumn;
use Illuminate\Support\Str;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

class BarBuilderIconResource extends Resource
{
    use HasTranslatableName;

    protected static ?string $cluster = BarBuilder::class;

    protected static ?string $model = BarBuilderIcon::class;

    protected static ?string $navigationIcon = 'heroicon-o-sparkles';

    protected static ?string $navigationLabel = 'Icons';

    protected static ?string $modelLabel = 'icon';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Icon')
                    ->description('An SVG glyph customers can engrave on a cap instead of a letter.')
                    ->schema([
                        FileUpload::make('svg_upload')
                            ->label('Upload SVG')
                            ->helperText('Upload an .svg file to auto-fill the path data below. Only <path> elements are read; other shapes (circle, rect, etc.) are ignored.')
                            ->acceptedFileTypes(['image/svg+xml', '.svg'])
                            ->maxSize(512)
                            ->dehydrated(false)
                            ->live()
                            ->afterStateUpdated(function ($state, Forms\Set $set) {
                                if (! $state instanceof TemporaryUploadedFile) {
                                    return;
                                }

                                $parsed = static::parseUploadedSvg($state->get());

                                if (! $parsed) {
                                    Notification::make()
                                        ->title('Could not read that file')
                                        ->body('No <path> elements were found in the uploaded SVG.')
                                        ->danger()
                                        ->send();

                                    return;
                                }

                                $set('svg_paths', implode("\n", $parsed['paths']));
                                $set('cx', $parsed['cx']);
                                $set('cy', $parsed['cy']);
                                $set('scale', $parsed['scale']);
                            })
                            ->columnSpanFull(),
                        static::nameFormFieldset('e.g. Dog paw', 'e.g. Hondenpoot'),
                        Textarea::make('svg_paths')
                            ->label('SVG path data')
                            ->helperText('One SVG "d" path per line, authored on a 0-100 canvas. Multiple lines are layered into one icon.')
                            ->rows(4)
                            ->required()
                            ->live(onBlur: true)
                            ->afterStateHydrated(function (Textarea $component, $state) {
                                $component->state(is_array($state) ? implode("\n", $state) : $state);
                            })
                            ->dehydrateStateUsing(fn ($state) => array_values(array_filter(array_map('trim', explode("\n", (string) $state))))),
                        Forms\Components\Placeholder::make('svg_preview')
                            ->label('Preview')
                            ->content(fn (Forms\Get $get) => view('filament.bar-builder.icon-preview', [
                                'paths' => array_values(array_filter(array_map('trim', explode("\n", (string) $get('svg_paths'))))),
                                'cx' => (float) ($get('cx') ?: 50),
                                'cy' => (float) ($get('cy') ?: 50),
                                'scale' => (float) ($get('scale') ?: 1),
                            ])),
                        TextInput::make('cx')
                            ->label('Centre X')
                            ->numeric()
                            ->default(50)
                            ->required()
                            ->live(onBlur: true),
                        TextInput::make('cy')
                            ->label('Centre Y')
                            ->numeric()
                            ->default(50)
                            ->required()
                            ->live(onBlur: true),
                        TextInput::make('scale')
                            ->numeric()
                            ->step(0.01)
                            ->default(1)
                            ->required()
                            ->live(onBlur: true),
                        Toggle::make('enabled')
                            ->label('Available to customers')
                            ->default(true),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->reorderable('sort_order')
            ->defaultSort('sort_order')
            ->columns([
                static::nameTableColumn(),
                ToggleColumn::make('enabled'),
            ])
            ->actions([
                Tables\Actions\Action::make('download')
                    ->label('Download')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->color('gray')
                    ->action(fn (BarBuilderIcon $record) => response()->streamDownload(
                        fn () => print (static::iconToSvgMarkup($record)),
                        Str::slug($record->translate('name')).'.svg',
                        ['Content-Type' => 'image/svg+xml']
                    )),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageBarBuilderIcons::route('/'),
        ];
    }

    /**
     * @return array{paths: array<int, string>, cx: float, cy: float, scale: float}|null
     */
    private static function parseUploadedSvg(string $contents): ?array
    {
        $dom = new \DOMDocument();
        libxml_use_internal_errors(true);
        $loaded = $dom->loadXML($contents, LIBXML_NONET);
        libxml_clear_errors();

        if (! $loaded) {
            return null;
        }

        $xpath = new \DOMXPath($dom);
        $paths = [];

        foreach ($xpath->query('//*[local-name()="path"]') as $node) {
            $d = trim($node->getAttribute('d'));

            if ($d !== '') {
                $paths[] = $d;
            }
        }

        if (empty($paths)) {
            return null;
        }

        $width = 100.0;
        $height = 100.0;
        $svgNode = $xpath->query('//*[local-name()="svg"]')->item(0);

        if ($svgNode) {
            $viewBox = trim($svgNode->getAttribute('viewBox'));
            $parts = $viewBox !== '' ? preg_split('/[\s,]+/', $viewBox) : [];

            if (count($parts) === 4) {
                $width = (float) $parts[2];
                $height = (float) $parts[3];
            } elseif ($svgNode->getAttribute('width') && $svgNode->getAttribute('height')) {
                $width = (float) $svgNode->getAttribute('width');
                $height = (float) $svgNode->getAttribute('height');
            }
        }

        $width = $width ?: 100.0;
        $height = $height ?: 100.0;

        return [
            'paths' => $paths,
            'cx' => round($width / 2, 2),
            'cy' => round($height / 2, 2),
            'scale' => round(44 / max($width, $height), 2),
        ];
    }

    private static function iconToSvgMarkup(BarBuilderIcon $record): string
    {
        $d = e(implode(' ', $record->svg_paths ?? []));

        return <<<SVG
        <?xml version="1.0" encoding="UTF-8"?>
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100">
            <path d="{$d}" fill="#000000"/>
        </svg>
        SVG;
    }
}
