import $ from "jquery";
import "datatables.net-dt";

let categoriesTable = null;
let currentCategoryId = null;

// ============================================================
// CSRF
// ============================================================

$.ajaxSetup({
    headers: {
        "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
    },
});

// ============================================================
// DataTable
// ============================================================

export function initializeCategoriesTable() {
    const $table = $("#categoriesTable");

    if (!$table.length) {
        return null;
    }

    categoriesTable = $table.DataTable({
        processing: true,

        serverSide: true,

        ajax: $table.data("url"),

        autoWidth: false,

        responsive: true,

        pageLength: 10,

        order: [[0, "asc"]],

        columns: [
            {
                data: "name",
                name: "name",
                width: "25%",
            },

            {
                data: "sub_categories",
                name: "sub_categories",
                orderable: false,
                searchable: false,
                width: "45%",
            },

            {
                data: "status",
                name: "is_active",
                className: "text-center",
                width: "15%",
            },

            {
                data: "actions",
                name: "actions",
                orderable: false,
                searchable: false,
                className: "text-center",
                width: "15%",
            },
        ],
    });

    return categoriesTable;
}

// ============================================================
// Reset Add Category Form
// ============================================================

export function resetAddCategoryForm() {
    const form = $("#addCategoryForm")[0];

    if (form) {
        form.reset();
    }

    $("#categoryStatus").val("1");
}

// ============================================================
// Add Category
// ============================================================

export function handleAddCategory() {
    $("#addCategoryForm").on("submit", function (e) {
        e.preventDefault();

        const $form = $(this);

        const $button = $form.find('button[type="submit"]');

        $button
            .prop("disabled", true)
            .html(
                '<span class="spinner-border spinner-border-sm me-1"></span> Saving...',
            );

        $.ajax({
            url: "/categories",

            type: "POST",

            data: $form.serialize(),

            success: function (response) {
                if (response.success) {
                    hideModal("addCategoryModal");

                    resetAddCategoryForm();

                    categoriesTable.ajax.reload(null, false);

                    showSuccessMessage(response.message);
                }
            },

            error: function (xhr) {
                handleAjaxError(xhr);
            },

            complete: function () {
                $button
                    .prop("disabled", false)
                    .html('<i class="fas fa-save me-1"></i> Save Category');
            },
        });
    });
}

// ============================================================
// Edit Category
// ============================================================

export function handleEditCategory() {
    $(document).on("click", ".edit-category-btn", function () {
        const categoryId = $(this).data("id");

        currentCategoryId = categoryId;

        loadCategory(categoryId);
    });
}

function loadCategory(categoryId) {
    $.ajax({
        url: `/categories/${categoryId}`,

        type: "GET",

        success: function (response) {
            const category = response.category;

            $("#editCategoryId").val(category.id);

            $("#editCategoryName").val(category.name);

            $("#editCategoryStatus").val(category.is_active ? "1" : "0");

            $("#editCategorySubtitle").text(`Category #${category.id}`);

            renderSubCategories(category.sub_categories || []);

            const modal = new bootstrap.Modal(
                document.getElementById("editCategoryModal"),
            );

            modal.show();
        },

        error: function (xhr) {
            handleAjaxError(xhr);
        },
    });
}

// ============================================================
// Update Category
// ============================================================

export function handleUpdateCategory() {
    $("#editCategoryForm").on("submit", function (e) {
        e.preventDefault();

        const categoryId = $("#editCategoryId").val();

        const $form = $(this);

        const $button = $form.find('button[type="submit"]');

        $button
            .prop("disabled", true)
            .html(
                '<span class="spinner-border spinner-border-sm me-1"></span> Updating...',
            );

        $.ajax({
            url: `/categories/${categoryId}`,

            type: "PUT",

            data: $form.serialize(),

            success: function (response) {
                if (response.success) {
                    hideModal("editCategoryModal");

                    categoriesTable.ajax.reload(null, false);

                    showSuccessMessage(response.message);
                }
            },

            error: function (xhr) {
                handleAjaxError(xhr);
            },

            complete: function () {
                $button
                    .prop("disabled", false)
                    .html('<i class="fas fa-save me-1"></i> Update Category');
            },
        });
    });
}

// ============================================================
// Render Sub Categories
// ============================================================

function renderSubCategories(subCategories) {
    const $container = $("#subCategoriesContainer");

    $container.empty();

    if (!subCategories.length) {
        $container.html(`
            <div
                id="noSubCategories"
                class="text-center text-muted py-3">

                <i class="fa-solid fa-folder-open mb-2"></i>

                <div>
                    No sub categories
                </div>

            </div>
        `);

        return;
    }

    $.each(subCategories, function (index, subCategory) {
        const statusClass = subCategory.is_active
            ? "bg-success"
            : "bg-secondary";

        const statusText = subCategory.is_active ? "Active" : "Inactive";

        const row = $(`
            <div
                class="sub-category-row d-flex align-items-center justify-content-between border-bottom py-2"
                data-id="${subCategory.id}">

                <div class="d-flex align-items-center gap-2">

                    <i class="fa-solid fa-angle-right text-muted"></i>

                    <span class="sub-category-name">
                        ${escapeHtml(subCategory.name)}
                    </span>

                    <span class="badge ${statusClass}">
                        ${statusText}
                    </span>

                </div>

                <div class="d-flex gap-1">

                    <button
                        type="button"
                        class="btn btn-sm btn-outline-primary edit-sub-category-btn"
                        data-id="${subCategory.id}"
                        data-name="${escapeHtml(subCategory.name)}"
                        data-active="${subCategory.is_active ? 1 : 0}">

                        <i class="fa-solid fa-pen"></i>

                    </button>

                    <button
                        type="button"
                        class="btn btn-sm btn-outline-danger delete-sub-category-btn"
                        data-id="${subCategory.id}"
                        data-name="${escapeHtml(subCategory.name)}">

                        <i class="fa-solid fa-trash"></i>

                    </button>

                </div>

            </div>
        `);

        $container.append(row);
    });
}

// ============================================================
// Add Sub Category Modal
// ============================================================

export function handleAddSubCategory() {
    $("#addSubCategoryBtn").on("click", function () {
        $("#subCategoryParentName").val($("#editCategoryName").val());

        $("#subCategoryName").val("");

        $("#subCategoryStatus").val("1");

        const modal = new bootstrap.Modal(
            document.getElementById("addSubCategoryModal"),
        );

        modal.show();
    });
}

// ============================================================
// Save Sub Category
// ============================================================

export function handleSaveSubCategory() {
    $("#addSubCategoryForm").on("submit", function (e) {
        e.preventDefault();

        if (!currentCategoryId) {
            return;
        }

        const $form = $(this);

        const $button = $form.find('button[type="submit"]');

        $button
            .prop("disabled", true)
            .html(
                '<span class="spinner-border spinner-border-sm me-1"></span> Saving...',
            );

        $.ajax({
            url: `/categories/${currentCategoryId}/sub-categories`,

            type: "POST",

            data: $form.serialize(),

            success: function (response) {
                if (response.success) {
                    hideModal("addSubCategoryModal");

                    $form[0].reset();

                    loadCategory(currentCategoryId);

                    categoriesTable.ajax.reload(null, false);

                    showSuccessMessage(response.message);
                }
            },

            error: function (xhr) {
                handleAjaxError(xhr);
            },

            complete: function () {
                $button
                    .prop("disabled", false)
                    .html('<i class="fas fa-save me-1"></i> Save');
            },
        });
    });
}

// ============================================================
// Edit Sub Category
// ============================================================

export function handleEditSubCategory() {
    $(document).on("click", ".edit-sub-category-btn", function () {
        const subCategoryId = $(this).data("id");

        const currentName = $(this).data("name");

        const currentStatus = $(this).data("active");

        const newName = prompt("Sub Category Name:", currentName);

        if (newName === null) {
            return;
        }

        const name = newName.trim();

        if (!name) {
            alert("Sub category name is required.");
            return;
        }

        $.ajax({
            url: `/categories/${currentCategoryId}/sub-categories/${subCategoryId}`,

            type: "PUT",

            data: {
                name: name,
                is_active: currentStatus,
            },

            success: function (response) {
                if (response.success) {
                    loadCategory(currentCategoryId);

                    categoriesTable.ajax.reload(null, false);

                    showSuccessMessage(response.message);
                }
            },

            error: function (xhr) {
                handleAjaxError(xhr);
            },
        });
    });
}

// ============================================================
// Delete Sub Category
// ============================================================

export function handleDeleteSubCategory() {
    $(document).on("click", ".delete-sub-category-btn", function () {
        const subCategoryId = $(this).data("id");

        const name = $(this).data("name");

        if (!confirm(`Are you sure you want to delete "${name}"?`)) {
            return;
        }

        $.ajax({
            url: `/categories/${currentCategoryId}/sub-categories/${subCategoryId}`,

            type: "DELETE",

            success: function (response) {
                if (response.success) {
                    loadCategory(currentCategoryId);

                    categoriesTable.ajax.reload(null, false);

                    showSuccessMessage(response.message);
                }
            },

            error: function (xhr) {
                handleAjaxError(xhr);
            },
        });
    });
}

// ============================================================
// Delete Category
// ============================================================

export function handleDeleteCategory() {
    $(document).on("click", ".delete-category-btn", function () {
        const categoryId = $(this).data("id");

        const categoryName = $(this).data("name");

        $("#deleteCategoryName").text(categoryName);

        $("#deleteCategoryForm").data("id", categoryId);

        const modal = new bootstrap.Modal(
            document.getElementById("deleteCategoryModal"),
        );

        modal.show();
    });
}

// ============================================================
// Confirm Delete Category
// ============================================================

export function handleConfirmDeleteCategory() {
    $("#deleteCategoryForm").on("submit", function (e) {
        e.preventDefault();

        const categoryId = $(this).data("id");

        $.ajax({
            url: `/categories/${categoryId}`,

            type: "DELETE",

            success: function (response) {
                if (response.success) {
                    hideModal("deleteCategoryModal");

                    categoriesTable.ajax.reload(null, false);

                    showSuccessMessage(response.message);
                }
            },

            error: function (xhr) {
                handleAjaxError(xhr);
            },
        });
    });
}

function hideModal(id) {
    const modalElement = document.getElementById(id);

    if (modalElement) {
        bootstrap.Modal.getOrCreateInstance(modalElement).hide();
    }
}

// ============================================================
// Utility
// ============================================================

function escapeHtml(value) {
    return $("<div>")
        .text(value ?? "")
        .html();
}

function showSuccessMessage(message) {
    $("#category-success-message").remove();

    const $message = $(
        '<div id="category-success-message" class="flash-message flash-success" role="status" aria-live="polite"></div>',
    ).text(message || "Saved successfully.");

    $("body").append($message);

    window.setTimeout(function () {
        $message.fadeOut(250, function () {
            $(this).remove();
        });
    }, 3000);
}

function handleAjaxError(xhr) {
    if (xhr.status === 422 && xhr.responseJSON?.errors) {
        const errors = xhr.responseJSON.errors;

        const messages = Object.values(errors).flat().join("\n");

        alert(messages);

        return;
    }

    const message = xhr.responseJSON?.message || "Something went wrong.";

    alert(message);
}

// ============================================================
// Document Ready
// ============================================================

$(document).ready(function () {
    initializeCategoriesTable();

    handleAddCategory();

    handleEditCategory();

    handleUpdateCategory();

    handleAddSubCategory();

    handleSaveSubCategory();

    handleEditSubCategory();

    handleDeleteSubCategory();

    handleDeleteCategory();

    handleConfirmDeleteCategory();
});
