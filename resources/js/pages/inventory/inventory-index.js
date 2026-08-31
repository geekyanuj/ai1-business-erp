import $ from "jquery";

/* -------------------------------------------------
 | LOAD INVENTORY GRID
 -------------------------------------------------*/
function loadInventoryGrid(type) {
    const container = $(`.inventory-grid[data-type="${type}"]`);
    const section = container.closest(".inventory-section");

    $.ajax({
        url: container.data("url"),
        method: "GET",
        data: {
            inventory_type: type,
            search_text: $("#universalSearch").val(),
            location: section.find(".location-filter").val(),
        },
        beforeSend() {
            container.html(`
                <div class="col-12 text-center py-4 text-muted">
                    Loading...
                </div>
            `);
        },
        success(response) {
            let html = "";

            if (!response.data.length) {
                container.html(`
                    <div class="col-12 text-center py-4 text-muted">
                        No inventory found
                    </div>
                `);
                return;
            }

            response.data.forEach((item) => {
                const freeStock =
                    item.quantity_available - item.quantity_reserved;

                html += `
                <div class="col-xl-3 col-lg-4 col-md-6">
                    <div class="card shadow-sm h-100">

                        <div class="card-body">

                            <span class="badge ${
                                type === "raw"
                                    ? "bg-info text-dark"
                                    : type === "equipment"
                                    ? "bg-warning text-dark"
                                    : "bg-primary"
                            } mb-2">
                                ${type === "equipment" ? '<i class="fa fa-tools me-1"></i>' : ""}${type.toUpperCase()}
                            </span>

                            <h6 class="mb-1">
                                ${item.our_part_no ?? item.material_name}
                            </h6>

                            <small class="text-muted d-block mb-2">
                                ${item.location}
                            </small>

                            <div class="d-flex justify-content-between">
                                <span>Available</span>
                                <strong>${freeStock}</strong>
                            </div>

                            <div class="d-flex justify-content-between">
                                <span>Reserved</span>
                                <strong>${item.quantity_reserved}</strong>
                            </div>

                            <div class="mt-2">
                                ${
                                    freeStock <= 0
                                        ? `<span class="badge bg-danger">Out</span>`
                                        : freeStock < 10
                                        ? `<span class="badge bg-warning text-dark">Low</span>`
                                        : `<span class="badge bg-success">OK</span>`
                                }
                            </div>

                        </div>

                        <div class="card-footer bg-light text-end">
                            ${item.actions}
                        </div>

                    </div>
                </div>
                `;
            });

            container.html(html);
        },
    });
}

/* -------------------------------------------------
 | DEBOUNCE
 -------------------------------------------------*/
function debounce(fn, delay = 400) {
    let timer;
    return (...args) => {
        clearTimeout(timer);
        timer = setTimeout(() => fn.apply(this, args), delay);
    };
}

/* -------------------------------------------------
 | DOM READY
 -------------------------------------------------*/
$(document).ready(function () {
    // Initial load
    loadInventoryGrid("ready");

    // Tab switch
    $('button[data-bs-toggle="tab"]').on("shown.bs.tab", function (e) {
        const type = $(e.target).data("type");
        loadInventoryGrid(type);
    });

    // Universal search
    $("#universalSearch").on(
        "keyup",
        debounce(() => {
            const activeType = $("#inventoryTabs .nav-link.active").data(
                "type"
            );
            loadInventoryGrid(activeType);
        })
    );

    // Location filter per tab
    $(".location-filter").on("change", function () {
        const type = $(this).closest(".inventory-section").data("type");

        loadInventoryGrid(type);
    });
});
