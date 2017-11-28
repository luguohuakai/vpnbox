<?php
namespace app\index\controller;

use wx\WxPayApi;
use wx\WxPayNotify;
use wx\WxPayOrderQuery;

class Notify extends WxPayNotify
{
    //查询订单
    public function Queryorder($transaction_id)
    {
        $input = new WxPayOrderQuery();
        $input->SetTransaction_id($transaction_id);
        $result = WxPayApi::orderQuery($input);
//        Log::DEBUG("query:" . json_encode($result));
        if(array_key_exists("return_code", $result)
            && array_key_exists("result_code", $result)
            && $result["return_code"] == "SUCCESS"
            && $result["result_code"] == "SUCCESS")
        {
            return true;
        }
        return false;
    }

    //重写回调处理函数
    public function NotifyProcess($data, &$msg)
    {
//        Log::DEBUG("call back:" . json_encode($data));
        $notfiyOutput = array();

        if(!array_key_exists("transaction_id", $data)){
            $msg = "输入参数不正确";
            return false;
        }
        //查询订单，判断订单真实性
        if(!$this->Queryorder($data["transaction_id"])){
            $msg = "订单查询失败";
            return false;
        }

        // 接下来商户处理自己的业务逻辑
        L(json_encode($data),'aaa/bbb');

        // 缴费到产品
        // 写Alipay表
        // 写缴费记录
        // 写操作日志

        return true;
    }

    public function notify(){
        $notify = new self();
        $notify->Handle(false);
    }
}
