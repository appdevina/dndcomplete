<?php

namespace App\Http\Controllers;

use App\Models\Kpi;
use App\Models\KpiDetail;
use App\Models\KpiType;
use Exception;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Carbon\CarbonImmutable;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx\Rels;

class KpiDashboardController extends Controller
{
    public function indexDaily(Request $request)
    {
        $currentMonth = Carbon::now()->month;
        $currentYear = Carbon::now()->year;

        $kpis = Kpi::with('kpi_detail', 'kpi_detail.kpi_description', 'kpi_type', 'kpi_category', 'user')
        ->where('user_id', auth()->user()->id)
        ->where('kpi_type_id', 1)
        ->whereMonth('date', $currentMonth)
        ->whereYear('date', $currentYear)
        ->orderBy('date', 'DESC')
        ->get();

        if ($request->month) {
            $date = Carbon::createFromFormat('m/Y', $request->month);

            $kpis = Kpi::with('kpi_detail', 'kpi_detail.kpi_description', 'kpi_type', 'kpi_category', 'user')
            ->where('user_id', auth()->user()->id)
            ->where('kpi_type_id', 1)
            ->whereMonth('date', $date->month)
            ->whereYear('date', $date->year)
            ->orderBy('date', 'DESC')
            ->get();
        }

        // Group the KPIs by date
        $groupedKpisByDate = $kpis->groupBy('date');

        // Group the KPIs by KPI category within each date group
        $groupedKpisByDateAndCategory = [];

        foreach ($groupedKpisByDate as $date => $groupedKpi) {
            $groupedKpiByCategory = $groupedKpi->groupBy('kpi_category.name');
            $groupedKpisByDateAndCategory[$date] = $groupedKpiByCategory;
        }

        return view('kpi.kpi_dashboard.index_daily', [
            'title' => 'KPI Dashboard',
            'active' => 'kpi-dashboard',
            'groupedKpis' => $groupedKpisByDateAndCategory,
        ]);
    }

    public function indexWeekly(Request $request)
    {
        $currentMonth = Carbon::now()->month;
        $currentYear = Carbon::now()->year;

        $kpis = Kpi::with('kpi_detail', 'kpi_detail.kpi_description', 'kpi_type', 'kpi_category', 'user')
        ->where('user_id', auth()->user()->id)
        ->where('kpi_type_id', 2)
        ->whereMonth('date', $currentMonth)
        ->whereYear('date', $currentYear)
        ->orderBy('date', 'DESC')
        ->get();

        if ($request->month) {
            $date = Carbon::createFromFormat('m/Y', $request->month);

            $kpis = Kpi::with('kpi_detail', 'kpi_detail.kpi_description', 'kpi_type', 'kpi_category', 'user')
            ->where('user_id', auth()->user()->id)
            ->where('kpi_type_id', 2)
            ->whereMonth('date', $date->month)
            ->whereYear('date', $date->year)
            ->orderBy('date', 'DESC')
            ->get();
        }

        // Group the KPIs by yearly week
        $groupedKpisByWeek = $kpis->groupBy(function ($kpi) {
            // Parse the date and get the week number
            return CarbonImmutable::parse($kpi->date)->format('W');
        });

        // Group the KPIs by KPI category within each week group
        $groupedKpisByWeekAndCategory = [];

        foreach ($groupedKpisByWeek as $week => $groupedKpi) {
            $groupedKpiByCategory = $groupedKpi->groupBy('kpi_category.name');
            $groupedKpisByWeekAndCategory[$week] = $groupedKpiByCategory;
        }

        return view('kpi.kpi_dashboard.index_weekly', [
            'title' => 'KPI Dashboard',
            'active' => 'kpi-dashboard',
            'groupedKpis' => $groupedKpisByWeekAndCategory,
        ]);
    }

    public function indexMonthly(Request $request)
    {
        $currentMonth = Carbon::now()->month;
        $currentYear = Carbon::now()->year;

        $kpis = Kpi::with('kpi_detail', 'kpi_detail.kpi_description', 'kpi_type', 'kpi_category', 'user')
            ->where('user_id', auth()->user()->id)
            ->where('kpi_type_id', 3)
            ->whereMonth('date', $currentMonth)
            ->whereYear('date', $currentYear)
            ->orderBy('date', 'DESC')
            ->get();

        if ($request->month) {
            $date = Carbon::createFromFormat('m/Y', $request->month);

            $kpis = Kpi::with('kpi_detail', 'kpi_detail.kpi_description', 'kpi_type', 'kpi_category', 'user')
            ->where('user_id', auth()->user()->id)
            ->where('kpi_type_id', 3)
            ->whereMonth('date', $date->month)
            ->whereYear('date', $date->year)
            ->orderBy('date', 'DESC')
            ->get();
        }

        // Group the KPIs by yearly month
        $groupedKpisByYear = $kpis->groupBy(function ($kpi) {
            // Parse the date and get the year and month
            return CarbonImmutable::parse($kpi->date)->format('Y-m');
        });

        // Group the KPIs by KPI category within each month group
        $groupedKpisByYearAndCategory = [];

        foreach ($groupedKpisByYear as $yearMonth => $groupedKpi) {
            $groupedKpiByCategory = $groupedKpi->groupBy('kpi_category.name');
            $groupedKpisByYearAndCategory[$yearMonth] = $groupedKpiByCategory;
        }

        return view('kpi.kpi_dashboard.index_monthly', [
            'title' => 'KPI Dashboard',
            'active' => 'kpi-dashboard',
            'groupedKpis' => $groupedKpisByYearAndCategory,
        ]);
    }

    public function changeStatus(Request $request) {
        try {
            $detailKpi = KpiDetail::findOrFail($request->id);

            $detailKpi->value_result = ($detailKpi->value_result == 0 || $detailKpi->value_result == null) ? 1 : 0;
            $detailKpi->save();

            switch ($request->type) {
                case 'daily':
                    return redirect('dash-daily')->with(['success' => 'Successfully Updated !']);
                    break;
                case 'weekly':
                    return redirect('dash-weekly')->with(['success' => 'Successfully Updated !']);
                    break;
                default:
                    return redirect('dash-monthly')->with(['success' => 'Successfully Updated !']);
                    break;
            }

        } catch (Exception $e) {
            switch ($request->type) {
                case 'daily':
                    return redirect('dash-daily')->with(['error' => $e->getMessage()]);
                    break;
                case 'weekly':
                    return redirect('dash-weekly')->with(['error' => $e->getMessage()]);
                    break;
                default:
                    return redirect('dash-monthly')->with(['error' => $e->getMessage()]);
                    break;
            }
        }
    }
    
}
