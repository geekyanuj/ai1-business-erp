import{$ as t}from"./po-items-table-DLl3eRRB.js";import{i as c,h as r}from"./sales-items-table-BKHtIWdD.js";import{r as o,i as f,f as b}from"./clients-index-C90AJKbt.js";import"./dataTables-t187tmru.js";import"./choices-BxcHAull.js";window.$=window.jQuery=t;function y({products:e}){window.products=e,r({enableDiscount:!0,tableSelector:"#editItemsTable",addButtonSelector:"#addEditItemRow",subtotalSelector:"#edit_subtotal",taxSelector:"#edit_total_tax",grandTotalSelector:"#edit_grand_total",cgstSelector:"#edit_cgst_amount",sgstSelector:"#edit_sgst_amount",igstSelector:"#edit_igst_amount",taxTypeSelector:"#edit_tax_type",cgstLabelSelector:"#cgst_label_edit",sgstLabelSelector:"#sgst_label_edit",igstLabelSelector:"#igst_label_edit"})}function h(){r({enableDiscount:!0,tableSelector:"#updateItemsTable",addButtonSelector:"#addUpdateItemRow",subtotalSelector:"#update_subtotal",taxSelector:"#update_total_tax",grandTotalSelector:"#update_grand_total",cgstSelector:"#update_cgst_amount",sgstSelector:"#update_sgst_amount",igstSelector:"#update_igst_amount",taxTypeSelector:"#tax_type",cgstLabelSelector:"#cgst_label",sgstLabelSelector:"#sgst_label",igstLabelSelector:"#igst_label"})}t(document).on("click",".edit-items-btn",function(){const e=t(this).data("quotation"),n=t("#editItemsTable tbody");n.empty(),e.items.forEach((d,a)=>{n.append(m(d,a))}),_("#editItemsTable"),p("#editItemsTable")});t(document).on("click",".update-quotation-btn",function(){const e=t(this).data("quotation");t("#updateQuotationForm");const n=t("#updateItemsTable tbody");t("#update_quotation_id").val(e.id),t("#updateQuotationDate").val(e.quotation_date),t("#updateQuotationNumber").val(e.quotation_number),t("#updateQuotationNumberHidden").val(e.quotation_number),t("#updateClient").val(e.client_id),n.empty(),e.items.forEach((s,l)=>{n.append(m(s,l))});const d=document.getElementById("updateClient");c(d,!0);const a=document.getElementById("update_billing_address_id"),i=document.getElementById("update_shipping_address_id");c(a,!0),c(i,!0),t.get(`/clients/${e.client_id}/addresses`,function(s){o(a.choices,s,e.billing_address_id),o(i.choices,s,e.shipping_address_id)}),_("#updateItemsTable"),p("#updateItemsTable")});function I(){t(document).on("change","#updateClient",function(){const e=t(this).val();if(!e){o(document.getElementById("update_billing_address_id").choices,[]),o(document.getElementById("update_shipping_address_id").choices,[]);return}t.get(`/clients/${e}/addresses`,function(n){const d=document.getElementById("update_billing_address_id").choices,a=document.getElementById("update_shipping_address_id").choices;o(d,n),o(a,n),n.length>0&&(d.setChoiceByValue(String(n[0].id)),a.setChoiceByValue(String(n[0].id)))})}),t(document).on("click",".edit-client-shortcut",function(){const e=t("#updateClient").val();e&&t.get(`/clients/${e}/show-ajax`,function(n){t("#edit_client_id").val(n.id),t("#edit_client_name").val(n.name),t("#edit_client_contact_person").val(n.contact_person),t("#edit_client_email").val(n.email),t("#edit_client_phone").val(n.phone),t("#edit_client_gst_number").val(n.gst_number)})}),t(document).on("submit","#editClientForm",function(e){e.preventDefault();const n=t(this),d=t("#edit_client_id").val();t.ajax({url:`/clients/${d}/update-ajax`,type:"POST",data:n.serialize(),success:function(a){if(a.success){const i=a.data;t(`#updateClient option[value="${i.id}"]`).text(i.name),c(document.getElementById("updateClient"),!0),showMessage("success","Client updated successfully"),bootstrap.Offcanvas.getOrCreateInstance(document.getElementById("editClientOffcanvas")).hide()}}})}),t(document).on("submit","#addClientForm",function(e){e.preventDefault();const n=t(this);t.ajax({url:n.data("url"),type:"POST",data:n.serialize(),success:function(d){if(!d.success){showMessage("error","Client creation failed");return}const a=d.data;t("#updateClient").append(`<option value="${a.id}" selected>${a.name}</option>`),c(document.getElementById("updateClient"),!0),t.get(`/clients/${a.id}/addresses`,function(i){o(document.getElementById("update_billing_address_id").choices,i),o(document.getElementById("update_shipping_address_id").choices,i)}),n[0].reset(),bootstrap.Offcanvas.getOrCreateInstance(document.getElementById("clientOffcanvas")).hide(),showMessage("success",d.message)}})}),t(document).on("click",".edit-address-shortcut",function(){const e=t(this).data("select-id"),n=document.getElementById(e).value;n&&t.get(`/addresses/${n}/show-ajax`,function(d){const a=document.getElementById("editAddressForm");a&&(a.querySelector('[name="id"]').value=d.id,a.querySelector('[name="address_line_1"]').value=d.address_line_1,a.querySelector('[name="address_line_2"]').value=d.address_line_2||"",a.querySelector('[name="city"]').value=d.city,a.querySelector('[name="state"]').value=d.state||"",a.querySelector('[name="postal_code"]').value=d.postal_code||"",a.querySelector('[name="country"]').value=d.country,a.dataset.targetSelect=e)})}),t(document).on("submit","#addAddressForm",function(e){e.preventDefault();const n=t(this),d=n.attr("data-target-select"),a=t("#updateClient").val();if(!a)return showMessage("error","Please select a client first");t.ajax({url:`/addresses/client/${a}`,type:"POST",data:n.serialize(),success:function(i){const l=document.getElementById(d).choices;t.get(`/clients/${a}/addresses`,function(u){o(l,u,i.id),showMessage("success","Address created!"),bootstrap.Offcanvas.getOrCreateInstance(document.getElementById("addressOffcanvas")).hide()})}})}),t(document).on("submit","#editAddressForm",function(e){e.preventDefault();const n=t(this),d=n.attr("data-target-select"),a=n.find('[name="id"]').val(),i=t("#updateClient").val();t.ajax({url:`/addresses/${a}/update-ajax`,type:"POST",data:n.serialize(),success:function(s){const u=document.getElementById(d).choices;t.get(`/clients/${i}/addresses`,function(g){o(u,g,s.id),showMessage("success","Address updated!"),bootstrap.Offcanvas.getOrCreateInstance(document.getElementById("editAddressOffcanvas")).hide()})}})})}function m(e,n){return`
    <tr>
        <td>
            <select name="items[${n}][product_id]"
                class="product-dropdown form-select">
                <option value="">Select</option>
                ${window.products.map(d=>`
                    <option value="${d.id}"
                        ${d.id===e.product_id?"selected":""}>
                        ${d.our_part_no}
                    </option>
                `).join("")}
            </select>
        </td>

        <td>
            <input type="number"
                name="items[${n}][quantity]"
                class="form-control qty"
                value="${e.quantity}" min="1">
        </td>

        <td>
            <input type="number"
                name="items[${n}][unit_price]"
                class="form-control rate"
                value="${e.unit_price}" min="0">
        </td>

        <td>
            <input type="number" 
                name="items[${n}][discount_percent]" 
                class="form-control discount_percent" 
                value="${e.discount_percent}"
                step="0.01" min="0" > 
        </td>

        

        <td><input class="form-control taxable_amount" readonly></td>

        <td>
            <input type="number"
                name="items[${n}][tax_rate]"
                class="form-control tax_rate"
                value="${e.tax_rate}" min="0">
        </td>

        <td><input class="form-control tax_amount" readonly></td>
        <td><input class="form-control total_with_tax" readonly></td>

        <td>
            <button type="button"
                class="btn btn-danger btn-sm removeRow">X</button>
        </td>
    </tr>`}function _(e){document.querySelectorAll(`${e} .product-dropdown`).forEach(n=>c(n,!0))}function p(e){t(`${e} tbody tr`).each(function(){t(this).find(".qty").trigger("input")})}t(document).ready(function(){y({products:window.products}),h(),I(),f(),c(document.getElementById("add_client_billing_address"),!0),c(document.getElementById("add_client_shipping_address"),!0),b(function(e){o(document.getElementById("add_client_billing_address").choices,e),o(document.getElementById("add_client_shipping_address").choices,e)})});
