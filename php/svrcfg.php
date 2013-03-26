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

$request = new TroRequest("svrcfg");



// 根据路径取得服务器的端口配置 数组$port = array(4933, 4932)
function getAllPort($path, $ip){
	$sflag = false;
	$ret = array();

	$cmd = "cd $path && cat etc/server.conf";
	$exec = new TroExec($cmd, $ip);
	$txt = $exec->shellExecEx();
	
	foreach($txt as $key){
		if($sflag && ereg("[^\" -]+",$key, $catch)){
			$cmd = "cd $path && grep port etc/$catch[0]/$catch[0].conf";
			$exec = new TroExec($cmd, $ip);
			$port = $exec->shellExecEx();
			if(ereg("[0-9]+", $port[0], $port)){
				array_push($ret, (int)$port[0]);
			}
		}

		if(ereg("^svr", $key)){ // 标记开始
			$sflag = true;
		}
		if($sflag && ereg("}", $key)){ // 标记结束
			$sflag = false;
		}
	}
	return $ret;
}

// 得到某个端口的状态
function protStat($port, $ip){
	$cmd = "netstat -ntlp|grep 0:".$port;
	$exec = new TroExec($cmd, $ip);
	$txt = $exec->shellExecEx();
	return ($txt && count($txt) > 0);
}



// 得到某个端口的状态
function processStat($path, $ip, $pname){
	$cmd = "ps -ef|grep $pname|grep -v grep|awk '{system(\"ls -l /proc/\"$2\"/exe\")}'|grep \"$path\" &2>/dev/null";

	$exec = new TroExec($cmd, $ip);
	$txt = $exec->shellExecEx();
	if($txt && count($txt) > 0){
		echo "启动";
		echo "(".count($txt).")";
	}
	else{
		echo "关闭";
	}
}



// 功能配置
if(!$request->ty){
	$table = array(
		"ty"      => $request->svrFolders(),        // 游戏类型
		"name"    => $request->svrFolderNames(),    // 游戏名字
		"process" => array(),     // 游戏进程状态
		"port"    => array(),     // 游戏端口状态
	);

	echo json_encode($table);
}
elseif($request->ty == "port"){  // 端口及端口状态
	$port = getAllPort($request->svrGftPath(), $request->svrIp());
	for($i=0; $i<count($port);$i++){
		if($i != 0) echo "|";

		echo $port[$i];
		if(protStat($port[$i], $request->svrIp())){
			echo "(正常)";
		}
		else{
			echo "(关闭)";
		}
	}
}
elseif($request->ty == "process"){ // 进程状态
	if(ereg("g1$", $request->gft)){
		processStat($request->svrGftPath(), $request->svrIp(), $request->gft);
	}
	else{
		processStat($request->svrGftPath(), $request->svrIp(), "tsvr");
	}
}

?>
