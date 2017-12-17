<?php

namespace App\Http\Controllers\Seller;

use App\User;
use App\Seller;
use App\Product;
use Illuminate\Http\Request;
use App\Http\Controllers\ApiController;
use Illuminate\Support\Facades\Storage;
use App\Transformers\ProductTransformer;
use Symfony\Component\HttpKernel\Exception\HttpException;

class SellerProductController extends ApiController
{
    public function __construct()
    {
        parent::__construct();

        // Implementing middleware to allow the names of the attributes in the put - patch and store request
        // We use Product transformer because is the one that is being created
        $this->middleware('transform.input:'. ProductTransformer::class)->only(['store', 'update']);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Seller $seller)
    {
        $products = $seller->products;

        return $this->showAll($products);
    }

    /**
     * Store a newly created resource in storage.
     * Creating a products from a seller
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  App\User  $seller
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, User $seller)
    {
        $rules = [
            'name' => 'required',
            'description' => 'required',
            'quantity' => 'required|integer|min:1',
            'image' => 'required|image'
        ];

        $this->validate($request, $rules);

        $data = $request->all();

        $data['status'] = Product::PRODUCTO_NO_DISPONIBLE;
        $data['image'] = $request->image->store('', 'images');
        $data['seller_id'] = $seller->id;

        $product = Product::create($data);

        return $this->showOne($product, 201);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Seller  $seller
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Seller $seller, Product $product)
    {
        $rules = [
            'quantity' => '|integer|min:1',
            'status' => 'in:' . Product::PRODUCTO_DISPONIBLE . ',' . Product::PRODUCTO_NO_DISPONIBLE,
            'image' => 'image'
        ];

        $this->validate($request, $rules);

        $this->verificarVendedor($seller, $product);

        // Set the new info if exists in the request
        $product->fill($request->only([
            'name',
            'description',
            'quantity'
        ]));

        // Check if it has a new state
        if ($request->has('status')) {
            $product->status = $request->status;
            if ($product->estaDisponble() && $product->categories()->count() == 0) {
                return $this->errorResponse('Un Producto activo debe tener almenos una categoria', 409);
            }
        }

        // Eliminando y creando una nueva imagen
        if ($request->hasFile('image')) {
            Storage::disk('images')->delete($product->image);
            $product->image = $request->image->store('', 'images');
        }

        if ($product->isClean()) {
            return $this->errorResponse('Se debe especificar almenos un valor diferente para actualizar', 422);
        }

        $product->save();
        return $this->showOne($product);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Seller  $seller
     * @return \Illuminate\Http\Response
     */
    public function destroy(Seller $seller, Product $product)
    {
        $this->verificarVendedor($seller, $product);

        // Deleting the image
        Storage::disk('images')->delete($product->image);

        $product->delete();

        return $this->showOne($product);
    }

    /**
     * Validates if a seller isthe owner of a product.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Seller  $seller
     */
    protected function verificarVendedor(Seller $seller, Product $product)
    {
        if ($seller->id != $product->seller_id) {
            throw new HttpException(422, 'El vendedor no es el real dueño del producto');
        }
    }
}
