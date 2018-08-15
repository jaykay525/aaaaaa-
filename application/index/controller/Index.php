<?php
namespace app\index\controller;
use \think\Request;

class Index extends Common{
    public function index(){

        $ios = db('app_update')->where(array('app_key'=>'IOS','status'=>1))->value('down_url');
        $android_file_id = db('app_update')->where(array('app_key'=>'Android','status'=>1))->value('app_file_id');
        $android = db('file')->where(array('file_id'=>$android_file_id))->value('filepath');
        $android = config('WEB_URL').$android;

        $this->assign('ios',$ios);
        $this->assign('android',$android);
        $this->assign('web_title','区块链项目评测平台');
        $this->assign('no_cache_sign', date('ymdhis').rand(1111,999));
        return $this->view();

    }
}
