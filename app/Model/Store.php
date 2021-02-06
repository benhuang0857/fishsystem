<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Store extends Model
{
    
    protected $table = 'store';

    public function getMachinelistAttribute($value)
    {
        return explode(',', $value);
    }

    public function setMachinelistAttribute($value)
    {
        $this->attributes['machine_list'] = implode(',', $value);
    }

    public function Machine()
    {
        return $this->belongsToMany(Machine::class, 'store_machine', 'store_id', 'machine_id');
    }
}
