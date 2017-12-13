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
        $vpn_setting = Db::table('vpn_setting')->where('username',session('user_name'))->find();
        return view('resetpassword',['wifi_name' => $vpn_setting['ssid'],'pass' => $vpn_setting['key']]);
    }

    // 重置密码页面
    public function infor(){
        return view('infor');
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
//        $rs = Db::table('users')->where('wx_open_id',session('wx_open_id'))->value('user_name');
//        if ($rs){
//            return view('index',['mac' => $rs]);
//        }else{
            return view('bindmac');
//        }
    }

    // 微信绑定设备mac操作
    public function bindMacHandle(){
        $mac =  input('post.mac');
        $password = input('post.pass');
        $method = input('post.method'); // 1网页 2扫码
        if ($method == 2){
            $password = ED(base64_decode($password),'D');
        }
        // 最先要确定此mac存在吗
        $exist = Db::table('users')->where('user_name',$mac)->value('user_id');
        if (!$exist){
            $this->error('此Box不存在');
        }
        // 密码是否正确
        $db_password = Db::table('users')->where('user_name',$mac)->value('user_password');
        if ($db_password != $password){
            $this->error('Mac或密码错误');
        }
        // 先看此Box之前是否被绑定过了
        $wx_open_id = Db::table('users')->where('user_name',$mac)->value('wx_open_id');
        // 再看我(微信)是否已绑定了Box
        $user_name = Db::table('users')->where('wx_open_id',session('wx_open_id'))->value('user_name');
        if($wx_open_id){
            if ($wx_open_id == session('wx_open_id')){
                $this->error('您已经绑定过这个Box了');
            }else{
                $this->error('此Box已经被别人绑定了');
            }
        }else{
            $open_id = session('wx_open_id') ? session('wx_open_id') : '';
            if ($open_id){
                // 去绑定
                try{
                    // 如果我已经绑定过 先解绑之前的Box
                    $flag = true;
                    Db::startTrans();
                    if($user_name){
                        $rs1 = Db::table('users')->where('wx_open_id',session('wx_open_id'))->setField('wx_open_id','');
                        if(!$rs1){
                            $flag = false;
                        }
                    }
                    if($flag){
                        $rs2 = Db::table('users')->where('user_name',$mac)->setField('wx_open_id',$open_id);
                        if ($rs2){
                            Db::commit();
                        }else{
                            $flag = false;
                            Db::rollback();
                        }
                    }else{
                        Db::rollback();
                    }
                }catch (Exception $e){
                    $this->error($e->getMessage());
                }
                if ($flag){
                    session('user_name',$mac);
                    $this->success('绑定成功<br>' . $mac,url('index',['mac' => $mac]));
                }else{
                    $this->error('绑定失败<br>' . $mac);
                }
            }else{
                $this->error('没有获取到open_id<br>' . $mac);
            }
        }
    }

    //
}
