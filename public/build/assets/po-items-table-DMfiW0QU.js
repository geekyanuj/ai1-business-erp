import{$ as m}from"./po-items-table-DLl3eRRB.js";function h({container:c}){const n=m(c);if(!n.length)return;const o=n.find("#itemsTable"),d=n.find("#tax_type"),u=n.find("#subtotal"),f=n.find("#total_tax"),p=n.find("#grand_total");function i(){o.find("tbody tr").each((t,e)=>{m(e).find(".serial").text(t+1)})}function x(t){const e=Number(t.find(".qty").val())||0,r=Number(t.find(".rate").val())||0,b=Number(t.find('[name="items[tax_rate][]"]').val())||0,s=e*r,l=s*b/100,y=s+l;return t.find('[name="items[tax_amount][]"]').val(l.toFixed(2)),t.find('[name="items[total_with_tax][]"]').val(y.toFixed(2)),{taxable:s,taxAmount:l}}function a(){let t=0,e=0;o.find("tbody tr").each(function(){const r=x(m(this));t+=r.taxable,e+=r.taxAmount}),u.val(t.toFixed(2)),f.val(e.toFixed(2)),p.val((t+e).toFixed(2))}n.on("click",".addItemRow, #addItemRow",function(){o.find("tbody").append(`
        <tr>
            <td class="serial text-center"></td>

            <td>
                <input type="text" name="items[product_name][]" class="form-control form-control-sm" required>
                <input type="hidden" name="items[product_id][]">
            </td>

            <td>
                <input type="text" name="items[product_description][]" class="form-control form-control-sm">
            </td>

            <td>
                <input type="text" name="items[hsn_code][]" class="form-control form-control-sm">
            </td>

            <td>
                <input type="number" name="items[quantity][]" class="form-control form-control-sm qty" value="1" min="1" required>
            </td>

            <td>
                <input type="number" name="items[unit_price][]" class="form-control form-control-sm rate" step="0.01" min="0" required>
            </td>

            <td>
                <input type="text" name="items[uom][]" class="form-control form-control-sm">
            </td>

            <td>
                <input type="number" name="items[tax_rate][]" class="form-control form-control-sm" value="18" min="0" required>
            </td>

            <td>
                <input type="number" name="items[tax_amount][]" class="form-control form-control-sm" min="0" readonly>
            </td>

            <td>
                <input type="number" name="items[total_with_tax][]" class="form-control form-control-sm" min="0" readonly>
            </td>

            <td class="text-center">
                <button type="button" class="btn btn-danger btn-sm removeRow">X</button>
            </td>
        </tr>`),i()}),n.on("click",".removeRow",function(){o.find("tbody tr").length>1&&(m(this).closest("tr").remove(),i(),a())}),n.on("input",'.qty, .rate, [name="items[tax_rate][]"]',a),d.on("change",a),i(),a()}export{h};
