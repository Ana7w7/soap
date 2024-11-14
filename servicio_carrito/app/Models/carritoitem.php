<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class carritoitem extends Model
{

    protected $table = 'carritoitem';
    protected $fillable = [
        'carrito_id',
        'producto_id',
        'cantidad',
        'precio',
    ];
    

}
