<?php
namespace app\common\model;

class Admin extends BaseModel{
	// 获取状态类型说明
	public function getStatusDescAttr($value, $data){
		$status = [0 => '禁用', 1 => '正常', 99 => '正常'];
		return $status[$data['status']];
	}
	// 获取状态颜色类
	public function getStatusColorClassAttr($value, $data){
		$status = [0 => 'danger', 1 => 'primary', 99 => 'primary'];
		return $status[$data['status']];
	}
	// 获取管理员城市名称
	public function getCityNameAttr($value, $data){
		$city_name = model('Area')->getNames($data['city_id']);
		return $city_name ? $city_name : '全市';
	}
	// 获取管理员角色名称
	public function getRoleNameAttr($role_id = '', $data = array()){
		$role_id = $role_id ? $role_id : $data['role_id'];
		$name = $data['status'] == 99 ? '超级管理员' : model('AdminRole')->where('role_id', $role_id)->value('title');
		return $name;
	}
	// 获取权限字段数组数据
	public function getAuthsArrayAttr($value, $data){
		return unserialize($value);
	}
	// 保存时权限字段进行序列化
	public function setAuthsAttr($value, $data){
		$data = serialize($data['auths_array']);
		return $data ? $data : '';
	}
}