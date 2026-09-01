import{$ as t}from"./jquery-DLl3eRRB.js";import{i,h as m}from"./sales-items-table-BKHtIWdD.js";import{r as c,i as _,f as g}from"./clients-index-AZDexIJH.js";import"./dataTables-CGqb2kQY.js";import"./choices-BxcHAull.js";window.$=window.jQuery=t;function s(e,d){const a=document.getElementById("form-message");a&&a.remove();const n=document.createElement("div");n.id="form-message",n.style.position="fixed",n.style.top="60px",n.style.right="20px",n.style.width="300px",n.style.padding="15px 20px",n.style.borderRadius="5px",n.style.boxShadow="0 0 10px rgba(0,0,0,0.1)",n.style.zIndex="9999",n.style.backgroundColor=e==="success"?"#d4edda":"#f8d7da",n.style.color=e==="success"?"#155724":"#721c24",n.style.borderLeft=e==="success"?"5px solid #28a745":"5px solid #dc3545",n.innerText=d,document.body.appendChild(n),setTimeout(()=>n.remove(),3e3)}function f({products:e}){window.products=e,m({enableDiscount:!0,tableSelector:"#editItemsTable",addButtonSelector:"#addEditItemRow",subtotalSelector:"#edit_subtotal",taxSelector:"#edit_total_tax",grandTotalSelector:"#edit_grand_total",cgstSelector:"#edit_cgst_amount",sgstSelector:"#edit_sgst_amount",igstSelector:"#edit_igst_amount",taxTypeSelector:"#edit_tax_type",cgstLabelSelector:"#cgst_label_edit",sgstLabelSelector:"#sgst_label_edit",igstLabelSelector:"#igst_label_edit"})}function y(){m({enableDiscount:!0,tableSelector:"#updateItemsTable",addButtonSelector:"#addUpdateItemRow",subtotalSelector:"#update_subtotal",taxSelector:"#update_total_tax",grandTotalSelector:"#update_grand_total",cgstSelector:"#update_cgst_amount",sgstSelector:"#update_sgst_amount",igstSelector:"#update_igst_amount",taxTypeSelector:"#tax_type",cgstLabelSelector:"#cgst_label",sgstLabelSelector:"#sgst_label",igstLabelSelector:"#igst_label"})}t(document).on("click",".edit-items-btn",function(){const e=t(this).data("proforma"),d=t("#editItemsTable tbody");d.empty(),e.items.forEach((a,n)=>{d.append(h(a,n))}),v("#editItemsTable"),S("#editItemsTable")});t(document).on("click",".update-proforma-btn",function(){const e=t(this).data("proforma");t("#update_proforma_id").val(e.id),t("#updateProformaDate").val(e.proforma_date),t("#updateProformaNumber").val(e.proforma_number),t("#updateProformaNumberHidden").val(e.proforma_number),t("#updateClient").val(e.client_id);const d=document.getElementById("updateClient");i(d,!0);const a=document.getElementById("update_billing_address_id"),n=document.getElementById("update_shipping_address_id");i(a,!0),i(n,!0),t.get(`/clients/${e.client_id}/addresses`,function(o){c(a.choices,o,e.billing_address_id),c(n.choices,o,e.shipping_address_id)})});function b(){t(document).on("change","#updateClient",function(){const e=t(this).val();if(!e){c(document.getElementById("update_billing_address_id").choices,[]),c(document.getElementById("update_shipping_address_id").choices,[]);return}t.get(`/clients/${e}/addresses`,function(d){const a=document.getElementById("update_billing_address_id").choices,n=document.getElementById("update_shipping_address_id").choices;c(a,d),c(n,d),d.length>0&&(a.setChoiceByValue(String(d[0].id)),n.setChoiceByValue(String(d[0].id)))})}),t(document).on("click",".edit-client-shortcut",function(){const e=t("#updateClient").val();e&&t.get(`/clients/${e}/show-ajax`,function(d){t("#edit_client_id").val(d.id),t("#edit_client_name").val(d.name),t("#edit_client_contact_person").val(d.contact_person),t("#edit_client_email").val(d.email),t("#edit_client_phone").val(d.phone),t("#edit_client_gst_number").val(d.gst_number)})}),t(document).on("submit","#editClientForm",function(e){e.preventDefault();const d=t(this),a=t("#edit_client_id").val();t.ajax({url:`/clients/${a}/update-ajax`,type:"POST",data:d.serialize(),success:function(n){if(n.success){const o=n.data;t(`#updateClient option[value="${o.id}"]`).text(o.name),i(document.getElementById("updateClient"),!0),s("success","Client updated successfully"),bootstrap.Offcanvas.getOrCreateInstance(document.getElementById("editClientOffcanvas")).hide()}}})}),t(document).on("submit","#addClientForm",function(e){e.preventDefault();const d=t(this);t.ajax({url:d.data("url"),type:"POST",data:d.serialize(),success:function(a){if(!a.success){s("error","Client creation failed");return}const n=a.data;t("#updateClient").append(`<option value="${n.id}" selected>${n.name}</option>`),i(document.getElementById("updateClient"),!0),t.get(`/clients/${n.id}/addresses`,function(o){c(document.getElementById("update_billing_address_id").choices,o),c(document.getElementById("update_shipping_address_id").choices,o)}),d[0].reset(),bootstrap.Offcanvas.getOrCreateInstance(document.getElementById("clientOffcanvas")).hide(),s("success",a.message)}})}),t(document).on("click",".edit-address-shortcut",function(){const e=t(this).data("select-id"),d=document.getElementById(e).value;d&&t.get(`/addresses/${d}/show-ajax`,function(a){const n=document.getElementById("editAddressForm");n&&(n.querySelector('[name="id"]').value=a.id,n.querySelector('[name="address_line_1"]').value=a.address_line_1,n.querySelector('[name="address_line_2"]').value=a.address_line_2||"",n.querySelector('[name="city"]').value=a.city,n.querySelector('[name="state"]').value=a.state||"",n.querySelector('[name="postal_code"]').value=a.postal_code||"",n.querySelector('[name="country"]').value=a.country,n.dataset.targetSelect=e)})}),t(document).on("submit","#addAddressForm",function(e){e.preventDefault();const d=t(this),a=d.attr("data-target-select"),n=t("#updateClient").val();if(!n)return s("error","Please select a client first");t.ajax({url:`/addresses/client/${n}`,type:"POST",data:d.serialize(),success:function(o){const u=document.getElementById(a).choices;t.get(`/clients/${n}/addresses`,function(l){c(u,l,o.id),s("success","Address created!"),bootstrap.Offcanvas.getOrCreateInstance(document.getElementById("addressOffcanvas")).hide()})}})}),t(document).on("submit","#editAddressForm",function(e){e.preventDefault();const d=t(this),a=d.attr("data-target-select"),n=d.find('[name="id"]').val(),o=t("#updateClient").val();t.ajax({url:`/addresses/${n}/update-ajax`,type:"POST",data:d.serialize(),success:function(r){const l=document.getElementById(a).choices;t.get(`/clients/${o}/addresses`,function(p){c(l,p,r.id),s("success","Address updated!"),bootstrap.Offcanvas.getOrCreateInstance(document.getElementById("editAddressOffcanvas")).hide()})}})})}function h(e,d){return`
    <tr>
        <td>
            <select name="items[${d}][product_id]"
                class="product-dropdown form-select">
                <option value="">Select</option>
                ${window.products.map(a=>`
                    <option value="${a.id}"
                        ${a.id===e.product_id?"selected":""}>
                        ${a.our_part_no}
                    </option>
                `).join("")}
            </select>
        </td>

        <td>
            <input type="number"
                name="items[${d}][quantity]"
                class="form-control qty"
                value="${e.quantity}" min="1">
        </td>

        <td>
            <input type="number"
                name="items[${d}][unit_price]"
                class="form-control rate"
                value="${e.unit_price}" min="0">
        </td>

        <td>
            <input type="number" 
                name="items[${d}][discount_percent]" 
                class="form-control discount_percent" 
                value="${e.discount_percent}"
                step="0.01" min="0" > 
        </td>

        <td><input class="form-control taxable_amount" readonly></td>

        <td>
            <input type="number"
                name="items[${d}][tax_rate]"
                class="form-control tax_rate"
                value="${e.tax_rate}" min="0">
        </td>

        <td><input class="form-control tax_amount" readonly></td>
        <td><input class="form-control total_with_tax" readonly></td>

        <td>
            <button type="button"
                class="btn btn-danger btn-sm removeRow">X</button>
        </td>
    </tr>`}function v(e){document.querySelectorAll(`${e} .product-dropdown`).forEach(d=>i(d,!0))}function S(e){t(`${e} tbody tr`).each(function(){t(this).find(".qty").trigger("input")})}t(document).ready(function(){f({products:window.products}),y(),b(),_(),i(document.getElementById("add_client_billing_address"),!0),i(document.getElementById("add_client_shipping_address"),!0),g(function(e){c(document.getElementById("add_client_billing_address").choices,e),c(document.getElementById("add_client_shipping_address").choices,e)})});
