<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Communication extends Model
{
    protected $fillable = [
        'model_type',
        'model_id',
        'from_email',
        'to_emails',
        'cc_emails',
        'subject',
        'body',
        'attachments',
        'sent_by',
        'sent_at',
        'status'
    ];

    protected $casts = [
        'to_emails' => 'array',
        'cc_emails' => 'array',
        'attachments' => 'array'
    ];

    public function model()
    {
        return $this->morphTo();
    }
}

