@extends('layouts.master')
@section('title')
    <h1 class="pagetitle">PROSES BA SO</h1>
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
                        @if(isset($BtnDraftLHSO) && $BtnDraftLHSO )
                        <button class="btn btn-cust w-100 my-2" id="start_data_so" onclick="actionDraft()">{{ $BtnDraftLHSOText }}</button>
                        @endif
                        @if(isset($BtnProsesBASO) && $BtnProsesBASO)
                        <button class="btn btn-cust w-100 my-2" id="copy_data_so" onclick="prosesBaSo()">{{ $BtnProsesBASOText }}</button>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('page-script')
<script>
    @if(isset($check_error) && !empty($check_error))
        let check_error = "{{ $check_error }}";
        if(check_error){
            Swal.fire({
                title: 'Peringatan...!',
                text: `${check_error}`,
                icon: 'warning',
                showConfirmButton: true,
                allowOutsideClick: false,
                confirmButtonText: 'Kembali Ke Initial SO',
            }).then(() => {
                window.location.href = '/initial-so';
            });
        }
    @else
    function actionDraft(){
        Swal.fire({
            title: 'Yakin?',
            text: `Apakah anda yakin ingin melakukan Draft LHSO ?`,
            icon: 'warning',
            showCancelButton: true,
        })
        .then((result) => {
            if (result.value) {
                $('#modal_loading').modal('show');
                $.ajax({
                    url: `/proses-ba-so/action/draft-action`,
                    type: "POST",
                    data: {tanggal_start_so: "{{ $tgl_so }}"},
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

    function prosesBaSo(){
        Swal.fire({
            title: 'Yakin?',
            text: `Apakah anda yakin ingin melakukan proses BA SO ?`,
            icon: 'warning',
            showCancelButton: true,
        })
        .then((result) => {
            if (result.value) {
                $('#modal_loading').modal('show');
                $.ajax({
                    url: `/proses-ba-so/action/proses-ba-so`,
                    type: "POST",
                    data: {tanggal_start_so: "{{ $tgl_so }}"},
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
    @endif
</script>
@endpush