<?php
/**************************************************
#Nginx 图片裁切配置部分
  location ~ .*\.(gif|jpg|jpeg|png|bmp)$ {
	  expires      1d;

	  if (!-f $request_filename) {
		  rewrite ^/(.*)$ /Uploads/tmp/$1;
	  }
	  if (!-f $request_filename) {
		  rewrite ^/.*$ /SunccoAutoimg.php last;
	  }
	  #return 200;
  }
  location ~ .*\.(gif|jpg|jpeg|png|bmp|swf)$ {
	expires      1d;
  }
  location ~ .*\.(js|css)?$ {
	expires      12h;
  }
**************************************************/
error_reporting(0);
/**
 * URL图片缩略图(配合Nginx)
 */

$file = $_SERVER ['REQUEST_URI'];//请求字串 原文件!w80_h80_r5.jpg、@100w_200h_1e_1c.jpg
// $file = str_replace('/Uploads', '', $file);//替换重复目录
// $file_path = $_SERVER ['DOCUMENT_ROOT'] . $file; //目标目标路径 /var/www/http/file/abc.jpg.w320.jpg
//上传目录
$upload_path = '/uploads';
$script_name = $_SERVER['SCRIPT_FILENAME'];
$script_dir  = dirname($script_name);
$file_path   = $script_dir . $file;//过滤Nginx rewrite目录

$dirname     = dirname ( $file_path ) . "/";
$filename    = basename ( $file_path );
$target_path = $script_dir . '/uploads/tmp' . $file;
$tmp_path    = pathinfo($target_path);

$is_local_format = true;
if(strpos($filename, '!') !== false){
	$tm = explode('!', $filename);
}
// 阿里云OSS格式处理
if(strpos($filename, '@') !== false){
	$tm = explode('@', $filename);
	$is_local_format = false;
}

$pathinfo = pathinfo($tm[1]);

$wh = $pathinfo['filename'];
$m = explode('_', str_replace(array('w', 'h', 'r'), '', $wh));
if(count($m) == 1){
	$m = explode('x', str_replace(array('w', 'h', 'r'), '', $wh));
}

$ext = strtolower($pathinfo['extension']);
if(!in_array($ext, array('jpg', 'png', 'jpeg', 'gif', 'bmp'))){
	send404('output file extension error!');
}

$source_path = $dirname . $tm[0];
$width       = $m[0];
$height      = $m[1];

// 获取原始图片宽高信息并自动设置高度
$source_imginfo = getimagesize($source_path);
if($height == 0){
    $height = floor($source_imginfo[1]*$width / $source_imginfo[0]);
}

// 圆角处理
$round = 0;
if($is_local_format){
	$round = isset($m[2]) ? (($m[2] > 0 && $m[2] <= 100) ? round($m[2]) : 0) : 0;
}

// var_dump($filename);
// var_dump($width);
// var_dump($height);
// var_dump($target_path);exit;

// var_dump($round);exit;
$minlimit = 20;
$maxlimit = 3000;
if($width > $maxlimit || $height > $maxlimit || $width < $minlimit || $height < $minlimit){
	// send404('width or height error!');
}
//超过最大限制，使用最大宽度
if($width > $maxlimit){
	$width = $maxlimit;
}
//超过最大限制，使用最大高度
if($height > $maxlimit){
	$height = $maxlimit;
}
//低于最小限制，使用最小宽度
if($width < $minlimit){
	$width = $minlimit;
}
//低于最小限制，使用最小高度
if($height < $minlimit){
	$height = $minlimit;
}

// var_dump($_SERVER);
// var_dump($filename);
// var_dump($dirname);
// var_dump($width);
// var_dump($height);
// var_dump($source_path);
// var_dump($target_path);
// exit();

if(!file_exists($source_path)){
	send404('File not found!');
}

if(!file_exists($tmp_path['dirname'])){
	mkdir($tmp_path['dirname'], 0755, true);
}

require '../extend/Zebra_Image.class.php';

$image = new Zebra_Image();
$image->source_path  = $source_path;    //源文件
$image->target_path  = $target_path;    //生成文件
$image->jpeg_quality = 90;     //生成图片质量 级别越高文件越小
$image->png_compression = 9;   //用来检测 PNG 的压缩级别 0-9 级别越高文件越小
$image->sharpen_images = false;  //用于图像的锐化

$image->preserve_aspect_ratio  = true;  //保留原图宽度比率
$image->enlarge_smaller_images = true;  //放大缩小图片
$image->preserve_time          = false; //生成文件时间，与原文件一致

$image->round = $round;	                //圆度 可选值 0-100


if($image->resize($width, $height, ZEBRA_IMAGE_CROP_CENTER)){
	header ('content-type:image/png');
	echo @file_get_contents ($image->target_path);
	exit;
}

function send404($string){
	header('HTTP/1.1 404 Not Found');
	header('Status:404 Not Found');
	exit($string);	
}