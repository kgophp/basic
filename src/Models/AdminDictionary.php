<?php

namespace YK\Basic\Models;


use Illuminate\Database\Eloquent\Model;

class AdminDictionary extends BaseModel
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'parent_id','dict_type','dict_key','dict_value','updated_at', 'updated_by','created_at', 'created_by',
    ];

}
