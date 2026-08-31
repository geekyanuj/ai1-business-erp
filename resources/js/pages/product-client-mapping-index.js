import $ from "jquery";
import "datatables.net-dt"; // DataTables core
import "datatables.net-buttons-dt";
import "datatables.net-buttons/js/buttons.html5.js";
import JSZip from "jszip";
import Choices from 'choices.js';

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
        columns: [
            { data: 'client.name', name: 'client', className: 'text-center'},
            { data: 'product.our_part_no', name: 'our_part_no', className: 'text-center' },
            { data: 'notes', name: 'description', className: 'text-center', orderable: false  },
            { data: 'client_part_no', name: 'client_part_no', className: 'text-center'  },
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
// Handle Category Filter
// ---------------------------
// export function handleCategoryFilter(tableSelector, filterSelector) {
//     $(document).on('change', filterSelector, function () {
//         const table = $(tableSelector).DataTable();
//         table.column(1).search($(this).val()).draw(); // column index 1 = category
//     });
// }

// ---------------------------
// Format Date Helper
// ---------------------------
// export function formatDate(data) {
//     if (!data) return '';
//     const date = new Date(data);
//     const day = String(date.getDate()).padStart(2, '0');
//     const month = String(date.getMonth() + 1).padStart(2, '0');
//     const year = date.getFullYear();
//     const hours = String(date.getHours()).padStart(2, '0');
//     const minutes = String(date.getMinutes()).padStart(2, '0');
//     const seconds = String(date.getSeconds()).padStart(2, '0');
//     return `${year}-${month}-${day} ${hours}:${minutes}:${seconds}`;
// }





// ----------------------------------------------------
// Initialize Choices
// ----------------------------------------------------
function initChoices(element, isModal = false) {
    if (!element) return;

    if (element.choices) {
        try { element.choices.destroy(); } catch (e) {}
    }

    const instance = new Choices(element, {
        searchEnabled: true,
        itemSelectText: "",
        allowHTML: false,
        shouldSort: false,
        position: isModal ? "bottom" : "auto",
    });

    element.choices = instance;
    return instance;
}

// ---------------------------
// Initialize everything on DOM ready
// ---------------------------
$(document).ready(function () {
    const tableSelector = '#productClientMappingTable';
    const ajaxUrl = $(tableSelector).data('url');

    // Initialize products DataTable
    window.productsTable = initializeProductsTable(tableSelector, ajaxUrl);

    // Handle category filter
    // handleCategoryFilter(tableSelector, '#categoryFilter');

    // $('#exportExcelBtn').on('click', function () {
    //     window.productsTable.button('.buttons-excel').trigger();
    // });


    initChoices(document.getElementById("addClient"), true);
    initChoices(document.getElementById("addProduct"), true);



});
