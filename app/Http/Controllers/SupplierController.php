<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Supplier;
use Illuminate\Validation\Rule;
use Yajra\DataTables\DataTables;

class SupplierController extends Controller
{
    public function ajaxStore(Request $request)
    {
        // dd('Hello');
        $supplier = Supplier::create(
            $request->validate([
                'name' => [
                    'required',
                    'string',
                    'max:255',
                    Rule::unique('suppliers', 'name'),
                ],
                'phone' => 'nullable|digits:10',
                'email' => 'nullable|unique:suppliers,email',
                'gst_number' => 'nullable|string|max:15',
                'address_id' => 'required|exists:addresses,id',
            ])
        );

        return response()->json($supplier, 201);
    }


    public function index()
    {
        return view('supplier.index');
    }

    public function data()
    {
        return DataTables::of(Supplier::query())
            ->addColumn('actions', function ($supplier) {
                return '
                    <button class="btn btn-sm btn-primary edit-supplier-btn "
                        data-id="' . $supplier->id . '"
                         data-name="' . $supplier->name . '"
                        data-phone="' . $supplier->phone . '"
                        data-email="' . $supplier->email . '"
                        data-gst_number="' . $supplier->gst_number . '"
                        data-address_id="' . $supplier->address_id . '"
                        title="Edit" data-bs-toggle="modal" data-bs-target="#editSupplierModal">
                        <i class="fa fa-edit"></i>
                    </button>

                    <form method="POST" action="' . route('suppliers.destroy', $supplier) . '"
                    class="d-inline">
                    ' . csrf_field() . method_field('DELETE') . '
                    <button class="btn btn-sm btn-danger"
                        onclick="return confirm(\'Delete this supplier?\')">
                        <i class="fa fa-trash"></i>
                    </button>
                </form>
                ';
            })
            ->rawColumns(['actions'])
            ->make(true);
    }


    public function store(Request $request)
    {
        Supplier::create($request->validate([
            'name' => 'required|string|max:255|unique:suppliers',
            'phone' => 'nullable|digits:10',
            'email' => 'nullable|email|unique:suppliers,email',
            'gst_number' => 'nullable|string|max:15',
            'address_id' => 'required|exists:addresses,id',
        ]));

        return redirect()->route('suppliers.index')->with('success', 'Supplier added');
    }

    public function update(Request $request, Supplier $supplier)
    {
        $supplier->update($request->validate([
            'name' => 'required|string|max:255|unique:suppliers,name,' . $supplier->id,
            'phone' => 'nullable|digits:10',
            'email' => 'nullable|email|unique:suppliers,email,' . $supplier->id,
            'gst_number' => 'nullable|string|max:15',
            'address_id' => 'required|exists:addresses,id',
        ]));

        return redirect()->route('suppliers.index')->with('success', 'Supplier updated');
    }

    public function destroy(Supplier $supplier)
    {
        $supplier->delete();
        return redirect()->route('suppliers.index')->with('success', 'Supplier deleted');
    }


}
