<?php
/*
* News 新闻
* @package	News
* @author	Sun Guo Liang
* @since	Version 1.0.0
* @filesource
*/

class News extends CI_Controller{

	public function __construct(){

		parent::__construct();
		$this->load->library('session');
		$this->load->helper('url_helper');
		$this->load->database();

		if(empty($this->session->admin)){
			//show_error('<a href="'.base_url().'index/login">请登录!</a>',null,'错误提示');
			jump('',site_url('index/login'));
		}

		//获取分类
		$query = $this->db->query("SELECT * FROM {$this->db->dbprefix}class WHERE classType=1");
	    $this->classes = $query->result();
	    if(!$this->classes){
	    	show_error('<a href="'.site_url("category/index/1").'">暂无新闻分类,点击添加!</a>',null,'友情提示');
	    }

		$this->head_data['cname'] = __CLASS__;
		$this->table = $this->db->dbprefix('article');
		$this->load->view('templates/header.html',$this->head_data);
		$this->load->view('templates/menu.html',$this->head_data);

	}
	public function index(){

		//获取所有新闻分类
		$q = $this->db->query("SELECT classId,className FROM {$this->db->dbprefix('class')} WHERE classType=1");
	    $class = $q->result();
		
		$title = trim($this->input->get('title'));

		$where = "WHERE n.articleType=1";

		if(!empty($title)){
			$where .= " AND n.articleTitle LIKE '%{$title}%'";
		}

		$cid = trim($this->input->get('cid'));

		if(!empty($cid)){
			$where .= " AND n.classId={$cid}";
		}

	    $config = array();
	    $config['per_page'] = 10; //每页显示的数据数
	    $current_page = intval($this->input->get('per_page')); //获取当前分页页码数
	    //page还原
	    if(0 == $current_page){
	      	$current_page = 1;
	    }
	    $offset = ($current_page - 1 ) * $config['per_page']; //设置偏移量 限定 数据查询 起始位置（从 $offset 条开始）

	    $query = $this->db->query("SELECT * FROM {$this->table} n {$where}");
	    $result['total'] = $query->num_rows();

	    $sql = "
	    	SELECT 
	    		c.className,
	    		n.* 
	    	FROM 
	    		{$this->table} n
	    	INNER JOIN
	    		{$this->db->dbprefix('class')} c
	    	ON
	    		n.classId=c.classId
	    	{$where}
	    	 ORDER BY n.articleId DESC LIMIT {$offset},{$config['per_page']}
	    ";

	    $query = $this->db->query($sql);
	    $result['list'] = $query->result();

	    $config['base_url']   = site_url('news/index');//'admin.php/news/index?';
	    
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
	    	'cid' => $cid,
	    	'class' => $class,
	    	'title' => $title,
	        'rows' => $result['list'],
	        'total'  => $result['total'],
	        'current_page' => $current_page,
	        'per_page' => $config['per_page'],
	        'page'  => $this->pagination->create_links(),
	    );
		$this->load->view('news/index.html',$data);
	}

	public function add(){
		$data['classes'] = $this->classes; 
		$this->load->view('news/add.html',$data);
	}

	public function edit($id){

		$data = array();

		if(!empty($id)){
			$query = $this->db->query("SELECT * FROM {$this->table} WHERE articleId={$id}");
	    	$data = $query->row();
		}

		$data->classes = $this->classes;
		
		$this->load->view('news/edit.html',$data);

	}

	public function save(){

		$id = $this->input->post('id');

		$cid = $this->input->post('cid');//分类

		$title = $this->input->post('title');

		$content = $this->input->post('content');

		$data = array(
		    'articleTitle' => $title,
		    'articleContent' => $content,
		    'articleType' => 1,
		    'classId' => $cid,
		    'createTime' => date('Y-m-d H:i:s')
		);

		if(empty($id)){
			$query = $this->db->query("SELECT * FROM {$this->table} WHERE articleType=1 AND articleTitle='{$title}'");
	    	$row = $query->row(); 
			if($row){
				jump('该设备已存在',site_url('news/add'));
			}
			$bool = $this->db->insert($this->table, $data);
		}else{
			$this->db->where('articleId', $id);
			$bool = $this->db->update($this->table, $data);
		}
		
		if($bool) {
            jump('操作成功',site_url('news/index'));
        }else{
            jump('操作失败');
        }

	}

	public function del($id = null){

		$pid = $this->input->post('id');

		$gid = $id;

		$bool = false;

		if($pid){//多删

			$query = $this->db->query("SELECT * FROM {$this->table} WHERE articleId IN(".implode(',', $pid).")");
	    	$rows = $query->result();

			foreach ($rows as $k => $v) {
				@unlink($v->articleAttach);
			}

			$bool = $this->db->query("DELETE FROM {$this->table} WHERE articleId IN(".implode(',', $pid).")"); 

		}else if (!empty($gid)) {//单删

			$query = $this->db->query("SELECT * FROM {$this->table} WHERE articleId={$gid}");
	    	$row = $query->row();
			//删除图片
			@unlink($row->articleAttach);

			$bool = $this->db->delete($this->table, array('articleId' => $gid));

		}
		
		if($bool) {
            jump('操作成功',site_url('news/index'));
        }else{
            jump('操作失败');
        }

	}

}
?>