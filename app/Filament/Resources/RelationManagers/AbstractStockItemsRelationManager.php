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
            ->schema([]);
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

                Tables\Columns\TextColumn::make('location.name')
                    ->label('Location')
                    ->toggleable(),

                Tables\Columns\TextColumn::make('cost_price')
                    ->money('USD')
                    ->sortable(),

                Tables\Columns\TextColumn::make('date_acquired')
                    ->date()
                    ->sortable(),

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
