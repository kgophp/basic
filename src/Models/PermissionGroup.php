<?php

namespace YK\Basic\Models;


use Illuminate\Database\Eloquent\Model;

class PermissionGroup extends Model
{
    protected $guarded = ['id'];

    public function permission()
    {
        return $this->hasMany('YK\Basic\Models\Permission', 'pg_id');
    }
}
