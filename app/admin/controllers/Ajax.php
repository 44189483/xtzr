<?php
/*
* Ajax INPUT/CHECKBOX 无刷新
* @package	Ajax
* @author	Sun Guo Liang
* @since	Version 1.0.0
* @filesource
*/

class Ajax extends CI_Controller{

	public function __construct(){

		parent::__construct();

		$this->load->database();

		$this->id = $this->input->post('id');//ID值

		$this->filedId = $this->input->post('filedId');//字段ID

		$this->filed = $this->input->post('filed');//字段

		$this->value = $this->input->post('value');//值
		
		$this->table = $this->db->dbprefix($this->input->post('table'));//表

	}

	//删除
	public function del(){

		$query = $this->db->query("SELECT {$this->filed} FROM {$this->table} WHERE {$this->filedId}={$this->id}");
	    $row = $query->row_array();
		
		//删除文件
		@unlink($row[$this->filed]);

		$bool = $this->db->query("UPDATE {$this->table} SET {$this->filed}='' WHERE {$this->filedId}={$this->id}");

		if($bool){
			echo '1';
		}else{
			echo '0';
		}

	}

	public function input(){

		$bool = $this->db->query("UPDATE {$this->table} SET {$this->filed}='{$this->value}' WHERE {$this->filedId}={$this->id}");

		if($bool){
			echo '1';
		}else{
			echo '0';
		}

	}

	public function checkbox(){

		$query = $this->db->query("SELECT {$this->filed} FROM {$this->table} WHERE {$this->filedId}={$this->id}");
	    $row = $query->row_array();

	    $val = $row[$this->filed] == 0 ? 1 : 0;

		$this->db->query("UPDATE {$this->table} SET {$this->filed}='{$val}' WHERE {$this->filedId}={$this->id}");

	}


}
?>