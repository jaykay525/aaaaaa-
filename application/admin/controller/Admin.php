<?php
namespace app\admin\controller;

class Admin extends Base{
	// 管理员列表
	public function index(){
		return parent::index('', $where, '');
	}
	public function add(){
		$this->assign('role_list', model('AdminRole')->select());
		return parent::add();
	}
	// 修改管理员信息
	public function edit(){
		$admin_id = $this->_get('admin_id', '对不起，您的操作有误！');
		if($this->isPost()){
			$password   = $this->_post('password');
			$repassword = $this->_post('repassword');

			if($password != $repassword){
				$this->error('对不起，两次密码不一致！');
			}

			//重新设置密码
			if($password){
				$data['password'] = password_md5_one($admin_id, $password);
			}else{
				//密码为空，不作修改
				$this->_filter_field = array('password');
			}
		}

		$auths_serialize = model('Admin')->where(array('admin_id' => $admin_id))->value('auths');
		$auths = unserialize($auths_serialize);
		$this->assign('user_auths', $auths);
		$this->assign('role_list', model('AdminRole')->select());
		return parent::edit('', $data);
	}
	// 个人信息
	public function detail(){

		return $this->view();
	}
	// 修改密码
	public function change_password(){
		if($this->isPost()){
			$old_password = $this->_post('old_password', '旧密码不能为空！');
			$password     = $this->_post('password', '密码不能为空！');
			$repassword   = $this->_post('repassword', '确认密码不能为空！');

			if($password != $repassword){
				$this->error('两次密码不一致！');
			}
			$password_md5 = password_md5_one($this->_admin_id, $old_password);
			if($password_md5 != $this->_user['password']){
				$this->error('您输入旧密码不对！');
			}
			$new_password = password_md5_one($this->_admin_id, $password);
			model('Admin')->where(array('admin_id' => $this->_admin_id))->update(array('password' => $new_password));
			parent::logout();
			$this->success('修改成功！', url('Login/index'));
		}
		return $this->view();
	}
	// 操作日志
	public function operation_log(){
		return parent::index('AdminOptLogs');
	}
	// 退出登录
	public function logout(){
		parent::logout();
		$url = url('Login/index');
		// 直接跳转
		if($this->_get('is_redirect')){
			$this->redirect($url, 302);
		}
		$this->success('退出登录成功！', $url);
	}
}
