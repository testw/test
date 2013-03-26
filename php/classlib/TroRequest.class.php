<?php

/**
 * TroRequest.class.php 
 *  
 * 仅供 管理工具使用 
 * 请求封装类，把常用的请求解析放到这里  
 */

require_once("Config.class.php");


/**
 * 对php的请求进行封装和取配置进行封装
 * 
 * @author troodon (2012/7/12)
 */
class TroRequest {
	// 调用者
	private $owner = "";

	// debug标志
	private $debug = false;

	// 游戏类型,如：xg,xl,mx,js,yn
	private $game = "";

	// 当前服务器类型，如：ver,bk,dev,test
	private $svr = "";

	// 目标服务器类型，如：ver,bk,dev,test
	private $tsvr = "";

	//操作类型，不同功能中这个类型表示的意义不一样，具体含义有不同模块自己定义
	private $ty = "";

	// 功能服务器类型（不同服务器的目录名字），如：gs,dts,dld, xg0001g1等
	private $gft = "";

	// shell脚本名字，更新，重启，关闭服务器时使用，（重启和关闭是客户端穿上来的，更新时是根据$svr生成的）如：restart等, stop.sh等
	private $shname = "";

	// 更新版本，上传文件，更新版本时用 如：xg_0_0010, xl_0_001等。
	private $ver = "";

	// socket指令，清理首杀，清理帮会竞技，清理限购等功能用，这个是连到游戏服务器后发送给服务器的指令。如：phpcmd cleanfirstkill
	private $sockcmd = "";

	// 文件类型: csv,zip || sj,jpg,zip  （文件上传功能用）
	private $ftype = "";

	// 表示上传资源类型，flash,img等（文件上传功能用）如果不是上传资源则此项为空
	private $restype  = "";          

	// 保存上传文件的相关信息。内容和格式跟$_FILES相同
	private $files = array(); 
	
	// 游戏的配置
	private $cfg = "";

	// 构造函数
	public function __construct($owner, $debug = false){  
		$this->owner = $owner;
		$this->debug = $debug;
		$this->_debug("init ok");

		$this->parseRequest(); // 解析请求
	}

	// 析构函数
	public function __destruct(){
		$this->_debug("uninit ok");
	}


	// 检查合法性
	public function validParam(){
		if($this->svr == "bk" && !$this->ver){
			$this->_debug("ver not found($this->game)");
			return false;
		}

		if($this->owner == "upload" && !$this->files){
			$this->_debug("upload file not found($this->game)");
			return false;
		}
		if($this->owner == "runshell" && (!$this->gft or !$this->shname))
		{
			$this->_debug("runshell gft or shname not found($this->game)");
			return false;
		}
		return true;
	}

	// 得到版本号
	public function svrVer(){
		return $this->cfg["svrver"];
	}

	// 得到svn安装目录
	public function svrSvn(){
		return $this->cfg["svrsvn"];
	}

	// 得到游戏服的ip ，不同产品，不用服务器，不同功能
	public function svrIp(){
		try{
			$cfgs = $this->checkCfg("svrip");
			return $this->getCfg($cfgs);;
		}
		catch(Exception $e){}

		return false;
	}

	// 得到游戏服的根目录
	public function svrRoot(){
		try{
			$cfgs = $this->checkCfg("svrroot");
			return $this->getCfg($cfgs);
		}
		catch(Exception $e){}

		return false;
	}

	// 得到当前功能服的路径
	public function svrGftPath(){
		try{
			$cfgs = $this->checkCfg("svrroot");
			$root = $this->getCfg($cfgs);
			if($this->gft){
				return $root.$this->gft;
			}
			else{
				$txt = "$svrGftPath() failed: reason: Uninitialized variables gft";
				echo $txt . "\n";
				throw new Exception($txt);
			}
		}
		catch(Exception $e){}

		return false;
	}

	// 得到游戏服的的正常服目录（开发、验收和测试1服）
	public function svrNormalFolder(){
		try{
			$cfgs = $this->checkCfg("svrfolder");
			$arr = $this->getCfg($cfgs);;
			if($arr){
				return isset($arr[0])?$arr[0]:false;
			}
		}
		catch(Exception $e){}

		return false;
	}

	// 得到游戏服的所有服务器的文件夹数组
	public function svrFolders(){
		try{
			$cfgs = $this->checkCfg("svrfolder");
			return $this->getCfg($cfgs);;
		}
		catch(Exception $e){}

		return false;
	}

	// 得到游戏服的所有服务器的文件夹数组
	public function svrFolderNames(){
		try{
			$cfgs = $this->checkCfg("svrfolder");
			return $this->getCfg($cfgs, "name");;
		}
		catch(Exception $e){}

		return false;
	}


	// 得到资源服的ip
	public function resIp(){
		try{
			$cfgs = $this->checkCfg("resip");
			return $this->getCfg($cfgs);;
		}
		catch(Exception $e){}

		return false;
	}

	// 得到资源服的根目录
	public function resRoot(){
		try{
			$cfgs = $this->checkCfg("resroot");
			return $this->getCfg($cfgs);;
		}
		catch(Exception $e){}

		return false;
	}

	// 得到某个类型的资源目录
	public function resFolder(){
		try{
			$cfgs = $this->checkCfg("resfolder");
			$arr = $this->getCfg($cfgs);;
			if($arr){
				return isset($arr[$this->restype])?$arr[$this->restype]:false;
			}
		}
		catch(Exception $e){}

		return false;
	}

	// 把所有参数输出
	public function dump(){
		echo "dump";
	}

//////////////////////以上是对外提供的接口////////////////////////////////////////////////


	public function __set($name, $value){
		if(isset($this->$name))
		{
			$this->$name = $value;
			return $this->$name;
		}
		else{
			throw new Exception("undefined $name");
		}
	}

	public function __get($name){
		if(isset($this->$name))
		{
			return $this->$name;
		}
		else{
			throw new Exception("undefined $name");
		}
	}

	// 解析请求数据
	private function parseRequest(){
		$this->game     = isset($_REQUEST["game"])		? $_REQUEST["game"]		: "";          // 游戏名: xl || mx
		$this->svr      = isset($_REQUEST["svr"])		? $_REQUEST["svr"]		: "";          // 服务器名: dev || bk
		$this->tsvr     = isset($_REQUEST["tsvr"])		? $_REQUEST["tsvr"]		: "";          // 目标服务器名: dev || bk
		$this->ftype    = isset($_REQUEST["ftype"])		? $_REQUEST["ftype"]	: "";          // 文件类型: csv,zip || sj,jpg,zip 
		$this->ver      = isset($_REQUEST["ver"])		? $_REQUEST["ver"]		: "";          // 版本号: xl_201203/xl_0_180
		$this->restype  = isset($_REQUEST["res"])		? $_REQUEST["res"]		: "";          // 表示上传资源类型，没有此项则不是上传资源
		$this->gft      = isset($_REQUEST["gft"])		? $_REQUEST["gft"]		: "";          // 目标功能服务器: xg0001, dts, gs, xgd001等
		$this->sockcmd  = isset($_REQUEST["cmd"])		? $_REQUEST["cmd"]		: "";          // 要执行的指令
		$this->shname   = isset($_REQUEST["shname"])	? $_REQUEST["shname"]	: "";          // shell类型 如 restart.sh close.sh
		$this->ty		= isset($_REQUEST["ty"])		? $_REQUEST["ty"]		: "";          //操作类型，不同功能中这个类型表示的意义不一样，
		if($_FILES){                      // 上传文件内容
			$this->files = $_FILES;
		}

		 // 增加指令执行头
		if($this->sockcmd){
			$this->sockcmd = "phpcmd " . $this->sockcmd;
		}

		// 生成shell文件名
		if($this->shname){
			$this->shname .= ".sh";
		}
		elseif($this->owner == "update" && $this->tsvr){
			$this->shname = "update" . $this->tsvr . ".sh";
		}

		// 取得游戏配置
		if($this->game){
			$tmp = $this->game; // 直接使用this不要用
			$this->cfg = Config::$$tmp;
		}
		$this->_debug("parse ok");
	}


	// 调试输出
	private function _debug($str){
		if($this->debug){
			print($str . "\n");
		}
	}

	// 检查某项配置是否存在并返回
	private function checkCfg($ty){
		if($this->cfg){
			$cfgs = $this->cfg[$ty];
			if(!$cfgs){
				$txt = "$ty() failed: reason: config not found($this->game => $ty)";
				echo $txt . "\n";
				throw new Exception($txt);
			}
			return $cfgs;
		}
		else{
			$txt = "$ty() failed: reason: Uninitialized variables cfg";
			echo $txt . "\n";
			throw new Exception($txt);
		}
	}

	// 根据当前服务器取得配置内容
	private function getCfg($cfgs, $extend = ""){
		$key = $extend ? $this->svr."_".$extend : $this->svr;
		$def = $extend ? "def_".$extend : "def";

		if(isset($cfgs[$key])){
			return $cfgs[$key];
		}
		elseif(isset($cfgs[$def])){
			return $cfgs[$def];
		}
		else{
			$txt = "$getCfg() failed: reason: Uninitialized variables $key";
			echo $txt . "\n";
			throw new Exception($txt);
		}
	}

}

?>
