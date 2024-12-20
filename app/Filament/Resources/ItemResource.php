<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ItemResource\Pages;
use App\Filament\Resources\ItemResource\RelationManagers;
use App\Models\Item;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ItemResource extends Resource
{
    protected static ?string $model = Item::class;
    protected static ?string $navigationIcon = 'heroicon-o-cube';
    protected static ?string $navigationGroup = 'Products Management';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make()
                    ->schema([
                        Forms\Components\Select::make('product_type_id')
                            ->label('Product Type')
                            ->relationship('productType', 'name')
                            ->required()
                            ->searchable()
                            ->preload()
                            ->createOptionForm([
                                Forms\Components\TextInput::make('name')
                                    ->required(),
                                Forms\Components\TextInput::make('manufacturer')
                                    ->required(),
                                Forms\Components\TextInput::make('model_number')
                                    ->required(),
                            ]),

                        Forms\Components\TextInput::make('serial_number')
                            ->label('Serial Number')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(255),

                        Forms\Components\Select::make('status')
                            ->label('Status')
                            ->options([
                                'in_warehouse' => 'In warehouse',
                                'on_downpayment' => 'On downpayment',
                                'on_lease' => 'On lease',
                                'sold' => 'Sold',
                                'in_transit' => 'In transit',
                            ])
                            ->required()
                            ->native(false),

                        Forms\Components\Select::make('warehouse_id')
                            ->label('Warehouse')
                            ->relationship('warehouse', 'name')
                            ->searchable()
                            ->preload(),

                        Forms\Components\Select::make('business_location_id')
                            ->label('Business Location')
                            ->relationship('businessLocation', 'name')
                            ->searchable()
                            ->preload(),

                        Forms\Components\TextInput::make('storage_location')
                            ->label('Storage Location')
                            ->maxLength(255),

                        Forms\Components\DatePicker::make('purchase_date')
                            ->label('Purchase Date')
                            ->required(),

                        Forms\Components\TextInput::make('purchase_price')
                            ->label('Purchase Price')
                            ->numeric()
                            ->required()
                            ->prefix('USD'),

                        Forms\Components\DatePicker::make('lease_start_date')
                            ->label('Lease Start Date'),

                        Forms\Components\DatePicker::make('lease_end_date')
                            ->label('Lease End Date')
                            ->after('lease_start_date'),
                    ])->columns(2)
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('serial_number')
                    ->label('Serial Number')
                    ->searchable(),
                Tables\Columns\TextColumn::make('productType.name')
                    ->label('Product Type')
                    ->searchable(),
                Tables\Columns\SelectColumn::make('status')
                    ->label('Status')
                    ->options([
                        'in_warehouse' => 'In warehouse',
                        'on_downpayment' => 'On downpayment',
                        'on_lease' => 'On lease',
                        'sold' => 'Sold',
                        'in_transit' => 'In transit',
                    ]),
                Tables\Columns\TextColumn::make('warehouse.name')
                    ->label('Warehouse')
                    ->searchable(),
                Tables\Columns\TextColumn::make('businessLocation.name')
                    ->label('Business Location')
                    ->searchable(),
                Tables\Columns\TextColumn::make('purchase_date')
                    ->label('Purchase Date')
                    ->date(),
                Tables\Columns\TextColumn::make('purchase_price')
                    ->label('Purchase Price')
                    ->money('USD'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('Status')
                    ->options([
                        'in_warehouse' => 'In warehouse',
                        'on_downpayment' => 'On downpayment',
                        'on_lease' => 'On lease',
                        'sold' => 'Sold',
                        'in_transit' => 'In transit',
                    ]),
                Tables\Filters\SelectFilter::make('warehouse_id')
                    ->label('Warehouse')
                    ->relationship('warehouse', 'name'),
                Tables\Filters\SelectFilter::make('product_type_id')
                    ->label('Product Type')
                    ->relationship('productType', 'name'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
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
            'index' => Pages\ListItems::route('/'),
            'create' => Pages\CreateItem::route('/create'),
            'edit' => Pages\EditItem::route('/{record}/edit'),
        ];
    }
}
