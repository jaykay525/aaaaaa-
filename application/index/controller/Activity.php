<?php
namespace app\index\controller;
use \think\Request;

class Activity extends Common{
    public function detail(){
        $token = $this->_get('invite');
        $activity_id = $this->_get('activity_id');
        $join_num = db('activity') -> field('join_num + vt_join_num as join_num') ->where(array('activity_id'=>$activity_id)) ->find();
        $join_num = $join_num['join_num'];
        $invite_url = config('WEB_URL').'/'.$token;

        $info = db('activity')
            -> alias('a')
            -> join('t_file b','a.banner_id = b.file_id')
            -> where(array('activity_id'=>$activity_id))
            ->find();
        $info['filepath'] = config('WEB_URL').$info['filepath'];

        $this->assign('info',$info);
        $this->assign('invite_url',$invite_url);
        $this->assign('join_num',$join_num);
        return $this->view();
    }
}