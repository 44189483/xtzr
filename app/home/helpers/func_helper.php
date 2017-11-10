<?php
/**
 * @package CodeIgniter
 * @author  EllisLab Dev Team
 * @copyright   Copyright (c) 2014 - 2017, British Columbia Institute of Technology (http://bcit.ca/)
 * @license http://opensource.org/licenses/MIT  MIT License
 * @link    https://codeigniter.com
 * @since   Version 1.0.0
 * @filesource
 */
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * CodeIgniter Array Helpers
 *
 * @package     CodeIgniter
 * @subpackage  Helpers
 * @category    Helpers
 * @author      
 * @link
 */


/** 
 * @param int $width 
 * @param int $height 
 * @param int $pixed_num 
 * @param int $line_num 
 * @param int $code_type 
 * @param int $code_length
 * 前端用法:
 * <img class="verifyimg" src="文件名" name="文件名" alt="" title="看不清则点击图片" onclick="this.src = this.name+'?'+'img='+Math.random();"> 
 */  
function CreateVerifyImage($width = 100, $height = 40, $pixed_num = 80, $line_num = 5, $code_type = null, $code_length = 4) {  

    if ($code_type == 1) {  
        $chars = join("", range(0, 9));  
    } elseif ($code_type == 2) {  
        $chars = join("", array_merge(range('a', 'z'), range('A', 'Z')));  
    } else {  
        $chars = join("", array_merge(range(0, 9), range('a', 'z'), range('A', 'Z')));  
    }  
    if (strlen($chars) < $code_length) {  
        exit("Error in VerifyImage(class): 字符串长度不够，CreateRandomVerifyCode Failed");  
    }  
    $chars = str_shuffle($chars);  
    $m_verify_code = substr($chars, 0, $code_length);

    $_SESSION['authImg'] = strtolower($m_verify_code);

    $m_image = imagecreatetruecolor($width, $height);  
    $white = imagecolorallocate($m_image, 255, 255, 255);  
    imagefill($m_image, 0, 0, $white);  
    // 注意：将字体导入到fonts目录下  
    $font_files = array('public/admin/fonts/Roboto-BlackItalic-webfont.ttf');  
    for($i = 0; $i < $code_length; $i++) {  
        $size = mt_rand(15, 20);  
        $angle = mt_rand(-15, 15);  
        // 将字符串显示到一个居中的位置  
        $x = (imagefontwidth($size) + 5) * ($i + 1);  
        $y = ($height - imagefontheight($size)) / 2 + $size;  
        $color = imagecolorallocate($m_image, mt_rand(0, 255), mt_rand(0, 255), mt_rand(0, 255));  
        $font = $font_files[mt_rand(0, count($font_files)-1)];  
        $text = substr($m_verify_code, $i, 1);  
        imagettftext($m_image, $size, $angle, $x, $y, $color, $font, $text);  
    }  
    for($i = 0; $i < $pixed_num; $i++) {  
        $color = imagecolorallocate($m_image, mt_rand(0, 255), mt_rand(0, 255), mt_rand(0, 255));  
        imagesetpixel($m_image, mt_rand(0, $width), mt_rand(0,$height), $color);  
    }  
    for($i = 0; $i < $line_num; $i++) {  
        $color = imagecolorallocate($m_image, mt_rand(0, 255), mt_rand(0, 255), mt_rand(0, 255));  
        imageline($m_image, mt_rand(0, $width), mt_rand(0, $height), mt_rand(0, $width), mt_rand(0, $height), $color);  
    }  
    header('content-type: image/png');  
    imagepng($m_image);  
    imagedestroy($m_image);  
}

if(!function_exists('utfSubstr')){

  /**
   * 截取字数方法 utfSubstr
   * @return string
  */
  function utfSubstr($str, $position, $length,$type=1){

    $startPos = strlen($str);
    $startByte = 0;
    $endPos = strlen($str);
    $count = 0;
    for($i=0; $i<strlen($str); $i++){
      if($count>=$position && $startPos>$i){
        $startPos = $i;
        $startByte = $count;
      }
      if(($count-$startByte) >= $length) {
        $endPos = $i;
        break;
      }
      $value = ord($str[$i]);
      if($value > 127){
        $count++;
      if($value>=192 && $value<=223) $i++;
      elseif($value>=224 && $value<=239) $i = $i + 2;
      elseif($value>=240 && $value<=247) $i = $i + 3;
      else return self::raiseError("\"$str\" Not a UTF-8 compatible string", 0, __CLASS__, __METHOD__, __FILE__, __LINE__);
      }
      $count++;

    }
    if($type==1 && $endPos>$length){
      return substr($str, $startPos, $endPos-$startPos)."...";
    }else{
        return substr($str, $startPos, $endPos-$startPos);
    }

  }

}

/*
* 获取随机数
* $length - 字符长度
* $type   - 类型 数字int/字符串型str
*/
function randcode($length=4,$type='int') {  

  if(empty($type) || $type == 'int'){
    $chars = "0123456789";
  }

  if($type == 'str'){
    $chars = "abcdefghijklmnopqrstuvwxyz0123456789";
  }
  
  $str ="";  
  for ( $i = 0; $i < $length; $i++ )  {  
    $str.= substr($chars, mt_rand(0, strlen($chars)-1), 1);  
  }  
  return $str;  
}

//判断是电脑还是手机访问
function CheckSubstrs($substrs,$text){    
    foreach($substrs as $substr)    
        if(false!==strpos($text,$substr)){    
            return true;    
        }    
        return false;    
}  

function isMobile(){ 

  $useragent = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '';    
  $useragent_commentsblock = preg_match('|\(.*?\)|',$useragent,$matches) > 0 ? $matches[0] : ''; 

  $mobile_os_list = array('Google Wireless Transcoder','Windows CE','WindowsCE','Symbian','Android','armv6l','armv5','Mobile','CentOS','mowser','AvantGo','Opera Mobi','J2ME/MIDP','Smartphone','Go.Web','Palm','iPAQ'); 

  $mobile_token_list=array('Profile/MIDP','Configuration/CLDC-','160×160','176×220','240×240','240×320','320×240','UP.Browser','UP.Link','SymbianOS','PalmOS','PocketPC','SonyEricsson','Nokia','BlackBerry','Vodafone','BenQ','Novarra-Vision','Iris','NetFront','HTC_','Xda_','SAMSUNG-SGH','Wapaka','DoCoMo','iPhone','iPod');    
            
  $found_mobile = CheckSubstrs($mobile_os_list,$useragent_commentsblock) || CheckSubstrs($mobile_token_list,$useragent);    
            
    if ($found_mobile){    
        return true;    
    }else{    
        return false;    
    }    
} 

/*
* 正则替换字符中的标签
* $str - 字符
* $tag - 标签
*/
function repStr($str){
  return preg_replace("/\d+/",'<strong>$0</strong>',$str);
}