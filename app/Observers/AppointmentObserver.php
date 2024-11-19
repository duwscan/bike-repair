<?php

namespace App\Observers;

use App\Models\Appointment;
use Filament\Notifications\Actions\Action;
use Filament\Notifications\Notification;

class AppointmentObserver
{
    /**
     * Handle the Appointment "created" event.
     */
    public function created(Appointment $appointment): void
    {
        $mechanic = $appointment->mechanic;
        $tenant = $appointment->service_point_id;

        Notification::make()
            ->title('New appointment!')
            ->body('from '.$appointment->customerBike->owner->name.' on '.$appointment->date->format('d-m-y'))
            ->icon('heroicon-o-document-text')
            ->iconColor('primary')
            ->actions([
                Action::make('View')
                    ->button()
                    ->url(route('filament.mechanic.resources.appointments.index', ['tenant' => $tenant]), shouldOpenInNewTab: true),
            ])
            ->sendToDatabase($mechanic);
    }
}
