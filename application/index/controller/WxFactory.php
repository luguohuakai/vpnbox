<?php
namespace app\index\controller;

// 微信总工厂
class WxFactory
{
    // 关注触发的事件
    public function subscribe($post_arr){
        $from = $post_arr['ToUserName'];
        $to = $post_arr['FromUserName'];
        $content = "终于把你等来了，还没绑定设备Mac吧，赶紧狠戳下面链接吧\r\n"
            . "<a href='" . url('/index/index/bindmac','',false,true) . "'>绑定设备</a>\r\n\r\n"
            . "什么？嫌麻烦，那就点击左下角的扫码绑定吧，我只能帮你到这里了。。。\r\n\r\n（悄悄告诉你：二维码在机身上哦）";

        return (new WxTpl())->replayText($from,$to,$content);
    }

    public function sendText($post_arr)
    {
        $from = $post_arr['ToUserName'];
        $to = $post_arr['FromUserName'];
        $msg = $post_arr['Content'];

        $content = '';
        switch ($msg){
            case '文字':
                $content = $msg;
                break;
        }

    }
}
