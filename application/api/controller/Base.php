<?php
/**
 *
 * +------------------------------------------------------------+
 * 基础类
 * +------------------------------------------------------------+
 *
 */
namespace app\api\controller;

use think\Cache;
use think\Request;
use think\Response;
use think\Controller;
use think\response\Redirect;

class Base extends Controller
{


    /**
     * 默认返回资源类型
     * @var string
     */
    protected $restDefaultType = 'json';

    public $checkHostIsDomain;

    protected $_userId;

    protected $_userToken;

    public function _initialize()
    {

        @header('Access-Control-Allow-Origin: *');
        @header('Access-Control-Allow-Headers: ukl-api-authkey,ukl-api-authtime,ukl-api-sourceid');

        //浏览器第一次在处理复杂请求的时候会先发起OPTIONS请求。路由在处理请求的时候会导致PUT请求失败。在检测到option请求的时候就停止继续执行
        if($_SERVER['REQUEST_METHOD'] == 'OPTIONS'){
            exit;
        }

        header('Expires: Thu, 01 Jan 1970 00:00:01 GMT');
        header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');
        header('Cache-Control: no-cache, must-revalidate, max-age=0');
        header('Pragma: no-cache');

        header('Content-Type: application/json;charset=utf-8');

        date_default_timezone_set('Asia/Shanghai');	//该函数为PHP5.1内置

        $this->_checkAuth();

        $this->_getUserInfo();

    }




    /**
     * 检查API授权
     */
    protected function _checkAuth(){

        $auth = new \app\api\controller\ApiAuth();
        $auth -> index();

    }


    /**
     * 获取用户信息
     */
    protected function _getUserInfo(){

        $token = Request::instance()->header('token') ?: ($_GET['token'] ?: $_POST['token']);

        if(!empty($token) && strlen($token) ==32) {

            $userId = Cache::store('redis')->get(config('HB_TOKEN_PRE') . $token);

            if($userId == false){

                exit(json_encode(array('code'=>441, 'message'=>'token无效！')));

            }

            Cache::store('redis')->set(config('HB_TOKEN_PRE') . $token,$userId,7*24*60*60);

            $this->_userId = $userId;
            $this->_userToken = $token;
        }else{
            if($this->request->action() != 'login'){
                exit(json_encode(array('code'=>442, 'message'=>'未登录！')));
            }
            $this->_userId = 0;

        }

    }

    /**
     * 检查用户是否已登录,未登录下报错
     */
    protected function _requestLogin(){

        if($this->_userId == 0){

            exit(json_encode(array('code'=>442, 'message'=>'未登录！')));

        }

    }


    /**
     * 设置响应类型
     * @param null $type
     * @return $this
     */
    public function setType($type = null)
    {
        $this->type = (string)(!empty($type)) ? $type : $this->restDefaultType;
        return $this;
    }

    /**
     * 失败响应
     * @param int $error
     * @param string $message
     * @param int $code
     * @param array $data
     * @param array $headers
     * @param array $options
     * @return Response|\think\response\Json|\think\response\Jsonp|\think\response\Xml
     */
    public function sendError($error = 100, $message = 'error', $code = 400, $data = [], $headers = [], $options = [])
    {
        $responseData['code'] = (int)$error;
        $responseData['message'] = (string)$message;
        if (!empty($data)) $responseData['data'] = $data;
        $responseData = array_merge($responseData, $options);
        return $this->response($responseData, $code, $headers);
    }

    /**
     * 成功响应
     * @param array $data
     * @param string $message
     * @param int $code
     * @param array $headers
     * @param array $options
     * @return Response|\think\response\Json|\think\response\Jsonp|Redirect|\think\response\Xml
     */
    public function sendSuccess($data = [], $message = 'success', $code = 200, $headers = [], $options = [])
    {
        $responseData['code'] = 200;
        $responseData['message'] = (string)$message;
        $responseData['data'] = $data;
        $responseData = array_merge($responseData, $options);
        return $this->response($responseData, $code, $headers);
    }

    /**
     * 重定向
     * @param $url
     * @param array $params
     * @param int $code
     * @param array $with
     * @return Redirect
     */
    public function sendRedirect($url, $params = [], $code = 302, $with = [])
    {
        $response = new Redirect($url);
        if (is_integer($params)) {
            $code = $params;
            $params = [];
        }
        $response->code($code)->params($params)->with($with);
        return $response;
    }

    /**
     * 响应
     * @param $responseData
     * @param $code
     * @param $headers
     * @return Response|\think\response\Json|\think\response\Jsonp|Redirect|\think\response\View|\think\response\Xml
     */
    public function response($responseData, $code, $headers)
    {
        if (!isset($this->type) || empty($this->type)) $this->setType();
        // dump($responseData);exit;
        return Response::create($responseData, $this->type, $code, $headers);
    }

    protected function _get($name = '', $empty_msg = '', $default_value = '', $is_array_str = false){
        $value = input($name);
        $value = $this->common_field_check($value, $empty_msg, $default_value, $is_array_str);

        $this->assign(str_replace(array('/a'), '', $name), $value);

        return $value;
    }
    protected function _post($name = '', $empty_msg = '', $default_value = '', $is_array_str = false){
        $value = input($name);
        $value = $this->common_field_check($value, $empty_msg, $default_value, $is_array_str);

        $this->assign(str_replace(array('/a'), '', $name), $value);

        return $value;
    }
    /**
     * 通用字段验证
     * @param  string $field         参数名
     * @param  string $empty_msg     值为空，提示信息
     * @param  string $default_value 值为空，默认值
     * @param  string $is_array_str  返回数组是否转为字符串，多个以逗号分隔
     */
    protected function common_field_check($value, $empty_msg = '', $default_value = '', $is_array_str = false){
        if(is_array($value)){
            $array_val = implode(',', $value);
            $is_empty  = empty($array_val);
            $value     = $is_array_str ? $array_val : $value;
        }else{
            $is_empty = !trim($value);
            $value    = trim($value);
        }

        if($is_empty && $empty_msg){
//            $this->error($empty_msg);
            exit(json_encode(array('code'=>100, 'message'=>$empty_msg)));
        }
        if($is_empty){
            $value = isset($default_value) ? $default_value : '';
        }

        return $value;
    }

}