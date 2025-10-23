<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'contact_person',
        'email',
        'phone',
        'address',
        'company',
        'notes',
        'status',
    ];

    protected $casts = [
        'status' => 'string',
    ];

    /**
     * Scope for searching suppliers
     */
    public function scopeSearch($query, $term)
    {
        return $query->where('name', 'like', "%{$term}%")
            ->orWhere('company', 'like', "%{$term}%")
            ->orWhere('contact_person', 'like', "%{$term}%")
            ->orWhere('email', 'like', "%{$term}%");
    }
}
