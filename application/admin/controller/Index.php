<?php
namespace app\admin\controller;

class Index extends Base{
    public function index(){
    	foreach ($this->_top_menu_list as $rs) {
            if($rs['show'] != 'no'){
                $jump_url = $rs['url'];
                break;
            }
        }
        if(!$jump_url){
            $this->error('对不起，您没有权限！');
        }
        $this->redirect($jump_url);
    }
    public function system_info(){
    	return $this->view();
    }
}
