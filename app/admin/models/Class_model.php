<?php
class Class_model extends CI_Model{
	public function __construct(){
		$this->load->database();
	}
	//获取分类
	public function get_class($slug){
		$query = $this->db->query("SELECT * FROM {$this->db->dbprefix}class WHERE classType={$slug}");
	    return $query->result();
	}
}
?>