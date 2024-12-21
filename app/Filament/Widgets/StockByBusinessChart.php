<?php

namespace App\Filament\Widgets;

use App\Models\Business;
use Filament\Widgets\ChartWidget;

class StockByBusinessChart extends ChartWidget
{
    protected static ?string $heading = 'Stock by Business';
    protected static ?int $sort = 2;
    protected int|string|array $columnSpan = 6;

    protected static ?string $maxHeight = '300px';

    protected function getData(): array
    {
        $data = Business::withCount('stockItems')
            ->whereHas('stockItems')
            ->get()
            ->sortByDesc('stock_items_count');

        return [
            'datasets' => [
                [
                    'label' => 'Assigned Items',
                    'data' => $data->pluck('stock_items_count')->toArray(),
                    'backgroundColor' => '#FF6384',
                ],
            ],
            'labels' => $data->pluck('company_name')->toArray(),
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }
}
