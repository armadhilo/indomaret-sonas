@extends('layouts.master')
@section('title')
    <h1 class="pagetitle">PERINCIAN BASO SEMENTARA</h1>
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
                            <h5 class="m-0">Perincian BASO Sementara</h5>
                        </div>
                        <form id="form_report">
                            <div class="form-group d-flex align-items-center">
                                <label class="label-form" for="divisi">Divisi <span>:</span></label>
                                <div class="d-flex align-items-center" style="gap: 20px">
                                    <input type="text" class="form-control">
                                    S/D
                                    <input type="text" class="form-control">
                                </div>
                            </div>
                            <div class="form-group d-flex align-items-center">
                                <label class="label-form" for="departemen">Departemen <span>:</span></label>
                                <div class="d-flex align-items-center" style="gap: 20px">
                                    <input type="text" class="form-control">
                                    S/D
                                    <input type="text" class="form-control">
                                </div>
                            </div>
                            <div class="form-group d-flex align-items-center">
                                <label class="label-form" for="kategori">Kategori <span>:</span></label>
                                <div class="d-flex align-items-center" style="gap: 20px">
                                    <input type="text" class="form-control">
                                    S/D
                                    <input type="text" class="form-control">
                                </div>
                            </div>
                            <div class="form-group d-flex align-items-center">
                                <label class="label-form" for="plu">PLU <span>:</span></label>
                                <div class="d-flex align-items-center" style="gap: 20px">
                                    <input type="text" class="form-control">
                                    S/D
                                    <input type="text" class="form-control">
                                </div>
                            </div>
                            <div class="form-group d-flex align-items-center">
                                <label class="label-form" for="jenis_barang">Jenis Barang <span>:</span></label>
                                <div class="d-flex align-items-center" style="gap: 20px">
                                    <input type="text" id="jenis_barang" name="jenis-barang" class="form-control single-word-input" style="width: 50px">
                                    <p class="m-0">[ B - Baik / T - Retur / R - Rusak ]</p>
                                </div>
                            </div>
                            <div class="form-group d-flex align-items-center">
                                <label class="label-form" for="selisih_so">Selisih SO <span>:</span></label>
                                <div class="d-flex align-items-center" style="gap: 20px">
                                    <input type="text" id="selisih_so" name="selisih-so" class="form-control single-word-input" style="width: 50px">
                                    <p class="m-0">[ 1 - ALL / 2 - < (-1) juta / 3 - > (1) juta ]</p>
                                </div>
                            </div>
                            <div class="form-group d-flex align-items-center">
                                <label class="form-checkbox" for="rpt_audit">
                                    <input type="checkbox" id="rpt_audit" class="form-control">
                                    Rpt Audit
                                </label>
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
        $('.single-word-input').on('input', function(){
            var words = $(this).val().split(' ')[0];
            if (words.length > 1) {
                $(this).val(words[0]);
            }
            // Capitalize the input
            $(this).val($(this).val().charAt(0).toUpperCase() + $(this).val().slice(1));
        });
    });
</script>
<script src="{{ asset('js/report-action.js') }}"></script>
@endpush