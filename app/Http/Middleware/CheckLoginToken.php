<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Redis;

class CheckLoginToken
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
        // 检测是否有token和id
        if(empty($_COOKIE['token']) || empty($_COOKIE['id'])){
            header('Refresh:3;url=http://passport.api.com');
            die('请先登录');
        }

        // 验证token是否有效
        $key = 'token_'.$_SERVER['REMOTE_ADDR'].'_'.$_COOKIE['id'];
        $token = Redis::get($key);
        if($_COOKIE['token'] == $token){

        }else{
            header('Refresh:3;url=http://passport.api.com');
            die('无效的token');
        }
        return $next($request);
    }
}
