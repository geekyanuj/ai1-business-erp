// ----------------------------------------------------
// purchase-orders-index.js
// ----------------------------------------------------

import "datatables.net-dt";
import "datatables.net-buttons-dt";
import "datatables.net-buttons/js/buttons.html5.js";
import JSZip from "jszip";
import $, { error } from "jquery";
import { handlePOItemsTable } from "../modules/po-items-table";
import { fetchAllAddresses, resetChoices, initAddressOffcanvas } from "./clients-index.js";

window.$ = window.jQuery = $;
window.JSZip = JSZip;

/* GLOBAL CSRF SETUP */
$.ajaxSetup({
    headers: {
        "X-CSRF-TOKEN": document
            .querySelector('meta[name="csrf-token"]')
            .getAttribute("content"),
    },
});

/* ----------------------------------------------------
   Toast Message
---------------------------------------------------- */
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

/* ----------------------------------------------------
   PO DataTable
---------------------------------------------------- */
export function initializePOTable(tableSelector, ajaxUrl) {
    const table = $(tableSelector).DataTable({
        processing: true,
        serverSide: true,
        ajax: ajaxUrl,
        columns: [
            { data: "po_number", name: "po_number" },
            { data: "po_type", name: "po_type", className: "camel-case-text" },
            { data: "supplier.name", name: "supplier.name" },
            {
                data: "ordered_date",
                name: "ordered_date",
                className: "text-center",
            },
            {
                data: "status",
                name: "status",
                className: "text-center camel-case-text",
                orderable: false,
            },
            { data: "remarks", name: "remarks", className: "text-center" },
            {
                data: "actions",
                name: "actions",
                orderable: false,
                searchable: false,
                className: "text-center",
            },
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
// Initialize Choices
// ----------------------------------------------------
function initChoices(element, isModal = false) {
    if (!element) return;

    if (element.choices) {
        try {
            element.choices.destroy();
        } catch (e) { }
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

function handleGeneratePO(generatePOUrl) {
    $("#addPurchaseOrderModal").on("show.bs.modal", function () {
        $.get(generatePOUrl, function (data) {
            $("#createPONumber").val(data.po_number);
            $("#createPONumberHidden").val(data.po_number);
        });

        // Reset Deliver To state on show
        const deliverToEntityEl = document.getElementById('deliver_to_entity_id');
        if (deliverToEntityEl && deliverToEntityEl.choices) {
            deliverToEntityEl.choices.setChoiceByValue("");
        }

        const deliverToAddressEl = document.getElementById('deliver_to_id');
        if (deliverToAddressEl && deliverToAddressEl.choices) {
            resetChoices(deliverToAddressEl.choices, []);
        }

        // Initially hide actions
        console.log("PO Modal show: hiding delivery actions");
        $('#delivery_address_actions').attr('style', 'display: none !important');
        $('#client_actions').attr('style', 'display: none !important');
    });
}

function handleSupplier() {
    const $form = $("#addSupplierForm");

    $("#saveSupplierBtn").on("click", function () {
        $.ajax({
            url: $form.data("url"),
            type: "POST",
            data: $form.serialize(),
            success: function (supplier) {
                // Append and select new supplier
                $("#supplier_id").append(
                    `<option value="${supplier.id}" selected>${supplier.name}</option>`
                );
                initChoices(document.getElementById("supplier_id"), true);

                // Reset form
                $form[0].reset();

                // Close offcanvas (NOT collapse, NOT modal)
                if (window.bootstrap) {
                    const offcanvasEl =
                        document.getElementById("supplierOffcanvas");
                    const offcanvas =
                        bootstrap.Offcanvas.getOrCreateInstance(offcanvasEl);
                    offcanvas.hide();
                }
            },
            error: function (xhr) {
                if (xhr.status === 422) {
                    showMessage(
                        "error",
                        Object.values(xhr.responseJSON.errors).join("\n")
                    );
                } else {
                    showMessage(
                        "error",
                        "Something went wrong while saving supplier"
                    );
                }
            },
        });
    });
}

function handleClientManagers() {
    // Edit Client Shortcut
    $(document).on('click', '.edit-client-shortcut', function () {
        const clientId = $("#deliver_to_entity_id").val();
        if (!clientId || clientId === 'company') return;

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
                    const deliverToEntityEl = document.getElementById('deliver_to_entity_id');
                    $(`#deliver_to_entity_id option[value="${client.id}"]`).text(client.name);
                    initChoices(deliverToEntityEl, true);
                    deliverToEntityEl.choices.setChoiceByValue(String(client.id));
                    
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
                const deliverToEntityEl = document.getElementById('deliver_to_entity_id');
                
                // Add to select group
                const optgroup = $(deliverToEntityEl).find('optgroup[label="Clients"]');
                optgroup.append(`<option value="${client.id}">${client.name}</option>`);
                
                initChoices(deliverToEntityEl, true);
                deliverToEntityEl.choices.setChoiceByValue(String(client.id));
                $(deliverToEntityEl).trigger('change');

                $form[0].reset();
                bootstrap.Offcanvas.getOrCreateInstance(document.getElementById("clientOffcanvas")).hide();
                showMessage("success", response.message);
            }
        });
    });
}

$(document).ready(function () {
    const tableSelector = "#poTable";
    const ajaxUrl = $(tableSelector).data("url");
    window.poTable = initializePOTable(tableSelector, ajaxUrl);

    initChoices(document.getElementById("supplier_id"), true);
    handleGeneratePO(window.generatePOUrl);
    handleSupplier();
    handleClientManagers();

    handlePOItemsTable({
        container: "#addPurchaseOrderModal",
    });

    const deliverToEntityEl = document.getElementById('deliver_to_entity_id');
    const deliverToAddressEl = document.getElementById('deliver_to_id');
    const mainSupplierEl = document.getElementById('supplier_id');

    let deliverToEntityChoice = null;
    let deliverToAddressChoice = null;

    if (deliverToEntityEl) {
        deliverToEntityChoice = new Choices(deliverToEntityEl, { searchEnabled: true, itemSelectText: '', position: 'bottom' });
        deliverToEntityEl.choices = deliverToEntityChoice;
    }

    if (deliverToAddressEl) {
        deliverToAddressChoice = new Choices(deliverToAddressEl, { searchEnabled: true, itemSelectText: '', position: 'bottom' });
        deliverToAddressEl.choices = deliverToAddressChoice;
    }

    function toggleDeliveryActions(entityId) {
        const $addrActions = $('#delivery_address_actions');
        const $clientActions = $('#client_actions');
        console.log("toggleDeliveryActions called with:", entityId);
        
        if (!entityId) {
            $addrActions.attr('style', 'display: none !important');
            $clientActions.attr('style', 'display: none !important');
        } else if (entityId === 'company') {
            $addrActions.attr('style', 'display: none !important');
            $clientActions.attr('style', 'display: none !important');
        } else {
            $addrActions.attr('style', 'display: flex !important');
            $clientActions.attr('style', 'display: flex !important');
        }
    }

    // When Deliver To Entity changes, fetch its addresses
    $(document).on('change', '#deliver_to_entity_id', function () {
        const entityId = this.value;
        console.log("deliver_to_entity_id changed to:", entityId);
        toggleDeliveryActions(entityId);

        if (!entityId) {
            resetChoices(deliverToAddressChoice, []);
            return;
        }

        let url = entityId === 'company' ? '/company/addresses' : `/clients/${entityId}/addresses`;

        $.get(url, function (data) {
            resetChoices(deliverToAddressChoice, data);
            if (data.length > 0) {
                deliverToAddressChoice.setChoiceByValue(String(data[0].id));
                if (entityId !== 'company') {
                    $(`.edit-address-shortcut[data-select-id="deliver_to_id"]`).fadeIn();
                }
            } else {
                $(`.edit-address-shortcut[data-select-id="deliver_to_id"]`).fadeOut();
            }
        });
    });

    // Handle Address Dropdowns for new supplier/client offcanvas
    const otherAddressSelectIds = ['addSupplierAddress', 'edit_deliver_to_id', 'add_client_billing_address', 'add_client_shipping_address'];

    fetchAllAddresses(function (data) {
        otherAddressSelectIds.forEach(id => {
            const el = document.getElementById(id);
            if (el) {
                el.choices = new Choices(el, { searchEnabled: true, itemSelectText: '', position: 'bottom' });
                resetChoices(el.choices, data);
            }
        });
    });

    // Handle Edit Address Shortcut Logic
    $(document).on('change', '#addSupplierAddress, #deliver_to_id, #edit_deliver_to_id', function () {
        const selectId = this.id;
        const val = this.value;
        const $editBtn = $(`.edit-address-shortcut[data-select-id="${selectId}"]`);

        if (val) {
            $editBtn.fadeIn();
        } else {
            $editBtn.fadeOut();
        }
    });

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

    // Handle Edit Address form submission
    const editAddrForm = document.getElementById('editAddressForm');
    if (editAddrForm && !editAddrForm.dataset.listenerAttached) {
        editAddrForm.dataset.listenerAttached = "true";

        editAddrForm.addEventListener('submit', function (e) {
            e.preventDefault();
            const formData = new FormData(editAddrForm);
            const addressId = formData.get('id');

            $.ajax({
                url: `/addresses/${addressId}`,
                method: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                headers: {
                    'X-HTTP-Method-Override': 'PUT',
                    'X-XSRF-TOKEN': decodeURIComponent(document.cookie.match(/XSRF-TOKEN=([^;]+)/)?.[1] || '')
                },
                success: function (response) {
                    const allAddressIds = ['addSupplierAddress', 'deliver_to_id', 'edit_deliver_to_id'];
                    const entityId = document.getElementById('deliver_to_entity_id')?.value;

                    if (entityId && editAddrForm.dataset.targetSelect === 'deliver_to_id') {
                        // Re-fetch only this entity's addresses to keep it filtered
                        let url = entityId === 'company' ? '/company/addresses' : `/clients/${entityId}/addresses`;
                        $.get(url, function (data) {
                            if (deliverToAddressChoice) {
                                resetChoices(deliverToAddressChoice, data);
                                deliverToAddressChoice.setChoiceByValue(String(response.address.id));
                            }
                        });
                    } else {
                        // Refresh everything else
                        fetchAllAddresses(function (data) {
                            allAddressIds.forEach(id => {
                                const el = document.getElementById(id);
                                if (el && el.choices && id !== 'deliver_to_id') {
                                    const currentVal = el.value;
                                    resetChoices(el.choices, data);
                                    if (currentVal) el.choices.setChoiceByValue(currentVal);
                                }
                            });
                        });
                    }

                    const offcanvasEl = document.getElementById('editAddressOffcanvas');
                    if (offcanvasEl) {
                        bootstrap.Offcanvas.getOrCreateInstance(offcanvasEl).hide();
                    }
                }
            });
        });
    }

    // Enables the address form AJAX submission binding
    initAddressOffcanvas();
});
