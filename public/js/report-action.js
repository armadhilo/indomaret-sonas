function cancelAction(){
    Swal.fire({
        title: 'Cancel action saat ini ?',
        text: `Semua input yg anda masukkan akan dihapus...`,
        icon: 'warning',
        showCancelButton: true,
        cancelButtonText: 'Kembali',
        confirmButtonText: 'Ya, Cancel'
    })
    .then((result) => {
        if (result.value) {
            window.location.href = '/report/';
        }
    });
}

$('#report_content').on('input', "#jenis_barang", function(){
    var input = $(this).val().trim(); 
    var words = input.split(' ');

    if (!/^[BTR]$/i.test(input)) {
        $(this).val('');
    } else {
        $(this).val(input.toUpperCase());
    }
});

let formReportHTML = '';
let currentRequestData = '';
$("#report_content").on("submit", "#form_report", function(e) {
    e.preventDefault();
    $('.form-control').removeClass('is-invalid');
    let this_form = this;
    Swal.fire({
        title: `Yakin ingin melanjutkan ?`,
        text: `Pastikan semua input telah benar...`,
        icon: 'warning',
        showCancelButton: true,
        cancelButtonText: 'Batalkan',
        confirmButtonText: 'Ya, Lanjutkan'
    })
    .then((result) => {
        if (result.value) {
            $('#modal_loading').modal('show');
            currentRequestData = $(this).serialize();
            $.ajax({
                url: $(this).attr('action'),
                type: $(this).attr('method'),
                data: $(this).serialize(),
                success: function(response) {
                    setTimeout(function () { $('#modal_loading').modal('hide'); }, 500);
                    var pdfContent = response.pdf;
                    formReportHTML = $('#report_content').html();
                    $('#report_container').removeClass('vertical-center');
                    $('#report_content').addClass('display-pdf');
                    $('#report_content').html(`
                        <div class="header-pdf">
                            <p>DISPLAY REPORT</p>
                            <div>
                                <button class="btn btn-danger mr-2" onclick="cancelShowPDF()">Cancel</button>
                                <button class="btn btn-warning" onclick="openInNewTabPDF()">Open in New Tab</button>
                            </div>
                        </div>
                        <div style="height: calc(100% - 55px); border: 6px solid #323639;">
                            <object data="data:application/pdf;base64,${pdfContent}" type="application/pdf" style="width:100%;height:100%;"></object>
                        </div>
                    `);
                },error: function(jqXHR, textStatus, errorThrown) {
                    setTimeout(function () { $('#modal_loading').modal('hide'); }, 500);
                    Swal.fire({
                        text: (jqXHR.responseJSON && jqXHR.responseJSON.code === 400)
                            ? jqXHR.responseJSON.message
                            : "Oops! Terjadi kesalahan segera hubungi tim IT (" + errorThrown + ")",
                        icon: "error"
                    });
                    currentRequestData = '';
                    if (jqXHR.responseJSON && jqXHR.responseJSON.code === 400){
                        Object.keys(jqXHR.responseJSON.errors).forEach(function (key) {
                            var responseError = jqXHR.responseJSON.errors[key];
                            var elem_name = $(this_form).find('[name=' + responseError['field'] + ']');
                            elem_name.addClass('is-invalid');
                        });
                    }
                }
            });
        }
    });
});

function cancelShowPDF(){
    Swal.fire({
        title: `Yakin ?`,
        text: 'Yakin ingin kembali ke form input...?',
        icon: 'warning',
        showCancelButton: true,
        cancelButtonText: 'Tidak',
        confirmButtonText: 'Ya, Kembali'
    })
    .then((result) => {
        if (result.value) {
            $('#report_content').html(formReportHTML);
            $('#report_container').addClass('vertical-center');
            $('#report_content').removeClass('display-pdf');
            formReportHTML = '';
            currentRequestData = '';
        }
    });
}

function openInNewTabPDF(){
    var currentURL = window.location.href;
    var pdfURL = currentURL + "/pdf";

    pdfURL += '?' + currentRequestData;
    window.open(pdfURL, '_blank');
}

function initializeHelpPLU(){
    tb_plu = $('#tb_plu').DataTable({
        "lengthChange": false,
        processing: true,
        ajax: {
            url: '/report/addon/get-plu',
            type: 'GET'
        },
        columnDefs: [
            { className: 'text-center', targets: [0] },
            { "width": '15%', targets: [0] },
        ],
        columns: [
            { data: 'prd_prdcd' },
            { data: 'prd_deskripsipanjang' },
        ],
    });
}

$('#modal_plu').on('shown.bs.modal', function () {
    tb_plu.columns.adjust().draw();
});

function showModalPLU(){
    $("#modal_plu").modal("show");
}