<?php
/**
 * @package	CodeIgniter
 * @author	EllisLab Dev Team
 * @copyright	Copyright (c) 2014 - 2017, British Columbia Institute of Technology (http://bcit.ca/)
 * @license	http://opensource.org/licenses/MIT	MIT License
 * @link	https://codeigniter.com
 * @since	Version 1.0.0
 * @filesource
 */
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * CodeIgniter Array Helpers
 *
 * @package		CodeIgniter
 * @subpackage	Helpers
 * @category	Helpers
 * @author		EllisLab Dev Team
 * @link		https://codeigniter.com/user_guide/helpers/array_helper.html
 */

// --------------------------------------------------------------------
if ( ! function_exists('jump')){
	/**
	 * JS跳转方法:jump
	 * @param	$msg - 提示文字
	 * @param	$url - 跳转URL
	 * @return	JS
	*/
	function jump($msg = '',$url = ''){
        $str = '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" /><script>';
        if(!empty($msg)){
        	$str .= 'alert("'.$msg.'");';
        }
        if(!empty($url)){
        	$str .= 'window.top.location="'.$url.'";';
        }
        $str .= '</script><noscript>如果您禁止了Javascript,请启用，否则请手工回到主页。感谢！</noscript>';
        echo $str;
    }

}

if(!function_exists('get_ip')){

    /**
     * 获取IP方法 get_ip
     * @return  ip
    */
    function get_ip(){
       if (getenv("HTTP_CLIENT_IP") && strcasecmp(getenv("HTTP_CLIENT_IP"), "unknown"))
           $ip = getenv("HTTP_CLIENT_IP");
       else if (getenv("HTTP_X_FORWARDED_FOR") && strcasecmp(getenv("HTTP_X_FORWARDED_FOR"), "unknown"))
           $ip = getenv("HTTP_X_FORWARDED_FOR");
       else if (getenv("REMOTE_ADDR") && strcasecmp(getenv("REMOTE_ADDR"), "unknown"))
           $ip = getenv("REMOTE_ADDR");
       else if (isset($_SERVER['REMOTE_ADDR']) && $_SERVER['REMOTE_ADDR'] && strcasecmp($_SERVER['REMOTE_ADDR'], "unknown"))
           $ip = $_SERVER['REMOTE_ADDR'];
       else
           $ip = "unknown";
           
       $tmp = explode(',', $ip);
       if (count($tmp)>1){
           $ip = $tmp[0];
       }
       $len = strlen($ip);
       if ($len>15){
           $ip = substr($ip,0,15);
       }
       return $ip;
    }

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