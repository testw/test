<?php 
/**
 * svrcmd.php 
 * 执行一条服务器指令 
 */

//设置过期时间
header("Expires: 0");
//设定最后修改时间为访问该页面的时间
header("Last-Modified: ".gmdate("D, d M Y H:i:s")." GMT");
//HTTP 1.1 不保留缓存
header("Cache-Control: no-store, no-cache, must-revalidate");
header("Cache-Control: post-check=0, pre-check=0", false);

require_once("classlib/TroRequest.class.php");
require_once("classlib/TroExec.class.php");
require_once("classlib/TroSocket.class.php");

$request = new TroRequest("svrcmd");


// 根据路径取得服务器的端口配置 etc/admin/admin.conf
function getPort($path, $ip){
	$cmd = "cd $path && grep port etc/admin/admin.conf";

	$exec = new TroExec($cmd, $ip);
	$txt = $exec->shellExecEx();
	if(ereg("[0-9]+", $txt[0], $txt)){
		return (int)$txt[0];
	}
	return false;
}

// 格式化ip 192.168.10.1
function formatIp($ip){
	if($ip && ereg("[0-9]+\.[0-9]+\.[0-9]+\.[0-9]+", $ip, $tmp)){
		$ip = $tmp[0];
	}
	return $ip;
}

if($request->validParam()){
	$ip = $request->svrIp();
	$port = getPort($request->svrGftPath(), $ip);

	$sockip = formatIp($ip);
	$sockip = $sockip ? $sockip : "127.0.0.1";

	$sock = new TroSocket($sockip, $port);
	$sock->connect();
	$sock->write($request->sockcmd);
	$info = $sock->read();
	if(ereg("<ok>", $info)){
		$info = str_replace("<ok>", "指令执行成功", $info);
	}
	elseif(ereg("<fail>", $info)){
		$info = str_replace("<fail>", "指令执行失败", $info);
	}
	echo(nl2br($info));
}

?>

