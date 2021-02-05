<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Machine extends Model
{
    protected $table = 'machine';

    public function Store()
    {
        return $this->belongsTo(Store::class, 'sid', 'id');
    }
}
