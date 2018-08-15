<?php
namespace app\admin\controller;

class Sample extends Base{
	// 列表页面
    public function page_index(){
    	return parent::index('Article');
    }
    // 添加修改
    public function page_edit(){

    	return $this->view();
    }
    // 按钮
    public function layout_button(){

    	return $this->view();
    }
    // 图标
    public function layout_icon(){

    	return $this->view();
    }
    // 表单
    public function layout_form(){
    	return $this->view();
    }
}
