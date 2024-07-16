<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Performer extends Model
{
    use HasFactory;
    protected $table = 'performers';
    protected $guarded = false;

    public function events()
    {
        return $this->belongsToMany(Event::class, 'events_performers', 'performer_id', 'event_id')->orderBy('date', 'asc')->orderBy('time', 'asc');
    }

    public function isFavorite() {
        if($user = Auth::user()) {
            $favorite = FavoritePerformer::where('user_id', $user->id)->where('performer_id', $this->id)->first();
            if($favorite) return true;
            else return false;
        }else{
            return false;
        }
    }

    public function favoritePerformers()
    {
        return $this->hasMany(FavoritePerformer::class);
    }
}
