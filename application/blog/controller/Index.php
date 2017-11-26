<?php
namespace app\blog\controller;

use app\blog\model\Comment;
use app\blog\model\Gnosis;
use app\blog\model\Passage;
use app\blog\model\Zone;
use think\Controller;
use think\Image;
use think\Maxim;
use think\Request;
use think\Session;

class Index extends Controller
{
    private $app = '&copy; 2016-2017 深度好文博客 Power by <a href="http://hpu.lifanko.cn">lifanko</a>';

    public function index($id = '')
    {
        if (empty(Session::get('view'))) {
            $mode = "dark";
            Session::set('view', 'dark');
            Session::set('trigger', true);
        } else {
            $mode = Session::get('view');
            Session::set('trigger', false);
        }
        $this->assign('mode', $mode);
        $this->assign('trigger', Session::get('trigger'));

        $this->assign('username', Session::get('username'));
        $this->assign('date', Session::get('date'));

        //文章目录
        $contents = Passage::showList();
        $this->assign('contents', $contents);

        //文章内容
        if (!empty($id) && is_numeric($id)) {
            $content = Passage::showContent($id);
            $comment = Comment::show($id);
        } else {
            $content = 'default';
            $comment = 'default';
        }
        $this->assign(['id' => $id, 'content' => $content, 'comment' => $comment]);

        //版权信息
        $this->assign('app', $this->app);

//        print_r($comment);

        return $this->fetch();
    }

    public function chMode()
    {
        if (Session::get('view') == 'dark') {
            Session::set('view', 'bright');
        } else {
            Session::set('view', 'dark');
        }
        return "<script>history.go(-1);</script>";
    }

    public function service(Request $request)
    {
        $result = "fail";
        $option = $request->get('option');
        if (!empty($option)) {
            switch ($option) {
                case 'viewPoint':
                    $id = $request->get('contentId');
                    $viewPoint = $request->get('viewPoint');
                    $num = $request->get('num');

                    $result = Passage::viewPoint($id, $viewPoint, $num);
                    break;
                case 'comment':
                    $result = Comment::saveComment($request->get('uuid'), $request->get('title'), $request->get('comment'), $request->get('name'));
                    break;
                case 'save':
                    $result = Gnosis::saveGnosis($request->post('id'), $request->post('textContent'), $request->post('length'));
                    break;
                case 'rename':
                    if ($request->get('option') == 'rename') {
                        $result = Zone::rename($request->get('id'), $request->get('newName'), $request->get('times'));
                    }
                    break;
                case 'release':
                    if ($request->get('option') == 'release') {
                        $result = Zone::release($request->get('id'), Session::get('username'));
                    }
                    break;
                case 'newPass':
                    if ($request->get('option') == 'newPass') {
                        $username = $request->get('u');
                        $newPass = $request->get('newPass');
                        if (strlen($newPass) >= 8 && $username == Session::get('username')) {
                            $password = substr(md5($newPass) . time(), 4, 32);
                            $result = Zone::newPass($request->get('u'), $password, time());
                        }
                    }
                    break;
                case 'withdraw':
                    if ($request->get('option') == 'withdraw') {
                        $result = Zone::withdraw($request->get('id'), Session::get('username'));
                    }
                    break;
                case 'delete':
                    if ($request->get('option') == 'delete') {
                        $result = Zone::del($request->get('id'), Session::get('username'));
                    }
                    break;
                case 'imgUpload':
                    if ($request->get('option') == 'imgUpload') {
                        if ($_FILES["fileData"]["size"] < 2048000) {
                            $type = $_FILES["fileData"]["type"];
                            if (($type == "image/jpeg") || ($type == "image/png") || ($type == "image/gif") || ($type == "image/x-ms-bmp")) {
                                if (!$_FILES["fileData"]["error"]) {
                                    $new_file = "static/blog/imgUpload/" . date('Ymd', time()) . "/";
                                    if (!file_exists($new_file)) {
                                        mkdir($new_file, 0700, true);     //检查是否有该文件夹，如果没有就创建并给予权限
                                    }
                                    $new_file = $new_file . time() . "." . substr($type, 6);

                                    if (move_uploaded_file($_FILES["fileData"]["tmp_name"], $new_file)) {
                                        //添加水印
                                        $author = Session::get('username');
                                        $image = Image::open($new_file);
                                        $image->text($author . '-深度好文', './static/font/boleyaya.ttf', 12, 'auto', $image::WATER_SOUTHEAST, [-10, -5])->save($new_file);

                                        $array = ["success" => true, "file_path" => "/$new_file"];
                                    } else {
                                        $array = ["success" => false, "msg" => "拉取图片失败，请重试！"];
                                    }
                                } else {
                                    $array = ["success" => false, "msg" => "上传遇到问题，请重试！"];
                                }
                            } else {
                                $array = ["success" => false, "msg" => "请重新上传——图片格式应为：jpg/png/gif/bmp"];
                            }
                        } else {
                            $array = ["success" => false, "msg" => "请重新上传——图片大小不能大于2M"];
                        }
                        $result = json_encode($array);
                    }
                    break;
            }
        }

        return $result;
    }

    public function intro()
    {
        return view();
    }

    public function about()
    {
        return view();
    }

    public function zone($tip = '')
    {
        $username = Session::get('username');
        $date = Session::get('date');

        if (!empty($username) && !empty($date)) {
            $this->assign('username', $username);
            $this->assign('date', $date);
        } else {
            //在此处重定向
            die('<span style="font-size: 35px">身份验证失败<span style="font-size: 14px;color: #777">　Code::21345</span></span><br>请求源无法确定，需要重新进行<a href="/blog/login" style="color: #00A2D4">登录验证</a>，<span id="timer">5</span>s后自动跳转...
                <script>var total = 5;setInterval(function () {document.getElementById("timer").innerText = --total;if (!total) {window.location = "/blog/login";}}, 1000);</script>');
        }

        $passageGnosis = Zone::passageGnosis($username);
        $passageGnosised = Zone::passageGnosised($username);
        $passageGnosising = Zone::passageGnosising($username);

        $this->assign('passageGnosisTable', $passageGnosis['passage']);

        $this->assign('passageGnosisNum', $passageGnosis['num']);
        $this->assign('passageGnosisLength', $passageGnosis['length']);
        $this->assign('passageGnosisRead', $passageGnosis['read']);
        $this->assign('passageGnosisGood', $passageGnosis['good']);

        $this->assign('passageGnosisedTable', $passageGnosised);
        $this->assign('passageGnosisingTable', $passageGnosising);

        $this->assign('tip', $tip);

        //版权信息
        $this->assign('app', $this->app);

        return view();
    }

    public function gnosis($id = '')
    {
        if ($this->checkUser($id)) {
            $gnosis = Gnosis::get(['id' => $id]);

            $this->assign('id', $gnosis['id']);
            $this->assign('body', $gnosis['body']);
            $this->assign('title', $gnosis['head']);
            $this->assign('author', $gnosis['name']);

            //Maxim
            $this->assign('maxim', Maxim::get());

            //版权信息
            $this->assign('app', $this->app);

            return view();
        } else {
            return '<span style="font-size: 35px">身份验证失败<span style="font-size: 14px;color: #777">　Code::21555</span></span><br>请求源无法确定，需要重新进行<a href="/blog/login" style="color: #00A2D4">登录验证</a>，<span id="timer">5</span>s后自动跳转...
                <script>var total = 5;setInterval(function () {document.getElementById("timer").innerText = --total;if (!total) {window.location = "/blog/login";}}, 1000);</script>';
        }
    }

    public function newGnosis(Request $request)
    {
        $author = $request->post('author');
        $title = $request->post('titleText');
        if ($author == Session::get('username')) {
            if (!empty(trim($title))) {

                if (empty(Gnosis::get(['head' => $title, 'name' => $author]))) {
                    $gnosis = new Gnosis([
                        'name' => $author,
                        'head' => $title,
                        'date' => date('Y/m/d H:i', time())
                    ]);
                    $gnosis->save();

                    $id = $gnosis->getLastInsID();  //获取插入id

                    return "<script>window.location.href='/blog/gnosis/$id'</script>";
                } else {
                    return '<span style="font-size: 35px">新建文章失败<span style="font-size: 14px;color: #777">　Code::11555</span></span><br>你已拥有相同题目的文章，博客平台限制文章重复，
                    <span id="timer">5</span>s后自动<a href="/blog" style="color: #00A2D4">返回首页</a>
                    <script>var total = 5;setInterval(function () {document.getElementById("timer").innerText = --total;if (!total) {window.location = "/blog";}}, 1000);</script>';
                }
            }
        }
        return '<span style="font-size: 35px">身份验证失败<span style="font-size: 14px;color: #777">　Code::21344</span></span><br>请求源无法确定，需要重新进行<a href="/blog/login" style="color: #00A2D4">登录验证</a>，<span id="timer">5</span>s后自动跳转...
                <script>var total = 5;setInterval(function () {document.getElementById("timer").innerText = --total;if (!total) {window.location = "/blog/login";}}, 1000);</script>';
    }

    public function rename(Request $request)
    {
        $id = $request->get('id');

        if ($this->checkUser($id)) {
            $passage = Zone::get(['id' => $id]);
            if ($passage['status'] == 'gnosis') {
                $reEdit = $passage['reEdit'] - 1;
            } else {
                $reEdit = 3;
            }

            $dom = "<!DOCTYPE html><html lang='en'><head><meta charset='UTF-8'><title>修改文章标题</title></head><body>
                    <div class='modal-header'>
                    <button type='button' class='close' data-dismiss='modal'><span aria-hidden='true'>&times;</span><span class='sr-only'>Close</span></button>
                    <h4 class='modal-title' id='myModalLabel'>修改文章题目：</h4></div>
                    <form action='/blog/service' method='get'><div class='modal-body'><h4>作者：{$passage['name']}</h4>
                    <input type='hidden' name='option' value='rename'>
                    <input type='hidden' name='id' value='$id'>
                    <input type='hidden' name='times' value='$reEdit'>
                    <h4>新标题：<input name='newName' style='width: 80%' placeholder='{$passage['head']}' title='请输入新的文章标题' type='text' autocomplete='off' required='required'></h4>
                    </div><div class='modal-footer'><button type='submit' class='btn btn-sm btn-info'>确认修改</button></div>
                    </form></body></html>";
        } else {
            $dom = "<!DOCTYPE html><html lang='en'><head><meta charset='UTF-8'><title>修改文章标题</title></head><body>
                    <div class='modal-header'>
                    <button type='button' class='close' data-dismiss='modal'><span aria-hidden='true'>&times;</span><span class='sr-only'>Close</span></button>
                    <h4 class='modal-title' id='myModalLabel'>修改文章题目：</h4></div><div style='padding: 1pc 1pc 2pc 1pc'>
                    <span style='font-size: 35px'>身份验证失败<span style='font-size: 14px;color: #777'>　Code::21445</span></span><br>请求源无法确定，需要重新进行<a href='/blog/login' style='color: #00A2D4'>登录验证</a>，<span id='timer'>5</span>s后自动跳转...
                    <script>var total = 5;setInterval(function () {document.getElementById('timer').innerText = --total;if (!total) {window.location = '/blog/login';}}, 1000);</script>
                    </div></body></html>";
        }
        echo $dom;
    }

    public function newPass(Request $request)
    {
        $username = $request->get('u');

        if ($this->checkUser($username, 'name')) {
            $passage = Zone::get(['name' => $username]);

            $dom = "<!DOCTYPE html><html lang='en'><head><meta charset='UTF-8'><title>修改密码</title></head><body>
                    <div class='modal-header'>
                    <button type='button' class='close' data-dismiss='modal'><span aria-hidden='true'>&times;</span><span class='sr-only'>Close</span></button>
                    <h4 class='modal-title' id='myModalLabel'>修改密码：</h4></div>
                    <form action='/blog/service' method='get'><div class='modal-body'>
                    <h4>新的密码：<input id='p1' style='width: 80%' placeholder='长度限制：8-16' autocomplete='off' maxlength='16' type='password' required='required'></h4>
                    <input type='hidden' name='option' value='newPass'>
                    <input type='hidden' name='u' value='{$passage['name']}'>
                    <h4>确认密码：<input id='p2' name='newPass' style='width: 80%' placeholder='长度限制：8-16' autocomplete='off' maxlength='16' type='password' required='required' disabled></h4>
                    </div><div class='modal-footer'><button type='submit' id='btnPass' class='btn btn-sm disabled'>确认修改</button></div>
                    </form>
                    <script>
                    $('#p1').on('keyup',function() {
                        if($('#p1').val().length>=8){
                            $('#p2').removeAttr('disabled');
                        }else{
                            $('#p2').attr('disabled',true);
                        }
                    });
                    $('#p2').on('keyup',function() {
                        if($('#p2').val()==$('#p1').val()){
                            $('#btnPass').removeClass('disabled').addClass('btn-info')
                        }else if($('#btnPass').hasClass('btn-info')){
                            $('#btnPass').removeClass('btn-info')
                        }
                    })</script>
                    </body></html>";
        } else {
            $dom = "<!DOCTYPE html><html lang='en'><head><meta charset='UTF-8'><title>修改密码</title></head><body>
                    <div class='modal-header'>
                    <button type='button' class='close' data-dismiss='modal'><span aria-hidden='true'>&times;</span><span class='sr-only'>Close</span></button>
                    <h4 class='modal-title' id='myModalLabel'>修改密码：</h4></div><div style='padding: 1pc 1pc 2pc 1pc'>
                    <span style='font-size: 35px'>身份验证失败<span style='font-size: 14px;color: #777'>　Code::21445</span></span><br>请求源无法确定，需要重新进行<a href='/blog/login' style='color: #00A2D4'>登录验证</a>，<span id='timer'>5</span>s后自动跳转...
                    <script>var total = 5;setInterval(function () {document.getElementById('timer').innerText = --total;if (!total) {window.location = '/blog/login';}}, 1000);</script>
                    </div></body></html>";
        }
        echo $dom;
    }

    private function checkUser($val, $field = 'id')
    {
        if (!empty($val)) {
            $passage = Passage::get([$field => $val]);

            $username = Session::get('username');

            if (!empty($passage) && $passage[$field] == $val && $passage['name'] == $username) {
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    public function errCode()
    {
        //版权信息
        $this->assign('app', $this->app);

        return view();
    }
}
