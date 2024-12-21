<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BusinessResource\RelationManagers\StockItemsRelationManager;
use App\Filament\Resources\WarehouseResource\Pages;
use App\Filament\Resources\WarehouseResource\RelationManagers;
use App\Models\Warehouse;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class WarehouseResource extends Resource
{
    protected static ?string $model = Warehouse::class;

    protected static ?string $navigationIcon = 'heroicon-o-building-storefront';
    protected static ?string $navigationGroup = 'General Management';
    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Grid::make(2)
                    ->schema([
                        // Left Column
                        Forms\Components\Section::make('Warehouse Information')
                            ->schema([
                                Forms\Components\TextInput::make('name')
                                    ->required()
                                    ->maxLength(255)
                                    ->label('Warehouse Name')
                                    ->placeholder('Enter warehouse name'),

                                Forms\Components\Select::make('business_id')
                                    ->relationship('business', 'company_name')
                                    ->searchable()
                                    ->preload()
                                    ->label('Associated Business (Optional)')
                                    ->placeholder('Select a business'),




                            ])
                            ->columnSpan(1),

                        // Right Column
                        Forms\Components\Section::make('Location Details')
                            ->schema([
                                Forms\Components\Hidden::make('latitude'),
                                Forms\Components\Hidden::make('longitude'),

                                Forms\Components\TextInput::make('address')
                                    ->required()
                                    ->label('Address'),

                                Forms\Components\View::make('forms.components.google-map')
                                    ->columnSpanFull(),
                            ])
                            ->columnSpan(1),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('business.company_name')
                    ->searchable()
                    ->sortable()
                    ->label('Associated Business'),

                Tables\Columns\TextColumn::make('address')
                    ->searchable()
                    ->wrap()
                    ->limit(50),

                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('business_id')
                    ->relationship('business', 'company_name')
                    ->searchable()
                    ->preload()
                    ->label('Filter by Business')
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
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
            StockItemsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListWarehouses::route('/'),
            'create' => Pages\CreateWarehouse::route('/create'),
            'edit' => Pages\EditWarehouse::route('/{record}/edit'),
        ];
    }
}
