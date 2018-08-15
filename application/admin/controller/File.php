<?php
namespace app\admin\controller;

class File extends Base{
	// 文件上传
	public function upload(){
		$model = model('File');
		$data  = $model->upload('', '', '', $this->_admin_id);
		if($data){
			json('上传成功！', $data);
		}else{
			json('上传失败->' . $model->tips_info, '', 400);
		}
	}
	// 本地上传
	public function win_upload(){
		$this->assign('field_name', $this->_get('field_name'));
		$this->assign('is_mulit', $this->_get('is_mulit', '', 0));
		$this->assign('upload_type', $this->_get('upload_type'));
		$this->assign('upload_ext', $this->_get('upload_ext'));
		return $this->view();
	}
	// 选择在线文件
	public function win_online(){
		$where['admin_id'] = $this->_admin_id;
		parent::index('', $where);
		$this->assign('field_name', $this->_get('field_name'));
		$this->assign('is_mulit', $this->_get('is_mulit', '', 0));
		$this->assign('upload_type', $this->_get('upload_type'));
		$this->assign('upload_ext', $this->_get('upload_ext'));
		return $this->view();
	}
	// 删除文件
	public function delete(){
		$file_id = $this->_get('file_id');
		$ids     = $this->_post('ids/a');
		if(!is_array($ids) && $file_id){
			$file_ids = $file_id;
		}else if(is_array($ids)){
			$file_ids = implode(',', $ids);
		}else{
			$this->error('请选择要删除内容!');
		}
		if(model('File')->delete_file($file_ids)){
			$this->success('删除成功!');
		}else{
			$this->error('删除失败!');
		}
	}
	// 上传文件至OSS
	public function upload_oss(){
		$file_id = $this->_get('file_id', '对不起，您的操作有误！');
		$model = model('File');
		if($oss_time = $model->upload_oss_file($file_id)){
			$this->success('上传成功！', '', array('label' => date('Y-m-d H:i:s', $oss_time)));
		}
		else{
			$this->error('上传失败->' . $model->tips_info);
		}
	}
	// 下载OSS文件
	public function down_oss_file(){
		$file_id = $this->_get('file_id', '对不起，您的操作有误！');
		$model = model('File');
		if($oss_time = $model->download_oss_file($file_id)){
			$this->success('下载成功！');
		}
		else{
			$this->error('下载失败->' . $model->tips_info);
		}
	}

	public function ueditor_uploads(){
		$ueditor_php_path = 'public/js/ueditor/php/';
		date_default_timezone_set("Asia/chongqing");
		error_reporting(E_ERROR);
		header("Content-Type: text/json; charset=utf-8");
		
		$upload_path = 'manage';
		$config_json = file_get_contents($ueditor_php_path . "config.json");
		$config_json = str_replace('/uploads/ueditor/', '/uploads/ueditor/' . $upload_path . '/', $config_json);
		$CONFIG = json_decode(preg_replace("/\/\*[\s\S]+?\*\//", "", $config_json), true);
		$action = $_GET['action'];

		switch ($action) {
		    case 'config':
				$oss_config  = model('Config')->_get('', 'aliyun_oss');
				$file_domain = $oss_config['file_domain'];
				$img_domain  = $oss_config['img_domain'];
				$oss_domain  = $img_domain ? $img_domain : $file_domain;

				if($oss_domain){
		    		$CONFIG['catcherLocalDomain'][] = str_replace(array('http://', 'https://'), '', $oss_domain);
				}
				if(DOMAIN_PATH){
		    		$CONFIG['catcherLocalDomain'][] = str_replace(array('http://', 'https://'), '', DOMAIN_PATH);
				}
				$result = json_encode($CONFIG);
		        break;

		    /* 上传图片 */
		    case 'uploadimage':
		    /* 上传涂鸦 */
		    case 'uploadscrawl':
		    /* 上传视频 */
		    case 'uploadvideo':
		    /* 上传文件 */
		    case 'uploadfile':
		        $result = include($ueditor_php_path . "action_upload.php");
		        break;

		    /* 列出图片 */
		    case 'listimage':
		        // $result = include($ueditor_php_path . "action_list.php");
		    	$result = $this->ueditor_file_list();
		        break;
		    /* 列出文件 */
		    case 'listfile':
		        $result = include($ueditor_php_path . "action_list.php");
		        $result = $this->ueditor_file_list();
		        break;

		    /* 抓取远程文件 */
		    case 'catchimage':
		        $result = include($ueditor_php_path . "action_crawler.php");
		        break;

		    default:
		        $result = json_encode(array(
		            'state' => '请求地址出错'
		        ));
		        break;
		}
		$json_data = json_decode($result, true);
		// 保存文件到数据库
		if($json_data['state'] == 'SUCCESS' && in_array($action, array('uploadimage', 'catchimage', 'uploadfile', 'uploadvideo', 'uploadscrawl'))){
			$data['filename']         = $json_data['original'];//原文件名
			$data['savename']         = $json_data['title'];//保存文件名
			$data['filepath']         = $json_data['url'];
			$data['file_md5']         = md5($data['filepath']);
			$data['ext']              = str_replace('.', '', $json_data['type']);
			$data['size']             = $json_data['size'];
			$data['create_time']      = time();
			$data['admin_id']         = $this->_admin_id;
			$data['is_editor_upload'] = 1;
			$data['client_ip']        = request()->ip();
			$data['server_ip']        = $_SERVER['SERVER_ADDR'];
			model('File')->create($data);
		}

		/* 输出结果 */
		if (isset($_GET["callback"])) {
		    if (preg_match("/^[\w_]+$/", $_GET["callback"])) {
		        echo htmlspecialchars($_GET["callback"]) . '(' . $result . ')';
		    } else {
		        echo json_encode(array(
		            'state'=> 'callback参数不合法'
		        ));
		    }
		} else {
		    echo $result;
		}
	}
	// 编辑器上传文件清单
	public function ueditor_file_list(){
		//分页参数
		$start    = intval($_GET['start']);
		$pagesize = $this->_get('pagesize', '', 20);

		$model = model('File');

		$where['admin_id'] = $this->_admin_id;
		$where['ext']      = array('in', implode(',', $model->getAllowExt('img')));
		$count = $model->where($where)->count();
		$list  = $model->where($where)->order('create_time desc')->limit($start . ',' . $pagesize)->select();
		foreach ($list as $rs) {
			$row['url']   = $rs['oss_time'] ? $rs['file_url'] : $rs['filepath'];
			$row['mtime'] = $rs['create_time'];
			$file_list[]  = $row;
		}
		$data['list']     = $file_list;
		$data['start']    = $start;
		$data['state']    = 'SUCCESS';
		$data['pagesize'] = $pagesize;
		$data['total']    = $count;

		return json_encode($data);
	}
}