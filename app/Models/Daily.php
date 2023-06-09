<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Daily extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = [
        'id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class)->withTrashed();
    }


    public function tag()
    {
        return $this->belongsTo(User::class, 'tag_id')->withTrashed();
    }

    public function getDateAttribute($value)
    {
        if ($value) {
            return Carbon::parse($value)->getPreciseTimestamp(3);
        }
    }

    public function taskcategory()
    {
        return $this->belongsTo(TaskCategory::class, 'task_category_id');
    }

    public function taskstatus()
    {
        return $this->belongsTo(TaskStatus::class, 'task_status_id');
    }

    public function add()
    {
        return $this->belongsTo(User::class, 'add_id')->withTrashed();
    }
}
