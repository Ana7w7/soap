<?php
// app/Models/Carrito.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;


class Carrito extends Model
{
    use HasFactory;

    protected $table = 'carrito';

    protected $fillable = [
        'user_id'
    ];
    public function carritoitems() : HasMany
    {
        return $this->hasMany(carritoitem::class);
    }
   

    
}
?>