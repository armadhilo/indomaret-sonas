@extends('layouts.master')
@section('title')
    <h1 class="pagetitle">INPUT KKSO</h1>
@endsection

@section('css')
<style>
    .header{
        padding: 0 15px
    }

    #header_tb{
        background: #6E214A;
        padding: 10px 6px;
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

    .form-header{
        display: flex;
        align-items: center;
        gap: 13px;
    }

    .form-header label{
        flex: 2;
    }

    .form-header div{
        flex: 4;
    }

    .flex-2 > *{
        flex: 2;
    }

    .btn-cust{
        height: 42px;
        width: 92px;
    }

    .invalid-feedback{
        font-size: 75%;
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
                        <div class="header">
                            <div id="header_tb" style="width: 180px">
                                <h5 class="m-0">F5 - SAVE</h5>
                            </div>
                            <form id="form_input_kkso">
                                <input type="hidden" value="{{ isset($tglSo) ? $tglSo : '' }}" name="tanggal_start_so">
                                <div class="d-flex flex-2" style="gap: 25px">
                                    <div>
                                        <div class="form-group form-header">
                                            <label for="kode_rak" class="text-nowrap">Kode Rak</label>
                                            <div>
                                                <input type="text" id="kode_rak" name="txtKodeRak" value="D38" class="form-control" required>
                                            </div>
                                        </div>
                                        <div class="form-group form-header">
                                            <label for="kode_subrak" class="text-nowrap">Kode SubRak</label>
                                            <div>
                                                <input type="text" id="kode_subrak" name="txtKodeSubRak" value="10" class="form-control" required>
                                            </div>
                                        </div>
                                    </div>
                                    <div>
                                        <div class="form-group form-header">
                                            <label for="tipe_rak" class="text-nowrap">Tipe Rak</label>
                                            <div>
                                                <input type="text" id="tipe_rak" name="txtTipeRak" value="N" class="form-control" required>
                                            </div>
                                        </div>
                                        <div class="form-group form-header">
                                            <label for="shelving_rak" class="text-nowrap">Shelving Rak</label>
                                            <div>
                                                <input type="text" id="shelving_rak" name="txtShelvingRak" value="01" class="form-control" required>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-group" style="flex: 1">
                                        <label for="jenis_barang" class="text-nowrap">Jenis Barang</label>
                                        <select id="jenis_barang" class="form-control">
                                            <option value="baik" selected>Baik</option>
                                        </select>
                                    </div>

                                    <div class="form-group d-flex align-items-center" style="gap: 15px; flex: 1;">
                                        <button type="submit" class="btn btn-lg btn-cust btn-primary">View</button>
                                        <button type="button" class="btn btn-lg btn-cust btn-secondary" onclick="clearForm()">Clear</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                        <div class="body">
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
    </div>
@endsection

@push('page-script')
<script>
    let tb_kkso;
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

        tb_kkso = $('#tb_kkso').DataTable({
            language: {
                emptyTable: "<div class='datatable-no-data' style='color: #ababab'>Tidak Ada Data</div>",
            },
            columns: [
                { data: 'lso_nourut'},
                { data: 'prd_prdcd'},
                { data: 'prd_deskripsipanjang'},
                { data: 'prd_unit',
                    render: function(data, type, row, meta) {
                        // Additional Input
                        return `
                            <input name="lso_nourut" type="hidden" readonly value="${row.lso_nourut}">
                            <input name="lso_jenisrak" type="hidden" readonly value="${row.prd_frac}">
                            <input name="prd_prdcd" type="hidden" readonly value="${row.prd_frac}">
                            <input name="prd_deskripsipanjang" type="hidden" readonly value="${row.prd_frac}">
                            <input name="prd_unit" type="hidden" readonly value="${row.prd_frac}">
                            <input name="prd_frac" type="hidden" readonly class="prd_frac" value="${row.prd_frac !== null ? row.prd_frac : 0}">
                            <input name="st_avgcost" type="hidden" readonly value="${row.st_avgcost}">
                            <input name="lso_qty" type="hidden" readonly value="${row.lso_qty}">
                            <input name="new_qty_ctn" type="hidden" readonly value="${row.new_qty_ctn}">
                            <input name="new_qty_pcs" type="hidden" readonly value="${row.new_qty_pcs}">

                            <div>${data}/${row.prd_frac !== null ? row.prd_frac : ''}</div>
                        `;

                    }
                },
                { data: 'lso_tmp_qtyctn',
                    render: function(data, type, row) {
                        return `<input name="lso_tmp_qtyctn" type="text" class="form-control lso_tmp_qtyctn_input" value="${data !== null ? data : ''}">`;
                    }
                },
                { data: 'lso_tmp_qtypcs',
                    render: function(data, type, row) {
                        return `<input name="lso_tmp_qtypcs" type="text" class="form-control lso_tmp_qtypcs_input" value="${data !== null ? data : ''}">`;
                    }
                },
                {
                    "data": null,
                    "render": function(data, type, row, meta) {
                        var prd_frac = row.prd_frac !== null ? parseInt(row.prd_frac) : 0;
                        var lso_tmp_qtyctn = row.lso_tmp_qtyctn !== null ? parseInt(row.lso_tmp_qtyctn) : 0;
                        var lso_tmp_qtypcs = row.lso_tmp_qtypcs !== null ? parseInt(row.lso_tmp_qtypcs) : 0;
                        var result = (lso_tmp_qtyctn * prd_frac) + lso_tmp_qtypcs;
                        return `<div class="total">${result}</div>`;
                    }
                }
            ],
            data: [],
            columnDefs: [
                { className: 'text-center-vh', targets: '_all' },
                { width: '50%', targets: 2 },
            ],
            paging: false,
            searching: false,
            info: false,
            order: [],
        });
    });

    function calculateRowResult($row) {
        var prd_frac = parseFloat($row.find('.prd_frac').val()) || 0;
        var lso_tmp_qtyctn = parseFloat($row.find('.lso_tmp_qtyctn_input').val()) || 0;
        var lso_tmp_qtypcs = parseFloat($row.find('.lso_tmp_qtypcs_input').val()) || 0;

        var result = (prd_frac * lso_tmp_qtyctn) + lso_tmp_qtypcs;
        $row.find('.total').text(result);
    }

    $(document).on('input', '.lso_tmp_qtyctn_input, .lso_tmp_qtypcs_input', function() {
        var $row = $(this).closest('tr');
        calculateRowResult($row);
    });

    $(document).keydown(function(event) {
        if (event.which == 116) {
            event.preventDefault();
            actionUpdate();
        }
    });

    function clearForm(){
        $('#form_input_kkso')[0].reset();
        tb_kkso.clear().draw();
    }

    $("#form_input_kkso").submit(function(e){
        let this_form = this;
        e.preventDefault();
        tb_kkso.clear().draw();
        $('.invalid-feedback').remove();
        $('input.form-control').css('margin-bottom', '0px');
        $('input, textarea, select').removeClass('is-invalid');
        $('.datatable-no-data').css('color', '#F2F2F2');
        $('#loading_datatable').removeClass('d-none');
        $('#loading_datatable_detail').removeClass('d-none');
        $.ajax({
            url: "/input-kkso/datatables",
            type: "GET",
            data: $("#form_input_kkso").serialize(),
            contentType: false,
            processData: false,
            success: function(response) {
                $('#loading_datatable').addClass('d-none');
                $('.datatable-no-data').css('color', '#ababab');
                tb_kkso.rows.add(response.data).draw();
            }, error: function(jqXHR, textStatus, errorThrown) {
                setTimeout(function () { $('#loading_datatable').addClass('d-none'); }, 500);
                $('#loading_datatable_detail').addClass('d-none');
                $('.datatable-no-data').css('color', '#ababab');
                if(jqXHR.responseJSON.code == 500){
                    Object.keys(jqXHR.responseJSON.errors).forEach(function (key) {
                    var responseError = jqXHR.responseJSON.errors[key];
                    var elem_name = $(this_form).find('[name=' + responseError['field'] + ']');
                    elem_name.after(`<span class="d-flex text-danger invalid-feedback">${responseError['message']}</span>`)
                    elem_name.addClass('is-invalid');
                    elem_name.parent().parent().find('label').css('margin-bottom', '19.2px');
                });
                }else if(jqXHR.responseJSON.code == 400) {
                    Swal.fire('Oops!',jqXHR.responseJSON.message,'error');
                }else {
                    Swal.fire('Oops!','Something wrong try again later (' + errorThrown + ')','error');
                }
            }
        });
    });

    function getRequestData(){
        var data = [];

        tb_kkso.rows().every(function() {
            var rowData = {};

            var rowNode = this.node();
            $(rowNode).find('input').each(function() {
                var fieldName = $(this).attr('name');
                var fieldValue = $(this).val();
                rowData[fieldName] = fieldValue;
            });

            data.push(rowData);
        });

        return $("#form_input_kkso").serialize() + '&' + $.param({ 'datatables': data });

    }

    function actionUpdate(){
        Swal.fire({
            title: 'Yakin?',
            text: `Apakah anda yakin ingin Update KKSO..?`,
            icon: 'warning',
            showCancelButton: true,
        })
        .then((result) => {
            if (result.value) {
                $('#modal_loading').modal('show');
                let requestData = getRequestData();
                $.ajax({
                    url: `/input-kkso/action/update`,
                    type: "POST",
                    data: requestData,
                    success: function(response) {
                        setTimeout(function () { $('#modal_loading').modal('hide'); }, 500);
                        Swal.fire('Success!',response.message,'success').then(function(){
                            window.location.href = '/input-kkso/';
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
    };

</script>

@endpush
