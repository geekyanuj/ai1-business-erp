import{$ as e}from"./po-items-table-DLl3eRRB.js";import{i as o,h as m}from"./sales-items-table-BKHtIWdD.js";import{r as c,i as y,f as b}from"./clients-index-C90AJKbt.js";import"./dataTables-t187tmru.js";import"./choices-BxcHAull.js";window.$=window.jQuery=e;function l(t,d){const a=document.getElementById("form-message");a&&a.remove();const n=document.createElement("div");n.id="form-message",n.style.position="fixed",n.style.top="60px",n.style.right="20px",n.style.width="300px",n.style.padding="15px 20px",n.style.borderRadius="5px",n.style.boxShadow="0 0 10px rgba(0,0,0,0.1)",n.style.zIndex="9999",n.style.backgroundColor=t==="success"?"#d4edda":"#f8d7da",n.style.color=t==="success"?"#155724":"#721c24",n.style.borderLeft=t==="success"?"5px solid #28a745":"5px solid #dc3545",n.innerText=d,document.body.appendChild(n),setTimeout(()=>n.remove(),3e3)}function v({products:t}){window.products=t,m({enableDiscount:!1,tableSelector:"#editItemsTable",addButtonSelector:"#addEditItemRow",subtotalSelector:"#edit_subtotal",taxSelector:"#edit_total_tax",grandTotalSelector:"#edit_grand_total",cgstSelector:"#edit_cgst_amount",sgstSelector:"#edit_sgst_amount",igstSelector:"#edit_igst_amount",taxTypeSelector:"#edit_tax_type",cgstLabelSelector:"#cgst_label_edit",sgstLabelSelector:"#sgst_label_edit",igstLabelSelector:"#igst_label_edit"})}function h(){m({enableDiscount:!1,tableSelector:"#updateItemsTable",addButtonSelector:"#addUpdateItemRow",subtotalSelector:"#update_subtotal",taxSelector:"#update_total_tax",grandTotalSelector:"#update_grand_total",cgstSelector:"#update_cgst_amount",sgstSelector:"#update_sgst_amount",igstSelector:"#update_igst_amount",taxTypeSelector:"#tax_type",cgstLabelSelector:"#cgst_label",sgstLabelSelector:"#sgst_label",igstLabelSelector:"#igst_label"})}e(document).on("click",".edit-items-btn",function(){const t=e(this).data("invoice"),d=e("#editItemsTable tbody");d.empty(),t.items.forEach((a,n)=>{d.append(p(a,n))}),_("#editItemsTable"),g("#editItemsTable")});e(document).on("click",".update-invoice-btn",function(){const t=e(this).data("invoice");e("#updateInvoiceForm");const d=e("#updateItemsTable tbody");e("#update_invoice_id").val(t.id),e("#updateInvoiceDate").val(t.invoice_date),e("#updateInvoiceNumber").val(t.invoice_number),e("#updateInvoiceNumberHidden").val(t.invoice_number),e("#updatePaymentMode").val(t.payment_mode),e("#updateClient").val(t.client_id),d.empty(),t.items.forEach((s,u)=>{d.append(p(s,u))});const a=document.getElementById("updateClient");o(a,!0);const n=document.getElementById("update_billing_address_id"),i=document.getElementById("update_shipping_address_id");o(n,!0),o(i,!0),e.get(`/clients/${t.client_id}/addresses`,function(s){c(n.choices,s,t.billing_address_id),c(i.choices,s,t.shipping_address_id)}),_("#updateItemsTable"),g("#updateItemsTable")});function I(){e(document).on("change","#updateClient",function(){const t=e(this).val();if(!t){c(document.getElementById("update_billing_address_id").choices,[]),c(document.getElementById("update_shipping_address_id").choices,[]);return}e.get(`/clients/${t}/addresses`,function(d){const a=document.getElementById("update_billing_address_id").choices,n=document.getElementById("update_shipping_address_id").choices;c(a,d),c(n,d),d.length>0&&(a.setChoiceByValue(String(d[0].id)),n.setChoiceByValue(String(d[0].id)))})}),e(document).on("click",".edit-client-shortcut",function(){const t=e("#updateClient").val();t&&e.get(`/clients/${t}/show-ajax`,function(d){e("#edit_client_id").val(d.id),e("#edit_client_name").val(d.name),e("#edit_client_contact_person").val(d.contact_person),e("#edit_client_email").val(d.email),e("#edit_client_phone").val(d.phone),e("#edit_client_gst_number").val(d.gst_number)})}),e(document).on("submit","#editClientForm",function(t){t.preventDefault();const d=e(this),a=e("#edit_client_id").val();e.ajax({url:`/clients/${a}/update-ajax`,type:"POST",data:d.serialize(),success:function(n){if(n.success){const i=n.data;e(`#updateClient option[value="${i.id}"]`).text(i.name),o(document.getElementById("updateClient"),!0),l("success","Client updated successfully"),bootstrap.Offcanvas.getOrCreateInstance(document.getElementById("editClientOffcanvas")).hide()}}})}),e(document).on("submit","#addClientForm",function(t){t.preventDefault();const d=e(this);e.ajax({url:d.data("url"),type:"POST",data:d.serialize(),success:function(a){if(!a.success){l("error","Client creation failed");return}const n=a.data;e("#updateClient").append(`<option value="${n.id}" selected>${n.name}</option>`),o(document.getElementById("updateClient"),!0),e.get(`/clients/${n.id}/addresses`,function(i){c(document.getElementById("update_billing_address_id").choices,i),c(document.getElementById("update_shipping_address_id").choices,i)}),d[0].reset(),bootstrap.Offcanvas.getOrCreateInstance(document.getElementById("clientOffcanvas")).hide(),l("success",a.message)}})}),e(document).on("click",".edit-address-shortcut",function(){const t=e(this).data("select-id"),d=document.getElementById(t).value;d&&e.get(`/addresses/${d}/show-ajax`,function(a){const n=document.getElementById("editAddressForm");n&&(n.querySelector('[name="id"]').value=a.id,n.querySelector('[name="address_line_1"]').value=a.address_line_1,n.querySelector('[name="address_line_2"]').value=a.address_line_2||"",n.querySelector('[name="city"]').value=a.city,n.querySelector('[name="state"]').value=a.state||"",n.querySelector('[name="postal_code"]').value=a.postal_code||"",n.querySelector('[name="country"]').value=a.country,n.dataset.targetSelect=t)})}),e(document).on("submit","#addAddressForm",function(t){t.preventDefault();const d=e(this),a=d.attr("data-target-select"),n=e("#updateClient").val();if(!n)return l("error","Please select a client first");e.ajax({url:`/addresses/client/${n}`,type:"POST",data:d.serialize(),success:function(i){const u=document.getElementById(a).choices;e.get(`/clients/${n}/addresses`,function(r){c(u,r,i.id),l("success","Address created!"),bootstrap.Offcanvas.getOrCreateInstance(document.getElementById("addressOffcanvas")).hide()})}})}),e(document).on("submit","#editAddressForm",function(t){t.preventDefault();const d=e(this),a=d.attr("data-target-select"),n=d.find('[name="id"]').val(),i=e("#updateClient").val();e.ajax({url:`/addresses/${n}/update-ajax`,type:"POST",data:d.serialize(),success:function(s){const r=document.getElementById(a).choices;e.get(`/clients/${i}/addresses`,function(f){c(r,f,s.id),l("success","Address updated!"),bootstrap.Offcanvas.getOrCreateInstance(document.getElementById("editAddressOffcanvas")).hide()})}})})}function p(t,d){return`
    <tr>
        <td>
            <select name="items[${d}][product_id]"
                class="product-dropdown form-select">
                <option value="">Select</option>
                ${window.products.map(a=>`
                    <option value="${a.id}"
                        ${a.id===t.product_id?"selected":""}>
                        ${a.our_part_no}
                    </option>
                `).join("")}
            </select>
        </td>

        <td>
            <input type="number"
                name="items[${d}][quantity]"
                class="form-control qty"
                value="${t.quantity}">
        </td>

        <td>
            <input type="number"
                name="items[${d}][unit_price]"
                class="form-control rate"
                value="${t.unit_price}">
        </td>

        <td>
            <input type="number"
                name="items[${d}][tax_rate]"
                class="form-control tax_rate"
                value="${t.tax_rate}">
        </td>

        <td><input class="form-control taxable_amount" readonly></td>
        <td><input class="form-control tax_amount" readonly></td>
        <td><input class="form-control total_with_tax" readonly></td>

        <td>
            <button type="button"
                class="btn btn-danger btn-sm removeRow">X</button>
        </td>
    </tr>`}function _(t){document.querySelectorAll(`${t} .product-dropdown`).forEach(d=>o(d,!0))}function g(t){e(`${t} tbody tr`).each(function(){e(this).find(".qty").trigger("input")})}e(document).ready(function(){v({products:window.products}),h(),I(),y(),o(document.getElementById("add_client_billing_address"),!0),o(document.getElementById("add_client_shipping_address"),!0),b(function(t){c(document.getElementById("add_client_billing_address").choices,t),c(document.getElementById("add_client_shipping_address").choices,t)})});
