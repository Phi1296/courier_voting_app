<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Vote extends Model
{
    protected $fillable = ['user_id', 'courier_id', 'type'];

    public function courier() {
        return $this->belongsTo(Courier::class);
    }

    public function user() {
        return $this->belongsTo(User::class);
    }
}
