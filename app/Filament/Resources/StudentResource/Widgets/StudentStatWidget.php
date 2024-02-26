<?php

namespace App\Filament\Resources\StudentResource\Widgets;

use App\Filament\Resources\StudentResource\Pages\ListStudents;
use App\Models\Borrow;
use App\Models\Student;
use Filament\Pages\Concerns\ExposesTableToWidgets;
use Filament\Widgets\Concerns\InteractsWithPageTable;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StudentStatWidget extends BaseWidget
{
    use ExposesTableToWidgets;
    use InteractsWithPageTable;

    protected int | string | array $columnSpan = 'full';




    public Borrow $borrow;

    protected function getTablePage(): string
    {
        return ListStudents::class;
    }

    protected function getStats(): array
    {
        return [
            // TODO: Find a way to fix the grow the statcards to fit into a single row
            Stat::make('Total Students', $this->getPageTableQuery()->count()),
            Stat::make(
                'Total Borrowing Students',
                $this->borrow->distinct('student_id')->count()
            ),
        ];
    }

    public function mount()
    {
        $this->borrow = new Borrow();
    }
}
