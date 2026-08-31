function k(s,a=!1){if(!s)return;if(s.choices)try{s.choices.destroy()}catch{}const u=new Choices(s,{searchEnabled:!0,itemSelectText:"",allowHTML:!1,shouldSort:!1,position:a?"bottom":"auto"});return s.choices=u,u}function A({enableDiscount:s=!1,tableSelector:a="#itemsTable",addButtonSelector:u="#addItemRow",subtotalSelector:v="#subtotal",taxSelector:G="#total_tax",grandTotalSelector:C="#grand_total",cgstSelector:b="#cgst_amount",sgstSelector:w="#sgst_amount",igstSelector:g="#igst_amount",taxTypeSelector:y="#tax_type",cgstLabelSelector:F="#cgst_label",sgstLabelSelector:T="#sgst_label",igstLabelSelector:N="#igst_label"}={}){const e=$(a),p=Number(window.cgst_division_percentage)||50,x=Number(window.sgst_division_percentage)||50,m=p+x||1;$(document).on("click",u,function(){const t=e.find("tbody tr").length,o=`
        <tr>
            <td>
                <select name="items[${t}][product_id]"
                        class="product-dropdown form-select" required>
                    <option value="">Select</option>
                    ${products.map(i=>`<option value="${i.id}">${i.our_part_no}</option>`).join("")}
                </select>
            </td>

            <td>
                <input type="number" name="items[${t}][quantity]"
                       class="form-control qty" min="1" value="1">
            </td>

            <td>
                <input type="number" name="items[${t}][unit_price]"
                       class="form-control rate" step="0.01" min="0">
            </td>

            ${s?`
            <td>
                <input type="number"
                       name="items[${t}][discount_percent]"
                       class="form-control discount_percent" step="0.01" min="0">
            </td>`:""}

            <td><input type="text" class="form-control taxable_amount" readonly></td>

            <td>
                <input type="number" name="items[${t}][tax_rate]"
                       class="form-control tax_rate" step="0.01" min="0">
            </td>

            <td><input type="text" class="form-control tax_amount" readonly></td>
            <td><input type="text" class="form-control total_with_tax" readonly></td>

            <td>
                <button type="button"
                        class="btn btn-danger btn-sm removeRow">X</button>
            </td>
        </tr>`,n=$(o);e.find("tbody").append(n),k(n.find(".product-dropdown")[0],!0),q(e),h(),r()});function h(){const t=new Set(e.find(".product-dropdown").map(function(){return $(this).val()}).get().filter(Boolean));e.find(".product-dropdown").each(function(){const o=this.choices;o&&o.setChoices(o._store.choices.map(n=>({value:n.value,label:n.label,selected:n.selected,disabled:n.value&&t.has(n.value)&&!n.selected})),"value","label",!0)})}$(document).on("change",`${a} .product-dropdown`,function(){const t=$(this).closest("tr");R(t),r(),h()}),$(document).on("input",`${a} .qty,
         ${a} .rate,
         ${a} .discount_percent,
         ${a} .tax_rate`,function(){const t=$(this).closest("tr");R(t),r()}),$(document).on("change",y,function(){r()});function R(t){const o=Number(t.find(".qty").val())||0,n=Number(t.find(".rate").val())||0,i=Number(t.find(".tax_rate").val())||0,c=o*n;let d=0;if(s){const I=Number(t.find(".discount_percent").val())||0;d=c*I/100}const l=Math.max(c-d,0),f=l*i/100,_=l+f;t.find(".taxable_amount").val(l.toFixed(2)),t.find(".tax_amount").val(f.toFixed(2)),t.find(".total_with_tax").val(_.toFixed(2))}function r(){let t=0,o=0;const n=new Set;e.find(".taxable_amount").each(function(){t+=Number($(this).val())||0}),e.find(".tax_rate").each(function(){const d=$(this).val();d&&n.add(Number(d))}),e.find(".tax_amount").each(function(){o+=Number($(this).val())||0}),$(v).val(t.toFixed(2)),$(G).val(o.toFixed(2));const i=$(y).val(),c=n.size===1?[...n][0]:null;if(i==="cgst_sgst"){const d=o*(p/m),l=o*(x/m);if($(b).val(d.toFixed(2)),$(w).val(l.toFixed(2)),$(g).val("0.00"),c!==null){const f=(c*p/m).toFixed(2),_=(c*x/m).toFixed(2);$(F).text(`CGST (${parseFloat(f)}%)`),$(T).text(`SGST (${parseFloat(_)}%)`)}else $(F).text("CGST"),$(T).text("SGST");$(".cgst-sgst-row").show(),$(".igst-row").hide()}else $(g).val(o.toFixed(2)),$(b).val("0.00"),$(w).val("0.00"),c!==null?$(N).text(`IGST (${parseFloat(c.toFixed(2))}% )`):$(N).text("IGST"),$(".igst-row").show(),$(".cgst-sgst-row").hide();$(C).val((t+o).toFixed(2))}function q(t){t.find("tbody tr").each(function(o){$(this).find("input, select, textarea").each(function(){const n=$(this).attr("name");if(!n)return;const i=n.replace(/items\[\d+\]/,`items[${o}]`);$(this).attr("name",i)})})}$(document).on("click",`${a} .removeRow`,function(){if(e.find("tbody tr").length===1)return;const t=$(this).closest("tr").find(".product-dropdown")[0];t!=null&&t.choices&&t.choices.destroy(),$(this).closest("tr").remove(),q(e),h(),r()})}export{A as h,k as i};
