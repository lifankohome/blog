<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------

return [
    '__pattern__' => [
        'name' => '\w+',
        'id' => '\d+',
    ],
    '[hello]'     => [
        ':id'   => ['index/hello', ['method' => 'get'], ['id' => '\d+']],
        ':name' => ['index/hello', ['method' => 'post']],
    ],
    //静态地址路由
    'blog/intro' => 'blog/index/intro',
    'blog/[:id]' => 'blog/index/index',
    'blog/chMode' => 'blog/index/chMode',
    'blog/login' => 'login/index/index',
    'blog/logout' => 'login/index/logout',
    'blog/about' => 'blog/index/about',
    'blog/zone' => 'blog/index/zone',
    'blog/zone/[:tip]' => 'blog/index/zone',
    'blog/gnosis/[:id]' => 'blog/index/gnosis',
    'blog/rename' => 'blog/index/rename',
    'blog/errCode' => 'blog/index/errCode',
    'blog/service' => 'blog/index/service'
];
