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

$("#form_report").submit(function(e){
    e.preventDefault();
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
            // $('#modal_loading').modal('show');
                    var currentURL = window.location.href;
            
            // Append "/pdf" to the current URL
            var pdfURL = currentURL + "/pdf";
            
            // Open the PDF URL in a new tab or window
            window.open(pdfURL, '_blank');
            // $.ajax({
            //     url: $(this).attr('action'),
            //     type: $(this).attr('method'),
            //     data: $(this).serialize(),
            //     success: function(response) {
            //         setTimeout(function () { $('#modal_loading').modal('hide'); }, 500);
            //         Swal.fire('Good job!',response.message,'success').then(function(){
            //             window.location.href = '/report/';
            //         });
            //     }, error: function(jqXHR, textStatus, errorThrown) {
            //         setTimeout(function () { $('#modal_loading').modal('hide'); }, 500);
            //         Swal.fire({
            //             text: (jqXHR.responseJSON && jqXHR.responseJSON.code === 400)
            //                 ? jqXHR.responseJSON.message
            //                 : "Oops! Terjadi kesalahan segera hubungi tim IT (" + errorThrown + ")",
            //             icon: "error"
            //         });
            //     }
            // });
        }
    });

});