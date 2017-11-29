<?php
namespace app\index\controller;

require_once(EXTEND_PATH . 'wx/WxPayData.php');

use think\Db;
//use wx\CLogFileHandler;
use wx\JsApiPay;
//use wx\Log;
use wx\WxPayApi;
use wx\WxPayConfig;
use wx\WxPayException;
use wx\WxPayUnifiedOrder;

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
            $this->redirect(url('/index/index/bindmac'));
        }
    }

    // 发起支付请求统一下单
    public function payRequest(){
        $amount = input('get.amount') * 100;

//初始化日志
//        $logHandler= new CLogFileHandler("../logs/".date('Y-m-d').'.log');
//        $log = Log::Init($logHandler, 15);

//打印输出数组信息
//        function printf_info($data)
//        {
//            foreach($data as $key=>$value){
//                echo "<font color='#00ff55;'>$key</font> : $value <br/>";
//            }
//        }

//①、获取用户openid
        $tools = new JsApiPay();
        $openId = $tools->GetOpenid();

//②、统一下单
        $input = new WxPayUnifiedOrder();
        $input->SetBody("SrunBox Charge");
        $input->SetAttach("SrunBox");
        $out_trade_no = WxPayConfig::MCHID.date("YmdHis");
        $input->SetOut_trade_no($out_trade_no);
        $input->SetTotal_fee($amount);
//        $input->SetTotal_fee(1);
        $input->SetTime_start(date("YmdHis"));
        $input->SetTime_expire(date("YmdHis", time() + 600));
        $input->SetGoods_tag("BoxCharge");
        $input->SetNotify_url("http://wx.srun.com/index/pay/notifyurl");
        $input->SetTrade_type("JSAPI");
        $input->SetOpenid($openId);
        $order = WxPayApi::unifiedOrder($input);
//        echo '<font color="#f00"><b>统一下单支付单信息</b></font><br/>';
//        printf_info($order);
        $jsApiParameters = $tools->GetJsApiParameters($order);

//获取共享收货地址js函数参数
        $editAddress = $tools->GetEditAddressParameters();

//③、在支持成功回调通知中处理成功之后的事宜，见 notify.php
        /**WxPayConfig::MCHID.date("YmdHis")
         * 注意：
         * 1、当你的回调地址不可访问的时候，回调通知会失败，可以通过查询订单来确认支付是否成功
         * 2、jsapi支付时需要填入用户openid，WxPay.JsApiPay.php中有获取openid流程 （文档可以参考微信公众平台“网页授权接口”，
         * 参考http://mp.weixin.qq.com/wiki/17/c0f37d5704f0b64713d5d2c37b468d75.html）
         */
        $re['msg'] = '请求成功';
        $re['code'] = 200;
        $re['js_api_parameters'] = $jsApiParameters;
        $re['edit_address'] = $editAddress;

        return view('payrequest',['jsApiParameters' => $jsApiParameters,'editAddress' => $editAddress,'out_trade_no' => $out_trade_no]);
//        return json($re);
    }

    // 支付后返回页面 return_url
    public function returnurl(){
        return view('returnurl');
    }

    // 支付回调接口 notify_url
    public function notifyurl(){
        // 微信回调
        $notify = new Notify();
        $notify->Handle(false);
    }

    // test
    public function test(){
        L('dskjf','kdkd/lso');
        $aa = new WxPayException();
    }
}
