<?php

namespace App\Filament\Resources;

use App\Filament\Resources\StockItemResource\Pages;
use App\Filament\Resources\StockItemResource\RelationManagers;
use App\Models\StockItem;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class StockItemResource extends Resource
{
    protected static ?string $model = StockItem::class;

    protected static ?string $navigationIcon = 'heroicon-o-squares-2x2';


    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Grid::make(2)
                    ->schema([
                        // Left Column - Basic Information
                        Forms\Components\Section::make('Basic Information')
                            ->schema([
                                Forms\Components\Select::make('product_type_id')
                                    ->relationship('productType', 'name')
                                    ->required()
                                    ->searchable()
                                    ->preload()
                                    ->createOptionForm([
                                        Forms\Components\TextInput::make('name')
                                            ->required()
                                            ->maxLength(255),
                                        Forms\Components\TextInput::make('manufacturer')
                                            ->required()
                                            ->maxLength(255),
                                        Forms\Components\TextInput::make('model')
                                            ->required()
                                            ->maxLength(255),
                                    ]),

                                Forms\Components\TextInput::make('serial_number')
                                    ->required()
                                    ->unique(ignoreRecord: true)
                                    ->maxLength(255),

                                Forms\Components\Select::make('status')
                                    ->required()
                                    ->options([
                                        'in_warehouse' => 'In Warehouse',
                                        'on_downpayment' => 'On Downpayment',
                                        'on_lease' => 'On Lease',
                                        'sold' => 'Sold',
                                        'in_transit' => 'In Transit',
                                    ])
                                    ->default('in_warehouse')
                                    ->live(),

                                Forms\Components\TextInput::make('cost_price')
                                    ->numeric()
                                    ->prefix('$'),

                                Forms\Components\DatePicker::make('date_acquired')
                                    ->default(now()),
                            ]),

                        // Right Column - Location & Leasing
                        Forms\Components\Section::make('Location & Leasing Details')
                            ->schema([
                                Forms\Components\Select::make('warehouse_id')
                                    ->relationship('warehouse', 'name')
                                    ->searchable()
                                    ->preload(),

                                Forms\Components\TextInput::make('storage_location')
                                    ->maxLength(255)
                                    ->placeholder('e.g., Bay A, Shelf 3'),

                                Forms\Components\Select::make('business_id')
                                    ->relationship('business', 'company_name')
                                    ->searchable()
                                    ->preload()
                                    ->reactive()
                                    ->afterStateUpdated(fn(callable $set) => $set('location_id', null)),

                                Forms\Components\Select::make('location_id')
                                    ->label('Business Location')
                                    ->options(function (callable $get) {
                                        $businessId = $get('business_id');

                                        if (!$businessId) {
                                            return [];
                                        }

                                        return \App\Models\Location::query()
                                            ->where('business_id', $businessId)
                                            ->pluck('name', 'id')
                                            ->toArray();
                                    })
                                    ->disabled(fn(callable $get) => !$get('business_id'))
                                    ->required(fn(callable $get) => (bool) $get('business_id'))
                                    ->searchable()
                                    ->preload(),

                                Forms\Components\DatePicker::make('lease_start'),
                                Forms\Components\DatePicker::make('lease_end')
                            ]),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('serial_number')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('productType.name')
                    ->searchable()
                    ->sortable()
                    ->label('Product Type'),

                Tables\Columns\SelectColumn::make('status')
                    ->options([
                        'in_warehouse' => 'In Warehouse',
                        'on_downpayment' => 'On Downpayment',
                        'on_lease' => 'On Lease',
                        'sold' => 'Sold',
                        'in_transit' => 'In Transit',
                    ]),

                Tables\Columns\TextColumn::make('warehouse.name')
                    ->searchable()
                    ->sortable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('business.company_name')
                    ->searchable()
                    ->sortable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('location.name')
                    ->label('Business Location')
                    ->searchable()
                    ->toggleable(),


                Tables\Columns\TextColumn::make('cost_price')
                    ->money('USD')
                    ->sortable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('date_acquired')
                    ->date()
                    ->sortable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('lease_start')
                    ->date()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('lease_end')
                    ->date()
                    ->toggleable(),

            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'in_warehouse' => 'In Warehouse',
                        'on_downpayment' => 'On Downpayment',
                        'on_lease' => 'On Lease',
                        'sold' => 'Sold',
                        'in_transit' => 'In Transit',
                    ]),
                Tables\Filters\SelectFilter::make('warehouse')
                    ->relationship('warehouse', 'name'),
                // Tables\Filters\SelectFilter::make('product_type')
                //     ->relationship('productType', 'name'),
                Tables\Filters\SelectFilter::make('business')
                    ->relationship('business', 'company_name'),
                Tables\Filters\SelectFilter::make('manufacturer')
                    ->relationship('productType.manufacturer', 'name')
                    ->searchable()
                    ->preload()
                    ->label('Manufacturer'),

                Tables\Filters\SelectFilter::make('category')
                    ->relationship('productType.category', 'name')
                    ->searchable()
                    ->preload()
                    ->label('Category'),

                Tables\Filters\SelectFilter::make('sub_category')
                    ->relationship('productType.subCategory', 'name')
                    ->searchable()
                    ->preload()
                    ->label('Sub Category'),

            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\ViewAction::make(),
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\Action::make('duplicate')
                        ->label('Duplicate')
                        ->icon('heroicon-o-document-duplicate')
                        ->action(function (StockItem $record) {
                            $newRecord = $record->replicate();
                            $newRecord->serial_number .= ' (Copy)';
                            $newRecord->save();
                        }),
                    Tables\Actions\DeleteAction::make(),
                ]),

                // Tables\Actions\Action::make('export')
                //     ->label('Export')
                //     ->icon('heroicon-0-arrow-down-tray')
                //     ->url(fn(StockItem $record) => route('stock-items.export', $record))
                //     ->openUrlInNewTab(),

            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListStockItems::route('/'),
            'create' => Pages\CreateStockItem::route('/create'),
            'edit' => Pages\EditStockItem::route('/{record}/edit'),
        ];
    }
}
