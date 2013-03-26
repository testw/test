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


$request = new TroRequest("upload");

if($request->validParam()){
	if($request->restype){  // 上传资源
		$ip = $request->resIp();
		$root = $request->resRoot();
		$folder = $request->resFolder() . "/";
	}
	else{
		$ip = $request->svrIp();
		$root = $request->svrRoot();
		if($request->ver){ // 有版本号表示是上传到版本中
			$folder = SVRPATH.$request->ver;
			$svncommit = $root."svncommit.sh $root$folder 页面更新" ;
		}
		else{
			$folder = $request->svrNormalFolder() . "/";
		}
	}
	
	$target = $root.$folder;

    if(!$ip && !is_dir($target)){
        echo "no such file or directory($target)";
        return;
    }

	$file = $request->files["filename"];
    $spath = UPLOAD.basename($file["name"]);
    $result = move_uploaded_file($file["tmp_name"], $spath);
    if($result){
        if($request->ftype == CSVSUFFIX){
            $target .=  CSVPATH;
        }
		elseif($request->ftype == MAPSUFFIX){
            $target .=  MAPPATH;
        }
		$exec = new TroExec();
        $exec->remoteupload($spath, $target, $ip);
        if(isset($svncommit)){
        	echo $svncommit."<br/>";
        	$exec = new TroExec($svncommit, $ip);
        	$exec->shellExec();
        }
   }
   else{
	   echo "upload error:<br/>";
       foreach($file as $key=>$value){
		   echo "$key => $value <br/>";
	   }
   }
}
else{
	$request->dump();
}

?>
