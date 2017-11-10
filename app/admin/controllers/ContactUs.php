<?php
/*
* ContactUs 联系我们
* @package	ContactUs
* @author	Sun Guo Liang
* @since	Version 1.0.0
* @filesource
*/

class Contactus extends CI_Controller{

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
		$query = $this->db->query("SELECT * FROM {$this->table} WHERE articleId=1");
    	$data = $query->row();
		$this->load->view('contactus.html',$data);
	}

	public function save(){

		$content = $this->input->post('content');

		$data = array(
		    'articleContent' => $content
		);

		//文件存在判断
		if(!empty($_FILES["attchment"]["name"]) && is_uploaded_file($_FILES["attchment"]["tmp_name"])){

			//重命名
			$attchname = $_FILES["attchment"]["name"];
			$ext = substr($attchname,strripos($attchname,'.') + 1);
			$name = date('Ymd').rand(0,999).'.'.$ext;
			$config['file_name'] = $name;

			$config['upload_path'] = 'upload/banner/';
			$config['allowed_types'] = 'gif|jpg|png|jpeg';
			$config['max_size'] = '0';
			$config['max_width'] = '1024';
			$config['max_height'] = '768';
			$this->load->library('upload', $config);
			$this->upload->initialize($config);
			$this->upload->do_upload('attchment');
			$info = $this->upload->data();

		    if($info && !empty($id)){//删除旧图
				$query = $this->db->query("SELECT articleAttach FROM {$this->table} WHERE articleId={$id}");
	    		$row = $query->row(); 
				@unlink($row->articleAttach);
		    }

		    $data['articleAttach'] = $config['upload_path'].$name;

		}

		$this->db->where('articleId', 1);
		$bool = $this->db->update($this->table, $data);
		
		if($bool) {
            jump('操作成功',site_url('contactus/index'));
        }else{
            jump('操作失败');
        }

	}

}
?>