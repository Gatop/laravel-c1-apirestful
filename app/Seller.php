<?php

namespace App;

use App\Product;
use App\Scopes\SellerScope;

class Seller extends User
{
    // Metodo para poder implementar los scopes
    // Permite la inyeccion explicita de dependencia en Buyer
    protected static function boot()
    {
        parent::boot();

        static::addGlobalScope(new SellerScope);
    }

    public function products() {
        return $this->hasMany(Product::class);
    }
}
