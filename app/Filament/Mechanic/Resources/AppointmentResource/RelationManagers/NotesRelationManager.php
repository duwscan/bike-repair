<?php

namespace App\Filament\Mechanic\Resources\AppointmentResource\RelationManagers;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Support\Colors\Color;
use Filament\Support\Enums\FontWeight;
use Filament\Resources\RelationManagers\RelationManager;
use Illuminate\Database\Eloquent\Model;

class NotesRelationManager extends RelationManager
{
    protected static string $relationship = 'notes';

    public static function getRecordLabel(): ?string
    {
        return trans('notes.label');
    }
    public static function getPluralRecordLabel(): ?string
    {
        return trans('notes.plural_label');
    }
    public static function getTitle(Model $ownerRecord, string $pageClass): string
    {
        return trans('notes.title');
    }

    protected static ?string $icon = 'heroicon-o-pencil';

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
            ->recordTitleAttribute('Notitie')
            ->columns([
                Tables\Columns\Layout\Split::make([
                    Tables\Columns\Layout\Stack::make([
                        Tables\Columns\TextColumn::make('created_at')
                            ->date('d-m-Y | h:i')
                            ->color(Color::Orange),
                        Tables\Columns\TextColumn::make('body')
                            ->formatStateUsing(fn ($state) => strip_tags($state))
                            ->weight(FontWeight::SemiBold)
                            // ->bulleted()
                            ->limit(255)
                    ])->space(1),
                ])
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
