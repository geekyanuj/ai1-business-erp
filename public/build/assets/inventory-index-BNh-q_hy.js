import{$ as a}from"./po-items-table-DLl3eRRB.js";function i(t){const e=a(`.inventory-grid[data-type="${t}"]`),s=e.closest(".inventory-section");a.ajax({url:e.data("url"),method:"GET",data:{inventory_type:t,search_text:a("#universalSearch").val(),location:s.find(".location-filter").val()},beforeSend(){e.html(`
                <div class="col-12 text-center py-4 text-muted">
                    Loading...
                </div>
            `)},success(o){let d="";if(!o.data.length){e.html(`
                    <div class="col-12 text-center py-4 text-muted">
                        No inventory found
                    </div>
                `);return}o.data.forEach(n=>{const c=n.quantity_available-n.quantity_reserved;d+=`
                <div class="col-xl-3 col-lg-4 col-md-6">
                    <div class="card shadow-sm h-100">

                        <div class="card-body">

                            <span class="badge ${t==="raw"?"bg-info text-dark":t==="equipment"?"bg-warning text-dark":"bg-primary"} mb-2">
                                ${t==="equipment"?'<i class="fa fa-tools me-1"></i>':""}${t.toUpperCase()}
                            </span>

                            <h6 class="mb-1">
                                ${n.our_part_no??n.material_name}
                            </h6>

                            <small class="text-muted d-block mb-2">
                                ${n.location}
                            </small>

                            <div class="d-flex justify-content-between">
                                <span>Available</span>
                                <strong>${c}</strong>
                            </div>

                            <div class="d-flex justify-content-between">
                                <span>Reserved</span>
                                <strong>${n.quantity_reserved}</strong>
                            </div>

                            <div class="mt-2">
                                ${c<=0?'<span class="badge bg-danger">Out</span>':c<10?'<span class="badge bg-warning text-dark">Low</span>':'<span class="badge bg-success">OK</span>'}
                            </div>

                        </div>

                        <div class="card-footer bg-light text-end">
                            ${n.actions}
                        </div>

                    </div>
                </div>
                `}),e.html(d)}})}function l(t,e=400){let s;return(...o)=>{clearTimeout(s),s=setTimeout(()=>t.apply(this,o),e)}}a(document).ready(function(){i("ready"),a('button[data-bs-toggle="tab"]').on("shown.bs.tab",function(t){const e=a(t.target).data("type");i(e)}),a("#universalSearch").on("keyup",l(()=>{const t=a("#inventoryTabs .nav-link.active").data("type");i(t)})),a(".location-filter").on("change",function(){const t=a(this).closest(".inventory-section").data("type");i(t)})});
