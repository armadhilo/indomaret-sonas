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
                                                <input type="text" id="kode_rak" name="txtKodeRak" class="form-control" required>
                                            </div>
                                        </div>
                                        <div class="form-group form-header">
                                            <label for="kode_subrak" class="text-nowrap">Kode SubRak</label>
                                            <div>
                                                <input type="text" id="kode_subrak" name="txtKodeSubRak" class="form-control" required>
                                            </div>
                                        </div>
                                    </div>
                                    <div>
                                        <div class="form-group form-header">
                                            <label for="tipe_rak" class="text-nowrap">Tipe Rak</label>
                                            <div>
                                                <input type="text" id="tipe_rak" name="txtTipeRak" class="form-control" required>
                                            </div>
                                        </div>
                                        <div class="form-group form-header">
                                            <label for="shelving_rak" class="text-nowrap">Shelving Rak</label>
                                            <div>
                                                <input type="text" id="shelving_rak" name="txtShelvingRak" class="form-control" required>
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
                { data: 'prd_unit'},
                { data: 'lso_tmp_qtyctn'},
                { data: 'lso_tmp_qtypcs'},
                { data: 'st_avgcost'},
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

    function clearForm(){
        $('#form_input_kkso')[0].reset();
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

</script>
    
@endpush