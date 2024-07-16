<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FavoriteEvent extends Model
{
    use HasFactory;

    protected $table = "favorite_events";
    protected $guarded = false;
}
