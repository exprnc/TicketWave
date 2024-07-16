<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AgeLimit extends Model
{
    use HasFactory;
    protected $table = 'age_limits';
    protected $guarded = false;
}
