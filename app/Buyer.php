<?php

namespace App;

use App\Transaction;
use App\Scopes\BuyerScope;
use App\Transformers\BuyerTransformer;

class Buyer extends User
{


    public $transformer = BuyerTransformer::class;

    // Metodo para poder implementar los scopes
    // Permite la inyeccion explicita de dependencia en Buyer
    protected static function boot()
    {
        parent::boot();

        static::addGlobalScope(new BuyerScope);
    }

    public function transactions() {
        return $this->hasMany(Transaction::class);
    }
}
