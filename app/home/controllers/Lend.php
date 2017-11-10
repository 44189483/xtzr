<?php
/*
* Lend 出借页面
* @package	Lend
* @author	Sun Guo Liang
* @since	Version 1.0.0
* @filesource
*/

class Lend extends CI_Controller{

	public function __construct(){
		parent::__construct();
		$this->load->library('session');
		$this->load->helper('url_helper');
		$this->load->database();
		$this->load->helper('func_helper');
		$this->templates = isMobile() == true ? 'mobile/' : 'pc/';
		$this->table = $this->db->dbprefix('project');
		$this->header = array(
			'cname' => __CLASS__,
			'fname' => '',
			'member' => $this->session->member
		);
	}

	public function Index(){

		$this->header['nav'] = '出借';
	    $this->load->view($this->templates.'templates/header.html',$this->header);
		$this->load->view($this->templates.'lend.html');
		$this->load->view($this->templates.'templates/footer.html');

	}

	public function getlist($cat,$e){

		if(empty($cat)){
			show_error('页面参数有误', null, '错误提示');
		}

		//项目状态
		$status = $this->get_class(2);
		//年收益率
		$yearRate = $this->get_class(3);
		//出借期限
		$timeLimit = $this->get_class(4);
		//还款方式
		$repayment = $this->get_class(5);

		$where = "WHERE projectType=0";

		$getparam = explode('-', $cat);

		$count = count($getparam);

		if($count < 4){
			show_error('页面参数有误', null, '错误提示');
		}

		if($getparam[0] > 0){
			$where .= " AND p.status='{$getparam[0]}'";
		}

		if($getparam[1] > 0){
			$where .= " AND p.yearRate='{$this->get_class_name($getparam[1])}'";
		}

		if($getparam[2] > 0){
			$where .= " AND p.timeLimit='{$this->get_class_name($getparam[2])}'";
		}

		if($getparam[3] > 0){
			$where .= " AND p.repayment='{$this->get_class_name($getparam[3])}'";
		}

	    $config = array();
	    $config['per_page'] = 10; //每页显示的数据数
	    $current_page = intval($this->input->get('per_page')); //获取当前分页页码数
	    //page还原
	    if($current_page == 0){
	      	$current_page = 1;
	    }
	    $offset = ($current_page - 1 ) * $config['per_page']; //设置偏移量 限定 数据查询 起始位置（从 $offset 条开始）

	    $query = $this->db->query("SELECT * FROM {$this->table} p {$where}");
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
	    	{$where}	
	    	ORDER BY p.projectOrd DESC,p.projectId DESC LIMIT {$offset},{$config['per_page']}
	    ";

	    //出借服务
	    $lend_query = $this->db->query($sql);
	    $result['list'] = $lend_query->result();

	    $config['base_url']   = site_url('lend');

	    $config['full_tag_open'] = '<ul class="page">';
	    $config['full_tag_close'] = '</ul>';

		$config['first_link'] = '首页';
		$config['first_tag_open'] = '<li>';
		$config['first_tag_close'] = '</li>';

		$config['last_link'] = '尾页';
		$config['last_tag_open'] = '<li>';
		$config['last_tag_close'] = '</li>';

		$config['next_link'] = '下一页';
		$config['next_tag_open'] = '<li>';
		$config['next_tag_close'] = '</li>';

		$config['prev_link'] = '上一页';
		$config['prev_tag_open'] = '<li>';
		$config['prev_tag_close'] = '</li>';
		
		$config['cur_tag_open'] = '<li class="active">';
		$config['cur_tag_close'] = '</li>';

		$config['num_tag_open'] = '<li>';
		$config['num_tag_close'] = '</li>';

	    $config['total_rows'] = $result['total'];//总条数
	    $config['num_links']  = 5;//页码连接数
	    $config['use_page_numbers'] = TRUE;//使用页码而不是offset
	    $config['use_page_titles']  = TRUE;
	    $config['page_query_string'] = TRUE;
	    $this->load->library('pagination');//加载ci pagination类
	    $this->pagination->initialize($config);

	    $data = array(
	    	'cat' => $cat,
	    	'getparam' => $getparam,
	    	'status' => $status,
	    	'yearRate' => $yearRate,
	    	'timeLimit' => $timeLimit,
	    	'repayment' => $repayment,
	        'rows' => $result['list'],
	        'total'  => $result['total'],
	        'current_page' => $current_page,
	        'per_page' => $config['per_page'],
	        'page'  => $this->pagination->create_links(),
	    );

	    $this->load->view($this->templates.'loadlend.html',$data);

	}

	public function detail($id){

		if(empty($id)){
			show_error('参数有误',null,'错误提示');
		}

		//详情
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
	    	WHERE projectId={$id}
	    ";
		$query = $this->db->query($sql);
    	$data = $query->row();

    	if(!$data){
    		show_error('参数有误',null,'错误提示');
    	}

    	switch ($data->repayment) {
    		case '按月还本付息':
    			$tip = '每月按照一定比例归还本金、支付利息';
    			break;
    		case '等额本息':
    			$tip = '每月偿还同等数额的借款，包括本金和利息';
    			break;
    		case '先息后本':
    			$tip = '每月支付利息，到期支付最后一期利息和全部本金';
    			break;
    		case '一次性还本付息':
    			$tip = '到期后一次性归还本金和利息';
    			break;
    	}

    	$data->tip = $tip;

    	//借款人
    	$res = $this->db->query("SELECT * FROM {$this->db->dbprefix('project_user')} WHERE projectId={$id}");
    	$data->users = $res->result();

		$this->header['nav'] = $data->projectName;

    	$this->load->view($this->templates.'templates/header.html',$this->header);
		$this->load->view($this->templates.'lenddetail.html',$data);
		$this->load->view($this->templates.'templates/footer.html');
		
	}

	//获取大分类
	public function get_class($slug){
		$query = $this->db->query("SELECT * FROM {$this->db->dbprefix}class WHERE classType={$slug}");
	    return $query->result();
	}

	//获取分类名称
	public function get_class_name($cid){
		$query = $this->db->query("SELECT className FROM {$this->db->dbprefix}class WHERE classId={$cid}");
	    $row = $query->row();
	    return $row->className;
	}

}
?>