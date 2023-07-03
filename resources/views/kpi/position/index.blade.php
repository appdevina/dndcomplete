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
                            <h3 class="card-title"><strong>Position</strong></h3>
                            <div class="card-tools">
                                <a data-toggle="modal" data-target="#addPosition" class="btn btn-tool btn-sm" data-toggle="tooltip" title="Add Job Position" >
                                    <i class="fas fa-plus"></i>
                                </a>
                            </div>
                        </div>
                        <!-- /.card-header -->
                        <div class="card-body table-responsive p-0" style="height: 500px;">
                            <table class="table table-striped table-head-fixed text-nowrap">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Positon</th>
                                        <th style="text-align: right;">More</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($positions as $post)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ $post->name }}</td>
                                            <td style="text-align: right;">
                                                <a href="/position/{{ $post->id }}/edit" style="color: orange;">
                                                    <span><i class="fas fa-edit"></i></span>
                                                </a>
                                                <a href="/position/{{ $post->id }}/delete" style="color: red;" onclick="return confirm('Sure to delete data ?')">
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
    <!-- Modal Add -->
    <div class="modal fade" id="addPosition" tabindex="-1" role="dialog" aria-labelledby="addPositionLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form method="POST" action="/position">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title" id="addPositionLabel">Add Job Position</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3 col-lg-12">
                            <label for="name" class="form-label">Position</label>
                            <input type="text" class="form-control" name="name" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-info">Add</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
