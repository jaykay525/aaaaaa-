<?php
namespace app\common\model;
use \think\Model;

class BaseModel extends Model{
	public $tips_info = '';
	public $params    = array();
	
	// 获取Session登录用户ID
	public function user_id(){
		$user_info = session('user_info');
		$user_id = $user_info['user_id'];

		return $user_id ? $user_id : 0;
	}
	/**
	 * 设置参数
	 * @param string $key   [description]
	 * @param string $value [description]
	 */
	public function setParams($key = '', $value = ''){
		$this->params[$key] = $value;
	}
	/**
	 * 获取参数
	 * @param  string $key [description]
	 * @return [type]      [description]
	 */
	public function getParams($key = ''){
		if(!$key){
			return '';
		}
		$value = $this->params[$key];
		return $value ? $value : '';
	}

	/**
	 * 类别名称获取器
	 * @param  string $content [description]
	 * @param  array  $data    [description]
	 * @return [type]          [description]
	 */
	public function getCategoryNameAttr($category_id = '', $data = array()){
		$category_id   = $category_id ? $category_id : $data['category_id'];
		$category_name = model('Category')->getName($category_id);
		
		return $category_name;
	}
	// 更新时间修改器
	public function setEditTimeAttr($value = '', $data = array()){
		return time();
	}
	// 写入IP
	protected function setIpAttr(){

        return request()->ip();
    }
	// 时间与当前说明
	public function getTimeAgoDescAttr($time = '', $data = array()){
		$time = $time ? $time : $data['create_time'];
		$time_desc = time_ago_desc($time);

		return $time_desc;
	}
	// 获取用户经验
	public function getUserExperienceAttr($user_id = '', $data = array()){
		$user_id = $user_id ? $user_id : $data['user_id'];
		$value   = db('UserData')->where(array('user_id' => $user_id))->value('experience');

		return $value ? $value : 0;
	}
	// 获取用户豆子
	public function getUserBeansAttr($user_id = '', $data = array()){
		$user_id = $user_id ? $user_id : $data['user_id'];
		$value = db('UserData')->where(array('user_id' => $user_id))->value('beans');

		return $value ? $value : 0;
	}
	// 获取用户问题数量
	public function getUserQuestionNumAttr($user_id = '', $data = array()){
		$user_id = $user_id ? $user_id : $data['user_id'];
		$value = db('UserData')->where(array('user_id' => $user_id))->value('question_num');

		return $value ? $value : 0;
	}
	// 获取用户回答数量
	public function getUserAnswerNumAttr($user_id = '', $data = array()){
		$user_id = $user_id ? $user_id : $data['user_id'];
		$value = db('UserData')->where(array('user_id' => $user_id))->value('answer_num');

		return $value ? $value : 0;
	}
	/**
	 * 用户名称
	 * @param  string $user_id 用户ID
	 * @param  array  $data    数据记录
	 */
	public function getUserNameAttr($user_id = '', $data = array()){
		$user_id = $user_id ? $user_id : $data['user_id'];
		if(is_numeric($user_id)){
			$user_name = model('User')->getShowNameAttr($user_id);
		}else{
			$user_name = $user_id ? $user_id : $data['user_name'];
		}

		return $user_name ? $user_name : '';
	}
	/**
	 * 用户手机号
	 * @param  string $user_id 用户ID
	 * @param  array  $data    数据记录
	 */
	public function getUserMobileAttr($user_id = '', $data = array()){
		$user_id = $user_id ? $user_id : $data['user_id'];
		$mobile  = '';
		if($user_id){
			$mobile  = model('User')->where(['user_id' => $user_id])->value('mobile');
		}

		return $mobile ? $mobile : '';
	}
	public function getUserHeadUrlAttr($user_id = '', $data = array()){
		$user_id  = $user_id ? $user_id : $data['user_id'];
		$head_url = model('User')->get_user_head_url($user_id);

		return $head_url ? $head_url : '';
	}
}