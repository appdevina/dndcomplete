@extends('layout.main_tamplate')

@section('content')
    <section class="content-header">
        <!-- Main content -->
        <section class="content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header bg-dark">
                                <div class="row d-inline-flex">
                                    <h3 class="card-title">Team's Weekly</h3>
                                </div>
                                <div class="card-tools d-flex">
                                    <div class="input-group input-group-sm mr-3" style="width: 220px;">
                                        <form action="/teams/weekly" class="d-inline-flex">
                                            <select class="custom-select col-lg-10 mx-2" name="user">
                                                <option value="">-- User --</option>
                                                @foreach ($users as $user)
                                                    <option value="{{ $user->id }}">
                                                        {{ $user->nama_lengkap }}</option>
                                                @endforeach
                                            </select>
                                            <div class="input-group-append">
                                                <button type="submit" class="btn btn-default">
                                                    <i class="fas fa-search"></i>
                                                </button>
                                            </div>
                                        </form>
                                    </div>
                                    <div class="input-group input-group-sm mr-3" style="width: 220px;">
                                        <form action="/teams/weekly" class="d-inline-flex">
                                            <select class="custom-select col-lg-10 mx-2" name="tasktype" id="tasktype">
                                                <option value="">--Choose One--</option>
                                                <option value="1">This Week</option>
                                                <option value="2">Last Week</option>
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
                            <div class="card-body table-responsive table-striped p-0" style="height: 500px;">
                                <table class="table table-head-fixed text-nowrap">
                                    <thead>
                                        <tr>
                                            <th>No</th>
                                            <th>Action</th>
                                            <th>User</th>
                                            <th>Year</th>
                                            <th>Week</th>
                                            <th>Task</th>
                                            <th>Category</th>
                                            <th>Task Status</th>
                                            <th>Type</th>
                                            <th>Plan Result</th>
                                            <th>Actual Result</th>
                                            <th>Status</th>
                                            <th>Task Plan</th>
                                            <th>Change Task</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($weeklys as $weekly)
                                            <tr>
                                                <td>{{ $loop->iteration }}</td>
                                                {{-- <td>
                                                    <a href="/weekly/{{ $weekly->id }}" class="badge bg-warning"><span><i
                                                    class="fas fa-edit"></i></span></a>
                                                </td> --}}
                                                <td class="d-flex" style="text-align: center;">
                                                    <!-- @if ($weekly->tipe == 'NON')
                                                    <form action="/weekly/change" method="POST">
                                                        @csrf
                                                            <input type="hidden" name="id" value="{{ $weekly->id }}">
                                                            <input type="hidden" name="page" value="teams">
                                                            <input type="hidden" name="tasktype" value="{{ app('request')->input('tasktype') }}">
                                                            <button type="submit" class="btn far fa-check-circle"
                                                                style="color: {{ $weekly->value ? 'green' : 'grey' }};"></button>
                                                        </form>
                                                    @else
                                                        <a href="/weekly/change/result/{{ $weekly->id }}"><i
                                                                class="btn far fa-check-circle"
                                                                style="color: {{ $weekly->value ? 'green' : 'grey' }};"></i></a>
                                                    @endif -->
                                                    <a href="/teams/weekly/edit/{{ $weekly->id }}"><i class="btn fas fa-edit"
                                                            style="color: rgb(239, 239, 54)"></i></a>
                                                    <form action="/weekly/delete" method="POST">
                                                        @csrf
                                                        <input type="hidden" name="id" value="{{ $weekly->id }}">
                                                        <button type="submit" class="btn"
                                                        style="color: rgb(204, 26, 26);"><i
                                                        class="fas fa-trash"></i></button>
                                                        
                                                    </form>
                                                </td>
                                                <td>{{ $weekly->user->nama_lengkap}}</td>
                                                <td>{{ $weekly->year }}</td>
                                                <td>{{ $weekly->week }}</td>
                                                <td>{{ $weekly->task }}</td>
                                                <td>{{ $weekly->taskcategory->task_category ?? '' }}</td>
                                                <td class="
                                                    @if ($weekly->taskstatus != null)
                                                        @if ($weekly->taskstatus->task_status == 'PROGRESS')
                                                            {{"text-warning"}}
                                                        @elseif ($weekly->taskstatus->task_status == 'DONE')
                                                            {{"text-success"}}
                                                        @elseif ($weekly->taskstatus->task_status == 'PLANNED')
                                                            {{"text-info"}}
                                                        @else
                                                            {{"text-danger"}}
                                                        @endif
                                                    @endif
                                                ">
                                                {{ $weekly->taskstatus->task_status ?? '' }}</td>
                                                <td>{{ $weekly->tipe }}</td>
                                                <td>{{ $weekly->value_plan ? number_format($weekly->value_plan, 0, ',', '.') : '-' }}
                                                </td>
                                                <td>{{ $weekly->value_actual ? number_format($weekly->value_actual, 0, ',', '.') : '-' }}
                                                </td>
                                                <td class="
                                                    {{ $weekly->value ? 'text-success' : 'text-warning', }}
                                                ">
                                                {{ $weekly->value ? 'CLOSED' : 'OPEN', }}</td>
                                                <td>{{ !$weekly->is_add ? 'Plan' : 'Extra Task' }}</td>
                                                <td>
                                                    @if ($weekly->isupdate)
                                                        <i class="far fa-check-circle" style="color: green;"></i>
                                                    @else
                                                        <i class="far fa-times-circle" style="color: red;"></i>
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
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header bg-dark">
                                <div class="row d-inline-flex">
                                    <h3 class="card-title">Log Activity</h3>
                                </div>
                                <div class="card-tools d-flex">
                                    <div class="input-group input-group-sm mr-3" style="width: 220px;">
                                        <form action="/teams/weekly" class="d-inline-flex">
                                            <select class="custom-select col-lg-10 mx-2" name="user_log">
                                                <option value="">-- User --</option>
                                                @foreach ($users as $user)
                                                    <option value="{{ $user->id }}">
                                                        {{ $user->nama_lengkap }}</option>
                                                @endforeach
                                            </select>
                                            <div class="input-group-append">
                                                <button type="submit" class="btn btn-default">
                                                    <i class="fas fa-search"></i>
                                                </button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                            <!-- /.card-header -->
                            <div class="card-body table-responsive table-striped p-0" style="height: 500px;">
                                <table class="table table-head-fixed text-nowrap">
                                    <thead>
                                        <tr>
                                            <th>No</th>
                                            <th>User</th>
                                            <th>Activity</th>
                                            <th>Date</th>
                                            <th>Time</th>
                                            <!-- <th>On-Time Point</th> -->
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($logs as $log)
                                            <tr>
                                                <td>{{ $loop->iteration }}</td>

                                                <td>{{ $log->user->nama_lengkap}}</td>
                                                <td>{{ $log->activity }}</td>
                                                <td>{{ Carbon\Carbon::parse($log->created_at)->format('d M Y') }}</td>
                                                <td>{{ Carbon\Carbon::parse($log->created_at)->format('H:i') }}</td>
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
        </section>
    </section>


    <!-- Modal -->
    <form action={{ auth()->user()->role_id == 1 ? '/admin/weekly/import' : '/weekly/import' }} method="POST"
        enctype="multipart/form-data">
        @csrf
        <div class="modal fade" id="importWeekly" tabindex="-1" aria-labelledby="importWeeklyLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="importWeeklyLabel">Import Weekly</h5>
                    </div>
                    <div class="modal-body">
                        @if (auth()->user()->role_id == 1)
                            <div class="col-12 mt-3">
                                <label for="userid" class="form-label">Nama</label>
                                <select class="custom-select form-control" id="userid" name="userid">
                                    @foreach ($users as $user)
                                        <option value="{{ $user->id }}">{{ $user->nama_lengkap }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        @endif
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
    <form action="/admin/weekly/export" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="modal fade" id="exportWeekly" tabindex="-1" aria-labelledby="exportWeeklytLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exportWeeklytLabel">Export Weekly</h5>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-6 mt-3">
                                <label for="year" class="form-label">Tahun</label>
                                <input type="number" class="form-control" id="year" name="year" min="2022" max="2025"
                                    step="1" value="{{ now()->year }}" required>
                            </div>
                            <div class="col-6 mt-3">
                                <label for="week" class="form-label">Minggu</label>
                                <input type="number" class="form-control" id="week" name="week" min="1"
                                    max="{{ now()->weekOfYear + 1 }}" step="1" value="{{ now()->weekOfYear }}"
                                    required>
                            </div>
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
    <form action="/weekly" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="modal fade" id="addWeekly" tabindex="-1" aria-labelledby="addWeeklytLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="addWeeklytLabel">Add Weekly</h5>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3 col-lg-12 ml-4">
                            <input type="checkbox" class="form-check-input" id="extraTaskWeekly" name="isplan">
                            <label class="form-check-label" for="extraTaskWeekly">Extra Task</label>
                        </div>
                        <div class="mb-3 col-lg-12">
                            <label for="date" class="form-label">Date</label>
                            <input type="date" class="form-control" id="date" name="date" required>
                        </div>
                        <div class="mb-3 col-lg-12" id="addWeeklyTime">
                            <label for="time" class="form-label">Time</label>
                            <input type="time" class="form-control" id="time" name="time">
                        </div>
                        <div class="mb-3 col-lg-12">
                            <label for="task" class="form-label">Task</label>
                            <input type="text" class="form-control" id="task" name="task" autocomplete="off" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">+ Add</button>
                    </div>
                </div>
            </div>
        </div>
    </form>
@endsection
