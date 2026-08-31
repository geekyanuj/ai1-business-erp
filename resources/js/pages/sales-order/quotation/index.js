// ----------------------------------------------------
// Imports
// ----------------------------------------------------
import "datatables.net-dt";
import "datatables.net-buttons-dt";
import "datatables.net-buttons/js/buttons.html5.js";
import JSZip from "jszip";
import $ from "jquery";
import { handleSalesItems, initChoices } from "../../../modules/sales-items-table";
import { fetchAllAddresses, resetChoices, initAddressOffcanvas } from "../../clients-index.js";


window.$ = window.jQuery = $;
window.JSZip = JSZip;

const Choices = window.Choices;


function showMessage(type, text) {
    const existing = document.getElementById("form-message");
    if (existing) existing.remove();

    const div = document.createElement("div");
    div.id = "form-message";
    div.style.position = "fixed";
    div.style.top = "60px";
    div.style.right = "20px";
    div.style.width = "300px";
    div.style.padding = "15px 20px";
    div.style.borderRadius = "5px";
    div.style.boxShadow = "0 0 10px rgba(0,0,0,0.1)";
    div.style.zIndex = "9999";
    div.style.backgroundColor = type === "success" ? "#d4edda" : "#f8d7da";
    div.style.color = type === "success" ? "#155724" : "#721c24";
    div.style.borderLeft =
        type === "success" ? "5px solid #28a745" : "5px solid #dc3545";
    div.innerText = text;
    document.body.appendChild(div);
    setTimeout(() => div.remove(), 3000);
}


// ----------------------------------------------------
// Initialize Orders DataTable
// ----------------------------------------------------
export function initializeOrdersTable(tableSelector, ajaxUrl) {
    const table = $(tableSelector).DataTable({
        processing: true,
        serverSide: true,
        ajax: ajaxUrl,

        columns: [
            { data: "quotation_number", name: "quotation_number" },
            { data: "client", name: "client" },
            { data: "quotation_date", name: "quotation_date", className: "text-center" },
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
// Generate Quotation Number
// ----------------------------------------------------
export function handleGenerateQuotationNo(generateSOUrl) {
    $("#addQuotationModal").on("show.bs.modal", function () {
        $.get(generateSOUrl, function (data) {
            $("#addQuotationNumber").val(data.quotation_number);
            $("#addQuotationNumberHidden").val(data.quotation_number);
        });
    });
}


// ----------------------------------------------------
// Handle Add Client AJAX
// ----------------------------------------------------
// ----------------------------------------------------
// Handle Client & Address Creation/Editing
// ----------------------------------------------------
function handleClientManagers() {

    // When Client Changes:
    // 1. Show/Hide Edit Client Button
    // 2. Fetch Addresses for this Client
    $(document).on("change", "#addClientName", function () {
        const clientId = $(this).val();
        const $editBtn = $(".edit-client-shortcut");
        const $addressSections = $(".address-actions");

        if (!clientId) {
            $editBtn.fadeOut();
            $addressSections.fadeOut();
            resetChoices(document.getElementById("billing_address_id").choices, []);
            resetChoices(document.getElementById("shipping_address_id").choices, []);
            return;
        }

        $editBtn.fadeIn();
        $addressSections.fadeIn();

        // Fetch addresses for this client
        $.get(`/clients/${clientId}/addresses`, function (addresses) {
            const billingSel = document.getElementById("billing_address_id").choices;
            const shippingSel = document.getElementById("shipping_address_id").choices;

            resetChoices(billingSel, addresses);
            resetChoices(shippingSel, addresses);

            // Default selection if available
            if (addresses.length > 0) {
                billingSel.setChoiceByValue(String(addresses[0].id));
                shippingSel.setChoiceByValue(String(addresses[0].id));
            }
        });
    });

    // Edit Client Shortcut
    $(document).on('click', '.edit-client-shortcut', function () {
        const clientId = $("#addClientName").val();
        if (!clientId) return;

        $.get(`/clients/${clientId}/show-ajax`, function (response) {
            $("#edit_client_id").val(response.id);
            $("#edit_client_name").val(response.name);
            $("#edit_client_contact_person").val(response.contact_person);
            $("#edit_client_email").val(response.email);
            $("#edit_client_phone").val(response.phone);
            $("#edit_client_gst_number").val(response.gst_number);
        });
    });

    // Update Client AJAX
    $(document).on("submit", "#editClientForm", function (e) {
        e.preventDefault();
        const $form = $(this);
        const clientId = $("#edit_client_id").val();

        $.ajax({
            url: `/clients/${clientId}/update-ajax`,
            type: "POST",
            data: $form.serialize(),
            success: function (response) {
                if (response.success) {
                    const client = response.data;
                    // Update option text in main select
                    $(`#addClientName option[value="${client.id}"]`).text(client.name);
                    initChoices(document.getElementById("addClientName"), true);

                    showMessage("success", "Client updated successfully");
                    bootstrap.Offcanvas.getOrCreateInstance(document.getElementById("editClientOffcanvas")).hide();
                }
            }
        });
    });

    // Edit Address Shortcut
    $(document).on('click', '.edit-address-shortcut', function () {
        const selectId = $(this).data('select-id');
        const addressId = document.getElementById(selectId).value;
        if (!addressId) return;

        $.get(`/addresses/${addressId}/show-ajax`, function (response) {
            const form = document.getElementById('editAddressForm');
            if (form) {
                form.querySelector('[name="id"]').value = response.id;
                form.querySelector('[name="address_line_1"]').value = response.address_line_1;
                form.querySelector('[name="address_line_2"]').value = response.address_line_2 || '';
                form.querySelector('[name="city"]').value = response.city;
                form.querySelector('[name="state"]').value = response.state || '';
                form.querySelector('[name="postal_code"]').value = response.postal_code || '';
                form.querySelector('[name="country"]').value = response.country;
                form.dataset.targetSelect = selectId;
            }
        });
    });

    // Save New Address
    $(document).on('submit', '#addAddressForm', function (e) {
        e.preventDefault();
        const form = $(this);
        const targetSelectId = form.attr('data-target-select');
        const clientId = $("#addClientName").val();

        if (!clientId) return showMessage("error", "Please select a client first");

        $.ajax({
            url: `/addresses/client/${clientId}`,
            type: "POST",
            data: form.serialize(),
            success: function (response) {
                const selectEl = document.getElementById(targetSelectId);
                const choices = selectEl.choices;
                
                $.get(`/clients/${clientId}/addresses`, function (addresses) {
                    resetChoices(choices, addresses, response.id);
                    showMessage("success", "Address created!");
                    bootstrap.Offcanvas.getOrCreateInstance(document.getElementById("addressOffcanvas")).hide();
                });
            }
        });
    });

    // Update Existing Address
    $(document).on('submit', '#editAddressForm', function (e) {
        e.preventDefault();
        const form = $(this);
        const targetSelectId = form.attr('data-target-select');
        const addressId = form.find('[name="id"]').val();
        const clientId = $("#addClientName").val();

        $.ajax({
            url: `/addresses/${addressId}/update-ajax`,
            type: "POST",
            data: form.serialize(),
            success: function (response) {
                const selectEl = document.getElementById(targetSelectId);
                const choices = selectEl.choices;

                $.get(`/clients/${clientId}/addresses`, function (addresses) {
                    resetChoices(choices, addresses, response.id);
                    showMessage("success", "Address updated!");
                    bootstrap.Offcanvas.getOrCreateInstance(document.getElementById("editAddressOffcanvas")).hide();
                });
            }
        });
    });
}


function handleAddClient() {

    $(document).on("submit", "#addClientForm", function (e) {
        e.preventDefault();

        const $form = $(this);

        $.ajax({
            url: $form.data("url"),
            type: "POST",
            data: $form.serialize(),

            success: function (response) {

                if (!response.success) {
                    showMessage("error", "Client creation failed");
                    return;
                }

                const client = response.data;

                // Append & select new client
                $("#addClientName").append(
                    `<option value="${client.id}" selected>${client.name}</option>`
                );

                initChoices(document.getElementById("addClientName"), true);
                
                // Fetch addresses for this new client and populate main dropdowns
                $.get(`/clients/${client.id}/addresses`, function (addresses) {
                    resetChoices(document.getElementById("billing_address_id").choices, addresses);
                    resetChoices(document.getElementById("shipping_address_id").choices, addresses);
                    $(".edit-client-shortcut").fadeIn();
                    $(".address-actions").fadeIn();
                });

                // Reset form
                $form[0].reset();
                resetChoices(document.getElementById("add_client_billing_address").choices, []);
                resetChoices(document.getElementById("add_client_shipping_address").choices, []);

                // Close offcanvas
                if (window.bootstrap) {
                    const offcanvasEl = document.getElementById("clientOffcanvas");
                    const offcanvas = bootstrap.Offcanvas.getOrCreateInstance(offcanvasEl);
                    offcanvas.hide();
                }

                showMessage("success", response.message);
            },

            error: function (xhr) {

                if (xhr.status === 422) {
                    const errors = Object.values(xhr.responseJSON.errors)
                        .flat()
                        .join("\n");

                    showMessage("error", errors);
                } else {
                    showMessage(
                        "error",
                        "Something went wrong while saving client"
                    );
                }
            },
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
    handleAddClient();
    handleClientManagers();
    initAddressOffcanvas();

    initChoices(document.getElementById("clientFilter"));
    initChoices(document.getElementById("addClientName"), true);
    initChoices(document.getElementById("billing_address_id"), true);
    initChoices(document.getElementById("shipping_address_id"), true);
    initChoices(document.getElementById("add_client_billing_address"), true);
    initChoices(document.getElementById("add_client_shipping_address"), true);
    
    fetchAllAddresses(function (data) {
        resetChoices(document.getElementById("add_client_billing_address").choices, data);
        resetChoices(document.getElementById("add_client_shipping_address").choices, data);
    });

    document.querySelectorAll(".product-dropdown").forEach(el => {
        initChoices(el);
    });

    handleGenerateQuotationNo($("#addQuotationNumber").data("url"));



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

    // Automate Tax Type based on Billing Address
    $(document).on("change", "#billing_address_id", function () {
        const addressId = $(this).val();
        if (!addressId) return;

        $.get(`/addresses/${addressId}/show-ajax`, function (address) {
            const clientState = address.state || "";
            const branchState = window.branchState || "";

            console.log("Detecting tax type for address selection:", { clientState, branchState });

            if (clientState.toLowerCase().trim() === branchState.toLowerCase().trim()) {
                $("#tax_type").val("cgst_sgst");
            } else {
                $("#tax_type").val("igst");
            }
        });
    });



    
});
