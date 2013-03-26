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


$request = new TroRequest("svnupdate");

$bkroot = $request->svrRoot();

$path = $bkroot.SVRPATH;
$svn = $request->svrSvn();
$ip = $request->svrIp();


$shell = "cd $path && $svn  --config-dir ./.svn/cfg update . ";
$exec = new TroExec($shell, $ip);
$exec->shellexec();



?>
