<?php

namespace App\Http\Controllers;

use App\Models\Inventory;
use App\Models\ProductionBatch;
use App\Models\Product;
use App\Models\Label;
use App\Services\InventoryService;
use App\Services\NotificationService;
use DB;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Carbon\Carbon;

class ProductionBatchController extends Controller
{
    protected InventoryService $inventoryService;

    public function __construct(InventoryService $inventoryService)
    {
        $this->inventoryService = $inventoryService;
    }





    public function index()
    {
        return view('production.batches.index');
    }

    /**
     * DataTable JSON endpoint
     */
    public function getDataTable(Request $request)
    {
        $batches = ProductionBatch::with('product', 'operator')->get()->map(function ($batch) {
            return (object) [
                'lot_no' => $batch->lot_no ?: $batch->batch_no,
                'product' => ($batch->product->our_part_no ?? '-') . '<br><small class="text-muted">' . e($batch->product->description ?? '') . '</small>',
                'category' => $batch->product->category ?? '-',
                'quantity' => $batch->quantity_produced,
                'client' => '-',
                'serials' => '-',
                'notes' => $batch->remarks ?: '-',
                'production_date' => $batch->production_date ? Carbon::parse($batch->production_date)->format('d M Y') : '-',
                'status' => '<span class="badge bg-' . match ($batch->status) {
                    'draft' => 'secondary', 'in_progress' => 'warning', 'completed' => 'success', 'cancelled' => 'danger', default => 'secondary'
                } . '">' . ucfirst(str_replace('_', ' ', $batch->status)) . '</span>',
                'source' => 'Production',
                'actions' => '<a href="' . route('production.batches.show', $batch->id) . '" class="btn btn-sm btn-outline-primary" title="View lot"><i class="fa fa-eye"></i></a>',
                'sort_date' => $batch->created_at,
            ];
        });

        $labelLots = Label::with(['client', 'labelItems.product'])
            ->where(function ($query) {
                $query->where('category', 'RF Cable Assembly')
                    ->orWhereHas('labelItems.product', fn ($product) => $product->where('category', 'RF Cable Assembly'));
            })
            ->get()
            ->map(function ($label) {
                $item = $label->labelItems->first();
                $product = $item?->product;
                $partNumbers = $label->labelItems->pluck('product.our_part_no')->filter()->unique()->join(', ');
                $descriptions = $label->labelItems->pluck('product.description')->filter()->unique()->join('; ');

                return (object) [
                    'lot_no' => $label->lot_no,
                    'product' => e($partNumbers ?: '-') . '<br><small class="text-muted">' . e($descriptions) . '</small>',
                    'category' => $label->category ?: ($product?->category ?? 'RF Cable Assembly'),
                    'quantity' => $label->labelItems->count(),
                    'client' => $label->client->name ?? '-',
                    'serials' => $label->labelItems->pluck('serial_no')->filter()->join(', ') ?: '-',
                    'notes' => $label->notes ?: '-',
                    'production_date' => $label->created_at?->format('d M Y') ?: '-',
                    'status' => '<span class="badge bg-info text-dark">Label lot</span>',
                    'source' => 'Existing Labels',
                    'actions' => '<a href="' . route('labels.show', $label->id) . '" class="btn btn-sm btn-outline-primary" title="View lot"><i class="fa fa-eye"></i></a> <a href="' . route('labels.print-unit', $label->id) . '" class="btn btn-sm btn-outline-warning" title="Print labels"><i class="fa fa-print"></i></a>',
                    'sort_date' => $label->created_at,
                ];
            });

        return DataTables::of($batches->merge($labelLots)->sortByDesc('sort_date')->values())
            ->rawColumns(['product', 'status', 'actions'])
            ->make(true);
    }

    // public function create()
    // {
    //     $products = Product::orderBy('our_part_no')->get();

    //     return view('production.batches.create', compact('products'));
    // }
    public function create()
    {
        $products = Product::orderBy('our_part_no')->get();

        return view('production.batches.create', compact('products'));
    }


    public function store(Request $request)
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'batch_no' => 'required|string|max:100|unique:production_batches,batch_no',
            'lot_no' => 'nullable|string|max:100',
            'quantity_produced' => 'required|integer|min:1',
            'production_date' => 'nullable|date',
            'expiry_date' => 'nullable|date|after_or_equal:production_date',
            'remarks' => 'nullable|string',
        ]);

        $batch = ProductionBatch::create([
            ...$validated,
            'operator_id' => auth()->id(),
            'status' => 'draft',
        ]);

        return redirect()
            ->route('production.batches.show', $batch->id)
            ->with('success', 'Production batch created in draft state.');
    }


    public function show(ProductionBatch $batch)
    {
        $batch->load('product', 'operator');

        return view('production.batches.show', compact('batch'));
    }

    public function start(ProductionBatch $batch)
    {
        if ($batch->status !== 'draft') {
            abort(403, 'Batch cannot be started.');
        }

        $batch->update([
            'status' => 'in_progress',
        ]);

        activity()
            ->performedOn($batch)
            ->causedBy(auth()->user())
            ->log("Production batch started");

        return back()->with('success', 'Production batch started.');
    }


    public function complete(ProductionBatch $batch, Request $request)
    {
        if ($batch->status !== 'in_progress') {
            abort(403, 'Batch must be in progress to complete.');
        }

        DB::transaction(function () use ($batch) {

            // Add finished goods to ready inventory
            $readyInventory = Inventory::firstOrCreate(
                [
                    'inventory_type' => 'ready',
                    'product_id'     => $batch->product_id,
                ],
                [
                    'uom'                => 'pcs',
                    'location'           => 'Main Warehouse',
                    'quantity_available' => 0,
                    'quantity_reserved'  => 0,
                ]
            );

            $this->inventoryService->addStock(
                inventory: $readyInventory,
                quantity: $batch->quantity_produced,
                movementType: 'production_in',
                referenceType: ProductionBatch::class,
                referenceId: $batch->id,
                remarks: "Produced via batch {$batch->batch_no}"
            );

            $batch->update(['status' => 'completed']);

            activity()
                ->performedOn($batch)
                ->causedBy(auth()->user())
                ->log("Production batch completed");

            if ($batch->created_by) {
                NotificationService::notify(
                    $batch->created_by,
                    'batch_completed',
                    "Batch #{$batch->batch_no} completed successfully"
                );
            }
        });

        return redirect()
            ->route('production.batches.show', $batch->id)
            ->with('success', 'Production completed and inventory updated.');
    }


}
