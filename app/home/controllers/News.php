<?php
class News extends CI_Controller{

	public function __construct(){
		parent::__construct();
		$this->load->library('session');
		$this->load->helper('url_helper');
		$this->load->database();
		$this->load->helper('func_helper');
		$this->templates = isMobile() == true ? 'mobile/' : 'pc/';
		$this->table = $this->db->dbprefix('article');
		$this->header = array(
			'cname' => __CLASS__,
			'fname' => '',
			'member' => $this->session->member
		);

		//获取所有新闻分类
		$class_query = $this->db->query("SELECT * FROM {$this->db->dbprefix('class')} WHERE classType=1 ORDER BY ord DESC,classId DESC");
	    $this->classes = $class_query->result();
	}

	public function Index($cid = null){

		if(!empty($cid)){
			$where = "WHERE classId={$cid}";
		}else{
			$cid = $this->classes[0]->classId;
			$where = "WHERE classId={$this->classes[0]->classId}";
		}

		//获取单个新闻分类
		$c_query = $this->db->query("SELECT * FROM {$this->db->dbprefix('class')} {$where}");
		$class = $c_query->row();

		$this->header['nav'] = $class->className;

		$where .= " AND articleShow=1";
		
	    $config = array();
	    $config['per_page'] = 10; //每页显示的数据数
	    $current_page = intval($this->input->get('per_page')); //获取当前分页页码数
	    //page还原
	    if(0 == $current_page){
	      	$current_page = 1;
	    }
	    $offset = ($current_page - 1 ) * $config['per_page']; //设置偏移量 限定 数据查询 起始位置（从 $offset 条开始）

	    $query = $this->db->query("SELECT * FROM {$this->table} {$where}");
	    $result['total'] = $query->num_rows();

	    $news_query = $this->db->query("SELECT * FROM {$this->table} {$where} ORDER BY articleOrd DESC,articleId DESC LIMIT {$offset},{$config['per_page']}");
	    $result['list'] = $news_query->result();

	    $config['base_url']   = site_url('news');

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
	    	'cid' => $cid,
	    	'class' => $class,
	    	'classes' => $this->classes,
	        'rows' => $result['list'],
	        'total'  => $result['total'],
	        'current_page' => $current_page,
	        'per_page' => $config['per_page'],
	        'page'  => $this->pagination->create_links(),
	    );

	    $this->load->view($this->templates.'templates/header.html',$this->header);
		$this->load->view($this->templates.'news.html',$data);
		$this->load->view($this->templates.'templates/footer.html');

	}

	public function detail($id){

		if(empty($id)){
			show_error('参数有误',null,'错误提示');
		}

		//详情
		$query = $this->db->query("SELECT * FROM {$this->table} WHERE articleId={$id}");
    	$data = $query->row();

    	if(!$data){
    		show_error('参数有误',null,'错误提示');
    	}

    	$data->classes = $this->classes;

		$this->header['nav'] = $data->articleTitle;

		$sql = "SELECT articleId,articleTitle FROM {$this->table} WHERE articleType=1 AND articleShow=1 AND classId={$data->classId}";

		//上一条
		$prev_query = $this->db->query($sql." AND articleId<{$id}");
    	$data->prev = $prev_query->row();

    	//下一条
		$next_query = $this->db->query($sql." AND articleId>{$id}");
    	$data->next = $next_query->row();

    	$this->load->view($this->templates.'templates/header.html',$this->header);
		$this->load->view($this->templates.'newsdetail.html',$data);
		$this->load->view($this->templates.'templates/footer.html');
		
	}
}
?>