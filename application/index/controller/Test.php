<?php
namespace app\index\controller;

use think\Db;
use think\Exception;

class Test extends Base
{
    public function index()
    {
//        return '<style type="text/css">*{ padding: 0; margin: 0; } .think_default_text{ padding: 4px 48px;} a{color:#2E5CD5;cursor: pointer;text-decoration: none} a:hover{text-decoration:underline; } body{ background: #fff; font-family: "Century Gothic","Microsoft yahei"; color: #333;font-size:18px} h1{ font-size: 100px; font-weight: normal; margin-bottom: 12px; } p{ line-height: 1.6em; font-size: 42px }</style><div style="padding: 24px 48px;"> <h1>:)</h1><p> ThinkPHP V5<br/><span style="font-size:30px">十年磨一剑 - 为API开发设计的高性能框架</span></p><span style="font-size:22px;">[ V5.0 版本由 <a href="http://www.qiniu.com" target="qiniu">七牛云</a> 独家赞助发布 ]</span></div><script type="text/javascript" src="http://tajs.qq.com/stats?sId=9347272" charset="UTF-8"></script><script type="text/javascript" src="http://ad.topthink.com/Public/static/client.js"></script><thinkad id="ad_bd568ce7058a1091"></thinkad>';
        return json(['code' => 200, 'msg' => '获取成功']);
    }

    // 微信token验证
    public function checkSignature(){
        // 微信post的xml数据
        $post_xml = $GLOBALS["HTTP_RAW_POST_DATA"];
        $post_arr = json_decode(json_encode(simplexml_load_string($post_xml, 'SimpleXMLElement', LIBXML_NOCDATA)), true);

        // 关注事件
        if($post_arr['Event'] == 'subscribe'){
            // 发送消息给用户 邀请绑定

        }






//        L(json_encode($post_arr),'cc/dd');
        // 验证一次基本就不用了

//        $url = 'http://47.104.1.91/index/index/checksignature'; // 正式服
//        $url = 'http://wx.srun.com/index/index/checksignature'; // 正式服
//        $url = 'http://39.104.51.121/index/index/checksignature'; // 测试服
        $url = 'http://test.srun.com/index/index/checksignature'; // 测试服
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

    // 设置微信菜单
    public function setMenu(){
        $rs = Rwx()->get('weixin:token');
        $rs = json_decode($rs,true);
        $token = $rs['access_token'];
        $app_id = config('wx.app_id');
        $wx_domain = config('wx.domain');

        $menu = [
            'button' => [
                [
                    'name' => '绑定设备',
                    'sub_button' => [
                        [
                            'type' => 'scancode_push',
                            'name' => '扫码绑定',
                            'key' => 'rselfmenu_0_1',
                        ],
                        [
                            'type' => 'view',
                            'name' => '手动绑定',
//                            'url' => 'http://test.srun.com/index/index/bindmac',
                            'url' => url('/index/index/bindmac','',false,true),
                        ],
                    ],
                ],
                [
                    'name' => '我的设备',
                    'sub_button' => [
                        [
                            'type' => 'view',
                            'name' => '查看设备',
//                            'url' => 'http://47.104.1.91/index.php?mac=78-A3-51-15-DD-38',
                            'url' => url('/index/index/index','',false,true),
                        ],
                        [
                            'type' => 'view',
                            'name' => 'Box充值',
                            'url' => url('/index/pay/index','',false,true),
                        ],
//                        [
//                            'type' => 'view',
//                            'name' => '查看session',
//                            'url' => url('/index/test/sessionlist','',false,true),
//                        ],
//                        [
//                            'type' => 'view',
//                            'name' => '清除session',
//                            'url' => url('/index/test/clearsession','',false,true),
//                        ],
                    ]
                ],
                [
                    'name' => '设置',
                    'type' => 'view',
                    'url' => url('/index/index/settings','',false,true),
                ]
            ]
        ];
        $menu_json = json_encode($menu,JSON_UNESCAPED_UNICODE);

        $api = "cgi-bin/menu/create?access_token=$token";

        $res = post($wx_domain . $api,$menu_json);
        dump($res);
        dd($menu);
    }

    // 清除session
    public function clearSession(){
        dump(session(null));
    }
    // 查看session
    public function sessionList(){
        dump(session(''));
    }

    // 生成二维码链接
    public function getQrUrl(){
        $mac = input('mac');
        $data['mac'] = $mac;
        $pass = Db::table('users')->where('user_name',$mac)->value('user_password');
        if (!$pass){
            return json('输入有误');
        }
        $data['code'] = ED($pass,'E');
        $data['method'] = 2; // 1手动绑定 2扫码绑定
        $url = url('/index/index/bindmac',$data,true,true);
        echo "<textarea id=\"\" cols=\"100\" rows=\"10\">{$url}</textarea>";exit;
        return json($url);
    }

    // 连接数据库
    public function testDb(){
        $users = Db::table('setting')->where('key','weixin_access_token')->value('value');

        dump($users);
    }
}
