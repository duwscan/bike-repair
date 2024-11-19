<?php

namespace App\Providers\Filament;

use App\Filament\Admin\Pages\Dashboard;
use App\Filament\Admin\Pages\Faq;
use App\Filament\Admin\Resources\ActivityResource;
use App\Filament\Admin\Resources\AppointmentResource;
use App\Filament\Admin\Resources\CustomerBikeResource;
use App\Filament\Admin\Resources\InventoryItemResource;
use App\Filament\Admin\Resources\LoanBikeResource;
use App\Filament\Admin\Resources\ScheduleResource;
use App\Filament\Admin\Resources\ServicePointResource;
use App\Filament\Admin\Resources\UserResource;
use App\Http\Middleware\CustomAuthenticateMiddleware;
use App\Http\Middleware\RedirectIfAuthenticated;
use App\Utils\CustomizeLogin;
use Filament\Admin\Widgets\UserAccountWidget;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Navigation\MenuItem;
use Filament\Navigation\NavigationBuilder;
use Filament\Navigation\NavigationGroup;
use Filament\Navigation\NavigationItem;
use Filament\Pages;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\SpatieLaravelTranslatablePlugin;
use Filament\Support\Colors\Color;
use Filament\View\PanelsRenderHook;
use Hasnayeen\Themes\ThemesPlugin;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\AuthenticateSession;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Illuminate\View\View;
use JibayMcs\FilamentTour\FilamentTourPlugin;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->brandLogo(asset('images/logo.svg'))
            ->favicon(asset('images/logo.svg'))
            ->login(CustomizeLogin::class)
            ->profile()
            ->databaseNotifications()
            ->globalSearchKeyBindings(['command+k', 'ctrl+k'])
            ->navigation(function (NavigationBuilder $builder): NavigationBuilder {

                return $builder->groups([
                    NavigationGroup::make('')
                        ->items([
                            NavigationItem::make('Dashboard')
                                ->icon('heroicon-o-home')
                                ->isActiveWhen(fn (): bool => request()->routeIs('filament.admin.pages.dashboard'))
                                ->url(fn (): string => Dashboard::getUrl()),
                        ]),
                    NavigationGroup::make(__('admin-panel.schedule-resource-group'))
                        ->items([
                            ...AppointmentResource::getNavigationItems(),
                            ...ScheduleResource::getNavigationItems(),
                        ]),
                    NavigationGroup::make(__('admin-panel.service-resource-group'))
                        ->items([
                            ...ServicePointResource::getNavigationItems(),
                            ...InventoryItemResource::getNavigationItems(),
                            ...CustomerBikeResource::getNavigationItems(),
                            ...LoanBikeResource::getNavigationItems(),
                        ]),
                    NavigationGroup::make(__('admin-panel.user-resource-group'))
                        ->items([
                            ...UserResource::getNavigationItems(),
                            ...ActivityResource::getNavigationItems(),
                        ]),
                ]);
            })
            // Needs livewire component and view
            ->renderHook(PanelsRenderHook::GLOBAL_SEARCH_AFTER, fn (): View => view('filament.hooks.test'))
            ->userMenuItems([
                'faq' => MenuItem::make()
                    ->label('FAQ')
                    ->icon('heroicon-o-question-mark-circle')
                    ->url(fn (): string => Faq::getUrl()),
            ])
            ->resources([
                config('filament-logger.activity_resource'),
            ])
            ->colors([
                'primary' => Color::Purple,
                'danger' => Color::Red,
                'gray' => Color::Zinc,
                'info' => Color::Blue,
                'success' => Color::Green,
                'warning' => Color::Yellow,
            ])
            ->discoverResources(in: app_path('Filament/Admin/Resources'), for: 'App\\Filament\\Admin\\Resources')
            ->discoverPages(in: app_path('Filament/Admin/Pages'), for: 'App\\Filament\\Admin\\Pages')
            ->pages([
                Pages\Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Admin/Widgets'), for: 'App\\Filament\\Admin\\Widgets')
            ->widgets([
                UserAccountWidget::class,
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
                \Hasnayeen\Themes\Http\Middleware\SetTheme::class,
                RedirectIfAuthenticated::class,
            ])
            ->authMiddleware([
                CustomAuthenticateMiddleware::class,
            ])
            ->plugins([
                SpatieLaravelTranslatablePlugin::make()
                    ->defaultLocales(['en', 'vi']),
                ThemesPlugin::make(),
            ]);
    }
}
