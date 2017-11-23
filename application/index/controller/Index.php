<?php
namespace app\index\controller;

use think\Controller;
use think\Db;

class Index extends Controller
{
    public function index()
    {
//        return '<style type="text/css">*{ padding: 0; margin: 0; } .think_default_text{ padding: 4px 48px;} a{color:#2E5CD5;cursor: pointer;text-decoration: none} a:hover{text-decoration:underline; } body{ background: #fff; font-family: "Century Gothic","Microsoft yahei"; color: #333;font-size:18px} h1{ font-size: 100px; font-weight: normal; margin-bottom: 12px; } p{ line-height: 1.6em; font-size: 42px }</style><div style="padding: 24px 48px;"> <h1>:)</h1><p> ThinkPHP V5<br/><span style="font-size:30px">十年磨一剑 - 为API开发设计的高性能框架</span></p><span style="font-size:22px;">[ V5.0 版本由 <a href="http://www.qiniu.com" target="qiniu">七牛云</a> 独家赞助发布 ]</span></div><script type="text/javascript" src="http://tajs.qq.com/stats?sId=9347272" charset="UTF-8"></script><script type="text/javascript" src="http://ad.topthink.com/Public/static/client.js"></script><thinkad id="ad_bd568ce7058a1091"></thinkad>';
        return json(['code' => 200, 'msg' => '获取成功']);
    }

    // 微信token验证
    public function checkSignature(){
        // 验证一次基本就不用了

//        $url = 'http://47.104.1.91/index/index/checksignature'; // 正式服
        $url = 'http://39.104.51.121/index/index/checksignature'; // 测试服
        $token = '5rrwbnib5dzss3tf9kn97s1rijszcwff';
        $EncodingAESKey = 'n7XdcDFZWsM8XUr8MvdTAsTwq9zoZbAXPEGyyPSxQgM';

//        file_put_contents('a.txt',input('get.timestamp'));

        $timestamp = input('get.timestamp');
        $nonce = input('get.nonce');
        $arr = [$token,$timestamp,$nonce];
        sort($arr,SORT_STRING);
        $local_sign = sha1(implode($arr));
        if ($local_sign == input('get.signature')){
            exit(input('get.echostr'));
        }else{
            exit('验证失败');
        }
    }

    // 定时获取并存储微信token 每一个半小时获取一次 存入setting中
    // crontab -e
    // */30 1 * * *
    public function storeWxAccessToken(){
        $domain = config('wx.domain');
        $app_id = config('wx.app_id');
        $app_secret = config('wx.app_secret');

        $api = "cgi-bin/token?grant_type=client_credential&$app_id=APPID&secret=$app_secret";
        $url = $domain . $api;

        $rs = get($url);

        dd($rs);
        dd($domain);
    }

    // 连接数据库
    public function testDb(){
        $users = Db::table('setting')->where('key','weixin_access_token')->value('value');

        dump($users);
    }
}
