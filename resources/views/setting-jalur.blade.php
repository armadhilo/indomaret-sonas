@extends('layouts.master')
@section('title')
    <h1 class="pagetitle">SETTING JALUR HH</h1>
@endsection

@section('css')
<style>
</style>
@endsection

@section('content')
    <script src="{{ url('js/home.js?time=') . rand() }}"></script>

    <div class="container-fluid">
        <div class="row">
            <div class="col-12 col-lg-6 offset-lg-3">
                <div class="card shadow mb-4">
                    <div class="card-body">
                        <form id="form_setting_jalur">
                            <input type="hidden" value="{{ $tglSo }}" name="tanggal_start_so">
                            <div class="row">
                                <div class="col-12 col-md-6">
                                    <div class="form-group">
                                        <label for="kode_rak">Kode Rak</label>
                                        <input type="text" class="form-control" name="kode_rak" id="kode_rak">
                                    </div>
                                </div>
                                <div class="col-12 col-md-6">
                                    <div class="form-group">
                                        <label for="kode_sub_rak">Kode Sub Rak</label>
                                        <input type="text" class="form-control" name="kode_sub_rak" id="kode_sub_rak">
                                    </div>
                                </div>
                                <div class="col-12 col-md-6">
                                    <div class="form-group">
                                        <label for="tipe_rak">tipe_rak</label>
                                        <input type="text" class="form-control" name="tipe_rak" id="tipe_rak">
                                    </div>
                                </div>
                                <div class="col-12 col-md-6">
                                    <div class="form-group">
                                        <label for="shelving_rak">Shelving Rak</label>
                                        <input type="text" class="form-control" name="shelving_rak" id="shelving_rak">
                                    </div>
                                </div>
                                <div class="col-12 col-md-6">
                                    <div class="form-group">
                                        <label for="no_urut">No. Urut</label>
                                        <input type="text" class="form-control" name="no_urut" id="no_urut">
                                    </div>
                                </div>
                                <div class="col-12 col-md-6">
                                    <div class="form-group">
                                        <label for="jalur_kertas">Jalur</label>
                                        <select class="form-control" name="jalur_kertas" id="jalur_kertas">
                                            <option disabled selected>-- Pilih Jalur Kertas --</option>
                                            <option value="H">Handheld</option>
                                            <option value="K">kertas</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group float-right mt-2">
                                <button class="btn btn-lg btn-success px-5">Save</button>
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
    });

    $("#form_setting_jalur").submit(function(e){
        e.preventDefault();
        let this_form = this;
        Swal.fire({
            title: 'Yakin?',
            text: `Apakah anda yakin ingin Update Jalur HH..?`,
            icon: 'warning',
            showCancelButton: true,
        })
        .then((result) => {
            if (result.value) {
                $('#modal_loading').modal('show');
                $('.invalid-feedback').remove();
                $('input, textarea, select').removeClass('is-invalid');
                $.ajax({
                    url: `/setting-jalur/action/update-jalur`,
                    type: "POST",
                    data: $("#form_setting_jalur").serialize(),
                    success: function(response) {
                        setTimeout(function () { $('#modal_loading').modal('hide'); }, 500);
                        Swal.fire('Success!',response.message,'success').then(function(){
                            location.reload();
                        });
                    }, error: function(jqXHR, textStatus, errorThrown) {
                        setTimeout(function () { $('#modal_loading').modal('hide'); }, 500);
                        if(jqXHR.responseJSON.code == 500){
                            Object.keys(jqXHR.responseJSON.errors).forEach(function (key) {
                            var responseError = jqXHR.responseJSON.errors[key];
                            var elem_name = $(this_form).find('[name=' + responseError['field'] + ']');
                            elem_name.after(`<span class="d-flex text-danger invalid-feedback">${responseError['message']}</span>`)
                            elem_name.addClass('is-invalid');
                        });
                        }else if(jqXHR.responseJSON.code == 400) {
                            Swal.fire('Oops!',jqXHR.responseJSON.message,'error');
                        }else {
                            Swal.fire('Oops!','Something wrong try again later (' + errorThrown + ')','error');
                        }
                    }
                });
            }
        })
    });
</script>
@endpush