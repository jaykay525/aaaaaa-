<?php
/**
 * Created by PhpStorm.
 * User: ukl.io
 * Date: 2018/4/17
 * Time: 10:26
 */
namespace app\api\model;
use think\Model;
use think\Cache;

class UserModel extends Model
{
    protected $name = 'user';

    public function ticket($uid){
        $redis = Cache::store('redis');
        $name = md5('ticket_'.$uid.time());
        $ticket = $uid;//键值
        $expire = 30*24*60*60;

        $redis->rm($name);//先删除账号的redis
        $redis->set(config('UKL_TICKET_PRE').$name,$ticket,$expire);

        $data['value'] = $name;
        $data['expire'] = $expire;

        return $data;
    }

    public function token($uid){
        $redis = Cache::store('redis');
        $name = md5('token_'.$uid.time());
        $token = $uid;
        $expire = 7*24*60*60;

        $redis->rm($name);//先删除账号的redis
        $redis->set(config('UKL_TOKEN_PRE').$name,$token,$expire);

        $data['value'] = $name;
        $data['expire'] = $expire;

        return $data;
    }

    /**
     * 生成用户ID
     */
    public function generate_member_id(){
        //随机生成9位数的用户ID
        $chars   = str_repeat('012356789', 3);
        $chars   = str_shuffle($chars);
        $member_id = substr($chars, 0, 9);
        //第一位为0的处理
        if(substr($member_id, 0, 1) == 0){
            $rand_id = str_shuffle(str_replace('0', '', $member_id));
            $user_id = substr($rand_id, 0, 1) . substr($member_id, 1);
        }
        //如果存在，再次生成ID
        if($this->is_exists($member_id)){
            return $this->generate_member_id();
        }
        return $member_id;
    }

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

    /**
     * 判断该钱包地址是否已被绑定
     * @param  [type]  $purse 钱包地址
     * @return boolean          [description]
     */
    public function is_bindPurse($purse){
        $find = db('member') -> where(array('purse'=>$purse))->find();
        return $find;
    }

}