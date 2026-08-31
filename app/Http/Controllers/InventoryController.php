<?php

namespace App\Http\Controllers;

use App\Models\Inventory;
use App\Models\InventoryMovement;
use App\Models\Product;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Carbon\Carbon;
use App\Services\InventoryService;

class InventoryController extends Controller
{

    protected InventoryService $inventoryService;

    public function __construct(InventoryService $inventoryService)
    {
        $this->inventoryService = $inventoryService;
    }


    /**
     * Inventory listing page
     */
    public function index()
    {
        return view('inventory.index');
    }

    /**
     * Inventory DataTable
     */
    public function getInventoryDataTable(Request $request)
    {
        if ($request->ajax()) {

            $data = Inventory::query()
                ->select([
                    'inventories.id',
                    'inventories.inventory_type',
                    'products.our_part_no',
                    'inventories.material_name',
                    'inventories.location',
                    'inventories.quantity_available',
                    'inventories.quantity_reserved',
                    'inventories.updated_at',
                ])
                ->leftJoin('products', 'inventories.product_id', '=', 'products.id');

            /* -------------------------------------------------
             | APPLY FILTERS (from DataTable AJAX)
             -------------------------------------------------*/

            // Inventory Type filter
            if ($request->filled('inventory_type')) {
                $data->where('inventories.inventory_type', $request->inventory_type);
            }

            // Location filter
            if ($request->filled('location')) {
                $data->where('inventories.location', $request->location);
            }

            // Search (Part No OR Material Name)
            if ($request->filled('search_text')) {
                $search = $request->search_text;

                $data->where(function ($q) use ($search) {
                    $q->where('products.our_part_no', 'like', "%{$search}%")
                        ->orWhere('inventories.material_name', 'like', "%{$search}%");
                });
            }

            return DataTables::of($data)

                /* -------------------------------------------------
                 | INVENTORY TYPE BADGE
                 -------------------------------------------------*/
                ->editColumn('inventory_type', function ($row) {
                    return match ($row->inventory_type) {
                        'raw'       => '<span class="badge bg-info text-dark">RAW MATERIAL</span>',
                        'ready'     => '<span class="badge bg-primary">READY PRODUCT</span>',
                        'equipment' => '<span class="badge bg-warning text-dark"><i class="fa fa-tools"></i> EQUIPMENT</span>',
                        default     => '<span class="badge bg-secondary">N/A</span>',
                    };
                })

                /* -------------------------------------------------
                 | AVAILABLE STOCK BADGE
                 -------------------------------------------------*/
                ->addColumn('available_stock', function ($row) {
                    $available = $row->quantity_available - $row->quantity_reserved;

                    if ($available <= 0) {
                        return '<span class="badge bg-danger">Out of Stock</span>';
                    } elseif ($available < 10) {
                        return '<span class="badge bg-warning text-dark">Low</span>';
                    } else {
                        return '<span class="badge bg-success">Available</span>';
                    }
                })

                /* -------------------------------------------------
                 | ACTION BUTTONS
                 -------------------------------------------------*/
                ->addColumn('actions', function ($row) {
                    return '
                    <a href="' . route('inventory.show', $row->id) . '" 
                       class="btn btn-sm btn-outline-primary" title="View">
                        <i class="fa fa-eye"></i>
                    </a>

                    <a href="' . route('inventory.adjust', $row->id) . '"
                       class="btn btn-sm btn-outline-warning" title="Adjust">
                        <i class="fa fa-sliders"></i>
                    </a>

                    <a href="' . route('inventory.movements', $row->id) . '"
                       class="btn btn-sm btn-outline-secondary" title="Movements">
                        <i class="fa fa-exchange-alt"></i>
                    </a>
                ';
                })

                /* -------------------------------------------------
                 | RAW HTML COLUMNS
                 -------------------------------------------------*/
                ->rawColumns([
                    'inventory_type',
                    'available_stock',
                    'actions'
                ])

                ->make(true);
        }
    }


    /**
     * Inventory details (read-only)
     */
    public function show($id)
    {
        $inventory = Inventory::with('product')->findOrFail($id);
        return view('inventory.show', compact('inventory'));
    }


    /**
     * Reserve stock (Sales Order confirmation)
     */
    public static function reserveStock(
        int $productId,
        int $quantity
    ) {
        $inventory = Inventory::where('product_id', $productId)
            ->where('quantity_available', '>=', $quantity)
            ->orderBy('created_at')
            ->first();

        if (!$inventory) {
            throw new \Exception('Insufficient stock');
        }

        $inventory->quantity_reserved += $quantity;
        $inventory->last_updated = Carbon::now();
        $inventory->save();
    }

    /**
     * Deduct stock (Shipment)
     */
    public static function deductStock(
        int $productId,
        int $quantity
    ) {
        $inventory = Inventory::where('product_id', $productId)
            ->where('quantity_reserved', '>=', $quantity)
            ->orderBy('created_at')
            ->first();

        if (!$inventory) {
            throw new \Exception('Stock not reserved or insufficient');
        }

        $inventory->quantity_available -= $quantity;
        $inventory->quantity_reserved -= $quantity;
        $inventory->last_updated = Carbon::now();
        $inventory->save();
    }


    /**
     * Show adjustment page
     */
    public function adjust(Inventory $inventory)
    {
        return view('inventory.adjust', compact('inventory'));
    }

    /**
     * Store manual adjustment
     */
    public function storeAdjustment(Request $request, Inventory $inventory)
    {
        $validated = $request->validate([
            'adjustment_type' => 'required|in:add,remove',
            'quantity' => 'required|numeric|min:0.01',
            'remarks' => 'required|string|max:500',
        ]);

        if ($validated['adjustment_type'] === 'add') {
            $this->inventoryService->addStock(
                inventory: $inventory,
                quantity: $validated['quantity'],
                movementType: 'adjustment',
                referenceType: Inventory::class,
                referenceId: $inventory->id,
                remarks: $validated['remarks']
            );
        } else {
            $this->inventoryService->removeStock(
                inventory: $inventory,
                quantity: $validated['quantity'],
                movementType: 'adjustment',
                referenceType: Inventory::class,
                referenceId: $inventory->id,
                remarks: $validated['remarks']
            );
        }

        return redirect()
            ->route('inventory.show', $inventory->id)
            ->with('success', 'Inventory adjusted successfully.');
    }

    /**
     * Show create form for new inventory item (equipment/raw)
     */
    public function create()
    {
        return view('inventory.create');
    }

    /**
     * Store a new inventory item (equipment/tools or raw)
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'inventory_type' => 'required|in:raw,equipment',
            'material_name'  => 'required|string|max:255',
            'uom'            => 'required|string|max:20',
            'location'       => 'nullable|string|max:255',
            'description'    => 'nullable|string|max:1000',
            'quantity_available' => 'nullable|numeric|min:0',
        ]);

        $existing = Inventory::where('inventory_type', $validated['inventory_type'])
            ->where('material_name', $validated['material_name'])
            ->whereNull('product_id')
            ->first();

        if ($existing) {
            return back()->withErrors(['material_name' => 'Item with this name already exists in this category.'])->withInput();
        }

        Inventory::create([
            'inventory_type'     => $validated['inventory_type'],
            'material_name'      => $validated['material_name'],
            'uom'                => $validated['uom'],
            'location'           => $validated['location'] ?? null,
            'description'        => $validated['description'] ?? null,
            'quantity_available' => $validated['quantity_available'] ?? 0,
            'quantity_reserved'  => 0,
        ]);

        return redirect()->route('inventory.index')
            ->with('success', ucfirst($validated['inventory_type']) . ' item added to inventory.');
    }

    /**
     * Inventory movement / ledger page
     */
    public function movements(Inventory $inventory)
    {
        $movements = InventoryMovement::with('creator')
            ->where('inventory_id', $inventory->id)
            ->orderBy('created_at', 'desc')
            ->get();

        return view('inventory.movements', compact('inventory', 'movements'));
    }
}
