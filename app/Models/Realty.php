<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Realty extends Model
{
    use HasFactory;

    protected $table='realties';
    protected $guarded = [];
    protected $casts = [
        'created_at' => 'datetime:Y-m-d H:i:s',
        'updated_at' => 'datetime:Y-m-d H:i:s',
        'photo' => 'array',
        'is_published' => 'boolean',
    ];

    /**
     * @return BelongsTo
     */
    public function realtyType(): BelongsTo
    {
        return $this->belongsTo(RealtyType::class, 'type_id', 'id');
    }

    public function equipments(): BelongsToMany
    {
        return $this->belongsToMany(Equipment::class, RealtyEquipment::class);
    }
}
