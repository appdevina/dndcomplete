@extends('layout.main_tamplate')

@section('content')
    <section class="content-header">
        <div class="container-fluid">
            <div class="row">
                <div class="col-lg-12">
                    @if ($message = Session::get('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close" style="color: white;"><span aria-hidden="true">&times;</span></button>
                            <strong>{{ $message }}</strong>
                        </div>
                    @endif
                    @if ($message = Session::get('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close" style="color: white;"><span aria-hidden="true">&times;</span></button>
                            <strong>{{ $message }}</strong>
                        </div>
                    @endif
                    <div class="card">
                        <div class="card-header p-2">
                            <div class="row d-inline-flex ml-2">
                                <ul class="nav nav-pills">
                                    <li class="nav-item"><a class="nav-link" href="/dash-kpi" style="color: #917FB3;">Dashboard</a></li>
                                    <!-- <li class="nav-item"><a class="nav-link" href="/dash-daily">Daily</a></li>
                                    <li class="nav-item"><a class="nav-link" href="/dash-weekly">Weekly</a></li> -->
                                    <li class="nav-item"><a class="nav-link active" href="/dash-monthly" style="background-color: #917FB3;">KPI</a></li>
                                </ul>
                            </div>
                            <div class="card-tools d-flex align-items-center">
                                <div class="input-group input-group-sm mr-3 mt-1" style="width: {{ auth()->user()->role_id != 2 ? '450px' : '320px' }};">
                                    <form action="/dash-monthly" class="d-inline-flex">
                                        @if (auth()->user()->role_id != 2)
                                            <select class="custom-select col-lg-5 mx-2" name="user_id" id="user_id">
                                                <option value="">--Choose User--</option>
                                                @foreach ($users as $user)
                                                    <option value="{{ $user->id }}">{{ $user->nama_lengkap }}</option>
                                                @endforeach
                                            </select>
                                        @endif
                                        <input type="text" id="monthpicker" name="month" class="form-control {{ auth()->user()->role_id != 2 ? 'col-lg-4' : 'col-lg-12' }} mr-1" placeholder="Choose Month">
                                        <div class="input-group-append">
                                            <button type="submit" class="btn btn-default">
                                                <i class="fas fa-search"></i>
                                            </button>
                                        </div>
                                    </form>
                                </div>
                                @if (auth()->user()->role_id != 2)
                                <div class="input-group input-group-sm mr-3 mt-1" style="width: 30px;">
                                    <a href="" data-toggle="modal" data-target="#exportKpi" data-toggle="tooltip" data-placement="top" title="Download Report" class="btn btn-tool btn-sm">
                                        <i class="fa fa-download"></i>
                                    </a>
                                </div>
                                @endif
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="tab-content">
                                <div>
                                    <div class="timeline timeline-inverse">
                                        @foreach ($groupedKpis as $yearMonth => $groupedKpisByCategory)
                                            @php
                                                // Format the yearMonth as desired, e.g., 'January 2023'
                                                $yearMonthText = \Carbon\Carbon::parse($yearMonth)->format('F Y');
                                            @endphp

                                            @php
                                                $userKpi = $groupedKpisByCategory->first()->first();
                                            @endphp

                                            <div class="time-label">
                                                @if ($userKpi)
                                                    <span style="background-color: #2A2F4F; color: white;">{{ $userKpi->user->nama_lengkap }} - {{ $userKpi->user->position->name ?? '-' }}</span>
                                                @else
                                                    <span style="background-color: #2A2F4F; color: white;">No KPIs available</span>
                                                @endif
                                            </div>

                                            {{-- Time label for each month --}}
                                            <div class="time-label">
                                                <span style="background-color: #2A2F4F; color: white;">{{ $yearMonthText }}</span>
                                            </div>

                                            {{-- KPIs by Category --}}
                                            @foreach ($groupedKpisByCategory as $categoryName => $kpis)
                                                <div>
                                                    <!-- <i class="fas fa-check bg-success"></i> -->
                                                    <div class="timeline-item">
                                                        <span class="time">Percentage: {{ $kpis->first()->percentage }}%</span>
                                                        <h3 class="timeline-header"><strong>{{ $categoryName }}</strong></h3>
                                                        <div class="table-responsive">
                                                            <table class="table">
                                                                <thead>
                                                                    <tr>
                                                                        <th style="width: 2%;"></th>
                                                                        <th>KPI Description</th>
                                                                        <th class="text-center" style="width: 10%;">Type</th>
                                                                        <th class="text-center" style="width: 10%;">Value Plan</th>
                                                                        <th class="text-center" style="width: 10%;">Value Actual</th>
                                                                        <th class="text-center" style="width: 10%;">Value Result</th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody>
                                                                    @foreach ($kpis as $kpi)
                                                                        @foreach ($kpi->kpi_detail as $kpiDetail)
                                                                            <tr>
                                                                                <td>
                                                                                    @if ($kpiDetail->count_type === 'NON')
                                                                                        <form action="/dash/change" method="POST" id="nonCountForm">
                                                                                            @csrf
                                                                                            <input type="hidden" name="id" value="{{ $kpiDetail->id }}">
                                                                                            <input type="hidden" name="type" value="monthly">
                                                                                            <button type="submit" class="btn fas fa-check-circle" style="color: {{ $kpiDetail->value_result != null ? 'green' : 'red' }};"></button>
                                                                                        </form>
                                                                                    @elseif ($kpiDetail->count_type === 'RESULT')
                                                                                        <button type="button" class="btn fas fa-check-circle" style="color: {{ $kpiDetail->value_result != null ? 'green' : 'red' }};" data-toggle="modal" data-target="#changeStatus{{ $kpiDetail->id }}"></button>
                                                                                        <!-- Modal Edit -->
                                                                                        <div class="modal fade" id="changeStatus{{ $kpiDetail->id }}" tabindex="-1" role="dialog" aria-labelledby="changeStatusLabel" aria-hidden="true">
                                                                                            <div class="modal-dialog modal-lg" role="document">
                                                                                                <div class="modal-content">
                                                                                                    <div class="modal-header">
                                                                                                        <h5 class="modal-title" id="changeStatusLabel">Update KPI</h5>
                                                                                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                                                            <span aria-hidden="true">&times;</span>
                                                                                                        </button>
                                                                                                    </div>
                                                                                                    <div class="modal-body">
                                                                                                        <form action="/dash/change" method="POST">
                                                                                                            @csrf
                                                                                                            <input type="hidden" name="id" value="{{ $kpiDetail->id }}">
                                                                                                            <input type="hidden" name="type" value="monthly">
                                                                                                            <div class="row">
                                                                                                                <div class="mb-3 col-lg-6">
                                                                                                                    <label for="kpi_description_id" class="form-label">KPI Desc</label>
                                                                                                                    <input type="text" class="form-control" id="kpi_description_id" name="kpi_description_id"
                                                                                                                        value="{{ $kpiDetail->kpi_description->description }}" disabled>
                                                                                                                </div>
                                                                                                                <div class="mb-3 col-lg-6">
                                                                                                                    <label for="count_type" class="form-label">Tipe</label>
                                                                                                                    <input type="text" class="form-control" id="count_type" name="count_type"
                                                                                                                        value="{{ $kpiDetail->count_type }}" disabled>
                                                                                                                </div>
                                                                                                            </div>
                                                                                                            <div class="row">
                                                                                                                <div class="mb-3 col-lg-6">
                                                                                                                    <label for="week" class="form-label">Month</label>
                                                                                                                    <input type="text" class="form-control" id="week" name="week"
                                                                                                                        value="{{ Carbon\Carbon::parse($kpi->date)->format('F Y') }}" readonly>
                                                                                                                </div>
                                                                                                                <div class="mb-3 col-lg-6">
                                                                                                                    <label for="value_plan" class="form-label">Value Plan</label>
                                                                                                                    <input type="text" class="form-control" name="value_plan"
                                                                                                                        value="{{ number_format($kpiDetail->value_plan, 0, ',', '.') }}" readonly>
                                                                                                                </div>
                                                                                                            </div>
                                                                                                            <div class="row">
                                                                                                                <div class="mb-3 col-lg-6">
                                                                                                                    <label for="value_actual" class="form-label">Value Actual</label>
                                                                                                                    <input type="number" class="form-control" id="value_actual" name="value_actual"
                                                                                                                        value="0" required>
                                                                                                                </div>
                                                                                                            </div>
                                                                                                            <button type="submit" class="btn btn-success mt-3">Submit</button>
                                                                                                        </form>
                                                                                                    </div>
                                                                                                    <div class="modal-footer">
                                                                                                    </div>
                                                                                                </div>
                                                                                            </div>
                                                                                        </div>
                                                                                    @endif
                                                                                </td>
                                                                                <td>{{ $kpiDetail->kpi_description->description }}</td>
                                                                                <td class="text-center">{{ $kpiDetail->count_type }}</td>
                                                                                <td class="text-center">{{ $kpiDetail->value_plan }}</td>
                                                                                <td class="text-center">{{ $kpiDetail->value_actual }}</td>
                                                                                @if ($kpiDetail->count_type === 'NON')
                                                                                <td class="text-center">{{ $kpiDetail->value_result == 1 ? '100%' : $kpiDetail->value_result . '%' }}</td>
                                                                                @elseif ($kpiDetail->count_type === 'RESULT')
                                                                                <td class="text-center">{{ $kpiDetail->value_result * 100 }}%</td>
                                                                                @endif
                                                                            </tr>
                                                                        @endforeach
                                                                    @endforeach
                                                                </tbody>
                                                            </table>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        @endforeach
                                        {{-- Placeholder for the gray icon --}}
                                        <div>
                                            <i class="fas fa-exclamation-circle" style="background-color: #917FB3; color: white;"></i>
                                        </div>

                                        <!-- RESULTS -->
                                        @foreach ($groupedKpis as $yearMonth => $groupedKpisByCategory)
                                            {{-- KPIs by Category --}}
                                            <div>
                                                <div class="timeline-item">
                                                    <h3 class="timeline-header"><strong>YOUR RESULTS !</strong></h3>
                                                    <div class="table-responsive">
                                                        <table class="table">
                                                            <thead>
                                                                <tr>
                                                                    <th>KPI Category</th>
                                                                    <th class="text-center" style="width: 10%;">Percentage</th>
                                                                    <th class="text-center" style="width: 10%;">Actual</th>
                                                                    <th class="text-center" style="width: 10%;">Score</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                @foreach ($groupedKpisByCategory as $categoryName => $kpis)
                                                                    @foreach ($kpis as $kpi)
                                                                        <tr>
                                                                            <td>{{ $kpi->kpi_category->name }}</td>
                                                                            <td class="text-center">{{ $kpi->percentage }}%</td>
                                                                             <td class="text-center">{{ $kpi->actualCount * 100 }}%</td>
                                                                            <td class="text-center">{{ $kpi->score * 100 }}%</td>
                                                                        </tr>
                                                                    @endforeach
                                                                @endforeach
                                                            </tbody>
                                                            <tfoot>
                                                                <tr>
                                                                <td><strong>FINAL SCORE</strong></td>
                                                                <td></td>
                                                                <td></td>
                                                                <td class="text-center"><strong>{{ $totalScore * 100 }}%</strong></td>
                                                                </tr>
                                                            </tfoot>
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- /.card -->
                </div>
            </div>
        </div>
    </section>
    <!-- /.content -->
    <!-- /.content-wrapper -->
    <!-- Modal Download -->
    <div class="modal fade" id="exportKpi" tabindex="-1" role="dialog" aria-labelledby="exportKpiLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form method="GET" action="/kpi/exportMonthly">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title" id="exportKpiLabel">Download KPI</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                    @if (auth()->user()->role_id == 1)
                        <div class="mb-3 col-lg-12">
                            <label for="divisi_id" class="form-label">Division</label>
                            <select class="custom-select" name="divisi_id" id="divisi_id">
                                <option value="">--Choose Division--</option>
                                @foreach ($divisions as $divisi)
                                    <option value="{{ $divisi->id }}">{{ $divisi->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    @endif
                        <div class="mb-3 col-lg-12">
                            <label for="date" class="form-label">Date</label>
                            <input class="form-control" id="exportMonthly" name="date" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn" style="background-color: #917FB3; color: white;">EXPORT</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
