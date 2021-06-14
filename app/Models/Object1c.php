<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Object1c extends Model
{
    use HasFactory;

    protected $guarded = [];

    /**
     * Get the own User
     */
    public function user()
    {
        return $this->belongsTo('App\Models\User');
    }
}
