<?php 

class qq_oauth_client_class extends base_client_class{
	public $_qq_weibo_oauth;
	public $_qq_weibo_client;
    public $_auth_url = "";
    
	function __construct($app_key,$app_secret){
		$this->_app_key = $app_key;
		$this->_app_secret = $app_secret;
		parent::__construct($app_key,$app_secret);
		$_SESSION["scope"] = "get_user_info,add_share,list_album,add_album,upload_pic,add_topic,add_one_blog,add_weibo";
	}
	
	//获得授权链接的
	function get_auth_url($callback){
	
		$_SESSION['state'] = md5(uniqid(rand(), TRUE)); //CSRF protection
		$_SESSION['callback'] = $callback;
		$login_url = "https://graph.qq.com/oauth2.0/authorize?response_type=code&client_id="
				. $this->_app_key . "&redirect_uri=" . urlencode($callback)
				. "&state=" . $_SESSION['state']
				. "&scope=".$_SESSION["scope"];
		
		return $login_url;
	}
	
	//验证是否有授权
	function get_access_token(){
		return $_SESSION['auth_qq']['last_key'];
	}
	
	//销毁授权
	function clear_access_token(){
		
		unset($_SESSION ['auth_qq'] ['last_key']);
		unset( $_SESSION['auth_qq']['openid']);
	}
	
	/**
	 * 通过授权
	 * oauth_token=8226392397541222103&
	 * openid=E76A5A82F2904BD5E93E436DCA20AB08&
	 * oauth_signature=EnY68jM5GUWoDB%2F47Q%2Fq%2BQ0%2Bdfs%3D&
	 * oauth_vericode=1311817775&
	 * timestamp=1319172591
	 * 
	 * @see base_client_class::create_access_token()
	 */
	function create_access_token($oauth_verifier=false){
		global $oauth_vericode;
		
		if($_REQUEST['state'] == $_SESSION['state']) //csrf
		{
			$token_url = "https://graph.qq.com/oauth2.0/token?grant_type=authorization_code&"
					. "client_id=" . $this->_app_key. "&redirect_uri=" . urlencode($_SESSION['callback'])
					. "&client_secret=" . $this->_app_secret. "&code=" . $_REQUEST["code"];
		
			$response = file_get_contents($token_url);
			$params = array();
			parse_str($response, $params);
			$_SESSION['auth_qq']['last_key'] = $params["access_token"];
			
			//var_dump();
			$graph_url = "https://graph.qq.com/oauth2.0/me?access_token="
					. $_SESSION['auth_qq']['last_key'];
			$openid_str  = file_get_contents($graph_url);
			preg_match_all('/\((.*)\)/',$openid_str,$arr);
			
			$openid_arr = json_decode($arr[1][0]);
			
			$_SESSION['auth_qq']['openid'] = $openid_arr->openid;
			
			$_SESSION['auth_qq']['login_info'] = self::get_login_info();
			
			return true;
		}
		
	}
	
	private function init_client(){
		 
		
	}
	
	
	function get_login_info(){
		global $_K;
		
		if($_SESSION['auth_qq']['login_info']){
			return $_SESSION['auth_qq']['login_info'];
		}else{
			$get_user_info = "https://graph.qq.com/user/get_user_info?"
					. "access_token=" . $_SESSION['auth_qq']['last_key']
					. "&oauth_consumer_key=" . $this->_app_key
					. "&openid=" . $_SESSION['auth_qq']['openid']
					. "&format=json";
			$info = file_get_contents($get_user_info);
			$data = json_decode($info, true);
			
			if(strtolower($_K['charset'])=='gbk'){
				$data = kekezu::utftogbk($data);
			}
			
			return $data;
		}
		
		
		 
	}
	
	//微博更新
	function post_wb($msg,$img){
		 
	}
	
	//时间线
	function get_wb_list($page=0,$page_size=0){
		 
	}
	
	function get_wb_info($sid){
		 
	}
	
	
	
	//根据UID加关注
	function follow_wb_user($u_name){
		 
	}
	
	//根据SID转发一条微博
	function repost_wb($sid,$text=null){
		 
		
	}
	
	//根据SID评论一条问微博
	function send_comment($sid,$text=null){
		
		 
	}
	
	//用户数据格式化
	function user_data_format($data){
		$r = array();
		 
		if(!$data){
		 	return false;
		}
		$r['account'] = $data['nickname'];
		$r["name"]=$data['nickname'];
		$r["location"]="";//$data['location'];
		$r['img']=$data['figureurl'];
		$r['url']="";
	 	$r['wb_count']="";
		$r['sex'] = $data['gender'];
		 
		return $r;
	}
	
	//微博数据格式化
	function wb_data_format($data){
		$r = array();
		$r['id']=$data['id'];
		$r['text']=$data['origtext'];
		$r['uid']=$data['name'];
		$r['username']=$data['nick'];
		$r['img'] = $data['image'][0]?$data['image'][0].'/120':null;
		$r['url']="http://t.qq.com/p/t/{$r['id']}";
		return $r;
	}
	
	
	
	function get_operate(){
		return $this->_qq_weibo_oauth;
	}
	
	function get_client(){
		return $this->_qq_weibo_client;
	}
	
	function do_post($url, $data){
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
		curl_setopt($ch, CURLOPT_POST, TRUE);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
		curl_setopt($ch, CURLOPT_URL, $url);
		$ret = curl_exec($ch);
		curl_close($ch);
		return $ret;
	}
	function get_url_contents($url){
		if (ini_get("allow_url_fopen") == "1")
			return file_get_contents($url);
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
		curl_setopt($ch, CURLOPT_URL, $url);
		$result =  curl_exec($ch);
		curl_close($ch);
		return $result;
	}
}
