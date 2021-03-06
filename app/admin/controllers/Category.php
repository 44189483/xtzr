<?php
/*
* Category 整体分类
* @package	Category
* @author	Sun Guo Liang
* @since	Version 1.0.0
* @filesource
*/

class Category extends CI_Controller{

	public function __construct(){

		parent::__construct();
		$this->load->library('session');
		$this->load->helper('url_helper');
		if(empty($this->session->admin)){
			//show_error('<a href="'.base_url().'index/login">请登录!</a>',null,'错误提示');
			jump('',site_url('index/login'));
		}
		$this->load->library('pagination');
		$this->load->database();
		$this->head_data['cname'] = __CLASS__;

		$this->table = $this->db->dbprefix('class');

		$this->load->view('templates/header.html',$this->head_data);
		$this->load->view('templates/menu.html',$this->head_data);

	}
	
	public function index($type){

		if(empty($type) || $type == 0){
			show_error('分类参数非法',null,'错误提示');
		}else{
			switch ($type) {
				case '1':
					$title = '新闻';
					break;
			}
		}
		
		$where = "WHERE classType={$type}";

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

	    $query = $this->db->query("SELECT * FROM {$this->table} {$where} ORDER BY classId DESC LIMIT {$offset},{$config['per_page']}");
	    $result['list'] = $query->result();

	    $config['base_url']   = site_url("category/index/type/{$type}");//'admin.php/news/index?';
	    
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
	    	'title' => $title,
	    	'type' => $type,
	        'rows' => $result['list'],
	        'total'  => $result['total'],
	        'current_page' => $current_page,
	        'per_page' => $config['per_page'],
	        'page'  => $this->pagination->create_links(),
	    );
		$this->load->view('category.html',$data);
	}

	public function save(){

		$type = $this->input->post('type');

		$cname = $this->input->post('cname');
		
		$data = array(
		    'classType' => $type,
		    'className' => $cname
		);
		
		$bool = $this->db->insert($this->table, $data);

		if($bool) {
            jump('操作成功',site_url("category/index/{$type}"));
        }else{
            jump('操作失败');
        }

	}

	public function del($type,$id = null){

		$pid = $this->input->post('id');

		$gid = $id;

		$bool = false;

		if($pid){//多删

			//删除分类下所有关联的文章
			$this->db->query("DELETE FROM bxj_article WHERE classId IN(".implode(',', $pid).")"); 

			$bool = $this->db->query("DELETE FROM {$this->table} WHERE classId IN(".implode(',', $pid).")"); 

		}else if (!empty($gid)) {//单删

			$this->db->query("DELETE FROM bxj_article WHERE classId={$gid}"); 

			$bool = $this->db->delete($this->table, array('classId' => $gid));

		}
		
		if($bool) {
            jump('操作成功',site_url("category/index/{$type}"));
        }else{
            jump('操作失败');
        }

	}

}
?>