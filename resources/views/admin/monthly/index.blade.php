@extends('layout.main_tamplate')

@section('content')
    <section class="content-header">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header bg-dark">
                            <div class="row d-inline-flex">
                                <h3 class="card-title">Monthly</h3>
                                @if (auth()->user()->role_id == 1)
                                    <a href="#">
                                        <button class="badge bg-success mx-3 elevation-0" data-toggle="modal"
                                            data-target="#exportMonthly">EXPORT</button>
                                    </a>
                                    <a href="#">
                                        <button class="badge bg-primary mx-3 elevation-0" data-toggle="modal"
                                            data-target="#reportMonthly">REPORT</button>
                                    </a>
                                @else
                                    <a href="/monthly/template">
                                        <button class="badge bg-primary mx-3 elevation-0">TEMPLATE IMPORT</button>
                                    </a>
                                    <a href="#">
                                        <button class="badge bg-warning mx-3 elevation-0" data-toggle="modal"
                                            data-target="#importMonthly">IMPORT</button>
                                    </a>
                                    <a href="#">
                                        <button class="badge bg-success mx-3 elevation-0" data-toggle="modal"
                                            data-target="#addMonthly">+ ADD</button>
                                    </a>
                                @endif
                            </div>
                            @if (auth()->user()->role_id == 1)
                                <div class="card-tools d-flex">
                                    <div class="input-group input-group-sm mr-3" style="width: 400px;">
                                        <form action="/admin/monthly" class="d-inline-flex">
                                            <select class="custom-select col-lg-12 mx-2" name="divisi_id" id="divisi_id"
                                                required>
                                                <option value="">--Choose Divisi--</option>
                                                @foreach ($divisis as $divisi)
                                                    <option value="{{ $divisi->id }}">{{ $divisi->name }}</option>
                                                @endforeach
                                            </select>
                                            <div class="input-group-append">
                                                <button type="submit" class="btn btn-default">
                                                    <i class="fas fa-search"></i>
                                                </button>
                                            </div>
                                        </form>
                                    </div>
                                    <div class="input-group input-group-sm" style="width: 400px;">
                                        <form action="/admin/monthly" class="d-inline-flex">
                                            <input type="month" name="month" class="form-control mr-3 float-right" required>
                                            <input type="text" name="name" class="form-control mr-3 float-right"
                                                placeholder="Name" required>
                                            <div class="input-group-append">
                                                <button type="submit" class="btn btn-default">
                                                    <i class="fas fa-search"></i>
                                                </button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            @else
                                <div class="card-tools d-flex">
                                    <div class="input-group input-group-sm mr-3" style="width: 220px;">
                                        <form action="/monthly" class="d-inline-flex">
                                            <select class="custom-select col-lg-10 mx-2" name="tasktype" id="tasktype"
                                                required>
                                                <option value="">--Choose One--</option>
                                                <option value="1">This Month</option>
                                                <option value="2">Last Month</option>
                                                <option value="3">All</option>
                                            </select>
                                            <div class="input-group-append">
                                                <button type="submit" class="btn btn-default">
                                                    <i class="fas fa-search"></i>
                                                </button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            @endif
                        </div>
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
                        <!-- /.card-header -->
                        <div class="card-body table-responsive p-0" style="height: 500px;">
                            <table class="table table-head-fixed text-nowrap">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        @if (auth()->user()->role_id == 1)
                                            <th>User</th>
                                        @else
                                            <th>Action</th>
                                        @endif
                                        <th>Month</th>
                                        <th>Task</th>
                                        <th>Type</th>
                                        <th>Plan Result</th>
                                        <th>Value Result</th>
                                        <!-- <th>Value</th> -->
                                        <th>Status</th>
                                        <th>Task Plan</th>
                                        <th>Tagged By</th>
                                        <th>Added By</th>
                                        <th>Change Task</th>
                                        {{-- @if (auth()->user()->role_id == 1)
                                            <th>Action</th>
                                        @endif --}}
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($monthlys as $monthly)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            @if (auth()->user()->role_id == 1)
                                                <td>{{ $monthly->user->nama_lengkap }}</td>
                                            @else
                                                <td class="d-flex" style="text-align: center;">
                                                    @if ($monthly->tipe == 'NON')
                                                        <form action="/monthly/change" method="POST">
                                                            @csrf
                                                            <input type="hidden" name="id" value="{{ $monthly->id }}">
                                                            <input type="hidden" name="tasktype"
                                                                value="{{ app('request')->input('tasktype') }}">
                                                            <button type="submit" class="btn far fa-check-circle"
                                                                style="color: {{ $monthly->value ? 'green' : 'grey' }};"></button>
                                                        </form>
                                                    @else
                                                        <a href="/monthly/change/result/{{ $monthly->id }}"><i
                                                                class="btn far fa-check-circle"
                                                                style="color: {{ $monthly->value ? 'green' : 'grey' }};"></i></a>
                                                    @endif
                                                    <a href="/monthly/edit/{{ $monthly->id }}"><i class="btn fas fa-edit"
                                                            style="color: rgb(239, 239, 54)"></i></a>
                                                    <form action="/monthly/delete" method="POST">
                                                        @csrf
                                                        <input type="hidden" name="id" value="{{ $monthly->id }}">
                                                        <button type="submit" class="btn"
                                                            style="color: rgb(204, 26, 26);"><i
                                                                class="fas fa-trash"></i></button>

                                                    </form>
                                                </td>
                                            @endif
                                            <td>{{ date('M-y', $monthly->date / 1000) }}</td>
                                            <td>{{ $monthly->task }}</td>
                                            <td>{{ $monthly->tipe }}</td>
                                            <td>{{ $monthly->value_plan ? number_format($monthly->value_plan, 0, ',', '.') : '-' }}
                                            </td>
                                            <td>{{ $monthly->value_actual ? number_format($monthly->value_actual, 0, ',', '.') : '-' }}
                                            </td>
                                            <!-- <td>{{ $monthly->value ? number_format($monthly->value, 0, ',', '.') : '-' }}</td> -->
                                            <td>{{ $monthly->value ? 'CLOSED' : 'OPEN' }}</td>
                                            <td>{{ !$monthly->is_add ? 'Plan' : 'Extra Task' }}</td>
                                            <td>
                                                @if ($monthly->tag_id)
                                                    {{ $monthly->tag->nama_lengkap }}
                                                @else
                                                    -
                                                @endif
                                            </td>
                                            <td>
                                                @if ($monthly->add_id)
                                                    {{ $monthly->add->nama_lengkap }}
                                                @else
                                                    -
                                                @endif
                                            </td>
                                            <td>
                                                @if ($monthly->isupdate)
                                                    <i class="far fa-check-circle" style="color: green;"></i>
                                                @else
                                                    <i class="far fa-times-circle" style="color: red;"></i>
                                                @endif
                                            </td>
                                            {{-- @if (auth()->user()->role_id == 1)
                                                <td>
                                                    <a href="/monthly/{{ $monthly->id }}"
                                                        class="badge bg-warning"><span><i
                                                                class="fas fa-edit"></i></span></a>
                                                </td>
                                            @endif --}}
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
            @if (count($monthlys) == 100)
                    <div class="d-flex justify-content-center">
                        {{ $monthlys->links() }}
                    </div>
                @endif
    </section>

    <!-- Modal -->
    <form action={{ auth()->user()->role_id == 1 ? '/admin/monthly/import' : '/monthly/import' }} method="POST"
        enctype="multipart/form-data">
        @csrf
        <div class="modal fade" id="importMonthly" tabindex="-1" aria-labelledby="importMonthlyLabel"
            aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="importMonthlyLabel">Import Outlet</h5>
                    </div>
                    <div class="modal-body">
                        <div class="col-12 mt-3">
                            <label for="formFile" class="form-label">Pilih File</label>
                            <input class="form-control" type="file" id="formFile" name="file">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Import</button>
                    </div>
                </div>
            </div>
        </div>
    </form>

    <!-- Modal -->
    <form action="/monthly" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="modal fade" id="addMonthly" tabindex="-1" aria-labelledby="addMonthlytLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="addMonthlytLabel">Add Monthly</h5>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3 col-lg-12 ml-4">
                            <input type="checkbox" class="form-check-input" id="extraTaskMonthly" name="is_add">
                            <label class="form-check-label" for="extraTaskMonthly">Extra Task</label>
                        </div>
                        <div class="mb-3 col-lg-12" id="addMonthlyWeek">
                            <label for="date" class="form-label">Month</label>
                            <input type="month" class="form-control" id="date" name="date"
                                value="{{ now()->format('Y-m') }}">
                        </div>
                        <div class="mb-3 col-lg-12">
                            <label for="task" class="form-label">Task</label>
                            <input type="text" class="form-control" id="task" name="task" autocomplete="off" required>
                        </div>
                        {{-- @if (auth()->user()->mr) --}}
                        <div class="mb-3 col-lg-12 ml-4 d-flex">
                            <input type="checkbox" class="form-check-input" id="resultMonthly" name="result">
                            <label class="form-check-label" for="resultMonthly">Result ?</label>
                            <div class="col-md-8">
                                <input type="number" class="form-control ml-4" id="value_plan" name="value_plan"
                                    autocomplete="off">
                                <span class="ml-4" id="nominal"></span>
                            </div>
                        </div>
                        {{-- @endif --}}
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">+ Add</button>
                    </div>
                </div>
            </div>
        </div>
    </form>

    <!-- Modal -->
    <form action="/admin/monthly/export" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="modal fade" id="exportMonthly" tabindex="-1" aria-labelledby="exportMonthlyLabel"
            aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exportMonthlyLabel">Export Monthly</h5>
                    </div>
                    <div class="modal-body">
                        <div class="col-12 mt-3">
                            <label for="month" class="form-label">Month</label>
                            <input type="month" class="form-control" id="month" name="month" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Export</button>
                    </div>
                </div>
            </div>
        </div>
    </form>

    <!-- Modal -->
    <form action="/admin/monthly/report" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="modal fade" id="reportMonthly" tabindex="-1" aria-labelledby="reportMonthlyLabel"
            aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="reportMonthlyLabel">Report Monthly</h5>
                    </div>
                    <div class="modal-body">
                        <div class="col-12 mt-3">
                            <label for="month" class="form-label">Month</label>
                            <input type="month" class="form-control" id="month" name="month" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Report</button>
                    </div>
                </div>
            </div>
        </div>
    </form>
@endsection
