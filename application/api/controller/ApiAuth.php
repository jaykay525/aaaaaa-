<?php
/**
 *
 * +------------------------------------------------------------+
 * 接口授权
 * +------------------------------------------------------------+
 *
 *
 * @copyright http://ukl.io
 * @version 1.0
 *
 * Create at : 2018/2/23 下午2:06
 *
 */

namespace app\api\controller;

use think\Controller;
use think\Request;

class ApiAuth extends Controller
{


    public function index(){

        $controller_name = $this->request->controller();
        $action_name     = $this->request->action();

        //获取头部信息
        $request = Request::instance();
        //nginx header默认不支持下划线，客户端需要传 -
//        $UKL_API_SourceID = $_SERVER['HTTP_UKL_API_SOURCEID'];
//        $UKL_API_AuthKey = strtolower($_SERVER['HTTP_UKL_API_AUTHKEY']);
//        $UKL_API_AuthTime = $_SERVER['HTTP_UKL_API_AUTHTIME'];
//
//
//        if($UKL_API_SourceID){
//            $info = getApiInfo($UKL_API_SourceID);
//
//            if(!$UKL_API_AuthKey){
//                $this->sendError('认证KEY不能为空！',400);
//            }
//            if(!$UKL_API_AuthTime||!is_numeric($UKL_API_AuthTime)){
//                $this->sendError('认证时间不能为空或有误！',401);
//            }
////            $deftime = time()-substr($UKL_API_AuthTime,0,10);
////            if($deftime>$info['maxtime']){
////                $this->sendError('认证时间已超时！',402);
////            }
//            if($info){
//                $dekey = md5($info['app_key'].$UKL_API_AuthTime.$UKL_API_SourceID);
//                if($UKL_API_AuthKey!=$dekey){
//                    $this->sendError('加密KEY有误！',403);
//                }
//            }
//        }else{
//            //$info = getIpToApiInfo();
//            $this->sendError('没设置源ID！',404);
//        }
//        if(empty($info)){
//            $this->sendError('没有权限操作！',405);
//        }
        if(empty($controller_name)){
            $this->sendError('mod参数不能为空',406);
        }
        if(empty($action_name)){
            $this->sendError('act参数不能为空',407);
        }

    }


    /***
     * 输出消息
     * @param $message
     * @param $code
     */
    private function sendError($message,$code){

        $data = array(

            'code' => $code,
            'message' => $message

        );

        exit(json_encode($data));


    }



}