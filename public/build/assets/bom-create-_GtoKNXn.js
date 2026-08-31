import{$ as t}from"./po-items-table-DLl3eRRB.js";t(document).ready(function(){let e=t("#bom-items tbody tr").length;t("#add-row").on("click",function(){const o=t("#bom-items").data("inventories");let a='<option value="">-- Select Raw Material --</option>';o.forEach(n=>{a+=`<option value="${n.material_name}" data-uom="${n.uom}">${n.material_name}</option>`});const i=`
            <tr>
                <td>
                    <select name="items[${e}][material_name]" class="form-select material-select" required>
                        ${a}
                    </select>
                </td>
                <td>
                    <div class="d-flex align-items-center gap-2">
                        <input type="number" step="0.001" min="0.001" name="items[${e}][quantity_per_unit]" class="form-control" required>
                        <span class="uom-text"></span>
                    </div>
                </td>
                <td class="text-center">
                    <button type="button" class="btn btn-sm btn-danger remove-row">X</button>
                </td>
            </tr>
        `;t("#bom-items tbody").append(i),e++}),t(document).on("change",".material-select",function(){const o=t(this).find(":selected").data("uom")||"";t(this).closest("tr").find(".uom-text").text(o)}),t(document).on("click",".remove-row",function(){t(this).closest("tr").remove()}),t("#bom-items").data("disabled")&&t("input, select, button").not(".btn-outline-secondary").prop("disabled",!0)});
