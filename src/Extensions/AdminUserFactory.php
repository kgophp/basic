<?php

namespace YK\Basic\Extensions;


use Illuminate\Database\Eloquent\Model;

class AdminUserFactory
{
    /**
     * @return Model
     */
    public static function adminUser()
    {
        $key = 'auth.providers.' . config('admin.super_admin.provider') . '.model';

        return app(config($key));
    }
}
