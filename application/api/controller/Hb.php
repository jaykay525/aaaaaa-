<?php
/**
 * 红包相关
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/8/14
 * Time: 22:01
 */

namespace app\api\controller;


use app\api\model\HbCommentModel;
use app\api\model\HbLikeModel;
use app\api\model\HbModel;

class Hb extends Base
{
    //发红包
    public function sendHb(){
        $content = $this->_post('content','请填写内容');
        $imgs = $this->_post('imgs','请至少选择一张图片');
        $img_list = explode(',',$imgs);
        if(count($img_list) <= 0){
            return $this->sendError('100','请至少选择一张图片');
        }
        $type = $this->_post('type','缺少类型');//红包类型 默认普通红包
        $location_x = $this->_post('location_x','',0);
        $location_y = $this->_post('location_y','',0);
        $location_type = $this->_post('location_type','请选择红包发布范围');
        $total_money = $this->_post('total_money','请填写总金额');
        $total_count = $this->_post('total_count','请填写红包个数');
        $min_hb_money = config('MIN_SINGLE_HB_MONEY');//单个红包最低金额
        if($total_money/$total_count < $min_hb_money){
            return $this->sendError('100','单个红包不能低于'.$min_hb_money.'元');
        }

        //执行微信支付扣款

        $hbModel = new HbModel();
        $data['user_id'] = $this->_userId;
        $data['type'] = $type;
        $data['content'] = $content;
        $data['imgs'] = $imgs;
        $data['location_x'] = $location_x;
        $data['location_y'] = $location_y;
        $data['location_type'] = $location_type;
        $data['total_money'] = $data['current_money'] = $total_money;
        $data['total_count'] = $total_count;
        $data['create_time'] = $data['update_time'] = time();
        $insert = $hbModel->insert($data);
        if($insert){
            $redis = Cache::store('redis');
            $redis->set(config('HB_COUNT_PRE').$hbModel->getLastInsID(),$total_count);
            return $this->sendSuccess('','发红包成功！');
        }else{
            return $this->sendError('100','发红包失败！');
        }
    }

    //往红包塞钱
    public function addHbMoney(){
        $hb_id = $this->_post('hb_id','缺少红包id');
        $hbModel = new HbModel();
        $hb_info = $hbModel->where('id',$hb_id)->find();
        if($hb_info){
            $total_money = $this->_post('total_money','请填写总金额');
            $total_count = $this->_post('total_count','请填写红包个数');
            $min_hb_money = config('MIN_SINGLE_HB_MONEY');//单个红包最低金额
            if($total_money/$total_count < $min_hb_money){
                return $this->sendError('100','单个红包不能低于'.$min_hb_money.'元');
            }

            //执行微信支付扣款

            $update['total_money'] = $hb_info['total_money'] + $total_money;
            $update['current_money'] = $hb_info['current_money'] + $total_money;
            $update['total_count'] = $hb_info['total_count'] + $total_count;
            $result = $hbModel->where('id',$hb_id)->update($update);
            if($result){
                $redis = Cache::store('redis');
                $logout_token = $redis->inc(config('HB_COUNT_PRE').$hb_id,$total_count);
                return $this->sendSuccess('','续费成功！');
            }else{
                return $this->sendError('100','续费失败！');
            }
        }else{
            return $this->sendError('100','该红包异常');
        }
    }

    //红包点赞
    public function addHbLike(){
        $hb_id = $this->_post('hb_id','缺少红包id');
        $like = $this->_post('like','缺少参数');
        if(!in_array($like,[0,1])){
            return $this->sendError('100','传参错误！');
        }
        $hbLikeModel = new HbLikeModel();
        $info = $hbLikeModel->isExistsLike($this->_userId,$hb_id);
        if($info){
            if($info['like'] == $like){
                return $this->sendError('100','重复操作！');
            }else{
                $result = $hbLikeModel->updateLikeData($this->_userId,$hb_id,$like);
            }
        }else{
            $result = $hbLikeModel->addLikeData($this->_userId,$hb_id,$like);
        }

        if($result){
            return $this->sendSuccess('','操作成功！');
        }else{
            return $this->sendError('100','操作失败！');
        }
    }

    //红包评论
    public function addHbComment(){
        $hb_id = $this->_post('hb_id','缺少红包id');
        $accept_id = $this->_post('accept_id','',0);
        $comment = $this->_post('comment','评论内容不能为空');

        $redis = Cache::store('redis');
        $last_comment = $redis->get(config('HB_COMMENT_PRE').$this->_userId.'_'.$hb_id);
        if(!$last_comment){
            $hbModel = new HbModel();
            $hb = $hbModel->get($hb_id);
            if($hb){
                $hbCommentModel = new HbCommentModel();
                $data['hb_id'] = $hb_id;
                $data['accept_id'] = $accept_id;
                $data['comment'] = $comment;
                $data['create_time'] = time();
                $insert = $hbCommentModel->insert($data);
                if($insert){
                    $redis->set(config('HB_COMMENT_PRE').$this->_userId.'_'.$hb_id,1,config('COMMENT_INTERVAL_TIME'));
                    return $this->sendSuccess('','评论成功');
                }else{
                    return $this->sendError('100','评论失败');
                }
            }else{
                return $this->sendError('100','该红包不存在');
            }
        }else{
            return $this->sendError('100',config('COMMENT_INTERVAL_TIME').'秒内不能重复评论');
        }
    }

    //获取红包评论列表
    public function getHbCommentList(){
        $hb_id = $this->_post('hb_id','缺少红包id');
        $page = $this->_post('page','',1);
        $pageSize = $this->_post('page_size','',config('PAGE_SIZE'));

        $hbCommentModel = new HbCommentModel();
        $data = $hbCommentModel->alias('hbc')->join('t_user u','hbc.user_id=u.id','LEFT')->join('t_user u1','hbc.accept_id=u1.id','LEFT')
            ->where('hbc.hb_id',$hb_id)->order('hbc.create_time DESC')->field('hbc.id,hbc.user_id,u.nick_name,u.head,hbc.`comment`,hbc.create_time,hbc.accept_id,u1.nick_name as accept_nick_name,u1.head as accept_head')
            ->page($page,$pageSize)
            ->select();

        return $this->sendSuccess($data,'success');
    }

    //获取红包详情
    public function getHbInfo(){
        $hb_id = $this->_post('hb_id','缺少红包id');
        $hbModel = new HbModel();
        $hb = $hbModel->get($hb_id);
        if($hb){

        }else{
            return $this->sendError('100','该红包不存在');
        }
    }
}