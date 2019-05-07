<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Session;

class registertentimes
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
        // 设置接口调用次数限制 每半分钟10次
        $key = 'register:time:'.$_SERVER['REMOTE_ADDR'].$request->id;
        $num = Redis::get($key);
        if($num > 10){
            die('超出次数限制');
        }
//        echo $num;
//        echo "<br>";
        Redis::incr($key);
        Redis::expire($key,60);


        return $next($request);
    }
}
