import{$ as d}from"./po-items-table-DLl3eRRB.js";import"./dataTables-t187tmru.js";import{J as b}from"./jszip.min-B_Iqyv4e.js";import{C as p}from"./choices-BxcHAull.js";window.JSZip=b;function f(e,t){const l=d(e);return l.length?l.DataTable({processing:!0,serverSide:!0,ajax:t,columns:[{data:"lot_no",name:"lot_no",className:"text-center"},{data:"client_name",name:"client_name",className:"text-center"},{data:"category",name:"category",className:"text-center"},{data:"notes",name:"notes",className:"text-center",orderable:!1,searchable:!1},{data:"actions",orderable:!1,searchable:!1,className:"text-center"}],responsive:!0,pageLength:10}):null}function c(e,t=!1){if(!e)return;if(e.choices)try{e.choices.destroy()}catch{}const l=new p(e,{searchEnabled:!0,itemSelectText:"",allowHTML:!1,shouldSort:!1,position:t?"bottom":"auto"});return e.choices=l,l}function r(){document.querySelectorAll(".label-row").forEach((e,t)=>{const l=e.querySelector(".label-number");l&&(l.textContent=`#${t+1}`)})}function u(e){var t;e.querySelector(".product-select").addEventListener("change",()=>s(e)),(t=e.querySelector(".quantity-input"))==null||t.addEventListener("input",()=>{o()}),document.getElementById("clientSelect").addEventListener("change",()=>s(e))}function m(e){const t=document.getElementById("categorySelect").value,l=e.value,n=[...e.querySelectorAll("option")];e.choices&&e.choices.destroy(),e.innerHTML=n.filter(a=>!a.value||a.dataset.category===t).map(a=>a.outerHTML).join(""),e.value=n.some(a=>a.value===l&&a.dataset.category===t)?l:"",e.disabled=!t,c(e,!0)}function i(){document.querySelectorAll(".product-select").forEach(m)}function s(e){const t=e.querySelector(".product-select").value,l=document.getElementById("clientSelect").value,n=e.querySelector(".client-part-no");if(!t||!l){n.value="";return}fetch(`/product-client-part-no?product_id=${t}&client_id=${l}`).then(a=>a.json()).then(a=>{n.value=a.client_part_no??"-"})}function o(){let e=0;document.querySelectorAll(".quantity-input").forEach(t=>{const l=parseInt(t.value,10);isNaN(l)||(e+=l)}),document.getElementById("totalQuantity").value=e}window.addLabelRow=function(){const e=document.getElementById("labels-container"),t=document.createElement("div");t.className="label-row border rounded p-2 mb-2",t.innerHTML=`
<div class="row g-2 align-items-end ">

    <div class="col-md-4">
        <label class="small">Part No</label>
        <select class="form-select product-select"
            name="products[]" required>
            <option value="">Select Product</option>
            ${window.productsOptionsHtml}
        </select>
    </div>

    <div class="col-md-3">
        <label class="small">Client Part No</label>
        <input type="text" class="form-control client-part-no input-disabled-look" readonly>
    </div>

    <div class="col-md-1">
        <label class="small">Quantity</label>
        <input type="number" name="quantities[]" class="form-control quantity-input" min="1" required>
    </div>

    <div class="col-md-3">
        <label class="small">Serial Prefix (Item Code)</label>
        <input type="text" name="prefixes[]" class="form-control" placeholder="">
    </div>

    <div class="col-md-1 text-end">
        <div class="mb-1 fw-bold text-primary label-number">#</div>
        <button type="button"
            class="btn btn-danger btn-sm"
            onclick="removeLabelRow(this)">✕</button>
    </div>

</div>
`,e.appendChild(t),u(t),m(t.querySelector(".product-select")),r(),o()};window.removeLabelRow=function(e){const t=e.closest(".label-row");if(document.querySelectorAll(".label-row").length===1){alert("At least one label is required");return}t.remove(),r(),o()};document.addEventListener("DOMContentLoaded",()=>{var l;document.querySelectorAll(".label-row").forEach(n=>{u(n),c(n.querySelector(".product-select"),!0),c(document.getElementById("clientSelect"),!0)}),(l=document.getElementById("categorySelect"))==null||l.addEventListener("change",i),i(),r(),o();const e="#LabelTable",t=d(e).data("url");f(e,t)});
