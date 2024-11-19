<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;

class SlotService
{
    public function availableFor(Builder $query, User $mechanic, int $dayOfTheWeek, int $servicePointId, Carbon $date): void
    {
        $query->whereHas('schedule', function (Builder $query) use ($mechanic, $dayOfTheWeek, $servicePointId) {
            $query
                ->where('service_point_id', $servicePointId)
                ->where('day_of_the_week', $dayOfTheWeek)
                ->whereBelongsTo($mechanic, 'owner');
        })
            ->whereDoesntHave('appointment', function (Builder $query) use ($date) {
                $query->whereDate('date', $date);
            });
    }
}
