<?php

namespace App\Filament\Mechanic\Resources\AppointmentResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Support\Colors\Color;
use Filament\Support\Enums\FontWeight;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class LogsRelationManager extends RelationManager
{
    protected static string $relationship = 'logs';

    public static function getRecordLabel(): ?string
    {
        return trans('filament-logger::filament-logger.resource.label.logs');
    }
    public static function getPluralRecordLabel(): ?string
    {
        return trans('filament-logger::filament-logger.resource.label.logs');
    }

    public static function getTitle(Model $ownerRecord, string $pageClass): string
    {
        return trans('filament-logger::filament-logger.resource.label.logs');
    }

    protected static ?string $icon = 'heroicon-o-computer-desktop';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\RichEditor::make('body')
                    ->label('')
                    ->required()
                    ->maxLength(255)
                    ->columnSpanFull(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('')
            ->columns([
                Tables\Columns\Layout\Split::make([
                    Tables\Columns\Layout\Stack::make([
                        Tables\Columns\TextColumn::make('created_at')
                            ->date('d-m-Y | h:i')
                            ->color(Color::Orange),
                        Tables\Columns\TextColumn::make('body')
                            ->formatStateUsing(fn ($state) => strip_tags($state))
                            // ->bulleted()
                            ->weight(FontWeight::SemiBold),
                    ])->space(1),
                ]),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                // Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                // Tables\Actions\ViewAction::make(),
                // Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                // Tables\Actions\BulkActionGroup::make([
                //     Tables\Actions\DeleteBulkAction::make(),
                // ]),
            ]);
    }
}
