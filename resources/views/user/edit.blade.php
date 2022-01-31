@extends('layout.main_tamplate')

@section('content')
    <section class="content-header">
        <!-- Main content -->
        <section class="content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-12">
                        <div class="card card-dark">
                            <!-- /.card-header -->
                            <div class="card-header">
                                <h3 class="card-title">EDIT &raquo; {{ $user->nama_lengkap }}</h3>
                            </div>
                            <div class="card-body">
                                <form action="/user/update/{{ $user->id }}" method="POST">
                                    @csrf
                                    <div class="mb-3">
                                        <label for="nama_lengkap" class="form-label">Nama Lengkap</label>
                                        <input type="text" class="form-control" id="nama_lengkap"
                                            value="{{ $user->nama_lengkap }}" name="nama_lengkap" required>
                                    </div>
                                    <div class="row">
                                        <div class="mb-3 col-lg-6">
                                            <label for="username" class="form-label">User Name</label>
                                            <input type="text" class="form-control" id="username" name="username"
                                                value="{{ $user->username }}" required>
                                        </div>
                                        <div class="mb-3 col-lg-6">
                                            <label for="password" class="form-label">Password</label>
                                            <input type="text" class="form-control" id="password" name="password"
                                                required>
                                        </div>
                                    </div>
                                    <div class="mb-3">
                                        <div class="row">
                                            <div class="col-lg-4">
                                                <label for="role_id" class="form-label col-lg-12">Role</label>
                                                <select class="custom-select col-lg-12" name="role_id" id="role_id"
                                                    required>
                                                    @foreach ($roles as $role)
                                                        <option value="{{ $role->id }}" @if ($user->role_id === $role->id)
                                                            selected
                                                    @endif>
                                                    {{ $role->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="col-lg-4">
                                                <label for="divisi_id" class="form-label col-lg-12">Divisi</label>
                                                <select class="custom-select col-lg-12" id="divisi_id" name="divisi_id"
                                                    required>
                                                    @foreach ($divisis as $divisi)
                                                        <option value="{{ $divisi->id }}"
                                                            {{ $divisi->id === $user->divisi_id ? 'selected' : '' }}>
                                                            {{ $divisi->name.' - '.$divisi->area->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="col-lg-4">
                                                <label for="approval_id" class="form-label col-lg-12">Approval</label>
                                                <select class="custom-select col-lg-12" id="approval_id" name="approval_id"
                                                    required>
                                                    @foreach ($approvals as $approval)
                                                        <option value="{{ $approval->id }}"
                                                            {{ $approval->id === $user->approval_id ? 'selected' : '' }}>
                                                            {{ $approval->nama_lengkap }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="mb-3">
                                        <div class="row">
                                            <div class="col-lg-3">
                                                <label for="wn" class="form-label col-lg-12">Weekly Non</label>
                                                <select class="custom-select col-lg-12" id="wn" name="wn" required>
                                                    <option value="1" {{ $user->wn ? 'selected' : '' }}>YES</option>
                                                    <option value="0" {{ $user->wn ? '-' : 'selected' }}>NO</option>
                                                </select>
                                            </div>
                                            <div class="col-lg-3">
                                                <label for="wr" class="form-label col-lg-12">Weekly Result</label>
                                                <select class="custom-select col-lg-12" id="wr" name="wr" required>
                                                    <option value="1" {{ $user->wr ? 'selected' : '' }}>YES</option>
                                                    <option value="0" {{ $user->wr ? '-' : 'selected' }}>NO</option>
                                                </select>
                                            </div>
                                             <div class="col-lg-3">
                                                <label for="mn" class="form-label col-lg-12">Monthly Non</label>
                                                <select class="custom-select col-lg-12" id="mn" name="mn" required>
                                                    <option value="1" {{ $user->mn ? 'selected' : '' }}>YES</option>
                                                    <option value="0" {{ $user->mn ? '-' : 'selected' }}>NO</option>
                                                </select>
                                            </div>
                                             <div class="col-lg-3">
                                                <label for="mr" class="form-label col-lg-12">Monthly Result</label>
                                                <select class="custom-select col-lg-12" id="mr" name="mr" required>
                                                    <option value="1" {{ $user->mr ? 'selected' : '' }}>YES</option>
                                                    <option value="0" {{ $user->mr ? '-' : 'selected' }}>NO</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <button type="submit" class="btn btn-success mt-3">Update</button>
                                </form>
                            </div>
                        </div>
                    </div>
                    <!-- /.card -->
                </div>
            </div>
        </section>
    </section>
    <!-- /.content -->
@endsection
