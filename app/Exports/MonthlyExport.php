<?php

namespace App\Exports;

use App\Models\Monthly;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class MonthlyExport implements FromCollection, WithHeadings, WithMapping
{
    protected string $month;

    function __construct(string $month)
    {
        $this->month = $month;
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        return Monthly::with('user')
            ->where('date', $this->month)
            ->get()
            ->sortBy('user.nama_lengkap');
    }

    public function headings(): array
    {
        return [
            'nama',
            'month',
            'task',
            'tipe',
            'result_plan',
            'result_actual',
            'status',
        ];
    }

    public function map($row): array
    {
        return [
            $row->user->nama_lengkap,
            date('M y', $row->date / 1000),
            $row->task,
            $row->tipe,
            $row->value_plan ? number_format($row->value_plan, 0, ',', '.') : '-',
            $row->value_actual ? number_format($row->value_actual, 0, ',', '.') : '-',
            $row->value ? 'CLOSED' : 'OPEN',
        ];
    }
}
