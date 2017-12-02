<?php
namespace app\index\controller;

// 微信事件工厂 Event
class WxEventFactory
{
    // 微信事件列表 Event
    private $wx_event_lists = [
        'subscribe', // 订阅
        'unsubscribe', // 取消订阅
        'scancode_push', // 扫码二维码推送
        'SCAN', // 扫码事件
        'LOCATION', // 位置上报事件
        'CLICK', // 点击菜单事件
        'VIEW', // 点击菜单跳转事件
    ];
    private $post_arr = [];
    public $xml;

    public function __construct($post_arr)
    {
        $this->post_arr = $post_arr;
        $event = $post_arr['Event'];
        $this->$event();
    }

    // 订阅
    private function subscribe(){
        $from = $this->post_arr['ToUserName'];
        $to = $this->post_arr['FromUserName'];
        $content = "终于把你等来了，还没绑定设备Mac吧，赶紧狠戳下面链接吧\r\n"
            . "<a href='" . url('/index/index/bindmac','',false,true) . "'>绑定设备</a>\r\n\r\n"
            . "什么？嫌麻烦，那就点击左下角的扫码绑定吧，我只能帮你到这里了。。。\r\n\r\n（悄悄告诉你：二维码在机身上哦）";

        $this->xml = (new WxTpl())->replayText($from,$to,$content);
    }

    // 取消订阅
    public function unsubscribe(){
        return false;
    }

    // 扫码二维码推送
    public function scancode_push(){
        return false;
    }

    // 扫码事件
    public function SCAN(){
        return false;
    }

    // 位置上报事件
    public function LOCATION(){
        return false;
    }

    // 点击菜单事件
    public function CLICK(){
        return false;
    }

    // 点击菜单跳转事件
    public function VIEW(){
        return false;
    }
}
