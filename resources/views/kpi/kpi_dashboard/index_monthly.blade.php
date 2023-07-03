@extends('layout.main_tamplate')

@section('content')
    <section class="content-header">
        <div class="container-fluid">
            <div class="row">
                <div class="col-lg-12">
                    @if ($message = Session::get('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <strong>{{ $message }}</strong>
                        </div>
                    @endif
                    @if ($message = Session::get('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <strong>{{ $message }}</strong>
                        </div>
                    @endif
                    <div class="card">
                        <div class="card-header p-2">
                            <div class="row d-inline-flex ml-2">
                                <ul class="nav nav-pills">
                                    <li class="nav-item"><a class="nav-link" href="/dash-daily">Daily</a></li>
                                    <li class="nav-item"><a class="nav-link" href="/dash-weekly">Weekly</a></li>
                                    <li class="nav-item"><a class="nav-link active" href="/dash-monthly">Monthly</a></li>
                                </ul>
                            </div>
                            <div class="card-tools">
                                <div class="input-group input-group-sm mr-3 mt-1" style="width: 220px;">
                                    <form action="/dash-monthly" class="d-inline-flex">
                                        <input type="text" id="monthpicker" name="month" class="form-control" placeholder="Choose Month">
                                        <div class="input-group-append">
                                            <button type="submit" class="btn btn-default">
                                                <i class="fas fa-search"></i>
                                            </button>
                                        </div>
                                    </form>
                                </div>
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

                                            {{-- Time label for each month --}}
                                            <div class="time-label">
                                                <span class="bg-warning">{{ $yearMonthText }}</span>
                                            </div>

                                            {{-- KPIs by Category --}}
                                            @foreach ($groupedKpisByCategory as $categoryName => $kpis)
                                                <div>
                                                    <!-- <i class="fas fa-check bg-success"></i> -->
                                                    <div class="timeline-item">
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
                                                                                    <form action="/dash/change" method="POST">
                                                                                        @csrf
                                                                                        <input type="hidden" name="id" value="{{ $kpiDetail->id }}">
                                                                                        <input type="hidden" name="type" value="monthly">
                                                                                        <button type="submit" class="btn far fa-check-circle"
                                                                                            style="color: {{ $kpiDetail->value_result != null ? 'green' : 'grey' }};"></button>
                                                                                    </form>
                                                                                </td>
                                                                                <td>{{ $kpiDetail->kpi_description->description }}</td>
                                                                                <td class="text-center">{{ $kpiDetail->count_type }}</td>
                                                                                <td class="text-center">{{ $kpiDetail->value_plan }}</td>
                                                                                <td class="text-center">{{ $kpiDetail->value_actual }}</td>
                                                                                <td class="text-center">{{ $kpiDetail->value_result }}</td>
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
                                            <i class="far fa-clock bg-gray"></i>
                                        </div>

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
@endsection
