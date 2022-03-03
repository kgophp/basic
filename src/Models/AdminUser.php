<?php

namespace YK\Basic\Models;


use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Passport\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class AdminUser extends Authenticatable
{
    use HasApiTokens, Notifiable, HasRoles;

    protected $guard_name = 'admin';

    public static function boot() {
        parent::boot();

        static::creating(function($model) {
            if (empty($model->attributes['uuid'])){
                $tmp=\DB::select('select uuid() as uuid');
                $model->attributes['uuid']=str_replace('-','',$tmp[0]->uuid);
            }

        });
    }

    public function findForPassport($login)
    {
        return $this->Where('username', $login)->first();
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'username','name', 'mobile','email', 'password','update_password_time',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];
}
