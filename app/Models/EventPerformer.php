<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EventPerformer extends Model
{
    use HasFactory;
    protected $table = "events_performers";
    protected $guarded = false;
    public $timestamps = false;
}
