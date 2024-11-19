<?php

namespace App\Providers\Filament;

use App\Filament\Mechanic\Pages\Contact;
use App\Filament\Mechanic\Pages\Faq;
use App\Filament\Mechanic\Resources\AppointmentResource;
use App\Filament\Mechanic\Resources\CustomerBikeResource;
use App\Filament\Mechanic\Resources\LoanBikeResource;
use App\Filament\Mechanic\Resources\ScheduleResource;
use App\Http\Middleware\ApplyTenantScopes;
use App\Http\Middleware\AssignGlobalScopes;
use App\Http\Middleware\CustomAuthenticateMiddleware;
use App\Http\Middleware\RedirectIfAuthenticated;
use App\Models\ServicePoint;
use Filament\Facades\Filament;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Navigation\MenuItem;
use Filament\Navigation\NavigationBuilder;
use Filament\Navigation\NavigationGroup;
use Filament\Navigation\NavigationItem;
use Filament\Pages;
use Filament\Pages\Dashboard;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\SpatieLaravelTranslatablePlugin;
use Filament\Support\Colors\Color;
use Filament\Widgets;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\AuthenticateSession;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class MechanicPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->id('mechanic')
            ->path('mechanic')
            ->tenant(ServicePoint::class)
            ->brandLogo(asset('images/logo.svg'))
            ->favicon(asset('images/logo.svg'))
            ->profile()
            ->passwordReset()
            ->databaseNotifications()
            // Custom navigation order, render Pages within a NavigationGroup::make('') to render a Filament Page..
            ->navigation(function (NavigationBuilder $builder): NavigationBuilder {

                return $builder->groups([
                    NavigationGroup::make('') // <- This makes the navigation item not have a group.
                        ->items([
                            NavigationItem::make('Dashboard')
                                ->icon('heroicon-o-home')
                                ->isActiveWhen(fn (): bool => request()->routeIs('filament.mechanic.pages.dashboard'))
                                ->url(fn (): string => Dashboard::getUrl()),
                        ]),
                    NavigationGroup::make('Planning')
                        ->items([
                            ...AppointmentResource::getNavigationItems(),
                            ...ScheduleResource::getNavigationItems(),
                        ]),
                    NavigationGroup::make('Servicebeheer')
                        ->items([
                            ...CustomerBikeResource::getNavigationItems(),
                            ...LoanBikeResource::getNavigationItems(),
                        ]),
                ]);
            })
            // Top right menu pages.
            ->userMenuItems([
                'contact' => MenuItem::make()
                    ->label('Contactgegevens')
                    ->icon('heroicon-o-phone')
                    ->url(fn (): string => Contact::getUrl(
                        [
                            'tenant' => Filament::getTenant(),
                            'contact',
                        ]
                    )),
                'faq' => MenuItem::make()
                    ->label('FAQ')
                    ->icon('heroicon-o-question-mark-circle')
                    ->url(fn (): string => Faq::getUrl(
                        [
                            'tenant' => Filament::getTenant(),
                            'faq',
                        ]
                    )),
            ])
            ->colors([
                'primary' => Color::Orange,
                'danger' => Color::Red,
                'gray' => Color::Zinc,
                'info' => Color::Blue,
                'success' => Color::Green,
                'warning' => Color::Yellow,
            ])
            ->discoverResources(in: app_path('Filament/Mechanic/Resources'), for: 'App\\Filament\\Mechanic\\Resources')
            ->discoverPages(in: app_path('Filament/Mechanic/Pages'), for: 'App\\Filament\\Mechanic\\Pages')
            ->pages([
                Pages\Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Mechanic/Widgets'), for: 'App\\Filament\\Mechanic\\Widgets')
            ->widgets([
                Widgets\StatsOverviewWidget::class,
            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
                RedirectIfAuthenticated::class,
            ])
            ->tenantMiddleware([
                ApplyTenantScopes::class,
                AssignGlobalScopes::class,
            ], isPersistent: true)
            ->authMiddleware([
                CustomAuthenticateMiddleware::class,
            ])
            ->plugins([
                SpatieLaravelTranslatablePlugin::make()
                    ->defaultLocales(['en', 'vi']),
            ]);
    }
}
