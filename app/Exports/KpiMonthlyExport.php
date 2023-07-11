<?php

namespace App\Exports;

use App\Models\Kpi;
use App\Models\User;
use Carbon\Carbon;
use Carbon\CarbonImmutable;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class KpiMonthlyExport implements FromArray, WithHeadings, WithMapping
{
    protected String $year;
    protected $usersMonthlyKpiUsers;
    protected $usersMonthYear;
    protected $usersAverageYearlyKpi;

    function __construct(String $year) {
        $this->year = $year;
        $this->generateDataArrays();
    }

    public function array(): array 
    {
        // USERS YEARLY
        $result = [];
        foreach ($this->usersAverageYearlyKpi as $user => $monthlyKpi) {
            $row = ['name' => $user];
            foreach ($monthlyKpi as $kpi) {
                $row[] = $kpi;
            }
            $result[] = $row;
        }
        return $result;
    }

    public function headings(): array
    {   
        $headings = ['Name'];
        $headings = array_merge($headings, $this->usersMonthYear);

        return $headings;
    }

    public function map($row): array
    {
        $result = [];
        foreach ($row as $value) {
            $result[] = $value;
        }
        return $result;
    }

    private function generateDataArrays()
    {
        // USERS YEARLY
        $usersYearlyKpis = Kpi::with('kpi_detail', 'kpi_detail.kpi_description', 'kpi_type', 'kpi_category', 'user')
            ->where('kpi_type_id', 3);

        if (auth()->user()->role_id != 1) {
            $usersYearlyKpis = $usersYearlyKpis->whereHas('user', function ($q) {
                $q->where('divisi_id', auth()->user()->divisi_id);
            });
        } else {
            $usersYearlyKpis = $usersYearlyKpis->limit(15);
        }

        $usersYearlyKpis = $usersYearlyKpis->orderBy('date', 'DESC')->get();

        // Group the KPIs by year
        $groupedUsersKpisByYear = $usersYearlyKpis->groupBy(function ($kpi) {
            return CarbonImmutable::parse($kpi->date)->format('Y-m');
        });

        // Calculate the average KPI for each user and month
        $usersAverageKpiMonthlyByYear = [];

        for ($month = 1; $month <= 12; $month++) {
            $yearMonth = $this->year . '-' . sprintf("%02d", $month);
            $yearlyGroupedKpis = $groupedUsersKpisByYear[$yearMonth] ?? collect();
            $cumulativeScore = 0;

            foreach ($yearlyGroupedKpis as $kpi) {
                $kpiDetailWithValue = $kpi->kpi_detail->filter(function ($kpiDetail) {
                    return $kpiDetail->value_result !== null && $kpiDetail->value_result >= 0;
                });

                if ($kpiDetailWithValue->isNotEmpty()) {
                    $actualCount = $kpiDetailWithValue->sum('value_result');
                    $score = ($kpi->percentage / 100) * ($actualCount / $kpiDetailWithValue->count());

                    $cumulativeScore += $score;
                }
            }
            // Calculate the average KPI for the month
            $usersAverageKpiMonthly = $yearlyGroupedKpis->count() > 0 ? ($cumulativeScore * 100) : 0;

            // Store the average KPI in the array
            $usersAverageKpiMonthlyByYear[$yearMonth] = $usersAverageKpiMonthly;
        }
        // dd($usersAverageKpiMonthlyByYear);

        $this->usersAverageYearlyKpi = [];
        $this->usersMonthYear = [];

        for ($month = 1; $month <= 12; $month++) {
            $yearMonth = $this->year . '-' . sprintf("%02d", $month);
            $monthName = Carbon::parse($yearMonth)->format('M');
            $this->usersMonthYear[] = $monthName;
        }

        foreach ($usersAverageKpiMonthlyByYear as $yearMonth => $averageKpi) {
            $this->usersAverageYearlyKpi[] = array_merge(['name' => $yearMonth], [$averageKpi]);
        }
    }
}
