<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class JackpotHistory extends Model
{
    protected $table = 'history_data';

    protected $fillable = [
        'mac', 'player', 'jackpot', 'coins', 'datetime', 'verified'
    ];

    public function Machine()
    {
        return $this->belongsTo(Machine::class, 'mac', 'mac');
    }
}
