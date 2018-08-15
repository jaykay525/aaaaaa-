<?php
namespace app\index\controller;
use think\Response;
use think\Controller;
use think\response\Redirect;
use think\Request;

class Common extends Controller{
    /**
     * 默认返回资源类型
     * @var string
     */
    protected $restDefaultType = 'json';

	function _initialize(){

        $currentPlat = preg_match("/(iPad|IOS|iPhone)/si",$_SERVER['HTTP_USER_AGENT']) ? 'ios' : 'android';
        $this->assign('current_plat', $currentPlat);
		
	}
	public function _get($name = ''){
		$value = input('get.' . $name);

		$this->assign(str_replace(array('/a'), '', $name), $value);

		return $value;
	}

    public function _post($name = ''){
        $value = input('post.' . $name);

        $this->assign(str_replace(array('/a'), '', $name), $value);

        return $value;
    }
	
	public function view($tpl = ''){
		return view($tpl);
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
}