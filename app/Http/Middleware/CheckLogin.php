<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Redis;

class CheckLogin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $token = $request->token;
        $id = $request->id;

        if(empty($token) || empty($id)){
            $response = [
                'errcode'    =>  50001,
                'msg'        =>  '参数不全',
            ];
            die(json_encode($response,JSON_UNESCAPED_UNICODE));
        }

        if($token){
            $key = 'login_token:id:'.$id;
            $local_token = Redis::get($key);
            // var_dump($local_token);
            if($token == $local_token){
                // TODO
            }else{
                $response = [
                    'errcode'    =>  50003,
                    'msg'        =>  '无效的token',
                ];
                die(json_encode($response,JSON_UNESCAPED_UNICODE));
            }
        }else{
            $response = [
                'errcode'    =>  50004,
                'msg'        =>  '未授权',
            ];
            die(json_encode($response,JSON_UNESCAPED_UNICODE));
        }
        return $next($request);
    }
}
