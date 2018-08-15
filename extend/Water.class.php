<?php
/**
 *
 * +------------------------------------------------------------+
 * @category Water
 * +------------------------------------------------------------+
 * 图片添加水印功能
 * +------------------------------------------------------------+
 *
 * @copyright http://ukl.io 2012
 * @version 1.0
 *
 * Modified at : 2012-12-21 上午09:07:58
 *
 */
/*
$water = new Water(array (
  'watermarkstatus' => '9',     // 水印添加的位置(3x3 共 9 个位置可选)。不支持动画 GIF 格式
  'watermarkminwidth' => '0',   // 设置水印添加的条件，小于此宽度的图片附件将不添加水印
  'watermarkminheight' => '0',  // 设置水印添加的条件，小于此高度的图片附件将不添加水印
  'watermarktype' => 'png',     // 水印图片类型，取值：png、gif
  'watermarkfile' => 'watermark.png',   // 水印图片文件
  'watermarktrans' => 50,       // 水印融合度，设置 GIF 类型水印图片与原始图片的融合度，范围为 1～100 的整数，数值越大水印图片透明度越低。PNG 类型水印本身具有真彩透明效果，无须此设置
  'watermarkquality' => 90,     // JPEG 水印质量，设置 JPEG 类型的图片附件添加水印后的质量参数，范围为 0～100 的整数，数值越大结果图片效果越好，但尺寸也越大
));
$water->Watermark('watermarkpreview.jpg', 'watermark_temp3.jpg');       // Watermark(源文件, 目标文件)，如果不指定目标文件，则直接在源文件上打水印
*/
class Water {
        var $source = '';
        var $target = '';
        var $imginfo = array();
        var $imagecreatefromfunc = '';
        var $imagefunc = '';
        var $tmpfile = '';
        var $libmethod = 0;
        var $param = array();
        var $errorcode = 0;
       
    function __construct($param){
            $this->param = $param;
    }
    function Water($param) {
            $this->param = $param;
    }

    function Watermark($source, $target = '') {
            $return = $this->init('watermask', $source, $target);
            if($return <= 0) {
                    return $this->returncode($return);
            }

            if(!$this->param['watermarkstatus'] || ($this->param['watermarkminwidth'] && $this->imginfo['width'] <= $this->param['watermarkminwidth'] && $this->param['watermarkminheight'] && $this->imginfo['height'] <= $this->param['watermarkminheight'])) {
                    return $this->returncode(0);
            }
            if(!is_readable($this->param['watermarkfile'])) {
                    return $this->returncode(-3);
            }

            $return = $this->Watermark_GD();

            return $this->sleep($return);
    }

    function error() {
            return $this->errorcode;
    }

    function init($method, $source, $target, $nosuffix = 0) {

            $this->errorcode = 0;
            if(empty($source)) {
                    return -2;
            }
           
            if(empty($target)) {
                    $target = $source;
            }
           
            $targetpath = dirname($target);
            if(!file_exists($targetpath)) {
                    @mkdir($targetpath, 0700, TRUE);
            }

            clearstatcache();
            if(!is_readable($source) || !is_writable($targetpath)) {
                    return -2;
            }

            $imginfo = @getimagesize($source);
            if($imginfo === FALSE) {
                    return -1;
            }

            $this->source = $source;
            $this->target = $target;
            $this->imginfo['width'] = $imginfo[0];
            $this->imginfo['height'] = $imginfo[1];
            $this->imginfo['mime'] = $imginfo['mime'];
            $this->imginfo['size'] = @filesize($source);

            if(!$this->libmethod) {
                    switch($this->imginfo['mime']) {
                            case 'image/jpeg':
                                    $this->imagecreatefromfunc = function_exists('imagecreatefromjpeg') ? 'imagecreatefromjpeg' : '';
                                    $this->imagefunc = function_exists('imagejpeg') ? 'imagejpeg' : '';
                                    break;
                            case 'image/gif':
                                    $this->imagecreatefromfunc = function_exists('imagecreatefromgif') ? 'imagecreatefromgif' : '';
                                    $this->imagefunc = function_exists('imagegif') ? 'imagegif' : '';
                                    break;
                            case 'image/png':
                                    $this->imagecreatefromfunc = function_exists('imagecreatefrompng') ? 'imagecreatefrompng' : '';
                                    $this->imagefunc = function_exists('imagepng') ? 'imagepng' : '';
                                    break;
                    }
            } else {
                    $this->imagecreatefromfunc = $this->imagefunc = TRUE;
            }

            if(!$this->libmethod && $this->imginfo['mime'] == 'image/gif') {
                    if(!$this->imagecreatefromfunc) {
                            return -4;
                    }
                    if(!($fp = @fopen($source, 'rb'))) {
                            return -2;
                    }
                    $content = fread($fp, $this->imginfo['size']);
                    fclose($fp);
                    $this->imginfo['animated'] = strpos($content, 'NETSCAPE2.0') === FALSE ? 0 : 1;
            }

            return $this->imagecreatefromfunc ? 1 : -4;
    }

    function sleep($return) {
            if($this->tmpfile) {
                    @unlink($this->tmpfile);
            }
            $this->imginfo['size'] = @filesize($this->target);
            return $this->returncode($return);
    }

    function returncode($return) {
            if($return > 0 && file_exists($this->target)) {
                    return true;
            } else {
                    $this->errorcode = $return;
                    return false;
            }
    }

    function loadsource() {
            $imagecreatefromfunc = &$this->imagecreatefromfunc;
            $im = @$imagecreatefromfunc($this->source);
            if(!$im) {
                    if(!function_exists('imagecreatefromstring')) {
                            return -4;
                    }
                    $fp = @fopen($this->source, 'rb');
                    $contents = @fread($fp, filesize($this->source));
                    fclose($fp);
                    $im = @imagecreatefromstring($contents);
                    if($im == FALSE) {
                            return -1;
                    }
            }
            return $im;
    }

    function Watermark_GD() {
            if(!function_exists('imagecreatetruecolor')) {
                    return -4;
            }

            $imagefunc = &$this->imagefunc;

            if(!function_exists('imagecopy') || !function_exists('imagecreatefrompng') || !function_exists('imagecreatefromgif') || !function_exists('imagealphablending') || !function_exists('imagecopymerge')) {
                    return -4;
            }
            $watermarkinfo = @getimagesize($this->param['watermarkfile']);
            if($watermarkinfo === FALSE) {
                    return -3;
            }
            $watermark_logo = $this->param['watermarktype'] == 'png' ? @imageCreateFromPNG($this->param['watermarkfile']) : @imageCreateFromGIF($this->param['watermarkfile']);
            if(!$watermark_logo) {
                    return 0;
            }
            list($logo_w, $logo_h) = $watermarkinfo;

            $wmwidth = $this->imginfo['width'] - $logo_w;
            $wmheight = $this->imginfo['height'] - $logo_h;

            if($wmwidth > 10 && $wmheight > 10 && !$this->imginfo['animated']) {
                    switch($this->param['watermarkstatus']) {
                            case 1:
                                    $x = 5;
                                    $y = 5;
                                    break;
                            case 2:
                                    $x = ($this->imginfo['width'] - $logo_w) / 2;
                                    $y = 5;
                                    break;
                            case 3:
                                    $x = $this->imginfo['width'] - $logo_w - 5;
                                    $y = 5;
                                    break;
                            case 4:
                                    $x = 5;
                                    $y = ($this->imginfo['height'] - $logo_h) / 2;
                                    break;
                            case 5:
                                    $x = ($this->imginfo['width'] - $logo_w) / 2;
                                    $y = ($this->imginfo['height'] - $logo_h) / 2;
                                    break;
                            case 6:
                                    $x = $this->imginfo['width'] - $logo_w;
                                    $y = ($this->imginfo['height'] - $logo_h) / 2;
                                    break;
                            case 7:
                                    $x = 5;
                                    $y = $this->imginfo['height'] - $logo_h - 5;
                                    break;
                            case 8:
                                    $x = ($this->imginfo['width'] - $logo_w) / 2;
                                    $y = $this->imginfo['height'] - $logo_h - 5;
                                    break;
                            case 9:
                                    $x = $this->imginfo['width'] - $logo_w - 5;
                                    $y = $this->imginfo['height'] - $logo_h - 5;
                                    break;
                    }
                    if($this->imginfo['mime'] != 'image/png') {
                            $color_photo = imagecreatetruecolor($this->imginfo['width'], $this->imginfo['height']);
                    }
                    $dst_photo = $this->loadsource();
                    if($dst_photo < 0) {
                            return $dst_photo;
                    }
                    imagealphablending($dst_photo, true);
                    imagesavealpha($dst_photo, true);
                    if($this->imginfo['mime'] != 'image/png') {
                            imageCopy($color_photo, $dst_photo, 0, 0, 0, 0, $this->imginfo['width'], $this->imginfo['height']);
                            $dst_photo = $color_photo;
                    }
                    if($this->param['watermarktype'] == 'png') {
                            imageCopy($dst_photo, $watermark_logo, $x, $y, 0, 0, $logo_w, $logo_h);
                    } else {
                            imageAlphaBlending($watermark_logo, true);
                            imageCopyMerge($dst_photo, $watermark_logo, $x, $y, 0, 0, $logo_w, $logo_h, $this->param['watermarktrans']);
                    }

                    clearstatcache();
                    if($this->imginfo['mime'] == 'image/jpeg') {
                            @$imagefunc($dst_photo, $this->target, $this->param['watermarkquality']);
                    } else {
                            @$imagefunc($dst_photo, $this->target);
                    }
            }
            return 1;
    }
}
 
?>