<?php
namespace app\index\controller;

// 微信事件工厂 Event
class WxTextFactory
{
    private $post_arr = [];
    public $xml = '';

    public function __construct($post_arr)
    {
        $this->post_arr = $post_arr;
        $this->replay();
    }

    private function replay()
    {
        $content = $this->post_arr['Content'];
        switch ($content){
            case '1':
                $this->toXml('这是1');
                break;
            case '2':
                $this->toXml('这是2');
                break;
            default:
                $this->toXml($content);
                break;
        }
    }

    private function toXml($content){
        $from = $this->post_arr['ToUserName'];
        $to = $this->post_arr['FromUserName'];
        $this->xml = (new WxTpl())->replayText($from,$to,$content);
    }

}
