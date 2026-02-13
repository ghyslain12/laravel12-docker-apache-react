<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Ticket extends Model
{
    use HasFactory;

    protected $fillable = ['titre', 'description'];

    /**
     * Get the sales associated with the ticket.
     *
     * @return BelongsToMany
     */
    public function sales(): BelongsToMany {
        return $this->belongsToMany(Sale::class);
    }
}
