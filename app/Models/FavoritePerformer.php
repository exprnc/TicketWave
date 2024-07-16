<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FavoritePerformer extends Model
{
    use HasFactory;
    protected $table = "favorite_performers";
    protected $guarded = false;
}
