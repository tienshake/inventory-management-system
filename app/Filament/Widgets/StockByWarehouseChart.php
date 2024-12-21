<?php

namespace App\Filament\Widgets;

use App\Models\Warehouse;
use Filament\Widgets\ChartWidget;

class StockByWarehouseChart extends ChartWidget
{
    protected static ?string $heading = 'Stock by Warehouse';
    protected static ?int $sort = 2;
    protected int|string|array $columnSpan = 6;


    protected static ?string $maxHeight = '300px';

    protected function getData(): array
    {
        $data = Warehouse::withCount('stockItems')
            ->get()
            ->sortByDesc('stock_items_count');

        return [
            'datasets' => [
                [
                    'label' => 'Items in Stock',
                    'data' => $data->pluck('stock_items_count')->toArray(),
                    'backgroundColor' => '#36A2EB',
                ],
            ],
            'labels' => $data->pluck('name')->toArray(),
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}
