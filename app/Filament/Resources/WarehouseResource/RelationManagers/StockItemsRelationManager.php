<?php

namespace App\Filament\Resources\WarehouseResource\RelationManagers;

use App\Filament\Resources\RelationManagers\AbstractStockItemsRelationManager;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;


class StockItemsRelationManager extends AbstractStockItemsRelationManager
{
    // No need to define form() and table() methods here
}
