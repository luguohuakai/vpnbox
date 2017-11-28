<?php
namespace app\index\controller;

use think\Db;
use think\Exception;

class Pay extends Base
{
    // 支付页面
    public function index()
    {
        // 先看是否已经绑定
        $rs = Db::table('users')->where('wx_open_id',session('wx_open_id'))->value('user_name');
        if ($rs){
            return view('index',['mac' => $rs]);
        }else{
            $this->redirect('/index/index/bindmac');
        }
    }

    // 支付后返回页面 return_url
    public function Returnurl(){

    }

    // 支付回调接口 notify_url
    public function Notifyurl(){

    }
}
