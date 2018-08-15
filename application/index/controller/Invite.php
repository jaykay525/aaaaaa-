<?php
/**
 * Created by PhpStorm.
 * User: ukl.io
 * Date: 2018/4/26
 * Time: 10:44
 */

namespace app\index\controller;
use think\Request;
use think\Cache;

class Invite extends Common
{
    public function index(){
        $invitation_code = $this->_get('invitation');
        $credit_exchange_begin = db('config')->where(array('name'=>'credit_exchange_begin'))->value('val');
        $invite_award_credit = db('config')->where(array('name'=>'invite_award_credit'))->value('val');

        $this->assign('web_title','区块链项目评测平台');
        $this->assign('invitation_code',$invitation_code);
        $this->assign('credit_exchange_begin',$credit_exchange_begin);
        $this->assign('invite_award_credit',$invite_award_credit);
        return $this->view();
    }

    public function down(){

        //统一使用首页下载页面
        $this->redirect('Index/index');
        
    }

    public function getCountryCode(){
        $country = db('country')->select();
        return json('获取数据成功！',$country);
    }

    public function shortUrl(){
        $code = $this->_get('code');
        if($code){
            //查看短链接是否存在,查询code
            $short = db('short_url')->where(array('code'=>$code))->find();//db

            if($short){
                $this->redirect('/invite/index?invitation='.$short['invitation']);
            }else{
                return json('无效页面！');
            }
        }else{
            return json('页面错误！');
        }
    }

    /**
     * 接受邀请领福利
     */
    //接口验证邀请页面
    public function checkInvitation(){
        $invitation = $this->_get('invitation_code');
        $invitation = base64_decode($invitation);
        $inviter_member_id = decrypt($invitation,config('AES_KEY'),config('AES_IV'));//邀请人id
        $invitee_mobile = $this->_get('invitee_mobile');//被邀请人手机号码
        $invitee_country_code = $this->_get('invitee_country_code');//被邀请人国家代码
        $captcha_code = $this->_get('pic_code');  //图形验证码

        //查看邀请页面有效性
        if(!$inviter_member_id || !model('Invite')->is_exists($inviter_member_id)){
            return  $this->sendError('100','参数错误！');
        }
        if(!captcha_check($captcha_code)){
            return  $this->sendError('100','图形验证码错误！');
        }

        if(!$invitee_mobile){
            return  $this->sendError('100','手机号码不能为空！');
        }
        if(!$invitee_country_code){
            return  $this->sendError('100','电话代码不能为空！');
        }

        $params['invitee_mobile'] = $invitee_mobile;
        $params['invitee_country_code'] = $invitee_country_code;

        //验证手机号码是否已经注册
        if(db('member')->where(array('country_code'=>$invitee_country_code,'mobile'=>$invitee_mobile))->count()){
            return $this->sendError('100','该手机号码已注册UKL！');
        }

        //验证是否已接受邀请
        if(db('invitation')->where($params)->count()){
            return $this->sendError('100','该用户已被人邀请！');
        }

        return $this->sendSuccess('','验证通过！');
    }

    //接受邀请
    public function acceptInvitation(){
        $invitation = $this->_get('invitation_code');
        $invitation = base64_decode($invitation);
        $inviter_member_id = decrypt($invitation,config('AES_KEY'),config('AES_IV'));//邀请人id
        $invitee_mobile = $this->_get('invitee_mobile');//被邀请人手机号码
        $invitee_country_code = $this->_get('invitee_country_code');//被邀请人国家代码
        $mobile_code = $this->_get('mobile_code');//手机验证码

        //查看邀请页面有效性
        if(!$inviter_member_id || !model('Invite')->is_exists($inviter_member_id)){
            return  $this->sendError('100','参数错误！');
        }
        if(!$invitee_mobile){
            return  $this->sendError('100','手机号码不能为空！');
        }
        if(!$invitee_country_code){
            return  $this->sendError('100','电话代码不能为空！');
        }
        if(!$mobile_code){
            return  $this->sendError('100','短信验证码不能为空！');
        }

        //查看验证码有效性
        $redis = Cache::store('redis');

        //检查是否开启调试模式
        $smsConfig = model('Config') -> _get('','sms');
        $verify_smscode_debug = $smsConfig['verify_smscode_debug'];
        $smscode_whitelist = explode("\r\n", $smsConfig['smscode_whitelist']);

        if(($mobile_code!=  $redis->get('code_'.$invitee_country_code.$invitee_mobile)) && ($verify_smscode_debug == 0) && (in_array($invitee_mobile, $smscode_whitelist) === false)){
            return $this->sendError('100','验证码不正确！');
        }

        //增加数据
        $params['inviter_member_id'] = $inviter_member_id;//
        $params['invitee_mobile'] = $invitee_mobile;
        $params['invitee_country_code'] = $invitee_country_code;
        $params['create_time'] = time();
        $add = db('invitation')->insert($params);

        if($add){
            return $this->sendSuccess('','接受邀请成功！');
        }else{
            return $this->sendError('100','接受邀请失败！');
        }
    }

    //发送手机验证码
    public function sendCode(){
        $mobile = $this->_post('mobile','手机号码不能为空！');
        $country_code = $this->_post('country_code','电话代码不能为空！');
        if(!$mobile){
            return $this->sendError('100','手机号码不能为空！');
        }
        if(!$country_code){
            return $this->sendError('100','电话代码不能为空！');
        }
        if($country_code==86 && !preg_match("/^1[3456789]{1}\d{9}$/",$mobile)){
            return $this->sendError('100','手机号码格式错误！');
        }

        //检查是否开启调试
        $smsConfig = model('Config') -> _get('','sms');
        $verify_smscode_debug = $smsConfig['verify_smscode_debug'];
        $smscode_whitelist = explode("\r\n", $smsConfig['smscode_whitelist']);
        if($verify_smscode_debug == 1){
            return $this->sendSuccess('','调试模式已开启，短信验证码请随意输入！');
        }
        if(in_array($mobile, $smscode_whitelist) !== false){
            return $this->sendSuccess('','Please enter any string for verification, thank you!');
        }


        $code = rand(100000,999999);


        //存入redis
        $redis = Cache::store('redis');

        //单IP请求次数限制
        $ip = request()->ip();
        $ip_times = $redis->get($ip);//当前IP发送次数
        if($ip_times){
            if($ip_times<100){
                $redis->inc($ip);
            }else{
                return $this->sendError('100','该IP暂时无法发送！！');
            }
        }else{
            $redis->set($ip,1,300);
        }
        //限制发送时长
        $times = $redis->get('times_'.$country_code.$mobile);
        if($times){
            return $this->sendError('100','60s内不能多次发送验证码！');
        }

        //发送短信
        $smscode_tpl = model('Config')->_get('smscode_tpl', 'sms');
        $smscode_tpl = str_replace(
            array('#code#'),
            array($code),
            $smscode_tpl
        );
        $content = $smscode_tpl ? $smscode_tpl : '您的短信验证码为' . $code;
        if($country_code == 86){
            model("Sms")->send_type = 'ms';
            $result = model("Sms")->send($mobile,$content);
        }else{
            model("Sms")->send_type = 'isms';
            $result = model("Sms")->send($country_code.$mobile,$content);
        }

        $redis->set('code_'.$country_code.$mobile,$code,300);
        $redis->set('times_'.$country_code.$mobile,$mobile,60);


        if($result){
            return $this->sendSuccess('','发送成功！');
        }else{
            return $this->sendError('100','发送失败！');
        }
    }

    public function rules(){

        $mark = 'wyyq';

        $list = db('article')->where(array('mark'=>$mark))->find();

        if($list){
            $list['content'] = db('article_content')->where(array('article_id'=>$list['article_id']))->value('content');
        }

        $this->assign('list',$list);
        return $this->view();
    }
}