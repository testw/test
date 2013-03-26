<?php

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

$request = new TroRequest("runshell");


// 重启某个服务器
function restart($path, $cmd, $ip=""){    
	$cmd = "cd $path && ./$cmd\n";

	$exec = new TroExec($cmd, $ip);
	$exec->shellexec();
}

if($request->validParam()){
	$gfolders = $request->svrFolders();
	$groot = $request->svrRoot();
	$ip = $request->svrIp();

	if($request->gft == "all"){
		foreach($gfolders as $value) {
			restart($groot.$value, $request->shname, $ip);
		}
	}
	else{
		restart($request->svrGftPath(), $request->shname, $ip);
	}

}
?>
