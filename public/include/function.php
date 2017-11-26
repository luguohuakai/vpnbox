<?php
/**
 * Created by PhpStorm.
 * User: DM
 * Date: 17/3/31
 * Time: 上午11:45
 */

if(!function_exists('dd')){
    /**
     * 调试输出
     * @param $var
     */
    function dd($var = ''){
        if($var === false) die('bool false');
        if($var === null) die('null');
        if(is_string($var) and trim($var) === '') die('string ""');
        echo '<pre>';
        print_r($var);
        die('</pre>');
    }
}

if(!function_exists('Rds')){
    /**
     * @param int $index
     * @param int $port
     * @param string $host
     * @return Redis
     */
    function Rds($index = 0,$port = 6379,$host = 'localhost'){
        $rds = new \Redis();
        $rds->connect($host,$port);
        $rds->select($index);

        return $rds;
    }
}

if(!function_exists('alert')){
    /**
     * @param $var
     */
    function alert($var){
        $str = (string)json_encode($var);
        echo "<script type='text/javascript'>alert('$str');</script>";
    }
}

if(!function_exists('L')){
    /**
     * @param $msg
     * @param $file_name
     */
    function L($msg,$file_name){
        $msg = date('Y-m-d H:i:s')
            . "\r\n" . $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']
            . "\r\n" . Yii::$app->controller->module->id . '/' . Yii::$app->controller->id . '/' . Yii::$app->controller->action->id
            . "\r\n输出信息:" . $msg
            . "\r\n\r\n";
        error_log($msg,3,__DIR__ . '/../../center/runtime/logs/'. $file_name . '_' . date('Y-m-d') . '.txt');
    }
}

if(!function_exists('export_csv')){
    /**
     * @param $data
     * @param string $filename
     */
    function export_csv($data,$filename = '')
    {
        if(!$filename) $filename = date('YmdHis') . '.csv';
        header("Content-type:text/csv");
        header("Content-Disposition:attachment;filename=".$filename);
        header('Cache-Control:must-revalidate,post-check=0,pre-check=0');
        header('Expires:0');
        header('Pragma:public');
        $str = '';
        $keys = array_keys($data[0]);
        for($i = 0;$i < count($keys);$i++){
            if($i != count($keys) - 1){
                $str .= $keys[$i] . ',';
            }else{
                $str .= $keys[$i] . "\r\n";
            }
        }
        foreach ($data as $vv){
            $k = 0;
            foreach ($vv as $vvv){
                if ($k != count($vv) - 1){
                    $str .= $vvv . ',';
                }else{
                    $str .= $vvv . "\r\n";
                }
                $k++;
            }
        }
        $str = iconv('utf-8','gb2312',$str);
        exit($str);
    }
}

if (!function_exists('get')) {
    /**
     * @param $url
     * @return mixed
     */
    function get($url){
        //初始化
        $ch = curl_init();
        //设置选项，包括URL
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        //执行并获取HTML文档内容
        $output = curl_exec($ch);
        //释放curl句柄
        curl_close($ch);

        return $output;
    }
}

if (!function_exists('post')) {
    /**
     * @param $url
     * @param $post_data
     * @return mixed
     */
    function post($url,$post_data){
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        // post数据
        curl_setopt($ch, CURLOPT_POST, 1);
        // post的变量
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
        $output = curl_exec($ch);
        curl_close($ch);

        return $output;
    }
}

if (!function_exists('C')) {
    /**
     * 快捷获取配置信息
     * @param $str
     * @return mixed
     */
    function C($str){
        return Yii::$app->params[$str];
    }
}

if (!function_exists('transform_mac_to_formal')) {
    /**
     * 转化为标准mac
     * @param $mac
     * @return string
     */
    function transform_mac_to_formal($mac){
        return strtolower(str_replace('-', ':', trim($mac)));
    }
}

if (!function_exists('json')) {
    /**
     * @param array $data
     * @param string $msg
     * @param int $code
     * @param array $header
     * @param array $options
     * @return mixed
     */
    function json($msg = '请求成功', $code = 200, $data = [], $header = [], $options = [])
    {
        header('content-type:application/json');

        if(is_array($msg)){
            return $msg;
        }

        $re['msg'] = $msg;
        $re['code'] = $code;
        if(!empty($data)){
            $re['data'] = $data;
        }

        return $re;
    }
}