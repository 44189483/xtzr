<?php
/*
* User 会员中心相关
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
		$this->load->helper('func_helper');
		$this->templates = isMobile() == true ? 'mobile/' : 'pc/';
		$this->table = $this->db->dbprefix('member');

		if(empty($this->session->member->user)){
			redirect(site_url('/member/login'));
		}

		$query = $this->db->query("SELECT * FROM {$this->table} WHERE mobile='{$this->session->member->user}'");
    	$this->member = $query->row();

    	$this->header = array(
			'cname' => __CLASS__,
			'member' => $this->session->member
		);	

	}

	public function index(){

		$this->header += array('nav' => '个人中心','fname' => __FUNCTION__);

		$this->load->view($this->templates.'templates/header.html',$this->header);
		$this->load->view($this->templates.'user/myinfo.html',$this->member);
		$this->load->view($this->templates.'templates/footer.html');

	}

	public function mysafe(){

		$this->header += array('nav' => '安全设置','fname' => __FUNCTION__);

		//登录时间
		$query = $this->db->query("SELECT logTime FROM {$this->db->dbprefix('member_login')} WHERE mobile='{$this->session->member->user}'");
    	$row = $query->row();

    	if($row){
    		$this->member->logTime = $row->logTime;
    	}

    	/*
		//安全等级

    	$i = 1;

    	if(!empty($row->email)){
    		$i += 1;
    	}

    	if(!empty($row->email)){
    		$i += 1;
    	}

    	$this->member->level = / 3 * 100;

    	*/

    	$this->member->level = 70;

		$this->load->view($this->templates.'templates/header.html',$this->header);
		$this->load->view($this->templates.'user/mysafe.html',$this->member);
		$this->load->view($this->templates.'templates/footer.html');

	}

	public function mylend(){

		$this->header += array('nav' => '个人出借','fname' => __FUNCTION__);

		$table = $this->db->dbprefix('fund');

		//累计收益
		$query_total_interest = $this->db->query("SELECT SUM(interest) AS totalInterest FROM {$table} WHERE mid='{$this->member->id}' AND status=1");
    	$row_total_interest = $query_total_interest->row();

    	$this->member->total_interest = $row_total_interest->totalInterest == null ? 0 : $row_total_interest->totalInterest;

    	//待收本金
    	$query_total_principal = $this->db->query("SELECT SUM(principal) AS totalPrincipal FROM {$table} WHERE mid='{$this->member->id}' AND status=0");
    	$row_total_principal = $query_total_principal->row();

    	$this->member->total_principal = $row_total_principal->totalPrincipal == null ? 0 : $row_total_principal->totalPrincipal;

    	//待收利息
    	$query_interest = $this->db->query("SELECT SUM(interest) AS interest FROM {$table} WHERE mid='{$this->member->id}' AND status=0");
    	$row_interest = $query_interest->row();

    	$this->member->interest = $row_interest->interest == null ? 0 : $row_interest->interest;

		$sql = "
	    	SELECT 
	    		f.principal,
	    		f.interest,
	    		p.projectId,
	    		p.projectName,
	    		p.yearRate,
	    		p.timeLimit,
	    		c.className
	    	FROM 
	    		{$this->db->dbprefix('fund')} f
	    	INNER JOIN
	    		{$this->db->dbprefix('project')} p 
	    	ON
	    		f.pid=p.projectId
	    	INNER JOIN
	    		{$this->db->dbprefix('class')} c
	    	ON
	    		p.status=c.classId
	    	WHERE
	    		f.mid={$this->member->id}
	    	ORDER BY f.id DESC
	    ";

	    $query = $this->db->query($sql);
	    $this->member->all = $query->result();

	    $this->member->lend = new stdclass();
	    $this->member->repayment = new stdclass();
		$this->member->hadrepayment = new stdclass();

	    foreach ($this->member->all as $key => $val) {
	    	if($val->className == '出借中'){
	    		$this->member->lend->$key = $val;
	    	}
	    	if($val->className == '还款中'){
	    		$this->member->repayment->$key = $val;
	    	}
	    	if($val->className == '已还款'){
	    		$this->member->hadrepayment->$key = $val;
	    	}
	    }

		$this->load->view($this->templates.'templates/header.html',$this->header);
		$this->load->view($this->templates.'user/mylend.html',$this->member);
		$this->load->view($this->templates.'templates/footer.html');

	}

	public function myproperty(){

		$this->header += array('nav' => '个人资产','fname' => __FUNCTION__);

		$table = $this->db->dbprefix('fund');

		//累计收益
		$query_total_interest = $this->db->query("SELECT SUM(interest) AS totalInterest FROM {$table} WHERE mid='{$this->member->id}' AND status=1");
    	$row_total_interest = $query_total_interest->row();

    	$this->member->total_interest = $row_total_interest->totalInterest == null ? 0 : $row_total_interest->totalInterest;

    	//待收本金
    	$query_total_principal = $this->db->query("SELECT SUM(principal) AS totalPrincipal FROM {$table} WHERE mid='{$this->member->id}' AND status=0");
    	$row_total_principal = $query_total_principal->row();

    	$this->member->total_principal = $row_total_principal->totalPrincipal == null ? 0 : $row_total_principal->totalPrincipal;

    	//待收利息
    	$query_interest = $this->db->query("SELECT SUM(interest) AS interest FROM {$table} WHERE mid='{$this->member->id}' AND status=0");
    	$row_interest = $query_interest->row();

    	$this->member->interest = $row_interest->interest == null ? 0 : $row_interest->interest;

		$this->load->view($this->templates.'templates/header.html',$this->header);
		$this->load->view($this->templates.'user/myproperty.html',$this->member);
		$this->load->view($this->templates.'templates/footer.html');

	}

	//提交
	public function save(){

		$formtype = $this->input->post('formtype');

		switch ($formtype) {

			//个人详情
			case 'myinfo':

				$realname = $this->input->post('realname');

				$sex = $this->input->post('sex');

				$marital = $this->input->post('marital');

				$education = $this->input->post('education');

				$job = $this->input->post('job');

				$province = $this->input->post('province');

				$city = $this->input->post('city');

				$data = array(
				    'realname' => $realname,
				    'sex' => $sex,
				    'marital' => $marital,
				    'education' => $education,
				    'job' => $job,
				    'province' => $province,
				    'city' => $city
				);

				//文件存在判断
				if(!empty($_FILES["attchment"]["name"]) && is_uploaded_file($_FILES["attchment"]["tmp_name"])){

					//删除旧图
					@unlink($_SERVER['DOCUMENT_ROOT'].'/'.$this->member->avatar);

					//重命名
					$attchname = $_FILES["attchment"]["name"];
					$ext = substr($attchname,strripos($attchname,'.') + 1);
					$name = $this->session->member->user.'.'.$ext;
					$config['file_name'] = $name;

					$config['upload_path'] = 'upload/avatar/';
					$config['allowed_types'] = 'gif|jpg|png|jpeg';
					$this->load->library('upload', $config);
					$this->upload->initialize($config);
					$this->upload->do_upload('attchment');
					$info = $this->upload->data();
				    $data['avatar'] = $config['upload_path'].$name;

				}

				$this->db->where('mobile', $this->session->member->user);
				$bool = $this->db->update($this->table, $data);

				redirect(site_url('/user'));
				
				break;

			//安全设置
			case 'mysafe':

				$pwd = password_hash($this->input->post('pwd'), PASSWORD_BCRYPT);

				$data = array(
				    'pwd' => $pwd
				);

				$this->db->where('mobile', $this->session->member->user);
				$bool = $this->db->update($this->table, $data);

				if($bool){
					echo '密码设置成功.';
				}else{
					echo '密码设置失败.';
				}

				break;
		}

	}

	public function ajax_safe(){

		$filed = $this->input->post('filed');

		$value = $this->input->post('value');

		//防止重复
		$query = $this->db->query("SELECT * FROM {$this->table} WHERE mobile<>'{$this->session->member->user}' AND {$filed}='{$value}'"); 
    	$num = $query->num_rows();

     	if($num > 0){
    		echo -1;
    		exit();
    	}

		$this->db->query("UPDATE $this->table SET {$filed}='{$value}' WHERE mobile='{$this->session->member->user}'");

	}

	//退出
	public function logout(){
    	//退出 清除session
		$this->session->member->user = '';
		session_destroy();
		redirect(site_url('/member/login'));
    }

}
?>