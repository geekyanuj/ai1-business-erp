<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Client;
use Illuminate\Validation\Rule;
use Yajra\DataTables\DataTables;

class ClientController extends Controller
{
    public function index()
    {
        return view('client.client-index');
    }

    public function getClients(Request $request)
    {
        if ($request->ajax()) {
            $data = Client::all();
            return DataTables::of($data)
                ->addColumn('actions', function ($client) {
                    return '
                        <button type="button" class="btn btn-sm btn-outline-primary edit-user-btn" 
                                data-id="' . $client->id . '" 
                                data-name="' . $client->name . '" 
                                data-contact_person="' . $client->contact_person . '" 
                                data-email="' . $client->email . '" 
                                data-phone="' . $client->phone . '" 
                                data-billing_address_id="' . $client->billing_address_id . '" 
                                data-shipping_address_id="' . $client->shipping_address_id . '" 
                                data-gst_number="' . $client->gst_number . '" 
                                data-notes="' . $client->notes . '" 
                                title="Edit" data-bs-toggle="modal" data-bs-target="#editClientModal">
                            <i class="fas fa-edit"></i>
                        </button>
                        <form action="' . route('clients.destroy', $client->id) . '" method="POST" class="d-inline">
                            ' . csrf_field() . method_field('DELETE') . '
                            <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm(\'Delete this Client?\')">
                                <i class="fas fa-trash-alt"></i>
                            </button>
                        </form>';
                })

                ->rawColumns(['actions'])
                ->make(true);


        }
    }


    public function getClientsList()
    {
        return Client::select('id', 'name')
            ->orderBy('name')
            ->get();
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'contact_person' => 'required|string|max:255',
            'email' => 'required|email|unique:clients,email',
            'phone' => 'nullable|digits:10',
            'billing_address_id' => 'required|exists:addresses,id',
            'shipping_address_id' => 'required|exists:addresses,id',
            'gst_number' => 'nullable|string|max:255',
            'notes' => 'nullable|string|max:255',
        ]);
        // dd(request()->all());
        $client = new Client($validated);

        $client->save();

        return redirect()->back()->with('success', 'Client created successfully.');

    }

    public function ajaxStore(Request $request)
    {
        $client = Client::create(
            $request->validate([
                'name' => [
                    'required',
                    'string',
                    'max:255',
                    Rule::unique('clients', 'name'),
                ],
                'contact_person' => 'required|string|max:255',
                'email' => 'required|email|unique:clients,email',
                'phone' => 'nullable|digits:10',
                'billing_address_id' => 'required|exists:addresses,id',
                'shipping_address_id' => 'required|exists:addresses,id',
                'gst_number' => 'nullable|string|max:255',
                'notes' => 'nullable|string|max:255',
            ])
        );

        return response()->json([
            'success' => true,
            'message' => 'Client created successfully.',
            'data' => $client
        ], 201);
    }



    public function update(Request $request, Client $client)
    {
        // dd(request()->all());
        $request->validate([
            'name' => 'required|string|max:255',
            'contact_person' => 'required|string|max:255',
            'email' => 'required|email|unique:clients,email,' . $client->id,
            'phone' => 'nullable|digits:10',
            'billing_address_id' => 'required|exists:addresses,id',
            'shipping_address_id' => 'required|exists:addresses,id',
            'gst_number' => 'nullable|string|max:255',
            'notes' => 'nullable|string|max:255',
            // 'phone' => 'nullable|regex:/^\+?[0-9]{6,10}$/',
        ]);

        // Prepare data to update
        $data = $request->all();


        $client->update($data);
        return redirect()->back()->with('success', 'Client updated successfully.');
    }

    public function destroy(Client $client)
    {
        $client->delete();
        return redirect()->back()->with('success', 'Client deleted successfully.');
    }



}
