<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ItemMovementResource\Pages;
use App\Filament\Resources\ItemMovementResource\RelationManagers;
use App\Models\BusinessLocation;
use App\Models\ItemMovement;
use App\Models\Warehouse;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ItemMovementResource extends Resource
{
    protected static ?string $model = ItemMovement::class;
    protected static ?string $navigationIcon = 'heroicon-o-arrow-path';
    protected static ?string $navigationGroup = 'Products Management';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('item_id')
                    ->label('Item')
                    ->relationship('item', 'serial_number')
                    ->required()
                    ->searchable()
                    ->preload(),

                Forms\Components\Select::make('from_location_type')
                    ->label('From Location Type')
                    ->options([
                        'warehouse' => 'Warehouse',
                        'business_location' => 'Business Location'
                    ])
                    ->required(),

                // Dynamic select based on from_location_type
                Forms\Components\Select::make('from_location_id')
                    ->label('From Location')
                    ->options(function (callable $get) {
                        $type = $get('from_location_type');
                        if ($type === 'warehouse') {
                            return Warehouse::pluck('name', 'id');
                        } elseif ($type === 'business_location') {
                            return BusinessLocation::pluck('name', 'id');
                        }
                        return [];
                    })
                    ->required(),

                Forms\Components\Select::make('to_location_type')
                    ->label('To Location Type')
                    ->options([
                        'warehouse' => 'Warehouse',
                        'business_location' => 'Business Location'
                    ])
                    ->required(),

                Forms\Components\Select::make('to_location_id')
                    ->label('To Location')
                    ->options(function (callable $get) {
                        $type = $get('to_location_type');
                        if ($type === 'warehouse') {
                            return Warehouse::pluck('name', 'id');
                        } elseif ($type === 'business_location') {
                            return BusinessLocation::pluck('name', 'id');
                        }
                        return [];
                    })
                    ->required(),

                Forms\Components\DatePicker::make('movement_date')
                    ->label('Movement Date')
                    ->required(),

                Forms\Components\Textarea::make('notes')
                    ->label('Notes')
                    ->maxLength(65535),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('item.serial_number')
                    ->label('Serial Number')
                    ->searchable(),
                Tables\Columns\TextColumn::make('from_location_type')
                    ->label('From')
                    ->formatStateUsing(
                        fn($state) =>
                        $state === 'warehouse' ? 'Warehouse' : 'Business Location'
                    ),
                Tables\Columns\TextColumn::make('to_location_type')
                    ->label('To')
                    ->formatStateUsing(
                        fn($state) =>
                        $state === 'warehouse' ? 'Warehouse' : 'Business Location'
                    ),
                Tables\Columns\TextColumn::make('movement_date')
                    ->label('Movement Date')
                    ->date(),
                Tables\Columns\TextColumn::make('notes')
                    ->label('Notes')
                    ->limit(50),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('item')
                    ->relationship('item', 'serial_number'),
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

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListItemMovements::route('/'),
            'create' => Pages\CreateItemMovement::route('/create'),
            'edit' => Pages\EditItemMovement::route('/{record}/edit'),
        ];
    }
}
