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
                <div class="card shadow mb-4 vertical-center w-100" id="report_container">
                    <div class="card-body" id="report_content">
                        <div id="header_tb">
                            <h5 class="m-0">Perincian BASO Sementara</h5>
                        </div>
                        <form id="form_report" method="POST" action="/report/perincian-baso/show-pdf">
                            <input type="hidden" name="tanggal_start_so" value="{{ $TanggalSO }}">
                            <input type="hidden" name="check_rpt_audit" value="0">
                            <div class="form-group d-flex align-items-center">
                                <label class="label-form" for="divisi">Divisi <span>:</span></label>
                                <div class="d-flex align-items-center" style="gap: 20px">
                                    <input type="text" class="form-control" name="div1">
                                    S/D
                                    <input type="text" class="form-control" name="div2">
                                </div>
                            </div>
                            <div class="form-group d-flex align-items-center">
                                <label class="label-form" for="departemen">Departemen <span>:</span></label>
                                <div class="d-flex align-items-center" style="gap: 20px">
                                    <input type="text" class="form-control" name="dept1">
                                    S/D
                                    <input type="text" class="form-control" name="dept2">
                                </div>
                            </div>
                            <div class="form-group d-flex align-items-center">
                                <label class="label-form" for="kategori">Kategori <span>:</span></label>
                                <div class="d-flex align-items-center" style="gap: 20px">
                                    <input type="text" class="form-control" name="kat1">
                                    S/D
                                    <input type="text" class="form-control" name="kat2">
                                </div>
                            </div>
                            <div class="form-group d-flex align-items-center">
                                <label class="label-form" for="plu">PLU <span>:</span></label>
                                <div class="d-flex align-items-center" style="gap: 20px">
                                    <input type="text" class="form-control" name="plu1">
                                    S/D
                                    <input type="text" class="form-control" name="plu2">
                                </div>
                            </div>
                            <div class="form-group d-flex align-items-center">
                                <label class="label-form" for="jenis_barang">Jenis Barang <span>:</span></label>
                                <div class="d-flex align-items-center" style="gap: 20px">
                                    <input type="text" id="jenis_barang" name="jenis_barang" required class="form-control single-word-input" style="width: 70px">
                                    <p class="m-0">[ B - Baik / T - Retur / R - Rusak ]</p>
                                </div>
                            </div>
                            <div class="form-group d-flex align-items-center">
                                <label class="label-form" for="selisih_so">Selisih SO <span>:</span></label>
                                <div class="d-flex align-items-center" style="gap: 20px">
                                    <input type="text" id="selisih_so" name="selisih_so" required class="form-control single-word-input" style="width: 70px">
                                    <p class="m-0">[ 1 - ALL / 2 - < (-1) juta / 3 - > (1) juta ]</p>
                                </div>
                            </div>
                            <div class="form-group d-flex align-items-center">
                                <label class="form-checkbox" for="rpt_audit">
                                    <input type="checkbox" id="rpt_audit" onclick="$(this).val(this.checked ? 1 : 0)" name="check_rpt_audit" class="form-control">
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
        
        $('#report_content').on('input', "#selisih_so", function(){
            var input = $(this).val().trim(); 
            
            // Ensure input consists of only 1, 2, or 3
            if (!/^[1-3]$/.test(input)) {
                $(this).val('');
            } else {
                $(this).val(input.toUpperCase());
            }
        });
    });

</script>
<script src="{{ asset('js/report-action.js') }}"></script>
@endpush