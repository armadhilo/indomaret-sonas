@extends('layouts.master')
@section('title')
    <h1 class="pagetitle">LOKASI SO</h1>
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
        width: 200px;
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
                            <h5 class="m-0">Lokasi SO</h5>
                        </div>
                        <form id="form_report" method="POST" action="/report/lokasi-so/show-pdf">
                            <input type="hidden" name="tanggal_start_so" value="{{ $TanggalSO }}">
                            <div class="form-group d-flex align-items-center">
                                <label class="label-form" for="kode">Kode Rak dan SubRak <span>:</span></label>
                                <div class="d-flex align-items-center" style="gap: 20px;">
                                    <input type="text" class="form-control" name="raksubrak" style="width: 180px">
                                    <p class="m-0 fw-semibold">[ R01.01 / Kosong = All ]</p>
                                </div>
                            </div>
                            <div class="form-group d-flex align-items-center">
                                <label class="label-form" for="sarana_so">Sarana SO <span>:</span></label>
                                <div class="d-flex align-items-center" style="gap: 20px;">
                                    <input type="text" class="form-control" name="sarana" id="sarana_so" style="width: 180px">
                                    <p class="m-0 fw-semibold">[ H / K ]</p>
                                </div>
                            </div>
                            <div class="float-right">
                                <button type="submit" class="btn btn-primary mr-3" style="padding: 10px 50px;">OK</button>
                                <button type="button" class="btn btn-danger" style="padding: 10px 50px;" onclick="cancelAction()">Cancel</button>
                            </div>
                        </form>
                        <div id="header_tb" style="margin-top: 80px; margin-bottom: 0">
                            <h5 class="m-0" style="font-weight: 700; font-size: 1rem;">*F1 - Help Lokasi SO</h5>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('page-script')
<script>
    $(document).ready(function(){
        $('#report_content').on('input', "#sarana_so", function(){
            var input = $(this).val().trim(); 
            
            // Ensure input consists of only 'H' or 'K'
            if (!/^[HKhk]$/.test(input)) {
                $(this).val('');
            } else {
                $(this).val(input.toUpperCase());
            }
        })
    });
</script>

<script src="{{ asset('js/report-action.js') }}"></script>
@endpush