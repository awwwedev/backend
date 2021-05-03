<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;


class News extends Model
{
    use HasFactory;

    protected $guarded = [];
    protected $casts = [
        'created_at' => 'datetime:Y-m-d H:i:s',
        'updated_at' => 'datetime:Y-m-d H:i:s',
    ];

    /**
     * @param $value
     *
     * @return false|string
     */
    public function getPhoto($value){
        $pics=json_decode($value);
        foreach ($pics as $key=>$pick){
            $pics[$key]=base_path().$pick;
        }
        return json_encode($pics);
    }
    /**
     * Get the own User
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo('App\Models\User');
    }
}
