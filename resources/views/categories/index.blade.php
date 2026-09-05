@extends('layouts.app')

@section('title')
    Categories
@endsection

@section('content')

    <div class="mb-3">
        <h5 class="my-primary-color">Categories</h5>

        <small class="text-muted">
            <a class="text-decoration-none" href="{{ route('dashboard') }}">
                Home
            </a>

            <i class="fa-solid fa-angle-right"></i>

            Categories
        </small>
    </div>

    <div class="row">
        <div class="col-md-12">

            <div class="user-action">

                <div class="d-flex justify-content-between align-items-center mb-3">

                    <div>
                        <h6 class="mb-0">
                            Product Categories
                        </h6>

                        <small class="text-muted">
                            Manage categories and optional sub categories
                        </small>
                    </div>

                    <button
                        type="button"
                        class="btn btn-sm text-white bg-my-primary"
                        data-bs-toggle="modal"
                        data-bs-target="#addCategoryModal">

                        <i class="fa-solid fa-plus"></i>

                        Add Category
                    </button>

                </div>


                <div class="card">

                    <div class="card-body my-0 py-0">

                        <table
                            id="categoriesTable"
                            data-url="{{ route('categories.data') }}"
                            class="table table-sm table-bordered table-striped pt-2 w-100">

                            <thead>
                                <tr>
                                    <th style="width: 25%;">
                                        Category
                                    </th>

                                    <th style="width: 45%;">
                                        Sub Categories
                                    </th>

                                    <th style="width: 15%;">
                                        Status
                                    </th>

                                    <th style="width: 15%;">
                                        Actions
                                    </th>
                                </tr>
                            </thead>

                        </table>

                    </div>

                </div>

            </div>

        </div>
    </div>


    {{-- ========================================================= --}}
    {{-- ADD CATEGORY MODAL --}}
    {{-- ========================================================= --}}

    <div
        class="modal fade"
        id="addCategoryModal"
        tabindex="-1"
        aria-labelledby="addCategoryModalLabel"
        aria-hidden="true">

        <div class="modal-dialog modal-lg">

            <div class="modal-content">

                <form id="addCategoryForm">

                    @csrf

                    <div class="modal-header py-2">

                        <h6
                            class="modal-title my-primary-color"
                            id="addCategoryModalLabel">

                            Add Category

                        </h6>

                        <button
                            type="button"
                            class="btn-close"
                            data-bs-dismiss="modal">
                        </button>

                    </div>


                    <div class="modal-body">

                        <div class="row g-3">

                            {{-- Category Name --}}
                            <div class="col-md-8">

                                <label
                                    for="categoryName"
                                    class="form-label mb-1 required-field">

                                    Category Name

                                </label>

                                <input
                                    type="text"
                                    name="name"
                                    id="categoryName"
                                    class="form-control form-control-sm"
                                    placeholder="Enter category name"
                                    required>

                            </div>


                            {{-- Status --}}
                            <div class="col-md-4">

                                <label
                                    for="categoryStatus"
                                    class="form-label mb-1">

                                    Status

                                </label>

                                <select
                                    name="is_active"
                                    id="categoryStatus"
                                    class="form-select form-select-sm">

                                    <option value="1" selected>
                                        Active
                                    </option>

                                    <option value="0">
                                        Inactive
                                    </option>

                                </select>

                            </div>

                        </div>

                    </div>


                    <div class="modal-footer py-2">

                        <button
                            type="button"
                            class="btn btn-secondary btn-sm px-3"
                            data-bs-dismiss="modal">

                            Cancel

                        </button>

                        <button
                            type="submit"
                            class="btn btn-primary bg-my-primary btn-sm px-3">

                            <i class="fas fa-save me-1"></i>

                            Save Category

                        </button>

                    </div>

                </form>

            </div>

        </div>

    </div>


    {{-- ========================================================= --}}
    {{-- EDIT CATEGORY MODAL --}}
    {{-- ========================================================= --}}

    <div
        class="modal fade"
        id="editCategoryModal"
        tabindex="-1"
        aria-labelledby="editCategoryModalLabel"
        aria-hidden="true">

        <div class="modal-dialog modal-lg">

            <div class="modal-content">

                <form id="editCategoryForm">

                    @csrf

                    @method('PUT')

                    <input
                        type="hidden"
                        id="editCategoryId"
                        name="id">


                    <div class="modal-header py-2">

                        <div>

                            <h6
                                class="modal-title my-primary-color mb-0"
                                id="editCategoryModalLabel">

                                Edit Category

                            </h6>

                            <small
                                class="text-muted"
                                id="editCategorySubtitle">

                            </small>

                        </div>

                        <button
                            type="button"
                            class="btn-close"
                            data-bs-dismiss="modal">
                        </button>

                    </div>


                    <div class="modal-body">

                        <div class="row g-3">

                            {{-- Category Name --}}
                            <div class="col-md-8">

                                <label
                                    for="editCategoryName"
                                    class="form-label mb-1 required-field">

                                    Category Name

                                </label>

                                <input
                                    type="text"
                                    name="name"
                                    id="editCategoryName"
                                    class="form-control form-control-sm"
                                    required>

                            </div>


                            {{-- Status --}}
                            <div class="col-md-4">

                                <label
                                    for="editCategoryStatus"
                                    class="form-label mb-1">

                                    Status

                                </label>

                                <select
                                    name="is_active"
                                    id="editCategoryStatus"
                                    class="form-select form-select-sm">

                                    <option value="1">
                                        Active
                                    </option>

                                    <option value="0">
                                        Inactive
                                    </option>

                                </select>

                            </div>


                            {{-- Divider --}}
                            <div class="col-md-12">

                                <hr class="my-1">

                            </div>


                            {{-- Sub Categories --}}
                            <div class="col-md-12">

                                <div class="d-flex justify-content-between align-items-center mb-2">

                                    <div>

                                        <label class="form-label mb-0">
                                            Sub Categories
                                        </label>

                                        <div class="small text-muted">
                                            Optional
                                        </div>

                                    </div>

                                    <button
                                        type="button"
                                        id="addSubCategoryBtn"
                                        class="btn btn-sm btn-outline-primary">

                                        <i class="fa-solid fa-plus"></i>

                                        Add Sub Category

                                    </button>

                                </div>


                                <div
                                    id="subCategoriesContainer"
                                    class="border rounded p-2 bg-light">

                                    <div
                                        id="noSubCategories"
                                        class="text-center text-muted py-3">

                                        <i class="fa-solid fa-folder-open mb-2"></i>

                                        <div>
                                            No sub categories
                                        </div>

                                    </div>

                                </div>

                            </div>

                        </div>

                    </div>


                    <div class="modal-footer py-2">

                        <button
                            type="button"
                            class="btn btn-secondary btn-sm px-3"
                            data-bs-dismiss="modal">

                            Close

                        </button>

                        <button
                            type="submit"
                            class="btn btn-primary bg-my-primary btn-sm px-3">

                            <i class="fas fa-save me-1"></i>

                            Update Category

                        </button>

                    </div>

                </form>

            </div>

        </div>

    </div>


    {{-- ========================================================= --}}
    {{-- ADD SUB CATEGORY MODAL --}}
    {{-- ========================================================= --}}

    <div
        class="modal fade"
        id="addSubCategoryModal"
        tabindex="-1"
        aria-labelledby="addSubCategoryModalLabel"
        aria-hidden="true">

        <div class="modal-dialog">

            <div class="modal-content">

                <form id="addSubCategoryForm">

                    @csrf

                    <div class="modal-header py-2">

                        <h6
                            class="modal-title my-primary-color"
                            id="addSubCategoryModalLabel">

                            Add Sub Category

                        </h6>

                        <button
                            type="button"
                            class="btn-close"
                            data-bs-dismiss="modal">
                        </button>

                    </div>


                    <div class="modal-body">

                        <div class="mb-3">

                            <label
                                class="form-label mb-1">

                                Category

                            </label>

                            <input
                                type="text"
                                id="subCategoryParentName"
                                class="form-control form-control-sm"
                                readonly>

                        </div>


                        <div class="mb-3">

                            <label
                                for="subCategoryName"
                                class="form-label mb-1 required-field">

                                Sub Category Name

                            </label>

                            <input
                                type="text"
                                name="name"
                                id="subCategoryName"
                                class="form-control form-control-sm"
                                placeholder="Enter sub category name"
                                required>

                        </div>


                        <div>

                            <label
                                for="subCategoryStatus"
                                class="form-label mb-1">

                                Status

                            </label>

                            <select
                                name="is_active"
                                id="subCategoryStatus"
                                class="form-select form-select-sm">

                                <option value="1" selected>
                                    Active
                                </option>

                                <option value="0">
                                    Inactive
                                </option>

                            </select>

                        </div>

                    </div>


                    <div class="modal-footer py-2">

                        <button
                            type="button"
                            class="btn btn-secondary btn-sm"
                            data-bs-dismiss="modal">

                            Cancel

                        </button>

                        <button
                            type="submit"
                            class="btn btn-primary bg-my-primary btn-sm">

                            <i class="fas fa-save me-1"></i>

                            Save

                        </button>

                    </div>

                </form>

            </div>

        </div>

    </div>


    {{-- ========================================================= --}}
    {{-- DELETE CATEGORY MODAL --}}
    {{-- ========================================================= --}}

    <div
        class="modal fade"
        id="deleteCategoryModal"
        tabindex="-1"
        aria-labelledby="deleteCategoryModalLabel"
        aria-hidden="true">

        <div class="modal-dialog modal-sm">

            <div class="modal-content">

                <form id="deleteCategoryForm">

                    @csrf

                    @method('DELETE')

                    <div class="modal-header py-2">

                        <h6
                            class="modal-title text-danger"
                            id="deleteCategoryModalLabel">

                            Delete Category

                        </h6>

                        <button
                            type="button"
                            class="btn-close"
                            data-bs-dismiss="modal">
                        </button>

                    </div>


                    <div class="modal-body text-center">

                        <i
                            class="fa-solid fa-triangle-exclamation text-danger fs-2 mb-3">
                        </i>

                        <p class="mb-1">
                            Are you sure you want to delete
                        </p>

                        <strong id="deleteCategoryName"></strong>

                        <p class="small text-muted mt-2 mb-0">
                            This action cannot be undone.
                        </p>

                    </div>


                    <div class="modal-footer py-2 justify-content-center">

                        <button
                            type="button"
                            class="btn btn-secondary btn-sm"
                            data-bs-dismiss="modal">

                            Cancel

                        </button>

                        <button
                            type="submit"
                            class="btn btn-danger btn-sm">

                            Delete

                        </button>

                    </div>

                </form>

            </div>

        </div>

    </div>

@endsection


@push('scripts')

    @vite('resources/js/pages/categories-index.js')

@endpush
