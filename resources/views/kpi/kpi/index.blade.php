@extends('layout.main_tamplate')

@section('content')
    <section class="content-header">
        <div class="container-fluid">
            <div class="row">
                <div class="col-lg-12">
                    @if ($message = Session::get('success'))
                        <div class="alert alert-info alert-dismissible fade show" role="alert">
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
                                <div class="input-group input-group-sm mr-3" style="width: 350px;">
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
                                <div class="input-group input-group-sm" style="width: 50px;">
                                    <a href="/kpi/create" data-toggle="tooltip" data-placement="top" title="Add KPI" class="btn btn-tool btn-sm">
                                        <i class="fas fa-plus"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                        <!-- /.card-header -->
                        <div class="card-body table-responsive p-0" style="height: 500px;">
                            <table class="table table-striped table-head-fixed text-nowrap">
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
                                                <a href="/kpi/{{ $kpi->id }}/delete" style="color: red;" onclick="return confirm('Sure to delete data ?')">
                                                    <span><i class="fas fa-trash"></i></span>
                                                </a>
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
@endsection
