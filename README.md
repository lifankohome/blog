## 深度好文博客 Base On ThinkPHP5

### 安装：

 + 获取TP5源代码
 
 + 将代码文件对应增量复制进TP5框架
 
 + 获取simditor（2.3.6）源代码 + Emoji 插件代码，并将其复制进以下文件夹：
````
/public/static/blog/simditor
````

 + 将 /Application/config.php 中：
```
'url_param_type' => 0 改为：'url_param_type' => 1
```

 + 安装图像处理依赖项(如果在/vendor/中库已存在可省略)：
```
composer require topthink/think-image
```

### 数据库（MySQL）：

博客共涉及一个数据库（lifanko），三张数据表（blog、comment、gnosis），blog用于保存用户信息，comment用于保存评论，gnosis用于保存博客。

 + blog字段：id（自增主键），username（varchar，16，默认无），password（varchar，48，默认无），date(varchar，16，默认无)。

 + comment字段：id(自增主键)，uuid（varchar，8，默认无），title（varchar，64，默认无），comment（varchar，4096，默认无）。

 + gnosis字段：id（自增主键），name（varchar，8，默认无），head（varchar，32，默认无），length（varchar，8，默认0），saved（varchar，32，默认无），date（varchar，32，默认无），good（varchar，8，默认0），bad（varchar，8，默认0），status（varchar，16，默认gnosising），time（varchar，32，默认无），times（varchar，8，默认0），reEdit（varchar，1，默认3）。

### 目录结构
```
www  WEB部署目录（或者子目录）
├─application           应用目录
│  ├─blog               博客主目录
│  │  ├─controller      控制器目录
│  │  ├─model           模型目录
│  │  └─view            视图目录
│  ├─login              登录主目录
│  │  ├─controller      控制器目录
│  │  ├─model           模型目录
│  │  └─view            视图目录
│  └─route.php          路由配置文件
│
├─public                WEB目录（对外访问目录）
│  ├─blog               静态文件
│  │  ├─Logo            评论头像目录
│  │  └─simditor        富文本编辑器目录
│  ├─common             通用静态文件
│  ├─font               字体文件
│  └─login              登录静态文件
│  │  └─img             登录界面所需图片
│
├─thinkphp              框架系统目录
│  ├─library            框架类库目录
│  │  └─think           Think类库包目录
│  │  │  └─Maxim.php    箴言爬虫
│
├─vendor                框架系统目录
│  ├─topthink           框架类库目录
│  │  └─think-image     图片水印类库目录
│
├─LICENSE               授权说明文件
└─README.md             README 文件
```

### 介绍：

 + 基于Bootstrap3设计，响应式布局，性能稳定，部署简单。
 + 编辑器自动保存默认开启，关闭方法：把blog视图文件夹中的gnosis.html中第172行：autoSave(true)改为autoSave(false)或直接将其注释。
 + 编辑器默认开启图片作者水印，上传的图片会在右下角生成一个格式为【作者-深度好文】、12像素大小、自动颜色的水印，修改方法：blog控制器文件夹的Index.php中第131行：
 
```
$image->text($author . '-深度好文', './static/font/boleyaya.ttf', 12, 'auto', $image::WATER_SOUTHEAST, [-10, -5])->save($new_file);
```

### 提示&注意：

 + 博客无法在移动端登录
 
 + 谨慎更改登录验证逻辑，因为其贯穿整个博客系统
 
 + 评论系统使用base64编码将评论相关的json格式数据保存于comment字段

### 演示地址：

我的博客：http://hpu.lifanko.cn/blog

### Demo：

![布洛格截图](https://raw.githubusercontent.com/lifankohome/blog/master/screenshot.jpg)