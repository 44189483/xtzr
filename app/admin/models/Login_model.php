<?php
class Login_model extends CI_Model{
	public function __construct(){
		$this->load->database();
	}
	public function get_login(){
		$username = $this->input->post('username');
		$userpwd = $this->input->post('password');
		$query = $this->db->query("SELECT * FROM {$this->db->dbprefix('option')} WHERE optionType='AdminContrl' AND optionKey='{$username}'");
		$row = $query->row();
		return password_verify($userpwd, $row->optionValue);
	}
}
?>