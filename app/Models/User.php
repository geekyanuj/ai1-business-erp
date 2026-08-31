<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */

    // Explicitly define custom table name
    protected $table = 'users';

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'last_login',
        'login_enabled',
        'telephone',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];


    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'last_login' => 'datetime',
        ];
    }


    public function productionBatches(): HasMany
    {
        return $this->hasMany(ProductionBatch::class, 'operator_id');
    }

    public function reports(): HasMany
    {
        return $this->hasMany(Report::class, 'generated_by');
    }

    public function auditLogs(): HasMany
    {
        return $this->hasMany(AuditLog::class, 'changed_by');
    }

    public function notifications(): HasMany
    {
        return $this->hasMany(Notification::class, 'user_id');
    }

    // Spatie Activity Log
    public function activities()
    {
        return $this->morphMany(\Spatie\Activitylog\Models\Activity::class, 'causer');
    }



}
