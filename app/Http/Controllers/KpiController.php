<?php

namespace App\Http\Controllers;

use App\Exports\KpiMonthlyExport;
use App\Imports\KpiImport;
use App\Models\Kpi;
use App\Models\KpiCategory;
use App\Models\KpiDescription;
use App\Models\KpiDetail;
use App\Models\KpiType;
use App\Models\Position;
use App\Models\User;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class KpiController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if (auth()->user()->role_id != 1) {
            $kpis = Kpi::orderBy('date', 'DESC')
                ->whereHas('user', function ($q) {
                    $q->where('divisi_id', auth()->user()->divisi_id);
                })
                ->get();

            $positions = Position::whereHas('user', function ($q) {
                    $q->where('divisi_id', auth()->user()->divisi_id);
                })
                ->get();
        } else {
            $kpis = Kpi::orderBy('date', 'DESC')
                ->get();

            $positions = Position::all();
        }

        if ($request->position_id) {
            $kpis = Kpi::whereHas('user', function ($q) use ($request) {
                $q->where('position_id', $request->position_id);
            })
            ->orderBy('date', 'DESC')
            ->get();
        }

        return view('kpi.kpi.index', [
            'title' => 'KPI',
            'active' => 'kpi',
            'kpis' => $kpis,
            'positions' => $positions,
            'kpicategories' => KpiCategory::all(),
            'kpitypes' => KpiType::all(),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        // VALIDASI WAKTU
        if (auth()->user()->role_id != 1) {
            $today = Carbon::createFromFormat('d/m/Y', '01/01/2023');
            // $today = Carbon::now();

            if ($today > $today->copy()->setTimezone(env('DEFAULT_TIMEZONE_APP', 'Asia/Jakarta'))->startOfMonth()->addDay(3)) {
                return redirect('kpi')->with(['error' => 'Can not add KPI, now already more than 3 days since start of month !']);
            }
        } 
        
        // KALAU ROLE MANAGER BUAT KPI UNTUK ROLE COORDINATOR SAJA
        if (auth()->user()->role_id == 5) {
            $positions = Position::whereHas('user', function ($q) {
                $q->where('role_id', 4);
            })
            ->get();
        // KALAU ROLE MANAGER BUAT KPI UNTUK ROLE TEAM LEADER & STAFF
        } else if (auth()->user()->role_id == 4) {
            $positions = Position::whereHas('user', function ($q) {
                $q->whereIn('role_id', [3,2]);
            })
            ->get();
        } else {
            $positions = Position::all();
        }

        $kpidescs = KpiDescription::all();

        return view('kpi.kpi.create', [
            'title' => 'KPI',
            'active' => 'kpi',
            'kpicategories' => KpiCategory::all(),
            'kpitypes' => KpiType::all(),
            'kpidescs' => $kpidescs,
            'positions' => $positions,
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
            // dd($request->all());
            $kpiDescriptionsAdm = [];
            $kpiDescriptionsRep = [];
            $kpiDescriptionsMain = [];

            // KPI ADMINISTRATION
            for ($i = 0; $i < count($request->get('kpis')); $i++) {
                $temp = [
                    'kpi_category_id' => $request->kpi_category_id,
                    'description' => $request->get('kpis')[$i],
                ];
                $kpiDescriptionAdm = KpiDescription::create($temp);
                $kpiDescriptionsAdm[$i] = $kpiDescriptionAdm;
            }

            // KPI REPORTING
            for ($i = 0; $i < count($request->get('kpisRep')); $i++) {
                $temp = [
                    'kpi_category_id' => $request->kpi_category_id,
                    'description' => $request->get('kpisRep')[$i],
                ];
                $kpiDescriptionRep = KpiDescription::create($temp);
                $kpiDescriptionsRep[$i] = $kpiDescriptionRep;
            }

            // KPI MAIN JOB
            for ($i = 0; $i < count($request->get('kpisMain')); $i++) {
                $temp = [
                    'kpi_category_id' => $request->kpi_category_id,
                    'description' => $request->get('kpisMain')[$i],
                ];
                $kpiDescriptionMain = KpiDescription::create($temp);
                $kpiDescriptionsMain[$i] = $kpiDescriptionMain;
            }

            $users = User::where('position_id', $request->position_id)->get();

            foreach ($users as $user) {
                // KPI ADMINISTRATION
                $kpiAdm = Kpi::create([
                    'user_id' => $user->id,
                    'kpi_type_id' => $request->kpi_type_id,
                    'kpi_category_id' => 5,
                    'date' => Carbon::createFromFormat('m/Y', $request->date)->format('Y-m-d'),
                    'percentage' => $request->percentageAdm,
                ]);
    
                for ($i = 0; $i < count($request->get('kpis')); $i++) {
                    $temp = [
                        'kpi_id' => $kpiAdm->id,
                        'kpi_description_id' => $kpiDescriptionsAdm[$i]->id,
                        'count_type' => $request->get('count_type')[$i],
                        'value_plan' => $request->get('value_plan')[$i] ?? null,
                        'value_result' => 0,
                    ];
    
                    KpiDetail::create($temp);
                }

                // KPI REPORTING
                $kpiAdm = Kpi::create([
                    'user_id' => $user->id,
                    'kpi_type_id' => $request->kpi_type_id,
                    'kpi_category_id' => 6,
                    'date' => Carbon::createFromFormat('m/Y', $request->date)->format('Y-m-d'),
                    'percentage' => $request->percentageRep,
                ]);
    
                for ($i = 0; $i < count($request->get('kpisRep')); $i++) {
                    $temp = [
                        'kpi_id' => $kpiAdm->id,
                        'kpi_description_id' => $kpiDescriptionsRep[$i]->id,
                        'count_type' => $request->get('count_typeRep')[$i],
                        'value_plan' => $request->get('value_planRep')[$i] ?? null,
                        'value_result' => 0,
                    ];
    
                    KpiDetail::create($temp);
                }

                // KPI MAIN JOB
                $kpiMain = Kpi::create([
                    'user_id' => $user->id,
                    'kpi_type_id' => $request->kpi_type_id,
                    'kpi_category_id' => 7,
                    'date' => Carbon::createFromFormat('m/Y', $request->date)->format('Y-m-d'),
                    'percentage' => $request->percentageMain,
                ]);
    
                for ($i = 0; $i < count($request->get('kpisMain')); $i++) {
                    $temp = [
                        'kpi_id' => $kpiMain->id,
                        'kpi_description_id' => $kpiDescriptionsMain[$i]->id,
                        'count_type' => $request->get('count_typeMain')[$i],
                        'value_plan' => $request->get('value_planMain')[$i] ?? null,
                        'value_result' => 0,
                    ];
    
                    KpiDetail::create($temp);
                }
            }

            return redirect('kpi')->with('success', 'Data Added !');
        } catch (Exception $e) {
            return redirect('kpi')->with(['error' => $e->getMessage()]);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Kpi $kpi)
    {
        return view('kpi.kpi.show', [
            'title' => 'KPI',
            'active' => 'kpi',
            'kpi' => $kpi,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Kpi $kpi)
    {
        $kpidescs = KpiDescription::all();

        return view('kpi.kpi.edit', [
            'title' => 'KPI',
            'active' => 'kpi',
            'kpi' => $kpi,
            'kpicategories' => KpiCategory::all(),
            'kpitypes' => KpiType::all(),
            'kpidescs' => $kpidescs,
            'positions' => Position::all(),
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Kpi $kpi)
    {
        try {
            $kpi->kpi_type_id = $request->kpi_type_id;
            $kpi->kpi_category_id = $request->kpi_category_id;
            $kpi->date = Carbon::createFromFormat('m/Y', $request->date)->format('Y-m-d');
            $kpi->percentage = $request->percentage;
            $kpi->save();

            // Get the existing KpiDetail records associated with the current Kpi
            $existingKpiDetails = KpiDetail::where('kpi_id', $kpi->id)->get();

            // Loop through the new request data
            for ($i = 0; $i < count($request->get('kpis')); $i++) {
                $kpiDescription = $request->get('kpis')[$i];
                $kpiDetailData = [
                    'kpi_id' => $kpi->id,
                    'count_type' => $request->get('count_type')[$i],
                    'value_plan' => $request->get('value_plan')[$i] ?? null,
                    // 'value_result' => $request->get('value_result')[$i] ?? 0,
                ];

                // Check if the KpiDescription with the given description already exists
                $existingKpiDescription = KpiDescription::where('description', $kpiDescription)->first();

                if ($existingKpiDescription) {
                    // If it exists, update the existing record with new data
                    $existingKpiDetail = $existingKpiDetails->where('kpi_description_id', $existingKpiDescription->id)->first();
                    if ($existingKpiDetail) {
                        $existingKpiDetail->update($kpiDetailData);
                    } else {
                        $kpiDetailData['kpi_description_id'] = $existingKpiDescription->id;
                        KpiDetail::create($kpiDetailData);
                    }
                } else {
                    // If it doesn't exist, create a new KpiDescription and KpiDetail record
                    $newKpiDescription = KpiDescription::create([
                        'description' => $kpiDescription,
                        'kpi_category_id' => $request->kpi_category_id,
                    ]);
                    $kpiDetailData['kpi_description_id'] = $newKpiDescription->id;
                    KpiDetail::create($kpiDetailData);
                }
            }

            // Delete any KpiDetail records that are missing in the new request data
            $existingKpiDescriptions = KpiDescription::whereIn('description', $request->get('kpis'))->pluck('id');
            $existingKpiDetails->whereNotIn('kpi_description_id', $existingKpiDescriptions)->each(function ($kpiDetail) {
                $kpiDetail->delete();
            });

            return redirect('kpi/'.$kpi->id.'/show')->with('success', 'Data Updated !');
        } catch (Exception $e) {
            return redirect('kpi/'.$kpi->id.'/show')->with(['error' => $e->getMessage()]);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Kpi $kpi)
    {
        try {
            $kpi->delete($kpi);

            return redirect('kpi')->with('success', 'Successfully Deleted !');
        } catch (Exception $e) {
            return redirect('kpi')->with(['error' => $e->getMessage()]);
        }
    }

    public function getKpiDetail($kpiId)
    {
        $kpi = Kpi::with('kpi_detail', 'kpi_detail.kpi_description')->find($kpiId);

        return response()->json($kpi->kpi_detail);
    }

    public function import(Request $request)
    {
        $file = $request->file('file');
        $namaFile = $file->getClientOriginalName();
        $file->move(public_path('import'), $namaFile);

        try {
            $users = User::where('position_id', $request->position_id)->get();
            $kpiIds = [];

            foreach ($users as $user) {
                $kpi = Kpi::create([
                    'user_id' => $user->id,
                    'kpi_type_id' => $request->kpi_type_id,
                    'kpi_category_id' => $request->kpi_category_id,
                    'date' => Carbon::parse($request->date)->format('Y-m-d'),
                    'percentage' => $request->percentage,
                ]);

                $kpiIds[] = $kpi->id;
            }

            Excel::import(new KpiImport($kpiIds), public_path('/import/' . $namaFile));
        } catch (Exception $e) {
            return redirect('kpi')->with(['error' => $e->getMessage()]);
        }
        return redirect('kpi')->with(['success' => 'Successfully Uploaded !']);
    }

    public function exportMonthly(Request $request){
        
        return Excel::download(new KpiMonthlyExport($request->date), 'KPI_'. auth()->user()->divisi->name . '_' . $request->date . '.xlsx');
    }
}
