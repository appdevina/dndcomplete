<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Monthly extends Model
{
    use HasFactory,SoftDeletes;

    protected $guarded = [
        'id',
    ];

    public function user()
    {
        return $this->hasMany(User::class);
    }

    public function getDateAttribute($value)
    {
        return Carbon::parse($value)->getPreciseTimestamp(3);
    }

    public function getCreatedAtAttribute($value)
    {
        return Carbon::parse($value)->getPreciseTimestamp(3);
    }

    public function getUpdatedAtAttribute($value)
    {
        return Carbon::parse($value)->getPreciseTimestamp(3);
    }
}
