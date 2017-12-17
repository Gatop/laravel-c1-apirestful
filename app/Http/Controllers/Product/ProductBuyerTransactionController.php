<?php

namespace App\Http\Controllers\Product;

use App\User;
use App\Product;
use App\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\ApiController;
use App\Transformers\TransactionTransformer;

class ProductBuyerTransactionController extends ApiController
{
    public function __construct()
    {
        parent::__construct();

        // Implementing middleware to allow the names of the attributes in the put - patch and store request
        // We use Transaction transformer because is the one that is being created
        $this->middleware('transform.input:'. TransactionTransformer::class)->only(['store']);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, Product $product, User $buyer)
    {
        $rules = [
            'quantity' => 'required|integer|min:1'
        ];

        $this->validate($request, $rules);

        if ($buyer->id == $product->seller_id) {
            return $this->errorResponse('El comprador debe ser diferente al vendedor', 409);
        }

        if (!$buyer->esVerificado()) {
            return $this->errorResponse('El comprador debe un usuario verificado', 409);
        }

        if (!$product->seller->esVerificado()) {
            return $this->errorResponse('El vendedor debe un usuario verificado', 409);
        }

        if (!$product->estaDisponible()) {
            return $this->errorResponse('El producto debe estar disponible', 409);
        }

        if ($product->quantity < $request->quantity) {
            return $this->errorResponse('El producto no tiene la cantidad disponible requerida para la esta transacccion', 409);
        }

        // Se habilita DB transaccions por si llega mas de una
        // Si en algun momento falla hace un rollback de todo
        return DB::transaction(function () use ($request, $product, $buyer){
            // Se reduce la cantidad del producto
            $product->quantity -= $request->quantity;
            $product->save();

            $transaction = Transaction::create([
                'quantity'   => $request->quantity,
                'buyer_id'   => $buyer->id,
                'product_id' => $product->id
            ]);

            return $this->showOne($transaction);
        });

    }
}
