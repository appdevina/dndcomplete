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
                        <div class="card-header bg-white">
                            <h3 class="card-title"><strong>KPI</strong></h3>
                            <div class="card-tools d-flex align-items-center">
                                <div class="input-group input-group-sm mr-3" style="width: 430px;">
                                    <form action="kpi" class="d-inline-flex">
                                        <select class="custom-select col-lg-12 mx-2" name="position_id" id="position_id">
                                            <option value="">--Choose Position--</option>
                                            @foreach ($positions as $post)
                                                <option value="{{ $post->id }}">{{ $post->name }}</option>
                                            @endforeach
                                        </select>
                                        <div class="input-group-append">
                                            <button type="submit" class="btn btn-default">
                                                <i class="fas fa-search"></i>
                                            </button>
                                        </div>
                                    </form>
                                </div>
                                <!-- KPI GABISA IMPORT DONG -->
                                <!-- <div class="input-group input-group-sm mr-3" style="width: 30px;">
                                    <a href="" data-toggle="modal" data-target="#importKpi" data-toggle="tooltip" data-placement="top" title="Upload KPI" class="btn btn-tool btn-sm">
                                        <i class="fa fa-upload"></i>
                                    </a>
                                </div> -->
                                <div class="input-group input-group-sm" style="width: 30px;">
                                    <a href="/kpi/create" data-toggle="tooltip" data-placement="top" title="Add KPI" class="btn btn-tool btn-sm">
                                        <i class="fas fa-plus"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                        <!-- /.card-header -->
                        <div class="card-body table-responsive p-0" style="height: 500px;">
                            <table class="table table-striped table-hover table-head-fixed text-nowrap">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Position</th>
                                        <th>User</th>
                                        <th>Category</th>
                                        <th>Type</th>
                                        <th>Date</th>
                                        <th>Percentage</th>
                                        <th style="text-align: right;">More</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($kpis as $kpi)
                                        <tr onclick="window.location='/kpi/{{ $kpi->id }}/show';" style="cursor: pointer;">
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ $kpi->user->position->name ?? '-' }}</td>
                                            <td>{{ $kpi->user->nama_lengkap }}</td>
                                            <td>{{ $kpi->kpi_category->name }}</td>
                                            <td>{{ $kpi->kpi_type->name }}</td>
                                            <td>
                                                @if ($kpi->kpi_type->name === 'DAILY')
                                                    {{ Carbon\Carbon::parse($kpi->date)->format('d M Y') }}
                                                @elseif ($kpi->kpi_type->name === 'WEEKLY')
                                                    {{-- Format date as yearly week format (e.g., 2023-W26) --}}
                                                    {{ Carbon\Carbon::parse($kpi->date)->format('\WW Y') }}
                                                @elseif ($kpi->kpi_type->name === 'MONTHLY')
                                                    {{ Carbon\Carbon::parse($kpi->date)->format('F Y') }}
                                                @else
                                                    {{-- Default date format --}}
                                                    {{ Carbon\Carbon::parse($kpi->date)->format('d M Y') }}
                                                @endif
                                            </td>
                                            <td>{{ $kpi->percentage }}%</td>
                                            <td style="text-align: right;">
                                                <!-- <a href="/kpi/{{ $kpi->id }}/edit" style="color: orange;">
                                                    <span><i class="fas fa-edit"></i></span>
                                                </a> -->
                                                @if (auth()->user()->role_id == 1)
                                                    <a href="/kpi/{{ $kpi->id }}/delete" style="color: red;" onclick="return confirm('Sure to delete data ?')">
                                                        <span><i class="fas fa-trash"></i></span>
                                                    </a>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <!-- /.card-body -->
                    </div>
                    <!-- /.card -->
                </div>
            </div>
        </div>
    </section>
    <!-- /.content -->
    <!-- /.content-wrapper -->
    <!-- Modal Upload -->
    <div class="modal fade" id="importKpi" tabindex="-1" role="dialog" aria-labelledby="importKpiLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form method="POST" action="/kpi/import" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title" id="importKpiLabel">Import KPI</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3 col-lg-12">
                            <label for="kpi_type_id" class="form-label">KPI Type</label>
                            <select class="form-control" id="kpi_type_id" name="kpi_type_id" required onchange="toggleOption()">
                                <option selected disabled>-- Choose Type --</option>
                                @foreach ($kpitypes as $kpit)
                                    <option value="{{ $kpit->id }}">
                                        {{ $kpit->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3 col-lg-12">
                            <label for="kpi_category_id" class="form-label">KPI Category</label>
                            <select class="form-control" id="kpi_category_id" name="kpi_category_id" required onchange="toggleOption()">
                                <option selected disabled>-- Choose Category --</option>
                                @foreach ($kpicategories as $kpic)
                                    <option value="{{ $kpic->id }}">
                                        {{ $kpic->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3 col-lg-12">
                            <label for="position_id" class="form-label">Job Position</label>
                            <select class="form-control" id="position_id" name="position_id" required onchange="toggleOption()">
                                <option selected disabled>-- Choose Job Position --</option>
                                @foreach ($positions as $post)
                                    <option value="{{ $post->id }}">
                                        {{ $post->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3 col-lg-12">
                            <label for="date" class="form-label">Date</label>
                            <input type="date" class="form-control" id="date" name="date" required>
                        </div>
                        <div class="mb-3 col-lg-12">
                            <label for="percentage" class="form-label">Percentage %</label>
                            <input type="percentage" class="form-control" name="percentage" required>
                        </div>
                        <div class="col-12 mb-3">
                            <label for="formFile" class="form-label">Pilih File</label>
                            <input class="form-control" type="file" id="formFile" name="file">
                        </div>
                        <br>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-info">Import</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
