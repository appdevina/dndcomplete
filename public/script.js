$(document).ready(function () {
    var dailyi = 0;
    var weeklyi = 0;
    var monthlyi = 0;
    //ADD USER
    $(document).ready(function () {
        $divisi = $('.adduserdivisi');
        $approval = $('.adduserapproval');
        $divisi.append('<option value="">---Choose Area First--</option>');
        $approval.append('<option value="">---Choose Division First--</option>');
        $('.adduserarea').change(function (e) {
            var $areaId = $(".adduserarea").val();
            if ($areaId === "") {
                $divisi.empty();
                $divisi.append('<option value="">---Choose Area First--</option>');
                $approval.empty();
                $approval.append('<option value="">---Choose Division First--</option>');
            } else {
                $divisi.empty();
                $divisi.append('<option value="">---Choose Division--</option>');
                $approval.empty();
                $approval.append(
                    '<option value="">---Choose Division First--</option>');
                $.ajax({
                    type: "GET",
                    url: 'http://dnd.completeselular.com/divisi/get/' + $areaId,
                    success: function (data) {
                        $.each(data, function (index, value) {
                            $divisi.append('<option value="' + value.id + '">' +
                                value
                                    .name + '</option>');
                        });
                    }
                });
            }

        });

        $divisi.change(function (e) {
            var $areaId = $(".adduserarea").val();
            if ($divisi.val() === '' || $areaId === '') {
                $approval.empty();
                $approval.append('<option value="">---Choose Division First--</option>');
            } else {
                $.ajax({
                    type: "GET",
                    url: "http://dnd.completeselular.com/approval/get?areaid=" + $areaId + "&divisiid=" +
                        $divisi.val(),
                    success: function (data) {
                        $approval.empty();
                        $approval.append(
                            '<option value="">---Choose Approval Person--</option>');
                        $.each(data, function (index, value) {
                            $approval.append('<option value="' + value.id + '">' +
                                value
                                    .nama_lengkap + '</option>');
                        });
                    }
                });
            }
        });
    });

    $('.select2').select2();

    //DAILY
    $("#extraTaskDaily").change(function (e) {
        if ($("#extraTaskDaily").is(':checked')) {
            $('#addDailyTime').hide();
        } else {
            $('#addDailyTime').show();
        }
    })
    //WEEKLY
    $('#value_plan').hide();
    $("#resultkWeekly").change(function (e) {
        isChecked($("#resultkWeekly"));
    });

    $("#value_plan").on("input", function () {
        $val = addPeriod($("#value_plan").val());
        changeVal($val)
    });

    $("#valueactual").on("input", function () {
        $val = addPeriod($("#valueactual").val());
        changeVal($val)
    });

    $("#valueplan").on("input", function () {
        $val = addPeriod($("#valueplan").val());
        changeVal($val)
    });

    //MONTHLY
    $("#resultMonthly").change(function (e) {
        isChecked($("#resultMonthly"));
    });

    function changeVal(val) {
        $('#nominal').text('Value : ' + val);
    };

    function isChecked(val) {
        val.is(':checked') ? $('#value_plan').show() : $('#value_plan').hide();
        val.is(':checked') ? $('#nominal').show() : $('#nominal').hide();
    };

    function addPeriod(nStr) {
        nStr += '';
        x = nStr.split('.');
        x1 = x[0];
        x2 = x.length > 1 ? '.' + x[1] : '';
        var rgx = /(\d+)(\d{3})/;
        while (rgx.test(x1)) {
            x1 = x1.replace(rgx, '$1' + '.' + '$2');
        }
        return x1 + x2;
    }



    //CHANGE TASK
    $('.daterequest').hide();
    $('.weekrequest').hide();
    $('.monthrequest').hide();
    $('#formreplacedaily').hide();
    $('#formreplaceweekly').hide();
    $('#formreplacemonthly').hide()

    $('#jenistodo').change(function (e) {
        $('.daterequest').hide();
        $('.weekrequest').hide();
        $('.monthrequest').hide();
        $('#formreplaceweekly').hide();
        $('#formreplacedaily').hide();
        $('#formreplacemonthly').hide();

        //
        $('.dateselectedrequest').prop('required', false);
        $('.weekselectedrequest').prop('required', false);
        $('.monthselectedrequest').prop('required', false);
        switch ($('#jenistodo').val()) {
            case 'Daily':
                $('.daterequest').show();
                $('#formreplacedaily').show();
                $('.dateselectedrequest').prop('required', true);
                $('#tasksdaily')
                    .find('tr')
                    .remove()
                    .end();
                $('#tasksdaily').append('<tr id="rowdaily' + dailyi + '"><td><input type"text" class="form-control col-lg-12" placeholder="Input task replace" name="taskdaily[]"></td><td><input type="time" class="form-control col-lg-8" name="timedaily[]"></td><td><a href="#formreplacedaily" class="badge bg-danger btn_remove" id="daily' +
                    dailyi +
                    '"><span><i class="fas fa-times-circle"></i></span></a></td></tr>');
                dailyi++;
                break;
            case 'Weekly':
                $('.weekrequest').show();
                $('#formreplaceweekly').show();
                $('.weekselectedrequest').prop('required', true);
                $('#tasksweekly')
                    .find('tr')
                    .remove()
                    .end();
                if ($('#wr').val() == 1) {
                    $('#tasksweekly').append('<tr id="rowweekly' + weeklyi + '"><td><input type"text" class="form-control col-lg-12" placeholder="Input task replace" name="taskweekly[]"></td><td><select class="custom-select col-lg-12" name="tipe[]" id="tipe"><option value="NON" selected>NON</option><option value="RESULT">RESULT</option></select ></td><td><input type="number" placeholder="isi jika result" min="1" step="1" class="form-control" name="value_plan[]"></td><td><a href="#formreplaceweekly" class="badge bg-danger btn_remove" id="weekly' +
                        weeklyi +
                        '"><span><i class="fas fa-times-circle"></i></span></a></td></tr>');
                } else {
                    $('#tasksweekly').append('<tr id="rowweekly' + weeklyi + '"><td><input type"text" class="form-control col-lg-12" placeholder="Input task replace" name="taskweekly[]"></td><td><a href="#formreplaceweekly" class="badge bg-danger btn_remove" id="weekly' +
                        weeklyi +
                        '"><span><i class="fas fa-times-circle"></i></span></a></td></tr>');
                }
                weeklyi++;
                break;
            case 'Monthly':
                $('.monthrequest').show();
                $('#formreplacemonthly').show()
                $('.monthselectedrequest').prop('required', true);
                $('#tasksweekly')
                    .find('tr')
                    .remove()
                    .end();
                if ($('#mr').val() == 1) {
                    $('#tasksmonthly').append('<tr id="rowmonthly' + monthlyi + '"><td><input type"text" class="form-control col-lg-12" placeholder="Input task replace" name="taskmonthly[]"></td><td><select class="custom-select col-lg-12" name="tipe[]" id="tipe"><option value="NON" selected>NON</option><option value="RESULT">RESULT</option></select ></td><td><input type="number" placeholder="isi jika result" min="1" step="1" class="form-control" name="value_plan[]"></td><td><a href="#formreplacemonthly" class="badge bg-danger btn_remove" id="monthly' +
                        monthlyi +
                        '"><span><i class="fas fa-times-circle"></i></span></a></td></tr>');
                } else {
                    $('#tasksmonthly').append('<tr id="rowmonthly' + monthlyi + '"><td><input type"text" class="form-control col-lg-12" placeholder="Input task replace" name="taskmonthly[]"></td><td><a href="#formreplacemonthly" class="badge bg-danger btn_remove" id="monthly' +
                        monthlyi +
                        '"><span><i class="fas fa-times-circle"></i></span></a></td></tr>');
                }
                monthlyi++;
                break;
        }
        $('.existingtask')
            .find('option')
            .remove()
            .end();
        $('.duallistbox').bootstrapDualListbox('refresh', true);

    });
    $('.duallistbox').bootstrapDualListbox({
        nonSelectedListLabel: 'Task Existing',
        selectedListLabel: 'To Be Replaced',
        moveSelectedLabel: true,
        moveOnSelect: false,
        infoText: 'Total Task {0}',
        infoTextEmpty: 'No Task',
    });

    $('.dateselectedrequest').change(function (e) {
        $('.existingtask')
            .find('option')
            .remove()
            .end();
        var $date = $('.dateselectedrequest').val();
        var $idreq = $('#idrequest').val();
        $.ajax({
            type: "get",
            url: 'http://dnd.completeselular.com/daily/get?date=' + $date + '&id=' + $idreq,
            success: function (data) {
                $.each(data, function (index, value) {
                    $('.existingtask')
                        .append('<option value="' + value.id + '">' +
                            value
                                .time + ' ' + value.task + '</option>');
                });
                $('.duallistbox').bootstrapDualListbox('refresh', true);
            }
        });
    });

    $('.weekselectedrequest').change(function (e) {
        $('.existingtask')
            .find('option')
            .remove()
            .end();
        var $year = $('#year').val();
        var $week = $('#week').val();
        var $idreq = $('#idrequest').val();
        $.ajax({
            type: "get",
            url: 'http://dnd.completeselular.com/weekly/get?year=' + $year + '&week=' + $week + '&id=' + $idreq,
            success: function (data) {
                $.each(data, function (index, value) {
                    $('.existingtask')
                        .append('<option value="' + value.id + '">[' + value.tipe + '] ' + value.task + '</option>');
                });
                $('.duallistbox').bootstrapDualListbox('refresh', true);
            }
        });
    });

    $('.monthselectedrequest').change(function (e) {
        $('.existingtask')
            .find('option')
            .remove()
            .end();
        var $date = $('#month').val() + '-01';
        var $idreq = $('#idrequest').val();
        $.ajax({
            type: "get",
            url: 'http://dnd.completeselular.com/monthly/get?date=' + $date + '&id=' + $idreq,
            success: function (data) {
                $.each(data, function (index, value) {
                    $('.existingtask')
                        .append('<option value="' + value.id + '">[' + value.tipe + '] ' + value.task + '</option>');
                });
                $('.duallistbox').bootstrapDualListbox('refresh', true);
            }
        });
    });

    $('#addDaily').click(function (e) {
        $('#tasksdaily').append('<tr id="rowdaily' + dailyi + '"><td><input type"text" class="form-control col-lg-12" placeholder="Input task replace" name="taskdaily[]"></td><td><input type="time" class="form-control col-lg-8" name="timedaily[]"></td><td><a href="#formreplacedaily" class="badge bg-danger btn_remove" id="daily' +
            dailyi +
            '"><span><i class="fas fa-times-circle"></i></span></a></td></tr>');
        dailyi++;
    });

    $(document).on('click', '.btn_remove', function () {
        var button_id = $(this).attr("id");
        $('#row' + button_id + '').remove();
    });

    $('#addWeekly').click(function (e) {
        if ($('#wr').val() == 1) {
            $('#tasksweekly').append('<tr id="rowweekly' + weeklyi + '"><td><input type"text" class="form-control col-lg-12" placeholder="Input task replace" name="taskweekly[]"></td><td><select class="custom-select col-lg-12" name="tipe[]" id="tipe"><option value="NON" selected>NON</option><option value="RESULT">RESULT</option></select ></td><td><input type="number" placeholder="isi jika result" min="1" step="1" class="form-control" name="value_plan[]"></td><td><a href="#formreplaceweekly" class="badge bg-danger btn_remove" id="weekly' +
                weeklyi +
                '"><span><i class="fas fa-times-circle"></i></span></a></td></tr>');
        } else {
            $('#tasksweekly').append('<tr id="rowweekly' + weeklyi + '"><td><input type"text" class="form-control col-lg-12" placeholder="Input task replace" name="taskweekly[]"></td><td><a href="#formreplaceweekly" class="badge bg-danger btn_remove" id="weekly' +
                weeklyi +
                '"><span><i class="fas fa-times-circle"></i></span></a></td></tr>');
        }
        weeklyi++;
    });

    $('#addMonthly').click(function (e) {
        if ($('#mr').val() == 1) {
            $('#tasksmonthly').append('<tr id="rowmonthly' + monthlyi + '"><td><input type"text" class="form-control col-lg-12" placeholder="Input task replace" name="taskmonthly[]"></td><td><select class="custom-select col-lg-12" name="tipe[]" id="tipe"><option value="NON" selected>NON</option><option value="RESULT">RESULT</option></select ></td><td><input type="number" placeholder="isi jika result" min="1" step="1" class="form-control" name="value_plan[]"></td><td><a href="#formreplacemonthly" class="badge bg-danger btn_remove" id="monthly' +
                monthlyi +
                '"><span><i class="fas fa-times-circle"></i></span></a></td></tr>');
        } else {
            $('#tasksmonthly').append('<tr id="rowmonthly' + monthlyi + '"><td><input type"text" class="form-control col-lg-12" placeholder="Input task replace" name="taskmonthly[]"></td><td><a href="#formreplacemonthly" class="badge bg-danger btn_remove" id="monthly' +
                monthlyi +
                '"><span><i class="fas fa-times-circle"></i></span></a></td></tr>');
        }
        monthlyi++;
    });

    $('#tanggal').daterangepicker();
});
