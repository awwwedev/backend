<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class RealtyType extends Model
{
    use HasFactory;

    public $timestamps=false;
    protected $guarded = [];
    protected $casts = [
        'created_at' => 'datetime:Y-m-d H:i:s',
        'updated_at' => 'datetime:Y-m-d H:i:s',
    ];

    /**
     * @return HasMany
     */
    public function realty(): HasMany
    {
        return $this->hasMany(Realty::class);
    }

    /**
     * @return HasMany
     */
    public function equipments(): HasMany
    {
        return $this->hasMany(Equipment::class);
    }

}
