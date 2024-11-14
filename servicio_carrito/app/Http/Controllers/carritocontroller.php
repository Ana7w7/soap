<?php
// app/Http/Controllers/CarritoController.php

namespace App\Http\Controllers;

use App\Models\Carrito;
use App\Models\carritoitem;

use App\Services\ProductService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CarritoController extends Controller
{
    protected $productService;

    public function __construct(ProductService $productService)
    {
        $this->productService = $productService;
    }


    /**
     * Agregar un producto al carrito
     */
    public function addTocarrito(Request $request)
    {
        // Obtener los detalles del producto desde el microservicio SOAP
        $productDetails = $this->productService->getProductById($request->product_id);
        // dd($request, $productDetails);
        if ($productDetails['id']==null) {
            return response()->json(['message' => 'Producto no encontrado.'], 404);
        }

        // Crear o buscar el carrito del usuario
        $carrito = carrito::firstOrCreate(['user_id' => $request->user_id]);

        // Crear o actualizar el item del carrito
        $carritoitem = carritoitem::firstOrCreate(
            [
                'carrito_id' => $carrito->id,
                'producto_id' => $request->product_id
            ],
            ['precio' => $productDetails['precio']]
        );

        // Si ya existe el producto en el carrito, sumar la cantidad
        $carritoitem->cantidad += $request->quantity;
        $carritoitem->save();

        return response()->json(['message' => 'Producto agregado al carrito.', 'carritoitem' => $carritoitem], 201);
    }


    /**
     * Actualizar la cantidad de un producto en el carrito
     */
    public function updatecarrito(Request $request)
    {
        // Obtener el producto desde el servicio SOAP
        $productDetails = $this->productService->getProductById($request->product_id);

        if (!$productDetails) {
            return response()->json(['message' => 'Producto no encontrado.'], 404);
        }

        // Buscar el carrito del usuario
        $carrito = carrito::where('user_id', $request->user_id)->first();

        if (!$carrito) {
            return response()->json(['message' => 'Carrito no encontrado.'], 404);
        }

        // Buscar el item en el carrito
        $carritoitem = carritoitem::where('carrito_id', $carrito->id)
                            ->where('product_id', $request->product_id)
                            ->first();

        if (!$carritoitem) {
            return response()->json(['message' => 'Producto no encontrado en el carrito.'], 404);
        }

        // Actualizar la cantidad del producto
        $carritoitem->quantity = $request->quantity;
        $carritoitem->save();

        return response()->json(['message' => 'Cantidad actualizada.', 'carritoitem' => $carritoitem], 200);
    }


    /**
     * Eliminar un producto del carrito
     */
    public function removeFromcarrito(Request $request)
    {
        // Buscar el carrito del usuario
        $carrito = carrito::where('user_id', $request->user_id)->first();

        if (!$carrito) {
            return response()->json(['message' => 'Carrito no encontrado.'], 404);
        }

        // Buscar el item en el carrito y eliminarlo
        $carritoitem = carritoitem::where('carrito_id', $carrito->id)
                            ->where('product_id', $request->product_id)
                            ->first();

        if (!$carritoitem) {
            return response()->json(['message' => 'Producto no encontrado en el carrito.'], 404);
        }

        $carritoitem->delete();

        return response()->json(['message' => 'Producto eliminado del carrito.'], 200);
    }


    /**
     * Mostrar el contenido del carrito
     */
    public function showcarrito(Request $request)
    {
        // Cargar el carrito junto con los carritoitem
        $carrito = carrito::with('carritoitems')->where('user_id', $request->user_id)->first();

        // dd($carrito->carritoitems);

        // Verificar si el carrito o los carritoitem están vacíos
        if (!$carrito || !$carrito->carritoitems) {
            return response()->json(['message' => 'Carrito vacío.'], 404);
        }

        $total = $carrito->carritoitems->reduce(function ($carry, $item) {
            return $carry + ($item->price * $item->quantity);
        }, 0);

        $carrito->total = $total;

        return response()->json(['carrito' => $carrito],200);
    }
}

?>