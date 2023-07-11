<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Kpi extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = [
        'id',
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];   

    public function kpi_category()
    {
        return $this->belongsTo(KpiCategory::class);
    }

    public function kpi_type()
    {
        return $this->belongsTo(KpiType::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function kpi_detail()
    {
        return $this->hasMany(KpiDetail::class, 'kpi_id');
    }
}
