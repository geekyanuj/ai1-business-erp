import $ from "jquery";

$(document).on('click', '.edit-product-btn', function () {
    // Read product data from button's data attribute
    let product = $(this).data('product');

    $('#editProductId').val(product.id);
    $('#editProductPartNo').val(product.our_part_no);
    $('#editProductDescription').val(product.description);
    $('#editProductCategory').val(product.category);
    $('#editProductSpecs').val(product.specs);

    $('#editProductForm').attr('action', '/products/' + product.id);
});
