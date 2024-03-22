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

    <div class="modal fade" role="dialog" id="modal_lokasi" data-keyboard="false" data-backdrop="static">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header br">
                    <h5 class="modal-title">Help Lokasi SO</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <button class="btn btn-primary" style="margin-left: 20px; padding: .375rem 1.3rem; position: absolute; z-index: 1500" onclick="tb_lokasi_so.ajax.reload();">Refresh</button>
                    <div class="table-responsive">
                        <table class="table table-striped table-hover datatable-dark-primary w-100" id="tb_lokasi_so" style="margin: 20px">
                            <thead>
                                <tr>
                                    <th>Kode Rak</th>
                                    <th>Kode SubRak</th>
                                    <th>Tipe Rak</th>
                                    <th>Shelving Rak</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer bg-whitesmoke br">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('page-script')
<script>
    $(document).ready(function(){
        tb_lokasi_so = $('#tb_lokasi_so').DataTable({
            "lengthChange": false,
            processing: true,
            ajax: {
                url: '/report/addon/get-lokasi',
                type: 'GET'
            },
            columnDefs: [
                { className: 'text-center', targets: [0,1,2,3] },
            ],
            columns: [
                { data: 'lso_koderak' },
                { data: 'lso_kodesubrak' },
                { data: 'lso_tiperak' },
                { data: 'lso_shelvingrak' },
                { data: null },
            ],
            rowCallback: function (row, data) {
                $('td:eq(4)', row).html(`<button class="btn btn-info btn-sm mr-1" onclick="pilihLokasi('${data.lso_koderak}', '${data.lso_kodesubrak}', '${data.lso_tiperak}', '${data.lso_shelvingrak}')">Pilih Lokasi</button>`);
            }
        });

        $(document).keydown(function(event){
            if(event.keyCode == 112) {
                event.preventDefault();
                showModalLokasi();
            }
        });

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

    function pilihLokasi(koderak, kodeSubrak, tipeRak, Shelving){
        $("#modal_lokasi").modal("hide");
        $("[name=raksubrak]").val(`${koderak}.${kodeSubrak}`);
    }

    function showModalLokasi(){
        $("#modal_lokasi").modal("show");
    }
</script>

<script src="{{ asset('js/report-action.js') }}"></script>
@endpush