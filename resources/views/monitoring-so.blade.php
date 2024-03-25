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
@endsection

@push('page-script')
<script src="{{ asset('plugin/jstree/jstree.min.js') }}"></script>
<script>
$(document).ready(function() {
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
                // window.location.href = '/initial-so';
        initializePage();   

            });
        }
    @else
        initializePage();   
    @endif
});

function initializePage(){
    tb_monitoring = $('#tb_monitoring').DataTable({
        processing: true,
        language: {
            emptyTable: "<div class='datatable-no-data' style='color: #ababab'>Tidak Ada Data</div>",
        },
        columns: [
            { data: 'lso_nourut'},
            { data: 'prd_prdcd'},
            { data: 'prd_deskripsipanjang'},
            { data: 'prd_unit'},
            { data: 'prd_unit'},
            { data: 'prd_unit'},
            { data: 'prd_unit'},
        ],
        columnDefs: [
            { className: 'text-center-vh', targets: '_all' },
            { "width": "25%", "targets": 2 },
        ],
        data: [],
    });

    $.ajax({
        url: '/monitoring-so/get-monitoring', 
        type: 'GET',
        dataType: 'json',
        success: function(data) {
            $('#jstree').jstree({
                'core': {
                    'check_callback': true,
                    'data': [
                        {
                            "text": "TOKO: " + data.toko.progress,
                            "children": data.detail_toko.map(function (item) {
                                return {
                                    "text": item.lso_koderak + ": " + item.progress,
                                    "data": {
                                        "data": item.lso_koderak
                                    },
                                    "children": item.data_subrak.map(function (subrak) {
                                        return {
                                            "text": "Subrak: " + subrak.subrak + `&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; <button class="btn btn-xs btn-print-action btn-sm btn-primary"><i class="fas fa-print"></i></button>`,
                                            "data": {
                                                "data": subrak.subrak
                                            },
                                            "children": subrak.data_tiperak.map(function (tiperak) {
                                                return {
                                                    "text": "Tiperak: " + tiperak.tiperak + `&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; <button class="btn btn-xs btn-print-action btn-sm btn-primary"><i class="fas fa-print"></i></button>`,
                                                    "data": {
                                                        "data": tiperak.tiperak
                                                    },
                                                    "children": tiperak.data_shelving.map(function (shelving) {
                                                        return {
                                                            "text": "Shelving: " + shelving.shelving + `&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; <button class="btn btn-xs btn-print-action btn-sm btn-primary"><i class="fas fa-print"></i></button>`,
                                                            "data": {
                                                                "data": shelving.shelving
                                                            },
                                                            "li_attr": {
                                                                "class": "no-hover-style"
                                                            }
                                                        };
                                                    })
                                                };
                                            })
                                        };
                                    })
                                };
                            })
                        },
                        {
                            "text": "GUDANG: " + data.gudang.progress,
                            "children": data.detail_toko.map(function (item) {
                                return {
                                    "text": item.lso_koderak + ": " + item.progress,
                                    "data": {
                                        "data": item.lso_koderak
                                    },
                                    "children": item.data_subrak.map(function (subrak) {
                                        return {
                                            "text": "Subrak: " + subrak.subrak + `&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; <button class="btn btn-xs btn-print-action btn-sm btn-primary"><i class="fas fa-print"></i></button>`,
                                            "data": {
                                                "data": subrak.subrak
                                            },
                                            "children": subrak.data_tiperak.map(function (tiperak) {
                                                return {
                                                    "text": "Tiperak: " + tiperak.tiperak + `&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; <button class="btn btn-xs btn-print-action btn-sm btn-primary"><i class="fas fa-print"></i></button>`,
                                                    "data": {
                                                        "data": tiperak.tiperak
                                                    },
                                                    "children": tiperak.data_shelving.map(function (shelving) {
                                                        return {
                                                            "text": "Shelving: " + shelving.shelving + `&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; <button class="btn btn-xs btn-print-action btn-sm btn-primary"><i class="fas fa-print"></i></button>`,
                                                            "data": {
                                                                "data": shelving.shelving
                                                            },
                                                            "li_attr": {
                                                                "class": "no-hover-style"
                                                            }
                                                        };
                                                    })
                                                };
                                            })
                                        };
                                    })
                                };
                            })
                        }
                    ]
                },
                'types': {
                    'default': {
                        'icon': 'custom-icon'
                    }
                }
            });

            $('#jstree').on('dblclick.jstree', function(event) {
                var node = $(event.target).closest('.jstree-node');
                var nodeData = $('#jstree').jstree(true).get_node(node);

                if (nodeData.parents.length === 2){
                    reinitializeDatatables( nodeData.data.data, null, null, null);
                } else if (nodeData.parents.length === 3){
                    let koderak = $('#jstree').jstree(true).get_node(nodeData.parent);
                    reinitializeDatatables( koderak.data.data, nodeData.data.data, null, null);
                } else if (nodeData.parents.length === 4){
                    let koderak = $('#jstree').jstree(true).get_node(nodeData.parent);
                    let subrak = $('#jstree').jstree(true).get_node(nodeData.parents[1]);
                    reinitializeDatatables( koderak.data.data, subrak.data.data, nodeData.data.data, null);
                } else if (nodeData.parents.length === 5){
                    let koderak = $('#jstree').jstree(true).get_node(nodeData.parent);
                    let subrak = $('#jstree').jstree(true).get_node(nodeData.parents[1]);
                    let tiperak = $('#jstree').jstree(true).get_node(nodeData.parents[2]);
                    reinitializeDatatables( koderak.data.data, subrak.data.data, tiperak.data.data, nodeData.data.data);
                }
            });
        }
    });
}

function reinitializeDatatables(koderak, subrak, tiperak, shelvingrak){
    tb_monitoring.clear().draw();
    $('.datatable-no-data').css('color', '#F2F2F2');
    $('#loading_datatable').removeClass('d-none');
    $.ajax({
        url: "/monitoring-so/datatables",
        type: "GET",
        data: {tanggal_start_so: $("#tanggal_start_so").val(), kodeRak: koderak, KodeSubrak: subrak, Tiperak: tiperak, Shelvingrak: shelvingrak},
        success: function(response) {
            $('#loading_datatable').addClass('d-none');
            $('.datatable-no-data').css('color', '#ababab');
            tb_monitoring.rows.add(response.data).draw();
        }, error: function(jqXHR, textStatus, errorThrown) {
            setTimeout(function () { $('#loading_datatable').addClass('d-none'); }, 500);
            $('.datatable-no-data').css('color', '#ababab');
            Swal.fire({
                text: "Oops! Terjadi kesalahan segera hubungi tim IT (" + errorThrown + ")",
                icon: "error"
            });
        }
    });
}

$("#jstree").on("click", ".btn-print-action", function(event) {
    var node = $(event.target).closest('.jstree-node');
    var nodeData = $('#jstree').jstree(true).get_node(node);
    if (nodeData.parents.length === 2){
        printStrukAction( nodeData.data.data, null, null, null);
    } else if (nodeData.parents.length === 3){
        let koderak = $('#jstree').jstree(true).get_node(nodeData.parent);
        printStrukAction( koderak.data.data, nodeData.data.data, null, null);
    } else if (nodeData.parents.length === 4){
        let koderak = $('#jstree').jstree(true).get_node(nodeData.parent);
        let subrak = $('#jstree').jstree(true).get_node(nodeData.parents[1]);
        printStrukAction( koderak.data.data, subrak.data.data, nodeData.data.data, null);
    } else if (nodeData.parents.length === 5){
        let koderak = $('#jstree').jstree(true).get_node(nodeData.parent);
        let subrak = $('#jstree').jstree(true).get_node(nodeData.parents[1]);
        let tiperak = $('#jstree').jstree(true).get_node(nodeData.parents[2]);
        printStrukAction( koderak.data.data, subrak.data.data, tiperak.data.data, nodeData.data.data);
    }
});

function printStrukAction(koderak, subrak, tiperak = null, shelvingrak = null){

    var tanggal_start_so = $("#tanggal_start_so").val();
    var url = `/monitoring-so/print-struk-so/${tanggal_start_so}/${koderak}/${subrak}`;

    $('#modal_loading').modal('show');
    $.ajax({
    url: url,
    type: "GET",
    success: function(response) {
        var xhr = new XMLHttpRequest();
        xhr.open('GET', url, true);
        xhr.responseType = 'blob';
        xhr.onload = function() {
            if (this.status === 200) {
                setTimeout(function () { $('#modal_loading').modal('hide'); }, 500);
                var blob = new Blob([this.response], { type: 'application/zip' });
                var link = document.createElement('a');
                link.href = window.URL.createObjectURL(blob);
                link.download = 'MONITORING SO.zip';
                document.body.appendChild(link);
                link.click();
                document.body.removeChild(link);
            }
        };
        xhr.send();
    },
    error: function(jqXHR, textStatus, errorThrown) {
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

</script>

@endpush
