<?php

namespace App\Providers;

use App\User;
use App\Product;
use App\Mail\UserCreated;
use App\Mail\UserMailChanged;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        // Added in order to allow the support for emoticons stored in DB
        Schema::defaultStringLength(191);

        // Hacer update de la cantidad del producto
        Product::updated(function ($product) {
            if ($product->quantity == 0 && $product->estaDisponible()) {
                $product->status = Product::PRODUCTO_NO_DISPONIBLE;

                $product->save();
            }
        });

        // Enviar correo cada vez que creo un usuario
        User::created(function ($user) {
            retry(5, function() use($user) {
                Mail::to($user)->send(new UserCreated($user));
            }, 100);
        });

        // Enviar correo cada vez que hago un update al correo
        User::updated(function ($user) {
            if ($user->isDirty('email')) {
                retry(5, function() use($user) {
                    Mail::to($user)->send(new UserMailChanged($user));
                }, 100);
            }
        });
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
