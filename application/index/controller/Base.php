<?php
namespace app\index\controller;

use think\Controller;
use think\Db;
use think\Exception;

class Base extends Controller
{
    protected $app_id;
    protected $app_secret;
    protected $domain;
    protected $open_domain;
    // 不需要open_id和网页授权access_token的请求
    protected $access_action = [
//        'index/index/bindmachandle',
//        'index/index/bindmac',
//        'index/index/index',
//        'index/index/payway',
//        'index/index/settings',
        'index/test/clearsession',
        'index/test/sessionlist',
        'index/test/checksignature',
        'index/test/index',
        'index/test/setmenu',
        'index/test/getqrurl',
        'index/pay/test',
        'index/pay/index',
        'index/pay/payrequest',
        'index/pay/notifyurl',
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
//dd($this->request->path());
        // 找user_id 和 user_name
        $wx_open_id = session('wx_open_id');
        if ($wx_open_id){
            try{
                $rs = Db::table('users')->where('wx_open_id',$wx_open_id)->field('user_id,user_name')->find();
                if ($rs){
                    session('uid',$rs['user_id']);
                    session('user_name',$rs['user_name']);
                    $md5_password = Rwx(0)->hGet('hash:users:' . $rs['user_id'],'md5_password');
                    session('md5_password',$md5_password);
                    setcookie('md5_password',$md5_password);
                }else{
                    session('uid',null);
                    session('user_name',null);
                    session('md5_password',null);
                }
            }catch (Exception $e){
                dd();
                $this->error($e->getMessage());
            }
        }

        $this->app_id = config('wx.app_id');
        $this->app_secret = config('wx.app_secret');
        $this->domain = config('wx.domain');
        $this->open_domain = config('wx.open_domain');

        if (!in_array($this->request->path(),$this->access_action) || in_array($this->request->path(),$this->detail_action)){
            if (in_array($this->request->path(),$this->detail_action)){
                $scope = 'snsapi_userinfo';
            }else{
                $scope = 'snsapi_base';
            }
            // 获取access_token和openid
            if (input('get.code')){
                self::getToken(input('get.code'));
            }else{
                // 获取code
                if(!session('wx_open_id')){
                    self::getCode($scope);
                    // 此处必须退出一下 否则会有意想不到的后果！！！！
                    exit;
                }
            }
        }
        // 获取用户详情
        if (
            in_array($this->request->path(),$this->detail_action) &&
            session('wx_access_token') &&
            session('wx_open_id')
        ){
            self::getDetail();
        }
    }

    public function index()
    {
        return json(['code' => 200, 'msg' => '获取成功']);
    }

    // 微信网页授权登录
    // 获取code
    public function getCode($scope){
        $uri = url($this->request->url(),'',true,true);
        $app_id = $this->app_id;
        $redirect_uri = urlencode($uri);
        $url = $this->open_domain . "connect/oauth2/authorize?appid={$app_id}&redirect_uri={$redirect_uri}&response_type=code&scope={$scope}&state=1#wechat_redirect";

        header('Location:' . $url);
//        redirect($url);
//        $this->redirect($url);
    }

    // 获取token openid并存入session
    public function getToken($wx_code){
        $domain = $this->domain;
        $app_id = $this->app_id;
        $secret = $this->app_secret;

        session('wx_code',$wx_code);
        if($wx_code){
            // 获取 token 和 open_id
            $url = $domain . "sns/oauth2/access_token?appid={$app_id}&secret={$secret}&code={$wx_code}&grant_type=authorization_code";
            $rs = get($url);

            if($rs){
                $rs = json_decode($rs,true);
                if (isset($rs['openid'])){
                    session('wx_open_id',$rs['openid']);
                    session('wx_access_token',$rs['access_token']);
                }
            }
        }
    }

    // 获取用户详情
    public function getDetail()
    {
        $domain = $this->domain;
        $access_token = session('wx_access_token');
        $open_id = session('wx_open_id');
        $url = $domain . "sns/userinfo?access_token=$access_token&openid=$open_id&lang=zh_CN";

        $rs = get($url);
        if ($rs){
            session('wx_user_detail',$rs);
        }
    }
}
