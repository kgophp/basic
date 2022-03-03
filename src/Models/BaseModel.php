<?php

namespace YK\Basic\Models;


use Illuminate\Database\Eloquent\Model;
use YK\Basic\Extensions\AdminUserFactory;

class BaseModel extends Model
{
    private $userModel;

    public function __construct(array $attributes = []){
        parent::__construct($attributes);
        $this->userModel='auth.providers.' . config('admin.super_admin.provider') . '.model';
    }

    /*
     * 创建人
    */
    public function creator(){
        return $this->belongsTo($this->userModel,'createby');
    }

    /*
     * 修改人
     */
    public function updater(){
        return $this->belongsTo($this->userModel,'updateby');
    }
}
