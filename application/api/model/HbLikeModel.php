<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/8/15
 * Time: 9:46
 */

namespace app\api\model;


use think\Model;

class HbLikeModel extends Model
{
    protected $name = 'hb_like';

    public function isExistsLike($user_id,$hb_id){
        $result = $this->where(['user_id'=>$user_id,'hb_id'=>$hb_id])->find();
        return $result;
    }

    public function addLikeData($user_id,$hb_id,$like){
        $insert['user_id'] = $user_id;
        $insert['hb_id'] = $hb_id;
        $insert['like'] = $like;
        $insert['create_time'] = $insert['update_time'] = time();
        $result = $this->insert($insert);
        return $result;
    }

    public function updateLikeData($user_id,$hb_id,$like){
        $update['like'] = $like;
        $update['update_time'] = time();
        $result = $this->where(['user_id'=>$user_id,'hb_id'=>$hb_id])->update($update);
        return $result;
    }
}