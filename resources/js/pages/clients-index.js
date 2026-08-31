import $ from "jquery";
import "datatables.net-dt"; // DataTables core
import Choices from "choices.js";

let editBillingChoice;
let editShippingChoice;
let globalAddresses = [];

export function fetchAllAddresses(callback) {
    $.get('/addresses/all', function (data) {
        globalAddresses = data;
        if (callback) callback(data);
    }).fail(function(xhr) {
        console.error('Failed to load addresses:', xhr);
    });
}

export function resetChoices(choiceInstance, items, selectedValue = null) {
    if (!choiceInstance) return;
    const choicesData = items.map(item => ({
        value: String(item.id),
        label: item.full_address,
        selected: selectedValue ? String(selectedValue) === String(item.id) : false,
    }));

    choiceInstance.clearStore();
    choiceInstance.setChoices(choicesData, 'value', 'label', true);
}

// ---------------------------
// AJAX Add Address via Offcanvas
// ---------------------------
export function initAddressOffcanvas() {
    // Dynamically set WHICH select element to update when an offcanvas opens
    $(document).on('show.bs.offcanvas', '.offcanvas', function (e) {
        const button = $(e.relatedTarget);
        const targetSelectId = button.data('target-select');
        if (targetSelectId) {
            $(this).find('form').attr('data-target-select', targetSelectId);
        }
    });

    // Handle the generic address form submit
    $(document).on('submit', '[id$="AddressForm"]', function (e) {
        e.preventDefault();
        
        const form = $(this);
        const targetElId = form.attr('data-target-select');
        const offcanvasId = form.attr('data-offcanvas-id');

        const saveBtn = form.find('button[type="submit"]');
        const saveText = saveBtn.find('.save-text');
        const saveSpinner = saveBtn.find('.save-spinner');
        const statusDiv = form.find('.mb-2[id$="-status"]');

        saveBtn.prop('disabled', true);
        saveText.addClass('d-none');
        saveSpinner.removeClass('d-none');
        statusDiv.hide().empty();

        let saveUrl = '/addresses/store';
        if (targetElId === 'deliver_to_id') {
            const entityId = $('#deliver_to_entity_id').val();
            if (entityId && entityId !== 'company') {
                saveUrl = `/addresses/client/${entityId}`;
            }
        }

        $.ajax({
            url: saveUrl,
            method: 'POST',
            data: form.serialize(),
            success: function (response) {
                statusDiv.show().html('<div class="alert alert-success alert-sm py-1 mb-0">Address saved!</div>');
                
                // Add the new address to our global list if it doesn't exist
                const exists = globalAddresses.find(a => String(a.id) === String(response.id));
                if (!exists) {
                    globalAddresses.push(response);
                }

                if (targetElId) {
                    const selectEl = document.getElementById(targetElId);
                    const choicesInstance = selectEl ? (selectEl.choices || selectEl.choiceInstance) : null;

                    if (choicesInstance) {
                        if (targetElId === 'deliver_to_id') {
                            const entityId = $('#deliver_to_entity_id').val();
                            if (entityId && entityId !== 'company') {
                                // Re-fetch only this client's addresses to keep it filtered
                                $.get(`/clients/${entityId}/addresses`, function (clientAddresses) {
                                    resetChoices(choicesInstance, clientAddresses, response.id);
                                });
                            } else if (entityId === 'company') {
                                $.get('/company/addresses', function (branchAddresses) {
                                    resetChoices(choicesInstance, branchAddresses, response.id);
                                });
                            }
                        } else {
                            resetChoices(choicesInstance, globalAddresses, response.id);
                        }
                    }
                }

                setTimeout(() => {
                    const offcanvasEl = document.getElementById(offcanvasId);
                    if (offcanvasEl) {
                        const bsOffcanvas = window.bootstrap.Offcanvas.getInstance(offcanvasEl) || new window.bootstrap.Offcanvas(offcanvasEl);
                        if(bsOffcanvas) bsOffcanvas.hide();
                    }
                    form[0].reset();
                    statusDiv.hide();
                    saveBtn.prop('disabled', false);
                    saveText.removeClass('d-none');
                    saveSpinner.addClass('d-none');
                }, 800);
            },
            error: function (xhr) {
                let msg = 'Failed to save address.';
                if (xhr.status === 422) {
                    msg = Object.values(xhr.responseJSON.errors).flat().join('<br>');
                } else if (xhr.responseJSON && xhr.responseJSON.message) {
                    msg = xhr.responseJSON.message;
                }
                statusDiv.show().html(`<div class="alert alert-danger alert-sm py-1 mb-0">${msg}</div>`);
                saveBtn.prop('disabled', false);
                saveText.removeClass('d-none');
                saveSpinner.addClass('d-none');
            }
        });
    });
}


// ---------------------------
// Initialize Clients DataTable
// ---------------------------
export function initializeClientsTable(tableSelector, ajaxUrl) {
    const $table = $(tableSelector);

    if (!$table.length) return null;

    return $table.DataTable({
        processing: true,
        serverSide: true,
        ajax: ajaxUrl,
        columns: [
            { data: 'name', name: 'name' },
            { data: 'contact_person', name: 'contact_person', orderable: false },
            { data: 'email', name: 'email', orderable: false },
            { data: 'phone', name: 'phone', orderable: false, className: 'text-center'  },
            {
                data: 'created_at',
                name: 'created_at',
                className: 'text-center' ,
                render: function (data) {
                    if (!data) return '';
                    const date = new Date(data);
                    const day = String(date.getDate()).padStart(2, '0');
                    const month = String(date.getMonth() + 1).padStart(2, '0');
                    const year = date.getFullYear();
                    return `${year}-${month}-${day}`;
                }
            },
            {
                data: 'actions',
                name: 'actions',
                orderable: false,
                searchable: false,
                className: 'text-center'
            }
        ],
        responsive: true,
        pageLength: 10,
    });
}

// ---------------------------
// Handle Edit Client Modal
// ---------------------------
export function handleEditClientModal() {
    $(document).on('click', '.edit-user-btn', function () {
        const clientId = $(this).data('id');
        const clientName = $(this).data('name');
        const clientContactPerson = $(this).data('contact_person');
        const clientEmail = $(this).data('email');
        const clientPhone = $(this).data('phone');
        const billingAddressId = $(this).data('billing_address_id');
        const shippingAddressId = $(this).data('shipping_address_id');
        const clientGstNumber = $(this).data('gst_number');
        const clientNotes = $(this).data('notes');

        $('#editClientId').val(clientId);
        $('#editClientName').val(clientName);
        $('#editClientContactPerson').val(clientContactPerson);
        $('#editClientEmail').val(clientEmail);
        $('#editClientPhone').val(clientPhone);
        $('#editClientGstNumber').val(clientGstNumber);
        $('#editClientNotes').val(clientNotes);
        
        $('#editClientForm').attr('action', `/clients/${clientId}`);

        // Set choices
        if (editBillingChoice) resetChoices(editBillingChoice, globalAddresses, billingAddressId);
        if (editShippingChoice) resetChoices(editShippingChoice, globalAddresses, shippingAddressId);
    });
}


$(document).ready(function () {
    // Initialize the clients table
    const tableSelector = '#clientsTable';
    const ajaxUrl = $('#clientsTable').data('url');
    window.clientsTable = initializeClientsTable(tableSelector, ajaxUrl);

    // Load Addresses and Init Add Client Modal Choices
    fetchAllAddresses(function(data) {
        const addBillingSelect = document.getElementById('addBillingAddress');
        if (addBillingSelect) {
            addBillingSelect.choices = new Choices(addBillingSelect, { searchEnabled: true, itemSelectText: '' });
            resetChoices(addBillingSelect.choices, data);
        }

        const addShippingSelect = document.getElementById('addShippingAddress');
        if (addShippingSelect) {
            addShippingSelect.choices = new Choices(addShippingSelect, { searchEnabled: true, itemSelectText: '' });
            resetChoices(addShippingSelect.choices, data);
        }

        // Init Edit Client Choices
        const editBillingSelect = document.getElementById('editClientBillingAddress');
        if (editBillingSelect) {
            editBillingChoice = new Choices(editBillingSelect, { searchEnabled: true, itemSelectText: '' });
            editBillingSelect.choices = editBillingChoice;
            resetChoices(editBillingChoice, data);
        }

        const editShippingSelect = document.getElementById('editClientShippingAddress');
        if (editShippingSelect) {
            editShippingChoice = new Choices(editShippingSelect, { searchEnabled: true, itemSelectText: '' });
            editShippingSelect.choices = editShippingChoice;
            resetChoices(editShippingChoice, data);
        }
    });

    handleEditClientModal();
    initAddressOffcanvas();
});
