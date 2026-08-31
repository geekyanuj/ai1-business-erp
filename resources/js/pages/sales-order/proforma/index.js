// ----------------------------------------------------
// Imports
// ----------------------------------------------------
import "datatables.net-dt";
import "datatables.net-buttons-dt";
import "datatables.net-buttons/js/buttons.html5.js";
import JSZip from "jszip";
import $ from "jquery";
import { handleSalesItems, initChoices } from "../../../modules/sales-items-table";


window.$ = window.jQuery = $;
window.JSZip = JSZip;

const Choices = window.Choices;


// ----------------------------------------------------
// Initialize Orders DataTable
// ----------------------------------------------------
export function initializeOrdersTable(tableSelector, ajaxUrl) {
    const table = $(tableSelector).DataTable({
        processing: true,
        serverSide: true,
        ajax: ajaxUrl,

        columns: [
            { data: "proforma_number", name: "proforma_number" },
            { data: "client", name: "client" },
            { data: "proforma_date", name: "proforma_date", className: "text-center" },
            { data: "status", name: "status", orderable: false, className: "text-center" },
            { data: "grand_total", name: "grand_total", orderable: false, searchable: false },
            { data: "actions", name: "actions", orderable: false, searchable: false, className: "text-center" }
        ],

        responsive: true,
        pageLength: 10,

        buttons: [
            {
                extend: "excelHtml5",
                text: "Hidden Excel",
                exportOptions: { modifier: { search: "applied" } },
            },
        ],
    });

    table.buttons().container().hide();
    return table;
}



// ----------------------------------------------------
// Filter Panel Toggle
// ----------------------------------------------------
export function handleFilterPanel() {
    $(document).on("click", ".filterIcon", function () {
        $("#filterContainer").toggleClass("show");
    });
}

// ----------------------------------------------------
// Handle Table Filters
// ----------------------------------------------------
export function handleTableFilters(tableSelector) {
    const table = $(tableSelector).DataTable();

    $(document).on("change", "#clientFilter", function () {
        table.column(1).search($(this).val()).draw();
    });

    $(document).on("change", "#statusFilter", function () {
        table.column(3).search($(this).val()).draw();
    });
}

// ----------------------------------------------------
// Generate Proforma Number
// ----------------------------------------------------
export function handleGenerateProformaNo(generateSOUrl) {
    $("#addProformaModal").on("show.bs.modal", function () {
        $.get(generateSOUrl, function (data) {
            $("#addProformaNumber").val(data.proforma_number);
            $("#addProformaNumberHidden").val(data.proforma_number);
        });
    });
}



// ----------------------------------------------------
// Document Ready
// ----------------------------------------------------
$(document).ready(function () {
    const tableSelector = "#orderTable";

    window.ordersTable = initializeOrdersTable(tableSelector, $(tableSelector).data("url"));

    $("#exportBtn").click(() => window.ordersTable.button(".buttons-excel").trigger());

    handleTableFilters(tableSelector);
    handleFilterPanel();

    initChoices(document.getElementById("clientFilter"));
    initChoices(document.getElementById("addClientName"), true);
    document.querySelectorAll(".product-dropdown").forEach(el => {
        initChoices(el);
    });

    handleGenerateProformaNo($("#addProformaNumber").data("url"));

    handleSalesItems({
        enableDiscount: true,
        tableSelector: "#itemsTable",
        addButtonSelector: "#addItemRow",
        subtotalSelector: "#subtotal",
        taxSelector: "#total_tax",
        grandTotalSelector: "#grand_total",
        cgstSelector: "#cgst_amount",
        sgstSelector: "#sgst_amount",
        igstSelector: "#igst_amount",
        taxTypeSelector: "#tax_type",
        cgstLabelSelector: "#cgst_label",
        sgstLabelSelector: "#sgst_label",
        igstLabelSelector: "#igst_label",
    });

    $(document).on("change", "#addClientName", function () {
        const clientId = $(this).val();
        if (!clientId) return;

        const client = window.clientsData.find(c => String(c.id) === String(clientId));
        if (client && client.billing_address) {
            const clientState = client.billing_address.state || "";
            const branchState = window.branchState || "";

            if (clientState.toLowerCase().trim() === branchState.toLowerCase().trim()) {
                $("#tax_type").val("cgst_sgst");
            } else {
                $("#tax_type").val("igst");
            }
        }
    });

    
});
