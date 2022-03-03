<?php

namespace YK\Basic\Models;


use Illuminate\Database\Eloquent\Model;

class AdminParam extends BaseModel
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'param_name', 'param_value', 'created_by','updated_by',
    ];

    public static function getValue($name,$default=null)
    {
        $data = AdminParam::where('param_name', '=', $name)->get();
        if (count($data) > 0)
            return empty($data[0]->param_value) ? $default : $data[0]->param_value;
        return $default;
    }

}
