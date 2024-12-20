<?php

namespace App\Filament\Resources\BusinessResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ItemsRelationManager extends RelationManager
{
    protected static string $relationship = 'items';

    protected static ?string $recordTitleAttribute = 'serial_number';

    public function form(Form $form): Form
    {
        return $form
            ->schema([

                Forms\Components\Select::make('product_type_id')
                    ->relationship('productType', 'name')
                    ->required(),

                Forms\Components\TextInput::make('serial_number')
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->maxLength(255),

                Forms\Components\Select::make('status')
                    ->options([
                        'in_warehouse' => 'In Warehouse',
                        'on_downpayment' => 'On Downpayment',
                        'on_lease' => 'On Lease',
                        'sold' => 'Sold',
                        'in_transit' => 'In Transit'
                    ])
                    ->required(),

                Forms\Components\Select::make('warehouse_id')
                    ->relationship('warehouse', 'name')
                    ->nullable(),

                Forms\Components\TextInput::make('storage_location')
                    ->maxLength(255)
                    ->nullable(),

                Forms\Components\DatePicker::make('purchase_date')
                    ->required(),

                Forms\Components\TextInput::make('purchase_price')
                    ->numeric()
                    ->prefix('$')
                    ->required()
                    ->step('0.01'),

                Forms\Components\DatePicker::make('lease_start_date')
                    ->nullable(),

                Forms\Components\DatePicker::make('lease_end_date')
                    ->nullable()
                    ->after('lease_start_date'),

                Forms\Components\Select::make('business_location_id')
                    ->label('Location')
                    ->options(function (RelationManager $livewire) {
                        return $livewire->ownerRecord->locations()
                            ->pluck('name', 'id');
                    }),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('serial_number')
            ->columns([
                Tables\Columns\TextColumn::make('serial_number')
                    ->label('Serial Number')
                    ->searchable(),

                Tables\Columns\TextColumn::make('productType.name')
                    ->label('Product Type')
                    ->searchable(),

                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->colors([
                        'success' => 'in_warehouse',
                        'warning' => 'on_downpayment',
                        'primary' => 'on_lease',
                        'info' => 'sold',
                        'danger' => 'in_transit',
                    ]),

                Tables\Columns\TextColumn::make('businessLocation.name')
                    ->label('Location'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'in_warehouse' => 'In Warehouse',
                        'on_downpayment' => 'On Downpayment',
                        'on_lease' => 'On Lease',
                        'sold' => 'Sold',
                        'in_transit' => 'In Transit'
                    ]),
                Tables\Filters\SelectFilter::make('business_location_id')
                    ->label('Location')
                    ->options(function (RelationManager $livewire) {
                        return $livewire->ownerRecord->locations()
                            ->pluck('name', 'id');
                    }),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\ViewAction::make(),
                    Tables\Actions\DeleteAction::make(),
                ]),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
