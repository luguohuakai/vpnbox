<?php
namespace app\index\controller;


require_once(EXTEND_PATH . 'wx/WxPayData.php');
require_once(EXTEND_PATH . 'wx/WxPayApi.php');

use think\Db;
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
//        $data['appid'] = 'wx3af475e968290a3e';
//        $data['attach'] = 'test';
//        $data['bank_type'] = 'CFT';
//        $data['cash_fee'] = '1';
//        $data['fee_type'] = 'CNY';
//        $data['is_subscribe'] = 'Y';
//        $data['mch_id'] = '1267761301';
//        $data['nonce_str'] = 'nr0x75ttsp42bqhf4114q3noksdky0rw';
//        $data['openid'] = 'oh53Pv9d__PlzIvbyJx-TujSqVig';
//        $data['out_trade_no'] = '126776130120171129122132';
//        $data['result_code'] = 'SUCCESS';
//        $data['return_code'] = 'SUCCESS';
//        $data['sign'] = 'DA72871C75D46B427AB833FC7468BCD1';
//        $data['time_end'] = '20171129122144';
//        $data['total_fee'] = '1';
//        $data['trade_type'] = 'JSAPI';
//        $data['transaction_id'] = '4200000020201711297792273350';
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
        L(json_encode($data),'pay/notify');
        // 输出信息:{
        //"appid":"wx3af475e968290a3e",
        //"attach":"test",
        //"bank_type":"CFT",
        //"cash_fee":"1",
        //"fee_type":"CNY",
        //"is_subscribe":"Y",
        //"mch_id":"1267761301",
        //"nonce_str":"nr0x75ttsp42bqhf4114q3noksdky0rw",
        //"openid":"oh53Pv9d__PlzIvbyJx-TujSqVig",
        //"out_trade_no":"126776130120171129122132",
        //"result_code":"SUCCESS",
        //"return_code":"SUCCESS",
        //"sign":"DA72871C75D46B427AB833FC7468BCD1",
        //"time_end":"20171129122144",
        //"total_fee":"1",
        //"trade_type":"JSAPI",
        //"transaction_id":"4200000020201711297792273350"
        //}

        // 获取用户信息
        $open_id = $data['openid'];
        $users = Db::table('users')->where('wx_open_id',$open_id)->find();
        if (!$users){
            $msg = "用户不存在";
            return false;
        }

        $rds = Rwx(0);
        // 获取正在使用的产品id
        $product_id = $rds->hGet('hash:users:' . $users['user_id'],'products_id');

        // 缴费到产品
        $key = 'hash:users:products:' . $users['user_id'] . ':' . $product_id;
        $user_balance = $rds->hGet($key,'user_balance');
        $rs = $rds->hSet($key,'user_balance',$data['total_fee'] / 100 + $user_balance);

        // 写Alipay表
        $alipay_data = [
            'user_name' => $users['user_name'],
            'out_trade_no' => $data['out_trade_no'],
            'money' => $data['total_fee'] / 100,
            'type' => $product_id,
            'buy_time' => time(),
            'status' => '2',
            'payment' => '0',
            'trade_no' => $data['transaction_id'],
            'pay_type' => '1',
            'remark' => '用户通过微信公众号支付',
        ];
//        if ($rs){
            Db::table('alipay')->insert($alipay_data);
//        }
        // 写缴费记录
        $pay_list_data = [
            'user_name' => $users['user_name'],
            'user_real_name' => $users['user_real_name'],
            'pay_num' => $data['total_fee'] / 100,
            'balance_pre' => $users['balance'],
            'type' => '1',
            'pay_type_id' => '7',
            'product_id' => $product_id,
            'package_id' => '0',
            'extra_pay_id' => '0',
            'order_no' => $data['out_trade_no'],
            'create_at' => time(),
            'mgr_name' => $data['openid'],
            'bill_number' => date('YmdHis' . time()),
            'print_num' => '0',
            'is_refund' => '0',
            'gift' => '0',
            'group_id' => $users['group_id'],
            'user_id' => $users['user_id'],
        ];
            Db::table('pay_list')->insert($pay_list_data);
        // 写操作日志
        $log_operate_data = [
            'operator' => $data['openid'],
            'user_id' => $users['user_id'],
            'target' => $users['user_name'],
            'action' => 'pay',
            'action_type' => 'Financial Pay',
            'content' => '微信用户:' . $data['openid'] . ',给账号:' . $users['user_name'] . ',充值:' . $data['total_fee'] / 100 . '元.',
            'opt_ip' => 'weixin_callback',
            'opt_time' => time(),
            'class' => 'center\modules\financial\models\PayList',
            'type' => '1',
        ];
            Db::table('log_operate')->insert($log_operate_data);

        return true;
    }

    public function notify(){
        $notify = new self();
        $notify->Handle(false);
    }
}
