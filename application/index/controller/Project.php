<?php
/**
 * 项目
 */
namespace app\index\controller;
use \think\Request;

class Project extends Common{

    //项目详情
    public function detail(){
        $project_id = $this->_get('project_id');
        $token = $this->_get('invite');
        $invite_url = config('WEB_URL').'/'.$token;
        $info = model('news')->getProjectInfo($project_id);
        $info['wechat_head'] = db('file')->where(array('file_id'=>$info['wechat_head_id']))->value('filepath');
        $info['cover_path'] = db('file')->where(array('file_id'=>$info['imgs_id']))->value('filepath');
        if($info['cover_path']){
            $ext = db('file')->where(array('file_id'=>$info['imgs_id']))->value('ext');
            $info['cover_path'] = $info['cover_path'].'@w750_h220.'.$ext;
        }

        //样式、标签替换
        $info['project_team'] = $this->richTextReplace($info['project_team']);
        $info['introduction'] = $this->richTextReplace($info['introduction']);

        $this->assign('info',$info);
        $this->assign('invite_url',$invite_url);
        return $this->view();
    }

    /**
     * 富文本处理
     * @param $data 待处理的文本
     * @return mixed|null|string|string[]
     */
    public function richTextReplace($data){

        //样式、标签替换
        $data = preg_replace("/style=.+?['|\"]/i",'',$data);
        $data = strip_tags($data,'<img><p><a><br>');

        return $data;
    }
}
