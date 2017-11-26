<?php
/**
 * Created by PhpStorm.
 * User: lifanko  lee
 * Date: 2017/10/18
 * Time: 9:55
 */
namespace app\blog\model;

use think\Db;
use think\Model;

class Zone extends Model
{
    protected $table = 'gnosis';

    private static $tableName = 'gnosis';   //DB使用-数据表名

    public static function passageGnosis($username)
    {
        $result['passage'] = Db::table(Zone::$tableName)->where('name', $username)->where('status', 'gnosis')->select();

        $result['num'] = Db::table(Zone::$tableName)->where('name', $username)->where('status','gnosis')->count();
        $result['length'] = Db::table(Zone::$tableName)->where('name', $username)->where('status', 'gnosis')->sum('length');
        $result['read'] = Db::table(Zone::$tableName)->where('name', $username)->where('status', 'gnosis')->sum('times');
        $result['good'] = Db::table(Zone::$tableName)->where('name', $username)->where('status', 'gnosis')->sum('good');

        return $result;
    }

    public static function passageGnosised($username)
    {
        $result = Db::table(Zone::$tableName)->where('name', $username)->where('status', 'gnosised')->select();

        return $result;
    }

    public static function passageGnosising($username)
    {
        $result = Db::table('gnosis')->where('name', $username)->where('status', 'gnosising')->select();

        return $result;
    }

    public static function rename($id, $title, $times)
    {
        if (!empty(trim($title))) {
            $res = Db::table('gnosis')
                ->where('id', $id)
                ->update([
                    'head' => $title,
                    'reEdit' => $times,
                    'saved' => time()
                ]);
        } else {
            $res = false;
        }
        if ($res) {
            return '<script>window.location = "/blog/zone/1";</script>';
        } else {
            return '<script>window.location = "/blog/zone/2";</script>';
        }
    }

    public static function release($id, $username)
    {
        $passage = Db::table('gnosis')->where('id', $id)->find();

        $timeStamp = time();
        if ($passage['status'] == 'gnosising') {
            $timeDiff = self::timediff(strtotime($passage['date']), strtotime(date('Y/m/d H:i', $timeStamp)));

            if ($timeDiff['day'] > 0) {//超过一天
                $time = $timeDiff['day'] . "天";
            } else {
                if ($timeDiff['hour'] > 0) {//超过一小时
                    $time = $timeDiff['hour'] . "小时";
                } else {
                    if ($timeDiff['min'] > 0) {//超过一分钟
                        $time = $timeDiff['min'] . "分钟";
                    } else {
                        $time = "1分钟";
                    }
                }
            }

            if ($username == $passage['name']) {
                $res = Db::table('gnosis')
                    ->where('id', $id)
                    ->update([
                        'saved' => $timeStamp,
                        'date' => date('Y/m/d H:i', $timeStamp),
                        'status' => 'gnosis',
                        'time' => $time
                ]);
            } else {
                $res = false;
            }
        } else {
            $res = false;
        }

        if ($res) {
            return '<script>window.location = "/blog/zone/5";</script>';
        } else {
            return '<script>window.location = "/blog/zone/6";</script>';
        }
    }

    public static function withdraw($id, $username)
    {
        $passage = Db::table('gnosis')->where('id', $id)->find();

        if ($passage['status'] == 'gnosis') {
            if ($username == $passage['name']) {
                $res = Db::table('gnosis')
                    ->where('id', $id)
                    ->update([
                        'saved' => time(),
                        'status' => 'gnosised'
                    ]);
            } else {
                $res = false;
            }
        } else {
            $res = false;
        }

        if ($res) {
            return '<script>window.location = "/blog/zone/7";</script>';
        } else {
            return '<script>window.location = "/blog/zone/8";</script>';
        }
    }

    public static function del($id, $username)
    {
        $passage = Db::table('gnosis')->where('id', $id)->find();

        if ($passage['status'] != 'gnosis') {
            if ($username == $passage['name']) {
                $res = Db::table('gnosis')
                    ->where('id', $id)
                    ->update([
                        'saved' => time(),
                        'status' => 'gg'
                    ]);
            } else {
                $res = false;
            }
        } else {
            $res = false;
        }

        if ($res) {
            return '<script>window.location = "/blog/zone/9";</script>';
        } else {
            return '<script>window.location = "/blog/zone/10";</script>';
        }
    }

    public static function newPass($username, $newPass, $date)
    {
        if (strlen($newPass) == 32) {
            $res = Db::table('blog')
                ->where('username', $username)
                ->update([
                    'password' => $newPass,
                    'date' => $date
                ]);
        } else {
            $res = false;
        }
        if ($res) {
            return '<script>window.location = "/blog/zone/3";</script>';
        } else {
            return '<script>window.location = "/blog/zone/4";</script>';
        }
    }

    private static function timeDiff($begin_time, $end_time)
    { //获取时间差
        if ($begin_time < $end_time) {
            $startTime = $begin_time;
            $endTime = $end_time;
        } else {
            $startTime = $end_time;
            $endTime = $begin_time;
        }
        $timeDiff = $endTime - $startTime;
        $day = intval($timeDiff / 86400);
        $remain = $timeDiff % 86400;
        $hour = intval($remain / 3600);
        $remain = $remain % 3600;
        $min = intval($remain / 60);
        $sec = $remain % 60;
        $res = array("day" => $day, "hour" => $hour, "min" => $min, "sec" => $sec);
        return $res;
    }
}