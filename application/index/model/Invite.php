<?php
/**
 * Created by PhpStorm.
 * User: ukl.io
 * Date: 2018/4/26
 * Time: 15:48
 */
namespace app\index\model;
use \think\Request;

class Invite
{
    /**
     * 判断用户是否存在
     * @param  [type]  $user_id 用户ID
     * @return boolean          [description]
     */
    public function is_exists($member_id){
        $where['member_id'] = $member_id;
        $count = db('member')->where($where)->count();
        return $count > 0;
    }
}