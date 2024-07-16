<?php

namespace App\Providers;

use App\Models\Genre;
use App\Models\User;
use App\Models\Wallet;
use App\Models\Watched;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        View::composer('*', function ($view) {
            $view->with('user', auth()->user());
            $view->with('genres', Genre::all());
            if(Auth::user()) {
                $view->with('watched', Watched::where('user_id', auth()->user()->id)->orderBy('updated_at', 'desc')->take(4)->get());
                $walletId = User::where('id', auth()->user()->id)->value('wallet_id');
                $view->with('wallet', Wallet::where('id', $walletId)->first());
                $view->with('auth', true);
            } else {
                $view->with('auth', false);
            }
        });
    }
}
