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


$request = new TroRequest("getver");

$bkroot = $request->svrRoot();


$path = $bkroot.SVRPATH;
$verhead = $request->svrVer();
$ip = $request->svrIp();

$shell = "cd $path && find . -name \"$verhead*\" |awk -F / '{if(\$3) print \$2\"/\"\$3}'|sort -k1r|uniq|grep $verhead";
$exec = new TroExec($shell, $ip);
$exec->shellexec(false);


?>
