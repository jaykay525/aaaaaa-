<?php
// +----------------------------------------------------------------------
// | TOPThink [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2010 http://topthink.com All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 麦当苗儿 <zuojiazi.cn@gmail.com> <http://www.zjzit.cn>
// +----------------------------------------------------------------------

class ThinkWechat {
	public $token  = '';
	public $appid  = '';
	public $secret = '';

	/**
	 * 微信推送过来的数据或响应数据
	 * @var array
	 */
	private $data = array();

	/**
	 * 构造方法，用于实例化微信SDK
	 * @param string $token 微信开放平台设置的TOKEN
	 */
	public function __construct($token, $is_menu = false){
		//$postStr = $GLOBALS["HTTP_RAW_POST_DATA"];
		$token = $token ? $token : $this->token;
		// $this->logs('TOKEN：' . $token);
		// $this->logs('GET：' . print_r($_GET, true));
		// $this->logs('POST：' . print_r($_POST, true));

		if($is_menu == false){
			$this->auth($token) || exit;
			if(request()->isGet()){
				exit($_GET['echostr']);
			} else {
				$xml = file_get_contents("php://input");
				$xml = new SimpleXMLElement($xml);
				$xml || exit;

		        foreach ($xml as $key => $value) {
		        	$this->data[$key] = strval($value);
		        }
			}
		}
	}

	/**
	 * 获取微信推送的数据
	 * @return array 转换为数组后的数据
	 */
	public function request(){
		
		$this->logs('微信平台POST：' . print_r($this->data, true));

       	return $this->data;
	}

	/**
	 * * 响应微信发送的信息（自动回复）
	 * @param  string $to      接收用户名
	 * @param  string $from    发送者用户名
	 * @param  array  $content 回复信息，文本信息为string类型
	 * @param  string $type    消息类型
	 * @param  string $flag    是否新标刚接受到的信息
	 * @return string          XML字符串
	 */
	public function response($content, $type = 'text', $flag = 0){
		/* 基础数据 */
		$this->data = array(
			'ToUserName'   => $this->data['FromUserName'],
			'FromUserName' => $this->data['ToUserName'],
			'CreateTime'   => NOW_TIME,
			'MsgType'      => $type,
		);

		/* 添加类型数据 */
		$this->$type($content);

		/* 添加状态 */
		$this->data['FuncFlag'] = $flag;

		$this->logs('微信助手POST：' . print_r($this->data, true));

		/* 转换数据为XML */
		$xml = new SimpleXMLElement('<xml></xml>');
		$this->data2xml($xml, $this->data);
		exit($xml->asXML());
	}

	/**
	 * 回复文本信息
	 * @param  string $content 要回复的信息
	 */
	private function text($content){
		$this->data['Content'] = $content;
	}

	/**
	 * 回复音乐信息
	 * @param  string $content 要回复的音乐
	 */
	private function music($music){
		list(
			$music['Title'], 
			$music['Description'], 
			$music['MusicUrl'], 
			$music['HQMusicUrl']
		) = $music;
		$this->data['Music'] = $music;
	}

	/**
	 * 回复图文信息
	 * @param  string $news 要回复的图文内容
	 */
	private function news($articles){
		// $articles = array();
		// foreach ($news as $key => $value) {
		// 	list(
		// 		$articles[$key]['Title'],
		// 		$articles[$key]['Description'],
		// 		$articles[$key]['PicUrl'],
		// 		$articles[$key]['Url']
		// 	) = $value;
		// 	if($key >= 9) { break; } //最多只允许10调新闻
		// }
		$this->data['ArticleCount'] = count($articles);
		$this->data['Articles'] = $articles;
	}

	/**
     * 数据XML编码
     * @param  object $xml  XML对象
     * @param  mixed  $data 数据
     * @param  string $item 数字索引时的节点名称
     * @return string
     */
    private function data2xml($xml, $data, $item = 'item') {
        foreach ($data as $key => $value) {
            /* 指定默认的数字key */
            is_numeric($key) && $key = $item;

            /* 添加子元素 */
            if(is_array($value) || is_object($value)){
                $child = $xml->addChild($key);
                $this->data2xml($child, $value, $item);
            } else {
            	if(is_numeric($value)){
            		$child = $xml->addChild($key, $value);
            	} else {
            		$child = $xml->addChild($key);
	                $node  = dom_import_simplexml($child);
				    $node->appendChild($node->ownerDocument->createCDATASection($value));
            	}
            }
        }
    }

    /**
	 * 对数据进行签名认证，确保是微信发送的数据
	 * @param  string $token 微信开放平台设置的TOKEN
	 * @return boolean       true-签名正确，false-签名错误
	 */
	private function auth($token){
		/* 获取数据 */
		$timestamp = $_GET['timestamp'];
		$nonce     = $_GET['nonce'];
		$signature = $_GET['signature'];
		$sign      = $_GET['signature'];

		// $data = array($_GET['timestamp'], $_GET['nonce'], $token);

		
		$data = array($token, $timestamp, $nonce);

		/* 对数据进行字典排序 */
		sort($data,SORT_STRING);

		/* 生成签名 */
		$tmpdata = implode($data);
		$signature = sha1($tmpdata);

		if($signature == $sign){
			// Log::write('签名：成功！', Log::INFO);
		}else{
			$this->logs('获取GET参数：' . print_r($_GET, true));
			$this->logs('签名：失败->本地[' . $signature . ']，微信[' . $sign . ']');
		}

		return $signature === $sign;
	}

	/**
	 * 自定义菜单TOKEN
	 * @return [string] ACCESS_TOKEN
	 */
	private function get_access_token(){
		$appid  = $this->appid;
		$secret = $this->secret;
		if(!$appid || !$secret){
			$this->logs('未设置AppId和Secret！');
			return false;
		}
		$url = 'https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=' . $appid . '&secret=' . $secret;

		$get_return = file_get_contents($url);
		if($get_return){
			$get_data = json_decode($get_return, true);
			$token = $get_data['access_token'];
			if(!$token){
				$this->logs('微信助手GET：获取access_token失败，请确认appid或secret是否正确！');
				// exit( '获取access_token失败！' );
				return false;
			}
		}
		return $token;
	}
	/**
	 * 修改菜单栏目
	 * @param [arrray] $memu 微信菜单栏目
	 */
	public function set_menu($json_menu){
		$access_token = $this->get_access_token();

		$url = 'http://api.weixin.qq.com/cgi-bin/menu/create?access_token='.$access_token;

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
		curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (compatible; MSIE 5.01; Windows NT 5.0)');
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
		curl_setopt($ch, CURLOPT_AUTOREFERER, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $json_menu);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		$respose_data = curl_exec($ch);
		if (curl_errno($ch)) {
			return curl_error($ch);
		}
		curl_close($ch);

		// var_dump($respose_data);

		if(!$respose_data){
			$this->logs('微信助手POST：请求修改菜单接口通讯失败！！');
			exit( '请求修改菜单接口通讯失败！' );
		}
		$array_data = json_decode($respose_data,true);
		if($array_data['errcode']==0){
			$status = true;
			$this->logs('微信助手POST：修改菜单成功！');
		}else{
			$status = false;
			$this->tips_info = $array_data['errcode'];
			$this->logs('微信助手POST：修改菜单失败['.$array_data['errcode'].']！');
		}
		return $status;
	}
	public function get_menu(){
		$access_token = $this->get_access_token();
		$url = 'https://api.weixin.qq.com/cgi-bin/menu/get?access_token='.$access_token;
		$data = file_get_contents($url);
		return $data;
	}
	public function del_menu(){
		$access_token = $this->get_access_token();
		$url = 'https://api.weixin.qq.com/cgi-bin/menu/delete?access_token='.$access_token;
		$data = file_get_contents($url);
		return $data;
	}
	public function get_web_code($redirect_url,$scope='snsapi_base',$state='200'){
		$redirect_url = str_replace(':80', '', $redirect_url);
		$redirect_url = urlencode($redirect_url);
		$url = 'https://open.weixin.qq.com/connect/oauth2/authorize?appid='.$this->appid.'&redirect_uri='.$redirect_url.'&response_type=code&scope='.$scope.'&state='.$state.'#wechat_redirect';
		header('Location: ' . $url);
	}
	public function get_code_openid($code=''){
		$url = 'https://api.weixin.qq.com/sns/oauth2/access_token?appid='.$this->appid.'&secret='.$this->secret.'&code='.$code.'&grant_type=authorization_code';
		$get_return = file_get_contents($url);
		if($get_return){
			$get_data = json_decode($get_return,true);
			$openid = $get_data['openid'];
			if(!$openid){
				$this->logs('微站：通过code获取用户openid['.$code.']！');
				$this->logs('微站：返回结果['.print_r($get_data, true).']！');
			}
		}
		return $openid;
	}
	/**
	 * 获取用户基本信息(UnionID机制)
	 * @param  [type] $openid 用户ID
	 */
	public function get_openid_userinfo($openid){
		$access_token = $this->get_access_token();
		$url = 'https://api.weixin.qq.com/cgi-bin/user/info?access_token='.$access_token.'&openid='.$openid.'&lang=zh_CN';
		$get_return = file_get_contents($url);
		if($get_return){
			$get_data = json_decode($get_return, true);
			$this->logs('通过openid获取用户信息：'.print_r($get_data, true));
		}
		return $get_data;
	}
	/**
	 * 获取二维码URL地址
	 * @param  integer $scene_id       场景值ID，临时二维码时为32位非0整型，永久二维码时最大值为100000（目前参数只支持1--100000）
	 * @param  array   $params         二维码携带参数
	 * @param  string  $action_name    二维码类型，QR_SCENE为临时,QR_LIMIT_SCENE为永久,QR_LIMIT_STR_SCENE为永久的字符串参数值
	 * @param  integer $expire_seconds 该二维码有效时间，以秒为单位。 最大不超过2592000（即30天），此字段如果不填，则默认有效期为30秒。
	 */
	public function get_qrcode_url($scene_id = 1, $action_name = 'QR_SCENE', $expire_seconds = 2592000){
		$access_token = $this->get_access_token();
		$url = 'https://api.weixin.qq.com/cgi-bin/qrcode/create?access_token=' . $access_token;
		$post_data['expire_seconds'] = $expire_seconds;
		$post_data['action_name']    = $action_name;
		$post_data['action_info']['scene']['scene_id'] = $scene_id;
		$post_josn = json_encode($post_data);
		$return_json = http($url, $post_josn, 'POST');
		$return_data = json_decode($return_json, true);
		$qrcode_url  = $return_data['url'];

		return $qrcode_url;
	}
	/**
	 * 发送模板消息
	 * @param  [type] $openid      接收用户OPENID
	 * @param  string $template_id 模板ID
	 * @param  [type] $msg_data    消息模板替换数据，如array('user_name' => array('value' => '黄三', 'color' => '#173177'))
	 * @param  string $msg_url     打开地址
	 */
	public function send_tpl_msg($openid = '', $template_id = '', $msg_data = array(), $msg_url = ''){
		if(!$openid || !$template_id || !$msg_data){
			return false;
		}
		$access_token = $this->get_access_token();
		$url = 'https://api.weixin.qq.com/cgi-bin/message/template/send?access_token=' . $access_token;
		$data['touser']      = $openid;
		$data['template_id'] = $template_id;
		$data['url']         = $msg_url ? $msg_url : '';
		$data['topcolor']    = '#FF0000';
		$data['data']        = $msg_data;
		$this->logs(print_r($data, true));
		$json = json_encode($data);
		$return_json = http($url, $json);
		$return_data = json_decode($return_json, true);
		$msgid  = $return_data['msgid'];
		$this->logs(print_r($return_data, true));

		return $msgid ? $msgid : false;
	}

	public function get_js_sign_params() {
		$jsapiTicket = $this->jsapi_ticket();

		// 注意 URL 一定要动态获取，不能 hardcode.
		$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
		$url = "$protocol$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";

		$timestamp = time();
		$nonceStr = $this->createNonceStr();

		// 这里参数的顺序要按照 key 值 ASCII 码升序排序
		$string = "jsapi_ticket=$jsapiTicket&noncestr=$nonceStr&timestamp=$timestamp&url=$url";

		$signature = sha1($string);

		$signPackage = array(
			"appId"     => $this->appid,
			"nonceStr"  => $nonceStr,
			"timestamp" => $timestamp,
			"url"       => $url,
			"signature" => $signature,
			"rawString" => $string
		);
		return $signPackage; 
  }
	public function jsapi_ticket(){
		$jsapi_ticket = cache('weixin_jsapi_ticket');
		if($jsapi_ticket){
			return $jsapi_ticket;
		}

		$access_token = $this->get_access_token();
		$url = "https://api.weixin.qq.com/cgi-bin/ticket/getticket?access_token=" . $access_token . "&type=jsapi";
		$get_return = file_get_contents($url);
		if($get_return){
			$get_data = json_decode($get_return, true);
			$jsapi_ticket = $get_data['ticket'];
			$expires_in   = $get_data['expires_in'];
			if($jsapi_ticket){
				cache('weixin_jsapi_ticket', $jsapi_ticket, $expires_in ? $expires_in : 7200);
			}else{
				$this->logs('获取jsapi_ticket：' . print_r($get_data, true));
			}
		}

		return $jsapi_ticket;
	}
	private function createNonceStr($length = 16) {
		$chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
		$str = "";
		for ($i = 0; $i < $length; $i++) {
			$str .= substr($chars, mt_rand(0, strlen($chars) - 1), 1);
		}
		return $str;
	}
	public function logs($content){
		\Think\Log::write($content, \Think\Log::INFO, '', LOG_PATH. 'weixin_' . date('y_m_d').'.log');
	}
}
