<?php
/**
 * Created by PhpStorm.
 * User: DM
 * Date: 2017/11/24
 * Time: 09:56
 */
namespace app\home\command;

use think\console\Command;
use think\console\Input;
use think\console\Output;

class StoreWxAccessToken extends Command
{
    protected function configure()
    {
        $this->setName('store_wx_access_token')->setDescription('crontab每一个小时获取一次微信token 存redis');
    }

    // crontab -e
    // * */1 * * * php /srun3/www/vpnbox/think store_wx_access_token
    protected function execute(Input $input, Output $output)
    {
        $domain = config('wx.domain');
        $app_id = config('wx.app_id');
        $app_secret = config('wx.app_secret');

        $api = "cgi-bin/token?grant_type=client_credential&appid=$app_id&secret=$app_secret";
        $url = $domain . $api;

        $rs = get($url);

        // 还是存redis算了
        if($rs){
            $rs = json_decode($rs,true);
            $rs['update_at'] = date('Y-m-d H:i:s');
            $rds = Rwx();
            $rds->set('weixin:token',json_encode($rs));
        }
        $output->writeln("执行成功");
    }
}