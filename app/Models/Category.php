<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $fillable = [
        'name',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function subCategories()
    {
        return $this->hasMany(SubCategory::class);
    }

    public function activeSubCategories()
    {
        return $this->hasMany(SubCategory::class)
            ->where('is_active', true);
    }

    public function products()
    {
        return $this->hasMany(Product::class);
    }
}
