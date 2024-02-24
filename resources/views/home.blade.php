@extends('master')
@section('title')
    <h1 class="pagetitle">UPDATE JATUH TEMPO OMI</h1>
@endsection

@section('css')
<style>
    .header{
        margin-bottom: 40px;
    }

    .table tbody tr.deactive td{
        background-color: #ffb6c19e;
    }

    .select-r td {
        background-color: #566cfb !important;
        color: white!important;
    }

    .table td{
        color: black;
        border-top: 1px solid #d5d7db!important;
    }
    .form-group label{
        margin: 0;
        white-space: nowrap;
    }

    .form-control{
        height: calc(1.5em + .75rem + 0px);
    }

    .footer{
        margin-top: 25px;
    }

    .blur-container{
        -webkit-filter: blur(20px);
        filter: blur(20px);
        -moz-filter: blur(20px);
        -o-filter: blur(20px);
        -ms-filter: blur(20px);
    }
</style>
@endsection

@section('content')
<script src="{{ url('js/home.js?time=') . rand() }}"></script>

<div class="container-fluid" id="container-wrapper">
    <div class="card shadow mb-4">
        <div class="card-body">
            <div class="d-flex flex-wrap header-action align-items-center mb-2 mt-1" style="gap: 12px;">
                <div class="header-action">
                    <div class="d-flex" style="gap: 15px">
                        <div class="form-group d-flex align-items-center" style="width: 300px; gap: 13px">
                            <label for="list_toko" style="color: #012970; font-size: 1rem; flex: 1;">Toko OMI : </label>
                            <select id="list_toko" class="form-control select2" style="width: 210px">
                            </select>
                        </div>
                        <div style="width: 450px">
                            <input type="text" class="form-control" readonly id="name_toko">
                        </div>
                    </div>
                    <div class="d-flex" style="gap: 15px">
                        <div class="form-group d-flex align-items-center" style="width: 300px; gap: 13px">
                            <label for="list_pb" style="color: #012970; font-size: 1rem; flex: 1;">No PB : </label>
                            <select id="list_pb" class="form-control select2" disabled style="width: 210px">
                            </select>
                        </div>
                        <span style="padding: 4px 10px; background: #7F1910; color: white; font-weight: 700; border-radius: 3px; height: 32px">* F3 - LOAD TRANSAKSI</span>
                    </div>
                </div>
            </div>
            <table class="table table-striped table-hover datatable-dark-primary w-100" id="table_transaksi">
                <thead>
                    <tr>
                        <th>TGL Struk</th>
                        <th>Jatuh Tempo</th>
                        <th>Kasir</th>
                        <th>Station</th>
                        <th>No. Struk</th>
                        <th>Nilai RPH</th>
                        <th>No. SPH</th>
                    </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
            <div class="footer">
                <div class="d-flex justify-content-center position-relative">
                    <div class="form-group d-flex align-items-center" style="gap: 15px">
                        <label style="color: #012970;">Update Tanggal Jatuh Tempo : </label>
                        <input type="date" id="tanggal_jatuh_tempo" class="form-control">
                    </div>
                    <div class="position-absolute" style="right: 25px">
                        <button class="btn btn-lg btn-warning" onclick="loginTempo()">UPDATE</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>

<div class="modal fade" role="dialog" id="modal_login" data-keyboard="false" data-backdrop="static">
    <div class="modal-dialog modal-md" role="document">
        <div class="modal-content">
            <div class="modal-header br">
                <h5 class="modal-title">LOGIN</h5>
            </div>
            <div class="modal-body">
                <div class="form-group d-flex align-items-center" style="width: 100%; gap: 13px">
                    <div style="width: 138px; background-color: #1d3093; padding: 5px;">
                        <label for="userID" style="color: #012970; font-size: 1rem; float: right; color: white">USERID : </label>
                    </div>
                    <input type="text" class="form-control" name="userID" id="userID">
                </div>
                <div class="form-group d-flex align-items-center" style="width: 100%; gap: 13px">
                    <label for="password" style="color: #012970; font-size: 1rem; width: 120px; background-color: #1d3093; padding: 5px; color: white">PASSWORD : </label>
                    <input type="password" class="form-control" name="password" id="password">
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-danger mr-2" onclick="cancelLoginUpdate()" style="width: 95px; font-weight: bold; height: 40px">Cancel</button>
                <button class="btn btn-success" onclick="loginUpdate()" style="width: 95px; font-weight: bold; height: 40px">Login</button>
            </div>
        </div>
    </div>
</div>

@push('page-script')
<script>
    let tb;
    let isF3Enabled = true;
    function initializeDatatables(){
        tb = $('#table_transaksi').DataTable({
            language: {
                emptyTable: "Tidak Ada Data Transaksi"
            },
            columns: [
                { data: 'tgl_struk',},
                { data: 'jatuh_tempo',},
                { data: 'kasir',},
                { data: 'station',},
                { data: 'no_struk',},
                { data: 'nilai_rph',},
                { data: 'no_sph', }
            ],
            columnDefs: [
                { className: 'text-center-vh', targets: '_all' },
            ],
            rowCallback: function(row, data){
            },
        });

    }

    $('#modal_login').on('shown.bs.modal', function () {
        isF3Enabled = false;
    });

    $('#modal_login').on('hidden.bs.modal', function () {
        isF3Enabled = true;
    });

    $(document).ready(function() {
        let today = new Date().toISOString().split('T')[0];
        $('#tanggal_transaksi').val(today);
        getDataToko();
        initializeDatatables();
    });

    function getDataToko(){
        $("#modal_loading").modal('show');
        $.ajax({
            url : "/home/load-toko",
            type: "GET",
            dataType: "JSON",
            success: function(response){
                setTimeout(function () {  $('#modal_loading').modal('hide'); }, 500);
                if(response.code === 200){
                    let data = response.data;
                    $("#list_toko").append(`<option value="null" selected disabled>-- Pilih Toko OMI --</option>`);
                    data.forEach(item => {
                        $("#list_toko").append(`<option value="${item.tko_kodeomi}" data-kodeCustomer="${item.tko_kodecustomer}" data-tokoName="${item.tko_namaomi}">${item.tko_kodeomi}</option>`);
                    });

                }else{
                    iziToast.error({
                        title: 'Error!',
                        message: response.message,
                        position: 'topRight'
                    });
                }
            },error: function (jqXHR, textStatus, errorThrown){
                setTimeout(function () {  $('#modal_loading').modal('hide'); }, 500);
                console.log('error');
                Swal.fire({
                    text: "Oops! Terjadi kesalahan segera hubungi tim IT (" + errorThrown + ")",
                    icon: "error"
                });
            }
        });
    }

    $("#list_toko").change(function(){
        let selected = $('#list_toko option:selected');
        let selectedValue = $(this).val();
        $('#name_toko').val(selected.data('tokoname'));

        $("#modal_loading").modal('show');
        $("#list_pb").empty();
        $.ajax({
            url : "/home/load-pb",
            type: "GET",
            data: {toko: selectedValue},
            dataType: "JSON",
            success: function(response){
                setTimeout(function () {  $('#modal_loading').modal('hide'); }, 500);
                if(response.code === 200){
                    let data = response.data;
                    $("#list_pb").prop('disabled', false);
                    $("#list_pb").append(`<option value="null" selected disabled>-- Pilih PB --</option>`);
                    data.forEach(item => {
                        $('#list_pb').append(`<option value="${item.btrim}">${item.btrim}</option>`);
                    });
                }else{
                    Swal.fire({
                        title: "Peringatan..!",
                        text: "Daftar PB Tidak Ditemukan",
                        icon: "error"
                    });
                    $("#list_pb").prop('disabled', true);
                    $("#list_pb").val();
                }
            },error: function (jqXHR, textStatus, errorThrown){
                setTimeout(function () {  $('#modal_loading').modal('hide'); }, 500);
                Swal.fire({
                    text: "Oops! Terjadi kesalahan segera hubungi tim IT (" + errorThrown + ")",
                    icon: "error"
                });
            }
        });
    });

    $(document).on('keydown', function(event) {
        if (event.which === 114 && isF3Enabled) {
            event.preventDefault();
            loadTransaction();
        }
    });

    function loadTransaction(){
        isF3Enabled = false;
        let no_pb = $('#list_pb').val();
        if(no_pb === null || no_pb == ''){
            tb.clear().draw();
            Swal.fire({
                title: "Peringatan..!",
                text: "Harap Pilih No. PB Terlebih Dahulu....",
                icon: "error"
            });
            isF3Enabled = true;
            return;
        }
        let selected = $('#list_toko option:selected');
        $("#modal_loading").modal('show');
        $.ajax({
            url : `/home/load-transaction/${selected.val()}/${no_pb}/${selected.data('kodecustomer')}`,
            type: "GET",
            dataType: "JSON",
            success: function(response){
                setTimeout(function () {  $('#modal_loading').modal('hide'); isF3Enabled = true; }, 500);
                let data = response.data;
                tb.clear().draw();
                tb.rows.add(data).draw();

            },error: function (jqXHR, textStatus, errorThrown){
                setTimeout(function () {  $('#modal_loading').modal('hide'); isF3Enabled = true; }, 500);
                Swal.fire({
                    text: "Oops! Terjadi kesalahan segera hubungi tim IT (" + errorThrown + ")",
                    icon: "error"
                });
            }
        });
    };

    function updateJatuhTempo(){
        isF3Enabled = false;
        let selected = $('#list_toko option:selected');
        let no_pb = $('#list_pb').val();
        $("#modal_loading").modal('show');
        let formatted_jatuh_tempo = $('#tanggal_jatuh_tempo').val().split('-').reverse().join('-');
        $.ajax({
            url : '/home/update-tempo',
            type: "POST",
            dataType: "JSON",
            data: { toko: selected.val(), no_pb: no_pb, kode_customer: selected.data('kodecustomer'), tgl_jatuh_tempo: formatted_jatuh_tempo },
            success: function(response){
                setTimeout(function () {  $('#modal_loading').modal('hide'); isF3Enabled = true; }, 500);
                console.log(response);
                if(response.code === 200){
                    Swal.fire({
                        text: response.message,
                        icon: "success"
                    });

                    loadTransaction();
                } else {
                    Swal.fire({
                        text: response.message,
                        icon: "error"
                    });
                }
            },error: function (jqXHR, textStatus, errorThrown){
                setTimeout(function () {  $('#modal_loading').modal('hide'); isF3Enabled = true; }, 500);
                Swal.fire({
                    text: "Oops! Terjadi kesalahan segera hubungi tim IT (" + errorThrown + ")",
                    icon: "error"
                });
            }
        });
    }

    function showModalLogin(){
        $('#modal_login').modal('show');
        $('#container-wrapper').addClass('blur-container');
    }

    function cancelLoginUpdate(){
        $('#modal_login').modal('hide');
        $('#container-wrapper').removeClass('blur-container');
        $('#userID').val('');
        $('#password').val('');
    }

    function loginTempo(){
        if($('#tanggal_jatuh_tempo').val() == null || $('#tanggal_jatuh_tempo').val() == ''){
            Swal.fire({
                title: 'Peringatan..!',
                text: `Harap masukkan Tanggal Jatuh Tempo Terlebih Dahulu !`,
                icon: 'warning',
            })
            return;
        }

        if (!$('#list_toko').val() || $('#list_toko').val().length === 0 || !$('#list_pb').val() || $('#list_pb').val().length === 0) {
            Swal.fire({
                title: 'Peringatan..!',
                text: `Toko atau No. PB Belum Terisi`,
                icon: 'warning',
            })
            return;
        }

        Swal.fire({
            title: 'Yakin?',
            text: `Update Tanggal Jatuh Tempo Menjadi ${$('#tanggal_jatuh_tempo').val()} ?`,
            icon: 'warning',
            showCancelButton: true,
        })
        .then((result) => {
            if (result.value) {
                showModalLogin();
            }
        })
    }

    function loginUpdate(){
        $("#modal_loading").modal('show');
        $.ajax({
            url : `/home/login-update`,
            type: "POST",
            data: {username: $('#userID').val(), password: $('#password').val()},
            dataType: "JSON",
            success: function(response){
                if(response.code === 200){
                    cancelLoginUpdate();
                    updateJatuhTempo();
                } else {
                    setTimeout(function () {  $('#modal_loading').modal('hide'); }, 500);
                    Swal.fire({
                        title: "Error..!",
                        text: response.message,
                        icon: "error"
                    });
                }
            },error: function (jqXHR, textStatus, errorThrown){
                setTimeout(function () {  $('#modal_loading').modal('hide'); }, 500);
                Swal.fire({
                    text: "Oops! Terjadi kesalahan segera hubungi tim IT (" + errorThrown + ")",
                    icon: "error"
                });
            }
        });
    }
</script>
@endpush
@endsection
