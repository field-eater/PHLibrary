<?php

namespace App\Filament\Widgets;

use Carbon\Carbon;
use DateTimeZone;
use Filament\Widgets\Widget;

class DateTimeWidget extends Widget
{
    protected static bool $isLazy = false;
    public $currentTime;
    public $currentDay;
    public $currentDate;

    public function mount()
    {
        $this->currentTime = Carbon::now()->format('g:i A');
        $this->currentDate = Carbon::now()->format('M d, Y');
        $this->currentDay = Carbon::now()->englishDayOfWeek;
    }

    public function refreshTime()
    {
        $this->currentTime = Carbon::now()->format('g:i A');
    }

    public function refreshDay()
    {
        $this->currentDay = Carbon::now()->englishDayOfWeek;
    }

    public function refreshDate()
    {
        return  $this->currentDate = Carbon::now()->format('M d, Y');
    }


    protected static string $view = 'filament.widgets.date-time-widget';
}
