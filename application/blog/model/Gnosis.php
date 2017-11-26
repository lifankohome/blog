<?php
/**
 * Created by PhpStorm.
 * User: lifanko  lee
 * Date: 2017/10/30
 * Time: 13:18
 */

namespace app\blog\model;

use think\Db;
use think\Model;

class Gnosis extends Model
{
    protected $table = 'gnosis';

    public static function saveGnosis($id, $content, $length)
    {
        $res = Db::table('gnosis')
            ->where('id', $id)
            ->update([
                'body' => $content,
                'length' => $length,
                'saved' => time()
            ]);
        if($res){
            return "Saved: ".date("H:i:s",time());
        }else{
            return "保存失败，请重试！";
        }
    }
}