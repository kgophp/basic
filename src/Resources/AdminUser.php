<?php

namespace YK\Basic\Resources;


use Illuminate\Http\Resources\Json\Resource;

class AdminUser extends Resource
{
    public function toArray($request)
    {
        return [
            'id'    => $this->id,
            'username'  => $this->username,
            'name'  => $this->name,
            'mobile' => $this->mobile,
            'email' => $this->email,
            'created_at' => (string)$this->created_at,
            'updated_at' => (string)$this->updated_at
        ];
    }
}
