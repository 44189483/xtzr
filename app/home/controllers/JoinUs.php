<?php
/*
* JoinUs 联系我们
* @package	JoinUs
* @author	Sun Guo Liang
* @since	Version 1.0.0
* @filesource
*/

class JoinUs extends CI_Controller{

	public function __construct(){

		parent::__construct();
		$this->load->library('session');
		$this->load->helper('url_helper');
		$this->load->helper('func_helper');
		$this->templates = isMobile() == true ? 'mobile/' : 'pc/';
		$this->load->database();

		$this->table = $this->db->dbprefix('article');
	}

	public function Index(){

		$query = $this->db->query("SELECT articleTitle,articleContent FROM {$this->table} WHERE articleType=4 ORDER BY articleOrd DESC,articleId DESC");
    	$data['joinus'] = $query->result();

		$header = array(
			'nav' => '联系我们',
			'cname' => __CLASS__,
			'fname' => '',
			'member' => $this->session->member
		);

		$this->load->view($this->templates.'templates/header.html',$header);
		$this->load->view($this->templates.'joinus.html',$data);
		$this->load->view($this->templates.'templates/footer.html');
		
	}

}
?>