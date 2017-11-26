<?php
/**
 * Created by PhpStorm.
 * User: lifanko  lee
 * Date: 2017/8/1
 * Time: 21:03
 */
namespace app\login\model;

use think\Db;
use think\Model;
use think\Session;

class User extends Model
{
    //数据表名称
    protected $table = 'blog';

    public static function check($username, $password)
    {
        $user = User::get(['username' => $username]);
        $password = substr(md5($password) . $user['date'], 4, 32);
        if ($user == null) {
            $result = 'null';
        } else if ($user['password'] == $password) {
            $result = '你好！' . $user['username'];
            //设置session
            Session::set('username', $user['username']);
            Session::set('date', $user['date']);
        } else {
            $result = '密码错误';
        }

        return $result;
    }

    public static function register($username, $password, $date)
    {
        $user = User::get(['username' => $username]);
        if ($user == null) {
            $res = Db::table('blog')->insert(['username' => $username, 'password' => $password, 'date' => $date]);
            if ($res) {
                $result = "注册成功，正在登录";
                //设置session
                Session::set('username', $username);
                Session::set('date', $date);
            } else {
                $result = "注册失败，请重试";
            }
        } else {
            $result = $username . " 已经被注册，请更换用户名";
        }

        return $result;
    }
}