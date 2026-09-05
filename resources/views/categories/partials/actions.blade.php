<div class="d-flex justify-content-center gap-1">
    <button
        type="button"
        class="btn btn-sm btn-outline-primary edit-category-btn"
        data-id="{{ $category->id }}"
        title="Edit">
        <i class="fa-solid fa-pen"></i>
    </button>

    <button
        type="button"
        class="btn btn-sm btn-outline-danger delete-category-btn"
        data-id="{{ $category->id }}"
        data-name="{{ e($category->name) }}"
        title="Delete">
        <i class="fa-solid fa-trash"></i>
    </button>
</div>
