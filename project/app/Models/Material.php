<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Material extends Model
{
    use HasFactory;

    protected $fillable = ['designation'];

    /**
     * Get the salse associated with the material.
     *
     * @return BelongsToMany
     */
    public function sales(): BelongsToMany {
        return $this->belongsToMany(Sale::class);
    }
}
