<?php
/*
* User 借款人
* @package	User
* @author	Sun Guo Liang
* @since	Version 1.0.0
* @filesource
*/

class User extends CI_Controller{

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
		$this->table = $this->db->dbprefix('project_user');
		$this->load->view('templates/header.html',$this->head_data);
		$this->load->view('templates/menu.html',$this->head_data);

		//获取所有项目
		$query = $this->db->query("SELECT projectId,projectName FROM {$this->db->dbprefix('project')} ORDER BY projectType DESC,projectId DESC");
	    $this->data = $query->result();

	}

	public function index($pid = null){

		if(empty($pid)){
			$pid = $this->input->get('pid');
		}
		
		$where = "WHERE 1=1";

		if(!empty($pid)){
			$where .= " AND u.projectId={$pid}";
			$q = $this->db->query("SELECT projectName FROM {$this->db->dbprefix('project')} WHERE projectId={$pid}");
	    	$res = $q->row();
	    	$pname = $res->projectName;
		}else{
			$pname = '';
		}

	    $config = array();
	    $config['per_page'] = 10; //每页显示的数据数
	    $current_page = intval($this->input->get('per_page')); //获取当前分页页码数
	    //page还原
	    if(0 == $current_page){
	      	$current_page = 1;
	    }
	    $offset = ($current_page - 1 ) * $config['per_page']; //设置偏移量 限定 数据查询 起始位置（从 $offset 条开始）

	    $query = $this->db->query("SELECT * FROM {$this->table} u {$where}");
	    $result['total'] = $query->num_rows();

	    $sql = "
	    	SELECT 
	    		p.projectName,
	    		u.* 
	    	FROM 
	    		bxj_project_user u
	    	INNER JOIN
	    		bxj_project p
	    	ON
	    		u.projectId=p.projectId
	    	{$where}
	    	ORDER BY u.userId DESC
	    	LIMIT {$offset},{$config['per_page']}
	    ";

	    $query = $this->db->query($sql);
	    $result['list'] = $query->result();

	    $config['base_url']   = site_url('user/index');//'admin.php/user/index?';
	    
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
	    	'pid' => $pid,
	    	'pname' => $pname,
	    	'projects' => $this->data,
	        'rows' => $result['list'],
	        'total'  => $result['total'],
	        'current_page' => $current_page,
	        'per_page' => $config['per_page'],
	        'page'  => $this->pagination->create_links(),
	    );
		$this->load->view('user/index.html',$data);
	}

	public function add($pid = null){
		if(!empty($pid)){
			$data['pid'] = $pid;
		}
		$data['projects'] = $this->data;
		$this->load->view('user/add.html',$data);
	}

	public function edit($id,$pid = null){

		$data = array();

		if(!empty($id)){
			$query = $this->db->query("SELECT * FROM {$this->table} WHERE userId={$id}");
	    	$data = $query->row();
		}

		$data->projects = $this->data;
	
		$this->load->view('user/edit.html',$data);

	}

	public function save(){

		$id = $this->input->post('id');

		$pid = $this->input->post('pid');

		$name = $this->input->post('name');

		$number = $this->input->post('number');

		$dbwp = $this->input->post('dbwp');

		$dbamount = $this->input->post('dbamount');

		$jkamount = $this->input->post('jkamount');

		$time = $this->input->post('time');

		$data = array(
		    'realName' => $name,
		    'identificationNumber' => $number,
		    'mortgageThing' => $dbwp,
		    'mortgageAmount' => $dbamount,
		    'lendAmount' => $jkamount,
		    'projectId' => $pid,
		    'createTime' => $time
		);
		
		if(empty($id)){
			$bool = $this->db->insert($this->table, $data);
		}else{
			$this->db->where('userId', $id);
			$bool = $this->db->update($this->table, $data);
		}
		
		if($bool) {
            jump('操作成功',site_url('user/index'));
        }else{
            jump('操作失败');
        }

	}

	public function del($id = null){

		$pid = $this->input->post('id');

		$gid = $id;

		$bool = false;

		if($pid){//多删

			$bool = $this->db->query("DELETE FROM {$this->table} WHERE userId IN(".implode(',', $pid).")"); 

		}else if (!empty($gid)) {//单删

			$bool = $this->db->delete($this->table, array('userId' => $gid));

		}
		
		if($bool) {
            jump('操作成功',site_url('user/index'));
        }else{
            jump('操作失败');
        }

	}

}
?>