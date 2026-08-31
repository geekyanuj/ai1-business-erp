import $ from "jquery";
import "datatables.net-bs5";

document.addEventListener("DOMContentLoaded", () => {
    const table = $("#grnsTable");

    if (!table.length) return;

    table.DataTable({
        pageLength: 10,
        lengthChange: true,
        searching: true,
        ordering: true,
        info: true,
        autoWidth: false,
        order: [[1, "desc"]], // GRN No descending
        columnDefs: [
            { orderable: false, targets: [0, 5] },
            { searchable: false, targets: [0, 5] },
        ],
    });
});
