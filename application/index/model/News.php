<?php
/**
 * Created by PhpStorm.
 * User: ukl.io
 * Date: 2018/4/26
 * Time: 15:48
 */
namespace app\index\model;
use \think\Request;

class News
{
    // 获取文章详情
    public function getArticleInfo($article_id){

        $info = db('article')->join('t_article_content','t_article_content.article_id=t_article.article_id')
            ->join('t_file','t_file.file_id=t_article.cover_id')
            ->where(array('t_article.article_id'=>$article_id))
            ->find();


        return $info;
    }

    // 获取新闻详情
    public function getNewsInfo($article_id){
        $info = db('article')->join('t_article_content','t_article_content.article_id=t_article.article_id')
            ->where(array('t_article.article_id'=>$article_id))
            ->find();
        $info['time_ago'] = time_ago_desc_app($info['create_time']);
        return $info;
    }

    // 获取项目详情
    public function getProjectInfo($project_id){
        $info = db('project')->where(array('project_id'=>$project_id))->find();

        return $info;
    }
}