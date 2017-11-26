<?php
/**
 * Created by PhpStorm.
 * User: lifanko  lee
 * Date: 2017/9/30
 * Time: 0:34
 */
namespace app\blog\model;

use think\Db;
use think\Model;

class Comment extends Model
{
    protected $table = 'comment';

    public function getLogo()
    {
        return "logo/default.jpg";
    }

    public static function show($uuid)
    {
        $result = Comment::get(['uuid' => $uuid]);
        if ($result == null) {
            $res = array();
        } else {
            $res = json_decode(base64_decode($result['comment']), true);
        }

        return $res;
    }

    public static function saveComment($uuid, $title, $comment, $name = '')
    {
        $date = time();
        $com = Comment::get(['uuid' => $uuid]);

        $temp['text'] = $comment;   //评论内容
        $temp['date'] = date('Y-m-d H:i', $date);      //评论日期
        $temp['warn'] = 0;          //评论被举报次数
        $temp['reply'] = "";        //对评论的评论

        if ($com == null) {
            if (empty($name)) {         //形成二维数组保存
                $arr[1] = $temp;        //如果没有姓名则用数字索引，从1开始
            } else {
                $arr[$name] = $temp;
            }

            $comment = base64_encode(json_encode($arr));    //编码后容许保存到数据库

            $res = Db::table('comment')->insert(['uuid' => $uuid, 'title' => $title, 'comment' => $comment]);
        } else if ($title == $com['title']) {
            $arr = json_decode(base64_decode($com['comment']), true);

            if (empty($name)) {
                $arr[] = $temp;
            } else {
                if (array_key_exists($name, $arr)) {
                    $temp['warn'] = $arr[$name]['warn'];
                    $temp['reply'] = $arr[$name]['reply'];
                }
                $arr[$name] = $temp;
            }
            $comment = base64_encode(json_encode($arr));

            $res = Db::table('comment')->where('uuid', $uuid)->update(['comment' => $comment]);
        } else {  //uuid一样，title却不一样，说明有异常
            $res = false;
        }

        if ($res) {
            return "success";
        } else {
            return "fail";
        }
    }
}