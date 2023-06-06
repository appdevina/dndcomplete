<?php

namespace App\Http\Controllers;

use App\Exports\TemplateWeekly;
use App\Exports\WeeklyExport;
use App\Helpers\ConvertDate;
use App\Imports\WeeklyImportUser;
use App\Models\Divisi;
use App\Models\Request as ModelsRequest;
use App\Models\TaskCategory;
use App\Models\TaskStatus;
use App\Models\User;
use App\Models\Weekly;
use App\Models\WeeklyLog;
use Exception;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

use function GuzzleHttp\Promise\all;

class WeeklyController extends Controller
{
    public function indexUser(Request $request)
    {
        switch ($request->tasktype) {
            case '1':
                $weeklys = Weekly::with('user')->where('year', now()->year)->where('week', now()->weekOfYear)->orderBy('week', 'DESC')->where('user_id', auth()->id())->get();
                break;

            case '2':
                $weeklys = Weekly::with('user')->where('year', now()->year)->where('week', now()->weekOfYear - 1)->orderBy('week', 'DESC')->where('user_id', auth()->id())->get();
                break;

            default:
                $weeklys = Weekly::with('user')->orderBy('week', 'DESC')->where('user_id', auth()->id())->get();
                break;
        }
        return view('admin.weekly.index')->with([
            'title' => 'Weekly',
            'active' => 'weekly',
            'weeklys' => $weeklys,
        ]);
    }

    public function indexTeamsWeekly(Request $request)
    {
        switch ($request->tasktype) {
            case '1':
                $weeklys = Weekly::with(['user'])
                ->whereHas('user', function ($query) {
                        $query->where('divisi_id', auth()->user()->divisi_id);
                    })
                ->where('year', now()->year)
                ->where('week', now()->weekOfYear)
                ->orderBy('week', 'DESC')
                ->simplePaginate(30);
                break;

            case '2':
                $weeklys = Weekly::with(['user'])
                ->whereHas('user', function ($query) {
                        $query->where('divisi_id', auth()->user()->divisi_id);
                    })
                ->where('year', now()->year)
                ->where('week', now()->weekOfYear - 1)
                ->orderBy('week', 'DESC')
                ->simplePaginate(30);
                break;

            default:
                $weeklys = Weekly::with(['user'])
                ->whereHas('user', function ($query) {
                        $query->where('divisi_id', auth()->user()->divisi_id);
                    })
                ->orderBy('week', 'DESC')
                ->simplePaginate(30);
                break;
        }

        if ($request->user) {
            $weeklys = Weekly::with('user')
            ->where('user_id', $request->user)
            ->orderBy('week', 'DESC')
            ->simplePaginate(30);
        }

        $logs = WeeklyLog::with(['user' => function ($query) {
                $query->select('id','nama_lengkap', 'divisi_id');
            }])
                ->whereHas('user', function ($query) {
                    $query->where('divisi_id', auth()->user()->divisi_id);
                })
                ->limit(30)
                ->orderBy('created_at', 'DESC')
                ->simplePaginate(30);

        if ($request->user_log) {
            $logs = WeeklyLog::with('user')
                ->where('user_id', $request->user_log)
                ->orderBy('created_at', 'DESC')
                ->simplePaginate(30);
        }

        return view('teams.weekly.index')->with([
            'title' => 'Weekly',
            'active' => 'teams-weekly',
            'weeklys' => $weeklys,
            'logs' => $logs,
            'users' => User::where('divisi_id', auth()->user()->divisi_id)->get(),
        ]);
    }

    public function teamsWeeklyEdit(Weekly $weekly)
    {
        ##CEK TASK PUNYA SENDIRI ATAU BUKAN
        if ($weekly->user_id != auth()->id()) {
            return redirect('/teams/weekly')->with(['error' => 'Task ini bukan milik anda !']);
        }

        return view('teams.weekly.change')->with([
            'title' => 'Edit Weekly',
            'active' => 'teams-weekly',
            'weekly' => $weekly,
            'task_categories' => TaskCategory::all(),
            'task_status' => TaskStatus::all(),
        ]);
    }

    public function teamsWeeklyUpdate(Request $request, Weekly $weekly)
    {
        try {
            ##CEK TASK DI REQUEST ATAU TIDAK
            $requesteds = ModelsRequest::where('user_id', auth()->id())->where('jenistodo', 'Weekly')->get();
            foreach ($requesteds as $requested) {
                $idTaskExistings = explode(',', $requested->todo_request);
                foreach ($idTaskExistings as $idTaskExisting) {
                    if ($request->id == $idTaskExisting && $requested->status == 'PENDING') {
                        if ($request->page == 'teams') {
                            return redirect('/teams/weekly')->with(['error' => 'Tidak bisa merubah status, task ini ada di pengajuan request task']);
                        }
                        return redirect('weekly')->with(['error' => "Tidak bisa merubah, task ini ada di pengajuan request task"]);
                    }
                }

                $idTaskReplaces = explode(',', $requested->todo_replace);
                foreach ($idTaskReplaces as $idTaskReplace) {
                    if ($request->id == $idTaskReplace && $requested->status == 'PENDING') {
                        if ($request->page == 'teams') {
                            return redirect('/teams/weekly')->with(['error' => 'Tidak bisa merubah status, task ini ada di pengajuan request task dan belum di approve']);
                        }
                        return redirect('weekly')->with(['error' => "Tidak bisa merubah, task ini ada di pengajuan request task"]);
                    }
                }
            }

            ##CEK WEEKLY LEBIH DARI WAKTUNYA CEKLIS ATAU TIDAK
            $monday = ConvertDate::getMondayOrSaturday($weekly->year, $weekly->week, true);

            if (auth()->user()->area_id == 2 && now() > $monday->addDay(8)->addHour(10)) {
                if ($request->page == 'teams') {
                    return redirect('/teams/weekly')->with(['error' => "Tidak bisa merubah status weekly sudah lebih dari hari selasa jam 10:00"]);
                }
                return redirect('weekly')->with(['error' => "Tidak bisa merubah status weekly sudah lebih dari hari selasa jam 10:00"]);
            }

            $monday2 = ConvertDate::getMondayOrSaturday($weekly->year, $weekly->week, true);
            if (auth()->user()->area_id != 2 && now() > $monday2->addDay(7)->addHour(17)) {
                if ($request->page == 'teams') {
                    return redirect('/teams/weekly')->with(['error' => "Tidak bisa merubah status weekly sudah lebih dari hari senin jam 17:00"]);
                }
                return redirect('weekly')->with(['error' => "Tidak bisa merubah status weekly sudah lebih dari hari senin jam 17:00"]);
            }

            ##PASS ALL VALIDATION
            if ($request->value_actual) {
                $weekly['value_actual'] = $request->value_actual;
                $weekly['status_result'] = true;
                $weekly['value'] = $weekly['value_actual'] / $weekly['value_plan'] > 1.2 ? 1.2 : $weekly['value_actual'] / $weekly['value_plan'];
            } else {
                $weekly['status_non'] = !$weekly['status_non'];
                $weekly['value'] = $weekly['status_non'] ? 1 : 0;
            }

            $weekly->task_category_id = $request->task_category_id ?? '';
            $weekly->task_status_id = $request->task_status_id ?? '';
    
            if ($request->task_status_id == 1) {
                $weekly->value = 1;
            } else {
                $weekly->value = 0;
            }
            $weekly->save();

            $task = Weekly::find($weekly->id);
            $category = TaskCategory::find($request->task_category_id);
            $status = TaskStatus::find($request->task_status_id);

            $weeklyLog = WeeklyLog::create([
                'user_id' => auth()->user()->id,
                'task_id' => $weekly->id,
                'activity' => 'Merubah kategori task ' . $task->task . ' menjadi ' . $category->task_category . ' dan status task menjadi ' . $status->task_status,
            ]);
            $weeklyLog->save();
            
            return redirect('/teams/weekly')->with(['success' => 'berhasil update status task']);
        } catch (Exception $e) {
            return redirect('/teams/weekly')->with(['error' => $e->getMessage()]);
        }
    }

    public function templateUser(Request $request)
    {
        return Excel::download(new TemplateWeekly, 'weekly_template.xlsx',);
    }

    public function importWeeklyUser(Request $request)
    {
        $file = $request->file('file');
        $namaFile = $file->getClientOriginalName();
        $file->move(public_path('import'), $namaFile);
        try {
            Excel::import(new WeeklyImportUser(auth()->user()->role_id == 1 ? User::find($request->userid) : auth()->user()), public_path('/import/' . $namaFile));
        } catch (Exception $e) {
            return redirect(auth()->user()->role_id == 1 ? 'admin/weekly' : 'weekly')->with(['error' => $e->getMessage()]);
        }

        return redirect(auth()->user()->role_id == 1 ? 'admin/weekly' : 'weekly')->with(['success' => 'berhasil import weekly']);
    }

    public function exportAdmin(Request $request)
    {
        return Excel::download(new WeeklyExport($request->week, $request->year), 'weekly_week_' . $request->week . '_year_' . $request->year . '.xlsx',);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if ($request->year && $request->week && $request->name) {
            $weeklys = Weekly::with('user', 'user.area', 'user.divisi')
                ->where('year', $request->year)
                ->where('week', $request->week)
                ->whereHas('user', function ($q) use ($request) {
                    $q->where('nama_lengkap', "like", '%' . $request->name . '%')->orderBy('nama_lengkap');
                })
                ->get();
        } else if ($request->divisi_id) {
            $weeklys = Weekly::with('user', 'user.area', 'user.divisi')
                ->where('year', now()->year)
                ->where('week', now()->weekOfYear)
                ->whereHas('user.divisi', function ($q) use ($request) {
                    $q->where('id', $request->divisi_id);
                })
                ->orderBy(User::select('nama_lengkap')->whereColumn('users.id', 'weeklies.user_id'))
                ->get();
        } else {
            $weeklys = Weekly::with('user', 'user.area', 'user.divisi')
                ->where('year', now()->year)
                ->where('week', now()->weekOfYear)
                // ->orderBy(User::select('nama_lengkap')->whereColumn('users.id', 'weeklies.user_id'))
                ->simplePaginate(100)
                ->sortBy('user.nama_lengkap');
        }
        // dd($weeklys[0]);
        return view('admin.weekly.index')->with([
            'title' => 'Weekly',
            'active' => 'weekly',
            'divisis' => Divisi::all()->except(17),
            'users' => User::orderBy('nama_lengkap')->get()->except(1),
            'weeklys' => $weeklys,
        ]);
    }

    public function change(Request $request)
    {
        try {
            $weekly = Weekly::findOrfail($request->id);
            // if (auth()->id() != $weekly->user_id) {
            //     return back();
            // }

            ##CEK TASK PUNYA SENDIRI ATAU BUKAN
            if ($weekly->user_id != auth()->id()) {
                if ($request->page == 'teams') {
                    return redirect('/teams/weekly')->with(['error' => 'Bukan task milik anda !']);
                }
                return redirect('weekly')->with(['error' => 'Tidak bisa merubah status, task ini bukan milik anda']);
            }
            
            ##CEK TASK DI REQUEST ATAU TIDAK
            $requesteds = ModelsRequest::where('user_id', auth()->id())->where('jenistodo', 'Weekly')->get();
            foreach ($requesteds as $requested) {
                $idTaskExistings = explode(',', $requested->todo_request);
                foreach ($idTaskExistings as $idTaskExisting) {
                    if ($request->id == $idTaskExisting && $requested->status == 'PENDING') {
                        if ($request->page == 'teams') {
                            return redirect('/teams/weekly')->with(['error' => 'Tidak bisa merubah status, task ini ada di pengajuan request task']);
                        }
                        return redirect('weekly')->with(['error' => "Tidak bisa merubah, task ini ada di pengajuan request task"]);
                    }
                }

                $idTaskReplaces = explode(',', $requested->todo_replace);
                foreach ($idTaskReplaces as $idTaskReplace) {
                    if ($request->id == $idTaskReplace && $requested->status == 'PENDING') {
                        if ($request->page == 'teams') {
                            return redirect('/teams/weekly')->with(['error' => 'Tidak bisa merubah status, task ini ada di pengajuan request task dan belum di approve']);
                        }
                        return redirect('weekly')->with(['error' => "Tidak bisa merubah, task ini ada di pengajuan request task"]);
                    }
                }
            }
            $monday = ConvertDate::getMondayOrSaturday($weekly->year, $weekly->week, true);

            if (auth()->user()->area_id == 2 && now() > $monday->addDay(8)->addHour(10)) {
                if ($request->page == 'teams') {
                    return redirect('/teams/weekly')->with(['error' => "Tidak bisa merubah status weekly sudah lebih dari hari selasa jam 10:00"]);
                }
                return redirect('weekly')->with(['error' => "Tidak bisa merubah status weekly sudah lebih dari hari selasa jam 10:00"]);
            }

            $monday2 = ConvertDate::getMondayOrSaturday($weekly->year, $weekly->week, true);
            if (auth()->user()->area_id != 2 && now() > $monday2->addDay(7)->addHour(17)) {
                if ($request->page == 'teams') {
                    return redirect('/teams/weekly')->with(['error' => "Tidak bisa merubah status weekly sudah lebih dari hari senin jam 17:00"]);
                }
                return redirect('weekly')->with(['error' => "Tidak bisa merubah status weekly sudah lebih dari hari senin jam 17:00"]);
            }

            if (now()->year == $weekly->year && now()->weekOfYear < $weekly->week) {
                if ($request->page == 'teams') {
                    return redirect('/teams/weekly')->with(['error' => "Tidak bisa merubah status weekly lebih dari week " . now()->weekOfYear]);
                }
                return redirect('weekly')->with(['error' => "Tidak bisa merubah status weekly lebih dari week " . now()->weekOfYear]);
            }

            if ($request->value_actual) {
                $weekly['value_actual'] = $request->value_actual;
                $weekly['status_result'] = true;
                $weekly['value'] = $weekly['value_actual'] / $weekly['value_plan'] > 1.2 ? 1.2 : $weekly['value_actual'] / $weekly['value_plan'];
            } else {
                $weekly['status_non'] = !$weekly['status_non'];
                $weekly['value'] = $weekly['status_non'] ? 1 : 0;
            }

            $weekly->save();

            ##REDIRECT TEAMS DAILY
            if ($request->page == 'teams') {
                return redirect('/teams/weekly?tasktype=' . $request->tasktype)->with(['success' => 'Berhasil merubah status weekly']);
            }
            return redirect('weekly?tasktype=' . $request->tasktype)->with(['success' => 'Berhasil merubah status weekly']);
        } catch (Exception $e) {
            return redirect('weekly')->with(['error' => $e->getMessage()]);
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function showresult($id)
    {
        $weekly = Weekly::find($id);
        if (now()->year == $weekly->year && now()->weekOfYear < $weekly->week) {
            return redirect('weekly')->with(['error' => "Tidak bisa merubah status weekly lebih dari week " . now()->weekOfYear]);
        }
        $requesteds = ModelsRequest::where('user_id', auth()->id())->where('jenistodo', 'Weekly')->get();
        ##CEK TASK PADA REQUEST
        foreach ($requesteds as $requested) {
            $idTaskExistings = explode(',', $requested->todo_request);
            foreach ($idTaskExistings as $idTaskExisting) {
                if ($id == $idTaskExisting && $requested->status == 'PENDING') {
                    return redirect('weekly')->with(['error' => 'Tidak bisa merubah, task ini ada di pengajuan request task']);
                }
            }

            $idTaskReplaces = explode(',', $requested->todo_replace);
            foreach ($idTaskReplaces as $idTaskReplace) {
                if ($id == $idTaskReplace && $requested->status == 'PENDING') {
                    return redirect('weekly')->with(['error' => 'Tidak bisa merubah, task ini ada di pengajuan request task']);
                }
            }
        }
        if (auth()->id() != $weekly->user_id) {
            return back();
        }
        $monday = ConvertDate::getMondayOrSaturday($weekly->year, $weekly->week, true);
        if (auth()->user()->area_id == 2 && now() > $monday->addDay(8)->addHour(10)) {
            return redirect('weekly')->with(['error' => 'Tidak bisa merubah weekly sudah lebih dari week ' . now()->weekOfYear . ' hari selasa jam 10:00']);
        } else if (auth()->user()->area_id != 2 && now() > $monday->addDay(7)->addHour(17)) {
            return redirect('weekly')->with(['error' => 'Tidak bisa merubah weekly sudah lebih dari week ' . now()->weekOfYear . ' hari senin jam 17:00']);
        }

        return view('admin.weekly.change')->with([
            'title' => 'Weekly',
            'active' => 'weekly',
            'weekly' => $weekly,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try {
            $data = $request->all();

            if ($request->is_add) {
                $data['is_add'] = 1;
                $monday = ConvertDate::getMondayOrSaturday($data['year'], $data['week'], true);
                if (auth()->user()->area_id == 2 && now() > $monday->addDay(8)->addHour(10)) {
                    return redirect('weekly')->with(['error' => "Tidak bisa menambahkan extra task weekly di week ' . $request->week . ' sudah lebih dari hari selasa jam 10:00"]);
                } else if (auth()->user()->area_id != 2 && now() > $monday->addDay(7)->addHour(17)) {
                    return redirect('weekly')->with(['error' => 'Tidak bisa menambahkan extra task weekly di week ' . $request->week . ' sudah lebih dari hari senin jam 17:00']);
                }
            } else {
                $monday = ConvertDate::getMondayOrSaturday($data['year'], $data['week'], true);
                if (auth()->user()->area_id == 2 && now() > $monday->addDay(1)->addHour(10)) {
                    return redirect('weekly')->with(['error' => "Tidak bisa menambahkan weekly di week ' . $request->week . ' sudah lebih dari hari selasa jam 10:00"]);
                } else if (auth()->user()->area_id != 2 && now() > $monday->addHour(17)) {
                    return redirect('weekly')->with(['error' => 'Tidak bisa menambahkan weekly di week ' . $request->week . ' sudah lebih dari hari senin jam 17:00']);
                }
            }
            $data['user_id'] = auth()->id();
            unset($data['_token']);
            if ($request->result) {
                if (!$request->value_plan) {
                    return redirect('weekly')->with(['error' => 'Task result harus memasukkan value plan']);
                }
                $data['tipe'] = 'RESULT';
                $data['status_result'] = 0;
            } else {
                $data['tipe'] = 'NON';
                $data['status_non'] = 0;
            }
            unset($data['result']);
            Weekly::create($data);
            return redirect('weekly')->with(['success' => 'Berhasil menambahkan weekly']);
        } catch (Exception $e) {
            return redirect('weekly')->with(['error' => $e->getMessage()]);
        }
    }


    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $weekly = Weekly::find($id);
        $requesteds = ModelsRequest::where('user_id', auth()->id())->where('jenistodo', 'Weekly')->get();
        ##CEK TASK PADA REQUEST
        foreach ($requesteds as $requested) {
            $idTaskExistings = explode(',', $requested->todo_request);
            foreach ($idTaskExistings as $idTaskExisting) {
                if ($id == $idTaskExisting && $requested->status == 'PENDING') {
                    return redirect('weekly')->with(['error' => 'Tidak bisa merubah, task ini ada di pengajuan request task']);
                }
            }

            $idTaskReplaces = explode(',', $requested->todo_replace);
            foreach ($idTaskReplaces as $idTaskReplace) {
                if ($id == $idTaskReplace && $requested->status == 'PENDING') {
                    return redirect('weekly')->with(['error' => 'Tidak bisa merubah, task ini ada di pengajuan request task']);
                }
            }
        }
        if (auth()->id() != $weekly->user_id) {
            return back();
        }
        $monday = ConvertDate::getMondayOrSaturday($weekly->year, $weekly->week, true);
        if (auth()->user()->area_id == 2 && now() > $monday->addDay(1)->addHour(10)) {
            return redirect('weekly')->with(['error' => 'Tidak bisa merubah weekly sudah lebih dari week ' . now()->weekOfYear . ' hari selasa jam 10:00']);
        } else if (auth()->user()->area_id != 2 && now() > $monday->addHour(17)) {
            return redirect('weekly')->with(['error' => 'Tidak bisa merubah weekly sudah lebih dari week ' . now()->weekOfYear . ' hari senin jam 17:00']);
        }
        return view('admin.weekly.edit')->with([
            'title' => 'Weekly',
            'active' => 'weekly',
            'weekly' => $weekly,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        try {
            $weekly = Weekly::find($id);
            if ($request->year <= $weekly->year && $request->week < $weekly->week) {
                return redirect('weekly')->with(['error' => 'Tidak bisa mengubah weekly ke minggu sebelumnya']);
            }
            $weekly['task'] = $request->task;
            $weekly['year'] = (int) $request->year;
            $weekly['week'] = (int) $request->week;
            if ($weekly->tipe == 'RESULT') {
                $weekly['value_plan'] = (int) $request->value_plan;
            }
            $weekly->save();
            return redirect('weekly')->with(['success' => 'Berhasil merubah weekly']);
        } catch (Exception $e) {
            return redirect('weekly')->with(['error' => $e->getMessage()]);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        try {
            $weekly = Weekly::findOrFail($request->id);
            $requesteds = ModelsRequest::where('user_id', auth()->id())->where('jenistodo', 'Weekly')->get();
            foreach ($requesteds as $requested) {
                $idTaskExistings = explode(',', $requested->todo_request);
                foreach ($idTaskExistings as $idTaskExisting) {
                    if ($request->id == $idTaskExisting && $requested->status == 'PENDING') {
                        return redirect('weekly')->with(['error' => "Tidak bisa menghapus, task ini ada di pengajuan request task"]);
                    }
                }

                $idTaskReplaces = explode(',', $requested->todo_replace);
                foreach ($idTaskReplaces as $idTaskReplace) {
                    if ($request->id == $idTaskReplace && $requested->status == 'PENDING') {
                        return redirect('weekly')->with(['error' => "Tidak bisa menghapus, task ini ada di pengajuan request task"]);
                    }
                }
            }

            $monday = ConvertDate::getMondayOrSaturday($weekly->year, $weekly->week, true);
            if (auth()->user()->area_id == 2 && now() > $monday->addDay(1)->addHour(10)) {
                return redirect('weekly')->with(['error' => "Tidak bisa menghapus weekly sudah lebih dari hari selasa jam 10:00"]);
            } else if (auth()->user()->area_id != 2 && now() > $monday->addHour(17)) {
                return redirect('weekly')->with(['error' => "Tidak bisa menghapus weekly sudah lebih dari hari senin jam 17:00"]);
            }

            $weekly->delete();
            return redirect('weekly')->with(['success' => "Berhasil menghapus weekly"]);
        } catch (Exception $e) {
            return redirect('weekly')->with(['error' => $e->getMessage()]);
        }
    }

    public function getweekly(Request $request)
    {
        $weeeklys =  Weekly::with('user', 'user.area', 'user.divisi')
            ->where('year', $request->year)
            ->where('week', $request->week)
            ->where('user_id', $request->id)
            ->where('is_add', 0)
            ->where('is_update', 0)
            ->get();
        return response()->json($weeeklys);
    }
}
