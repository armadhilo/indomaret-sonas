@extends('layouts.master')
@section('title')
    <h1 class="pagetitle">MONITORING SO</h1>
@endsection

@section('css')
<link rel="stylesheet" href="{{ asset('plugin/jstree/jstree.css') }}">
@endsection

@section('content')
    <script src="{{ url('js/home.js?time=') . rand() }}"></script>

    <div class="container-fluid">
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
                            <table class="table table-striped table-hover w-100 datatable-dark-primary table-center" id="tb_kkso" style="margin-top: 20px">
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
    $.ajax({
        url: '/monitoring-so/get-monitoring', 
        type: 'GET',
        dataType: 'json',
        success: function(data) {
           $('#jstree').jstree({
                'core': {
                    'check_callback': true,
                    'data': [{
                        "text": "TOKO: " + data.toko.progress,
                        "children": data.detail_toko.map(function(item) {
                            return {
                                "text": item.lso_koderak + ": " + item.progress,
                                "data": {
                                    "koderak": item.lso_koderak
                                }
                            };
                        })
                    }, {
                        "text": "GUDANG: " + data.gudang.progress,
                        "children": data.detail_gudang.map(function(item) {
                            return {
                                "text": item.lso_koderak + ": " + item.progress,
                                "data": {
                                    "koderak": item.lso_koderak
                                }
                            };
                        })
                    }]
                }
            }).on('dblclick.jstree', function (e) {
                var node = $(e.target).closest('.jstree-node').length ? $(e.target).closest('.jstree-node') : $(e.target);
                var nodeId = node.attr('id');
                var instance = $.jstree.reference(this);
                var koderak = instance.get_node(nodeId).data.koderak;
                if (koderak) {
                    console.log('Double-clicked Koderak:', koderak);
                    $.ajax({
                        url: '/monitoring-so/show-level/' + koderak,
                        type: 'GET',
                        success: function(response) {
                            // Append received data as child nodes
                            if (response.data) {
                                var children = response.data.map(function(item, index) {
                                    console.log(item);
                                    return {
                                        "text": item.lso_kodesubrak, // Use appropriate property for child node text
                                        "data": { // You can include additional data if needed
                                            "id": item.id,
                                            "kodesubrak": koderak + "/" +item.lso_kodesubrak
                                        }
                                    };
                                });
                                if (instance && instance.is_ready) {
                                    console.log("Before create_node");
instance.create_node(nodeId, { text: "Test Child Node" }, 'last', function (newNode, response) {
    console.log("Inside create_node callback");
    if (response && response.status === true) {
        console.log("Node creation successful:", newNode);
    } else {
        console.error("Node creation failed:", response ? response.error : "Unknown error");
    }
});
} else {
    console.error("jstree instance is not ready");
}

                            }
                        },
                        error: function(xhr, status, error) {
                            console.error('Error:', error);
                        }
                    });

                }
            });
        }
    });
});
</script>

@endpush
