<?php
ini_set('session.name', 'PHPSESSID_80');

if(!empty($_POST['validate'])){
session_start();
	if($_POST['validate']!=$_SESSION["authnum_session"]){
	//判断session值与用户输入的验证码是否一致;
		exit('no');
	}else{
		exit('ok');
	}
	
}

?>