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
                            <h3 class="card-title"><strong>Add KPI</strong></h3>
                        </div>
                        <!-- /.card-header -->
                        <div class="card-body table-responsive p-0" style="height: 750px;">
                            <div class="col-md-12 mt-3">
                                <div class="" id="formaddkpi" >
                                    <form action="/kpi" method="POST" enctype="multipart/form-data">
                                    {{csrf_field()}}
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
                                    <br>
                                    <div>
                                        <div class="form-group" >
                                            <div class="card-body table-responsive p-0" style="min-height: 300px;">
                                                <table class="table table-head-fixed text-nowrap">
                                                    <thead>
                                                        <tr>
                                                            <th style="width:70%;">KPI Desc</th>
                                                            <th style="width:10%;">Count Type</th>
                                                            <th style="width:10%;">Value Plan</th>
                                                            <th style="width:10%;">
                                                                <a href="#addkpi" class="badge bg-success" id="addKpi">Add <span class="lnr lnr-plus-circle"></span></a>
                                                            </th>
                                                        </tr>
                                                    </thead>
                                                    <tbody id="tablekpi" class="tablekpi">
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                    <br>
                                    <div class="col-lg-12" style="text-align: right;">
                                        <button type="submit" class="btn btn-info" onclick="return confirm('Are you sure want to submit ?')">SUBMIT</button>
                                    </div>
                                </div>
                                </form>
                            </div>
                        </div>
                        <!-- /.card-body -->
                    </div>
                    <!-- /.card -->
                </div>
            </div>
        </div>
    </section>
<script>
function toggleOption() {
    var kpi_type_id = $('#kpi_type_id').val();
    var kpi_category_id = $('#kpi_category_id').val();
    var position_id = $('#position_id').val();
}
</script>
@endsection