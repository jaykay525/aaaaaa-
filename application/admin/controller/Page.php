<?php
namespace app\admin\controller;

class Page extends Base{

	public function index(){
		$info    = model('Page')->where(array('module' => $this->_module))->find();
		$page_id = $info['page_id'];

		if($this->isPost()){
			$data['title']   = $this->_post('title', '标题不能为空！');
			$data['content'] = $this->_post('content');
			if($page_id){
				$data['edit_time'] = time();
				model('Page')->where(array('page_id' => $page_id))->update($data);
			}else{
				$data['edit_time']   = time();
				$data['create_time'] = time();
				$data['module']      = $this->_module;
				model('Page')->create($data);
			}

			$this->success('保存成功！');
		}
		$this->assign('info', $info);
		return $this->view();
	}
}