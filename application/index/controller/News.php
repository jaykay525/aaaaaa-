<?php
/**
 * 资讯类
 * 新闻资讯、糖果空投
 */
namespace app\index\controller;
use \think\Request;

class News extends Common{

    //糖果空投
    public function drop(){
        $article_id = $this->_get('article_id');
        $token = $this->_get('invite');
        $invite_url = config('WEB_URL').'/'.$token;
        $info = model('news')->getArticleInfo($article_id);
        $info['content'] = $this->richTextReplace($info['content'],1);
        $this->assign('info',$info);
        $this->assign('invite_url',$invite_url);
        return $this->view();
    }

    //新闻资讯
    public function detail(){
        $news_id = $this->_get('news_id');
        $token = $this->_get('invite');
        $invite_url = config('WEB_URL').'/'.$token;
        $info = model('news')->getNewsInfo($news_id);
        $this->assign('info',$info);
        $this->assign('invite_url',$invite_url);
        return $this->view();
    }


    /**
     * 富文本处理
     * @param $data 待处理的文本
     * @return mixed|null|string|string[]
     */
    public function richTextReplace($data,$type=0){

        //图片地址替换
        $data = str_replace(array(
            '<img src=\"/uploads/ueditor/',
            '<img src="/uploads/ueditor/'),
            '<img src="'.config('WEB_URL').'/uploads/ueditor/',
            $data);

        if($type){
            //样式、标签替换
            $data = preg_replace("/style=.+?['|\"]/i",'',$data);
            $data = strip_tags($data,'<img><p><a><br>');

            //增加样式(字体使用安卓和苹果默认字体) old-color:#727171
            $style = "<style> body,p{color: #727171; font-family: Droidsansfallback,'Droid Sans','Heiti SC',Helvetica,HelveticaNeue; font-size: 14px;line-height: 2;} img{ text-align: center; max-width: 100% !important; } </style>";
            $data  = $style.$data;
        }

        return $data;
    }

}
