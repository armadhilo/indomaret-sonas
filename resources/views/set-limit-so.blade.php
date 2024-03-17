@extends('layouts.master')
@section('title')
    <h1 class="pagetitle">SET LIMIT SO</h1>
@endsection

@section('css')
<style>
    .header{
        padding: 0 15px
    }

    input.form-no-style{
        border: none!important;
        background-color: transparent!important;
        box-shadow: none!important;
        outline: none;
        padding: 0;
        text-align: center;
    }

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
            <div class="col-12">
                <div class="card shadow mb-4">
                    <div class="card-body">
                        <div class="header">
                            <div class="form-group d-flex justify-content-center" style="gap: 35px">
                                <button type="button" onclick="downloadExcelAction();" class="btn btn-cust my-2">Download Excel</button>
                                <button type="button" onclick="uploadExcelAction();" class="btn btn-cust my-2">Upload Excel</button>
                            </div>
                        </div>
                        <div class="body">
                            <div class="position-relative">
                                <table class="table table-striped table-hover w-100 datatable-dark-primary table-center" id="tb" style="margin-top: 20px">
                                    <thead>
                                        <tr>
                                            <th>No. Urut</th>
                                            <th>PLU</th>
                                            <th>Deskripsi</th>
                                            <th>Lokasi</th>
                                            <th>Divisi</th>
                                            <th>Departemen</th>
                                            <th>Kategori</th>
                                            <th>Toko</th>
                                            <th>Gudang</th>
                                            <th>Total Plano</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    </tbody>
                                </table>
                                <button class="btn btn-lg btn-primary d-none" id="loading_datatable" style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%);" type="button" disabled>
                                    <span class="spinner-grow spinner-grow-sm" role="status" aria-hidden="true"></span>
                                    Loading...
                                </button>
                            </div>
                        </div>
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
            return;
        @endif
        
        tb = $('#tb').DataTable({
            language: {
                emptyTable: "<div class='datatable-no-data' style='color: #ababab'>Tidak Ada Data</div>",
            },
            ajax: {
                url: '/set-limit-so/action/datatables/' + "{{ $tahap }}" + '/' + "{{ $tglSO }}",
                type: 'GET',
            },
            columns: [
                { data: 'DT_RowIndex'},
                { data: 'plu'},
                { data: 'deskripsi'},
                { data: 'lso_lokasi'},
                { data: 'divisi'},
                { data: 'departement'},
                { data: 'kategori'},
                { data: 'areatoko'},
                { data: 'areagudang'},
                { data: 'total'},
            ],
            data: [],
            columnDefs: [
                { className: 'text-center-vh', targets: '_all' },
                { "width": "20%", "targets": 2 }
            ],
            paging: false,
            searching: false,
            ordering: false,
            info: false,
            order: [],
        });
    });

    function downloadExcelAction(){
        Swal.fire({
            title: `Yakin ?`,
            text: `yakin akan download excel untuk SET LIMIT SO..?`,
            icon: 'warning',
            showCancelButton: true,
            cancelButtonText: 'Batalkan',
            confirmButtonText: 'Ya'
        })
        .then((result) => {
            if (result.value) {
                $('#modal_loading').modal('show');
                $.ajax({
                    url: "/report/lpp-month-end/action/cetak-lpp",
                    type: "POST",
                    data: {plu: plu_list, jenis_barang: $("[name=jenis_barang]").val(), periode: $("#periode").val()},
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

@endpush
