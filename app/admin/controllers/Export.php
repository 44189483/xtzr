<?php
/*
* Export 导出EXCEL
* @package	User
* @author	Sun Guo Liang
* @since	Version 1.0.0
* @filesource
*/

class Export extends CI_Controller{

	public function __construct(){

		parent::__construct();
		$this->load->library('session');
		$this->load->helper('url_helper');
		$this->load->database();

		if(empty($this->session->admin)){
			//show_error('<a href="'.base_url().'index/login">请登录!</a>',null,'错误提示');
			jump('',site_url('index/login'));
		}

		$this->table = $this->db->dbprefix('member');

	}

	public function excel(){

		header('Content-type: text/html; charset=utf-8');
		header("Content-type:application/vnd.ms-excel;charset=UTF-8"); 
		header("Content-Disposition:filename=会员.xls"); //输出的表格名称

		$query = $this->db->query("SELECT * FROM {$this->table}");
	    $rows = $query->result(); 

		//if(preg_match('/Mac/i', $_SERVER['HTTP_USER_AGENT'])){  
	    	//苹果电脑 MAC 
	    	$content = "<table>";
	    	$content .= "<tr><td>姓名</td><td>性别</td><td>手机</td><td>邮箱</td><td>身份证</td><td>银行卡</td><td>婚姻状况</td><td>学历</td><td>工作</td><td>所在城市</td><td>所属业务员</td><td>加入时间</td><tr/>";
	    	foreach ($rows as $k => $v) {
	    		$content .= "<tr>";
	    		$content .= "<td>{$v->realname}<td>";
	    		$content .= "<td>".$v->sex == 1 ? '男' : '女'."</td>";
	    		$content .= "<td>{$v->mobile}</td>";
	    		$content .= "<td>{$v->email}</td>";
	    		$content .= "<td>{$v->identificationNumber}</td>";
	    		$content .= "<td>{$v->bankcard}</td>";
	    		$content .= "<td>{$v->marital}</td>";
	    		$content .= "<td>{$v->education}</td>";
	    		$content .= "<td>{$v->job}</td>";
	    		$content .= "<td>{$v->province}-{$v->city}</td>";
				if(!empty($v->sid)){
					$query = $this->db->query("SELECT realname FROM {$this->db->dbprefix('salesman')} WHERE id={$v->sid}");
		    		$row = $query->row();
		    		$content .= "<td>{$row->realname}</td>";
				}else{
					$content .= "<td> </td>";
				}
				$content .= "<td>{$v->createTime}</td>";
				$content .= "</tr>";
			}
	    	$content .= "</table>";
	    	//苹果电脑 MAC
	    	if(preg_match('/Mac/i', $_SERVER['HTTP_USER_AGENT'])){
	    		echo $content;
	    	}else{
	    		echo iconv("UTF-8","GB2312",$content);
	    	}
	    /*
	    }else{

	    	$title = "姓名\t 性别\t 手机\t 邮箱\t 身份证\t 银行卡\t 婚姻状况\t 学历\t 工作\t 所在城市\t 所属业务员\t 加入时间\t\n";

	    	echo iconv("UTF-8","GB2312",$title);

	    	foreach ($rows as $k => $v) {
				echo iconv("UTF-8","GB2312",$v->realname)."\t";
				echo iconv("UTF-8","GB2312",$v->sex == 1 ? "男" : "女")."\t";
				echo $v->mobile."\t";
				echo $v->email."\t";
				echo $v->identificationNumber."\t";
				echo iconv("UTF-8","GB2312",$v->bankcard)."\t";
				echo iconv("UTF-8","GB2312",$v->marital)."\t";
				echo iconv("UTF-8","GB2312",$v->education)."\t";
				echo iconv("UTF-8","GB2312",$v->job)."\t";
				echo iconv("UTF-8","GB2312",$v->province."-".$v->city)."\t";
				if(!empty($v->sid)){
					$query = $this->db->query("SELECT realname FROM {$this->db->dbprefix('salesman')} WHERE id={$v->sid}");
		    		$row = $query->row();
		    		echo iconv("UTF-8","GB2312",$row->realname)."\t";
				}else{
					echo " \t";
				}
				echo $v->createTime."\t\n";
			}

	    }
	    */  

	}


}
?>