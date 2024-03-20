@extends('layouts.master')
@section('title')
    <h1 class="pagetitle">LPP MONTH END (EXCEL)</h1>
@endsection

@section('css')
<style>
    #header_tb{
        background: #6E214A;
        padding: 10px 6px;
        color: white;
        margin-bottom: 15px;
        text-align: center;
        border: 2px groove lightgray;
    }

    .label-form{
        width: 125px;
    }

    .label-form span{
        float: right;
        margin-right: 15px;
    }

    .vertical-center {
        margin: 0;
        position: absolute;
        top: 50%;
        -ms-transform: translateY(-50%);
        transform: translateY(-50%);
    }

    .form-checkbox{
        background: #6E214A;
        display: flex;
        align-items: center;
        padding: 9px 15px;
        width: 141px;
        justify-content: center;
        gap: 6px;
    }

    .form-checkbox:hover{
        cursor: pointer;
    }

    .form-checkbox input{
        width: 19px;
        height: 19px;
        cursor: pointer;
    }

    .form-checkbox{
        color: white;
        margin: 0;
    }

    input.form-no-style{
        border: none!important;
        background-color: transparent!important;
        box-shadow: none!important;
        outline: none;
        padding: 0;
        text-align: center;
    }

    tbody tr td input[type=checkbox]{
        width: 27px;
    }

    tbody tr td input[type=checkbox]:focus, tbody tr td input[type=checkbox]:hover{
        box-shadow: unset!important;
        cursor: pointer;
    }

</style>
@endsection

@section('content')
    <script src="{{ url('js/home.js?time=') . rand() }}"></script>

    <div class="container-fluid">
        <div class="row">
            <div class="col-md-5 col-lg-4">
                @include('layouts.report-menu')
            </div>
            <div class="col-md-7 col-lg-8">
                <div class="card shadow mb-4 w-100" id="report_container">
                    <div class="card-body" id="report_content" style="padding: 1.75rem">
                        <div id="header_tb">
                            <h5 class="m-0">Export LPP to Excel</h5>
                        </div>
                        <form id="form_report" method="POST" action="/report/perincian-baso/show-pdf">
                            <div class="row">
                                <div class="col-9">
                                    <div class="form-group d-flex align-items-center">
                                        <label class="label-form" for="periode">periode <span>:</span></label>
                                        <div class="d-flex align-items-center" style="gap: 20px">
                                            <input type="month" class="form-control" name="periode" id="periode">
                                        </div>
                                    </div>
                                    <div class="form-group d-flex align-items-center">
                                        <label class="label-form" for="jenis_barang_cust">Jenis Barang <span>:</span></label>
                                        <div class="d-flex align-items-center" style="gap: 20px">
                                            <input type="text" id="jenis_barang_cust" name="jenis_barang" required class="form-control single-word-input" style="width: 70px">
                                            <p class="m-0">[ A - All / B - Baik / T - Retur / R - Rusak ]</p>
                                        </div>
                                    </div>
                                    <div class="form-group d-flex align-items-center">
                                        <label class="label-form" for="selisih_so">PLU <span>:</span></label>
                                        <div class="d-flex align-items-center" style="gap: 20px">
                                            <label class="form-checkbox" for="check_all">
                                                <input type="checkbox" id="check_all" onclick="$(this).val(this.checked ? 1 : 0)" value="0" name="check_all" class="form-control">
                                                All PLU
                                            </label>
                                            <input type="text" style="width: 20%" class="form-control" id="input_plu">
                                            <input type="text" style="width: 60%" class="form-control" id="input_desc">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-3" style="flex-direction: column">
                                    <div class="d-flex" style="gap: 9px; height: 25%">
                                        <button type="button" class="btn btn-warning" onclick="cetakDataLPP()" style="flex: 1">Cetak</button>
                                        <button type="button" class="btn btn-danger" onclick="cancelActionCust()" style="flex: 1">Cancel</button>
                                    </div>
                                    <div class="d-flex" style="margin-top: 9px; height: 25%">
                                        <button type="button" class="btn btn-secondary" onclick="clearDataPLU()" style="flex: 1">Reset Data</button>
                                    </div>
                                    <div class="d-flex" style="margin-top: 9px; height: 25%">
                                        <button type="button" class="btn btn-success" onclick="simpanDataPLU()" style="flex: 1">Simpan Data PLU</button>
                                    </div>
                                </div>
                            </div>
                            <table class="table table-striped table-hover w-100 datatable-dark-primary table-center" id="tb_lpp_month">
                                <thead>
                                    <tr>
                                        <th>Pilih</th>
                                        <th>PLU</th>
                                        <th>Keterangan</th>
                                    </tr>
                                </thead>
                            </table>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('page-script')
<script>
    let tb;
    $(document).ready(function(){
        setMonthNow('#periode');

        $('#report_content').on('input', "#jenis_barang_cust", function(){
            var input = $(this).val().trim();
            var words = input.split(' ');

            if (!/^[ABTR]$/i.test(input)) {
                $(this).val('');
            } else {
                $(this).val(input.toUpperCase());
            }
        });

        $('#check_all').change(function() {
            $('.table-checkbox').prop('checked', this.checked);
        });

        initializeDatatable();
    });

    function setMonthNow(element){
        var today = moment().format('yyyy-MM');
        $(element).val(today).trigger('change');
    }

    $("#input_plu").focus().on("keydown", function(event) {
        if (event.keyCode === 9) {
            event.preventDefault();
            checkDescPLU();
        }
    });


    function initializeDatatable(){
        tb = $('#tb_lpp_month').DataTable({
            ajax: {
                url: '/report/lpp-month-end/datatables',
                type: 'GET',
            },
            language: {
                emptyTable: "<div class='datatable-no-data' style='color: #ababab'>Data Masih Kosong</div>",
            },
            columns: [
                { data: null,
                    render: function(data, type, row) {
                        return `<input type="checkbox" class="form-control table-checkbox" onchange="check_checkbox()" name="checkbox[]" style="margin: auto">`;
                    }
                },
                { data: 'prd_prdcd',
                    render: function(data, type, row) {
                        return `<input name="plu[]" type="text" class="form-control form-no-style input-plu-table" readonly  value="${data}">`;
                    }
                },
                {data: 'prd_deskripsipanjang'},
            ],
            columnDefs: [
                { className: 'text-center', targets: '_all' },
                { width: '8%', targets: 0 },
                { width: '12%', targets: 1 },
                { width: '60%', targets: 2 },
            ],
            paging: false,
            searching: false,
            info: false,
            ordering: false,
            order: [],
        });
    }

    function checkDescPLU(){
        $('#modal_loading').modal('show');
        $.ajax({
            url: "/report/lpp-month-end/get-desc/" + $("#input_plu").val(),
            type: "GET",
            success: function(response) {
                setTimeout(function () { $('#modal_loading').modal('hide'); }, 500);
                $("#input_desc").val(response.data.prd_deskripsipanjang);
            },error: function(jqXHR, textStatus, errorThrown) {
                setTimeout(function () { $('#modal_loading').modal('hide'); }, 500);
                Swal.fire({
                    text: (jqXHR.responseJSON && jqXHR.responseJSON.code === 400)
                        ? jqXHR.responseJSON.message
                        : "Oops! Terjadi kesalahan segera hubungi tim IT (" + errorThrown + ")",
                    icon: "error"
                });
            }
        });
    }

    function check_checkbox() {
        var allChecked = $(".table-checkbox:checked").length === $(".table-checkbox").length;
        $('#check_all').prop('checked', allChecked);
    }

    function simpanDataPLU(){
        Swal.fire({
            title: `Yakin ingin simpan data PLU ?`,
            text: `Pastikan input PLU dan Deskripsi sudah terisi...`,
            icon: 'warning',
            showCancelButton: true,
            cancelButtonText: 'Batalkan',
            confirmButtonText: 'Ya, Lanjutkan'
        })
        .then((result) => {
            if (result.value) {
                $('#modal_loading').modal('show');
                $.ajax({
                    url: "/report/lpp-month-end/action/simpan-data",
                    type: "POST",
                    data: { prd_prdcd: $("#input_plu").val(), prd_deskripsipanjang: $("#input_desc").val() },
                    success: function(response) {
                        setTimeout(function () { $('#modal_loading').modal('hide'); }, 500);
                        $("#input_plu").val('');
                        $("#input_desc").val('');
                        tb.destroy();
                        initializeDatatable();
                    },error: function(jqXHR, textStatus, errorThrown) {
                        setTimeout(function () { $('#modal_loading').modal('hide'); }, 500);
                        Swal.fire({
                            text: (jqXHR.responseJSON && jqXHR.responseJSON.code === 400)
                                ? jqXHR.responseJSON.message
                                : "Oops! Terjadi kesalahan segera hubungi tim IT (" + errorThrown + ")",
                            icon: "error"
                        });
                    }
                });
            }
        });
    }

    function clearDataPLU(){
        Swal.fire({
            title: `Yakin akan membuat Data PLU Sonas Baru ?`,
            text: `Data PLU saat ini akan dihapus...`,
            icon: 'warning',
            showCancelButton: true,
            cancelButtonText: 'Batalkan',
            confirmButtonText: 'Ya, Lanjutkan'
        })
        .then((result) => {
            if (result.value) {
                $('#modal_loading').modal('show');
                $.ajax({
                    url: "/report/lpp-month-end/action/reset-data",
                    type: "DELETE",
                    success: function(response) {
                        setTimeout(function () { $('#modal_loading').modal('hide'); }, 500);
                        tb.destroy();
                        initializeDatatable();
                    },error: function(jqXHR, textStatus, errorThrown) {
                        setTimeout(function () { $('#modal_loading').modal('hide'); }, 500);
                        Swal.fire({
                            text: (jqXHR.responseJSON && jqXHR.responseJSON.code === 400)
                                ? jqXHR.responseJSON.message
                                : "Oops! Terjadi kesalahan segera hubungi tim IT (" + errorThrown + ")",
                            icon: "error"
                        });
                    }
                });
            }
        });
    }

    function cancelActionCust(){
        Swal.fire({
            title: `Yakin ingin membatalkan Export LPP ?`,
            text: `Data didalam table akan tetap tersimpan`,
            icon: 'warning',
            showCancelButton: true,
            cancelButtonText: 'Cancel',
            confirmButtonText: 'Ya'
        })
        .then((result) => {
            if (result.value) {
                window.location.href = '/report/';
            }
        });
    }

    function getCheckedPLU() {
        var checkedInputs = [];
        $('.table-checkbox:checked').each(function() {
            var input = $(this).closest('tr').find('.input-plu-table').val();
            checkedInputs.push(input);
        });
        return checkedInputs;
    }

    function cetakDataLPP(){
        Swal.fire({
            title: `Yakin akan Cetak Data PLU ?`,
            text: `Pastikan anda sudah memilih PLU yang akan dicetak...`,
            icon: 'warning',
            showCancelButton: true,
            cancelButtonText: 'Batalkan',
            confirmButtonText: 'Ya'
        })
        .then((result) => {
            if (result.value) {
                let plu_list = getCheckedPLU();
                if (!plu_list.length || !$("[name=jenis_barang]").val()) {
                    let message = !plu_list.length ? "Harap Pilih PLU Terlebih Dahulu" : "Harap Masukkan Jenis Barang Terlebih Dahulu";
                    Swal.fire(message, "", "warning");
                    return;
                }
                $('#modal_loading').modal('show');
                $.ajax({
                    url: "/report/lpp-month-end/action/cetak-lpp",
                    type: "POST",
                    data: {plu: plu_list, jenis_barang: $("[name=jenis_barang]").val(), periode: $("#periode").val(), all_plu: $("#check_all").val()},
                    xhrFields: {
                        responseType: 'blob' // Important for binary data
                    },
                    success: function(response) {
                        setTimeout(function () { $('#modal_loading').modal('hide'); }, 500);
                        var contentType = response.type;
                        var blob = new Blob([response], { type: contentType });
                        var downloadUrl = URL.createObjectURL(blob);
                        var a = document.createElement('a');
                        a.href = downloadUrl;
                        var fileName = 'LPP_MONTH_END.xlsx';
                        a.download = fileName;
                        document.body.appendChild(a);
                        a.click();
                        window.URL.revokeObjectURL(downloadUrl);
                        document.body.removeChild(a);
                    },error: function(jqXHR, textStatus, errorThrown) {
                        setTimeout(function () { $('#modal_loading').modal('hide'); }, 500);
                        Swal.fire({
                            text: (jqXHR.responseJSON && jqXHR.responseJSON.code === 400)
                                ? jqXHR.responseJSON.message
                                : "Oops! Terjadi kesalahan segera hubungi tim IT (" + errorThrown + ")",
                            icon: "error"
                        });
                    }
                });
            }
        });
    }
</script>
<script src="{{ asset('js/report-action.js') }}"></script>
@endpush
