<?php

namespace App\Filament\Resources\RelationManagers;

use App\Filament\Resources\StockItemResource;
use App\Models\StockItem;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

abstract class AbstractStockItemsRelationManager extends RelationManager
{
    protected static string $relationship = 'stockItems';


    public function form(Form $form): Form
    {
        return $form
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

                Forms\Components\Section::make('Warehouse')
                    ->schema([
                        // Forms\Components\Select::make('warehouse_id')
                        //     ->relationship('warehouse', 'name')
                        //     ->searchable()
                        //     ->required()
                        //     ->preload(),

                        Forms\Components\TextInput::make('storage_location')
                            ->maxLength(255)
                            ->required()
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
                            // ->required(fn(callable $get) => (bool) $get('business_id'))
                            ->searchable()
                            ->preload(),
                    ]),
            ]);
    }
    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('serial_number')
            ->columns([
                Tables\Columns\TextColumn::make('serial_number')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('productType.name')
                    ->searchable()
                    ->sortable()
                    ->label('Product Type'),

                Tables\Columns\TextColumn::make('cost_price')
                    ->money('USD')
                    ->sortable(),

                Tables\Columns\TextColumn::make('productType.manufacturer.name')
                    ->searchable()
                    ->sortable()
                    ->label('Manufacturer'),

                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'in_warehouse' => 'info',
                        'on_downpayment' => 'warning',
                        'on_lease' => 'success',
                        'sold' => 'danger',
                        'in_transit' => 'gray',
                        default => 'gray',
                    }),

                Tables\Columns\TextColumn::make('warehouse.name')
                    ->label('Warehouse')
                    ->toggleable(),

                Tables\Columns\TextColumn::make('business.name')
                    ->label('Business')
                    ->toggleable(),

                Tables\Columns\TextColumn::make('location.name')
                    ->label('Business Location')
                    ->toggleable(),

                Tables\Columns\TextColumn::make('date_acquired')
                    ->date()
                    ->sortable(),
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
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\Action::make('edit')
                    ->label('Edit Details')
                    ->icon('heroicon-m-pencil-square')
                    ->url(
                        fn(StockItem $record): string =>
                        StockItemResource::getUrl('edit', ['record' => $record])
                    ),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
