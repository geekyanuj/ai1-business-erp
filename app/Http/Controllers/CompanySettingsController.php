<?php

namespace App\Http\Controllers;

use App\Models\Company;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CompanySettingsController extends Controller
{
    public function index()
    {
        $company = Company::with('branches')->first();
        return view('settings.company', compact('company'));
    }

    public function store(Request $request)
    {
        return $this->saveCompany($request);
    }

    public function update(Request $request, Company $company)
    {
        return $this->saveCompany($request, $company);
    }

    /**
     * Shared save logic (Store + Update)
     */
    private function saveCompany(Request $request, Company $company = null)
{
    $validated = $request->validate([
        // ================= COMPANY =================
        'name' => 'required|string|max:255',
        'company_code' => 'required|string|max:4|unique:companies,company_code,' . optional($company)->id,
        'pan_number' => 'required|string|max:10',
        'cin_number' => 'nullable|string|max:25',
        'iec_number' => 'nullable|string|max:20',
        'email' => 'required|email',
        'phone' => 'required|string|max:10',
        'logo' => 'nullable|image|max:2048',
        'authorised_signature' => 'nullable|image|max:2048',

        // ================= BRANCHES =================
        'branches' => 'required|array|min:1',

        'branches.*.name' => 'required|string|max:255',
        'branches.*.branch_code' => 'required|string|max:20',
        'branches.*.gst_number' => 'required|string|max:15',
        'branches.*.state_code' => 'required|string|max:2',

        'branches.*.address_line1' => 'required|string',
        'branches.*.address_line2' => 'nullable|string',
        'branches.*.city' => 'required|string',
        'branches.*.state' => 'required|string',
        'branches.*.pincode' => 'required|string|max:10',
        'branches.*.country' => 'required|string|max:50',

        'branches.*.phone' => 'nullable|string|max:20',
        'branches.*.email' => 'nullable|email',

        'branches.*.is_active' => 'nullable|boolean',

        // default branch index
        'default_branch' => 'required|integer',
    ]);

    DB::transaction(function () use ($validated, $request, &$company) {

        // LOGO
        if ($request->hasFile('logo')) {
            $validated['logo'] = $request->file('logo')->store('company', 'public');
        }

        // LOGO
        if ($request->hasFile('authorised_signature')) {
            $validated['authorised_signature'] = $request->file('authorised_signature')->store('company', 'public');
        }

        // SAVE COMPANY
        $company = Company::updateOrCreate(
            ['id' => optional($company)->id],
            collect($validated)->except('branches', 'default_branch')->toArray()
        );

        // RESET BRANCHES
        $company->branches()->delete();

        $defaultIndex = (string) $validated['default_branch'];

        $branches = [];

        foreach ($validated['branches'] as $i => $branch) {
            $branch['is_default'] = ((string) $i === $defaultIndex);
            $branch['is_active'] = $branch['is_active'] ?? false;
            $branches[] = $branch;
        }

        $company->branches()->createMany($branches);
    });

    return redirect()
        ->route('company.index')
        ->with('success', 'Company settings saved successfully');
}


}
