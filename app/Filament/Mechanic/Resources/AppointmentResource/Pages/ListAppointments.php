<?php

namespace App\Filament\Mechanic\Resources\AppointmentResource\Pages;

use App\Enums\AppointmentStatus;
use App\Filament\Mechanic\Resources\AppointmentResource;
use App\Models\Appointment;
use Filament\Actions;
use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;

class ListAppointments extends ListRecords
{
    protected static string $resource = AppointmentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    public function getTabs(): array
    {
        $tabs = [
            trans('filament.all_appointments') => Tab::make()->badge(Appointment::query()->count()),
        ];

        foreach (AppointmentStatus::cases() as $status) {
            $tabs[$status->getLabel()] = Tab::make()
                ->label($status->getLabel())
                ->badge(Appointment::query()->where('status', $status->value)->count())
                ->badgeColor($status->getColor())
                ->modifyQueryUsing(function (Builder $query) use ($status) {
                    $query->where('status', $status->value);
                });
        }

        return $tabs;
    }

    public function getDefaultActiveTab(): string|int|null
    {
        return trans('filament.all_appointments');
    }
}
