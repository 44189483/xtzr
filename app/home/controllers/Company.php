<?php
/*
* Company 信息披露
* @package	Company
* @author	Sun Guo Liang
* @since	Version 1.0.0
* @filesource
*/

class Company extends CI_Controller{

	public function __construct(){

		parent::__construct();
		$this->load->library('session');
		$this->load->helper('url_helper');
		$this->load->helper('func_helper');
		$this->templates = isMobile() == true ? 'mobile/' : 'pc/';
		$this->load->database();
		$this->table = $this->db->dbprefix('article');
	}

	public function Index($id = null){

		$data = array();

		//获取所有信息分类
		$info_query = $this->db->query("SELECT articleId,articleTitle FROM {$this->table} WHERE articleType=2 AND articleShow=1 ORDER BY articleOrd DESC,articleId DESC");
	    $data['classes'] = $info_query->result();

	    if(empty($id)){
			$where = "WHERE articleId={$data['classes'][0]->articleId}";
			$data['id'] = $data['classes'][0]->articleId;
		}else{
			$where = "WHERE articleId={$id}";
			$data['id'] = $id;
		}

		$query = $this->db->query("SELECT * FROM {$this->table} {$where}");
    	$data['info'] = $query->row();

    	if(!$data['info']){
			show_error('页面参数有误', null, '错误提示');
		}

		$header = array(
			'nav' => $data['info']->articleTitle,
			'cname' => __CLASS__,
			'fname' => '',
			'member' => $this->session->member
		);

		$this->load->view($this->templates.'templates/header.html',$header);
		$this->load->view($this->templates.'company.html',$data);
		$this->load->view($this->templates.'templates/footer.html');
		
	}

}
?>