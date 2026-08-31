<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    protected $fillable = [
        'name','company_code','pan_number','cin_number','iec_number','email','phone','is_active','logo','authorised_signature'
    ];

    public function branches()
    {
        return $this->hasMany(CompanyBranch::class);
    }

    public function defaultBranch()
    {
        return $this->hasOne(CompanyBranch::class)->where('is_default', true);
    }
}

