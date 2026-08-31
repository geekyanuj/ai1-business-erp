import $ from "jquery";
import "datatables.net-dt";
import Choices from "choices.js";
import { fetchAllAddresses, resetChoices, initAddressOffcanvas } from "./clients-index.js"; // Re-use address management!

let editAddressChoice;
let globalAddresses = [];

$(document).ready(function () {

    const table = $('#suppliersTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: $('#suppliersTable').data('url'),
        columns: [
            { data: 'name', name: 'name' },
            { data: 'phone', orderable: false, className: 'text-center' },
            { data: 'email', orderable: false },
            { data: 'gst_number', orderable: false, className: 'text-center' },
            {
                data: 'created_at',
                className: 'text-center',
                render: data => {
                    if (!data) return '';
                    return new Date(data).toISOString().split('T')[0];
                }
            },
            { data: 'actions', orderable: false, searchable: false }
        ]
    });

    // Handle Address Dropdowns
    fetchAllAddresses(function(data) {
        globalAddresses = data;

        const addAddressSelect = document.getElementById('addSupplierAddress');
        if (addAddressSelect) {
            addAddressSelect.choiceInstance = new Choices(addAddressSelect, { searchEnabled: true, itemSelectText: '' });
            resetChoices(addAddressSelect.choiceInstance, data);
        }

        const editAddressSelect = document.getElementById('editSupplierAddress');
        if (editAddressSelect) {
            editAddressChoice = new Choices(editAddressSelect, { searchEnabled: true, itemSelectText: '' });
            editAddressSelect.choiceInstance = editAddressChoice;
            resetChoices(editAddressChoice, data);
        }
    });

    // ---------------------------
    // Populate Edit Modal
    // ---------------------------
    $(document).on('click', '.edit-supplier-btn', function () {

        const id = $(this).data('id');

        $('#editName').val($(this).data('name'));
        $('#editPhone').val($(this).data('phone'));
        $('#editEmail').val($(this).data('email'));
        $('#editGst').val($(this).data('gst_number'));
        $('#editSupplierForm').attr('action', `/suppliers/${id}`);

        const addressId = $(this).data('address_id');
        if (editAddressChoice) {
            resetChoices(editAddressChoice, globalAddresses, addressId);
        }
    });

    // ---------------------------
    // Phone validation
    // ---------------------------
    $(document).on('input', 'input[name="phone"]', function () {
        this.value = this.value.replace(/\D/g, '').slice(0, 10);
    });

    // Init address saving functionality
    initAddressOffcanvas();
});
