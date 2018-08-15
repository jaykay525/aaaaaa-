<?php
namespace app\common\model;

class Config extends BaseModel{

	/**
	 * 获取配置信息
	 * @param  [type] $name       KEY
	 */
	public function _get($name = '', $type = 'system'){
		if($name){
			$where['name'] = $name;
		}
		if($type){
			$where['type'] = $type;
		}
		$where['status'] = 1;
		$list   = model('Config')->where($where)->select();
		$config = array();
		foreach ($list as $rs) {
			$config[$rs['type']][$rs['name']] = trim($rs['val']);
		}

		return $name ? $config[$type][$name] : $config[$type];
	}

	public function get_setting($type = 'sms'){
		$list  = config('setting.setting');
		$model = model('Config');

		$conf_data = $list[$type];
		$where['type'] = $type;
		foreach ($conf_data as $rs) {
			$where['name'] = $rs['name'];
			$val = $model->where($where)->value('val');
			$rs['val'] = $val != NULL ? $val : $rs['val'];
			$new_list[] = $rs;
		}

		return $new_list;
	}
	/**
	 * 判断名称是否包含过滤内容
	 * @param  string  $content 需要判断内容
	 * @param  string  $name    [description]
	 * @param  string  $type    [description]
	 */
	public function is_name_filter($content = '', $name = 'name_filter', $type = 'user'){
		$filter_data = $this->_get($name, $type);
		$filter_list = explode(PHP_EOL, $filter_data);
		$is_filter   = false;
		// 去掉空格
		$content     = str_replace(array(' ', '　'), '', $content);
		foreach ($filter_list as $filter) {
			$filter = trim($filter);
			if(strpos($content, $filter) !== false){
				$is_filter = true;
				$this->tips_info = $filter;
				break;
			}
		}

		return $is_filter;
	}
	/**
	 * 过滤内容
	 * @param  string $content [description]
	 * @param  string $name    [description]
	 * @param  string $type    [description]
	 * @return [type]          [description]
	 */
	public function name_filter($content = '', $name = 'name_filter', $type = 'user'){
		$filter_data = $this->_get($name, $type);
		$filter_list = explode(PHP_EOL, $filter_data);
		$search = [];
		// 去掉空格
		$content = str_replace(array(' ', '　'), '', $content);
		foreach ($filter_list as $filter) {
			$search[] = trim($filter);
		}
		$content = str_replace($search, '', $content);

		return $content;
	}
}