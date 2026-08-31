import $ from "jquery";
import "datatables.net-dt"; // DataTables core
import "datatables.net-buttons-dt";
import "datatables.net-buttons/js/buttons.html5.js";
import JSZip from "jszip";

window.JSZip = JSZip; // required for Excel
// ---------------------------
// Initialize Products DataTable
// ---------------------------
export function initializeProductsTable(tableSelector, ajaxUrl) {
    const $table = $(tableSelector);
    if (!$table.length) return null;

    const table = $table.DataTable({
        processing: true,
        serverSide: true,
        ajax: ajaxUrl,
        autoWidth: false,
        columns: [
            { data: 'our_part_no', name: 'our_part_no',  width: "20%", },
            { data: 'category', name: 'category', className: 'text-center', width: "10%",},
            { data: 'specs', name: 'specs', className: 'text-center', width: "10%", orderable: false  },
            { data: 'hsn', name: 'hsn', className: 'text-center', width: "10%", orderable: false  },
            { data: 'created_at', name: 'created_at', render: formatDate, className: 'text-center', width: "10%",  },
            { data: 'updated_at', name: 'updated_at', render: formatDate, className: 'text-center' , width: "10%", },
            {
                data: 'actions',
                name: 'actions',
                orderable: false,
                searchable: false,
                width: "12%",
                className: 'text-center' 
            }
        ],

        responsive: true,
        pageLength: 10,

        buttons: [
            {
                extend: "excelHtml5",
                text: "Hidden Excel",
                exportOptions: {
                    modifier: { search: "applied" }
                }
            }
        ]
    });

    // hide the actual DataTable Buttons to avoid layout issues
    table.buttons().container().hide();

    return table;
}


// ---------------------------
// Handle Edit Product Modal
// ---------------------------
export function handleEditProductModal() {
    $(document).on('click', '.edit-product-btn', function () {
        const productId = $(this).data('id');
        const partNo = $(this).data('our_part_no');
        const description = $(this).data('description');
        const category = $(this).data('category');
        const specs = $(this).data('specs');
        const hsn = $(this).data('hsn');

        $('#editProductId').val(productId);
        $('#editProductPartNo').val(partNo);
        $('#editProductDescription').val(description);
        $('#editProductCategory').val(category);
        $('#editProductHsn').val(hsn);

        $('#editProductForm').attr('action', `/products/${productId}`);
    });
}

// ---------------------------
// Handle Category Filter
// ---------------------------
export function handleCategoryFilter(tableSelector, filterSelector) {
    $(document).on('change', filterSelector, function () {
        const table = $(tableSelector).DataTable();
        table.column(1).search($(this).val()).draw(); // column index 1 = category
    });
}

// ---------------------------
// Format Date Helper
// ---------------------------
export function formatDate(data) {
    if (!data) return '';
    const date = new Date(data);
    const day = String(date.getDate()).padStart(2, '0');
    const month = String(date.getMonth() + 1).padStart(2, '0');
    const year = date.getFullYear();
    const hours = String(date.getHours()).padStart(2, '0');
    const minutes = String(date.getMinutes()).padStart(2, '0');
    const seconds = String(date.getSeconds()).padStart(2, '0');
    return `${year}-${month}-${day} ${hours}:${minutes}:${seconds}`;
}

// ---------------------------
// Initialize everything on DOM ready
// ---------------------------
$(document).ready(function () {
    const tableSelector = '#productsTable';
    const ajaxUrl = $(tableSelector).data('url');

    // Initialize products DataTable
    window.productsTable = initializeProductsTable(tableSelector, ajaxUrl);

    // Handle modals
    handleEditProductModal();

    // Handle category filter
    handleCategoryFilter(tableSelector, '#categoryFilter');

    $('#exportExcelBtn').on('click', function () {
    window.productsTable.button('.buttons-excel').trigger();
});

});
