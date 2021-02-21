<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Machine extends Model
{
    protected $table = 'machine';

    public function Store()
    {
        return $this->belongsToMany(Store::class, 'store_machine', 'machine_id', 'store_id');
    }

    public function fishData()
    {
        return $this->hasMany(FishData::class, 'mac', 'mac');
    }

    public function JackpotHistory()
    {
        return $this->hasMany(JackpotHistory::class, 'mac', 'mac');
    }

    public function PlayerData()
    {
        return $this->hasMany(PlayerData::class, 'mac', 'mac');
    }
}
