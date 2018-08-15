<?php
namespace app\admin\model;
use app\common\model\BaseModel;

class AppSet extends BaseModel{

	protected $insert = ['app_id'];
	
	// 添加时自动生成AppID
	public function setAppIdAttr($value, $data){
		$app_id = generate_id();
		return $app_id ? $app_id : '';
	}
}