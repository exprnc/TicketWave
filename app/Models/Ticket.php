<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ticket extends Model
{
    use HasFactory;
    protected $table = 'tickets';
    protected $guarded = false;

    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    function isHaveSession() {
        $sessions = Session::all();
        $schemesIds = [];
        foreach($sessions as $session) {
            array_push($schemesIds, $session->scheme_id);
        }
        $schemes = Scheme::whereIn('id', $schemesIds)->get();

        foreach($schemes as $scheme) {
            $jsonScheme = json_decode($scheme->seats, true);
            foreach($jsonScheme as $schemeSeat) {
                if($this->user_id == $schemeSeat['user_id'] && $this->zone == $schemeSeat['zone'] && $this->row == $schemeSeat['row'] && $this->seat == $schemeSeat['seat']) {
                    return true;
                }
            }
        }
        return false;
    }

    function session() {
        $sessions = Session::all();
        $schemesIds = [];
        foreach($sessions as $session) {
            array_push($schemesIds, $session->scheme_id);
        }
        $schemes = Scheme::whereIn('id', $schemesIds)->get();

        foreach($schemes as $scheme) {
            $jsonScheme = json_decode($scheme->seats, true);
            foreach($jsonScheme as $schemeSeat) {
                if($this->user_id == $schemeSeat['user_id'] && $this->zone == $schemeSeat['zone'] && $this->row == $schemeSeat['row'] && $this->seat == $schemeSeat['seat']) {
                    return Session::where('scheme_id', $scheme->id)->first();
                }
            }
        }
        return 0;
    }
}
