@extends('layouts.master')
@section('title')
    <h1 class="pagetitle">DAFTAR KKSO ACOST 0</h1>
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

</style>
@endsection

@section('content')
    <script src="{{ url('js/home.js?time=') . rand() }}"></script>

    <div class="container-fluid">
        <div class="row">
            <div class="col-md-5 col-lg-4">
                @include('layouts.report-menu')
            </div>
            <div class="col-md-5 col-lg-6 offset-1">
                <div class="card shadow mb-4 vertical-center w-100">
                    <div class="card-body">
                        <div id="header_tb">
                            <h5 class="m-0">KKSO Dengan Avg Cost 0</h5>
                        </div>
                        <form action="">
                            <div class="form-group d-flex align-items-center">
                                <label class="label-form" for="kode_rak">Kode Rak <span>:</span></label>
                                <div class="d-flex align-items-center" style="gap: 20px">
                                    <input type="text" class="form-control">
                                    S/D
                                    <input type="text" class="form-control">
                                </div>
                            </div>
                            <div class="form-group d-flex align-items-center">
                                <label class="label-form" for="kode_subrak">Kode Sub Rak <span>:</span></label>
                                <div class="d-flex align-items-center" style="gap: 20px">
                                    <input type="text" class="form-control">
                                    S/D
                                    <input type="text" class="form-control">
                                </div>
                            </div>
                            <div class="form-group d-flex align-items-center">
                                <label class="label-form" for="tipe_rak">Tipe Rak <span>:</span></label>
                                <div class="d-flex align-items-center" style="gap: 20px">
                                    <input type="text" class="form-control">
                                    S/D
                                    <input type="text" class="form-control">
                                </div>
                            </div>
                            <div class="form-group d-flex align-items-center">
                                <label class="label-form" for="kode_shelving">Kode Shelving <span>:</span></label>
                                <div class="d-flex align-items-center" style="gap: 20px">
                                    <input type="text" class="form-control">
                                    S/D
                                    <input type="text" class="form-control">
                                </div>
                            </div>
                            <div class="form-group d-flex align-items-center">
                                <label class="label-form" for="jenis-barang">Jenis Barang <span>:</span></label>
                                <div class="d-flex align-items-center" style="gap: 20px">
                                    <input type="text" id="jenis_barang" name="jenis-barang" class="form-control" style="width: 50px">
                                    <p class="m-0">[ B - Baik / T - Retur / R - Rusak ]</p>
                                </div>
                            </div>
                            <div class="float-right">
                                <button type="submit" class="btn btn-primary mr-3" style="padding: 10px 50px;">OK</button>
                                <button type="button" class="btn btn-danger" style="padding: 10px 50px;" onclick="cancelAction()">Cancel</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('page-script')
<script>
    $(document).ready(function(){
        $('#jenis_barang').on('input', function(){
            var words = $(this).val().split(' ')[0];
            if (words.length > 1) {
                $(this).val(words[0]);
            }
            // Capitalize the input
            $(this).val($(this).val().charAt(0).toUpperCase() + $(this).val().slice(1));
        });
    });

    function cancelAction(){
        Swal.fire({
            title: 'Cancel action saat ini ?',
            text: `Semua input yg anda masukkan akan dihapus...`,
            icon: 'warning',
            showCancelButton: true,
            cancelButtonText: 'Kembali',
            confirmButtonText: 'Ya, Cancel'
        })
        .then((result) => {
            if (result.value) {
                window.location.href = '/report/';
            }
        });
    }
</script>
@endpush