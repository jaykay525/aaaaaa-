<?php
/**
* 短信类
*/
class Sms{
	public $smscode = '';
	protected $autoCheckFields = false;
	public $send_type;//发送类型
	public $err_info = array(
			-1  =>'没有该用户账户',
			-2  =>'密钥不正确',
			-3  =>'短信数量不足',
			-11 =>'该用户被禁用',
			-14 =>'短信内容出现非法字符',
			-4  =>'手机号格式不正确',
			-41 =>'手机号码为空',
			-42 =>'短信内容为空',
			-51 =>'短信签名格式不正确',
		);

	/**
	 * 发送短信
	 * @param  [type] $mobiles 手机号
	 * @param  [type] $content 短信内容
	 */
	public function send($mobiles, $content){
		//获取发送类型
		$send_type = $this->send_type ? $this->send_type : model('Config')->_get('type','sms');
		$send_type_desc = array(
		    'ms'            => 'ms_send',    //microsoco
            'isms'          => 'isms_send',  //isms
			'cl'            => 'cl_send',   //创蓝
			'b2m'           => '_send', //（亿美）
			'webchinese'    => 'wc_send',   //中国网建
			'yunpian'       => 'yunpian_send',  //云片网短信
			'yunpian_voice' => 'yunpian_voice_send',    //云片网语音
			'vcomcn'        => 'vcomcn_send',   //集时通
		);
		$send_func = $send_type_desc[$send_type];
		$send_func = $send_func ? $send_func : 'cl_send';
		$content   = ($send_type == 'yunpian_voice' && $this->smscode) ? $this->smscode : $content;

		//过虑手机号
		$filter_mobile_str   = model('Config')->_get('filter_mobile', 'filter_sms');
		$filter_mobile_array = explode("\r\n", trim($filter_mobile_str));
		//存在过滤手机号直接返回
		if(in_array($mobiles, $filter_mobile_array)){
			$result['status'] = 400;
			$result['info']   = '发送失败，手机号被过滤！';
		}else{
			$result = $this->$send_func($mobiles, $content);
		}

		//是否记录短信发送记录
		if(model('Config')->_get('is_record_send_logs','sms')){
			$data['send_type']   = $send_type;
			$data['mobiles']     = $mobiles;
			$data['content']     = $content;
			$data['create_time'] = time();
			$data['status']      = $result['status'] ?$result['status'] : '';
			$data['result']      = $result['info'] ? $result['info'] : '';
			db('SmsSendLogs')->insert($data);
		}

		return $result;
	}

    /***
     * microsoco 发送短信
     * @param $mobiles
     * @param string $content
     * @return mixed
     */
    public function ms_send($mobiles,$content=''){
        $account  = model('Config')->_get('ms_account','sms');
        $userid = model('Config')->_get('ms_userid','sms');
        $password = model('Config')->_get('ms_password','sms');
        $params['action'] = 'send';
        $params['account'] = iconv('UTF-8', 'GB2312',$account);
        $params['userid']    = iconv('UTF-8', 'GB2312',$userid);
        $params['password']    = iconv('UTF-8', 'GB2312',$password);
        $params['mobile']  = $mobiles;
        $params['content']     = $content;

        $url='http://sms.microsoco.com/sms.aspx';
        $post_data = http_build_query($params);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL,$url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
        $result   = curl_exec($ch);

        //记录CURL错误
        if(!$result){

            return [
               'status' => 500,
               'info' => 'curl_error_no:'.curl_errno($ch)

            ];

        }

        $result = $this->xml2array($result);

        if($result['returnstatus'] == 'Success'){
            $info['status'] = 200;
            $info['info']   = '发送成功！';
        }else{
            $info['status'] = 404;
            $info['info']   = $result['message'];
        }
        return $info;
    }


    /***
     * isms 发送短信
     * @param $mobiles
     * @param string $content
     * @return mixed
     */
    public function isms_send($mobiles,$content=''){
        $account  = model('Config')->_get('isms_account','sms');
        $password = model('Config')->_get('isms_password','sms');
        $params['un'] = iconv('UTF-8', 'GB2312',$account);
        $params['pwd']    = iconv('UTF-8', 'GB2312',$password);
        $params['dstno']  = $mobiles;
        $params['msg']     = $content;
        $params['type'] = 2;
        $params['agreedterm'] = 'YES';

        $url='https://www.isms.com.my/isms_send.php';
        $params_data = http_build_query($params);


        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL,$url.'?'.$params_data);
        curl_setopt($ch, CURLOPT_POST, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
        $result   = curl_exec($ch);

        // only test
//        $result = '2000 = SUCCESS';
        $result = explode('=', $result);

        if(trim($result[0]) == 2000){
            $info['status'] = 200;
            $info['info']   = '发送成功！';
        }else{
            $info['status'] = 404;
            $info['info']   = $result['message'];
        }

        return $info;
    }

	/**
	 * 发送短信(中国网建)
	 * @param  [type] $mobiles 手机号
	 * @param  [type] $content 短信内容
	 */
	public function wc_send($mobiles,$content){
		$uid = model('Config')->_get('wc_uid','sms');
		$key = model('Config')->_get('wc_key','sms');

		$mobiles = trim($mobiles);
		$content = trim($content);
		$content = urlencode($content);

		$url = 'http://utf8.sms.webchinese.cn/?Uid='.$uid.'&Key='.$key.'&smsMob='.$mobiles.'&smsText='.$content;

		$ch = curl_init();
		$timeout = 5;
		curl_setopt ($ch, CURLOPT_URL, $url);
		curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt ($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
		$sms_id = curl_exec($ch);
		curl_close($ch);
		
		if($sms_id > 0){
			$info['status'] = 200;
			$info['info']   = '发送成功！';
		}else{
			$msg = $this->err_info[$sms_id];
			$info['status'] = 404;
			$info['info']   = $msg?$msg:'[' . $sms_id . ']未知错误！';
		}
		return $info;
	}
	/**
	 * 发送短信（亿美）
	 * @param  string $mobiles 手机号
	 * @param  [type] $content 内容
	 */
	public function _send($mobiles='',$content){
		$url = 'http://sdkhttp.eucp.b2m.cn/sdkproxy/sendsms.action';
		$params['cdkey']    = model('Config')->_get('b2m_cdkey','sms');
		$params['password'] = model('Config')->_get('b2m_password','sms');
		$params['phone']    = $mobiles;
		$params['message']  = $content;

		$url = $url . '?' . http_build_query($params);

		$ch = curl_init();
		$timeout = 10;
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
		$xml_data = curl_exec($ch);
		curl_close($ch);
		$data = xml_to_array(trim($xml_data));
		$sms_code = $data['error'][0];
		$sms_message = $data['message'][0];
		if($sms_code == 0){
			$info['status'] = 200;
			$info['info']   = '发送成功！';
		}else{
			$info['status'] = 404;
			$info['info']   = '未知错误['.$sms_code.']->'.$sms_message;
		}
		return $info;
	}
	/**
	 * 创蓝发送短信
	 * @param  [type] $mobiles 手机号
	 * @param  string $content 短信内容
	 */
	public function cl_send($mobiles,$content=''){
		$content  = urlencode($content);
		$account  = model('Config')->_get('cl_account','sms');
		$password = model('Config')->_get('cl_password','sms');
		$params['account'] = iconv('UTF-8', 'GB2312',$account);
		$params['pswd']    = iconv('UTF-8', 'GB2312',$password);
		$params['mobile']  = $mobiles;
		$params['msg']     = mb_convert_encoding($content,'UTF-8', 'GB2312');

		// $url = 'http://222.73.117.158/msg/HttpSendSM'; //单发
		$url='http://222.73.117.158/msg/HttpBatchSendSM'; //群发
		$post_data = http_build_query($params);

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL,$url);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
		$result   = curl_exec($ch);

		$sms_code = 404;
		if($result){
			$res_data = explode(',', $result);
			$sms_code = $res_data[1];
		}
		$status_desc = array(
			0   => '提交成功',
			101 => '无此用户',
			102 => '密码错',
			103 => '提交过快（提交速度超过流速限制',
			104 => '系统忙（因平台侧原因，暂时无法处理提交的短信）',
			105 => '敏感短信（短信内容包含敏感词）',
			106 => '消息长度错（>536或<=0）',
			107 => '包含错误的手机号码',
			108 => '手机号码个数错（群发>50000或<=0;单发>200或<=0）',
			109 => '无发送额度（该用户可用短信数已使用完）',
			110 => '不在发送时间内',
			111 => '超出该账户当月发送额度限制',
			112 => '无此产品，用户没有订购该产品',
			113 => 'extno格式错（非数字或者长度不对）',
			115 => '自动审核驳回',
			116 => '签名不合法，未带签名（用户必须带签名的前提下）',
			117 => 'IP地址认证错,请求调用的IP地址不是系统登记的IP地址',
			118 => '用户没有相应的发送权限',
			119 => '用户已过期',
		);

		$sms_message = $status_desc[$sms_code];
		if($sms_code == 0){
			$info['status'] = 200;
			$info['info']   = '发送成功！';
		}else{
			$info['status'] = 404;
			$info['info']   = ($sms_message ? $sms_message : '未知错误') . '，代码：' . $sms_code;
		}
		return $info;
	}
	//云片网短信发送
	public function yunpian_send($mobiles, $content = ''){

		$params['apikey'] = model('Config')->_get('yunpian_apikey', 'sms');
		$params['mobile'] = $mobiles;
		$params['text']   = $content;

		$post_data = http_build_query($params);

		$header = array(
			'Accept:text/plain',
			'charset=utf-8',
			'Content-Type:application/x-www-form-urlencoded',
			'charset=utf-8'
		);

		$url = 'http://yunpian.com/v1/sms/send.json';
		$ch  = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
		$json = curl_exec($ch);
		$data = json_decode($json, true);

		if($data['code'] == 0 && $data['msg'] == 'OK'){
			$info['status'] = 200;
			$info['info']   = '发送成功！';
		}else{
			$info['status'] = 404;
			$info['info']   = 'code:' . $data['code'] . '，' . $data['msg'];
		}
		
		return $info;
	}
	//云片网语音发送
	public function yunpian_voice_send($mobile, $code = ''){

		$params['apikey'] = model('Config')->_get('yunpian_apikey', 'sms');
		$params['mobile'] = $mobile;
		$params['code']   = $code;

		$post_data = http_build_query($params);

		$header = array(
			'Accept:text/plain',
			'charset=utf-8',
			'Content-Type:application/x-www-form-urlencoded',
			'charset=utf-8'
		);

		$url = 'http://voice.yunpian.com/v1/voice/send.json';
		$ch  = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
		$json = curl_exec($ch);
		$data = json_decode($json, true);
		
		if($data['code'] == 0 && $data['msg'] == 'OK'){
			$info['status'] = 200;
			$info['info']   = '发送成功！';
		}else{
			$info['status'] = 404;
			$info['info']   = 'code:' . $data['code'] . '，' . $data['msg'];
		}
		
		return $info;
	}
	//集时通
	public function vcomcn_send($mobile = '', $content = '', $send_time = ''){
		$account   = model('Config')->_get('vcomcn_account', 'sms');
		$password  = model('Config')->_get('vcomcn_password', 'sms');
		$send_time = $send_time ? date('Y-m-d H:i:s', $send_time) : '';
		$content   = iconv('utf-8', 'gbk', $content);
		$xml_data  = '<Group Login_Name="' . $account . '" Login_Pwd="' . $password . '" OpKind="0" InterFaceID=""><E_Time>' . $send_time . '</E_Time> <Item><Task> <Recive_Phone_Number>' . $mobile . '</Recive_Phone_Number> <Content><![CDATA[' . $content . ']]></Content> <Search_ID>1</Search_ID></Task></Item></Group>';

		$url = 'http://userinterface.vcomcn.com/Opration.aspx';
		$ch  = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $xml_data);
		$code = curl_exec($ch);
		if($code == '00'){
			$info['status'] = 200;
			$info['info']   = '发送成功！';
		}else{
			$return_code = array(
				'00' => '提交成功',
				'01' => '账号或密码错误',
				'02' => '账号欠费',
				'09' => '无效的接收方号码',
				'10' => '网络或系统内部错误',
			);
			$info['status'] = 404;
			$info['info']   = 'code:' . $code . '，' . $return_code[$code];
		}
		return $info;
	}


    /***
     * xml数据转成数组
     * @param $xmlData
     * @return bool|mixed
     */
    public function xml2array($xmlData) {
        if ($xmlData) {
            $xmlData = simplexml_load_string($xmlData, 'SimpleXMLElement', LIBXML_NOCDATA);
            if (! is_object($xmlData)) {
                return false;
            }
            $array = json_decode(json_encode($xmlData), true); // xml对象转数组
            return $array;
        } else {
            return false;
        }
	}

}