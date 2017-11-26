<?php
namespace app\index\controller;

use think\Db;
use think\Exception;

class Index extends Base
{
    // 我的设备首页
    public function index()
    {
        // 先看是否已经绑定
        $rs = Db::table('users')->where('wx_open_id',session('wx_open_id'))->value('user_name');
        if ($rs){
            return view('index',['mac' => $rs]);
        }else{
            return view('bindmac');
        }
    }

    // 支付页面
    public function payWay(){
        // 先看是否已经绑定
        $rs = Db::table('users')->where('wx_open_id',session('wx_open_id'))->value('user_name');
        if ($rs){
            return view('payway',['mac' => $rs]);
        }else{
            return view('bindmac');
        }
    }

    // 设置页面
    public function settings(){
        // 先看是否已经绑定
        $rs = Db::table('users')->where('wx_open_id',session('wx_open_id'))->value('user_name');
        if ($rs){
            return view('settings',['mac' => $rs]);
        }else{
            return view('bindmac');
        }
    }

    // 重置密码页面
    public function resetpassword(){
        return view('resetpassword');
    }
    // 设定使用期限页面
    public function setstoptime(){
        return view('setstoptime');
    }
    // 切换产品页面
    public function changeproduct(){
        return view('changeproduct');
    }
    // 消费统计页面
    public function consumption(){
        return view('consumption');
    }
    // 使用统计页面
    public function used(){
        return view('used');
    }

    // 微信绑定设备mac页面
    public function bindMac(){
        // 先看是否已经绑定
        $rs = Db::table('users')->where('wx_open_id',session('wx_open_id'))->value('user_name');
        if ($rs){
            return view('index',['mac' => $rs]);
        }else{
            return view('bindmac');
        }
    }

    // 微信绑定设备mac操作
    public function bindMacHandle(){
        $mac =  input('post.mac');
        // 先看之前是否绑定过了
        $wx_open_id = Db::table('users')->where('user_name',$mac)->value('wx_open_id');
        if($wx_open_id){
            if ($wx_open_id == session('wx_open_id')){
                $this->error('您已经绑定过了');
            }else{
                $this->error('此Box已经绑定过了');
            }
        }else{
            $open_id = session('wx_open_id') ? session('wx_open_id') : '';
            if ($open_id){
                // 去绑定
                try{
                    $rs = Db::table('users')->where('user_name',$mac)->setField('wx_open_id',$open_id);
                }catch (Exception $e){
                    $this->error($e->getMessage());
                }
                if ($rs){
                    $this->success('绑定成功' . $mac,url('index',['mac' => $mac]));
                }else{
                    $this->error('绑定失败' . $mac);
                }
            }else{
                $this->error('没有获取到open_id' . $mac);
            }
        }
    }

    //
}
