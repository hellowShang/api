<?php

namespace App\Http\Controllers\Api;

use App\Model\UserModel;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Redis;

class UserController extends Controller
{
    // 获取用户信息
    public function getUserInfo(Request $request){
        // 接收id
        $id = $request->input('id');

        // 数据查询
        $arr  = UserModel::where(['id' => $id])->first();

        // 返回数据
        $data = [];
        if($arr){
            $data = [
                'errcode' => 0,
                'msg' => 'ok',
                'data' => $arr
            ];
        }else{
            $data = [
                'errcode' => 50001,
                'msg' => '出错了',
                'data' => $arr
            ];
        }

        echo json_encode($data,JSON_UNESCAPED_UNICODE);
    }

    // api请求apitest
    public function cURLTest(){
        $url = 'http://vm.apitest.com/api/userinfo?id=1';
        // 创建一个新cURL资源
        $ch = curl_init();

        // 设置URL和相应的选项
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HEADER, 0);

        // 抓取URL并把它传递给浏览器
        curl_exec($ch);

        // 关闭cURL资源，并且释放系统资源
        curl_close($ch);
    }

    // curl   post请求  form-data格式
    public function curlPost1(){
        $url = 'http://vm.apitest.com/api/post';
        $data = [
            'name'      =>  '陈佩斯',
            'email'     =>  'chengpeisi@qq.com',
            'sex'       =>  1
        ];
        $ch = curl_init();
        curl_setopt($ch,CURLOPT_URL,$url);

        // 禁用浏览器输出
        curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);

        // post方式发送
        curl_setopt($ch,CURLOPT_POSTFIELDS,$data);

        // 接收数据
        $rs = curl_exec($ch);
        var_dump($rs);
        curl_close($ch);
    }

    // curl   post请求  application/x-www-form-urlencoded格式
    public function curlPost2(){
        $url = 'http://vm.apitest.com/api/post';
        $str = "name=小兰姐姐&email=xiaolanjiejie@qq.com&sex=1";
        $ch = curl_init();
        curl_setopt($ch,CURLOPT_URL,$url);

        // 禁用浏览器输出
        curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);

        // post方式发送
        curl_setopt($ch,CURLOPT_POSTFIELDS,$str);

        // 接收数据
        $rs = curl_exec($ch);
        var_dump($rs);
        curl_close($ch);
    }

    // curl   post请求   raw(字符串文本)格式
    public function curlPost3(){
        $url = 'http://vm.apitest.com/api/post1';
        $data = [
            'name'      =>  '嘤嘤嘤',
            'email'     =>  'yingyingying@qq.com',
            'sex'       =>  2
        ];
        $json = json_encode($data,JSON_UNESCAPED_UNICODE);
        $ch = curl_init();
        curl_setopt($ch,CURLOPT_URL,$url);

        // 禁用浏览器输出
        curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);

        // post方式发送
        curl_setopt($ch,CURLOPT_POSTFIELDS,$json);
        curl_setopt($ch,CURLOPT_HTTPHEADER,['Content-Type:text/plain']);

        // 接收数据
        $rs = curl_exec($ch);
        var_dump($rs);
        curl_close($ch);
    }

    // 中间件
    public  function middle(){
        echo 123;
    }

    // 错误
    public function error($num,$font){
        $message = [
            'errcode'       =>  $num,
            'msg'           =>  $font
        ];
        return json_encode($message,JSON_UNESCAPED_UNICODE);
    }

    // 正确
    public function success($num,$font,$data){
        $message = [
            'errcode'       =>  $num,
            'msg'           =>  $font,
            'data'          =>  $data
        ];
        return json_encode($message,JSON_UNESCAPED_UNICODE);
    }

    // 注册
    public function register(Request $request){
        // 接收name属性值
        $data = $request->all();

        // 判断俩次密码
        if($data['pass1'] != $data['pass2']){
            die($this->error(50002,'俩次输入密码不一致'));
        }

        // 判断邮箱
        $e = UserModel::where(['email' => $data['email']])->first();
        if($e){
            die($this->error(50003,'该email已存在'));
        }

        // 密码hash算法加密
        $data['pass1'] = password_hash($data['pass1'],PASSWORD_BCRYPT);
        // 删除多余字段
        unset($data['pass2']);

        // 入库
        $id = UserModel::insertGetId($data);

        // 入库成功响应
        if($id){
            die($this->success(0,'注册成功',['id' => $id]));
        }else{
            die($this->error(50004,'注册失败'));
        }
    }

    // 登录
    public function login(Request $request){
        $data = $request->all();

        // 登录检测、验证
        $arr = UserModel::where(['email' => $data['email']])->first();
        if($arr){
            // hash算法密码验证
            if(password_verify($data['pass'],$arr->pass1)){
                // 生成token
                $token = $this->generateToken($arr->id);

                // 缓存
                $token_key = 'login_token:id:'.$arr->id;
                Redis::set($token_key,$token);
                // 设置过期时间为7天
                Redis::expire($token_key,604800);
                die($this->success(0,'登录成功',['token' => $token]));
            }else{
                die($this->error(50011,'账号或密码错误'));
            }
        }else{
            die($this->error(50010,'账号或密码错误'));
        }
    }

    // 生成登录token
    public function generateToken($id){
        return substr(md5($id.rand(1111,9999).time().Str::random(20)),5,20);
    }

    // 个人中心
    public function user(){
        echo 123;
    }
}
