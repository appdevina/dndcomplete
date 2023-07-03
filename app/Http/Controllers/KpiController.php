<?php

namespace App\Http\Controllers;

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

class KpiController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $kpis = Kpi::all();

        if ($request->position_id) {
            $kpis = Kpi::where('position_id', $request->position_id)->get();
        }

        return view('kpi.kpi.index', [
            'title' => 'KPI',
            'active' => 'kpi',
            'kpis' => $kpis,
            'positions' => Position::all(),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $kpidescs = KpiDescription::all();

        return view('kpi.kpi.create', [
            'title' => 'KPI',
            'active' => 'kpi',
            'kpicategories' => KpiCategory::all(),
            'kpitypes' => KpiType::all(),
            'kpidescs' => $kpidescs,
            'positions' => Position::all(),
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
            $users = User::where('position_id', $request->position_id)->get();

            foreach ($users as $user) {
                $kpi = Kpi::create([
                    'user_id' => $user->id,
                    'kpi_type_id' => $request->kpi_type_id,
                    'kpi_category_id' => $request->kpi_category_id,
                    'date' => Carbon::parse($request->date)->format('Y-m-d'),
                    'percentage' => $request->percentage,
                ]);
    
                for ($i = 0; $i < count($request->get('kpis')); $i++) {
                    $temp = array();
                    $temp['kpi_id'] = $kpi->id;
                    $temp['kpi_description_id'] = $request->get('kpis')[$i];
                    $temp['count_type'] = $request->get('count_type')[$i];
                    $temp['value_plan'] = $request->get('value_plan')[$i] ?? null;
    
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
            $kpi->date = Carbon::createFromFormat('d/m/Y', $request->date)->format('Y-m-d');
            $kpi->percentage = $request->percentage;
            $kpi->save();

            // Get the existing KpiDetail records associated with the current Kpi
            $existingKpiDetails = KpiDetail::where('kpi_id', $kpi->id)->get();

            // Loop through the new request data
            for ($i = 0; $i < count($request->get('kpis')); $i++) {
                $kpiDetailData = [
                    'kpi_id' => $kpi->id,
                    'kpi_description_id' => $request->get('kpis')[$i],
                    'count_type' => $request->get('count_type')[$i],
                    'value_plan' => $request->get('value_plan')[$i] ?? null,
                ];

                // Check if the KpiDetail with the given kpi_description_id already exists
                $existingKpiDetail = $existingKpiDetails->where('kpi_description_id', $kpiDetailData['kpi_description_id'])->first();

                if ($existingKpiDetail) {
                    // If it exists, update the existing record with new data
                    $existingKpiDetail->update($kpiDetailData);
                } else {
                    // If it doesn't exist, create a new KpiDetail record
                    KpiDetail::create($kpiDetailData);
                }
            }

            // Delete any KpiDetail records that are missing in the new request data
            $existingKpiDetails->whereNotIn('kpi_description_id', $request->get('kpis'))->each(function ($kpiDetail) {
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
}
