<?php
/**
 * 用户相关
 */

namespace app\api\controller;
use app\api\model\HbModel;
use app\api\model\PasteRecord;
use app\api\model\PasteRecordModel;
use app\api\model\RechargeModel;
use app\api\model\UserModel;
use think\Cache;

class User extends Base
{
    //登录注册操作
    public function login(){

        $unionid = $this->_post('unionid','unionid不能为空！');

        $userModel = new UserModel();
        $user = $userModel->where('unionid',$unionid)->find();
        //查看是否存在用户
        if($user) {
            //查看用户是否禁用
            if($user['account_state'] == 0){
                return $this->sendError('100','账号已被禁用，请联系管理员！');
            }

            //存在更新会员数据
            $update = $userModel->where('unionid',$unionid)->update(array('last_login_time'=>time(),'last_login_ip'=>request()->ip()));
            $uid = $user['id'];
        }else{
            //不存在则添加用户
            $nick_name = $this->_post('nick_name');//昵称
            $device = $this->_post('device');//设备
            $sex = $this->_post('sex');//性别
            $area = $this->_post('area','','未知');//地区

            //不存在,添加会员
            $uid = $userModel->generate_member_id();
            $data['uid'] = $uid;
            $data['sex'] = $sex;
            $data['unionid'] = $unionid;
            $data['area'] = $area;
            $data['nick_name'] = $nick_name;
            $data['register_date'] = $data['last_login_time'] = time();
            $data['register_ip'] = $data['last_login_ip'] = request()->ip();

            $update = $userModel->insert($data);
            $uid = $userModel->getLastInsID();
        }

        if(!$uid){
            return $this->sendError('100','登录失败！');
        }


        //生成token
        $token = $userModel->token($uid);
        $return['token'] = $token['value'];
        $return['head'] = $user['head']?$user['head']:'';//头像
        $return['nick_name'] = $user['nickname']?$user['nickname']:'';//昵称

        if($token){
            return $this->sendSuccess($data,'登录成功！');
        }else{
            return $this->sendError('100','登录失败！');
        }
    }

    //登出操作
    public function logout(){
        $token = $this->_userToken;
        $redis = Cache::store('redis');
        $logout_token = $redis->rm(config('HB_TOKEN_PRE').$token);

        if($logout_token){
            return $this->sendSuccess('','退出成功！');
        }else{
            return $this->sendError('100','登出失败！');
        }
    }

    //获取用户自己信息
    public function getUserInfo(){
        $redis = Cache::store('redis');
        $uid = $redis->get(config("HB_TOKEN_PRE").$this->_userToken);
        $userModel = new UserModel();
        $user = $userModel->where(array('id'=>$uid))->find();
        if($user){
            $data['account_balance'] = 100;//余额
            $data['head'] = $user['head']?$user['head']:'';//头像
            $data['nick_name'] = $user['nickname']?$user['nickname']:'';//昵称

            return $this->sendSuccess($data,'获取数据成功！');
        }else{
            return $this->sendError('100','获取数据失败！');
        }
    }

    //获取其他用户信息
    public function getOtherUserInfo(){
        $user_id = $this->_userId;
        $other_uid = $this->_post('other_id','id不能为空');
        $userModel = new UserModel();
        $user = $userModel->alias('u')->join('t_paste_record pr','pr.accept_id=u.id AND pr.user_id='.$user_id,'LEFT')
                            ->where('id',$other_uid)
                            ->field('u.id,u.nick_name,u.head,u.sign,pr.id as pr_id')
                            ->find();
        if($user){
            $rechargeModel = new RechargeModel();
            $user['pay_total_money'] = $rechargeModel->where('user_id',$other_uid)->sum('money');//发出的红包总金额
            $pasteRecordModel = new PasteRecordModel();
            $user['fans_count'] = $pasteRecordModel->where('accept_id',$other_uid)->count('id');//粉丝数量
            return $this->sendSuccess($user,'获取数据成功！');
        }else{
            return $this->sendError('100','获取数据失败！');
        }
    }

    //添加关注
    public function addPasteRecord(){
        $accept_id = $this->_post('accept_id','accept_id不能为空');
        $userModel = new UserModel();
        $accept_info = $userModel->where('accept_id',$accept_id)->find();
        if($accept_info){
            $pasteRecordModel = new PasteRecordModel();
            $data = $pasteRecordModel->where(['user_id'=>$this->_userId,'accept_id'=>$accept_id])->find();
            if($data){
                return $this->sendError('100','已关注过该用户！');
            }else{
                $insert['user_id'] = $this->_userId;
                $insert['accept_id'] = $accept_id;
                $insert['create_time'] = time();
                $result = $pasteRecordModel->insert($insert);
                if($result){
                    return $this->sendSuccess('','关注成功！');
                }else{
                    return $this->sendError('100','请求失败！');
                }
            }
        }else{
            return $this->sendError('100','数据异常！');
        }
    }

    //取消关注
    public function cancelPasteRecord(){
        $pr_id = $this->_post('pr_id','pr_id不能为空');

        $pasteRecordModel = new PasteRecordModel();
        $delete = $pasteRecordModel->where('id',$pr_id)->delete();
        if($delete){
            return $this->sendSuccess('','已取消关注！');
        }else{
            return $this->sendError('100','请求失败！');
        }
    }

    //获取粉丝列表
    public function getFansList(){
        $page = $this->_post('page','',1);
        $pageSize = $this->_post('page_size','',config('PAGE_SIZE'));
        $pasteRecordModel = new PasteRecord();
        $data = $pasteRecordModel->alias('pr')->join('t_user u','pr.user_id=u.id','LEFT')
            ->where('pr.accept_id',$this->_userId)
            ->field('u.id,u.head,u.nick_name,u,sign')
            ->order('pr.create_time DESC')
            ->page($page,$pageSize)
            ->select()->toArray();

        return $this->sendSuccess($data,'success');
    }
}