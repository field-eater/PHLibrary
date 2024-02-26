<?php

namespace App\Filament\Resources\UserResource\Widgets;

use App\Filament\Resources\UserResource\Pages\ListUsers;
use Filament\Pages\Concerns\ExposesTableToWidgets;
use Filament\Widgets\Concerns\InteractsWithPageTable;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class UserStatsWidget extends BaseWidget
{

    use ExposesTableToWidgets;

    use InteractsWithPageTable;

    protected function getTablePage(): string
    {
        return ListUsers::class;
    }
    protected function getStats(): array
    {
        return [
            //
            Stat::make('Total Active Users', $this->getPageTableQuery()->active()->count()),
            Stat::make('Total Inactive Users', $this->getPageTableQuery()->inactive()->count()),
        ];
    }
}
