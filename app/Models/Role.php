<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * Class Role
 * @package App\Models
 */
class Role extends Model
{
    use HasFactory;

    const ADMIN = 'ADMIN';
    const TENANT = 'TENANT';
    protected $casts = [
        'created_at' => 'datetime:Y-m-d H:i:s',
        'updated_at' => 'datetime:Y-m-d H:i:s',
    ];

    /**
     * Get the own User
     */
    public function users(): HasMany
    {
        return $this->hasMany('App\Models\User');
    }

    public static function getAdmin()
    {
        return Role::whereRole(self::ADMIN);
    }

    public static function getTenant()
    {
        return Role::whereRole(self::TENANT);
    }

    protected $guarded = [];
}

