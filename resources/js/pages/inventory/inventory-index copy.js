import $ from "jquery";
import "datatables.net-dt";
import "datatables.net-buttons-dt";
import "datatables.net-buttons/js/buttons.html5.js";
import JSZip from "jszip";

window.JSZip = JSZip;

// ---------------------------
// Initialize Inventory DataTable
// ---------------------------
export function initializeInventoryTable(tableSelector, ajaxUrl) {
    const $table = $(tableSelector);
    if (!$table.length) return null;

    const table = $table.DataTable({
        processing: true,
        serverSide: true,

        ajax: {
            url: ajaxUrl,
            data: function (d) {
                // 🔥 Custom filters sent to backend
                d.inventory_type = $('#inventoryType').val();
                d.search_text    = $('#inventorySearch').val();
                d.location       = $('#locationFilter').val();
            }
        },

        columns: [
            { data: 'id', name: 'inventories.id' },
            { data: 'inventory_type', name: 'inventories.inventory_type' },
            { data: 'our_part_no', name: 'products.our_part_no' },
            { data: 'material_name', name: 'inventories.material_name' },
            { data: 'location', name: 'inventories.location' },
            { data: 'quantity_available', name: 'inventories.quantity_available' },
            { data: 'quantity_reserved', name: 'inventories.quantity_reserved' },

            {
                data: 'available_stock',
                name: 'available_stock',
                orderable: false,
                searchable: false,
                className: 'text-center'
            },

            {
                data: 'updated_at',
                name: 'inventories.updated_at',
                render: function (data) {
                    return formatDate(data);
                }
            },

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
        order: [[0, 'desc']],

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

    // Hide default buttons
    table.buttons().container().hide();

    return table;
}

// ---------------------------
// Format Date Helper
// ---------------------------
export function formatDate(data) {
    if (!data) return '';
    const date = new Date(data);
    return date.toISOString().slice(0, 19).replace('T', ' ');
}

// ---------------------------
// DOM Ready
// ---------------------------
$(document).ready(function () {

    const tableSelector = '#inventoryTable';
    const ajaxUrl = $(tableSelector).data('url');

    window.inventoryTable = initializeInventoryTable(tableSelector, ajaxUrl);

    // 🔁 Reload table on filter change
    $('#inventoryType, #locationFilter').on('change', function () {
        window.inventoryTable.ajax.reload();
    });

    // 🔎 Search with debounce
    let searchTimer = null;
    $('#inventorySearch').on('keyup', function () {
        clearTimeout(searchTimer);
        searchTimer = setTimeout(() => {
            window.inventoryTable.ajax.reload();
        }, 400);
    });

    // 📤 Export
    $('#exportExcelBtn').on('click', function () {
        window.inventoryTable.button('.buttons-excel').trigger();
    });
});
