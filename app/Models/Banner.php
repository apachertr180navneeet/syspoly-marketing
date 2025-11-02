<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Banner extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int,string>
     */
    protected $fillable = [
        'name',
        'whatsappcontent',
        'emailcontant', // kept spelling
        'image',
        'createdimage',
        'status', // 👈 status field
        'position',
    ];

    /**
     * The attributes that should be mutated to dates.
     *
     * (SoftDeletes will automatically cast deleted_at)
     *
     * @var array<string,string>
     */
    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    /**
     * Optional: Accessor to get full URL for image if stored as a path.
     * Uncomment if you want an automatic full URL.
     */
    // public function getImageUrlAttribute(): ?string
    // {
    //     if (! $this->image) {
    //         return null;
    //     }
    //     return asset($this->image);
    // }
}
