<?php
namespace app\common\command;

use think\console\Command;
use think\console\Input;
use think\console\Output;

class FileOss extends Command{

	protected function configure(){
        $this->setName('file_oss')->setDescription('同步文件至阿里云OSS');
    }

    protected function execute(Input $input, Output $output){
        $output->writeln("执行同步:");
        $model = db('File');
        $oss_config = conf('', 'aliyun_oss');
        $this->oss_config = $oss_config;
        $output->writeln(print_r($oss_config, true));

		$where['oss_time']        = 0;
		$where['is_delete_local'] = 0;
		$where['oss_error_num']   = array('lt', 3);// 上传错误次数小于3次
		$where['create_time']     = array('elt', strtotime('-1 minute'));// 上传2分钟前的文件
			
		if(!$oss_config['is_open']){
			$output->writeln("未开启此服务，暂停处理！");
			return false;
		}
		// 是否删除本地文件
		$is_delete_local = 0;
		if($oss_config['is_delete_local']){
			$is_delete_local = 1;
		}
		$list = $model->where($where)->order('file_id desc')->limit(60)->select();
		$output->writeln($model->getLastSql() . "");
		if(empty($list)){
			$output->writeln("没有数据要处理的！");
			return false;
		}
		$oss_client  = $this->get_oss_client($input, $output);
		$bucket_name = $this->oss_bucket_name;
		if(!$oss_client){
			return false;
		}
		$file_id_arr = array();
		foreach ($list as $rs) {
			$oss_time = '';
			// 上传本地文件
			try {
				$file_id   = $rs['file_id'];
				$filepath  = './service' . $rs['filepath'];// 本地文件
				$save_file = substr($rs['filepath'], 1);// 远程保存文件
				$output->writeln('filepath :' . $filepath);
				$output->writeln('save_file:' . $save_file);
				if(!file_exists($filepath)){
					$output->writeln("文件[" . $file_id . "]不存在，跳过！");
					continue;
				}
				// 上传成功
				$oss_client->uploadFile($bucket_name, $save_file, $filepath);
				// 保存OSS上传时间
				$oss_time = time();
				$model->where(array('file_id' => $file_id))->update(array(
					'oss_time'        => $oss_time,
					'is_delete_local' => $is_delete_local,
				));
				// 删除本地文件
				if($is_delete_local){
					unlink($filepath);
				}
			} catch (OssException $e) {
				$model->where(array('file_id' => $file_id))->setInc('oss_error_num');
				$output->writeln("上传失败->" . $e->getMessage());
			}
			$output->writeln("上传文件->" . $rs['filepath'] . ($oss_time ? '成功' : '失败') . "！");
		}
		exit;
    }
    // 获取OssClient 
	public function get_oss_client($input, $output){
		$key_id      = $this->oss_config['key_id'];
		$key_secret  = $this->oss_config['key_secret'];
		$bucket_name = $this->oss_config['bucket_name'];
		$endpoint    = $this->oss_config['endpoint'];
		$is_open     = $this->oss_config['is_open'];
		if(!$is_open){
			$output->writeln('未开启OSS服务！');
			return false;
		}
		if(!$key_id || !$key_secret || !$bucket_name || !$endpoint){
			$output->writeln('OSS参数未配置！');
			return false;
		}
		try {
			$ossClient = new \OSS\OssClient($key_id, $key_secret, $endpoint, false);
		} catch (OssException $e) {
			$output->writeln($e->getMessage());
			return false;
		}
		$this->oss_bucket_name = $bucket_name;
		return $ossClient;
	}
}