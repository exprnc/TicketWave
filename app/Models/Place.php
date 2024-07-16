<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Place extends Model
{
    use HasFactory;
    protected $table = 'places';
    protected $guarded = false;

    public function events()
    {
        return $this->hasMany(Event::class)->orderBy('date', 'asc')->orderBy('time', 'asc');
    }

    public function isFavorite() {
        if($user = Auth::user()) {
            $favorite = FavoritePlace::where('user_id', $user->id)->where('place_id', $this->id)->first();
            if($favorite) return true;
            else return false;
        }else{
            return false;
        }
    }

    public function favoritePlaces()
    {
        return $this->hasMany(FavoritePlace::class);
    }
}
