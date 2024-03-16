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
                            <input type="hidden" name="tanggal_start_so" id="tanggal_start_so" value="{{ $tglSO }}">
                            <input type="hidden" name="tahap" id="tahap" value="{{ $tahap }}">
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
        @endif

        tb = $('#tb').DataTable({
            language: {
                emptyTable: "<div class='datatable-no-data' style='color: #ababab'>Tidak Ada Data</div>",
            },
            ajax: {
                url: '/set-limit-so/action/datatables/' + $("#tahap").val() + '/' + $("#tanggal_start_so").val(),
                type: 'GET',
            },
            columns: [
                { data: 'lso_nourut'},
                { data: 'prd_prdcd'},
                { data: 'prd_deskripsipanjang'},
            ],
            data: [],
            columnDefs: [
                { className: 'text-center-vh', targets: '_all' },
            ],
            paging: false,
            searching: false,
            info: false,
            order: [],
        });
    });

</script>

@endpush
