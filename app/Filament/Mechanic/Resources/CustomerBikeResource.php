<?php

namespace App\Filament\Mechanic\Resources;

use App\Enums\BikeType;
use App\Filament\Mechanic\Resources\CustomerBikeResource\Pages;
use App\Models\CustomerBike;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Storage;

class CustomerBikeResource extends Resource
{
    protected static ?string $model = CustomerBike::class;

    protected static ?string $navigationIcon = 'icon-bike';

    public static function getNavigationLabel(): string
    {
        return __('filament.customer_bikes.label');
    }

    public static function getLabel(): string
    {
        return __('filament.customer_bikes.label');
    }

    public static function getPluralModelLabel(): string
    {
        return __('filament.customer_bikes.plural_label');
    }

    protected static ?string $tenantOwnershipRelationshipName = 'servicePoints';

    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make([
                    Forms\Components\Select::make('owner_id')
                        ->relationship('owner', 'name')
                        ->native(false)
                        ->searchable()
                        ->preload()
                        ->label(trans('filament.owner')),
                    Forms\Components\TextInput::make('identifier')
                        ->required()
                        ->maxLength(255)
                        ->label(trans('filament.customerBike.identifier')),
                    Forms\Components\TextInput::make('brand')
                        ->required()
                        ->maxLength(255)
                        ->label(trans('filament.brand')),
                    Forms\Components\TextInput::make('model')
                        ->required()
                        ->maxLength(255)
                        ->label(trans('filament.model')),
                    Forms\Components\Select::make('type')
                        ->native(false)
                        ->label(trans('filament.customer_bikes.type'))
                        ->options(BikeType::class)
                        ->required()
                        ->searchable(),
                    Forms\Components\TextInput::make('color')
                        ->required()
                        ->maxLength(255)
                        ->label(trans('filament.color')),
                    Forms\Components\FileUpload::make('image')
                        ->image()
                        ->directory('asset-images')
                        ->imageEditor()
                        ->label(trans('filament.customer_bikes.image'))
                        ->columnSpanFull(),
                    Forms\Components\Textarea::make('specifications')
                        ->label(trans('filament.loan_bikes.specifications'))
                        ->maxLength(65535)
                        ->columnSpanFull(),
                ])
                    ->icon('icon-bike')
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('image')
                    ->square()
                    ->label(trans('filament.customer_bikes.image'))
                    ->defaultImageUrl(url('/images/logo.png')),
                Tables\Columns\TextColumn::make('servicePoints.name')
                    ->label(trans('admin-import.servicePoint'))
                    ->badge()
                    ->color('undefined'),
                Tables\Columns\TextColumn::make('owner.name')
                    ->label(trans('filament.owner'))
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('identifier')
                    ->label('ID')
                    ->searchable(),
                Tables\Columns\TextColumn::make('brand')
                    ->label(trans('filament.brand'))
                    ->searchable(),
                Tables\Columns\TextColumn::make('model')
                    ->label(trans('filament.model'))
                    ->searchable(),
                Tables\Columns\TextColumn::make('type')
                    ->label(trans('filament.customer_bikes.type'))
                    ->badge()
                    ->searchable(),
                Tables\Columns\TextColumn::make('color')
                    ->label(trans('filament.color'))
                    ->searchable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make()
                    ->before(function (CustomerBike $record) {
                        // Deleting the image from the server when Vehicle $record gets deleted.
                        Storage::delete('public/asset-images/'.$record->image);
                    }),
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

    public static function getNavigationBadge(): ?string
    {
        try {
            return (string) static::getModel()::count();
        } catch (QueryException $e) {
            return '0';
        }
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCustomerBikes::route('/'),
            'create' => Pages\CreateCustomerBike::route('/create'),
            'edit' => Pages\EditCustomerBike::route('/{record}/edit'),
        ];
    }
}
