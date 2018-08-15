<?php
namespace app\admin\controller;

class Config extends Base{

	public function setting(){

        $type = $this->_get('type');

		$model = model('Config');
		$list  = $model->get_setting($type);
		if($this->isPost()){
			foreach ($list as $rs) {
				unset($data, $where);
				$value = ($_POST[$rs['name']]);
				if(is_array($value)){
					$value = implode(',', $value);
					if(substr($value, 0, 2) == '0,'){
						$value = substr($value, 2);
					}
				}
				$value = trim($value);

				$where['type'] = $type;
				$where['name'] = $rs['name'];
				if($model->where($where)->count()){
					$data['val'] = $value;
					$model->update($data, $where, true);
				}else{
					$rs['type'] = $type;
					$rs['val']  = $value;
					$model->create($rs, true);
				}
			}
			$this->success('设置成功！');
		}

		$this->assign('list', $list);
		return $this->view();
	}
}