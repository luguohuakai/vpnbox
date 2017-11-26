<?php
namespace app\index\controller;

use think\Controller;

class Base extends Controller
{
    protected $app_id;
    protected $app_secret;
    protected $domain;
    protected $open_domain;
    // 需要open_id和网页授权access_token的请求
    protected $access_action = [
//        'index/index/bindmachandle',
        'index/index/bindmac',
//        'index/index/index',
//        'index/index/payway',
//        'index/index/settings',
        '',
    ];

    // 需要获取用户详情的请求
    protected $detail_action = [
        'index/index/bindmac',
        '',
    ];

    public function _initialize()
    {
        parent::_initialize();

        $this->app_id = config('wx.app_id');
        $this->app_secret = config('wx.app_secret');
        $this->domain = config('wx.domain');
        $this->open_domain = config('wx.open_domain');

        if (in_array($this->request->path(),$this->access_action) || in_array($this->request->path(),$this->detail_action)){
            if (in_array($this->request->path(),$this->detail_action)){
                $scope = 'snsapi_userinfo';
            }else{
                $scope = 'snsapi_base';
            }
            // 获取code
            if(!session('wx_open_id')){
                self::getCode($scope);
            }
            // 获取access_token和openid
            if (input('get.code')){
                session('wx_code',input('get.code'));
                self::getToken(input('get.code'));
            }
            // 获取用户详情
//            if (in_array($this->request->path(),$this->detail_action)){
                self::getDetail();
//            }
        }
    }

    public function index()
    {
        return json(['code' => 200, 'msg' => '获取成功']);
    }

    // 微信网页授权登录
    // 获取code
    public function getCode($scope){
        $uri = url($this->request->url(),'',false,true);
        $app_id = $this->app_id;
        $redirect_uri = urlencode($uri);
        $url = $this->open_domain . "connect/oauth2/authorize?appid=$app_id&redirect_uri=$redirect_uri&response_type=code&scope=$scope&state=1#wechat_redirect";

//        dd($url);
        header('Location:' . $url);
//        redirect($url);
//        $this->redirect($url);
    }

    // 获取token openid并存入session
    public function getToken($wx_code){
        $domain = $this->domain;
        $app_id = $this->app_id;
        $secret = $this->app_secret;

        if($wx_code){
            // 获取 token 和 open_id
            $url = $domain . "sns/oauth2/access_token?appid=$app_id&secret=$secret&code=$wx_code&grant_type=authorization_code";
            $rs = get($url);

            if($rs){
                $rs = json_decode($rs,true);
                session('wx_open_id',$rs['openid']);
                session('wx_access_token',$rs['access_token']);
            }
        }
    }

    // 获取用户详情
    public function getDetail()
    {
        session('gg','hh');
        $domain = $this->domain;
        $access_token = session('wx_access_token');
        $open_id = session('wx_open_id');
        $url = $domain . "sns/userinfo?access_token=$access_token&openid=$open_id&lang=zh_CN";

        $rs = get($url);
        if ($rs){
            session('aa','aa');
            session('wx_user_detail',$rs);
        }else{
            session('bb','bb');
        }
    }
}
