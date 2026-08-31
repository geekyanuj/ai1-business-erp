import $ from "jquery";
import "datatables.net-dt"; // DataTables core

// ---------------------------
// Initialize Users DataTable
// ---------------------------
export function initializeUsersTable(tableSelector, ajaxUrl) {
    const $table = $(tableSelector);
    if (!$table.length) return null;

    return $table.DataTable({
        processing: true,
        serverSide: true,
        ajax: ajaxUrl,
        columns: [
            { data: 'name', name: 'name' },
            { data: 'email', name: 'email', orderable: false },
            { data: 'role', name: 'role', orderable: false },
            { data: 'telephone', name: 'telephone', orderable: false, className: 'text-center' },
            {
                data: 'last_login',
                name: 'last_login',
                className: 'text-center'
            },
            {
                data: 'login_status',
                name: 'login_status',
                orderable: false,
                searchable: false,
                className: 'text-center'  
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
// Handle Edit User Modal
// ---------------------------
export function handleEditUserModal() {
    $(document).on('click', '.edit-user-btn', function () {
        const userId = $(this).data('id');
        const name = $(this).data('name');
        const email = $(this).data('email');
        const role = $(this).data('role');
        const telephone = $(this).data('telephone');
        const loginEnabled = $(this).data('login_enabled');

        $('#editUserId').val(userId);
        $('#editUserName').val(name);
        $('#editUserEmail').val(email);
        $('#editUserRole').val(role);
        $('#editUserTelephone').val(telephone);
        $('#editUserLogin_enabled').prop('checked', loginEnabled);

        $('#editUserForm').attr('action', `/users/${userId}`);

        // Hide password section initially
        $('#editPasswordSection').hide();
        $('#resetPasswordToggle').prop('checked', false);
    });
}

// ---------------------------
// Handle Reset Password Toggle
// ---------------------------
export function handleResetPasswordToggle() {
    $(document).on('change', '#resetPasswordToggle', function () {
        if ($(this).is(':checked')) {
            $('#editPasswordSection').show();
        } else {
            $('#editPasswordSection').hide();
            $('#editPassword').val('');
        }
    });
}

// ---------------------------
// User-side telephone validation
// ---------------------------
export function handleTelephoneValidation() {
    ['telephone', 'editUserTelephone'].forEach(function (id) {
        const input = document.getElementById(id);
        if (input) {
            input.addEventListener('input', function () {
                this.value = this.value.replace(/\D/g, '').slice(0, 10);
            });
        }
    });
}

// ---------------------------
// Initialize everything on DOM ready
// ---------------------------
$(document).ready(function () {
    const tableSelector = '#usersTable';
    const ajaxUrl = $(tableSelector).data('url');

    // Initialize the users table
    window.usersTable = initializeUsersTable(tableSelector, ajaxUrl);

    // Handle modals
    handleEditUserModal();

    // Handle reset password toggle
    handleResetPasswordToggle();

    // Handle telephone input validation
    handleTelephoneValidation();
});
