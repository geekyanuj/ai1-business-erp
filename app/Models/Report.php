<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class Report extends Model
{
    protected $fillable = [
        'report_type', 'generated_by', 'parameters', 'result'
    ];

    protected $casts = [
        'parameters' => 'array',
        'result' => 'array'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'generated_by');
    }
}
