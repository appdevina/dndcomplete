<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable,SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $guarded = [
        'id',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_secret',
        'two_factor_recovery_codes',
        'created_at',
        'updated_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function scopeFilter($query)
    {
        if(request('search')){
            $query->where('nama_lengkap',"like",'%'.request('search').'%');
        }
    }

    public function approval()
    {
        return $this->belongsTo(User::class,'approval_id');
    }

    public function area()
    {
        return $this->belongsTo(Area::class);
    }

    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    public function divisi()
    {
        return $this->belongsTo(Divisi::class);
    }

    public function daily()
    {
        return $this->hasMany(Daily::class);
    }

    public function weekly()
    {
        return $this->hasMany(Weekly::class);
    }

    public function monthly()
    {
        return $this->hasMany(Monthly::class);
    }

    public function request()
    {
        return $this->hasMany(Request::class);
    }

    public function cutpoint()
    {
        return $this->hasMany(Cutpoint::class);
    }

    public function overopen()
    {
        return $this->hasMany(Overopen::class);
    }

    public function position()
    {
        return $this->belongsTo(Position::class);
    }
}
