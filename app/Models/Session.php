<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Session extends Model
{
    use HasFactory;
    protected $table = "sessions";
    protected $guarded = false;
    public $timestamps = false;

    function jsReservedSeats() {
        $schemeId = $this->scheme_id;
        $scheme = Scheme::where('id', $schemeId)->first();
        $seatsData = json_decode($scheme->seats, true);
        $reservedSeats = [];
        foreach ($seatsData as $seat) {
            if ($seat['reserved']) {
                $reservedSeats[] = [$seat['row'], $seat['seat']];
            }
        }
        $jsReservedSeats = json_encode($reservedSeats);
        return $jsReservedSeats;
    }

    function formattedTime() {
        return Carbon::parse($this->time)->format('H:i');
    }

    function formattedDate() {
        return Carbon::parse($this->date)->format('d.m.Y');
    }
}
