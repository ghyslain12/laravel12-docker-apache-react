<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SaleMaterial extends Model
{
    use HasFactory;

    protected $fillable = ['sale_id', 'material_id'];
}
