<div class="modal fade" role="dialog" id="modal_plu" data-keyboard="false" data-backdrop="static">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header br">
                <h5 class="modal-title">Help PLU</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <button class="btn btn-primary" style="margin-left: 20px; padding: .375rem 1.3rem; position: absolute; z-index: 1500" onclick="tb_plu.ajax.reload();">Refresh</button>
                <div class="table-responsive">
                    <table class="table table-striped table-hover datatable-dark-primary w-100" id="tb_plu" style="margin: 20px">
                        <thead>
                            <tr>
                                <th>PLU</th>
                                <th>Deskripsi</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer bg-whitesmoke br">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>