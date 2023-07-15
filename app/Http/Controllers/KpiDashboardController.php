<?php

namespace App\Http\Controllers;

use App\Models\Divisi;
use App\Models\Kpi;
use App\Models\KpiDetail;
use App\Models\KpiType;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Scope;
use Matrix\Operators\Division;
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

            $kpisQuery = Kpi::with('kpi_detail', 'kpi_detail.kpi_description', 'kpi_type', 'kpi_category', 'user')
            ->where('kpi_type_id', 1)
            ->whereMonth('date', $date->month)
            ->whereYear('date', $date->year);

            $user_id = $request->input('user_id');

            if ($user_id) {
                $kpisQuery->where('user_id', $user_id);
            } else {
                $kpisQuery->where('user_id', auth()->user()->id);
            }

            $kpis = $kpisQuery->orderBy('date', 'DESC')
            ->get();
        }

        // Group the KPIs by date
        $groupedKpisByDate = $kpis->groupBy('date');

        // Group the KPIs by KPI category within each date group
        $groupedKpisByDateAndCategory = [];
        $totalScore = 0;

        foreach ($groupedKpisByDate as $date => $groupedKpi) {
            $groupedKpiByCategory = $groupedKpi->groupBy('kpi_category.name');
            $groupedKpisByDateAndCategory[$date] = $groupedKpiByCategory;

            foreach ($groupedKpiByCategory as $categoryName => $kpis) {
                foreach ($kpis as $kpi) {
                    // Now, you can safely access the KPI details and perform calculations.
                    $kpiDetailWithValue = $kpi->kpi_detail->filter(function ($kpiDetail) {
                        return $kpiDetail->value_result !== null && $kpiDetail->value_result >= 0;
                    });

                    $actualCount = $kpiDetailWithValue->sum('value_result');
                    $score = ($kpi->percentage / 100) * ($actualCount / $kpi->kpi_detail->count());

                    // Add the calculated values to the KPI object
                    $kpi->actualCount = $actualCount;
                    $kpi->score = $score;
                    $totalScore += $score;
                }
            }
        }

        return view('kpi.kpi_dashboard.index_daily', [
            'title' => 'KPI Dashboard',
            'active' => 'kpi-dashboard',
            'groupedKpis' => $groupedKpisByDateAndCategory,
            'totalScore' => $totalScore,
            'users' => $this->getUser(),
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

            $kpisQuery = Kpi::with('kpi_detail', 'kpi_detail.kpi_description', 'kpi_type', 'kpi_category', 'user')
            ->where('kpi_type_id', 2)
            ->whereMonth('date', $date->month)
            ->whereYear('date', $date->year);

            $user_id = $request->input('user_id');

            if ($user_id) {
                $kpisQuery->where('user_id', $user_id);
            } else {
                $kpisQuery->where('user_id', auth()->user()->id);
            }

            $kpis = $kpisQuery->orderBy('date', 'DESC')
            ->get();
        }

        // Group the KPIs by yearly week
        $groupedKpisByWeek = $kpis->groupBy(function ($kpi) {
            // Parse the date and get the week number
            return CarbonImmutable::parse($kpi->date)->format('W');
        });

        // Group the KPIs by KPI category within each week group
        $groupedKpisByWeekAndCategory = [];
        $totalScore = 0;

        foreach ($groupedKpisByWeek as $week => $groupedKpi) {
            $groupedKpiByCategory = $groupedKpi->groupBy('kpi_category.name');
            $groupedKpisByWeekAndCategory[$week] = $groupedKpiByCategory;

            foreach ($groupedKpiByCategory as $categoryName => $kpis) {
                foreach ($kpis as $kpi) {
                    // Now, you can safely access the KPI details and perform calculations.
                    $kpiDetailWithValue = $kpi->kpi_detail->filter(function ($kpiDetail) {
                        return $kpiDetail->value_result !== null && $kpiDetail->value_result >= 0;
                    });

                    $actualCount = $kpiDetailWithValue->sum('value_result');
                    $score = ($kpi->percentage / 100) * ($actualCount / $kpi->kpi_detail->count());

                    // Add the calculated values to the KPI object
                    $kpi->actualCount = $actualCount;
                    $kpi->score = $score;
                    $totalScore += $score;
                }
            }
        }

        return view('kpi.kpi_dashboard.index_weekly', [
            'title' => 'KPI Dashboard',
            'active' => 'kpi-dashboard',
            'groupedKpis' => $groupedKpisByWeekAndCategory,
            'totalScore' => $totalScore,
            'users' => $this->getUser(),
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

            $kpisQuery = Kpi::with('kpi_detail', 'kpi_detail.kpi_description', 'kpi_type', 'kpi_category', 'user')
            ->where('kpi_type_id', 3)
            ->whereMonth('date', $date->month)
            ->whereYear('date', $date->year);

            $user_id = $request->input('user_id');

            if ($user_id) {
                $kpisQuery->where('user_id', $user_id);
            } else {
                $kpisQuery->where('user_id', auth()->user()->id);
            }

            $kpis = $kpisQuery->orderBy('date', 'DESC')
            ->get();
        }

        // Group the KPIs by yearly month
        $groupedKpisByYear = $kpis->groupBy(function ($kpi) {
            // Parse the date and get the year and month
            return CarbonImmutable::parse($kpi->date)->format('Y-m');
        });

        // Group the KPIs by KPI category within each month group
        $groupedKpisByYearAndCategory = [];
        $totalScore = 0;
        $averageTotalScore = 0;

        foreach ($groupedKpisByYear as $yearMonth => $groupedKpi) {
            $groupedKpiByCategory = $groupedKpi->groupBy('kpi_category.name');

            // Sort the grouped data by category name
            $groupedKpiByCategory = $groupedKpiByCategory->sortBy(function ($kpis, $categoryName) {
                // Define the desired order of categories
                $categoryOrder = ['MAIN JOB', 'ADMINISTRATION', 'REPORTING'];
                // Get the index of the category name in the desired order
                $categoryIndex = array_search($categoryName, $categoryOrder);
                // Return the category index for sorting
                return $categoryIndex !== false ? $categoryIndex : count($categoryOrder);
            });

            $groupedKpisByYearAndCategory[$yearMonth] = $groupedKpiByCategory;

            $totalKpiCategories = count($groupedKpiByCategory);

            foreach ($groupedKpiByCategory as $categoryName => $kpis) {
                foreach ($kpis as $kpi) {
                    // Now, you can safely access the KPI details and perform calculations.
                    $kpiDetailWithValue = $kpi->kpi_detail->filter(function ($kpiDetail) {
                        return $kpiDetail->value_result !== null && $kpiDetail->value_result >= 0;
                    });

                    $actualCount = $kpiDetailWithValue->sum('value_result');
                    $count = $kpiDetailWithValue->count();

                    $score = 0;
                    if ($count > 0) {
                        $score = ($kpi->percentage / 100) * ($actualCount / $count);
                    }

                    // If $count is zero, assign a default value of 1 to divisor
                    $divisor = $count > 0 ? $count : 1;
                    $actualCount = $actualCount / $divisor;
                    $score = ($kpi->percentage / 100) * ($kpiDetailWithValue->sum('value_result') / $kpi->kpi_detail->count());

                    // Add the calculated values to the KPI object
                    $kpi->actualCount = $actualCount;
                    $kpi->score = $score;
                    $totalScore += $score;
                }
            }
            // $averageTotalScore = $totalScore / $totalKpiCategories;
        }


        return view('kpi.kpi_dashboard.index_monthly', [
            'title' => 'KPI Dashboard',
            'active' => 'kpi-dashboard',
            'groupedKpis' => $groupedKpisByYearAndCategory,
            'totalScore' => $totalScore,
            'users' => $this->getUser(),
            'divisions' => Divisi::all(),
        ]);
    }

    public function indexKpi(Request $request) {
        $currentMonth = Carbon::now()->month;
        $currentYear = Carbon::now()->year;

        $weeklyKpis = Kpi::with('kpi_detail', 'kpi_detail.kpi_description', 'kpi_type', 'kpi_category', 'user')
            ->where('kpi_type_id', 2);

        $monthlyKpis = Kpi::with('kpi_detail', 'kpi_detail.kpi_description', 'kpi_type', 'kpi_category', 'user')
            ->where('kpi_type_id', 3);

        $yearlyKpis = Kpi::with('kpi_detail', 'kpi_detail.kpi_description', 'kpi_type', 'kpi_category', 'user')
            ->where('kpi_type_id', 3);

        $usersYearlyKpis = Kpi::with('kpi_detail', 'kpi_detail.kpi_description', 'kpi_type', 'kpi_category', 'user')
            ->where('kpi_type_id', 3);

        // KALAU FILTER MONTH DI CHART KPI WEEKLY
        if ($request->dateChartHighestKpiWeekly) {
            $date = Carbon::createFromFormat('m/Y', $request->dateChartHighestKpiWeekly);

            $weeklyKpis = $weeklyKpis->whereMonth('date', $date->month)
            ->whereYear('date', $date->year);
        } else {
            $weeklyKpis = $weeklyKpis->whereMonth('date', $currentMonth)
            ->whereYear('date', $currentYear);
        }

        // KALAU FILTER MONTH DI CHART KPI MONTHLY
        if ($request->dateChartHighestKpiMonthly) {
            $date = Carbon::createFromFormat('m/Y', $request->dateChartHighestKpiMonthly);

            $dateChartHighestKpiMonthly = Carbon::parse($date)->format('M Y');

            $monthlyKpis = $monthlyKpis->whereMonth('date', $date->month)
            ->whereYear('date', $date->year);
        } else {
            $monthlyKpis = $monthlyKpis->whereMonth('date', $currentMonth)
            ->whereYear('date', $currentYear);
        }

        //KALAU FILTER YEAR DI CHART KPI YEARLY
        if ($request->dateChartKpiYearly) {
            $year = $request->dateChartKpiYearly;
            $date = Carbon::createFromFormat('Y-m-d', $year . '-01-01');
            $currentYear = $date->year;

            $dateChartKpiYearly = $date->format('Y');

            $yearlyKpis = $yearlyKpis->whereYear('date', $date->year);
        } else {
            $yearlyKpis = $yearlyKpis->whereYear('date', $currentYear);
        }

        //KALAU FILTER YEAR DI CHART USERS KPI YEARLY
        if ($request->dateChartUsersKpiYearly) {
            $year = $request->dateChartUsersKpiYearly;
            $date = Carbon::createFromFormat('Y-m-d', $year . '-01-01');
            $currentYear = $date->year;

            $dateChartUsersKpiYearly = $date->format('Y');

            $usersYearlyKpis = $usersYearlyKpis->whereYear('date', $date->year);
        } else {
            $usersYearlyKpis = $usersYearlyKpis->whereYear('date', $currentYear);
        }

        // KALAU FILTER DIVISI DI CHART KPI WEEKLY
        if($request->divisi_weekly) {
            $weeklyKpis = $weeklyKpis->whereHas('user', function ($q) use ($request) {
                $q->where('divisi_id', $request->divisi_weekly);
            });
        }

        // KALAU FILTER DIVISI DI CHART KPI MONTHLY
        if($request->divisi_monthly) {
            $monthlyKpis = $monthlyKpis->whereHas('user', function ($q) use ($request) {
                $q->where('divisi_id', $request->divisi_monthly);
            });

            $divisi_monthly = Divisi::where('id', $request->divisi_monthly)->value('name');
        }

        // KALAU FILTER DIVISI DI CHART KPI YEARLY
        if($request->divisi_yearly) {
            $yearlyKpis = $yearlyKpis->whereHas('user', function ($q) use ($request) {
                $q->where('divisi_id', $request->divisi_yearly);
            });

            $divisi_yearly = Divisi::where('id', $request->divisi_yearly)->value('name');
        }

        // KALAU FILTER USER DI CHART USERS KPI YEARLY
        if($request->user_yearly) {
            $usersYearlyKpis = $usersYearlyKpis->whereHas('user', function ($q) use ($request) {
                $q->where('id', $request->user_yearly);
            });

            $user_yearly = User::where('id', $request->user_yearly)->value('nama_lengkap');
        } else {
            $usersYearlyKpis = $usersYearlyKpis->whereHas('user', function ($q) use ($request) {
                $q->where('id', auth()->user()->id);
            });
        }

        if (auth()->user()->role_id != 1) {
            $weeklyKpis = $weeklyKpis->whereHas('user', function ($q) {
                $q->where('divisi_id', auth()->user()->divisi_id);
            });

            $monthlyKpis = $monthlyKpis->whereHas('user', function ($q) {
                $q->where('divisi_id', auth()->user()->divisi_id);
            });

            $yearlyKpis = $yearlyKpis->whereHas('user', function ($q) {
                $q->where('divisi_id', auth()->user()->divisi_id);
            });

            $usersYearlyKpis = $usersYearlyKpis->whereHas('user', function ($q) {
                $q->where('divisi_id', auth()->user()->divisi_id);
            });
        } else {
            $weeklyKpis = $weeklyKpis->limit(15);

            $monthlyKpis = $monthlyKpis->limit(15);

            $yearlyKpis = $yearlyKpis->limit(15);

            $usersYearlyKpis = $usersYearlyKpis->limit(15);
        }

        $weeklyKpis = $weeklyKpis->orderBy('date', 'DESC')
        ->get();

        $monthlyKpis = $monthlyKpis->orderBy('date', 'DESC')
        ->get();

        $yearlyKpis = $yearlyKpis->orderBy('date', 'DESC')
        ->get();

        $usersYearlyKpis = $usersYearlyKpis->orderBy('date', 'DESC')
        ->get();

        // Group the KPIs by yearly month
        $groupedKpisByYear = $weeklyKpis->groupBy(function ($kpi) {
            // Parse the date and get the year and month
            return CarbonImmutable::parse($kpi->date)->format('Y-m');
        });

        // Group the KPIs by yearly month
        $groupedMonthlyKpisByYear = $monthlyKpis->groupBy(function ($kpi) {
            // Parse the date and get the year and month
            return CarbonImmutable::parse($kpi->date)->format('Y-m');
        });

        // Group the KPIs by year
        $groupedYearKpisByYear = $yearlyKpis->groupBy(function ($kpi) {
            // Parse the date and get the year and month
            return CarbonImmutable::parse($kpi->date)->format('Y-m');
        });

        // Group the KPIs by year
        $groupedUsersKpisByYear = $usersYearlyKpis->groupBy(function ($kpi) {
            // Parse the date and get the year and month
            return CarbonImmutable::parse($kpi->date)->format('Y-m');
        });

        // Group the KPIs by KPI category within each month group
        $groupedKpisByYearAndCategory = [];
        $totalScore = 0;
        $userTotalScores = [];

        // Group the KPIs by KPI category within each month group
        $groupedMonthlyKpisByYearAndCategory = [];
        $totalScoreMonthly = 0;
        $userTotalScoresMonthly = [];

        // WEEKLY
        foreach ($groupedKpisByYear as $yearMonth => $groupedKpi) {
            $groupedKpiByCategory = $groupedKpi->groupBy('kpi_category.name');
            $groupedKpisByYearAndCategory[$yearMonth] = $groupedKpiByCategory;

            foreach ($groupedKpiByCategory as $categoryName => $weeklyKpis) {
                foreach ($weeklyKpis as $kpi) {
                    // Now, you can safely access the KPI details and perform calculations.
                    $kpiDetailWithValue = $kpi->kpi_detail->filter(function ($kpiDetail) {
                        return $kpiDetail->value_result !== null && $kpiDetail->value_result >= 0;
                    });

                    $actualCount = $kpiDetailWithValue->sum('value_result');
                    $score = ($kpi->percentage / 100) * ($actualCount / $kpi->kpi_detail->count());

                    // Add the calculated values to the KPI object
                    $kpi->actualCount = $actualCount;
                    $kpi->score = $score;

                    // Accumulate the score for each user
                    $userId = $kpi->user->id;
                    if (!isset($userTotalScores[$userId])) {
                        $userTotalScores[$userId] = 0;
                    }
                    $userTotalScores[$userId] += $score;
                }
            }
        }

        // MONTHLY
        foreach ($groupedMonthlyKpisByYear as $yearMonth => $groupedKpi) {
            $groupedMonthlyKpiByCategory = $groupedKpi->groupBy('kpi_category.name');
            $groupedMonthlyKpisByYearAndCategory[$yearMonth] = $groupedMonthlyKpiByCategory;

            foreach ($groupedMonthlyKpiByCategory as $categoryName => $monthlyKpis) {
                foreach ($monthlyKpis as $kpi) {
                    // Now, you can safely access the KPI details and perform calculations.
                    $kpiDetailWithValue = $kpi->kpi_detail->filter(function ($kpiDetail) {
                        return $kpiDetail->value_result !== null && $kpiDetail->value_result >= 0;
                    });

                    $actualCount = $kpiDetailWithValue->sum('value_result');
                    $score = ($kpi->percentage / 100) * ($actualCount / $kpi->kpi_detail->count());

                    // Add the calculated values to the KPI object
                    $kpi->actualCount = $actualCount;
                    $kpi->score = $score;

                    // Accumulate the score for each user
                    $userId = $kpi->user->id;
                    if (!isset($userTotalScoresMonthly[$userId])) {
                        $userTotalScoresMonthly[$userId] = 0;
                    }
                    $userTotalScoresMonthly[$userId] += $score;
                }
            }
        }

        // After calculating the total scores for each user, find the user with the highest score
        $highestScore = 0;
        foreach ($userTotalScores as $userId => $totalScore) {
            if ($totalScore > $highestScore) {
                $highestScore = $totalScore;
            }
        }
        arsort($userTotalScores);

        // After calculating the total scores for each user, find the user with the highest score
        $highestScoreMonthly = 0;
        foreach ($userTotalScoresMonthly as $userId => $totalScoreMonthly) {
            if ($totalScoreMonthly > $highestScoreMonthly) {
                $highestScoreMonthly = $totalScoreMonthly;
            }
        }
        arsort($userTotalScoresMonthly);

        $highestKpiWeeklyUser = [];
        $highestKpiWeeklyUnit = [];

        $highestKpiMonthlyUser = [];
        $highestKpiMonthlyUnit = [];

        // KPI WEEKLY
        foreach ($userTotalScores as $userId => $totalScore) {
            $user = User::find($userId); // Assuming you have a "User" model.

             if ($user && $totalScore > 0) {
                $highestKpiWeeklyUser[] = $user->nama_lengkap ?? '-'; // Assuming the user's name field is "name". Replace it with the actual field name.
                $highestKpiWeeklyUnit[] = $totalScore * 100; // Add the user's total score to the array
            }
        }

        // KPI MONTHLY
        foreach ($userTotalScoresMonthly as $userId => $totalScoreMonthly) {
            $user = User::find($userId); // Assuming you have a "User" model.

             if ($user && $totalScoreMonthly > 0) {
                $highestKpiMonthlyUser[] = $user->nama_lengkap ?? '-'; // Assuming the user's name field is "name". Replace it with the actual field name.
                $highestKpiMonthlyUnit[] = $totalScoreMonthly * 100; // Add the user's total score to the array
            }
        }

        // YEAR BAR CHART
        // Array to store the average KPI values for each month in 2023
        $averageKpiMonthlyByYear = [];
        $userCounts = [];

        // Iterate over each month in the year
        for ($month = 1; $month <= 12; $month++) {
            $yearMonth = $currentYear . '-' . sprintf("%02d", $month); // Format the month as "YYYY-MM"

            $yearlyGroupedKpis = $groupedYearKpisByYear[$yearMonth] ?? collect(); // Get the KPIs for the current month

            // Calculate the cumulative sum of KPI scores for the month
            $cumulativeScore = 0;

            // Create an empty array or collection to store unique user IDs
            $uniqueUserIds = [];

            // Group the KPIs by user ID
            $groupedKpisByUser = $yearlyGroupedKpis->groupBy('user_id');

            foreach ($yearlyGroupedKpis as $kpi) {
                $uniqueUserIds[] = $kpi->user_id;

                $kpiDetailWithValue = $kpi->kpi_detail->filter(function ($kpiDetail) {
                    return $kpiDetail->value_result !== null && $kpiDetail->value_result >= 0;
                });

                if ($kpiDetailWithValue->isNotEmpty()) {
                    $actualCount = $kpiDetailWithValue->sum('value_result');
                    $score = ($kpi->percentage / 100) * ($actualCount / $kpiDetailWithValue->count());

                    // Add the calculated values to the KPI object
                    $kpi->actualCount = $actualCount;
                    $kpi->score = $score;

                    // Accumulate the score for the month
                    $cumulativeScore += $score;
                }
            }
            // Calculate the count of unique user IDs
            $userCount = count(array_unique($uniqueUserIds));

            // Store the user count for the month
            $userCounts[$yearMonth] = $userCount;

            // Calculate the average KPI for the month
            $averageKpiMonthly = $userCount > 0 ? ($cumulativeScore * 100) / $userCount : 0;

            // Store the average KPI in the array
            $averageKpiMonthlyByYear[$yearMonth] = $averageKpiMonthly;
        }

        // Dump the average KPIs for each month in 2023
        // dd($averageKpiMonthlyByYear);

        $averageYearlyKpi = [];
        $monthYear = [];

        foreach ($averageKpiMonthlyByYear as $yearMonth => $averageKpi) {
            $carbonDate = Carbon::createFromFormat('Y-m', $yearMonth); // Create a Carbon instance from the yearMonth string
            $monthName = $carbonDate->formatLocalized('%b'); // Get the abbreviated month name

            $averageYearlyKpi[] = $averageKpi;
            $monthYear[] = $monthName;
        }

        // PER USER YEAR BAR CHART
        $usersAverageKpiMonthlyByYear = [];

        // Iterate over each month in the year
        for ($month = 1; $month <= 12; $month++) {
            $yearMonth = $currentYear . '-' . sprintf("%02d", $month); // Format the month as "YYYY-MM"

            $yearlyGroupedKpis = $groupedUsersKpisByYear[$yearMonth] ?? collect(); // Get the KPIs for the current month

            // Calculate the cumulative sum of KPI scores for the month
            $cumulativeScore = 0;
            $userKpis = $yearlyGroupedKpis;

            foreach ($userKpis as $kpi) {
                $kpiDetailWithValue = $kpi->kpi_detail->filter(function ($kpiDetail) {
                    return $kpiDetail->value_result !== null && $kpiDetail->value_result >= 0;
                });

                if ($kpiDetailWithValue->isNotEmpty()) {
                    $actualCount = $kpiDetailWithValue->sum('value_result');
                    $score = ($kpi->percentage / 100) * ($actualCount / $kpiDetailWithValue->count());

                    // Accumulate the score for the month
                    $cumulativeScore += $score;
                }
            }

            // Calculate the average KPI for the month
            $usersAverageKpiMonthly = $userKpis->count() > 0 ? ($cumulativeScore * 100) : 0;

            // Store the average KPI in the array
            $usersAverageKpiMonthlyByYear[$yearMonth] = $usersAverageKpiMonthly;
        }

        // Dump the average KPIs for each month in 2023
        // dd($usersAverageKpiMonthlyByYear);

        $usersAverageYearlyKpi = [];
        $usersMonthYear = [];

        foreach ($usersAverageKpiMonthlyByYear as $yearMonth => $averageKpi) {
            $carbonDate = Carbon::createFromFormat('Y-m', $yearMonth); // Create a Carbon instance from the yearMonth string
            $monthName = $carbonDate->formatLocalized('%b'); // Get the abbreviated month name

            $usersAverageYearlyKpi[] = $averageKpi;
            $usersMonthYear[] = $monthName;
        }

        return view('kpi.kpi_dashboard.index_kpi', [
            'title' => 'KPI Dashboard',
            'active' => 'kpi-dashboard',
            'divisions' => Divisi::all(),
            'highestKpiWeeklyUser' => $highestKpiWeeklyUser,
            'highestKpiWeeklyUnit' => $highestKpiWeeklyUnit,
            'highestKpiMonthlyUser' => $highestKpiMonthlyUser,
            'highestKpiMonthlyUnit' => $highestKpiMonthlyUnit,
            'dateChartHighestKpiMonthly' => $dateChartHighestKpiMonthly ?? now()->format('M Y'),
            'divisiMonthly' => $divisi_monthly ?? '',
            'averageYearlyKpi' => $averageYearlyKpi,
            'monthYear' => $monthYear,
            'dateChartKpiYearly' => $dateChartKpiYearly ?? now()->format('Y'),
            'divisiYearly' => $divisi_yearly ?? '',
            'usersAverageYearlyKpi' => $usersAverageYearlyKpi,
            'usersMonthYear' => $usersMonthYear,
            'users' => $this->getUser(),
            'dateChartUsersKpiYearly'  => $dateChartUsersKpiYearly ?? now()->format('Y'),
            'userYearly' => $user_yearly ?? auth()->user()->nama_lengkap,
        ]);
    }

    public function getUser() {
        if (auth()->user()->role_id == 1) {
            $users = User::where('id', '<>', 1)
            ->orderBy('nama_lengkap')
            ->get();
        } else {
            $users = User::where('divisi_id', auth()->user()->divisi_id)
            ->orderBy('nama_lengkap')
            ->get();
        }

        return $users;
    }

    public function changeStatus(Request $request) {
        try {
            // VALIDASI WAKTU
            $currentDate = Carbon::now();
            $detailKpi = KpiDetail::findOrFail($request->id);
            $kpiDate = Carbon::parse($detailKpi->kpi->date);

            // FOR TESTING PURPOSES
            // $currentDate = Carbon::createFromFormat('Y-m-d', '2023-08-07');

            // VALIDASI STAFF & TEAM LEDAER
            if (auth()->user()->role_id == 2 || auth()->user()->role_id == 3) {
                if (!$kpiDate->isSameMonth($currentDate)) {
                    return redirect()->back()->with(['error' => 'Cannot change KPI for previous or future months!']);
                }
            }

            if (auth()->user()->role_id != 1) {
                if ($currentDate > $kpiDate->addMonth(1)->firstOfMonth()->addDays(7)) {
                    return redirect()->back()->with(['error' => 'Cannot change KPI after 7 days of next month!']);
                }
            }

            if ($request->value_actual) {
                $detailKpi->value_actual = $request->value_actual;
                $detailKpi->value_result = $request->value_actual / $detailKpi->value_plan;
                $detailKpi->save();
            } else {
                $detailKpi->value_result = ($detailKpi->value_result == 0 || $detailKpi->value_result == null) ? 1 : 0;
                $detailKpi->save();
            }

            return redirect()->back()->with(['success' => 'Successfully Updated !']);
        } catch (Exception $e) {
            return redirect()->back()->with(['error' => $e->getMessage()]);
        }
    }

}
