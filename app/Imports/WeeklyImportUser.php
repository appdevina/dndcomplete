<?php

namespace App\Imports;

use App\Helpers\ConvertDate;
use App\Models\User;
use App\Models\Weekly;
use App\Models\WeeklyLog;
use Exception;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class WeeklyImportUser implements ToModel, WithHeadingRow
{
    protected $user, $page;

    function __construct($user, $page)
    {
        $this->user = $user;
        $this->page = $page;
    }

    /**
     * @param Collection $collection
     */
    public function model(array $row)
    {
        $year = preg_replace('/\s+/', '', $row['year']);
        $week = preg_replace('/\s+/', '', $row['week']);
        $tipe = preg_replace('/\s+/', '', strtoupper($row['tipe']));
        if (auth()->user()->role_id != 1) {
            $monday = ConvertDate::getMondayOrSaturday($year, $week, true);
            if ($this->user->area_id == 2 && now() > $monday->addDay(1)->addHour(10)) {
                throw new Exception('Tidak bisa import weekly week ' . now()->week . ' sudah lebih dari '.$monday->format('d M y'));
            }
            if ($this->user->area_id != 2 && now() > $monday->addHour(17)) {
                throw new Exception('Tidak bisa import weekly week ' . now()->week . ' sudah lebih dari ' . $monday->format('d M y - H:i'));
            }
        }
        if ($year <= now()->year && $week < now()->weekOfYear) {
            throw new Exception("Tidak bisa import weekly kurang dari week " . now()->weekOfYear);
        }
        if ($week > 52 || $year < 2022 || $week < 0) {
            throw new Exception("Tidak bisa import weekly lebih dari week 52 atau minimal tahun 2022");
        }
        if ($tipe == "NON") {
            $weekly = new Weekly([
                'user_id' => $this->user->id,
                'task' => $row['task'],
                'year' => $year,
                'week' => $week,
                'tipe' => $tipe,
                'status_non' => 0,
            ]);
            $weekly->save();
        } else if ($tipe == 'RESULT') {
            if (!$row['value_plan_result']) {
                throw new Exception('Untuk task bertipe result wajib isi kolom "value_plan_result"');
            }
            if (!ctype_digit(preg_replace('/\s+/', '', $row['value_plan_result']))) {
                throw new Exception('Tidak bisa import weekly untuk kolom "value_plan_result" harus berisi nominal angka');
            }
            if ($this->user->wr) {
                $weekly = new Weekly([
                    'user_id' => $this->user->id,
                    'task' => $row['task'],
                    'year' => $year,
                    'week' => $week,
                    'tipe' => $tipe,
                    'value_plan' => preg_replace('/\s+/', '', $row['value_plan_result']),
                    'value_actual' => 0,
                    'status_result' => 0,
                ]);
                $weekly->save();
            } else {
                throw new Exception('Tidak bisa import weekly anda tidak memiliki task weekly result');
            }
        }
            if ($this->page == 'teams') {
            $weeklyLog = WeeklyLog::create([
                'user_id' => auth()->user()->id,
                'task_id' => $weekly->id,
                'activity' => 'Mengirim task ' . Weekly::find($weekly->id)->task . ' ke ' . $this->user->nama_lengkap,
            ]);
            $weeklyLog->save();
            
            return $weekly;
        } else {
            throw new Exception('ada kesalahan format kolom tipe harus berisi NON atau RESULT');
        }
    }
}
