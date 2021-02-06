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

}
