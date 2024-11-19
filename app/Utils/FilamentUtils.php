<?php

namespace App\Utils;

use App\Models\User;
use Filament\Facades\Filament;

class FilamentUtils
{
    public static function setCurrentPanelByUserRole(User $user): void
    {
        $targetPanelId = $user->getPanelIdByRole();
        Filament::setCurrentPanel(Filament::getPanel($targetPanelId));
    }
}
