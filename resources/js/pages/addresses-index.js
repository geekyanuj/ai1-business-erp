import $ from "jquery";
import "datatables.net-dt";

$(document).ready(function () {
    const tableSelector = '#addressesTable';
    if (!$(tableSelector).length) return;

    const table = $(tableSelector).DataTable({
        processing: true,
        serverSide: true,
        ajax: '/addresses/data',
        columns: [
            { data: 'address_line_1', name: 'address_line_1' },
            { data: 'address_line_2', name: 'address_line_2' },
            { data: 'city', name: 'city' },
            { data: 'state', name: 'state' },
            { data: 'country', name: 'country' },
            { data: 'postal_code', name: 'postal_code' },
            { data: 'actions', name: 'actions', orderable: false, searchable: false, className: 'text-center' }
        ],
        responsive: true
    });

    // Handle Edit button click
    $(document).on('click', '.edit-address-btn', function () {
        const addressId = $(this).data('id');
        console.log('Edit button clicked for address ID:', addressId);

        $.get(`/addresses/${addressId}`, function (address) {
            console.log('Fetched address data:', address);
            const form = document.getElementById('editAddressForm');
            if (form) {
                form.action = `/addresses/${addressId}`;
                form.querySelector('[name="address_line_1"]').value = address.address_line_1 || '';
                form.querySelector('[name="address_line_2"]').value = address.address_line_2 || '';
                form.querySelector('[name="city"]').value           = address.city || '';
                form.querySelector('[name="state"]').value          = address.state || '';
                form.querySelector('[name="country"]').value        = address.country || '';
                form.querySelector('[name="postal_code"]').value    = address.postal_code || '';

                const modalEl = document.getElementById('editAddressModal');
                if (modalEl) {
                    const modal = bootstrap.Modal.getOrCreateInstance(modalEl);
                    modal.show();
                } else {
                    console.error('Modal element #editAddressModal not found');
                }
            } else {
                console.error('Form #editAddressForm not found');
            }
        }).fail(function(xhr) {
            console.error('Failed to fetch address data:', xhr);
            alert('Failed to load address data. Please try again.');
        });
    });

    // Handle update form submission
    const editForm = document.getElementById('editAddressForm');
    if (editForm) {
        editForm.addEventListener('submit', function (e) {
            e.preventDefault();

            const statusEl  = document.getElementById('editAddressForm-status');
            const submitBtn = editForm.querySelector('button[type="submit"]');
            const saveText  = submitBtn ? submitBtn.querySelector('.save-text')    : null;
            const spinner   = submitBtn ? submitBtn.querySelector('.save-spinner') : null;

            // Show spinner
            if (saveText)  saveText.classList.add('d-none');
            if (spinner)   spinner.classList.remove('d-none');
            if (submitBtn) submitBtn.disabled = true;
            if (statusEl)  { statusEl.style.display = 'none'; statusEl.innerHTML = ''; }

            const formData = new FormData(editForm);
            formData.append('_method', 'PUT'); // Laravel spoofing

            $.ajax({
                url: editForm.action,
                method: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                headers: { 'X-XSRF-TOKEN': decodeURIComponent(document.cookie.match(/XSRF-TOKEN=([^;]+)/)?.[1] || '') },
                success: function (response) {
                    if (statusEl) {
                        statusEl.style.display = 'block';
                        statusEl.innerHTML = '<div class="alert alert-success alert-sm py-1 mb-0">✅ Address updated!</div>';
                    }

                    table.ajax.reload(null, false); // Reload table

                    setTimeout(function () {
                        const modalEl = document.getElementById('editAddressModal');
                        if (modalEl) {
                            const modal = bootstrap.Modal.getInstance(modalEl);
                            if (modal) modal.hide();
                        }
                        editForm.reset();
                        if (statusEl) statusEl.style.display = 'none';
                    }, 1000);
                },
                error: function (xhr) {
                    let message = 'Failed to update address.';
                    if (xhr.responseJSON?.errors) {
                        message = Object.values(xhr.responseJSON.errors).flat().join('<br>');
                    }
                    if (statusEl) {
                        statusEl.style.display = 'block';
                        statusEl.innerHTML = `<div class="alert alert-danger alert-sm py-1 mb-0">${message}</div>`;
                    }
                },
                complete: function () {
                    if (saveText)  saveText.classList.remove('d-none');
                    if (spinner)   spinner.classList.add('d-none');
                    if (submitBtn) submitBtn.disabled = false;
                }
            });
        });
    }

    // Handle create form submission
    const createForm = document.getElementById('createAddressForm');
    if (createForm) {
        createForm.addEventListener('submit', function (e) {
            e.preventDefault();

            const statusEl  = document.getElementById('createAddressForm-status');
            const submitBtn = createForm.querySelector('button[type="submit"]');
            const saveText  = submitBtn ? submitBtn.querySelector('.save-text')    : null;
            const spinner   = submitBtn ? submitBtn.querySelector('.save-spinner') : null;

            // Show spinner
            if (saveText)  saveText.classList.add('d-none');
            if (spinner)   spinner.classList.remove('d-none');
            if (submitBtn) submitBtn.disabled = true;
            if (statusEl)  { statusEl.style.display = 'none'; statusEl.innerHTML = ''; }

            const formData = new FormData(createForm);

            $.ajax({
                url: '/addresses/store',
                method: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                headers: { 'X-XSRF-TOKEN': decodeURIComponent(document.cookie.match(/XSRF-TOKEN=([^;]+)/)?.[1] || '') },
                success: function (response) {
                    if (statusEl) {
                        statusEl.style.display = 'block';
                        statusEl.innerHTML = '<div class="alert alert-success alert-sm py-1 mb-0">✅ Address created!</div>';
                    }

                    table.ajax.reload(null, false); // Reload table

                    setTimeout(function () {
                        const modalEl = document.getElementById('addAddressModal');
                        if (modalEl) {
                            const modal = bootstrap.Modal.getInstance(modalEl);
                            if (modal) modal.hide();
                        }
                        createForm.reset();
                        if (statusEl) statusEl.style.display = 'none';
                    }, 1000);
                },
                error: function (xhr) {
                    let message = 'Failed to create address.';
                    if (xhr.responseJSON?.errors) {
                        message = Object.values(xhr.responseJSON.errors).flat().join('<br>');
                    }
                    if (statusEl) {
                        statusEl.style.display = 'block';
                        statusEl.innerHTML = `<div class="alert alert-danger alert-sm py-1 mb-0">${message}</div>`;
                    }
                },
                complete: function () {
                    if (saveText)  saveText.classList.remove('d-none');
                    if (spinner)   spinner.classList.add('d-none');
                    if (submitBtn) submitBtn.disabled = false;
                }
            });
        });
    }
});
