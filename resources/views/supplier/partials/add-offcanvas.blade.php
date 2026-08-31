{{-- Add Supplier Form --}}
<div class="offcanvas offcanvas-end" tabindex="-1" id="supplierOffcanvas">
    <div class="offcanvas-header">
        <h5 class="offcanvas-title">Add New Supplier</h5>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas"></button>
    </div>

    <div class="offcanvas-body">
        <form id="addSupplierForm" data-url="{{ route('suppliers.ajax-store') }}">
            @csrf

            <div class="mb-3">
                <label class="form-label required-field">Supplier Name</label>
                <input type="text" class="form-control" placeholder="Supplier Name" id="supplier_name" name="name">
            </div>

            <div class="mb-3">
                <label class="form-label">Phone</label>
                <input type="text" class="form-control" placeholder="Phone" id="supplier_phone" name="phone"
                    maxlength="10">
            </div>

            <div class="mb-3">
                <label class="form-label">Email</label>
                <input type="email" class="form-control" placeholder="Email" id="supplier_email" name="email">
            </div>

            <div class="mb-3">
                <label class="form-label">GST No</label>
                <input type="text" class="form-control" placeholder="GST No" id="supplier_gst" name="gst_number"
                    maxlength="15">
            </div>

            <div class="mb-3">
                <label class="form-label">Address</label>
                <textarea name="address" class="form-control" rows="2" placeholder="Address"></textarea>
            </div>

            <div class="text-end">
                <button type="button" class="btn btn-sm bg-my-primary text-white" id="saveSupplierBtn"
                    data-bs-dismiss="offcanvas">
                    Save Supplier
                </button>
            </div>
        </form>
    </div>
</div>