<?php

namespace App\Providers;

use App\Models\Borrow;
use App\Observers\BorrowObserver;
use Filament\Tables\Actions\EditAction;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
        EditAction::configureUsing(function (EditAction $action): void {
            $action->color('warning');
        });

    }
}
