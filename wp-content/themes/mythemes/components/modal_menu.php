<div class="modal fade" id="updateModal" tabindex="-1" aria-labelledby="updateModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="updateModalLabel">Edit Url</h5>
                    </div>
                    <div class="modal-body">
                        <div class="modal_massage text-danger"></div>
                        <form method="POST">
                            <div class="mb-3">
                                <input type="text" class="form-control" id="post_id" hidden>
                            </div>
                            <div class="mb-3">
                                <label for="new_original_url" class="col-form-label">Url:</label>
                                <input type="text" class="form-control" id="new_original_url">
                            </div>
                            <div class="mb-3">
                                <label for="short_url" class="col-form-label">Short url:</label>
                                <input type="text" class="form-control" id="short_url">
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="button" class="btn btn-primary" id="edit-save-button">Save</button>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="delConfirmModal" tabindex="-1" aria-labelledby="delConfirmModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <form method="POST">
                        <h5 class="modal-title m-3" id="updateModalLabel">Are you sure you want to delete the url?</h5>
                        <div class="mb-3">
                            <input type="text" class="form-control" id="post_id_for_del" hidden>
                        </div>
                    </form>
                    <div class="modal-footer">
                        <button type="button " class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="button " class="btn btn-danger" id="del-confirm-button" data-bs-dismiss="modal">Delete</button>
                    </div>
                </div>
            </div>
        </div>