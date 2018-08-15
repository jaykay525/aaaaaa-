<?php
namespace app\common\model;

class File extends BaseModel{
	public $tips_info       = '';
	public $oss_bucket_name = '';
	public $limit_size      = 0;// 限制上传大小
	public $oss_config      = array();

	protected $img_ext   = array('jpg', 'png', 'bmp', 'gif', 'jpeg');
	protected $app_ext   = array('apk', 'ipa');
	protected $word_ext  = array('doc', 'docx');
	protected $excel_ext = array('xls', 'xlsx');
	protected $zip_ext   = array('zip', 'rar');
	protected $cert_ext  = array('pem', 'cer', 'pfx');
	protected $other_ext = array('pdf');
	protected $all_ext   = array();

	//自定义初始化
    protected function initialize(){
        parent::initialize();
        // 初始化阿里云OSS配置
        $this->oss_config = model('Config')->_get('', 'aliyun_oss');
    }
    public function upload($user_id = 0, $file_ext = '', $path = '', $admin_id = 0){
    	$path = $path ? $path : '/uploads/file/';
		$file = request()->file('file');

		$file_ext = $file_ext ? $file_ext : $this->getAllExt();
		$validate['ext'] = $file_ext;
		if($this->limit_size){
			$validate['size'] = $this->limit_size;
		}
		$fileinfo = $file->validate($validate)->move(ROOT_PATH . 'service' . $path);
		if($fileinfo){
			$info = $fileinfo->getInfo();
			// 原文件名
			$data['filename']    = $info['name'];
			// 文件大小
			$data['size']        = $info['size']; 
			// 后缀
			$data['ext']         = $fileinfo->getExtension();
			// 保存路径
			$data['filepath']    = $path . $fileinfo->getSaveName();
			$data['file_md5']    = md5($data['filepath']);
			// 保存文件名
			$data['savename']    = $fileinfo->getFilename(); 
			$data['client_ip']   = request()->ip();
			$data['create_time'] = time();
			$data['user_id']     = $user_id ? $user_id : 0;
			$data['admin_id']    = $admin_id ? $admin_id : 0;

			$is_pic = $this->getIsPicAttr($data['ext'], $data);
			if($is_pic){
				$img_info = getimagesize('.' . $data['filepath']);
				$data['img_width']  = $img_info[0];
				$data['img_height'] = $img_info[1];
			}

			$this->create($data);

			$data['is_pic']   = $is_pic;
			$data['file_url'] = $this->getFileUrlAttr($data['filepath'], $data);
			$data['icon_url'] = $this->getIconUrlAttr($data['filepath'], $data);
			$data['file_id']  = $this->getLastInsID();

			return $data;
		}else{
			// 上传失败获取错误信息
			$this->tips_info = $file->getError();
			return false;
		}
    }
	public function getAllExt(){
		$this->all_ext = array_merge(
			$this->img_ext, 
			$this->app_ext, 
			$this->word_ext, 
			$this->excel_ext, 
			$this->zip_ext, 
			$this->cert_ext,
			$this->other_ext 
		);
		return $this->all_ext;
	}
	/**
	 * 获取允许后续名
	 * @param  string $type 类型
	 */
	public function getAllowExt($type = '', $is_string = false){
		switch ($type) {
			case 'img':
				$allow_ext = $this->img_ext;
				break;
			case 'app':
				$allow_ext = $this->app_ext;
				break;
			case 'word':
				$allow_ext = $this->word_ext;
				break;
			case 'zip':
				$allow_ext = $this->zip_ext;
				break;
			case 'excel':
				$allow_ext = $this->excel_ext;
				break;
			case 'cert':
				$allow_ext = $this->cert_ext;
				break;
			case 'other':
				$allow_ext = $this->other_ext;
				break;
			
			default:
				$allow_ext = $this->getAllExt();
				break;
		}
		if($is_string){
			$allow_ext = implode(',', $allow_ext);
		}
		return $allow_ext;
	}
	// 获取完整文件路径
	public function getFileUrlAttr($value = '', $data = array()){
		$file_domain = $this->oss_config['file_domain'];
		$img_domain  = $this->oss_config['img_domain'];
		$oss_domain  = $img_domain ? $img_domain : $file_domain;
		$domain = $data['oss_time'] > 0 ? $oss_domain : request()->domain();
		$domain = $domain ? $domain : request()->domain();

		$filepath = $domain . ($value ? $value : $data['filepath']);

		return $filepath;
	}
	// 判断文件类型是否为图片
	public function getIsPicAttr($value = '', $data = array()){
		$ext = strtolower($value ? $value : $data['ext']);
		if(in_array($ext, $this->img_ext)){
			return true;
		}

		return false;
	}
	public function getIconUrlAttr($value = '', $data = array()){
		$file_url = $value ? $value : $this->getFileUrlAttr($data['filepath'], $data);
		$img_url  = $this->getIsPicAttr($data['ext']) ? img($file_url, 400, 400) : $this->get_path('icon-file.png', 400, 400);

		return $img_url;
	}
	public function get_path($file_id = '', $width = '', $height = '', $ext = 'jpg'){
		//默认图片
		$default_file = '/public/images/default.png';
		
		$domain      = config('WEB_URL');       //  old:request()->domain();
		$file_domain = $this->oss_config['file_domain'];
		$img_domain  = $this->oss_config['img_domain'];
		$oss_domain  = $img_domain ? $img_domain : $file_domain;

		//获取图片相对地址
		if(is_numeric($file_id)){
			$info = $this->where(array('file_id' => $file_id))->find();
			$images_file = $info['filepath'];
			//返回OSS图片完整地址
			$images_url = ($info['oss_time'] ? $oss_domain : $domain) . ($images_file ? $images_file : $default_file);
		}else{
			$images_file = $file_id ? '/public/images/' . $file_id : '';
			//返回本地图片完整地址
			$images_url = $domain . ($images_file ? $images_file : $default_file);
		}
		if($width && $height){
			$ext = $this->getIsPicAttr($ext) ? $ext : 'jpg';
			// @120w_300h_1e_1c.jpg
			// $images_url = $images_url . '!w' . $width . '_h' . $height . '.jpg';
			// 阿里云OSS格式
			$images_url = $images_url . '@' . $width . 'w_' . $height . 'h_1e_1c.' . $ext;
		}
		return $images_url;
	}

	/**
	 * 替换HTML内容文件路径为OSS文件
	 * @param  [type] $content HTML代码
	 * @return [type]          [description]
	 */
	public function html_file_url_oss($content, $is_view_img = false, $width = 0, $height = 0){
		if(!$content){
			return '';
		}
		$file_domain = $this->oss_config['file_domain'];
		$img_domain  = $this->oss_config['img_domain'];
		$oss_domain  = $img_domain ? $img_domain : $file_domain;

		$file_md5_arr = [];
		$preg = '/<img.*?src=[\"|\']?(.*?)[\"|\']?\s.*?>/i';
		preg_match_all($preg, $content, $img_arr);
		foreach ($img_arr[1] as $filepath) {
			$file_md5 = md5(trim($filepath));
			$file_md5_arr[] = $file_md5;
		}
		$file_md5_str = $file_md5_arr ? implode(',', $file_md5_arr) : '';
		if(!$file_md5_str){
			return $content;
		}
		$img_url_search      = [];
		$img_url_replace     = [];
		$img_replace_md5_arr = [];
		$where['file_md5'] = array('in', $file_md5_str);
		$list = $this->field('filepath,oss_time,file_md5,img_width,img_height')->where($where)->select();
		foreach ($list as $rs) {
			if($rs['oss_time']){
				$file_url = $oss_domain . $rs['filepath'];
				$img_url_search[]  = $rs['filepath'];
				$img_url_replace[] = $file_url;
				$full_file_url = $file_url;
			}else{
				$file_url      = $rs['filepath'];
				$full_file_url = request()->domain() . $file_url;
			}
			$row['file_url']      = $file_url;
			$row['full_file_url'] = $full_file_url;
			$row['img_width']     = $rs['img_width'];
			$row['img_height']    = $rs['img_height'];
			$row['file_md5']      = $rs['file_md5'];
			$row['filepath']      = $rs['filepath'];
			$img_replace_md5_arr[$rs['file_md5']] = $row;
		}
		// 替换成OSS路径
		if($img_url_search && $img_url_replace){
			$content = str_replace($img_url_search, $img_url_replace, $content);
		}
		if($is_view_img){
			foreach ($img_arr[0] as $key => $img_html) {
				$original_img_url     = $img_arr[1][$key];
				$replace_img_info     = $img_replace_md5_arr[md5($original_img_url)];
				$replace_img_url      = $replace_img_info['file_url'];
				$full_replace_img_url = $replace_img_info['full_file_url'];
				if($replace_img_url){
					$img_ratio  = 0;
					$img_width  = 0;
					$img_height = 0;
					if($replace_img_info['img_width'] > 600){
						$img_width = 600;
						$img_ratio = $img_width / $replace_img_info['img_width'];
					}
					if($img_ratio > 0){
						$img_height = $replace_img_info['img_height'] * $img_ratio;
					}
					if(!$img_width && !$img_height && $replace_img_info['img_width'] && $replace_img_info['img_width']){
						$img_height = 600;
						$img_ratio  = $img_height / $replace_img_info['img_height'];
						if($img_ratio > 0){
							$img_width = $replace_img_info['img_width'] * $img_ratio;
						}
					}

					$width  = $width ? $width : $img_width;
					$width  = $width ? $width : 400;
					$height = $height ? $height : $img_height;
					$height = $height ? $height : 200;
					preg_match('/.*?style=[\"|\']?(.*?)[\"|\'].*?/i', $img_html, $style_matches);
					preg_match('/.*?class=[\"|\']?(.*?)[\"|\'].*?/i', $img_html, $class_matches);
					$class    = isset($class_matches[1]) ? $class_matches[1] : '';
					$style    = isset($style_matches[1]) ? $style_matches[1] : '';
					$img_html = str_replace($original_img_url, $replace_img_url, $img_html);
					if(BIND_MODULE == 'wap'){
						$content  = str_replace($img_html, '<img original="' . $full_replace_img_url . '" src="' . img($full_replace_img_url, $width, $height) . '" class="view-image ' . $class . '" style="' . $style . '" />', $content);
					}else{
						$content  = str_replace($img_html, '<a href="' . $full_replace_img_url . '" class="view_big_pic" title="查看原图"><img src="' . img($full_replace_img_url, $width, $height) . '" class="view-image ' . $class . '" style="' . $style . '" /></a>', $content);
					}
				}
			}
		}
		return $content;
	}
	/**
	 * HTML的OSS路径替换成本地的
	 * @param  [type] $content HTML代码
	 * @return [type]          [description]
	 */
	public function html_osspath_local($content){
		if(!$content){
			return '';
		}
		$file_domain = $this->oss_config['file_domain'];
		$img_domain  = $this->oss_config['img_domain'];
		$oss_domain  = $img_domain ? $img_domain : $file_domain;
		$replace[]   = $oss_domain;
		$replace[]   = request()->domain();
		$content = str_replace($replace, '', $content);

		return $content;
	}
	/**
	 * 删除文件
	 * @param  string $file_ids 文件，多个以逗号分隔
	 */
	public function delete_file($file_ids = ''){
		if(!$file_ids){
			return false;
		}
		$error_id_arr     = array();
		$oss_filepath_arr = array();
		$where['file_id'] = array('in', $file_ids);
		$list = $this->where($where)->select();
		// 处理删除本地文件
		foreach ($list as $rs) {
			$file_id  = $rs['file_id'];
			$filepath = '.' . $rs['filepath'];
			if(!unlink($filepath)){
				$error_id_arr[] = $file_id;
			}
			if($rs['oss_time']){
				$oss_filepath_arr[] = substr($filepath, 2);// 删除远程文件
			}
			$success_id_arr[]   = $file_id;
		}
		// 删除OSS文件
		if($oss_filepath_arr){
			$this->delete_oss_file($oss_filepath_arr);
		}
		// 删除数据库记录
		unset($where);
		$file_ids = implode(',', $success_id_arr);
		if($file_ids){
			$where['file_id'] = array('in', $file_ids);
			$this->where($where)->delete();
			return true;
		}
		return true;
	}
	/**
	 * 图片裁切
	 * @param  [type]  $file_id 文件ID
	 * @param  integer $x1      [description]
	 * @param  integer $y1      [description]
	 * @param  integer $x2      [description]
	 * @param  integer $y2      [description]
	 * @return [type]           [description]
	 */
	public function crop($user_id = '', $file_id, $x1 = 0, $y1 = 0, $x2 = 100, $y2 = 100, $width = 100, $height = 100){
		if(!$file_id){
			return false;
		}
		//获取原文件完整路径
		$info = $this->find($file_id);
		$source_path = $info['filepath'];
		if(!$source_path){
			return false;
		}
		// 如果文件已被删除，则从OSS上下载下来
		if($info['is_delete_local'] && $info['oss_time']){
			$this->download_oss_file($file_id);
		}
		$source_info = pathinfo($source_path);
		$source_path = str_replace('/uploads', 'uploads', $source_path);
		// 获取原图分辨率大小
		list($owidth, $oheight) = getimagesize($source_path);
		// 计算X比率
		$x_ratio = $owidth / ($width);
		// 计算Y比率
		$y_ratio = $oheight / ($height);

		$new_x1 = abs($x_ratio * $x1);
		$new_x2 = abs($x_ratio * $x2);
		$new_y1 = abs($y_ratio * $y1);
		$new_y2 = abs($y_ratio * $y2);

		// 保存文件名称
		$filename = md5($source_path . $new_x1 . $new_y1 . $new_x2 . $new_y2) . '.' . $source_info['extension'];
		//保存文件路径
		$filepath = 'uploads/crop/' . date('Ymd') . '/';
		if(!file_exists($filepath)){
			mkdir($filepath, 0700, true);
		}
		// 保存文件完整路径及名称
		$target_path = $filepath . $filename;
		// 导入图片裁切包
		import('Zebra_Image', EXTEND_PATH, '.class.php');
		$image = new \Zebra_Image();
		$image->source_path  = $source_path;//源文件
		$image->target_path  = $target_path;//生成文件
		$image->jpeg_quality = 100;//生成图片质量
		// 图片裁切
		if($image->crop($new_x1, $new_y1, $new_x2, $new_y2)){
			$fileinfo['filename']    = $source_info['basename'];//原文件名
			$fileinfo['savename']    = $filename;//保存文件名
			$fileinfo['filepath']    = '/' . $target_path;
			$fileinfo['ext']         = $source_info['extension'];
			$fileinfo['size']        = filesize($target_path);
			$fileinfo['create_time'] = time();
			$fileinfo['user_id']     = $user_id;
			$fileinfo['img_width']   = $width;
			$fileinfo['img_height']  = $height;
			$fileinfo['client_ip']   = request()->ip();
			$this->create($fileinfo);
			//文件ID
			$file_id = $this->getLastInsID();
			return $file_id;
		}
		return false;
	}
	// 获取OssClient 
	public function get_oss_client(){
		$key_id      = $this->oss_config['key_id'];
		$key_secret  = $this->oss_config['key_secret'];
		$bucket_name = $this->oss_config['bucket_name'];
		$endpoint    = $this->oss_config['endpoint'];
		$is_open     = $this->oss_config['is_open'];
		if(!$is_open){
			$this->tips_info = '未开启OSS服务！';
			return false;
		}
		if(!$key_id || !$key_secret || !$bucket_name || !$endpoint){
			$this->tips_info = 'OSS参数未配置！';
			return false;
		}
		try {
			$ossClient = new \OSS\OssClient($key_id, $key_secret, $endpoint, false);
		} catch (OssException $e) {
			$this->tips_info = $e->getMessage();
			return false;
		}
		$this->oss_bucket_name = $bucket_name;
		return $ossClient;
	}
	// 删除OSS文件
	public function delete_oss_file($file_arr){
		try {
			$oss_client  = $this->get_oss_client();
			if(!$oss_client){
				return false;
			}
			$bucket_name = $this->oss_bucket_name;
			$oss_client->deleteObjects($bucket_name, $file_arr);
			return true;
			return false;
		} catch (OssException $e) {
			$this->tips_info = $e->getMessage();
			return false;
		}
	}
	/**
	 * 上传文件至OSS
	 * @param  string $file_id 文件ID
	 */
	public function upload_oss_file($file_id = ''){
		if(!$file_id){
			$this->tips_info = '文件ID为空！';
			return false;
		}
		$info = $this->where(array('file_id' => $file_id))->find();
		if(!$info){
			$this->tips_info = '记录不存在！';
			return false;
		}
		// 上传本地文件
		try {
			$oss_client  = $this->get_oss_client();
			$bucket_name = $this->oss_bucket_name;
			$filepath    = '.' . $info['filepath'];// 本地文件
			$save_file   = substr($info['filepath'], 1);// 远程保存文件
			if(!$oss_client){
				return false;
			}
			$oss_client->uploadFile($bucket_name, $save_file, $filepath);
			// 上传成功
			// 删除本地文件
			$is_delete_local = 0;
			if($this->oss_config['is_delete_local']){
				unlink($filepath);
				$is_delete_local = 1;
			}
			// 保存OSS上传时间
			$oss_time = time();
			$this->where(array('file_id' => $file_id))->update(array(
				'oss_time'        => $oss_time,
				'is_delete_local' => $is_delete_local,
			));
			return $oss_time;
		} catch (OssException $e) {
			$this->tips_info = $e->getMessage();
			return false;
		}
		
		return false;
	}
	/**
	 * 下载OSS文件到本地
	 * @param  string $file_id 文件ID
	 */
	public function download_oss_file($file_id = ''){
		if(!$file_id){
			$this->tips_info = '文件ID为空！';
			return false;
		}
		$info = $this->where(array('file_id' => $file_id))->find();
		if(!$info){
			$this->tips_info = '记录不存在！';
			return false;
		}
		// 下载OSS文件
		try {
			$savename    = $info['savename'];// 保存文件名
			$filepath    = '.' . $info['filepath'];// 本地文件
			$oss_file    = substr($info['filepath'], 1);// 远程文件
			if(file_exists($filepath)){
				return true;
			}
			$oss_client = $this->get_oss_client();
			if(!$oss_client){
				return false;
			}
			$bucket_name = $this->oss_bucket_name;
			$file_data   = $oss_client->getObject($bucket_name, $oss_file);
			if($file_data){
				// 创建保存目录
				$down_dir = str_replace($savename, '', $filepath);
				if(file_exists($down_dir) == false){
					mkdir($down_dir, 0777, true);
				}
				// 保存成文件
				file_put_contents($filepath, $file_data);
				$this->where(array('file_id' => $file_id))->update(array(
					'is_delete_local' => 0,
				));
				return true;
			}
		} catch (OssException $e) {
			$this->tips_info = $e->getMessage();
			return false;
		}
		
		return false;
	}
}