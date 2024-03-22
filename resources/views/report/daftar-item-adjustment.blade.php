@extends('layouts.master')
@section('title')
    <h1 class="pagetitle">DAFTAR ITEM ADJUSTMENT</h1>
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
        width: 115px;
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
                <div class="card shadow mb-4 vertical-center w-100" id="report_container">
                    <div class="card-body" id="report_content">
                        <div id="header_tb">
                            <h5 class="m-0">Daftar Item yang sudah di Adjustment</h5>
                        </div>
                        <form id="form_report" method="POST" action="/report/daftar-item-adjustment/show-pdf">
                            <div class="form-group d-flex align-items-center">
                                <label class="label-form" for="tanggal_start_so">Tanggal SO <span>:</span></label>
                                <div class="d-flex align-items-center" style="gap: 20px;">
                                    <input type="date" name="tanggal_start_so" value="{{ $TanggalSO }}" class="form-control" style="width: 207px">
                                </div>
                            </div>
                            <div class="form-group d-flex align-items-center">
                                <label class="label-form" for="tanggal_adj">Tanggal Adj <span>:</span></label>
                                <div class="d-flex align-items-center" style="gap: 20px;">
                                    <input type="date" name="tanggal_adjust_start" class="form-control" style="width: 207px">
                                    S/D
                                    <input type="date" name="tanggal_adjust_end" class="form-control" style="width: 207px">
                                </div>
                            </div>
                            <div class="form-group d-flex align-items-center">
                                <label class="label-form" for="plu">PLU <span>:</span></label>
                                <div class="d-flex align-items-center" style="gap: 20px;">
                                    <input type="text" class="form-control" name="plu1">
                                    S/D
                                    <input type="text" class="form-control" name="plu2">
                                </div>
                            </div>
                            <div class="form-group d-flex align-items-center">
                                <label class="label-form" for="jenis_barang">Jenis Barang <span>:</span></label>
                                <div class="d-flex align-items-center" style="gap: 20px">
                                    <input type="text" id="jenis_barang" name="jenis_barang" class="form-control" style="width: 70px">
                                    <p class="m-0">[ B - Baik / T - Retur / R - Rusak ]</p>
                                </div>
                            </div>
                            <div class="float-right">
                                <button type="submit" class="btn btn-primary mr-3" style="padding: 10px 50px;">OK</button>
                                <button type="button" class="btn btn-danger" style="padding: 10px 50px;" onclick="cancelAction()">Cancel</button>
                            </div>
                        </form>
                        <div id="header_tb" style="margin-top: 80px; margin-bottom: 0">
                            <h5 class="m-0" style="font-weight: 700; font-size: 1rem;">*F1 - Help PLU</h5>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @include('layouts.modal-plu')
@endsection

@push('page-script')
<script src="{{ asset('js/report-action.js') }}"></script>
<script>
    $(document).ready(function(){
        initializeHelpPLU();
        $(document).keydown(function(event){
            if(event.keyCode == 112) {
                event.preventDefault();
                showModalPLU();
            }
        });
    });
</script>
@endpush