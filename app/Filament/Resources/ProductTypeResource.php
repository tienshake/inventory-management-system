<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductTypeResource\Pages;
use App\Filament\Resources\ProductTypeResource\RelationManagers;
use App\Models\ProductType;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ProductTypeResource extends Resource
{
    protected static ?string $model = ProductType::class;
    protected static ?string $navigationIcon = 'heroicon-o-square-3-stack-3d';
    protected static ?string $navigationGroup = 'Products Management';
    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make()
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Product Type Name')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('manufacturer')
                            ->label('Manufacturer')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('model_number')
                            ->label('Model Number')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\Select::make('category')
                            ->label('Category')
                            ->options([
                                'electronics' => 'electronics',
                                'furniture' => 'furniture',
                                'appliances' => 'appliances',
                                'others' => 'others',
                            ])
                            ->required(),
                        Forms\Components\TextInput::make('subcategory')
                            ->label('Subcategory')
                            ->maxLength(255),
                    ])->columns(2)
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Product Type Name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('manufacturer')
                    ->label('Manufacturer')
                    ->searchable(),
                Tables\Columns\TextColumn::make('model_number')
                    ->label('Model Number')
                    ->searchable(),
                Tables\Columns\TextColumn::make('category')
                    ->label('Category')
                    ->badge(),
                Tables\Columns\TextColumn::make('subcategory')
                    ->label('Subcategory'),
                Tables\Columns\TextColumn::make('items_count')
                    ->label('Items Count')
                    ->counts('items'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('category')
                    ->label('Category')
                    ->options([
                        'electronics' => 'Electronics',
                        'furniture' => 'Furniture',
                        'appliances' => 'Appliances',
                        'others' => 'Others',
                    ]),
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
            'index' => Pages\ListProductTypes::route('/'),
            'create' => Pages\CreateProductType::route('/create'),
            'edit' => Pages\EditProductType::route('/{record}/edit'),
        ];
    }
}
