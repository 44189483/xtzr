<?php
class Project_model extends CI_Model{
	public function __construct(){
		$this->load->database();
	}
	//获取项目名称
	public function get_project($slug){
	    $query = $this->db->query("SELECT projectName,yearRate FROM {$this->db->dbprefix('project')} WHERE projectId={$slug}");
	    return $query->row();
	}
}
?>