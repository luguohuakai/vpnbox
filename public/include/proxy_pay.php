<?php
/**
 * Created by PhpStorm.
 * User: DM
 * Date: 2016/6/5
 * Time: 9:01
 */

require_once './function.php';

//$host_9999 = 'http://47.104.1.91:9999/';
$host_9999 = 'http://wx.srun.com:9999/';
//$host_8080 = 'https://47.104.1.91:8080/api/';
$host_8080 = 'https://wx.srun.com:8080/api/';
$_POST['interface'] = 'service_demo/pay.php'; // 支付接口

//$_POST['subject'] = iconv('UTF-8','GBK//IGNORE','SRUNBOX充值'); // 充值项名称
$_POST['subject'] = 'SRUNBOX Recharge'; // 充值项名称
$_POST['type'] = 'product'; // 充值类型 为产品充值

// 根据用户名获取用户的第一个产品id
$product_id = false;
$rs_8080 = getHttpResponsePOST($host_8080 . 'qr/get-first-product-id',['user_name' => $_POST['user_name']]);
if ($rs_8080){
    $rs_8080 = json_decode($rs_8080,true);
    if($rs_8080['code'] == 200){
        $product_id = $rs_8080['product_id'];
        $_POST['product_id'] = $product_id; // 充值产品的id
    }
}
if (!$product_id){
    $re['msg'] = '用户名不正确或未订购产品';
    $re['code'] = 400;
    exit(json_encode($re));
}
//dd($_POST);
$count = count($_POST);
if ($count > 1 && isset($_POST['interface'])) {
    $interface = $_POST['interface'];
    unset($_POST['interface']);
    if($_POST['action'] == 'weixin_h5'){
        $_POST['ip'] = $_SERVER['REMOTE_ADDR'];
    }
    $para = $_POST;
    $rs = getHttpResponsePOST($host_9999 . $interface, $para);
    $rs = json_decode($rs,true);
    if($_POST['action'] == 'alipay_pc'){
//        dd($host_9999 . $interface);
//        dd($_POST);
//        dd($rs['form']);
        echo $rs['form']; // 输出支付宝充值表单
    }elseif($_POST['action'] == 'weixin_h5'){
        // 调用微信APP支付
        header("Location:" . $rs['form']."&redirect_url=" . urlencode('http://wx.srun.com/index/index/payresult.html?out_trade_no=' . $rs['out_trade_no'] . '&mac=' . $_POST['user_name']));
    }else{
        dd($rs);
    }
} elseif (($count == 1 && isset($_POST['interface'])) || isset($_GET['interface'])) {
//    isset($_POST['interface']) ? $interface = $_POST['interface'] : $interface = urldecode(str_replace('url=','',$_SERVER['QUERY_STRING']));
    if (isset($_POST['interface'])) {
        $interface = $_POST['interface'];
    } elseif (isset($_GET['interface'])) {
        $interface = $_GET['interface'];
        unset($_GET['interface']);
        $i = 0;
        foreach ($_GET as $k => $v) {
            if ($i == 0) {
                $interface .= '?' . $k . '=' . $v;
                $i++;
            } else {
                $interface .= '&' . $k . '=' . $v;
            }
        }
    }
    getHttpResponseGET($host_9999 . $interface);
} else {
    $re['msg'] = '请求错误（未传递任何参数或参数不正确）';
    $re['code'] = 400;
    exit(json_encode($re));
}

// post请求（curl()函数）
function getHttpResponsePOST($interface, $para, $cacert_url = '', $input_charset = '')
{
    $c = curl_init();
    curl_setopt($c, CURLOPT_SSL_VERIFYPEER, false);//SSL证书认证
    curl_setopt($c, CURLOPT_SSL_VERIFYHOST, false);//SSL证书认证
//    curl_setopt($c, CURLOPT_SSL_VERIFYHOST, 2);//严格认证
//    curl_setopt($c, CURLOPT_CAINFO,$cacert_url);//证书地址
    curl_setopt($c, CURLOPT_HEADER, 0); // 过滤HTTP头
    curl_setopt($c, CURLOPT_URL, $interface);   // 设置要访问的url地址
    curl_setopt($c, CURLOPT_RETURNTRANSFER, 1);   // ???? 显示输出结果
    curl_setopt($c, CURLOPT_POST, 1);   // 设置post
    curl_setopt($c, CURLOPT_POSTFIELDS, $para);   // 要post发送的数据
    $out_put = curl_exec($c);
//    var_dump( curl_error($c) );//如果执行curl过程中出现异常，可打开此开关，以便查看异常内容
    curl_close($c);

    return $out_put;
//    exit($out_put);
}

// get请求 （curl()函数）
function getHttpResponseGET($interface, $cacert_url = '')
{
    $c = curl_init();
    curl_setopt($c, CURLOPT_SSL_VERIFYPEER, false);//SSL证书认证
    curl_setopt($c, CURLOPT_SSL_VERIFYHOST, false);//SSL证书认证
//    curl_setopt($c, CURLOPT_SSL_VERIFYHOST, 1);//严格认证
//    curl_setopt($c, CURLOPT_CAINFO,$cacert_url);//证书地址
    curl_setopt($c, CURLOPT_HEADER, 0); // 过滤HTTP头
    curl_setopt($c, CURLOPT_URL, $interface);   // 设置要访问的url地址
    curl_setopt($c, CURLOPT_RETURNTRANSFER, 1);   // ???? 显示输出结果
    $out_put = curl_exec($c);
//    var_dump( curl_error($c) );//如果执行curl过程中出现异常，可打开此开关，以便查看异常内容
    curl_close($c);
    
    return $out_put;
//    exit($out_put);
}
