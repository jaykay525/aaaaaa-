<?php
namespace app\admin\model;
use \think\Request;
use app\common\model\BaseModel;

class AdminRole extends BaseModel{
	public $_is_supper_admin       = false;//是否超级管理员
	public $_top_menu_list         = array();//顶部菜单
	public $_left_menu_list        = array();//左侧菜单
	public $_user_menu_left        = array();//用户左侧菜单
	public $_select_left_menu_info = array();//选中左侧菜单信息

	// 获取状态类型说明
	public function getStatusDescAttr($value, $data){
		$status = [0 => '禁用', 1 => '正常'];
		return $status[$data['status']];
	}
	// 获取状态颜色类
	public function getStatusColorClassAttr($value, $data){
		$status = [0 => 'danger', 1 => 'primary'];
		return $status[$data['status']];
	}
	// 保存时权限字段进行序列化
	public function setAuthsAttr($value, $data){
		$data = serialize($data['auths_array']);
		return $data ? $data : '';
	}
	
	/**
	 * 网站栏目
	 * @param  string $company_id [description]
	 * @return [type]             [description]
	 */
	public function menu($role_id = '', $user_id = ''){
		$role_info  = model('AdminRole')->where(array('role_id' => $role_id))->find();
		$role_auths = $role_info['auths'];
		$role_auths = unserialize($role_auths);
		// unset($where);

		$info = model('Admin')->get($user_id)->getData();
		$user_auths = $info['auths'];
		$user_auths = unserialize($user_auths);
		
		$menu = config('menu.menu');
		//最高权限不限制
		if($info['status'] == 99){
			$this->_is_supper_admin = true;
			return $menu;
		}
		$role_menu = $this->auths_menu($role_auths, $menu);
		$user_menu = $this->auths_menu($user_auths, $menu);

		$merge_menu = $this->merge_menu($role_menu, $user_menu, $menu);
		// var_dump($merge_menu);exit;
		// 重写栏目
		config('menu.menu', $merge_menu);

		return $merge_menu ? $merge_menu : array();
	}
	/**
	 * 过滤需要权限栏目
	 * @param  array $role_auths 要显示权限栏目
	 * @param  array $menu       所有栏目结构
	 */
	public function auths_menu($role_auths, $menu){
		$new_menu    = array();
		$filter_list = array();
		//根据权限配置显示栏目
		if($menu){
			//一级栏目处理
			foreach ((array)$menu as $key => $menu_list) {
				//判断一级栏目是否有权限
				$cur_module = $role_auths[$key];
				if($cur_module){
					//二级栏目处理
					if($menu_list['list']){
						unset($list_2);
						foreach ((array)$menu_list['list'] as $rs) {
							//三级栏目处理
							unset($list_3);
							foreach ((array)$rs['list'] as $row) {
								$filter_list = array();
								if(in_array($row['url'], $cur_module)){
									foreach ((array)$row['filter_list'] as $rw) {
										if(in_array($rw['url'], $cur_module)){
											$filter_list[] = $rw;
										}
									}
									$row['filter_list'] = $filter_list;
									$list_3[] = $row;
								}
							}
							if($list_3){
								$rs['list'] = $list_3;
								$list_2[]   = $rs;
							}
						}
						//重组后三级栏目第一个栏目URL
						$menu_list['url'] = $list_2[0]['list'][0]['url'];
						$menu_list['list'] = $list_2;
					}

					$new_menu[$key] = $menu_list;
				}
			}
		}
		return $new_menu;
	}
	/**
	 * 合并栏目权限（交集）
	 * @param  array $menu1     要显示栏目1
	 * @param  array $menu2     要显示栏目2
	 * @param  array $full_menu 全部栏目
	 */
	public function merge_menu($menu1, $menu2, $full_menu=array()){
		foreach ((array)$menu1 as $key => $list) {
			foreach ((array)$list['list'] as $rs) {
				foreach ((array)$rs['list'] as $row) {
					$menu_auths[$key][] = $row['url'];
					foreach ((array)$row['filter_list'] as $rw) {
						$menu_auths[$key][] = $rw['url'];
					}
				}
			}
		}
		foreach ((array)$menu2 as $key => $list) {
			foreach ((array)$list['list'] as $rs) {
				foreach ((array)$rs['list'] as $row) {
					$menu_auths[$key][] = $row['url'];
					foreach ((array)$row['filter_list'] as $rw) {
						$menu_auths[$key][] = $rw['url'];
					}
				}
			}
		}
		$merge_menu = $this->auths_menu($menu_auths, $full_menu);
		return $merge_menu ? $merge_menu : array();
	}
	//获取选中左侧菜单信息
	public function get_select_left_menu(){
		$select_info = array();
		$left_menu   = $this->get_user_menu_left();
		foreach ((array)$left_menu as $rs) {
			foreach ((array)$rs['list'] as $row) {
				if($row['is_select']){
					$select_info[]  = $rs;
					$select_info[] = $row;
					$this->_current_menu_info = $row;
					break;
				}
			}
		}
		$this->_select_left_menu_info = $select_info;
		return $select_info;
	}
	public function get_user_menu_top(){
		// error_reporting(0);
		$list = config('menu.menu');
		$new_list = [];
		foreach ((array)$list as $rs) {
			$rs['is_select'] = $rs['is_select'] ? 1 : $this->is_select_menu($rs['url']);
			// var_dump($rs['url'] . '==> ' . ($rs['is_select'] ? 1 : 0));
			if(!$rs['is_select']){
				foreach ((array)$rs['list'] as $row) {
					$rs['is_select'] = $rs['is_select'] ? 1 : $this->is_select_menu($row['url']);
					if(!$rs['is_select']){
						foreach ((array) $row['list'] as $rw) {
							$rs['is_select'] = $rs['is_select'] ? 1 : $this->is_select_menu($rw['url']);
							foreach ((array) $rw['filter_list'] as $fl) {
								$rs['is_select'] = $rs['is_select'] ? 1 : $this->is_select_menu($fl['url']);
							}
						}
						foreach ((array) $row['filter_list'] as $fl) {
							$rs['is_select'] = $rs['is_select'] ? 1 : $this->is_select_menu($fl['url']);
						}
					}
				}
			}
			if($rs['is_select']){
				$this->_left_menu_list = $rs['list'];
			}
			$new_list[] = $rs;
		}
//		dump($new_list);exit;
		$this->_top_menu_list = $new_list;
		return $new_list;
	}
	public function get_user_menu_left(){
		if(!$this->_left_menu_list){
			$this->get_user_menu_top();
		}
		$new_list = [];
		$list = $this->_left_menu_list;
		foreach ((array)$list as $rs) {
			$rs['is_select'] = $this->is_select_menu($rs['url']);
			// var_dump($rs['url'] . '==> ' . ($rs['is_select'] ? 1 : 0));
			foreach ((array)$rs['filter_list'] as $fl) {
				$rs['is_select'] = $rs['is_select'] ? 1 : $this->is_select_menu($fl['url']);
			}
			$row_list = array();
			foreach ((array)$rs['list'] as $row) {
				$row['is_select'] = $this->is_select_menu($row['url']);
				$fl_list = array();
				foreach ((array)$row['filter_list'] as $fl) {
					$row['is_select'] = $row['is_select'] ? 1 : $this->is_select_menu($fl['url']);
					$rs['is_select']  = $row['is_select'] ? 1 : $rs['is_select'];
					$fl['is_select']  = $this->is_select_menu($fl['url']);
					$fl_list[] = $fl;
				}
				$row['filter_list'] = $fl_list;
				$rs['is_select'] = $row['is_select'] ? 1 : $rs['is_select'];
				$row_list[] = $row;
			}
			$rs['list'] = $row_list;
			$new_list[] = $rs;
		}
		$this->_user_menu_left = $new_list;
		return $new_list;
	}
	/**
	 * 是否选中菜单
	 * @param  [type]  $menu_url   [description]
	 * @param  [type]  $router_url [description]
	 * @return boolean             [description]
	 */
	public function is_select_menu($menu_url, $request_url = ''){
		$menu_url    = \think\Loader::parseName($menu_url);
		$mod_act     = str_replace(array('.html', '?', '=', '&'), '/', $menu_url);
		$request_url = $request_url ? $request_url : $_SERVER['REQUEST_URI'];
		if($request_url == '/'){
			$request_url = 'index/index';
		}
		$request_url = str_replace(array('.html', '?', '=', '&', '//'), '/', $request_url);
		// var_dump($request_url . '===' . $mod_act . '==>' . (strpos($request_url, $mod_act) !== false ? 1 : 0));
		if(strpos($request_url, $mod_act) !== false){
			return true;
		}
		return false;
	}
	// 获取用户栏目
	// _top_menu_list 顶部栏目
	// _user_menu_left 左侧栏目
	public function get_user_menu(){
		$this->get_user_menu_left();
		$this->get_select_left_menu();
		return $this->is_role();
	}
	// 是否有操作权限
	public function is_role(){
		$controller_name = strtolower(Request::instance()->controller());
		$action_name     = strtolower(Request::instance()->action());

		if($this->_is_supper_admin){
			return true;
		}
		$is_role = false;
		if(!$this->_top_menu_list){
			$this->get_user_menu_top();
		}
		foreach ((array)$this->_top_menu_list as $rs) {
			if($rs['is_select']){
				$is_role = true;
			}
		}
		if(in_array($controller_name, array('login'))){
			$is_role = true;
		}
		if(in_array($action_name, array('valid_field'))){
			$is_role = true;
		}
		// 退出登录权限过滤
		if($action_name == 'logout'){
			$is_role = true;
		}
		// 后台首页跳转过滤
		if($controller_name == 'index' && $action_name == 'index'){
			$is_role = true;
		}
		return $is_role;
	}
	public function url_is_acl($url, $is_has_full_url = 0){
		$url = $is_has_full_url ? $url : url($url);
		$is_acl = 0;
		if(!$this->_top_menu_list){
			$this->get_user_menu_top();
		}
		foreach ((array)$this->_top_menu_list as $rs) {
			if($this->is_select_menu($rs['url'], $url)){
				$is_acl = 1;
				break;
			}
			foreach ((array) $rs['filter_list'] as $fl) {
				if($this->is_select_menu($fl['url'], $url)){
					$is_acl = 1;
					break;
				}
			}
			foreach ((array)$rs['list'] as $row) {
				if($this->is_select_menu($row['url'], $url)){
					$is_acl = 1;
					break;
				}
				foreach ((array)$row['list'] as $row2) {
					if($this->is_select_menu($row2['url'], $url)){
						$is_acl = 1;
						break;
					}
					foreach ((array) $row2['filter_list'] as $fl) {
						if($this->is_select_menu($fl['url'], $url)){
							$is_acl = 1;
							break;
						}
					}
				}
				foreach ((array) $row['filter_list'] as $fl) {
					if($this->is_select_menu($fl['url'], $url)){
						$is_acl = 1;
						break;
					}
				}
			}
		}
		// 退出登录权限过滤
		if(strpos($url, 'admin/logout') !== false){
			$is_acl = 1;
		}
		// 登录页面权限过滤
		if(strpos($url, 'login/index') !== false){
			$is_acl = 1;
		}
		// 后台首页跳转过滤
		if(strpos($url, 'index/index') !== false){
			$is_acl = 1;
		}
		if($url == request()->domain() . '/'){
			$is_acl = 1;
		}
		$is_acl = $is_acl ? 1 : 0;
		// var_dump($url . '===>' . $is_acl);
		return $is_acl;
	}
	public function is_acl($url, $is_has_full_url = 0){
		return $this->url_is_acl($url) ? '' : 'url="#delete-link"';
	}
}