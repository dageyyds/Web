<?php 
/**
 * 微信开发类
 */

class WeChat{
	private $appid;
	private $appsecret;

	public function _construct($id,$secret){
		$this->appid=$id;
		$this->appsecret=$secret;
	}

	/**
	 * 获取access_token
	 *@param string $token_file 用来存储token的临时文件
	 */
	public function getAccessToken($token_file='./access_token'){
		//access_token会过期的，所以把access_token存储在某个文件中
		$life_time=7200;
		if(file_exists($token_file) && filemtime($token_file)>time()-$life_time){
			//存在且有效的access_token
			return file_get_contents($token_file);
		}
		//目标URL
		$url="https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid={$this->appid}&secret={$this->appsecret}";
		$result=$this->_requestGet($url);
		if(!$result){
			return false;
		}
		//存在返回相应结果
		$result_obj=json_decode($result);
		file_put_contents($token_file, $result_obj->access_token);
		return $result_obj['access_token'];
	}

	/**
	 * 返回ticket
	 *@return string ticket
	 */
	public function getQRCodeTicket($content){
		$access_token=$this->getAccessToken();
		$url="URL: https://api.weixin.qq.com/cgi-bin/qrcode/create?access_token={$access_token}";
		$data='{"expire_seconds": 604800, "action_name": "QR_SCENE", "action_info": {"scene": {"scene_id": '.$content.'}}}';
		$result=$this->_requestPost($url,$data);
		if(!$result){
			return false;
		}
		$result_obj=json_decode($result);
		return $result_obj->ticket;
	}

	public function getQRCode(){

	}

	private function _requestPost($url,$data=array(),$ssl=true){

	}


	/**
	 * 发送get请求方法
	 *@param string $url URL
	 *@param boll $ssl 是否为https协议
	 *@return string 相应主题content
	 */
	private function _requestGet($url,$ssl=ture){
		//curl初始化
		$curl=curl_init();
		curl_setopt($curl, CURLOPT_URL, $url);	//URL
		$user_agent=isset($_SERVER['HTTP_USER_AGENT'])?$_SERVER['HTTP_USER_AGENT']:'Mozilla/5.0 (Windows NT 6.1; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/51.0.2704.106 Safari/537.36';
		curl_setopt($curl, CURLOPT_USERAGENT, $user_agent);		//请求代理信息
		curl_setopt($curl, CURLOPT_AUTOREFERER, true);		//referer头，请求来源
		//SSL相关
		if($ssl){
			curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);	//禁用后，cURL将终止从服务器端进行验证
			curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 1);	//检查服务器ssl证书中是否存在一个公用名(common name)
		}
		curl_setopt($curl, CURLOPT_HEADER, false);		//是否处理响应头
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);	//是否返回相应结果

		// 发出请求
		$response=curl_exec($curl);
		if($response===false){
			echo curl_error($curl),'<br />';
			return false;
		}
		return $response;
	}
}