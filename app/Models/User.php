<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Laravel\Jetstream\HasProfilePhoto;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens;
    use HasFactory;
    use HasProfilePhoto;
    use Notifiable;
    use TwoFactorAuthenticatable;


    protected $guarded = [];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_recovery_codes',
        'two_factor_secret',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = [
        'profile_photo_url',
    ];

    /**
     * Get News models
     */
    public function news()
    {
        return $this->hasMany('App/Models/News');
    }

    /**
     * Get Contact models
     */
    public function contacts()
    {
        return $this->hasMany('App/Models/Contact');
    }

    /**
     * Get Slide models
     */
    public function slides()
    {
        return $this->hasMany('App/Models/Slides');
    }

    /**
     * Get Slide models
     */
    public function typeContacts()
    {
        return $this->hasMany('App/Models/TypeContact');
    }

    /**
     * Get Slide models
     */
    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }

    /**
     * Get object1c models
     */
    public function object1cs()
    {
        return $this->hasMany(Object1c::class);
    }

    /**
     * @return HasOne
     */
    public function ticket(): HasOne
    {
        return $this->hasOne(Ticket::class);
    }
}
