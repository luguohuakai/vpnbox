<?php
/**
 * Created by PhpStorm.
 * User: DM
 * Date: 2016/6/5
 * Time: 9:01
 */
// 简单请求方式（类似get请求）
//$interface = $_POST['interface'];
//$content = file_get_contents($interface);
//exit($content);
include_once './function.php';
sleep(1);
//$host_8080 = 'https://121.43.167.65:8080/api/';
$host_8080 = 'https://47.104.1.91:8080/api/';

$count = count($_POST);
if ($count > 1 && isset($_POST['interface'])) {
    $interface = $_POST['interface'];
    unset($_POST['interface']);
    $para = $_POST;
    getHttpResponsePOST($host_8080 . $interface, $para);
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
    getHttpResponseGET($host_8080 . $interface);
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
    exit($out_put);
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
    exit($out_put);
}
