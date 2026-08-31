<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
class AuditLog extends Model
{
    protected $fillable = [
        'entity_type', 'entity_id',
        'action', 'changed_by', 'changes'
    ];

    protected $casts = [
        'changes' => 'array'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'changed_by');
    }
}
