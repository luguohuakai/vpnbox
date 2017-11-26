<?php
//加入验证
function checkUser($url,$access_token,$user_name,$station_id){
	$url_check = $url.'/api/v1/hezi/called-station';
	$data = [
		'access_token' => $access_token,
		'user_name'=>$user_name,
		'mac' => $station_id
	];
	$mess = getMassageApi($url_check, $data);
	return $mess;
}
//用户信息
function getUserMess($url,$access_token,$user_name){
	//获取用户信息
	$url_get_time = $url."/api/v1/package/users-packages?access_token={$access_token}&user_name={$user_name}";
	//获取到期时间
	$res_time = getMassageApi($url_get_time,'','get');
	$lasttime = $res_time['data'][0]['checkout_date'];
	if($lasttime == '---'){
		$lasttime = '已过期';
	}
	return $lasttime;
}
//请求接口
function getMassageApi($url, $param='' , $method='post')
{
	if(is_array($param)){
		$o = "";
		foreach ($param as $k => $v) {
			$o .= "$k=" . urlencode($v) . "&";
		}
		$param = substr($o, 0, - 1);
	}
	$postUrl = $url;
	$curlPost = $param;
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $postUrl);
	curl_setopt($ch, CURLOPT_HEADER, 0);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	switch($method){
		case 'post':
			curl_setopt($ch, CURLOPT_POST, 1);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $curlPost);
			break;
		case "get" :
			curl_setopt($ch, CURLOPT_HTTPGET, true);
			break;
	}
	$data = curl_exec($ch);
	curl_close($ch);
	$res_array = json_decode($data, true);
	writeLog($url,$data);
	return $res_array;
}
function writeLog($url,$data){
 $logFile = '/srun3/log/box/'.date("Y-m-d").'.log';
 $msg ="\r\n".'>>>>>'.date('Y-m-d H:i:s')."\r\n".'url:'.$url."\r\n 返回数据：".$data." \r\n";
 $res = file_put_contents($logFile,$msg,FILE_APPEND );
}
$array = array(
    "ji_err_ja" => "数据加载失败",                                                                      
    "ji_err" => "激活失败",                                                                      
    "code_err" => "缺少参数的提示",                                                                      
    "account_password_error" => "密码错误",                                                                      
    "user_tab_error" => "认证程序未启动",                                                                         
    "username_error" => "用户名输入错误",                                                                           
    "logout_error" => "注销时发生错误,或没有帐号在线！",                                                                    
    "uid_error" => "您的账号不在线上.",                                                                              
    "logout_ok" => "注销成功，请等1分钟后登录。",                                                                         
    "login_ok" => "登录成功",                                                                                    
    "ok" => "登录成功",                                                                                          
    "mac_error" => "您的MAC地址不正确",                                                                             
    "password_error" => "用户名或密码错误,请重新输入！",                                                                   
    "non_auth_error" => "您无须认证，可直接上网",                                                                       
    "status_error" => "您已欠费，请尽快充值。",                                                                         
    "sync_error" => "您的资料已被修改正在等待同步，请2钟分后再试。\n如果您的帐号允许多个用户上线，请到WEB登录页面注销。",                                  
    "delete_error" => "您的帐号已经被删除",                                                                           
    "ip_exist_error" => "IP已存在，请稍后再试。",                                                                      
    "usernum_error" => "在线用户已满，请稍后再试。",                                                                      
    "online_num_error" => "正在注销在线账号，请重新连接",                                                                  
    "proxy_error" => "你的IP地址和认证地址不附，可能是经过小路由器登录的。",                                                          
    "mode_error" => "系统已禁止客户端登录，请使用WEB方式登录。",                                                                
    "flux_error" => "您的流量已用尽。",                                                                              
    "minutes_error" => "您的时长已用尽。",                                                                           
    "ip_error" => "您的IP地址不合法，可能是：\n一、与绑的IP地址附；二、IP不允许在当前区域登录；",                                              
    "time_policy_error" => "当前时段不允许连接。",                                                                     
    "available_error" => "抱歉，您的帐号已禁用",                                                                       
    "Addr table error~login_error" => "计费系统尚未授权，目前还不能使用！",                                                   
    "ipv6_error" => "您的IPv6地址不正确，请重新配置IPv6地址!",                                                              
    "auth_resault_timeout_err" => "认证服务无响应",                                                                 
    "The server is not responding." => "后台服务器无响应,请联系管理员检查后台服务运行状态",                                          
    "IP has been online, please logout." => "您的IP已经在线,可以直接上网,或者先注销再重新认证",                                    
    "You are not online." => "注销成功",                                                                       
    "You have been forcibly disconnected" => "您已经被服务器强制下线！",                                                 
    "Authentication failed, but no error message is returned." => "身份验证失败，但不返回错误消息。",                                    
    "E2611" => "您当前使用的设备非该账号绑定设备 请绑定或使用绑定的设备登入",                                                             
    "E2602" => "您还没有绑定手机号或绑定的非联通手机号码",                                                                       
    "E2601" => "您使用的不是专用客户端,IPOE-PPPoE混杂模式请联系管理员重新打包客户端程序",                                                  
    "E2532" => "您的两次认证的间隔太短,请稍候10秒后再重试登录",                                                                   
    "E2531" => "帐号不存在或密码错误",                                                                                 
    "E2553" => "帐号或密码错误",                                                                                    
    "E2533" => "密码错误次数超过限制，请5分钟后再重试登录",                                                                      
    "E2807" => "后台系统配置错误,请联系管理员检查后台计费策略配置",                                                                  
    "E2808" => "后台系统配置错误,请联系管理员检查后台控制策略配置",                                                                  
    "E2606" => "用户被禁用",                                                                                      
    "E2613" => "NAS PORT绑定错误",                                                                               
    "E2614" => "MAC地址绑定错误",                                                                                  
    "E2615" => "IP地址绑定错误",                                                                                   
    "E2616" => "用户已欠费",                                                                                      
    "E2620" => "已经在线了",
    "E2621" => "已经达到授权人数",
    "E2806" => "找不到符合条件的产品",
    "E2807" => "找不到符合条件的计费策略",
    "E2833" => "IP不在DHCP表中，需要重新拿地址。",
    "E2808" => "找不到符合条件的控制策略",
    "E2840" => "校内地址不允许访问外网。",
    "E2843" => "IP地址不正确!",
    "E2842" => "您的IP地址无需认证即可上网",
);
?>