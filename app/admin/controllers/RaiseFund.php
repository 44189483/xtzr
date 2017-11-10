<?php
/*
* RaiseFund 集资会员
* @package	RaiseFund
* @author	Sun Guo Liang
* @since	Version 1.0.0
* @filesource
*/

class Raisefund extends CI_Controller{

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
		$this->table = $this->db->dbprefix('fund');
		$this->load->view('templates/header.html',$this->head_data);
		$this->load->view('templates/menu.html',$this->head_data);

		//获取当前项目
		$this->load->model('project_model');

		//获取所有会员
		$query = $this->db->query("SELECT id,realname,mobile FROM {$this->db->dbprefix('member')}");
	    $this->members = $query->result();

	}

	public function index($pid){

	    $config = array();
	    $config['per_page'] = 10; //每页显示的数据数
	    $current_page = intval($this->input->get('per_page')); //获取当前分页页码数
	    //page还原
	    if(0 == $current_page){
	      	$current_page = 1;
	    }
	    $offset = ($current_page - 1 ) * $config['per_page']; //设置偏移量 限定 数据查询 起始位置（从 $offset 条开始）

	    $sql = "
	    	SELECT 
	    		m.realname,
	    		m.mobile,
	    		f.id,
	    		f.principal,
	    		f.interest,
	    		f.status,
	    		f.startTime 
	    	FROM 
	    		bxj_fund f
	    	INNER JOIN
	    		bxj_member m
	    	ON
	    		f.mid=m.id
	    	WHERE f.pid={$pid}
	    ";

	    $query = $this->db->query($sql);
	    $result['total'] = $query->num_rows();

	    $sql .= " LIMIT {$offset},{$config['per_page']}";

	    $query = $this->db->query($sql);
	    $result['list'] = $query->result();

	    $config['base_url']   = site_url('raisefund/index');//'admin.php/raisefund/index?';
	    
		$config['first_link'] = '首页';
		$config['first_tag_open'] = '<span class="first paginate_button paginate_button_disabled">';
		$config['first_tag_close'] = '</span>';

		$config['last_link'] = '尾页';

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

	    $project = $this->project_model->get_project($pid);

	    $data = array(
	    	'pid' => $pid,
	    	'pname' => $project->projectName,
	        'rows' => $result['list'],
	        'total'  => $result['total'],
	        'current_page' => $current_page,
	        'per_page' => $config['per_page'],
	        'page'  => $this->pagination->create_links(),
	    );
		$this->load->view('raisefund/index.html',$data);
	}

	public function add($pid){
		$project = $this->project_model->get_project($pid);
		$data = array(
			'pid' => $pid,
			'pname' => $project->projectName,
			'members' => $this->members
		);
		$this->load->view('raisefund/add.html',$data);
	}

	public function edit($id){
		$query = $this->db->query("SELECT * FROM {$this->table} WHERE id={$id}");
    	$data = $query->row();
    	$project = $this->project_model->get_project($data->pid);
    	$data->pname = $project->projectName;
    	$data->members = $this->members;
		$this->load->view('raisefund/edit.html',$data);
	}

	public function save(){

		$id = $this->input->post('id');

		$pid = $this->input->post('pid');

		$mid = $this->input->post('mid');

		$principal = $this->input->post('principal');

		$interest = $this->input->post('interest');

		$time = $this->input->post('time');

		$data = array(
		    'pid' => $pid,
		    'mid' => $mid,
		    'principal' => $principal,
		    'interest' => $interest,
		    'startTime' => $time
		);
		
		if(empty($id)){

			$query = $this->db->query("SELECT * FROM {$this->table} WHERE pid={$pid} AND mid={$mid}");
	    	$row = $query->row(); 
			if($row){
				jump('该集资会员已存在',site_url('raisefund/add/'.$pid));
				exit();
			}

			$bool = $this->db->insert($this->table, $data);
		}else{
			$this->db->where('id', $id);
			$bool = $this->db->update($this->table, $data);
		}
		
		if($bool) {
            jump('操作成功',site_url('raisefund/index/'.$pid));
        }else{
            jump('操作失败');
        }

	}

	public function del($id = null,$pid = null){

		if(empty($pid)){
			$pid = $this->input->post('pid');
		}

		$ids = $this->input->post('id');

		$gid = $id;

		$bool = false;

		if($ids){//多删

			$bool = $this->db->query("DELETE FROM {$this->table} WHERE id IN(".implode(',', $ids).")"); 

		}else if (!empty($gid)) {//单删

			$bool = $this->db->delete($this->table, array('id' => $gid));

		}
		
		if($bool) {
            jump('操作成功',site_url('raisefund/index/'.$pid));
        }else{
            jump('操作失败');
        }

	}

}
?>