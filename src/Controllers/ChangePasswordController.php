<?php

namespace  YK\Basic\Controllers;


use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use  YK\Basic\Requests\ChangePasswordRequest;

class ChangePasswordController extends Controller
{
    /**
     * @author nash
     * @param ChangePasswordRequest $request
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Response
     */
    public function changePassword(ChangePasswordRequest $request)
    {
        $user = Auth::user();

        if (! Hash::check($request->old_password, $user->password)) {
           throw new \Exception('原始密码错误');
        }
        if($request->password == $request->old_password){
            throw new \Exception('新密码和原始密码不能一致');
//            return $this->unprocesableEtity([
//                'password' => '新密码和原始密码不能一致'
//            ]);
        }

        $user->update_password_time = date('Y-m-d H:i:s',time());
        $user->password = bcrypt($request->password);
        $user->save();

        return $this->noContent();
    }
}
