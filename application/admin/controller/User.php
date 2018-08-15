<?php
namespace app\admin\controller;

use app\admin\model\UserModel;

class User extends Base{
	public function index(){
	    $userModel = new UserModel();
	    $param['user_id'] = input('user_id');
        $param['nick_name'] = input('nick_name');
        $param['device'] = input('device');
        $param['account_state'] = input('account_state');
        $result = $userModel;
        if(!empty($param['user_id'])){
            $result = $result->where('id',$param['user_id']);
        }

        if(!empty($param['nick_name'])){
            $result = $result->where('nick_name','like','%'.$param['nick_name'].'%');
        }

        if(!empty($param['device'])){
            $device = $param['device'] == 'ios'?2:($param['device'] == 'android'?1:3);
            $result = $result->where('id',$device);
        }

        if(!empty($param['account_state'])){
            $result = $result->where('account_state',$param['account_state']);
        }

        $info = $result->paginate(config('PAGE_SIZE'));
        $this->assign([
            'list' => $info,
            'page' => $info->render(),
            'param' => $param,
        ]);
        return $this->view();
	}
}