<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Comment extends Model
{
    use HasFactory;
    protected $table = 'comments';
    protected $guarded = false;

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    public function likes()
    {
        return $this->hasMany(Like::class);
    }

    public function dislikes()
    {
        return $this->hasMany(Dislike::class);
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
        return Carbon::parse($date)->format('d.m.Y H:i');
    }

    public function formatDate4($date)
    {
        return Carbon::parse($date)->format('d.m.Y H:i:s');
    }

    public function isLikedByUser() {
        if($user = Auth::user()) {
            $like = Like::where('user_id', $user->id)->where('comment_id', $this->id)->first();
            if($like) return true;
            else return false;
        }else{
            return false;
        }
    }

    public function isDislikedByUser() {
        if($user = Auth::user()) {
            $dislike = Dislike::where('user_id', $user->id)->where('comment_id', $this->id)->first();
            if($dislike) return true;
            else return false;
        }else{
            return false;
        }
    }
}
