<?php
/*
* Project 项目理财
* @package	Project
* @author	Sun Guo Liang
* @since	Version 1.0.0
* @filesource
*/

class Project extends CI_Controller{

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
		$this->table = $this->db->dbprefix('project');
		$this->load->view('templates/header.html',$this->head_data);
		$this->load->view('templates/menu.html',$this->head_data);

		$this->load->model('class_model');
		$this->class = array();
		//2.项目状态3.年收益率4.出借期限5.还款方式
		$this->class['status'] = $this->class_model->get_class(2);
		$this->class['yearRate'] = $this->class_model->get_class(3);
		$this->class['timeLimit'] = $this->class_model->get_class(4);
		$this->class['repayment'] = $this->class_model->get_class(5);

		//获取所有项目
		$query = $this->db->query("SELECT projectId,projectName FROM {$this->table}");
	    $this->data = $query->result();

	}
	public function index(){
		
		$pid = trim($this->input->get('pid'));

		$where = "WHERE 1=1";

		if(!empty($pid)){
			$where .= " AND projectId={$pid}";
		}

	    $config = array();
	    $config['per_page'] = 5; //每页显示的数据数
	    $current_page = intval($this->input->get('per_page')); //获取当前分页页码数
	    //page还原
	    if(0 == $current_page){
	      	$current_page = 1;
	    }
	    $offset = ($current_page - 1 ) * $config['per_page']; //设置偏移量 限定 数据查询 起始位置（从 $offset 条开始）

	    $query = $this->db->query("SELECT * FROM {$this->table} {$where}");
	    $result['total'] = $query->num_rows();

	    $sql = "
	    	SELECT 
	    		p.*,
	    		c.className 
	    	FROM 
	    		{$this->db->dbprefix('project')} p
	    	INNER JOIN
	    		{$this->db->dbprefix('class')} c 
	    	ON
	    		p.status=c.classId
	    	ORDER BY p.projectType DESC,p.projectOrd DESC,p.projectId DESC LIMIT {$offset},{$config['per_page']}
	    ";

	    $query = $this->db->query($sql);
	    $result['list'] = $query->result();
	    
	    $config['base_url']   = site_url('project/index');//'admin.php/project/index?';
	    
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
	    	'projects' => $this->data,
	        'rows' => $result['list'],
	        'total'  => $result['total'],
	        'current_page' => $current_page,
	        'per_page' => $config['per_page'],
	        'page'  => $this->pagination->create_links(),
	    );
		$this->load->view('project/index.html',$data);
	}

	public function add(){
		$data = array();
		$data['classes'] = $this->class; 
		$this->load->view('project/add.html',$data);
	}

	public function edit($id){

		$data = array();

		if(!empty($id)){
			$query = $this->db->query("SELECT * FROM {$this->table} WHERE projectId={$id}");
	    	$data = $query->row();
		}

		$data->classes = $this->class;
		
		$this->load->view('project/edit.html',$data);

	}

	public function save(){

		$id = $this->input->post('id');

		$type = $this->input->post('type');

		$name = $this->input->post('name');

		$ulimit = $this->input->post('ulimit');

		$lamount = $this->input->post('lamount');

		$mlamount = $this->input->post('mlamount');

		$rate = $this->input->post('rate');

		$progress = $this->input->post('progress');

		$tlimit = $this->input->post('tlimit');

		$repay = $this->input->post('repay');

		$date = $this->input->post('date');

		$status = $this->input->post('status');

		$other = $this->input->post('other');

		$time = $this->input->post('time');

		//$risk = $this->input->post('risk');//风险控制

		$data = array(
			'projectType' => $type,
		    'projectName' => $name,
		    'upperLendLimit' => $ulimit,
		    'loanAmount' => $lamount,
		    'minLoanAmount' => $mlamount,
		    'yearRate' => $rate,
		    'lendProgress' => $progress,
		    'timeLimit' => $tlimit,
		    'repayment' => $repay,
		    'interestDate' => $date,
		    //'riskManagement' => $risk,
		    'projectOther' => $other,
		    'status' => $status,
		    'createTime' => $time
		);
		
		if(empty($id)){
			$query = $this->db->query("SELECT * FROM {$this->table} WHERE projectName='{$name}'");
	    	$row = $query->row(); 
			if($row){
				jump('该项目已存在',site_url('project/add'));
				exit();
			}
			$bool = $this->db->insert($this->table, $data);
		}else{
			$this->db->where('projectId', $id);
			$bool = $this->db->update($this->table, $data);
		}
		
		if($bool) {

			//已还款更新金额表状态
			if($status == 18){
				$this->db->where('pid', $id);
				$this->db->update($this->db->dbprefix('fund'), array('status'=>1,'endTime'=>date('Y-m-d H:i:s')));
			}

            jump('操作成功',site_url('project/index'));
        }else{
            jump('操作失败');
        }

	}

	public function del($id = null){

		$pid = $this->input->post('id');

		$gid = $id;

		$bool = false;

		if($pid){//多删

			//删除所有项目下借款人
			$this->db->query("DELETE FROM {$this->db->dbprefix}project_user WHERE projectId IN(".implode(',', $pid).")");

			//删除所有项目下会员
			$this->db->query("DELETE FROM {$this->db->dbprefix}fund WHERE pid IN(".implode(',', $pid).")");

			$bool = $this->db->query("DELETE FROM {$this->table} WHERE projectId IN(".implode(',', $pid).")"); 

		}else if (!empty($gid)) {//单删

			//删除所有项目下借款人
			$this->db->query("DELETE FROM {$this->db->dbprefix}project_user WHERE projectId={$gid}");

			//删除所有项目下会员
			$this->db->query("DELETE FROM {$this->db->dbprefix}fund WHERE pid={$gid}");
			
			$bool = $this->db->delete($this->table, array('projectId' => $gid));

		}
		
		if($bool) {
            jump('操作成功',site_url('project/index'));
        }else{
            jump('操作失败');
        }

	}

}
?>