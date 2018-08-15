<?php
/***
 * 后端服务
 */
namespace app\client\controller;

use think\Console;
use think\Log;

class Index
{
    //增加文章访问量
    public function autoRead()
    {

        //默认参数
        $is_open_autoread = 0;
        $min_pv = 0;
        $max_pv = 0;
        $update_day = 1;

        $config = Conf('', 'admin');

        extract($config, EXTR_OVERWRITE);

        if($is_open_autoread !=1 ){

            return '服务未开启，无需执行任务！';

        }

        $update_day = $config['update_day'];
        $where['create_time'] = array('BETWEEN',array(time()-$update_day*86400,time()));
        $where['mark'] = array('IN','tgkt,xwzx');
        $article = db('article')->field('article_id,view_pv,title,create_time,mark')->where($where)->select();

        foreach($article as $key =>$val){
            $num = rand($min_pv,$max_pv);
            $add = db('article')->where(array('article_id'=>$val['article_id']))->setInc('view_pv',$num);

            $msg = '文章ID'.$val['article_id'].'标题《'.$val['title'].'》增加访问量'.$num;

            if($add){
                $msg .= '成功！';
            }else{
                $msg .= '失败!';
            }

            echo $msg;

            echo "\r\n";

        }

        return '任务执行完毕！';

    }
}
