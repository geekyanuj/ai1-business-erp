<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Address;
use Illuminate\Http\Request;

class AddressController extends Controller
{
    /**
     * Return ALL addresses as JSON (used by Add Client modal).
     */
    public function getAllAddresses()
    {
        return response()->json(
            Address::select('id', 'address_line_1', 'address_line_2', 'city', 'state', 'country', 'postal_code')
                ->orderBy('id')
                ->get()
                ->map(fn($a) => [
                    'id' => $a->id,
                    'full_address' => $a->full_address,
                ])
        );
    }

    /**
     * AJAX: Save a new address using firstOrCreate to avoid duplicates.
     * Returns JSON { id, full_address }.
     */
    public function storeAjax(Request $request)
    {
        $request->validate([
            'address_line_1' => 'required|string|max:255',
            'city' => 'required|string|max:100',
            'country' => 'required|string|max:100',
        ]);

        // Use firstOrCreate to avoid duplicate address entries
        $address = Address::firstOrCreate(
            // Match key: line1 + city + country (case-insensitive via lowercase)
            [
                'address_line_1' => $request->address_line_1,
                'city' => $request->city,
                'country' => $request->country,
            ],
            // Additional fields only set on CREATE
            [
                'address_line_2' => $request->address_line_2,
                'state' => $request->state,
                'postal_code' => $request->postal_code,
            ]
        );

        return response()->json([
            'id' => $address->id,
            'full_address' => $address->full_address,
            'created' => $address->wasRecentlyCreated,
        ]);
    }

    /**
     * AJAX: Save a new address and link it to a client.
     */
    public function storeForClient(Request $request, Client $client)
    {
        $request->validate([
            'address_line_1' => 'required|string|max:255',
            'city' => 'required|string|max:100',
            'country' => 'required|string|max:100',
        ]);

        $address = Address::firstOrCreate(
            [
                'address_line_1' => $request->address_line_1,
                'city' => $request->city,
                'country' => $request->country,
            ],
            [
                'address_line_2' => $request->address_line_2,
                'state' => $request->state,
                'postal_code' => $request->postal_code,
            ]
        );

        // Map to client if not already mapped
        if (!$client->addresses()->where('address_id', $address->id)->exists()) {
            $client->addresses()->attach($address->id);
        }

        return response()->json([
            'id' => $address->id,
            'full_address' => $address->full_address,
        ]);
    }

    public function getAddressesByClient(Client $client)
    {
        // Fetch all addresses linked via the pivot table
        $addresses = $client->addresses()
            ->select('addresses.id', 'address_line_1', 'city', 'state', 'country', 'postal_code')
            ->get()
            ->map(fn($a) => [
                'id' => $a->id,
                'full_address' => $a->full_address,
            ]);

        // If no addresses in pivot, at least return billing/shipping if they exist
        if ($addresses->isEmpty()) {
            $addressIds = array_filter([$client->billing_address_id, $client->shipping_address_id]);
            $addresses = Address::whereIn('id', $addressIds)
                ->get()
                ->map(fn($a) => [
                    'id' => $a->id,
                    'full_address' => $a->full_address,
                ]);
        }

        return response()->json($addresses);
    }

    public function getAddressesBySupplier(\App\Models\Supplier $supplier)
    {
        return response()->json(
            Address::where('id', $supplier->address_id)->select('id', 'address_line_1', 'city', 'state', 'country', 'postal_code')
                ->get()
                ->map(fn($a) => [
                    'id' => $a->id,
                    'full_address' => $a->full_address,
                ])
        );
    }

    public function getBranchAddresses()
    {
        $branches = \App\Models\CompanyBranch::all();
        $addresses = [];

        foreach ($branches as $branch) {
            $addr = Address::firstOrCreate(
                [
                    'address_line_1' => $branch->address_line1,
                    'city'           => $branch->city,
                    'country'        => $branch->country ?? 'India',
                ],
                [
                    'address_line_2' => $branch->address_line2,
                    'state'          => $branch->state,
                    'postal_code'    => $branch->pincode,
                ]
            );

            $addresses[] = [
                'id' => $addr->id,
                'full_address' => "[{$branch->name}] " . $addr->full_address,
            ];
        }

        return response()->json($addresses);
    }

    /**
     * Display a listing of the addresses.
     */
    public function index()
    {
        return view('address.index');
    }

    /**
     * Fetch addresses for DataTables.
     */
    public function data(Request $request)
    {
        $query = Address::query();

        return datatables()->of($query)
            ->addColumn('actions', function ($address) {
                return '
                    <div class="d-flex justify-content-center gap-1">
                        <button class="btn btn-sm btn-info edit-address-btn" data-id="' . $address->id . '" title="Edit">
                            <i class="fa-solid fa-pen-to-square"></i>
                        </button>
                    </div>';
            })
            ->rawColumns(['actions'])
            ->make(true);
    }

    /**
     * Update the specified address.
     */
    public function update(Request $request, Address $address)
    {
        $validated = $request->validate([
            'address_line_1' => 'required|string|max:255',
            'address_line_2' => 'nullable|string|max:255',
            'city' => 'required|string|max:100',
            'state' => 'nullable|string|max:100',
            'country' => 'required|string|max:100',
            'postal_code' => 'nullable|string|max:20',
        ]);

        $address->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Address updated successfully.',
            'address' => [
                'id' => $address->id,
                'full_address' => $address->full_address,
            ],
        ]);
    }

    public function showAjax($id)
    {
        $address = Address::find($id);

        if (!$address) {
            return response()->json([
                'message' => 'Address not found.'
            ], 404);
        }

        return response()->json([
            'id' => $address->id,
            'address_line_1' => $address->address_line_1,
            'address_line_2' => $address->address_line_2,
            'city' => $address->city,
            'state' => $address->state,
            'postal_code' => $address->postal_code,
            'country' => $address->country,
            'full_address' => $address->full_address,
        ]);
    }
}
