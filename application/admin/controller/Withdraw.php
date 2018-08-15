<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/8/15
 * Time: 15:46
 */

namespace app\admin\controller;


use app\admin\model\WithdrawModel;

class Withdraw extends Base
{
    public function index(){
        $withdrawModel = new WithdrawModel();

        $info = $withdrawModel->alias('wd')->join('t_user u','wd.user_id=u.id','LEFT')->order('wd.status,wd.create_time DESC')
            ->field('wd.*,u.uid,u.nick_name')
            ->paginate(config('PAGE_SIZE'));
        $this->assign([
            'list' => $info,
            'page' => $info->render(),
        ]);
        return $this->view();
    }
}