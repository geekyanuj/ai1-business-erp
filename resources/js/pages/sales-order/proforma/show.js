import $ from "jquery";
window.$ = window.jQuery = $;

import {
    handleSalesItems,
    initChoices,
} from "../../../modules/sales-items-table";
import { fetchAllAddresses, resetChoices, initAddressOffcanvas } from "../../clients-index.js";

/* ============================================================
   INIT BOTH MODULES
============================================================ */

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

/* ---------- EDIT ITEMS MODAL ---------- */
export function initEditItemsModal({ products }) {
    window.products = products;

    handleSalesItems({
        enableDiscount: true,
        tableSelector: "#editItemsTable",
        addButtonSelector: "#addEditItemRow",
        subtotalSelector: "#edit_subtotal",
        taxSelector: "#edit_total_tax",
        grandTotalSelector: "#edit_grand_total",
        cgstSelector: "#edit_cgst_amount",
        sgstSelector: "#edit_sgst_amount",
        igstSelector: "#edit_igst_amount",
        taxTypeSelector: "#edit_tax_type",
        cgstLabelSelector: "#cgst_label_edit",
        sgstLabelSelector: "#sgst_label_edit",
        igstLabelSelector: "#igst_label_edit",
    });
}

/* ---------- UPDATE PROFORMA MODAL ---------- */
export function initUpdateProformaModal() {
    handleSalesItems({
        enableDiscount: true,
        tableSelector: "#updateItemsTable",
        addButtonSelector: "#addUpdateItemRow",
        subtotalSelector: "#update_subtotal",
        taxSelector: "#update_total_tax",
        grandTotalSelector: "#update_grand_total",
        cgstSelector: "#update_cgst_amount",
        sgstSelector: "#update_sgst_amount",
        igstSelector: "#update_igst_amount",
        taxTypeSelector: "#tax_type",
        cgstLabelSelector: "#cgst_label",
        sgstLabelSelector: "#sgst_label",
        igstLabelSelector: "#igst_label",
    });
}

/* ============================================================
   EDIT ITEMS MODAL HANDLER
============================================================ */
$(document).on("click", ".edit-items-btn", function () {
    const proforma = $(this).data("proforma");
    const tbody = $("#editItemsTable tbody");

    tbody.empty();

    proforma.items.forEach((item, index) => {
        tbody.append(buildRow(item, index));
    });

    initDropdowns("#editItemsTable");
    recalc("#editItemsTable");
});

/* ============================================================
   UPDATE PROFORMA MODAL HANDLER
============================================================ */
$(document).on("click", ".update-proforma-btn", function () {
    const proforma = $(this).data("proforma");

    $("#update_proforma_id").val(proforma.id);
    $("#updateProformaDate").val(proforma.proforma_date);
    $("#updateProformaNumber").val(proforma.proforma_number);
    $("#updateProformaNumberHidden").val(proforma.proforma_number);
    $("#updateClient").val(proforma.client_id);

    // Initializing Choices
    const clientSelect = document.getElementById("updateClient");
    initChoices(clientSelect, true);

    const billingSel = document.getElementById("update_billing_address_id");
    const shippingSel = document.getElementById("update_shipping_address_id");
    initChoices(billingSel, true);
    initChoices(shippingSel, true);

    // Fetch addresses for the selected client
    $.get(`/clients/${proforma.client_id}/addresses`, function (addresses) {
        resetChoices(billingSel.choices, addresses, proforma.billing_address_id);
        resetChoices(shippingSel.choices, addresses, proforma.shipping_address_id);
    });
});

// ----------------------------------------------------
// Handle Client & Address Creation/Editing
// ----------------------------------------------------
function handleClientManagers() {

    // When Client Changes:
    $(document).on("change", "#updateClient", function () {
        const clientId = $(this).val();
        if (!clientId) {
            resetChoices(document.getElementById("update_billing_address_id").choices, []);
            resetChoices(document.getElementById("update_shipping_address_id").choices, []);
            return;
        }

        // Fetch addresses for this client
        $.get(`/clients/${clientId}/addresses`, function (addresses) {
            const billingSel = document.getElementById("update_billing_address_id").choices;
            const shippingSel = document.getElementById("update_shipping_address_id").choices;

            resetChoices(billingSel, addresses);
            resetChoices(shippingSel, addresses);

            if (addresses.length > 0) {
                billingSel.setChoiceByValue(String(addresses[0].id));
                shippingSel.setChoiceByValue(String(addresses[0].id));
            }
        });
    });

    // Edit Client Shortcut
    $(document).on('click', '.edit-client-shortcut', function () {
        const clientId = $("#updateClient").val();
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
                    $(`#updateClient option[value="${client.id}"]`).text(client.name);
                    initChoices(document.getElementById("updateClient"), true);
                    showMessage("success", "Client updated successfully");
                    bootstrap.Offcanvas.getOrCreateInstance(document.getElementById("editClientOffcanvas")).hide();
                }
            }
        });
    });

    // Add Client AJAX
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
                $("#updateClient").append(`<option value="${client.id}" selected>${client.name}</option>`);
                initChoices(document.getElementById("updateClient"), true);
                
                $.get(`/clients/${client.id}/addresses`, function (addresses) {
                    resetChoices(document.getElementById("update_billing_address_id").choices, addresses);
                    resetChoices(document.getElementById("update_shipping_address_id").choices, addresses);
                });

                $form[0].reset();
                bootstrap.Offcanvas.getOrCreateInstance(document.getElementById("clientOffcanvas")).hide();
                showMessage("success", response.message);
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
        const clientId = $("#updateClient").val();

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
        const clientId = $("#updateClient").val();

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

/* ============================================================
   SHARED HELPERS
============================================================ */

function buildRow(item, index) {
    return `
    <tr>
        <td>
            <select name="items[${index}][product_id]"
                class="product-dropdown form-select">
                <option value="">Select</option>
                ${window.products
                    .map(
                        (p) => `
                    <option value="${p.id}"
                        ${p.id === item.product_id ? "selected" : ""}>
                        ${p.our_part_no}
                    </option>
                `
                    )
                    .join("")}
            </select>
        </td>

        <td>
            <input type="number"
                name="items[${index}][quantity]"
                class="form-control qty"
                value="${item.quantity}" min="1">
        </td>

        <td>
            <input type="number"
                name="items[${index}][unit_price]"
                class="form-control rate"
                value="${item.unit_price}" min="0">
        </td>

        <td>
            <input type="number" 
                name="items[${index}][discount_percent]" 
                class="form-control discount_percent" 
                value="${item.discount_percent}"
                step="0.01" min="0" > 
        </td>

        <td><input class="form-control taxable_amount" readonly></td>

        <td>
            <input type="number"
                name="items[${index}][tax_rate]"
                class="form-control tax_rate"
                value="${item.tax_rate}" min="0">
        </td>

        <td><input class="form-control tax_amount" readonly></td>
        <td><input class="form-control total_with_tax" readonly></td>

        <td>
            <button type="button"
                class="btn btn-danger btn-sm removeRow">X</button>
        </td>
    </tr>`;
}

function initDropdowns(tableSelector) {
    document
        .querySelectorAll(`${tableSelector} .product-dropdown`)
        .forEach((el) => initChoices(el, true));
}

function recalc(tableSelector) {
    $(`${tableSelector} tbody tr`).each(function () {
        $(this).find(".qty").trigger("input");
    });
}

/* ============================================================
   INIT
============================================================ */
$(document).ready(function () {
    initEditItemsModal({
        products: window.products,
    });

    initUpdateProformaModal();
    handleClientManagers();
    initAddressOffcanvas();

    initChoices(document.getElementById("add_client_billing_address"), true);
    initChoices(document.getElementById("add_client_shipping_address"), true);
    
    fetchAllAddresses(function (data) {
        resetChoices(document.getElementById("add_client_billing_address").choices, data);
        resetChoices(document.getElementById("add_client_shipping_address").choices, data);
    });
});
