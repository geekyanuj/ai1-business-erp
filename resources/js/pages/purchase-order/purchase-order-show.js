import $ from "jquery";
import Choices from "choices.js";
import { handlePOItemsTable } from "../../modules/po-items-table";

/* ----------------------------------------------------
   INIT CHOICES 
---------------------------------------------------- */
function initChoices(element, isModal = false) {
    if (!element) return null;
    if (element.choices instanceof Choices) return element.choices;

    const instance = new Choices(element, {
        searchEnabled: true,
        allowHTML: false,
        shouldSort: false,
        position: isModal ? "bottom" : "auto",
    });

    element.choices = instance;
    return instance;
}

/* ----------------------------------------------------
   BOOTSTRAP MODAL
---------------------------------------------------- */
document.addEventListener("DOMContentLoaded", () => {
    initChoices(document.getElementById("supplier-dropdown"), true);

    const modal = document.getElementById("editPurchaseOrderModal");
    if (modal) {
        modal.addEventListener("shown.bs.modal", () => {
            handlePOItemsTable({
                container: "#editPurchaseOrderModal",
            });
        });
    }

    document.addEventListener("input", function (e) {
        if (!e.target.classList.contains("quantity-received")) return;

        const input = e.target;
        const remainingQty = parseFloat(input.dataset.remaining) || 0;
        let enteredQty = parseFloat(input.value) || 0;

        // Remove old error message
        input.classList.remove("is-invalid");
        input.nextElementSibling?.remove();

        // Negative guard
        if (enteredQty < 0) {
            input.value = 0;
            enteredQty = 0;
        }

        // Exceed remaining guard
        if (enteredQty > remainingQty) {
            input.value = remainingQty;
            input.classList.add("is-invalid");

            const error = document.createElement("div");
            error.className = "invalid-feedback";
            error.innerText = `Max allowed: ${remainingQty}`;

            input.after(error);
        }
    });

    document.querySelectorAll(".product-map").forEach((el) => {
        initChoices(el, true);
    });

    // --- Address Handling ---
    const deliverToEntitySelect = document.getElementById("edit_deliver_to_entity_id");
    const deliverToIdSelect = document.getElementById("edit_deliver_to_id");
    const addNewAddressBtn = document.querySelector(".action-add-new-address");
    const editAddressBtn = document.querySelector(".action-edit-address");

    function updateAddressActionsVisibility() {
        const entityId = deliverToEntitySelect?.value;
        const addressId = deliverToIdSelect?.value;

        // "New" button only for clients (entityId > 0)
        if (entityId && entityId !== "0") {
            if (addNewAddressBtn) addNewAddressBtn.style.display = "inline-block";
        } else {
            if (addNewAddressBtn) addNewAddressBtn.style.display = "none";
        }

        // "Edit" button only if an address is selected
        if (addressId) {
            if (editAddressBtn) editAddressBtn.style.display = "inline-block";
        } else {
            if (editAddressBtn) editAddressBtn.style.display = "none";
        }
    }

    if (deliverToEntitySelect) {
        deliverToEntitySelect.addEventListener("change", function () {
            const entityId = this.value;
            deliverToIdSelect.innerHTML = '<option value="">Select Address</option>';
            
            if (entityId) {
                let url = "/addresses/fetch-by-entity";
                if (entityId === "0") {
                    url += "?type=company";
                } else {
                    url += "?type=client&entity_id=" + entityId;
                }

                fetch(url)
                    .then(res => res.json())
                    .then(data => {
                        data.addresses.forEach(addr => {
                            const option = document.createElement("option");
                            option.value = addr.id;
                            option.text = addr.address_line_1 + (addr.city ? ", " + addr.city : "");
                            deliverToIdSelect.add(option);
                        });
                        updateAddressActionsVisibility();
                    });
            } else {
                updateAddressActionsVisibility();
            }
        });
    }

    if (deliverToIdSelect) {
        deliverToIdSelect.addEventListener("change", updateAddressActionsVisibility);
    }

    // Initial check
    if (deliverToEntitySelect) {
        updateAddressActionsVisibility();
    }

    // --- Add New Address Form Submission ---
    const addressForm = document.getElementById("supplierAddressForm");
    if (addressForm) {
        addressForm.addEventListener("submit", function (e) {
            e.preventDefault();
            const formData = new FormData(this);
            const entityId = deliverToEntitySelect.value;

            if (entityId && entityId !== "0") {
                formData.append("client_id", entityId);
            }

            fetch("/addresses/store-ajax", {
                method: "POST",
                body: formData,
                headers: {
                    "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content
                }
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    const option = document.createElement("option");
                    option.value = data.address.id;
                    option.text = data.address.address_line_1 + (data.address.city ? ", " + data.address.city : "");
                    deliverToIdSelect.add(option);
                    deliverToIdSelect.value = data.address.id;
                    
                    const offcanvasInstance = bootstrap.Offcanvas.getInstance(document.getElementById("supplierAddressOffcanvas"));
                    if (offcanvasInstance) offcanvasInstance.hide();
                    addressForm.reset();
                    updateAddressActionsVisibility();
                }
            });
        });
    }

    // --- Edit Address Shortcut ---
    $(".edit-address-shortcut").on("click", function() {
        const addressId = deliverToIdSelect.value;
        if (!addressId) return;

        fetch(`/addresses/${addressId}/edit-ajax`)
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    const form = document.getElementById("editAddressForm");
                    form.action = `/addresses/${addressId}/update-ajax`;
                    form.querySelector('[name="address_line_1"]').value = data.address.address_line_1 || '';
                    form.querySelector('[name="address_line_2"]').value = data.address.address_line_2 || '';
                    form.querySelector('[name="city"]').value = data.address.city || '';
                    form.querySelector('[name="state"]').value = data.address.state || '';
                    form.querySelector('[name="postal_code"]').value = data.address.postal_code || '';
                    form.querySelector('[name="country"]').value = data.address.country || 'India';
                }
            });
    });

    const editAddressForm = document.getElementById("editAddressForm");
    if (editAddressForm) {
        editAddressForm.addEventListener("submit", function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            const action = this.action;

            fetch(action, {
                method: "POST",
                body: formData,
                headers: {
                    "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content
                }
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    const selectedOption = deliverToIdSelect.options[deliverToIdSelect.selectedIndex];
                    selectedOption.text = data.address.address_line_1 + (data.address.city ? ", " + data.address.city : "");
                    
                    $("#editAddressModal").modal("hide");
                    editAddressForm.reset();
                }
            });
        });
    }
});
