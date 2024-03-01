@extends('layouts.master')
@section('title')
    <h1 class="pagetitle">INPUT MASTER LOKASI </h1>
@endsection

@section('css')
<style>
    #header_tb{
        background: #6E214A;
        padding: 10px 6px ;
        color: white;
        margin-bottom: 15px;
        text-align: center;
        border: 2px groove lightgray;
    }

    input.form-no-style{
        border: none!important;
        background-color: transparent!important;
        box-shadow: none!important;
        outline: none;
        padding: 0;
        text-align: center;
    }

    #header_tb h5{
        font-size: 1rem;
        font-weight: bold;
    }

    .invalid-feedback{
        font-size: 75%;
    }

    #tb tbody tr {
        cursor: pointer;
    }

    table td {
        color: black;
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
                        <form id="form">
                            <div class="header">
                                <div id="header_tb">
                                    <h5 class="m-0">F1 - ADD &nbsp;|&nbsp; F5 - SAVE &nbsp;|&nbsp; ENTER - SEARCH DESC &nbsp;|&nbsp; DEL - DELETE</h5>
                                </div>
                                <div class="form-container">
                                    <input type="date" hidden name="tanggal_start_so" id="tanggal_start_so">
                                    <input type="text" hidden name="kode_rak" id="kode_rak">
                                    <input type="text" hidden name="kode_sub_rak" id="kode_sub_rak">
                                    <input type="text" hidden name="tipe_rak" id="tipe_rak">
                                    <input type="text" hidden name="shelving_rak" id="shelving_rak">
                                    <input type="text" hidden name="jenis_barang" id="jenis_barang">
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
                                                <th>Unit</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        </tbody>
                                    </table>
                                </div>
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
    let tb; 
    $(document).ready(function(){
        var formData = decodeURIComponent(window.location.search.split('=')[1]);
    
        var dataArray = formData.split('&');
        $.each(dataArray, function(index, item) {
            var keyValue = item.split('=');
            $('#' + keyValue[0]).val(keyValue[1]);
        });

        var formDataFromURL = decodeURIComponent(window.location.search.split('=')[1]);
        var dataArray = formDataFromURL.split('&');
        var formDataObject = {};
        $.each(dataArray, function(index, item) {
            var keyValue = item.split('=');
            formDataObject[keyValue[0]] = keyValue[1];
        });

        tb = $('#tb').DataTable({
            ajax: {
                url: '/input-lokasi/action/detail-datatables',
                type: 'GET',
                data: formDataObject,
                dataSrc: function(data) {
                    var newData = [];
                    data.data.forEach(function(item) {
                        $.ajax({
                            url: `/input-lokasi/action/get-desc-plu/${item.lso_prdcd}`,
                            method: 'GET',
                            async: false, // Synchronous AJAX request to wait for response
                            success: function(response) {
                                item.deskripsiPanjang = response.data.prd_deskripsipanjang;
                                item.unit = response.data.prd_unit;
                                newData.push(item);
                            },
                            error: function(xhr, status, error) {
                                console.error('Error fetching description:', error);
                            }
                        });
                    });
                    return newData;
                }
            },
            language: {
                emptyTable: "<div class='datatable-no-data' style='color: #ababab'>Tidak Ada Data</div>",
            },
            columns: [
                { data: 'lso_nourut',
                    render: function(data, type, row) {
                        if (type === 'display' || type === 'filter') {
                            return `<input name="no_urut[]" value="${data}" type="text" class="form-control form-no-style" readonly>`;
                        }
                        return data;
                    }
                },
                { data: 'lso_prdcd',
                    render: function(data, type, row) {
                        if (type === 'display' || type === 'filter') {
                            return `<input name="plu[]" type="text" class="form-control required" value="${data}">`;
                        }
                        return data;
                    }
                },
                { data: 'deskripsiPanjang'},
                { data: 'unit'},
            ],
            columnDefs: [
                { className: 'text-center-vh', targets: '_all' },
                { width: '25%', targets: 1 },
                { width: '50%', targets: 2 },
                { width: '15%', targets: 3 },
            ],
            paging: false,
            searching: false,
            info: false,
            order: [],
        });

        addRowDatatable();
    });

    $(document).keydown(function(event) {
        if (event.keyCode === 112) {
            event.preventDefault();
            addRowDatatable(); 
        }
        else if (event.which == 116) {
            event.preventDefault();
            $("#form").submit();
        }
    });

    function addRowDatatable(){
        if($('#tb tbody tr:not(:has(td.dataTables_empty))').length > 0){
            var content = $('#tb tbody tr:last').find('td:first input').val();
            let addRow = [
                `${parseInt(content) + 1}`,
                '',
                '',
                '',
            ];
            let newRow = $('<tr>');
            addRow.forEach(item => {
                newRow.append(`<td>${item}</td>`);
            });
            $('#tb tbody').append(newRow);
            tb.row.add(newRow).draw();
            return;
        }
        $('#modal_loading').modal('show');
        $.ajax({
            url: `/input-lokasi/action/get-last-number`,
            type: "GET",
            data: $("#form").serialize(),
            success: function(response) {
                setTimeout(function () { $('#modal_loading').modal('hide'); }, 500);
                let addRow = [
                    `${response.data.nourut + $('#tb tbody tr:not(:has(td.dataTables_empty))').length + 1}`,
                    '',
                    '',
                    '',
                ];
                let newRow = $('<tr>');
                addRow.forEach(item => {
                    newRow.append(`<td>${item}</td>`);
                });
                $('#tb tbody').append(newRow);
                tb.row.add(newRow).draw();
            }, error: function(jqXHR, textStatus, errorThrown) {
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

    $("#form").submit(function(e){
        e.preventDefault();
        Swal.fire({
            title: 'Yakin?',
            text: `Apakah anda yakin ingin Save Master Lokasi..?`,
            icon: 'warning',
            showCancelButton: true,
        })
        .then((result) => {
            if (result.value) {
                if($('#tb tbody tr:not(:has(td.dataTables_empty))').length < 1 || $('.required').filter(function() { return $(this).val() === ''; }).length > 0){
                    Swal.fire('Peringatan!', 'Terdapat PLU yang Belum Diinput..!','warning');
                    return;
                }
                $('#modal_loading').modal('show');
                $.ajax({
                    url: `/input-lokasi/action/action-save`,
                    type: "POST",
                    data: $("#form").serialize(),
                    success: function(response) {
                        setTimeout(function () { $('#modal_loading').modal('hide'); }, 500);
                        Swal.fire('Success!',response.message,'success').then(function(){
                            window.location.href = '/input-lokasi/';
                        });
                    }, error: function(jqXHR, textStatus, errorThrown) {
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
    });

    function updateRowNumbers(firstNoUrut) {
        $('#tb tbody tr').each(function(index) {
            firstNoUrut = parseInt(firstNoUrut);
            console.log(firstNoUrut);
            $(this).find('td:first input').val(firstNoUrut);
            firstNoUrut++;
        });
    }

    $('#tb tbody').on('keypress', 'input.form-control', function(event) {
        $(this).closest('td').next('td').text('');
        $(this).closest('td').next('td').next('td').text('');
        if (event.keyCode === 13) { // Check if Enter key is pressed
            event.preventDefault();
            var currentInput = $(this);

            $('#modal_loading').modal('show');
            $.ajax({
                url: `/input-lokasi/action/get-desc-plu/${$(this).val()}`,
                type: "GET",
                success: function(response) {
                    setTimeout(function () { $('#modal_loading').modal('hide'); }, 500);
                    var currentRow = currentInput.closest('tr');
                    var deskripsiPanjang = currentInput.closest('td').next('td');
                    var unit = deskripsiPanjang.next('td');
                    deskripsiPanjang.text(response.data.prd_deskripsipanjang);
                    unit.text(response.data.prd_unit);
                    currentInput.blur();
                }, error: function(jqXHR, textStatus, errorThrown) {
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

    $('#tb tbody').on('keydown', 'input.form-control', function(event) {
        if (event.keyCode === 46) { // Check if delete key is pressed
            event.preventDefault();
            var currentRow = $(this).closest('tr');
            var table = $('#tb').DataTable();
            var selectedRows = table.row(currentRow).indexes().toArray();
            if (selectedRows.length > 0) {
                var firstNoUrut = $('#tb tbody tr:first').find('td:first input').val();
                table.rows(selectedRows).remove().draw();
                updateRowNumbers(firstNoUrut);
            }
        }
    });


</script>
    
@endpush