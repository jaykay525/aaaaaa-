<?php
namespace app\admin\widget;
use \think\Controller;

class Widget extends Controller{

	/**
	 * 上传插件
	 * @param  string  $show_name    显示名称
	 * @param  string  $field_name   字段名称
	 * @param  string  $file_value   字段值
	 * @param  string  $show_desc    显示描述
	 * @param  integer $is_mulit     是否上传多个
	 * @param  string  $upload_type  上传类型
	 * @param  string  $upload_ext   上传后缀
	 * @param  integer $is_wrap_name 是否包含HTML
	 * @param  integer $is_upload    是否上传
	 */
	public function upload_file($show_name = '', $field_name = 'file_id', $file_value = '', $show_desc = '', $is_mulit = 0, $upload_type = 'img', $upload_ext = '', $is_wrap_name = 1, $is_upload = 1){
		
		//默认配置
		$field_name   = $field_name ? $field_name : 'images_id';//上传字段
		$upload_type  = $upload_type ? $upload_type : 'img';//文件类型
		$show_name    = $show_name ? $show_name : ($upload_type == 'img' ? '图片' : '文件');//显示名称
		$show_desc    = $show_desc ? '(' . $show_desc . ')' : '';
		$is_wrap_name = $is_wrap_name == 1 ? 1 : 0;
		$upload_ext   = $upload_ext ? $upload_ext : '';
		$is_mulit     = $is_mulit == 1 ? 1 : 0;
		$is_upload    = $is_upload == 1 ? 1 : 0;

		if($file_value){
			$upload_list = model('File')->where('file_id', 'in', $file_value)->select();
		}

		$this->assign('is_upload', $is_upload);
		$this->assign('is_mulit', $is_mulit);
		$this->assign('upload_list', $upload_list);
		$this->assign('upload_type', $upload_type);
		$this->assign('show_name', $show_name);
		$this->assign('show_desc', $show_desc);
		$this->assign('is_wrap_name', $is_wrap_name);
		$this->assign('field_name', $field_name);
		$this->assign('file_value', $file_value);
		$this->assign('upload_ext', $upload_ext);

		return $this->fetch('widget/upload_file');
	}
	/**
	 * 选择类别插件
	 * @param  string  $show_name       显示名称
	 * @param  string  $field_name      字段名称
	 * @param  string  $field_value     字段值
	 * @param  string  $category_module 类别模块
	 * @param  string  $show_desc       显示描述
	 * @param  integer $is_wrap_name    是否包含HTML
	 * @param  integer $is_tree         是否树形结构
	 */
	public function category_select($show_name = '', $field_name = 'category_id', $field_value = '', $category_module = '', $show_desc = '', $is_required = 0, $is_wrap_name = 1, $is_tree = 0){
		//默认配置
		$field_name   = $field_name ? $field_name : 'category_id';//字段
		$show_name    = $show_name ? $show_name : '类别';//显示名称
		$show_desc    = $show_desc ? '(' . $show_desc . ')' : '';
		$is_wrap_name = $is_wrap_name == 1 ? 1 : 0;
		$is_tree      = $is_tree == 1 ? 1 : 0;
		$is_required  = $is_required == 1 ? 1 : 0;

		$where['module'] = $category_module;
		$where['status'] = 1;
		$category_list = model('Category')->where($where)->order('order_id,category_id desc')->select();

		if($is_tree){
			import('Tree', EXTEND_PATH, '.class.php');
			$tree = new \Tree($category_list, 'category_id', 'parent_id');
			$category_list = $tree->getTrees();
			foreach ($category_list as $rs) {
				$rs['title'] = $rs['prefix_title'];
				$new_list[] = $rs;
			}
			$category_list = $new_list;
		}

		$this->assign('show_name', $show_name);
		$this->assign('show_desc', $show_desc);
		$this->assign('is_wrap_name', $is_wrap_name);
		$this->assign('field_name', $field_name);
		$this->assign('field_value', $field_value);
		$this->assign('is_required', $is_required);
		$this->assign('_category_list', $category_list);

		return $this->fetch('widget/category_select');
	}

	public function area_select($show_name = '', $is_wrap_name = 1, $is_required = 1, $is_disabled = 0, $show_field = 'province_id,city_id', $province_id = 4, $city_id = 60, $area_id = 587){
		$model = model('Area');
		$province_list = $model->where(array('type' => 1))->select();

		$show_field_arr   = explode(',', $show_field);
		$is_show_province = in_array('province_id', $show_field_arr);
		$is_show_city     = in_array('city_id', $show_field_arr);
		$is_show_area     = in_array('area_id', $show_field_arr);

		if($province_id && $is_show_city){
			$city_list = $model->where(array('type' => 2, 'parent_id' => $province_id))->select();
		}

		if($city_id && $is_show_area){
			$area_list = $model->where(array('type' => 2, 'parent_id' => $city_id))->select();
		}
		if($is_disabled){
			$_widget_province_name = $model->getNames($province_id);
			$_widget_city_name     = $model->getNames($city_id);
			$_widget_area_name     = $model->getNames($area_id);
		}

		$this->assign('uniqid', md5(uniqid()));
		$this->assign('is_show_province', $is_show_province);
		$this->assign('is_show_city', $is_show_city);
		$this->assign('is_show_area', $is_show_area);
		$this->assign('city_id', $city_id);
		$this->assign('area_id', $area_id);
		$this->assign('show_name', $show_name);
		$this->assign('province_id', $province_id);
		$this->assign('province_list', $province_list);
		$this->assign('city_list', $city_list);
		$this->assign('area_list', $area_list);
		$this->assign('is_disabled', $is_disabled);
		$this->assign('is_required', $is_required);
		$this->assign('is_wrap_name', $is_wrap_name);
		$this->assign('_widget_province_name', $_widget_province_name);
		$this->assign('_widget_city_name', $_widget_city_name);
		$this->assign('_widget_area_name', $_widget_area_name);

		return $this->fetch('widget:area_select');
	}


}






