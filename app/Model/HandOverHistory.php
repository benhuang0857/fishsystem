<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class HandOverHistory extends Model
{
    protected $table = 'hand_over_records';
    /*
    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }
    */

    protected $fillable = ['store_id', 'income', 'payout'];

    public function store()
    {
        return $this->belongsTo(Store::class, 'store_id');
    }
}
