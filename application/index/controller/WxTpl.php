<?php
namespace app\index\controller;

class WxTpl
{
    // 1 回复文本消息
    public function replayText($from,$to,$content){
        $time = time();

        $xml = "
        <xml>

        <ToUserName><![CDATA[{$to}]]></ToUserName>
        
        <FromUserName><![CDATA[{$from}]]></FromUserName>
        
        <CreateTime>{$time}</CreateTime>
        
        <MsgType><![CDATA[text]]></MsgType>
        
        <Content><![CDATA[{$content}]]></Content>
        
        </xml>
        ";

        return $xml;
    }

    // 2 回复图片消息
    public function replayImage($from,$to,$media_id){
        $time = time();

        $xml = "
        <xml>
        <ToUserName><![CDATA[{$to}]]></ToUserName>
        <FromUserName><![CDATA[{$from}]]></FromUserName>
        <CreateTime>{$time}</CreateTime>
        <MsgType><![CDATA[image]]></MsgType>
        <Image>
        <MediaId><![CDATA[{$media_id}]]></MediaId>
        </Image>
        </xml>
        ";

        return $xml;
    }
    
    // 3 回复语音消息
    public function replayVoice($from,$to,$media_id){
        $time = time();

        $xml = "
        <xml>
        <ToUserName><![CDATA[{$to}]]></ToUserName>
        <FromUserName><![CDATA[{$from}]]></FromUserName>
        <CreateTime>{$time}</CreateTime>
        <MsgType><![CDATA[voice]]></MsgType>
        <Voice>
        <MediaId><![CDATA[{$media_id}]]></MediaId>
        </Voice>
        </xml>
        ";

        return $xml;
    }
    
    // 4 回复视频消息
    public function replayVideo($from,$to,$media_id,$title,$description){
        $time = time();

        $xml = "
        <xml>
        <ToUserName><![CDATA[{$to}]]></ToUserName>
        <FromUserName><![CDATA[{$from}]]></FromUserName>
        <CreateTime>{$time}</CreateTime>
        <MsgType><![CDATA[video]]></MsgType>
        <Video>
        <MediaId><![CDATA[{$media_id}]]></MediaId>
        <Title><![CDATA[{$title}]]></Title>
        <Description><![CDATA[{$description}]]></Description>
        </Video> 
        </xml>
        ";

        return $xml;
    }
    
    // 5 回复音乐消息
    public function replayMusic($from,$to,$media_id,$title,$description,$music_url,$hq_music_url){
        $time = time();

        $xml = "
        <xml>
        <ToUserName><![CDATA[{$to}]]></ToUserName>
        <FromUserName><![CDATA[{$from}]]></FromUserName>
        <CreateTime>{$time}</CreateTime>
        <MsgType><![CDATA[music]]></MsgType>
        <Music>
        <Title><![CDATA[{$title}]]></Title>
        <Description><![CDATA[{$description}]]></Description>
        <MusicUrl><![CDATA[{$music_url}]]></MusicUrl>
        <HQMusicUrl><![CDATA[{$hq_music_url}]]></HQMusicUrl>
        <ThumbMediaId><![CDATA[{$media_id}]]></ThumbMediaId>
        </Music>
        </xml>
        ";

        return $xml;
    }
    
    // 6 回复图文消息
    public function replayNews($from,$to,$articles){
        $time = time();
        $count = count($articles);
        $str = '';
        foreach ($articles as $article) {
            $str .= '
            <item>
            <Title><![CDATA['.$article['title'].']]></Title>
            <Description><![CDATA['.$article['description'].']]></Description>
            <PicUrl><![CDATA['.$article['picurl'].']]></PicUrl>
            <Url><![CDATA['.$article['url'].']]></Url>
            </item>
            ';
        }

        $xml = "
        <xml>
        <ToUserName><![CDATA[{$to}]]></ToUserName>
        <FromUserName><![CDATA[{$from}]]></FromUserName>
        <CreateTime>{$time}</CreateTime>
        <MsgType><![CDATA[news]]></MsgType>
        <ArticleCount>{$count}</ArticleCount>
        <Articles>{$str}</Articles>
        </xml>
        ";

        return $xml;
    }
    
}
