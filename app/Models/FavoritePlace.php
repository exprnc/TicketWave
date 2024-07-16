<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FavoritePlace extends Model
{
    use HasFactory;
    protected $table = "favorite_places";
    protected $guarded = false;
}
