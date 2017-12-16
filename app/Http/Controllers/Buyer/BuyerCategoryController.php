<?php

namespace App\Http\Controllers\Buyer;

use App\Buyer;
use Illuminate\Http\Request;
use App\Http\Controllers\ApiController;

class BuyerCategoryController extends ApiController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Buyer $buyer)
    {
        // Getting all the sellers with distinct (buyer->transactions->product->category)
        // Collapse joins all the lists in only one
        $categories = $buyer->transactions()
                            ->with('product.categories')
                            ->get()
                            ->pluck('product.categories')
                            ->collapse()
                            ->unique('id')
                            ->values();

        return $this->showAll($categories);
    }
}
