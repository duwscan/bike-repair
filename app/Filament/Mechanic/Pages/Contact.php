<?php

namespace App\Filament\Mechanic\Pages;

use App\Models\ServicePoint;
use App\Models\User;
use Filament\Facades\Filament;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Infolists\Concerns\InteractsWithInfolists;
use Filament\Infolists\Contracts\HasInfolists;
use Filament\Pages\Page;
use Filament\Support\Enums\FontWeight;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;

class Contact extends Page implements HasForms, HasInfolists, HasTable
{
    use InteractsWithForms;
    use InteractsWithInfolists;
    use InteractsWithTable;

    protected $tenant;

    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static string $view = 'filament.mechanic.pages.contact';

    public function mount()
    {
        $this->tenant = Filament::getTenant();
    }

    public function getHeading(): string
    {
        return __('filament.contact_information_of', ['tenant' => $this->tenant ? $this->tenant->name : '']);
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(ServicePoint::query()->where('id', $this->tenant->id))
            ->columns([
                TextColumn::make('name')
                    ->label(__('filament.service_point_name'))
                    ->icon('icon-service-point')
                    ->iconColor('primary')
                    ->weight(FontWeight::Bold),
                TextColumn::make('address')
                    ->label(trans('filament.address'))
                    ->icon('heroicon-o-map-pin')
                    ->iconColor('primary')
                    // Multiple record values in table so they can share an icon
                    ->formatStateUsing(function (ServicePoint $record) {
                        $address = optional($record)->address;
                        $zip = optional($record)->zip;

                        return "{$address}, {$zip}";
                    })
                    ->weight(FontWeight::Bold),
                TextColumn::make('phone')
                    ->label(__('filament.phone'))
                    ->icon('heroicon-o-phone')
                    ->iconColor('primary')
                    ->weight(FontWeight::Bold),

            ])->paginated(false);
    }

    public static function shouldRegisterNavigation(): bool
    {
        return false;
    }
}
