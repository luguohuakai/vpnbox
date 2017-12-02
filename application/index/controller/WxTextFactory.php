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
                $this->toXml($this->hello($content,$this->post_arr['FromUserName']));
                break;
        }
    }

    private function toXml($content){
        $from = $this->post_arr['ToUserName'];
        $to = $this->post_arr['FromUserName'];
        $this->xml = (new WxTpl())->replayText($from,$to,$content);
    }

    // 使用图灵机器人
    private function hello($content,$open_id){
        $api_key = '7a85575bf003474cbd5a41d03b35a848';

        $data['key'] = $api_key;
        $data['info'] = $content;
        $data['userid'] = str_replace(['_','-'],'',$open_id);
//        $data['loc'] = '北京';
        $param = http_build_query($data);
        $api_addr = 'http://www.tuling123.com/openapi/api?' . $param;

        $rs = get($api_addr);
        $rs = json_decode($rs,true);

        return $rs['text'];

    }

}
