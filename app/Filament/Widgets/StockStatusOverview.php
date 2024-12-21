<?php

namespace App\Filament\Widgets;

use App\Models\StockItem;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StockStatusOverview extends BaseWidget
{

    protected static ?int $sort = 1;
    protected int|string|array $columnSpan = 'full';

    protected function getStats(): array
    {
        return [
            Stat::make('In Warehouse', StockItem::where('status', 'in_warehouse')->count())
                ->color('success')
                ->chart([/* có thể thêm data cho sparkline chart */]),
            Stat::make('On Downpayment', StockItem::where('status', 'on_downpayment')->count())
                ->color('warning'),
            Stat::make('On Lease', StockItem::where('status', 'on_lease')->count())
                ->color('info'),
            Stat::make('Sold', StockItem::where('status', 'sold')->count())
                ->color('success'),
            Stat::make('In Transit', StockItem::where('status', 'in_transit')->count())
                ->color('danger'),
        ];
    }
}
