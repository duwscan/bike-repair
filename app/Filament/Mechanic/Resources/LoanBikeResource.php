<?php

namespace App\Filament\Mechanic\Resources;

use App\Enums\BikeType;
use App\Enums\LoanBikeStatus;
use App\Filament\Mechanic\Resources\LoanBikeResource\Pages;
use App\Models\LoanBike;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\QueryException;

class LoanBikeResource extends Resource
{
    protected static ?string $model = LoanBike::class;

    protected static ?string $navigationIcon = 'icon-loan-bike';

    public static function getNavigationLabel(): string
    {
        return trans('filament.loan_bikes.label');
    }

    public static function getLabel(): ?string
    {
        return trans('filament.loan_bikes.label');
    }

    protected static ?string $pluralModelLabel = 'Leenmiddelen';

    public static function getPluralModelLabel(): string
    {
        return trans('filament.loan_bikes.plural_label');
    }

    protected static ?int $navigationSort = 4;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('status')
                    ->label(trans('filament.loan_bikes.status'))
                    ->required()
                    ->native(false)
                    ->options(LoanBikeStatus::class)
                    ->default('available'),
                Forms\Components\TextInput::make('identifier')
                    ->label(trans('filament.loan_bikes.identifier'))
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('brand')
                    ->label(trans('filament.brand'))
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('model')
                    ->required()
                    ->label(trans('filament.loan_bikes.model'))
                    ->maxLength(255),
                Forms\Components\Select::make('type')
                    ->label(trans('filament.customer_bikes.type'))
                    ->native(false)
                    ->options(BikeType::class)
                    ->required()
                    ->searchable()
                    ->label(trans('filament.customer_bikes.type')),
                Forms\Components\FileUpload::make('image')
                    ->label(trans('filament.image'))
                    ->image(),
                Forms\Components\TextInput::make('color')
                    ->label(trans('filament.color'))
                    ->required()
                    ->maxLength(255),
                Forms\Components\Textarea::make('specifications')
                    ->label(trans('filament.loan_bikes.specifications'))
                    ->maxLength(65535)
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->searchable(),
                Tables\Columns\TextColumn::make('identifier')
                    ->label(trans('No'))
                    ->searchable(),
                Tables\Columns\TextColumn::make('brand')
                    ->label(trans('filament.brand'))
                    ->searchable(),
                Tables\Columns\TextColumn::make('model')
                    ->label(trans('filament.loan_bikes.model'))
                    ->searchable(),
                Tables\Columns\TextColumn::make('type')
                    ->badge()
                    ->label(trans('filament.loan_bikes.type'))
                    ->color('primary')
                    ->searchable(),
                Tables\Columns\TextColumn::make('color')
                    ->label(trans('admin-import.color'))
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
            'index' => Pages\ListLoanBikes::route('/'),
            'create' => Pages\CreateLoanBike::route('/create'),
            'edit' => Pages\EditLoanBike::route('/{record}/edit'),
        ];
    }
}
