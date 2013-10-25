<?php
/**
 * 短信发送接口V2.2
 * 三三得九的短信接口
 * @author Michael
 * 2012-10-08
 *
 */
class sms_d9 {
	
   
	private static $_params;
	public static  $_error;
	
	
	
	function __construct($mobiles,$content){
		global $kekezu;
		if(CHARSET=='gbk'){
			$content = kekezu::gbktoutf($content);
		}
		$content = mb_substr(strip_tags($content), 0,60,'utf-8');
		self::$_params = array(
				'username'=>$kekezu->_sys_config['mobile_username'], //机构代码+账号"65974:".$_K['mobile_username'],
				'password'=>$kekezu->_sys_config['mobile_password'], 
				'to'=>$mobiles,
				'content'=>$content
		);
	}
	function doget($url){
		$url2 = parse_url($url);
		$url2["path"] = ($url2["path"] == "" ? "/" : $url2["path"]);
		if(array_key_exists("port", $url2))
			$url2["port"] = ($url2["port"] == "" ? 80 : $url2["port"]);
		else
			$url2["port"] = 80;
		$host_ip = @gethostbyname($url2["host"]);
		$fsock_timeout = 2;  //2 second
		if(($fsock = fsockopen($host_ip, $url2['port'], $errno, $errstr, $fsock_timeout)) < 0){
			return false;
		}
		if(array_key_exists("query", $url2))
			$request =  $url2["path"] .($url2["query"] ? "?".$url2["query"] : "");
		else
			$request =  $url2["path"];
		$in  = "GET " . $request . " HTTP/1.0\r\n";
		$in .= "Accept: */*\r\n";
		$in .= "User-Agent: Payb-Agent\r\n";
		$in .= "Host: " . $url2["host"] . "\r\n";
		$in .= "Connection: Close\r\n\r\n";
		if(!@fwrite($fsock, $in, strlen($in))){
			fclose($fsock);
			return false;
		}
		return gethttpcontent($fsock);
	}
	
	function dopost($url,$post_data=array()){
		$url2 = parse_url($url);
		$url2["path"] = ($url2["path"] == "" ? "/" : $url2["path"]);
		$url2["port"] = ($url2["port"] == "" ? 80 : $url2["port"]);
		$host_ip = @gethostbyname($url2["host"]);
		$fsock_timeout = 2; //2 second
		if(($fsock = fsockopen($host_ip, $url2['port'], $errno, $errstr, $fsock_timeout)) < 0){
			return false;
		}
		$request =  $url2["path"].($url2["query"] ? "?" . $url2["query"] : "");
		$post_data2 = http_build_query($post_data);
		$in  = "POST " . $request . " HTTP/1.0\r\n";
		$in .= "Accept: */*\r\n";
		$in .= "Host: " . $url2["host"] . "\r\n";
		$in .= "User-Agent: Lowell-Agent\r\n";
		$in .= "Content-type: application/x-www-form-urlencoded\r\n";
		$in .= "Content-Length: " . strlen($post_data2) . "\r\n";
		$in .= "Connection: Close\r\n\r\n";
		$in .= $post_data2 . "\r\n\r\n";
		unset($post_data2);
		if(!@fwrite($fsock, $in, strlen($in))){
			fclose($fsock);
			return false;
		}
		return $this->gethttpcontent($fsock);
	}
	function gethttpcontent($fsock=null) {
		$out = null;
		while($buff = @fgets($fsock, 2048)){
			$out .= $buff;
		}
		fclose($fsock);
		$pos = strpos($out, "\r\n\r\n");
		$head = substr($out, 0, $pos);    //http head
		$status = substr($head, 0, strpos($head, "\r\n"));    //http status line
		$body = substr($out, $pos + 4, strlen($out) - ($pos + 4));//page body
		if(preg_match("/^HTTP\/\d\.\d\s([\d]+)\s.*$/", $status, $matches)){
			if(intval($matches[1]) / 100 == 2){
				return $body;  
			}else{
				return false;
			}
		}else{
			return false;
		}
	}
	public function httprequest($url, $data=array(), $abort=false) {
		if ( !function_exists('curl_init') ) { return empty($data) ? $this->doget($url) : $this->dopost($url, $data); }
		$timeout = $abort ? 1 : 2;
		$ch = curl_init();
		if (is_array($data) && $data) {
			$formdata = http_build_query($data);
			curl_setopt($ch, CURLOPT_POST, true);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $formdata);
		}
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
		curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
		$result = curl_exec($ch);
		return (false===$result && false==$abort)? ( empty($data) ?  $this->doget($url) : $this->dopost($url, $data) ) : $result;
	}
	/**
	 * 发送手机短信
	 * @see kekezu_sms::send()
	 */
	public function send(){
		/*$sendurl = "http://sms.aoasp.com/api/sms/send.json?username=".self::$_params['username']."&password=".self::$_params['password']."&numbers=".self::$_params['to']."&content=".self::$_params['content'];
		$re = file_get_contents($sendurl);  
		print_r($re);  
		exit;*/
		
/*
		 $argv = array( 
			 'username'=>self::$_params['username'], 
			 'password'=>self::$_params['password'], 
			 'numbers'=>self::$_params['to'],
			 'content'=>self::$_params['content'],//短信内容
		 );
		 $result = $this->httprequest($sendurl,$argv);
		var_dump($result);
		exit;*/
	    $client = new nusoap('http://sms.aoasp.com/api/sms/send.json');
		$client->soap_defencoding = 'utf-8';
		$client->decode_utf8 = false;
		$client->xml_encoding = 'utf-8';
		$parameters	= array(self::$_params['username'],self::$_params['password'],'',self::$_params['to'],self::$_params['content'],'','0|0|0|0');
		//var_dump($parameters);die();
		$str=$client->call('clusterSend',$parameters);
		if (!($err=$client->getError())==null) {
			die("sms send error:".$err);
		}
		if(CHARSET=='utf-8'){
			$str = str_replace("GBK", "UTF-8", $str);
		}
		
		if(CHARSET == 'gbk'){
			$str = kekezu::utftogbk($str);
		}
		$obj = simplexml_load_string($str);
		$code = (int)$obj->code;
		//var_dump($code);die();
		if($code){
			return $this->error($code);
		}else{
			throw new Keke_exception($str);
		}
		//通过数组生成字符
		
		 
	}
 
	public function get_userinfo(){
		$client = new nusoap('http://sms.aoasp.com/api/sms/report/set.json',true);
		$client->soap_defencoding = 'utf-8';
		$client->decode_utf8 = false;
		$client->xml_encoding = 'utf-8';
		
		$parameters	= array(self::$_params['username'],self::$_params['password']);
		$str=$client->call('getUserInfo',$parameters);
		if (!($err=$client->getError())==null) {
			throw new Keke_exception("sms api error:".$err);
		}
		
		if(CHARSET=='utf-8'){
			$str = str_replace("GBK", "UTF-8", $str);
		}
		
		if(CHARSET == 'gbk'){
			$str = kekezu::utftogbk($str);
		}
		$obj = simplexml_load_string($str);
		$arr  =kekezu::objtoarray($obj);
		$user = array();
		$user['balance'] = (float)$obj->balance;
		$user['price'] =(float) $obj->smsPrice;
		
		return $user;
	}
	public function error($e){
		$err = array(
				'1000'=>'操作成功',
				'1001'=>'用户不存在或密码出错',
				'1002'=>'用户被停用',
				'1003'=>'余额不足',
				'1004'=>'请求频繁',
				'1005'=>'内容超长',
				'1006'=>'非法手机号码',
				'1007'=>'关键字过滤',
				'1008'=>'接收号码数量过多',
				'1009'=>'帐户过期',
				'1010'=>'参数格式错误',
				'1011'=>'其它错误',
				'1012'=>'数据库繁忙',
				'1013'=>'非法发送时间');
		return $err[$e];
	}
	
}