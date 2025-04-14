<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Courier extends Model
{
    use HasFactory;

    protected $fillable = ['title', 'description', 'views'];

    public function votes() {
        return $this->hasMany(Vote::class);
    }
}
