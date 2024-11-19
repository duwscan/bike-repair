<?php

namespace App\Models;

use Filament\Models\Contracts\FilamentUser;
use Filament\Models\Contracts\HasAvatar;
use Filament\Models\Contracts\HasTenants;
use Filament\Panel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Collection;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable implements FilamentUser, HasAvatar, HasTenants
{
    use HasApiTokens;
    use HasFactory;
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role_id',
        'service_point_id',
        'phone',
        'avatar_url',
        'theme',
        'theme_color',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }

    public function customerBikes(): HasMany
    {
        return $this->hasMany(CustomerBike::class, 'owner_id');
    }

    public function schedules(): HasMany
    {
        return $this->hasMany(Schedule::class, 'owner_id');
    }

    public function servicePoints(): BelongsToMany
    {
        return $this->belongsToMany(ServicePoint::class);
    }

    /**
     * Get the tenants associated with the user.
     */
    public function getTenants(Panel $panel): array|Collection
    {
        return $this->servicePoints;
    }

    /**
     * Determine if the user can access the given tenant.
     */
    public function canAccessTenant(Model $tenant): bool
    {
        return $this->servicePoints->contains($tenant);
    }

    /**
     * Determine if the user can access the given panel.
     * @throws \Exception
     */
    public function canAccessPanel(Panel $panel): bool
    {
        return $panel->getId() === $this->getPanelIdByRole();
    }

    public function getPanelIdByRole(): string
    {
        return match ($this->role->name) {
            'admin' => 'admin',
            'mechanic' => 'mechanic',
            default => 'null',
        };
    }



    public function getFilamentAvatarUrl(): string
    {
        $avatarUrl = $this->avatar_url;

        if (! empty($avatarUrl)) {
            return "/storage/$avatarUrl";
        } else {
            return '/storage/logo.png';
        }
    }
}
