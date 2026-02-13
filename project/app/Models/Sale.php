<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Sale extends Model
{
    use HasFactory;

    protected $fillable = ['titre', 'description', 'customer_id', 'ticket_id', 'sale_id', 'material_id'];

    /**
     * Get the customer associated with the sale.
     *
     * @return HasOne
     */
    public function customer(): HasOne
    {
        return $this->hasOne(Customer::class, 'id', 'customer_id');
    }

    /**
     * Get the materials associated with the sale.
     *
     * @return BelongsToMany
     */
    public function materials(): BelongsToMany {
        return $this->belongsToMany(Material::class);
    }

    /**
     * Get the tickets associated with the sale.
     *
     * @return BelongsToMany
     */
    public function tickets(): BelongsToMany {
        return $this->belongsToMany(Ticket::class);
    }
}
