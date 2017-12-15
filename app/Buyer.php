<?php

namespace App;

use App\Transaction;
use App\Scopes\BuyerScope;

class Buyer extends User
{

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
