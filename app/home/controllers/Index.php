<?php
/*
* Index 首页
* @package	Index
* @author	Sun Guo Liang
* @since	Version 1.0.0
* @filesource
*/

class Index extends CI_Controller{

	public function __construct(){

		parent::__construct();
		$this->load->library('session');
		$this->load->helper('url_helper');
		$this->load->database();
		$this->load->helper('func_helper');
		$this->templates = isMobile() == true ? 'mobile/' : 'pc/';
	}

	public function index(){

		$header = array(
			'nav' => '首页',
			'cname' => __CLASS__,
			'fname' => '',
			'member' => $this->session->member
		);

		//轮播图
		$b_query = $this->db->query("SELECT * FROM {$this->db->dbprefix('img')} WHERE type='banner' ORDER BY ord DESC,id DESC");
	    $result['banner'] = $b_query->result();

	    //数据信息
	    $d_query = $this->db->query("SELECT * FROM {$this->db->dbprefix('option')} WHERE optionType='index_amount'");
	    $datas = $d_query->result();
	    foreach ($datas as $val) {
	    	$result['data'][$val->optionKey] = $val->optionValue;
	    }

	    //公告信息
	    $n_query = $this->db->query("SELECT * FROM {$this->db->dbprefix('article')} WHERE articleType=0 ORDER BY articleId DESC");
	    $result['notice'] = $n_query->result();

	    /*
	    $lend_sql = "
	    	SELECT 
	    		p.*,
	    		c.className 
	    	FROM 
	    		{$this->db->dbprefix('project')} p
	    	INNER JOIN
	    		{$this->db->dbprefix('class')} c 
	    	ON
	    		p.status=c.classId
	    	WHERE 1=1
	    ";//$lend_sql." AND p.projectType=1 LIMIT 1"
	    */

	    //新手专享
	    $novice_query = $this->db->query("SELECT * FROM {$this->db->dbprefix('project')} WHERE projectType=1 LIMIT 1");
	    $result['novice'] = $novice_query->row();

	    //出借服务
	    //$s_query = $this->db->query($lend_sql." AND p.projectType=0 ORDER BY p.projectOrd DESC,p.projectId DESC LIMIT 0,5");
	    //$result['service'] = $s_query->result();

	    //获取分类排除新手项目同时只有存在项目只显示分类
	    $sql = "
	    	SELECT 
	    		c.classId,
	    		c.className
	    	FROM 
	    		{$this->db->dbprefix('project')} p
	    	INNER JOIN
	    		{$this->db->dbprefix('class')} c 
	    	ON
	    		p.timeLimit=c.className
	    	WHERE 
	    		c.classType=4
	    		AND
	    		p.projectType=0
	    	GROUP BY c.className ORDER BY ord DESC,classId ASC
	    ";
	    $c_query = $this->db->query($sql);
	    $class = $c_query->result();
	    foreach ($class as $key => $val) {
	    	$result['service'][$key]['className'] = $val->className;
	    	$query = $this->db->query("SELECT * FROM {$this->db->dbprefix('project')} WHERE timeLimit='{$val->className}' ORDER BY projectOrd DESC,projectId DESC LIMIT 0,6");
	    	$result['service'][$key]['projects'] = $query->result();
	    }

	    $this->load->view($this->templates.'templates/header.html',$header);
		$this->load->view($this->templates.'index.html',$result);
		$this->load->view($this->templates.'templates/footer.html');

	}

}
?>