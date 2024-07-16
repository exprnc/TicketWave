<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subgenre extends Model
{
    use HasFactory;
    protected $table = 'subgenres';
    protected $guarded = false;

    public function genre()
    {
        return $this->belongsTo(Genre::class);
    }
}
