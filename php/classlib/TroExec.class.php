<?php

/**
 * TroExec.class.php 
 *  
 * 仅供 管理工具使用 
 * 封装php以root权限执行shell的能力。此功能依赖于exec4root 
 */


/**
 * 对php的socket进行封装
 * 
 * @author troodon (2012/6/15)
 */
class TroExec {
	// 代理指令
	private $proxy = "./shell/exec4root";

	// 需要替换的内容
	private $replace_str = array("\$"=>"\\\$", "\\"=>"\\\\", "\""=>"\\\"");

	// 目标ip
	private $ip = "";

	// 执行的shell
	private $cmd = "";

	// debug标志
	private $debug = false;

	public function __construct($cmd="", $ip = "", $debug = false){  
		$this->ip = $ip;
		$this->cmd = $cmd;
		$this->debug = $debug;

		$this->_debug("init ok");
	}

	public function __destruct(){
		$this->_debug("uninit ok");
	}



	// 用root权限执行一个shell指令,并把shell的输出存储到一个数组中返回
	public function shellExecEx(){
		$this->genCmd();
	    $cmds = $this->proxy .' "' . $this->unescapes($this->cmd) . ' 2>&1"';
	    $fp = popen($cmds, "r");
	    $ret = array();
		while(!feof($fp)) {
		  $buffer = fgets($fp);
		  if($buffer && strlen($buffer) > 0){
			  array_push($ret, $buffer);
		  }
		}   
		pclose($fp);
		$this->_debug("shellexec_ex ok: $cmds");
	   return $ret;
	}

	// 用root权限执行一个shell指令,并把shell的输出原样输出给页面
	public function shellExec($newline=true){
		$this->genCmd();
	    $cmds = $this->proxy . ' "' . $this->unescapes($this->cmd) . ' 2>&1"';
	    $fp = popen($cmds, "r");
		while(!feof($fp)) {
		  $buffer = fgets($fp);
		  if($buffer && strlen($buffer) > 1 ){
			  echo $newline?nl2br($buffer):$buffer;
		  }
		}   
		pclose($fp);
		$this->_debug("shellexec ok: $cmds");
		return true;
	}

	//拷贝文件拷贝到远程目录，通过shell（上传文件后从临时目录拷贝到相应目录）
	public function remoteupLoad($filename, $path, $ip=""){
		$this->cmd = sprintf("./shell/remoteupload.sh %s %s %s", $filename, $path, $ip);
		return $this->shellexec();
	}




/////////////////////以上对对外提供的接口/////////////////////////////////





	// 把" $ \改成 \" \$ \\
	private function unescapes($str){
		return strtr($str, $this->replace_str);
	}

	// 生成指令
	private function genCmd(){
		if($this->ip){
			$this->cmd = $this->unescapes($this->cmd);// 做一次反转义
			$this->cmd = "ssh $this->ip \"$this->cmd\""; 
		}
		$this->_debug("genCmd ok: $this->cmd");
	}



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



	// 调试输出
	private function _debug($str){
		if($this->debug){
			print($str . "\n");
		}
	}



}

?>
