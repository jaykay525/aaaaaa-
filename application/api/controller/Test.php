<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/8/14
 * Time: 22:42
 */

namespace app\api\controller;


use app\api\model\UserModel;
use think\Cache;
use think\Controller;

class Test
{
    public function xxk(){
        $redis = Cache::store('redis');
        echo $redis->set('testnum',10);
    }

    public function xxk1(){
        $redis = Cache::store('redis');
        echo $redis->inc('testnum');
    }

    public function xxk2(){
        $redis = Cache::store('redis');
        echo $redis->get('testnum');
    }

    public function xxk3(){
        $userModel = new UserModel();
        print_r($userModel->paginate(config('PAGE_SIZE')));
    }
}