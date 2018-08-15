<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/8/14
 * Time: 20:12
 */

namespace app\api\model;


use think\Model;

class HbModel extends Model
{
    protected $name = 'hb';

    public function addViewCount($hb_id){
        $this->where('id',$hb_id)->setInc('view_count');
    }
}