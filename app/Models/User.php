<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Auth;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasFactory;

    protected $table = 'users';
    protected $guarded = false;

    public function isAdmin() : Bool {
        return $this->role_id === 1;
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

    public function admin() {
        return $this->role_id == 1;
    }
}
