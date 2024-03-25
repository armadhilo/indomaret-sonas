@extends('layouts.master')
@section('title')
    <h1 class="pagetitle">MONITORING SO</h1>
@endsection

@section('css')
<link rel="stylesheet" href="{{ asset('plugin/jstree/jstree.css') }}">
<style>
    a.jstree-anchor.jstree-hovered[aria-level="5"] {
        background: transparent!important;
    }
    a.jstree-anchor[aria-level="5"] {
        width: 115px!important;
    }

    .btn-xs{
        padding: 2px 8px;
        font-size: .8rem;
    }
</style>
@endsection

@section('content')
    <script src="{{ url('js/home.js?time=') . rand() }}"></script>

    <div class="container-fluid">
        <input type="hidden" value="{{ $tgl_so }}" id="tanggal_start_so">
        <div class="row">
            <div class="col-5">
                <div class="card shadow mb-4">
                    <div class="card-body">
                        <div id="jstree"></div>
                    </div>
                </div>
            </div>
            <div class="col-7">
                <div class="card shadow mb-4">
                    <div class="card-body">
                        <div class="position-relative">
                            <table class="table table-striped table-hover w-100 datatable-dark-primary table-center" id="tb_monitoring" style="margin-top: 20px">
                                <thead>
                                    <tr>
                                        <th>No. Urut</th>
                                        <th>PLU</th>
                                        <th>Deskripsi</th>
                                        <th>Unit</th>
                                        <th>CTN / KG</th>
                                        <th>PCS / Gram</th>
                                        <th>Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('page-script')
<script src="{{ asset('plugin/jstree/jstree.min.js') }}"></script>
<script>
$(document).ready(function() {
    @if(isset($check_error['file']) && !empty($check_error['file']))
        let check_error = "{{ $check_error['file'] }}";
        if(check_error){
            Swal.fire({
                title: 'Peringatan...!',
                text: `${check_error}`,
                icon: 'warning',
                showConfirmButton: true,
                allowOutsideClick: false,
            }).then(() => {
                window.location.href = "/monitoring-so";
            });
        }
    @else
        window.location.href = "/monitoring-so";
    @endif
});
</script>

@endpush
