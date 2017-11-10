<?php
/*
* Salesman 业务员管理
* @package	Salesman
* @author	Sun Guo Liang
* @since	Version 1.0.0
* @filesource
*/

class Salesman extends CI_Controller{

	public function __construct(){

		parent::__construct();
		$this->load->library('session');
		$this->load->helper('url_helper');
		$this->load->database();

		if(empty($this->session->admin)){
			//show_error('<a href="'.base_url().'index/login">请登录!</a>',null,'错误提示');
			jump('',site_url('index/login'));
		}

		$this->load->library('pagination');
		$this->head_data['cname'] = __CLASS__;
		$this->table = $this->db->dbprefix('salesman');
		$this->load->view('templates/header.html',$this->head_data);
		$this->load->view('templates/menu.html',$this->head_data);

	}

	public function index(){
		
		$where = "WHERE 1=1";

		$mobile = $this->input->get('mobile');
		if(!empty($mobile)){
			$where .= " AND mobile='{$mobile}'";
		}

	    $config = array();
	    $config['per_page'] = 2; //每页显示的数据数
	    $current_page = intval($this->input->get('per_page')); //获取当前分页页码数
	    //page还原
	    if(0 == $current_page){
	      	$current_page = 1;
	    }
	    $offset = ($current_page - 1 ) * $config['per_page']; //设置偏移量 限定 数据查询 起始位置（从 $offset 条开始）

	    $query = $this->db->query("SELECT * FROM {$this->table} {$where}");
	    $result['total'] = $query->num_rows();

	    $query = $this->db->query("SELECT * FROM {$this->table} {$where} ORDER BY id DESC LIMIT {$offset},{$config['per_page']}");
	    $result['list'] = $query->result();

	    $config['base_url']   = site_url('salesman/index');//'admin.php/salesman/index?';
	    
		$config['first_link'] = '首页';
		$config['first_tag_open'] = '<span class="first paginate_button paginate_button_disabled">';
		$config['first_tag_close'] = '</span>';

		$config['last_link'] = '尾页';
		//$config['last_tag_open'] = '<div>';
		//$config['last_tag_close'] = '</div>';

		$config['next_link'] = '下一页';
		$config['next_tag_open'] = '<span class="next paginate_button">';
		$config['next_tag_close'] = '</span>';

		$config['prev_link'] = '上一页';
		$config['prev_tag_open'] = '<span class="previous paginate_button paginate_button_disabled">';
		$config['prev_tag_close'] = '</span>';

		$config['cur_tag_open'] = ' <b>';
		$config['cur_tag_close'] = '</b>';

	    $config['total_rows'] = $result['total'];//总条数
	    $config['num_links']  = 2;//页码连接数
	    $config['use_page_numbers'] = TRUE;//使用页码而不是offset
	    $config['use_page_titles']  = TRUE;
	    $config['page_query_string'] = TRUE;
	    $this->load->library('pagination');//加载ci pagination类
	    $this->pagination->initialize($config);

	    $data = array(
	    	'mobile' => $mobile,
	        'rows' => $result['list'],
	        'total'  => $result['total'],
	        'current_page' => $current_page,
	        'per_page' => $config['per_page'],
	        'page'  => $this->pagination->create_links(),
	    );
		$this->load->view('salesman/index.html',$data);
	}

	public function add(){
		$this->load->view('salesman/add.html');
	}

	public function edit($id){

		$data = array();

		if(!empty($id)){
			$query = $this->db->query("SELECT * FROM {$this->table} WHERE id={$id}");
	    	$data = $query->row();
		}
	
		$this->load->view('salesman/edit.html',$data);

	}

	public function save(){

		$id = $this->input->post('id');

		$name = $this->input->post('name');

		$sex = $this->input->post('sex');

		$birth = $this->input->post('birth');

		$number = $this->input->post('number');

		$mobile = $this->input->post('mobile');

		$email = $this->input->post('email');

		$province = $this->input->post('province');

		$city = $this->input->post('city');

		$remark = $this->input->post('remark');
		
		$data = array(
		    'realname' => $name,
		    'sex' => $sex,
		    'birth' => $birth,
		    'identificationNumber' => $number,
		    'mobile' => $mobile,
		    'email' => $email,
		    'province' => $province,
		    'city' => $city,
		    'remark' => $remark,
		    'createTime' => date('Y-m-d')
		);
		
		if(empty($id)){
			$bool = $this->db->insert($this->table, $data);
		}else{
			$this->db->where('id', $id);
			$bool = $this->db->update($this->table, $data);
		}
		
		if($bool) {
            jump('操作成功',site_url('salesman/index'));
        }else{
            jump('操作失败');
        }

	}

	public function del($id = null){

		$pid = $this->input->post('id');

		$gid = $id;

		$bool = false;

		if($pid){//多删

			$bool = $this->db->query("DELETE FROM {$this->table} WHERE id IN(".implode(',', $pid).")"); 

		}else if (!empty($gid)) {//单删

			$bool = $this->db->delete($this->table, array('id' => $gid));

		}
		
		if($bool) {
            jump('操作成功',site_url('salesman/index'));
        }else{
            jump('操作失败');
        }

	}

}
?>