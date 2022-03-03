<?php

namespace YK\Basic\Controllers;

use Illuminate\Http\Request;
use YK\Basic\Extensions\AdminUserFactory;

class LoginController extends Controller
{
    public function login(Request $request){
        $data = $request->all();
        $User=AdminUserFactory::adminUser();
        if(empty($data['username']))
            throw new \Exception('参数错误！');
        $userInfo=$User::where('username','=',$data['username'])->first();
        if(empty($userInfo))
            throw new \Exception('用户名或密码错误!');
        if(empty($userInfo->user_state))
            throw new \Exception('该用户已关闭，请联系系统管理员！');


        $proxy = Request::create(
            'oauth/token',
            'POST',
            $data
        );
        $response = \Route::dispatch($proxy);
        try{
            $tokenInfo=json_decode($response->content());
            if (!empty($tokenInfo->access_token)){
                $tokenInfo->username=$userInfo->username;
                $tokenInfo->name=$userInfo->name;
                $tokenInfo->head_image=$userInfo->head_image;
                $tokenInfo->remeber_me=!empty($data['remeber'])?$data['remeber']:false;
                $tokenInfo->is_update_password = false; //是否需要修改密码
                if(empty($userInfo->update_password_time)){
                    $tokenInfo->is_update_password = true;
                }else{
                    $intervalDays = $this->diffBetweenTwoDays($userInfo->update_password_time);
                    if($intervalDays >= 90){
                        $tokenInfo->is_update_password = true;
                    }
                }
                return response()->json($tokenInfo);
            }
        }catch (\Exception $e){
            return $response;
        }
        return $response;
    }

    public function logout(Request $request){
        $user=\Auth::user();
        if (empty($user)) {
            throw new \Exception('暂未登录！');
        }
        $token=$user->token();
        $token->delete();

        return $this->noContent();
    }

    private function diffBetweenTwoDays($updatedAt)
    {
        $Days = null;
        if(!empty($updatedAt)){
            $Date_1 = date("Y-m-d");
            $Date_2 = date('Y-m-d',strtotime($updatedAt));
            $d1 = strtotime($Date_1);
            $d2 = strtotime($Date_2);
            $Days = round(($d1-$d2)/3600/24);
        }
        return $Days;
    }

}
