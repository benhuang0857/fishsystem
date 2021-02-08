<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class FishData extends Model
{

    public function getTagsAttribute($value)
    {
        return explode(',', $value);
    }

    public function setTagsAttribute($value)
    {
        $this->attributes['name'] = implode(',', $value);
    }
    
    protected $table = 'fish_data';

    public $timestamps = false;

    protected $fillable = [
        'mac', 'machine_id', 'coin_ratio', 'player_count', 'income', 'payout', 'update_time'
    ];

    public function machine()
    {
        return $this->hasOne(Machine::class, 'mac', 'mac');
    }
}
