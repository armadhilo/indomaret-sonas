@extends('layouts.master')
@section('title')
    <h1 class="pagetitle">INITIAL SO</h1>
@endsection

@section('css')
<style>
    /* CSS */
    .btn-cust {
        align-items: center;
        appearance: none;
        background-color: #FCFCFD;
        border-radius: 4px;
        border-width: 0;
        box-shadow: rgba(45, 35, 66, 0.4) 0 2px 4px,rgba(45, 35, 66, 0.3) 0 7px 13px -3px,#D6D6E7 0 -3px 0 inset;
        box-sizing: border-box;
        color: #36395A!important;
        font-weight: 500;
        cursor: pointer;
        display: inline-flex;
        height: 70px;
        justify-content: center;
        line-height: 1;
        list-style: none;
        overflow: hidden;
        padding-left: 16px;
        padding-right: 16px;
        position: relative;
        text-align: left;
        text-decoration: none;
        transition: box-shadow .15s,transform .15s;
        user-select: none;
        -webkit-user-select: none;
        touch-action: manipulation;
        white-space: nowrap;
        will-change: box-shadow,transform;
        font-size: 18px;
    }

    .btn-cust:disabled{
        color: unset;
        cursor: unset!important;
    }

    .btn-cust:focus:not([disabled]) {
        box-shadow: #D6D6E7 0 0 0 1.5px inset, rgba(45, 35, 66, 0.4) 0 2px 4px, rgba(45, 35, 66, 0.3) 0 7px 13px -3px, #D6D6E7 0 -3px 0 inset;
    }

    .btn-cust:hover:not([disabled]) {
        box-shadow: rgba(45, 35, 66, 0.4) 0 4px 8px, rgba(45, 35, 66, 0.3) 0 7px 13px -3px, #D6D6E7 0 -3px 0 inset;
        background: #ededed;
        transform: translateY(-2px);
    }

    .btn-cust:active:not([disabled]) {
        box-shadow: #D6D6E7 0 3px 7px inset;
        transform: translateY(2px);
    }
</style>
@endsection

@section('content')
    <script src="{{ url('js/home.js?time=') . rand() }}"></script>

    <div class="container-fluid">
        <div class="row">
            <div class="col-12 col-lg-6 offset-lg-3">
                <div class="card shadow mb-4">
                    <div class="card-body">
                        <div class="form-group d-flex flex-row align-items-center" style="gap: 20px">
                            <label for="tanggal_start_so" class="text-nowrap m-0">Tanggal SO :</label>
                            <input type="date" class="form-control" id="tanggal_start_so" name="tanggal_start_so">
                        </div>
                        <button class="btn btn-cust w-100 my-2" id="start_data_so" onclick="actionStartDataSO()">START PERSIAPAN DATA SO</button>
                        <button class="btn btn-cust w-100 my-2" id="copy_data_so" onclick="actionCopyMasterLokasiSO()">COPY MASTER LOKASI KE LOKASI SO</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('page-script')
<script>
    $(document).ready(function(){
        setDateNow('#tanggal_start_so');
    });

    function actionStartDataSO(){
        Swal.fire({
            title: 'Yakin?',
            text: `Apakah anda yakin ingin memulai SO, Dan Sudah Yakin untuk Tanggal SO yang dipilih ?`,
            icon: 'warning',
            showCancelButton: true,
        })
        .then((result) => {
            if (result.value) {
                $('#modal_loading').modal('show');
                $.ajax({
                    url: `/initial-so/action/start-data-so`,
                    type: "POST",
                    data: {tanggal_start_so: $('#tanggal_start_so').val()},
                    success: function(response) {
                        setTimeout(function () { $('#modal_loading').modal('hide'); }, 500);
                        Swal.fire('Success!',response.message,'success').then(function(){
                            location.reload();
                        });
                    }, error: function(jqXHR, textStatus, errorThrown) {
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
        })
    }

    function nextAction(){
        $('#modal_loading').modal('show');
        $.ajax({
            url: `/initial-so/action/copy-master-lokasi-so/next`,
            type: "POST",
            data: {tanggal_start_so: $('#tanggal_start_so').val()},
            success: function(response) {
                setTimeout(function () { $('#modal_loading').modal('hide'); }, 500);
                Swal.fire({
                    text: response.message,
                    icon: "success"
                });
            }, error: function(jqXHR, textStatus, errorThrown) {
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

    // belum ditest
    function actionCopyMasterLokasiSO(){
        Swal.fire({
            title: 'Yakin?',
            text: `Apakah anda yakin ingin Copy Master Lokasi SO, Dan Sudah Yakin untuk Tanggal SO yang dipilih ?`,
            icon: 'warning',
            showCancelButton: true,
        })
        .then((result) => {
            if (result.value) {
                $('#modal_loading').modal('show');
                $.ajax({
                    url: `/initial-so/action/copy-master-lokasi-so`,
                    type: "POST",
                    data: {tanggal_start_so: $('#tanggal_start_so').val()},
                    xhrFields: {
                        responseType: 'blob' // Important for binary data
                    },
                    success: function(response) {
                        $('#modal_loading').modal('hide')
                        var blob = new Blob([response]);
                        var link = document.createElement('a');
                        link.href = window.URL.createObjectURL(blob);
                        link.download = 'INITIAL SO.zip';
                        document.body.appendChild(link);
                        link.click();
                        document.body.removeChild(link);
                        Swal.fire({
                            title: 'Yakin?',
                            text: `Apakah anda yakin ingin meng-copy Master Lokasi ke Lokasi SO?`,
                            icon: 'warning',
                            showCancelButton: true,
                        })
                        .then((result) => {
                            if (result.value) {
                                nextAction();
                            }
                        });
                    }, error: function(jqXHR, textStatus, errorThrown) {
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
        })
    }

    function checkPersiapanDataSo(){
        setTimeout(function () { $('#modal_loading').modal('show'); }, 800);
        $.ajax({
            url: `/initial-so/action/check-persiapan-data-so`,
            type: "GET",
            data: {tanggal_start_so: $('#tanggal_start_so').val()},
            success: function(response) {
                setTimeout(function () { $('#modal_loading').modal('hide'); }, 500);
                if(response.data){
                    let date = moment($('#tanggal_start_so').val(), 'YYYY-MM-DD');
                    date = date.format('DD/MM/YYYY');
                    $('#start_data_so').prop('disabled', true);
                    $('#start_data_so').text(`Proses Persiapan Data SO ${date} sudah selesai`);
                } else {
                    $('#start_data_so').prop('disabled', false);
                    $('#start_data_so').text(`START PERSIAPAN DATA SO`);
                }
            }, error: function(jqXHR, textStatus, errorThrown) {
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

    $('#tanggal_start_so').change(function(){
        checkPersiapanDataSo();
    });
</script>
@endpush