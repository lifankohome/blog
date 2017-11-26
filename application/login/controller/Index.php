<?php
namespace app\login\controller;

use app\login\model\User;
use think\Controller;
use think\Request;
use think\Session;

class Index extends Controller
{
    public function index()
    {
        return view();
    }

    public function login(Request $request)
    {
        $username = $request->post('username');
        $password = $request->post('password');

        $result = User::check($username, $password);

        echo $result;
    }

    public function logout()
    {
//      作用域为think的删除
        Session::delete('username', 'think');
        Session::delete('date', 'think');
        //在此处重定向
        echo "正在退出账号...<script>setTimeout(function() {window.location='/blog';},1500)</script>";
    }

    public function register(Request $request)
    {
        $username = $request->post('username');
        $password = $request->post('password');

        $usernameLen = mb_strlen($username);
        $passwordLen = mb_strlen($password);
        if ($usernameLen >= 3 && $usernameLen <= 12) {
            if ($passwordLen >= 8 && $passwordLen <= 16) {
                $date = time();
//                32 + 10 = 42 —— 4-36
                $password = substr(md5($password) . $date, 4, 32);
                $result = User::register($username, $password, $date);

                echo $result;
            } else {
                echo "密码长度不符合要求";
            }
        } else {
            echo "用户名长度不符合要求";
        }
    }
}
