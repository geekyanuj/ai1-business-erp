<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CompanyBranch extends Model
{
    protected $fillable = [
        'company_id','name','branch_code','gst_number','state_code','address_line1', 'address_line2','city','state',
        'pincode','country','phone','email','is_default','is_active',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public static function getDefault()
    {
        return self::where('is_default', true)->first() ?? self::first();
    }
}

