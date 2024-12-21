<?php

namespace App\Filament\Widgets;

use App\Models\ProductType;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class LowStockAlert extends BaseWidget
{
    protected static ?int $sort = 3;
    protected int|string|array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                ProductType::query()
                    ->withCount(['stockItems' => function ($query) {
                        $query->where('status', 'in_warehouse');
                    }])
                    ->having('stock_items_count', '<', 5)
            )
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Product Type')
                    ->searchable(),
                Tables\Columns\TextColumn::make('stock_items_count')
                    ->label('Available Stock')
                    ->sortable(),
            ])
            ->actions([]);
    }
}
