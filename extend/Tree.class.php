<?php
class Tree {
	private $_datas;
	
	private $_field = 'id';
	
	private $_parentField = 'parent_id';
	
	private $_trees = null;
	
	private $_children;
	
	private $_parent;
	
	private $_parentNav = array();
	
	private $_pValue = 0;
	
	public function __construct($data, $field = 'id', $parentField = 'parent_id', $pValue = 0){
		$this->_datas = $data;
		if (!empty($field)) {
			$this->_field = $field;
		}
		$this->_pValue = $pValue;
		if (!empty($parentField)) {
			$this->_parentField = $parentField;
		}
		
		$this->_children();
	}
	
	private function _children($pid = null){
		if (empty($this->_datas)) return;
		
		foreach ($this->_datas as $vo){
			if ($pid === null){
				$this->_children($vo[$this->_field]);
				$pv = (int)$vo[$this->_parentField];
				if (!$pv || $pv == $this->_pValue) $this->_parent[] = $vo;
				unset($pv);
			}else{
				if($vo[$this->_parentField] == $pid)
					$this->_children[$pid][] = $vo;
			}
		}
	}
	
	private function _hasChildren($id){
		return isset($this->_children[$id]) && !empty($this->_children[$id]);
	}
	
	private function _recur($data=null){
		$data = $data === null ? $this->_parent : $data;
		if (empty($data)) return;
		$j = 0;
		$count = count($data);
		foreach ($data as $vo){
			if($vo[$this->_parentField] <= 0 || $vo[$this->_parentField] == $this->_pValue){
				$vo['grade'] = 0;
				$vo['prefix'] = '';
				if($vo['title']) $vo['parentStr'] = $vo['title'];
			}else{
				if($vo['title']){
					$pstr = $this->_trees[$vo[$this->_parentField]]['parentStr'];
					$vo['parentStr'] = $pstr . ':' . $vo['title'];
				}
				$vo['grade']  = $this->_trees[$vo[$this->_parentField]]['grade'] + 1;
				$vo['prefix'] = $this->_trees[$vo[$this->_parentField]]['prefix'].'─';
			}
			$vo['isLower'] = 0;
			if ($this->_hasChildren($vo[$this->_field])){
				$vo['isLower'] = 1;
			}
			$this->_trees[$vo[$this->_field]] = $vo;
			if ($this->_hasChildren($vo[$this->_field])){
				$this->_recur($this->_children[$vo[$this->_field]]);
			}
		}
	}
	
	public function getTrees($imgPath=null){
		$imgPath ? $this->_getNavTree($imgPath) : $this->_recur();
		if($this->_trees){
			$i = 1;
			$count = count($this->_trees);
			foreach ($this->_trees as $rs) {
				if($i==1){
					$str = '┌';
				}else if ($count == $i) {
					$str = '└';
				}else{
					$str = '├';
				}
				// var_dump($count.'||'.$i.'===>'.($count==$i).'--->'.$str);
				$rs['prefix_title'] = $str . $rs['prefix'] . $rs['title'];
				$list[] = $rs;
				$i++;
			}
		}
		return $list;
	}
	
	public function getParent($pid){
		if (empty($this->_datas) || !$pid) return array();
		foreach ($this->_datas as $vo){
			if ($vo[$this->_field] == $pid){
				$this->_parentNav[] = $vo;
				if ($vo[$this->_parentField] > 0 || $vo[$this->_parentField] != $this->_pValue)
					$this->getParent($vo[$this->_parentField]);
				break;
			}
		}
		
		return array_reverse($this->_parentNav);
	}
	
	private function _getNavTree($imgPath,$data=null){
		$data = $data === null ? $this->_parent : $data;
		if (empty($data)) return;
		$count = count($data);
		$firstClass = $count > 1 ? 'elem-node-first' : 'single';
		$j = 0;
		$this->_trees .= '<ul>';
		foreach ($data as $vo){
			$_cls = $vo['noauth'] == 1 ? 'no-view-channel' : '';
			$attr = ' target="main" ' . ($_cls ? ' class="' . $_cls . '" ' : '');
			$rs = getModule($vo['moduleid']);
			if($vo['channeltype'] == 'in'){
				$href = U($rs['identy'] . '/' . $rs['method'],'ch=' . $vo['identy']);
			}elseif($vo['channeltype'] == 'page'){
				$href = U('Page/'.$vo['method'],'ch='.$rs['identy']);
			}elseif($vo['channeltype'] == 'out'){
				$href = $vo['weburl'] ? $vo['weburl'] : ($vo['wapurl'] ? $vo['wapurl'] : '#');
				$attr = ' target="_blank" ' . ($_cls ? ' class="' . $_cls . '" ' : '');
			}else{
				$href = '#';
				$attr = ' class="no-show-content ' . $_cls . '" ';
			}
			$class = $j == 0 ? $firstClass : ($j < $count - 1 ? 'elem-node-center' : 'elem-node-last');
			if ($this->_hasChildren($vo[$this->_field])){
				$img = '<img class="open-close-node" src="' . $imgPath . 'close_node.gif" alt="' . $vo['id'] . '"/>';
			}else{
				$img = '<img src="' . $imgPath . 'empty_node.gif" />';
			}
			$this->_trees .= '<li class="' . $class . '">' . $img . '<a href="' . $href . '"' . $attr . '>' . $vo['title'] . '</a></li>';
			if ($this->_hasChildren($vo[$this->_field])){
				$this->_trees .= '<li class="node-line">';
				$this->_getNavTree($imgPath,$this->_children[$vo[$this->_field]]);
				$this->_trees .= '</li>';
			}
			$j++;
			unset($class, $href, $attr);
		}
		$this->_trees .= '</ul>';
	}
}

?>