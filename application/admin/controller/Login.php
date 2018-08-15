<?php
namespace app\admin\controller;
use \think\Request;

class Login extends Base{

    public function index(){
    	if ($this->isPost()){
    		$username = $this->_post('username', '用户名不能为空！');
    		$password = $this->_post('password', '密码不能为空！');
			$info = model('Admin')->where(array('username' => $username))->find();
			$password_md5 = password_md5_one($info['admin_id'], $password);
			//判断密码
			if($info['password'] != $password_md5){
				$this->error('对不起，您输入密码有误！'.$password_md5);
			}
			//判断是否禁用
			if($info['status'] == 0){
				$this->error('对不起，您的账号被禁用，请联系相关人员！');
			}
			
			$time = 7 * 24 * 60 * 60;
	        session(array('expire' => $time));
	        session('admin_id', $info['admin_id']);
	        //正常登录
	        $this->success('登录成功！', 'Index/index');
    	}
		return $this->view();
    }
}
