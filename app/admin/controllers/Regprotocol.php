<?php
/*
* Regprotocol 注册协议
* @package	Regprotocol
* @author	Sun Guo Liang
* @since	Version 1.0.0
* @filesource
*/

class Regprotocol extends CI_Controller{

	public function __construct(){

		parent::__construct();
		$this->load->library('session');
		$this->load->helper('url_helper');
		$this->load->database();

		if(empty($this->session->admin)){
			//show_error('<a href="'.base_url().'index/login">请登录!</a>',null,'错误提示');
			jump('',site_url('index/login'));
		}

		$this->head_data['cname'] = __CLASS__;
		$this->table = $this->db->dbprefix('article');
		$this->load->view('templates/header.html',$this->head_data);
		$this->load->view('templates/menu.html',$this->head_data);

	}

	public function index(){
		$query = $this->db->query("SELECT * FROM {$this->table} WHERE articleId=2");
    	$data = $query->row();
		$this->load->view('regprotocol.html',$data);
	}

	public function save(){

		$content = $this->input->post('content');

		$data = array(
		    'articleContent' => $content
		);

		$this->db->where('articleId', 2);
		$bool = $this->db->update($this->table, $data);
		
		if($bool) {
            jump('操作成功',site_url('regprotocol/index'));
        }else{
            jump('操作失败');
        }

	}

}
?>