<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BusinessResource\Pages;
use App\Filament\Resources\BusinessResource\RelationManagers;
use App\Filament\Resources\BusinessResource\RelationManagers\LocationsRelationManager;
use App\Filament\Resources\BusinessResource\RelationManagers\StockItemsRelationManager;
use App\Models\Business;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class BusinessResource extends Resource
{
    protected static ?string $model = Business::class;

    protected static ?string $navigationIcon = 'heroicon-o-building-office';
    protected static ?string $navigationGroup = 'General Management';
    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make()
                    ->schema([
                        Forms\Components\TextInput::make('company_name')
                            ->required()
                            ->placeholder('Company Name')
                            ->maxLength(255),

                        Forms\Components\TextInput::make('organization_number')
                            ->required()
                            ->numeric()
                            ->placeholder('123456-7890')
                            ->unique(ignoreRecord: true)
                            ->maxLength(255),

                        Forms\Components\TextInput::make('email')
                            ->email()
                            ->required()
                            ->placeholder('email@gmail')
                            ->maxLength(255),

                        Forms\Components\TextInput::make('phone')
                            ->tel()
                            ->required()
                            ->placeholder('123-456-7890')
                            ->maxLength(255),

                        Forms\Components\Select::make('business_type')
                            ->options([
                                'customer' => 'Customer',
                                'supplier' => 'Supplier',
                                'internal' => 'Internal Business Unit',
                            ])
                            ->default('customer')
                            ->required(),

                        Forms\Components\TextInput::make('address')
                            ->required()
                            ->placeholder('Address')
                            ->maxLength(255),

                        Forms\Components\Section::make('Business Locations')
                            ->schema([
                                Forms\Components\Repeater::make('locations')
                                    ->relationship()
                                    ->schema([
                                        Forms\Components\TextInput::make('name')
                                            ->required()
                                            ->label('Business Location Name')
                                            ->placeholder('Branch name or location identifier'),

                                        Forms\Components\Hidden::make('latitude'),
                                        Forms\Components\Hidden::make('longitude'),
                                        Forms\Components\TextInput::make('address')
                                            ->required()
                                            ->label('Address'),

                                        Forms\Components\View::make('forms.components.google-map')
                                            ->columnSpanFull(),
                                    ])
                                    ->columns(1)
                                    ->collapsible()
                                    ->collapsed(
                                        fn($livewire) => !($livewire instanceof Pages\CreateBusiness)
                                    )
                                    ->itemLabel(
                                        fn(array $state): ?string =>
                                        $state['name'] ?? null
                                    )
                                    ->addActionLabel('Add New Location')
                            ])
                    ])->columns(2)
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('company_name')
                    ->searchable(),

                Tables\Columns\TextColumn::make('organization_number')
                    ->searchable(),

                Tables\Columns\TextColumn::make('email')
                    ->searchable(),

                Tables\Columns\TextColumn::make('phone')
                    ->searchable(),

                Tables\Columns\SelectColumn::make('business_type')
                    ->options([
                        'customer' => 'Customer',
                        'supplier' => 'Supplier',
                        'internal' => 'Internal',
                    ]),

                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('business_type')
                    ->options([
                        'customer' => 'Customer',
                        'supplier' => 'Supplier',
                        'internal' => 'Internal Business Unit',
                    ])
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
            LocationsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListBusinesses::route('/'),
            'create' => Pages\CreateBusiness::route('/create'),
            'edit' => Pages\EditBusiness::route('/{record}/edit'),
        ];
    }
}
