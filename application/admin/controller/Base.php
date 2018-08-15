<?php
namespace app\admin\controller;
use \think\Request;
use \think\Controller;
use app\common\model\BaseModel;

class Base extends Controller{
	public $_title        = '';// 完整标题
	public $_cur_title    = '';// 当前标题
	public $_trail        = [];// 
	public $_pagesize     = 10;// 分页数量
	public $_admin_id     = 0;// 管理员ID
	public $_realname     = '';// 管理员姓名
	public $_nickname     = '';// 管理员昵称
	public $_mobile       = '';// 管理员手机号
	
	public $_filter_field = [];// 添加修改过滤字段
	public $_like_field   = [];// 模糊查询字段
	public $_jump_url     = '';// 添加修改跳转URL
	public $_tpl          = '';// 使用模板

	public $_recycle_field = 'title';// 回收站标题

	function _initialize(){
		$controller_name = $this->request->controller();
		$action_name     = $this->request->action();

		// 管理员ID
		$admin_id = session('admin_id');
		// 未登录处理
		if(empty($admin_id) && in_array($controller_name, array('Login')) == false){
			$this->redirect('Login/index');
		}
		// 管理员信息
		$this->_user = model('Admin')->find($admin_id);
		$admin_status = $this->_user->getData('status');

		if($admin_status == 0 && in_array(controller_name, array('Login')) == false){
			$this->error('对不起，您的账号被禁用，请联系相关人员！', url('Login/index'));
		}

		$this->_admin_id    = $this->_user['admin_id'];
		$this->_nickname    = $this->_user['nickname'];
		$this->_mobile      = $this->_user['mobile'];
		$this->_realname    = $this->_user['realname'];
		$this->_province_id = $this->_user['province_id'];
		$this->_city_id     = $this->_user['city_id'];
		$this->_role_id     = $this->_user['role_id'];

		$this->assign('_admin_id', $this->_admin_id);
		$this->assign('_nickname', $this->_nickname);
		$this->assign('_mobile', $this->_mobile);
		$this->assign('_realname', $this->_realname);
		$this->assign('_admin_status', $this->admin_status);
		$this->assign('_user', $this->_user);


		//栏目权限
		$role_model = model('AdminRole');
		$menu_role  = $role_model->menu($this->_user['role_id'], $this->_admin_id);
		// var_dump($role_model->get_user_menu());exit;
		//左侧菜单
		if(!$role_model->get_user_menu()){
			$this->error('对不起，您没有权限！');
		}
		$this->assign('_top_menu_list', $role_model->_top_menu_list);
		$this->assign('_left_menu_list', $role_model->_user_menu_left);
		$this->_top_menu_list  = $role_model->_top_menu_list;
		$this->_left_menu_list = $role_model->_user_menu_left;

		//面包导航配置
		$this->add_step('首页', url('/'));
		//左侧选中菜单信息
		$select_info = $role_model->_select_left_menu_info;
		foreach ($select_info as $rs) {
			$this->add_step($rs['title'], url($rs['url']));
			foreach ((array) $rs['filter_list'] as $row) {
				if($row['is_select']){
					$this->add_step($row['title'], url($row['url']));
					break;
				}
			}
		}
		// 当前URL
		$this->_cur_url = $this->request->url(true);
		$this->assign('_cur_url', $this->_cur_url);
		// 模块
		$module = $this->_get('module');
		$this->_module = $module;
		// 上级ID
		$parent_id = $this->_get('parent_id');
		$this->_parent_id = $parent_id;
		// 网站标题
		$web_title = model('Config')->_get('web_title');
		$this->assign('web_title', $web_title);

		$this->write_system_logs();
	}
	//记录系统操作日志
	protected function write_system_logs(){
		$controller_name = $this->request->controller();
		$action_name     = $this->request->action();
		$method          = $this->request->method();
		//过滤不需要记录日志
		if(in_array($controller_name . '/' . $action_name, array('Admin/operation_log'))){
			return false;
		}
		if(in_array($controller_name . '/' . $action_name, array('Login/index'))){
			return false;
		}
		if(in_array($controller_name . '/' . $action_name, array('Index/logout'))){
			$this->_cur_title = '退出登录';
		}

		$data['realname']     = $this->_realname ? $this->_realname : '';
		$data['admin_id']     = $this->_admin_id ? $this->_admin_id : 0;
		$data['province_id']  = $this->_province_id ? $this->_province_id : 0;
		$data['city_id']      = $this->_city_id ? $this->_city_id : 0;
		$data['role_id']      = $this->_role_id ? $this->_role_id : 0;
		$data['module']       = $controller_name;
		$data['action']       = $action_name;
		$data['request_type'] = $method;
		$data['opt_name']     = $this->_cur_title;
		$data['params']       = http_build_query($_GET);
		$data['ip']           = $this->request->ip();
		$data['create_time']  = time();

		model('AdminOptLogs')->create($data, true);
	}
	// 默认列表
	public function index($model_name = '', $where = array(), $order = '', $is_model = true){
		// 模型名称
		$model_name = $model_name ? $model_name : $this->request->controller();
		// 使用模板
		$tpl        = $this->_tpl ? $this->_tpl : '';
		// 一页显示数量
		$pagesize   = $this->_pagesize ? $this->_pagesize : 10;
		// 查询条件
		$where      = $where ? $where : array();
		// 模糊查询字段
		$like_field = $this->_like_field ? $this->_like_field : array('title');

		$model = $is_model ? model($model_name) : db($model_name);
		// 主键
		$pk_key = $model->getPk();
		// 获取表字段信息
		$table_field = $model->getTableInfo('', 'fields');

		// 通用GET参数查询条件
		$search_where = array();
		$search_data  = input('get.');
		foreach ($search_data as $key => $value) {
			$value = trim($value);
			if($value != '' && in_array($key, $table_field)){
				$search_where[$key] = in_array($key, $like_field) ? array('like', '%' . $value . '%') : $value;
			}
			$this->assign($key, $value);
		}
		//处理列表记录内容修改
		if($this->isPost()){
			$id_arr = $this->_post('id_arr/a');
			$post_data = input('post.');
			foreach ($id_arr as $id) {
				foreach ($post_data as $key => $val) {
					if(!in_array($key, $table_field)){
						continue;
					}
					$value = htmlspecialchars($val[$id]);
					$data[$key] = $value;
					if($key == 'order_id'){
						$data[$key] = $data[$key] ? $data[$key] : '99';
					}
				}
				$ewhere[$pk_key] = $id;
				$model->where($ewhere)->update($data, '', true);
				unset($ewhere);
			}
			$jump_params = array_merge($search_where, $this->_module ? array('module' => $this->_module) : array());
			$this->redirect(url('index', $jump_params));
		}
		// 排序
		$order  = $order ? $order : ($pk_key ? (in_array('order_id', $table_field) ? 'order_id,' : '') . (in_array('status', $table_field) ? 'status desc,' : '') . $pk_key . ' desc' : '');
		// 查询条件
		$where  = array_merge($search_where, $where);
		// 不显示回收站的
		if(in_array('is_delete', $table_field) && !isset($where['is_delete'])){
			$where['is_delete'] = 0;
		}
		// 按模块查询
		if(in_array('module', $table_field) && $this->_module){
			$where['module'] = $this->_module;
		}
		
		$list = $model->where($where)->order($order)->paginate($pagesize, false, ['query' => $search_where]);
		if($total = $list->total()){
			$total_html = '<div class="total">共' . $total . '条记录</div>';
		}
		$page = '<div class="page">' . $total_html . $list->render() . '</div>';
		$this->assign('pk_key', $pk_key);
		$this->assign('list', $list);
		$this->assign('page', $page);
		$this->assign('_recycle_field', $this->_recycle_field);
		
		$list_arr     = $list->toArray();
		$this->_list  = $list_arr['data'];
		$this->_total = $list_arr['total'];

		return self::view($tpl);
	}

    // 默认列表
    public function index_join($model_name = '', $where = array(), $order = '', $is_model = true,$join_table = array(),$join_where = array(),$join_field=''){
        // 模型名称
        $model_name = $model_name ? $model_name : $this->request->controller();
        // 使用模板
        $tpl        = $this->_tpl ? $this->_tpl : '';
        // 一页显示数量
        $pagesize   = $this->_pagesize ? $this->_pagesize : 10;
        // 查询条件
        $where      = $where ? $where : array();
        // 模糊查询字段
        $like_field = $this->_like_field ? $this->_like_field : array('title');

        $model = $is_model ? model($model_name) : db($model_name);
        // 主键
        $pk_key = $model->getPk();


        // 获取表字段信息
        $table_field = $model->getTableInfo('', 'fields');
        $total_field = array(
            $model_name => $table_field
        );

        foreach($join_table as $k => $v){

            $table = explode('_',$v);
            $table_name = $table[1];
            if(count($table) == 3 ){
                $table_name = $table[1].ucfirst($table[2]);
            }
            $field = $model->getTableInfo($v, 'fields');
            $total_field[$table_name] = $field;
        }


        // 通用GET参数查询条件
        $search_where = array();
        $search_data  = input('get.');

        foreach ($search_data as $key => $value) {
            $value = trim($value);
            $flag = 0;
            $k_flag = 0;
            $s_flag = 0;
            $k_arr = explode('_',$key);

            foreach($total_field as $k => $v){

                if(in_array($key,$v)){
                    $flag = 1;
                }
                if((in_array($k_arr[2],$v) && $k == $k_arr[1] )){
                    $k_flag = 1;
                }
                // 表名有下划线的时候
                if(count($k_arr) == 4){
                    $table_name = $k_arr[1].'_'.$k_arr[2];

                    if((in_array($k_arr[3],$v) && $k == $table_name )){
                        $s_flag = 1;
                    }
                }

            }

            if($value != '' && ($flag == 1 || $k_flag == 1 || $s_flag == 1)){

                if($k_flag == 1){
                    $search_where[$k_arr[0].'_'.$k_arr[1].'.'.$k_arr[2]] = in_array($key, $like_field) ? array('like', '%' . $value . '%') : $value;
                }else if($s_flag == 1){
                    $search_where[$k_arr[0].'_'.$k_arr[1].'_'.$k_arr[2].'.'.$k_arr[3]] = in_array($key, $like_field) ? array('like', '%' . $value . '%') : $value;
                }else {
                    $search_where[$key] = in_array($key, $like_field) ? array('like', '%' . $value . '%') : $value;
                }
            }
            $this->assign($key, $value);
        }
        foreach($search_where as $k => $v){
            $count = 0;

            foreach($total_field as $key => $value){

                $in = in_array($k,$value);
                if($in){
                    $count ++;
                    $is_table = $key;
                }
            }

            if($count >1){
                $search_where['t_'.$is_table.'.'.$k] = $v;

                unset($search_where[$k]);
            }
        }

        //处理列表记录内容修改
        if($this->isPost()){
            $id_arr = $this->_post('id_arr/a');
            $post_data = input('post.');
            foreach ($id_arr as $id) {
                foreach ($post_data as $key => $val) {
                    if(!in_array($key, $table_field)){
                        continue;
                    }
                    $value = htmlspecialchars($val[$id]);
                    $data[$key] = $value;
                    if($key == 'order_id'){
                        $data[$key] = $data[$key] ? $data[$key] : '99';
                    }
                }
                $ewhere[$pk_key] = $id;
                $model->where($ewhere)->update($data, '', true);
                unset($ewhere);
            }
            $jump_params = array_merge($search_where, $this->_module ? array('module' => $this->_module) : array());
            $this->redirect(url('index', $jump_params));
        }
        // 排序
        $order  = $order ? $order : ($pk_key ? (in_array('order_id', $table_field) ? 'order_id,' : '') . (in_array('status', $table_field) ? 'status desc,' : '') . $pk_key . ' desc' : '');
        // 查询条件
        $where  = array_merge($search_where, $where);

        // 不显示回收站的
        if(in_array('is_delete', $table_field) && !isset($where['is_delete'])){
            $where['t_'.$model_name.'.is_delete'] = 0;
        }
        // 按模块查询
        if(in_array('module', $table_field) && $this->_module){
            $where['module'] = $this->_module;
        }

        $list = $model->where($where)->order($order);

        if($join_field){

            $list = $list->field($join_field);
        }
        for($i = 0;$i<count($join_table);$i++){

            $list = $list -> join($join_table[$i],$join_where[$i]);
        }

        $list = $list -> paginate($pagesize, false, ['query' => $search_where]);

        if($total = $list->total()){
            $total_html = '<div class="total">共' . $total . '条记录</div>';
        }
        $page = '<div class="page">' . $total_html . $list->render() . '</div>';
        $this->assign('pk_key', $pk_key);
        $this->assign('list', $list);
        $this->assign('page', $page);
        $this->assign('_recycle_field', $this->_recycle_field);

        $list_arr     = $list->toArray();
        $this->_list  = $list_arr['data'];
        $this->_total = $list_arr['total'];

        return self::view($tpl);
    }

	// 回收站
	public function recycle(){
		$count = count($this->_trail);
		$this->_cur_title = $this->_trail[$count - 2]['title'];
		
		$this->assign('_cur_title', $this->_cur_title);
		$where['is_delete'] = 1;
		
		self::index('', $where, 'recycle_time desc');

		$this->assign('list', $this->_list);
		$this->_tpl = $this->_tpl ? $this->_tpl : 'public/recycle';

		return self::view($this->_tpl);
	}
	/**
	 * 通用添加控制器
	 * @param string $model_name 模型名称
	 * @param array  $data       添加数据
	 * @param string $jump_url   跳转链接
	 */
	public function add($model_name = '', $data = array(), $jump_url = ''){
		$model_name   = $model_name ? $model_name : $this->request->controller();
		$tpl          = $this->_tpl ? $this->_tpl : 'edit';
		$jump_url     = $jump_url ? $jump_url : $this->_jump_url;
		$filter_field = $this->_filter_field;
		$data = $data ? $data : array();

		$model  = model($model_name);

		if($this->isPost()){
			$post_data = input('post.');
			// 更新修改时间
			$post_data['edit_time']   = time();
			// 创建时间
			$post_data['create_time'] = time();
			//合并表单及自定义字段
			$new_data = array_merge($post_data, $data);
			//处理数组字段成按逗号分隔字符串
			foreach ($new_data as $key => $value) {
				if(is_array($value)){
					$save_data[$key . '_array'] = $value;
					$value = implode(',', $value);
					if(substr($value, 0, 2) == '0,'){
						$value = substr($value, 2);
					}
				}
				$save_data[$key] = htmlspecialchars_decode($value);
			}
			//过滤不修改字段
			if($filter_field){
				foreach ($filter_field as $field) {
					unset($save_data[$field]);
				}
			}
			// 替换HTML内容的文件为本地路径
			// if($this->_html_field){
			// 	foreach ($this->_html_field as $field) {
			// 		$save_data[$field] = \app\common\File::html_osspath_local($save_data[$field]);
			// 	}
			// }
			
			// 验证器
			try{
				$validate = validate($model_name);
			}catch(\Exception $e){
				// 没有设置验证器，则不处理
				// var_dump($e);exit;
			}
			if($validate && !$validate->scene('add')->check($save_data)){
				$this->error($validate->getError());
			}
			// 添加之前操作
			$this->add_before($save_data);
			if($model->validate(true)->create($save_data, true) !== false){
				$last_insert_id = $model->getLastInsID();
				// 添加之前操作
				$this->add_after($last_insert_id, $save_data);
				$this->success('添加成功！', $jump_url ? $jump_url : url('index', array('module' => $this->_module))); 
			} else {
				$this->error('添加失败！');
			}
		}

		return $this->view($tpl);
	}
	/**
	 * 添加之前操作
	 * @param [type] $data 添加数据
	 */
	protected function add_before($data){}
	/**
	 * 添加之后操作
	 * @param [type] $data [description]
	 */
	protected function add_after($insert_id, $data){}
	/**
	 * 默认修改
	 * @param  string $model_name 模型名称
	 * @param  array  $data       修改数据
	 * @param  string $jump_url   跳转URL
	 */
	public function edit($model_name = '', $data = array(), $jump_url = ''){
		$model_name   = $model_name ? $model_name : $this->request->controller();
		$tpl          = $this->_tpl ? $this->_tpl : '';
		$jump_url     = $jump_url ? $jump_url : $this->_jump_url;
		$filter_field = $this->_filter_field;
		$data = $data ? $data : array();

		$model  = model($model_name);
		$pk_key = $model->getPk();
		$pk_val = $this->_get($pk_key, '对不起，您的操作有误！');
		if($this->isPost()){
			$post_data = input('post.');
			//更新修改时间
			$post_data['edit_time'] = time();
			//合并表单及自定义字段
			$new_data = array_merge($post_data, $data);
			//处理数组字段成按逗号分隔字符串
			foreach ($new_data as $key => $value) {
				if(is_array($value)){
					$save_data[$key . '_array'] = $value;
					$value = implode(',', $value);
					if(substr($value, 0, 2) == '0,'){
						$value = substr($value, 2);
					}
				}
				$save_data[$key] = htmlspecialchars_decode($value);
			}
			//过滤不修改字段
			if($filter_field){
				foreach ($filter_field as $field) {
					unset($save_data[$field]);
				}
			}
			// 替换HTML内容的文件为本地路径
			// if($this->_html_field){
			// 	foreach ($this->_html_field as $field) {
			// 		$save_data[$field] = \app\common\File::html_osspath_local($save_data[$field]);
			// 	}
			// }
			
			// 验证器
			try{
				$validate = validate($model_name);
			}catch(\Exception $e){
				// 没有设置验证器，则不处理
				// var_dump($e);exit;
			}
			if($validate && !$validate->scene('edit')->check($save_data)){
				$this->error($validate->getError());
			}
			// 修改之前操作
			$this->edit_before($save_data);
			$where[$pk_key] = $pk_val;
			if($model->validate(true)->update($save_data, $where, true) !== false){
				// 修改之前操作
				$this->edit_after($pk_val, $save_data);
				$this->success('修改成功！', $jump_url ? $jump_url : url('index', array('module' => $this->_module))); 
			} else {
				$this->error('修改失败！');
			}
		}
		$info = $model->get($pk_val);
		$this->assign('info', $info);

		return $this->view($tpl);
	}
	/**
	 * 修改之前操作
	 * @param [type] $data 修改数据
	 */
	protected function edit_before($data){}
	/**
	 * 修改之后操作
	 * @param [type] $data [description]
	 */
	protected function edit_after($insert_id, $data){}

	/**
	 * 通用删除
	 * @param  string $model_name 模型名称
	 */
	public function delete($model_name = ''){
		$ids   = $this->_post('ids/a');
		$where = $this->_db_where ? $this->_db_where : '';
		
		//使用模型
		$model = db($model_name ? $model_name : $this->request->controller());
		// 主键KEY
		$pk_name = $model->getPk();
		$pk_val  = $this->_get($pk_name);
		if(!is_array($ids) && $pk_val){
			$where[$pk_name] = $pk_val;
			if($model->where($where)->delete() == true){
				$this->success('删除成功!', '');
			}else{
				$this->error('删除失败!');
			}
		}
		elseif(is_array($ids)){
			$where[$pk_name] = array('in', implode(',', $ids));
			$model->where($where)->delete();
			$this->success('批量删除成功!');
		}else{
			$this->error('请选择要删除内容!');
		}
	}
	/**
	 * 虚拟删除
	 * @param  string $model [description]
	 * @param  string $did   [description]
	 * @return [type]        [description]
	 */
	public function del($model_name = ''){
		$ids   = $this->_post('ids/a');
		$where = $this->_db_where ? $this->_db_where : '';
		//使用模型
		$model = db($model_name ? $model_name : $this->request->controller());
		// 主键KEY
		$pk_name = $model->getPk();
		$pk_val  = $this->_get($pk_name);
		if(!is_array($ids) && $pk_val){
			$data['is_delete']    = 1;
			$data['recycle_time'] = time();
			$where[$pk_name] = $pk_val;
			if($model->where($where)->update($data) == true){
				$this->success('删除成功!', '');
			}else{
				$this->error('删除失败!');
			}
		}
		elseif(is_array($ids)){
			$where[$pk_name] = array('in', implode(',', $ids));
			$data['is_delete']    = 1;
			$data['recycle_time'] = time();
			$model->where($where)->update($data);
			$this->success('批量删除成功!');
		}else{
			$this->error('请选择要删除内容!');
		}
	}
	//从回收站还原
	public function permit($model_name = ''){
		$ids = $this->_post('ids/a');

		//使用模型
		$model   = db($model_name ? $model_name : $this->request->controller());
		// 主键KEY
		$pk_name = $model->getPk();
		$pk_val  = $this->_get($pk_name);

		$data['recycle_time'] = 0;
		$data['is_delete']    = 0;
		if(!is_array($ids) && is_numeric($pk_val)){
			$where[$pk_name] = $pk_val;
			if($model->where($where)->update($data) == true){
				$this->success('还原成功!', '');
			}else{
				$this->error('还原失败!');
			}
		}
		elseif(is_array($ids)){
			$ids             = implode(',', $ids);
			$where[$pk_name] = array('in', $ids);
			$model->where($where)->update($data);
			$this->success('批量还原成功!');
		}
		else{
			$this->error('请选择要还原记录!');
		}
	}
	
	public function checkbox($model_name = '', $where = ''){
		$pk_val = $this->_get('pk_val', '主键val不能为空！');
		$pk_key = $this->_get('pk_key', '主键key不能为空！');
		$field  = $this->_get('field_name', '修改字段不能为空！');

		//使用模型
		$model = db($model_name ? $model_name : $this->request->controller());

		$where[$pk_key] = $pk_val;
		$value = $model->where($where)->value($field);

		$data[$field] = $value ? 0 : 1;
		$model->where($where)->update($data);

		$this->success('设置成功！', '');
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
			$this->error($empty_msg);
		}
		if($is_empty){
			$value = isset($default_value) ? $default_value : '';
		}

		return $value;
	}
	//操作步骤
	protected function add_step($title, $url = ''){
		$this->_title     = empty($this->_title) ? $title : $title . ' - ' . $this->_title;
		$this->_trail[]   = array('title'=>$title,'url'=>$url);
		$this->_cur_title = $title;
		return $this;
	}
	protected function view($tpl = ''){
		$this->assign('_title', $this->_title);
		$this->assign('_trail', $this->_trail);
		$this->assign('_cur_title', $this->_cur_title);

		return view($tpl);
	}
	// 请求模型
	protected function request(){
		return Request::instance();
	}
	// 是否为Ajax请求
	protected function isAjax(){
		return $this->request->isAjax();
	}
	// 是否为手机访问
	protected function isMobile(){
		return $this->request->isMobile();
	}
	// 是否为GET请求
	protected function isGet(){
		return $this->request->isGet();
	}
	// 是否为POST请求
	protected function isPost(){
		return $this->request->isPost();
	}
	protected function logout(){
		session('admin_id', null);
	}

}