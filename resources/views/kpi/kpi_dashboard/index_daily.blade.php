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
                                    <li class="nav-item"><a class="nav-link active" href="/dash-daily">Daily</a></li>
                                    <li class="nav-item"><a class="nav-link" href="/dash-weekly">Weekly</a></li>
                                    <li class="nav-item"><a class="nav-link" href="/dash-monthly">Monthly</a></li>
                                </ul>
                            </div>
                            <div class="card-tools">
                                <div class="input-group input-group-sm mr-3 mt-1" style="width: 220px;">
                                    <form action="/dash-daily" class="d-inline-flex">
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
                                        @foreach ($groupedKpis as $date => $groupedKpisByCategory)
                                            @php
                                                $dateText = Carbon\Carbon::parse($date)->format('d M Y');
                                            @endphp

                                            {{-- Time label --}}
                                            <div class="time-label">
                                                <span class="bg-warning">{{ $dateText }}</span>
                                            </div>

                                            {{-- KPIs by Category --}}
                                            @foreach ($groupedKpisByCategory as $categoryName => $kpis)
                                                <div>
                                                    <!-- <i class="fas fa-check bg-success"></i> -->
                                                    <div class="timeline-item">
                                                        <h3 class="timeline-header"><strong>{{ $categoryName }}</strong></h3>
                                                        @foreach ($kpis as $kpi)
                                                            @foreach ($kpi->kpi_detail as $kpiDetail)
                                                                <div class="timeline-body">
                                                                    <div class="row align-items-center">
                                                                        <form action="/dash/change" method="POST">
                                                                            @csrf
                                                                            <input type="hidden" name="id" value="{{ $kpiDetail->id }}">
                                                                            <input type="hidden" name="type" value="daily">
                                                                            <button type="submit" class="btn far fa-check-circle"
                                                                                style="color: {{ $kpiDetail->value_result != null ? 'green' : 'grey' }};"></button>
                                                                        </form>
                                                                        {{ $kpiDetail->kpi_description->description }}
                                                                    </div>
                                                                </div>
                                                            @endforeach
                                                        @endforeach
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
