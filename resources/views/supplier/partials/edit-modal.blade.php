<div class="modal fade" id="editSupplierModal" tabindex="-1">
    <div class="modal-dialog">
        <form id="editSupplierForm">
            @csrf
            @method('PUT')

            <div class="modal-content">
                <div class="modal-header py-2">
                    <h6>Edit Supplier</h6>
                    <button class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <input type="hidden" id="editSupplierId">

                    <input class="form-control mb-2" name="name" id="editSupplierName">
                    <input class="form-control mb-2" name="phone" id="editSupplierPhone">
                    <input class="form-control mb-2" name="email" id="editSupplierEmail">
                    <input class="form-control mb-2" name="gst_number" id="editSupplierGst">
                    <textarea class="form-control" name="address" id="editSupplierAddress"></textarea>
                </div>

                <div class="modal-footer py-2">
                    <button class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Close</button>
                    <button class="btn btn-primary btn-sm">Update</button>
                </div>
            </div>
        </form>
    </div>
</div>
