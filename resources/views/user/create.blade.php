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
                                <h3 class="card-title">CREATE &raquo; USER</h3>
                            </div>
                            <div class="card-body">
                                <form action="/user" method="POST">
                                    @csrf
                                    <div class="row">
                                        <div class="mb-3 col-lg-4">
                                            <label for="nama_lengkap" class="form-label">Nama Lengkap</label>
                                            <input type="text" class="form-control" id="nama_lengkap" name="nama_lengkap"
                                                required>
                                        </div>
                                        <div class="mb-3 col-lg-4">
                                            <label for="username" class="form-label">User Name</label>
                                            <input type="text" class="form-control" id="username" name="username"
                                                required>
                                        </div>
                                        <div class="mb-3 col-lg-4">
                                            <label for="password" class="form-label">Password</label>
                                            <input type="text" class="form-control" id="password" name="password"
                                                required>
                                        </div>
                                    </div>
                                    <div class="mb-3">
                                        <div class="row">
                                            <div class="col-lg-3">
                                                <label for="role_id" class="form-label col-lg-12">Role</label>
                                                <select class="custom-select col-lg-12" name="role_id" id="role_id"
                                                    required>
                                                    @foreach ($roles as $role)
                                                        <option value="{{ $role->id }}">{{ $role->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="col-lg-3">
                                                <label for="area_id" class="form-label col-lg-12 ">area</label>
                                                <select class="custom-select col-lg-12 adduserarea" name="area_id"
                                                    id="area_id" required>
                                                    <option value="">--Choose Area--</option>
                                                    @foreach ($areas as $area)
                                                        <option value="{{ $area->id }}" {{ $area->name == 'STAFF' ? 'selected' : '' }}>{{ $area->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="col-lg-3">
                                                <label for="divisi_id" class="form-label col-lg-12">Divisi</label>
                                                <select class="custom-select col-lg-12 adduserdivisi" id="divisi_id"
                                                    name="divisi_id" required>
                                                </select>
                                            </div>
                                            <div class="col-lg-3">
                                                <label for="approval_id" class="form-label col-lg-12">Approval</label>
                                                <select class="custom-select col-lg-12 adduserapproval" id="approval_id"
                                                    name="approval_id" required>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="mb-3">
                                        <div class="row">
                                            <div class="col-lg-3">
                                                <label for="wn" class="form-label col-lg-12">Weekly Non</label>
                                                <select class="custom-select col-lg-12" id="wn" name="wn" required>
                                                    <option value="1" selected>YES</option>
                                                    <option value="0">NO</option>
                                                </select>
                                            </div>
                                            <div class="col-lg-3">
                                                <label for="wr" class="form-label col-lg-12">Weekly Result</label>
                                                <select class="custom-select col-lg-12" id="wr" name="wr" required>
                                                    <option value="1" selected>YES</option>
                                                    <option value="0">NO</option>
                                                </select>
                                            </div>
                                            <div class="col-lg-3">
                                                <label for="mn" class="form-label col-lg-12">Monthly Non</label>
                                                <select class="custom-select col-lg-12" id="mn" name="mn" required>
                                                    <option value="1" selected>YES</option>
                                                    <option value="0">NO</option>
                                                </select>
                                            </div>
                                            <div class="col-lg-3">
                                                <label for="mr" class="form-label col-lg-12">Monthly Result</label>
                                                <select class="custom-select col-lg-12" id="mr" name="mr" required>
                                                    <option value="1" selected>YES</option>
                                                    <option value="0">NO</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <button type="submit" class="btn btn-success mt-3">Simpan</button>
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
