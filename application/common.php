<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 流年 <liu21st@gmail.com>
// +----------------------------------------------------------------------

// 应用公共文件
use \think\Url;
use think\Cache;
use JPush\Client as JPush;

// 异常错误报错级别,
error_reporting(E_ERROR | E_PARSE );

if (!function_exists('url')) {
	/**
	 * 重写Url生成
	 * @param string        $url 路由地址
	 * @param string|array  $vars 变量
	 * @param bool|string   $suffix 生成的URL后缀
	 * @param bool|string   $domain 域名
	 * @return string
	 */
	function url($url = '', $vars = '', $suffix = true, $domain = true){
		$new_url = Url::build($url, $vars, $suffix, $domain);
		$is_acl  = 1;//有权限
		if(BIND_MODULE == 'admin'){
			$model  = model('AdminRole');
			$is_acl = $model->url_is_acl($new_url, 1);
		}

	    return $new_url . ($is_acl ? '' : '#delete-link');
	}
}

/**
 * 请求HTTP数据
 * @param  [type] $url     完整URL地址
 * @param  string $params  GET、POST参数
 * @param  string $method  提交方式GET、POST
 * @param  array  $header  Header参数
 */
function http($url, $params = '', $method = 'GET', $header = array(), $agent = array()){
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE); 
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
	curl_setopt($ch, CURLOPT_TIMEOUT, 30);
	if(strtoupper($method) == 'POST' && !empty($params)){
		curl_setopt($ch, CURLOPT_POST, TRUE);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
	}
	if(strtoupper($method) == 'GET' && $params){
		$query_str = http_build_query($params);
		$url = $url . '?' . $query_str;
	}
	curl_setopt($ch, CURLOPT_URL, $url);
	if(!empty($agent)){
		curl_setopt($ch, CURLOPT_PROXY, $agent['ip']); //代理服务器地址
		curl_setopt($ch, CURLOPT_PROXYPORT, $agent['port']); //代理服务器端口
		//http代理认证帐号，username:password的格式
		if($agent['username'] && $agent['password']){
			curl_setopt($ch, CURLOPT_PROXYUSERPWD, $agent['username'] . ":" . $agent['password']); 
			curl_setopt($ch, CURLOPT_PROXYTYPE, CURLPROXY_HTTP); //使用http代理模式
		}
	}
	curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (compatible; MSIE 5.01; Windows NT 5.0)');
	$response = curl_exec($ch);
	if (curl_errno($ch)) {
		return curl_error($ch);
	}
	curl_close($ch);

	return $response;
}

/**
 * 设置图片宽高
 * @param  string $images_url 原图片URL
 * @param  string $width      宽度
 * @param  string $height     高度
 * @return string             图片URL
 */
function img($images_url = '', $width = '', $height = '', $ext = 'jpg'){
	$img_ext  = array('jpg', 'png', 'bmp', 'gif', 'jpeg');
	$file_ext = in_array($ext, $img_ext) ? $ext : 'jpg';
	if($width && $height){
		$images_url = $images_url ? $images_url : request()->domain() . '/public/images/default.png';
		// 阿里云OSS格式（TODO 暂时不使用）
		// $images_url = $images_url . '@' . $width . 'w_' . $height . 'h_1e_1c.' . $file_ext;
		// $images_url = $images_url . '!w' . $width . '_h' . $height . '.jpg';
	}
	return $images_url;
}

/**
 * 获取类型描述
 * @param  [type] $key  KEY
 * @param  [type] $type 类型
 */
function get_type_desc($key = '', $type = ''){
	$type_desc = config('type_desc.' . $type);
	foreach (explode(',', $key) as $k) {
		$desc[] = $type_desc[$k];
	}
	$desc = implode(',', $desc);
	return $desc;
}
/**
 * 获取类型说明清单
 * @param  string $type [description]
 * @return [type]       [description]
 */
function get_type_desc_list($type = ''){
	$type_desc = config('type_desc.' . $type);
	foreach ($type_desc as $key => $val) {
		$rs['val']  = $key;
		$rs['name'] = $val;
		$data[] = $rs;
	}
	return $data ? $data : array();
}
/**
 * 生成订单号
 */
function generate_order_sn(){
	//随机生成2位数
	$chars = str_repeat('12356789', 3);
	$chars = str_shuffle($chars);

	$order_sn = substr($chars, 0, 2).substr(date('YmdHis'), 2) . \TPString::randString(2, 1);
	return $order_sn;
}
/**
 * 生成唯一编号
 */
function generate_id($len = 12){
	//随机生成2位数
	$chars    = str_repeat('12356789', 3);
	$chars    = str_shuffle($chars);
	$uid      = time() . ($len > 10 ? (\TPString::randString($len - 10, 1)) : '');

	return $uid;
}

/**
 * 密码加密
 * @param  string $username 用户名
 * @param  string $password 明文密码
 * @param  string $code     密钥
 * @return string           加密后密码
 */
function password_md5($username = '', $password = '', $code = 'tcms_'){
	$newpassword = md5($code . md5($password) . md5($username));
	return $newpassword;
}
/**
 * 密码加密（一次MD5加密）
 * @param  string $username 用户名
 * @param  string $password 一次MD5加密
 * @param  string $code     密钥
 * @return string           加密后密码
 */
function password_md5_one($username = '', $password = '', $code = 'tcms_'){
	$newpassword = md5($code . ($password) . md5($username));
	return $newpassword;
}

function json($info = '', $data = '', $status = ''){
	$json['info']   = $info ? $info : '';
	$json['data']   = $data ? $data : null;
	$json['status'] = $status ? $status : 200;
	header('Content-Type:application/json; charset=utf-8');
	exit(json_encode($json, JSON_UNESCAPED_UNICODE));
}


/**
 * 计算文件大小描述
 * @param  integer $filesize 文件大小bytes
 */
function filesize_desc($filesize = 0) {
	if($filesize >= 1073741824) {
		$filesize = round($filesize / 1073741824 * 100) / 100 . ' GB';
	} else if($filesize >= 1048576) {
		$filesize = round($filesize / 1048576 * 100) / 100 . ' MB';
	} else if($filesize >= 1024) {
		$filesize = round($filesize / 1024 * 100) / 100 . ' KB';
	} else {
		$filesize = $filesize . ' bytes';
	}
	return $filesize;
}

/**
 * 与当前时间差多少文字说明
 * @param  [type] $btime [description]
 * @return [type]        [description]
 */
function time_ago_desc($btime){  
	$result = '';  
	$time = time() - $btime ;
	if($time <= 5){
		$result = '刚刚';  
	}
	else if($time < 60){  
		$result = $time . '秒前';  
	}  
	else if($time < 3599){  
		$result = floor($time / 60) . '分钟前';  
	}else if($time < 86400){  
		$result = floor($time / 3600) . '小时前';  
	}else{  
		$zt = strtotime(date('Y-m-d 00:00:00'));  
		$qt = strtotime(date('Y-m-d 00:00:00', strtotime("-1 day")));
		$st = strtotime(date('Y-m-d 00:00:00', strtotime("-2 day")));
		$bt = strtotime(date('Y-m-d 00:00:00', strtotime("-7 day")));
		if($btime < $qt){  
			$result = floor($time / 86400) . '天前';  
		}else{  
			$result = '昨天';  
		}
	}
	return $result;  
}

/**
 * 与当前时间差多少文字说明(app对应)
 * @param  [type] $btime [description]
 * @return [type]        [description]
 */
function time_ago_desc_app($btime){
    $result = '';
    $time = time() - $btime;
    if($time <= 60){
        $result = '刚刚';
    }
    else if($time < 3599){
        $result = floor($time / 60) . '分钟前';
    }else if($time < 86400){
        $result = floor($time / 3600) . '小时前';
    }else{
        $zt = strtotime(date('Y-m-d 00:00:00'));
        $qt = strtotime(date('Y-m-d 00:00:00', strtotime("-1 day")));
        $st = strtotime(date('Y-m-d 00:00:00', strtotime("-2 day")));
        $bt = strtotime(date('Y-m-d 00:00:00', strtotime("-7 day")));

        $result = date("Y-m-d",$btime);
    }
    return $result;
}


function search_replace($content, $keyword = '', $limit = ''){
	$keyword = $keyword ? $keyword : input('keyword');
	$content = strip_tags($content);
	if($limit){
		$content = str_cut($content , $limit);
	}
	$content = str_replace($keyword, '<em>' . $keyword . '</em>', $content);

	return $content;
}

/**
 * 字符截取 支持UTF8/GBK
 * @param $string
 * @param $length
 * @param $dot
 */
function str_cut($string, $length, $dot = '...') {
	$strlen = strlen($string);
	if($strlen <= $length) return $string;
	$string = str_replace(array(' ','&nbsp;', '&amp;', '&quot;', '&#039;', '&ldquo;', '&rdquo;', '&mdash;', '&lt;', '&gt;', '&middot;', '&hellip;'), array('∵',' ', '&', '"', "'", '“', '”', '—', '<', '>', '·', '…'), $string);
	$strcut = '';
	if(strtolower('utf-8') == 'utf-8') {
		$length = intval($length-strlen($dot)-$length/3);
		$n = $tn = $noc = 0;
		while($n < strlen($string)) {
			$t = ord($string[$n]);
			if($t == 9 || $t == 10 || (32 <= $t && $t <= 126)) {
				$tn = 1; $n++; $noc++;
			} elseif(194 <= $t && $t <= 223) {
				$tn = 2; $n += 2; $noc += 2;
			} elseif(224 <= $t && $t <= 239) {
				$tn = 3; $n += 3; $noc += 2;
			} elseif(240 <= $t && $t <= 247) {
				$tn = 4; $n += 4; $noc += 2;
			} elseif(248 <= $t && $t <= 251) {
				$tn = 5; $n += 5; $noc += 2;
			} elseif($t == 252 || $t == 253) {
				$tn = 6; $n += 6; $noc += 2;
			} else {
				$n++;
			}
			if($noc >= $length) {
				break;
			}
		}
		if($noc > $length) {
			$n -= $tn;
		}
		$strcut = substr($string, 0, $n);
		$strcut = str_replace(array('∵', '&', '"', "'", '“', '”', '—', '<', '>', '·', '…'), array(' ', '&amp;', '&quot;', '&#039;', '&ldquo;', '&rdquo;', '&mdash;', '&lt;', '&gt;', '&middot;', '&hellip;'), $strcut);
	} else {
		$dotlen = strlen($dot);
		$maxi = $length - $dotlen - 1;
		$current_str = '';
		$search_arr = array('&',' ', '"', "'", '“', '”', '—', '<', '>', '·', '…','∵');
		$replace_arr = array('&amp;','&nbsp;', '&quot;', '&#039;', '&ldquo;', '&rdquo;', '&mdash;', '&lt;', '&gt;', '&middot;', '&hellip;',' ');
		$search_flip = array_flip($search_arr);
		for ($i = 0; $i < $maxi; $i++) {
			$current_str = ord($string[$i]) > 127 ? $string[$i].$string[++$i] : $string[$i];
			if (in_array($current_str, $search_arr)) {
				$key = $search_flip[$current_str];
				$current_str = str_replace($search_arr[$key], $replace_arr[$key], $current_str);
			}
			$strcut .= $current_str;
		}
	}
	return $strcut.$dot;
}
/**
 * 配置文件
 * @param  string $name 文件名称
 * @param  string $type 类型
 */
function conf($name = '', $type = 'system'){
	if($name){
		$where['name'] = $name;
	}
	if($type){
		$where['type'] = $type;
	}
	$where['status'] = 1;
	$list   = db('Config')->where($where)->select();
	$config = array();
	foreach ($list as $rs) {
		$config[$rs['type']][$rs['name']] = trim($rs['val']);
	}

	return $name ? $config[$type][$name] : $config[$type];
}


/**
 * 返回所有接口配置
 */
function getApiInfo($id){
    $key = "API_APIINFO_".$id;
    $data = Cache::get($key);
    if(!$data){
        $Model = db('AppSet');
        $where['status'] = 1;
        $where['app_id'] = $id;
        $data = $Model
            ->where($where)
            ->find();

        Cache::set($key,$data,21600);
    }
    return $data;
}

/**
 * 数组根据键排序
 * */
function array_sort($arr,$keys,$type='asc'){
    $keysvalue = $new_array = array();
    foreach ($arr as $k=>$v){
        $keysvalue[$k] = $v[$keys];
    }
    if($type == 'asc'){
        asort($keysvalue);
    }else{
        arsort($keysvalue);
    }
    reset($keysvalue);
    $i=0;
    foreach ($keysvalue as $k=>$v){
        $new_array[$i++] = $arr[$k];
    }
    return $new_array;
}

/**
 * 传递数据以易于阅读的样式格式化后输出
 * @param $str  要输出的字符串
 * */
function p($data){
    // 定义样式
    $str='<pre style="display: block;padding: 9.5px;margin: 44px 0 0 0;font-size: 13px;line-height: 1.42857;color: #333;word-break: break-all;word-wrap: break-word;background-color: #F5F5F5;border: 1px solid #CCC;border-radius: 4px;">';
    // 如果是boolean或者null直接显示文字；否则print
    if (is_bool($data)) {
        $show_data=$data ? 'true' : 'false';
    }elseif (is_null($data)) {
        $show_data='null';
    }else{
        $show_data=print_r($data,true);
    }
    $str.=$show_data;
    $str.='</pre>';
    echo $str;
}





/**
 * AES 加密
 * @param $str 要加密字符串
 * @param string $key 加密秘钥
 * @param string $iv 加密向量
 * @return string
 */
function encrypt($str,$key="",$iv=""){
    $encrypted = openssl_encrypt($str, 'aes-256-cbc', $key, false, $iv);
    return ($encrypted);
}

/**
 * AES 解密
 * @param $str 要解密密字符串
 * @param string $key 加密秘钥
 * @param string $iv 加密向量
 * @return string
 */
function decrypt($str,$key="",$iv=""){
    $data = ($str);

    $decrypted = openssl_decrypt($data, 'aes-256-cbc', $key, false, $iv);

    return $decrypted;
}


/**
 * 手机号加星
 * @param $mobile
 * @param string $star
 * @return string
 */
function mobile_add_star($mobile, $star = '****'){
    if(strlen($mobile)>7 && is_numeric($mobile)){
        $mobile = substr($mobile, 0, 3) . $star . substr($mobile, -4);
    }
    return $mobile;
}


/**
 * 返回地址的协议加url
 */
function http_url(){
    $http_type = ((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') || (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https')) ? 'https://' : 'http://';
    $res = $http_type.$_SERVER['HTTP_HOST'];
    return $res;
}

/**
 * 获取上传文件的信息
 * @param $file_id
 * @param int $more_info
 * @return array|false|PDOStatement|string|\think\Model
 * @throws \think\db\exception\DataNotFoundException
 * @throws \think\db\exception\ModelNotFoundException
 * @throws \think\exception\DbException
 */
function get_file_info($file_id, $more_info = 0){

    if($more_info == 0) {

        $file_path = db('File')->where('file_id', $file_id)->value('filepath');

        $url = $file_path ? config('WEB_URL') . $file_path : null;

        return $url;

    }else{

        $file_path = db('File')->where('file_id', $file_id)->find();

        $file_path['filepath'] = config('WEB_URL') . $file_path['filepath'];

        $data = $file_path['filepath'] ? $file_path : null;

        return $data;

    }

}

/***
 * 推送单播消息
 * @param $alias         用户别名
 * @param $content       推送内容
 * @param $title         推送标题
 * @param $type          类型 [1-文本消息，跳转到APP首页 2-跳转到网址 3-跳转到APP内部指定页面]
 * @param $url           跳转的网址
 * @param $inter_ident   内部应用标识
 * @return array
 */
function push_single($alias, $content, $title='', $type=1, $url='', $inter_ident=''){

    $is_open_push = conf('is_open_push', 'push');

    if($is_open_push == 0){

        return false;

    }

    $app_key = config('jpush.app_key');
    $master_secret = config('jpush.master_secret');

    $client = new JPush($app_key, $master_secret);

    try {
        $response = $client->push()
            ->setPlatform('all')
            ->addAlias($alias)
            ->iosNotification($content, array(
                'sound' => 'sound.caf',
                'category' => 'jiguang',
                'extras' => array(
                    'title' => $title,
                    'type' => $type,
                    'content' => $content,
                    'url' => $url,
                    'inter_ident' => $inter_ident
                ),
            ))
            ->androidNotification($content, array(
                'title' => $title,
                'extras' => array(
                    'title' => $title,
                    'type' => $type,
                    'content' => $content,
                    'url' => $url,
                    'inter_ident' => $inter_ident
                ),
            ))
            ->options(array(
                // apns_production: 表示APNs是否生产环境，
                // True 表示推送生产环境，False 表示要推送开发环境；如果不指定则默认为推送生产环境
                'apns_production' => true,
            ))
            ->send();
            return [
                'result' => 'success'
            ];

    } catch (\JPush\Exceptions\APIConnectionException $e) {
        return [
            'result' => 'api connect fail',
            'msg'    => $e->getMessage()
        ];
    } catch (\JPush\Exceptions\APIRequestException $e) {
        return [
            'result' => 'api request fail',
            'msg'    => $e->getMessage()
        ];
    }

}