<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Event extends Model
{
    use HasFactory;
    protected $table = 'events';
    protected $guarded = false;

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    public function getAverageRatingAttribute()
    {
        $totalRating = 0;
        $comments = $this->comments;

        if ($comments->count() > 0) {
            foreach ($comments as $comment) {
                $totalRating += $comment->rating;
            }
            $averageRating = $totalRating / $comments->count();
            return number_format($averageRating, 1);
        } else {
            return 5.0;
        }
    }

    public function performers()
    {
        return $this->belongsToMany(Performer::class, 'events_performers', 'event_id', 'performer_id');
    }

    public function ageLimit()
    {
        return $this->belongsTo(AgeLimit::class);
    }

    public function scheme()
    {
        return $this->belongsTo(Scheme::class);
    }
    
    public function subgenre()
    {
        return $this->belongsTo(Subgenre::class);
    }

    public function place()
    {
        return $this->belongsTo(Place::class);
    }

    public function formatDate1($date)
    {
        return Carbon::parse($date)->format('d.m.Y');
    }

    public function formatDate2($date)
    {
        return Carbon::parse($date)->format('H:i');
    }

    public function formatDate3($date)
    {
        return Carbon::parse($date)->format('d.m.Y • H:i');
    }

    public function formatDate4($date)
    {
        return Carbon::parse($date)->format('d.m.Y H:i:s');
    }

    public function isFavorite()
    {
        if ($user = Auth::user()) {
            $favorite = FavoriteEvent::where('user_id', $user->id)->where('event_id', $this->id)->first();
            if ($favorite) return true;
            else return false;
        } else {
            return false;
        }
    }

    public function favoriteEvents()
    {
        return $this->hasMany(FavoriteEvent::class);
    }
}
