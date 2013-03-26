<?php

/**
 * TroSocket.class.php 
 *  
 * socket类，封装socket相关功能  
 */


/**
 * 对php的socket进行封装
 * 
 * @author troodon (2012/6/15)
 */
class TroSocket {
	// 目标ip
	private $ip = "";

	// 目标端口
	private $port = 0;

	// socket 对象
	private $socket = null;

	// debug标志
	private $debug = false;

	public function __construct($ip, $port, $debug = false){  
		$this->ip = $ip;
		$this->port = $port;
		$this->debug = $debug;
		$this->socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
		if ($this->socket === false){
			$txt = "socket_create() failed: reason: " . socket_strerror(socket_last_error());
			echo $txt . "\n";
			throw new Exception($txt);
		}
		$this->_debug("init ok");
	}

	public function __destruct(){
		socket_close($this->socket);
		$this->_debug("uninit ok");
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


	// 建立连接
	public function connect(){
		$result = socket_connect($this->socket, $this->ip, $this->port);
		if($result === false){
			$txt = "socket_connect() failed: reason: " . socket_strerror(socket_last_error());
			echo $txt . "\n";
			throw new Exception($txt);
			break;
		}
		$this->_debug("connect ok");

		$this->write("admin_2343jio32j4ownrjewhtkjwhoih098uv890fuyb89ufosirj"); //连接到服务器
	}

	// 发送字符串(以\n结尾的字符串)
	public function write($buf){
		if(!strchr($buf, "\n") && !strchr($buf, "\r"))
			$buf .= "\n";

		$len = strlen($buf); 
		$offset = 0; 
		while ($offset < $len) { 
			$sent = socket_write($this->socket, substr($buf, $offset), $len-$offset); 
			if ($sent === false) { 
				$txt = "socket_write() failed: reason: " . socket_strerror(socket_last_error());
				echo $txt . "\n";
				throw new Exception($txt);
				break; 
			} 
			$offset += $sent; 
		} 
		$this->_debug("write ok:\n".$buf);
		return $offset;
	}


	// 读取字符串（以\n或\r结束的字符串）
	public function read($tail = array("<ok>\n", "<fail>\n"), $len = 65535){
		$buf = "";
		while($len > strlen($buf)){
			$this->_debug("begin read");
			$ret = socket_read($this->socket, $len-strlen($buf), PHP_NORMAL_READ);
			$this->_debug("readdata:\n$ret");
			if($ret === false){
				$txt = "socket_read() failed: reason: " . socket_strerror(socket_last_error());
				echo $txt . "\n";
				throw new Exception($txt);
				break;
			}
			elseif(strlen($ret) > 0){
				$buf .= $ret;
				if($this->isEnding($ret, $tail)){
					break;
				}
			}
			else{
				$this->_debug("else::readdata:\n$ret");
				break;
			}
		}
		return $buf;
	}

	// 调试输出
	private function _debug($str){
		if($this->debug){
			print($str . "\n");
		}
	}

	// 判断一个字符串是否是另一个字符串的结尾
	private function isEnding($ret, $tail){
		if(!$ret){
			return true;
		}
		elseif(is_array($tail)){
			foreach($tail as $k=>$v){
                if($ret == $v){
                    return true;
                }
            }
		}
		elseif(is_string($tail)){
			return $ret == $tail;
		}
	}

}

?>
