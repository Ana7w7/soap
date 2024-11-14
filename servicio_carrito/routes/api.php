<?php

use App\Http\Controllers\CarritoController;
use Illuminate\Support\Facades\Route;


  //ver carrito
  Route::get('ver_carrito', [CarritoController::class, 'showcarrito']);
  //añadir a carrito
  Route::post('agregar_carrito', [CarritoController::class, 'addTocarrito']);
  //quitar de carrito
  Route::post('quitar_carrito', [CarritoController::class, 'removeFromcarrito']);
  //actualizar carrito
  Route::post('actualizar_carrito', [CarritoController::class, 'updatecarrito']);


?>