<?php
/**
 * Created by PhpStorm.
 * User: lifanko  lee
 * Date: 2017/9/28
 * Time: 17:28
 */
namespace app\blog\model;

use think\Db;
use think\Model;
use think\Session;

class Passage extends Model
{
    protected $table = 'gnosis';

    public static function showList()
    {
        $contents = Passage::all(function ($query) {
            $query->where('status', 'gnosis')->order('id', 'DESC');
        });

        return $contents;
    }

    public static function showContent($id)
    {
        $content = Passage::get(['id' => $id]);

        if ($content['status'] == 'gnosis') {   //已发布的文章才记录阅读次数
            $arr = json_decode(Session::get('read'), true);

            if (empty($arr)) {
                $arr = ['index'];
                array_push($arr, $id);   //文章id添加进已读数组
                Session::set('read', json_encode($arr));    //保存

                Db::table('gnosis')->where('id', $id)->setInc('times'); //文章id不在已读数组中，阅读次数加一
            } else if (!in_array($id, $arr)) {
                array_push($arr, $id);   //文章id添加进已读数组
                Session::set('read', json_encode($arr));    //保存

                Db::table('gnosis')->where('id', $id)->setInc('times'); //文章id不在已读数组中，阅读次数加一
            }
        }

        return $content;
    }

    public static function viewPoint($id, $viewPoint, $num)
    {
        $dbNum = Db::table('gnosis')->where('id', $id)->value($viewPoint); // table方法必须指定完整的数据表名

        if ($num == $dbNum + 1) { //增加
            if ($viewPoint == 'good') { //goodPlus
                Db::table('gnosis')->where('id', $id)->setInc('good');

                return 'goodPlus';
            } elseif ($viewPoint == 'bad') { //badPlus
                Db::table('gnosis')->where('id', $id)->setInc('bad');

                return 'badPlus';
            }

        } elseif ($num + 1 == $dbNum) { //减少
            if ($viewPoint == 'good') { //goodMinus
                Db::table('gnosis')->where('id', $id)->setDec('good');

                return 'goodMinus';
            } elseif ($viewPoint == 'bad') { //badMinus
                Db::table('gnosis')->where('id', $id)->setDec('bad');

                return 'badMinus';
            }
        }

        return 'viewPoint';
    }
}