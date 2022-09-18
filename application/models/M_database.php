<?php
defined('BASEPATH') or exit('No direct script access allowed');

class M_database extends CI_Model
{

	public function getTables($databaseName){
		$this->db->trans_start();
		$this->db->query("use ".$databaseName."");
		$data= $this->db->query("show tables");
		
		$result = $data->result();
		$this->db->trans_complete();

		return $result;
	}

	public function getPrimaryKey($databaseName,$tableName){
		
		$this->db->trans_start();
		$this->db->query("use ".$databaseName."");
		$data= $this->db->query("SHOW KEYS FROM ".$tableName." WHERE Key_name = 'PRIMARY'");
		
		$result = $data->result();
		$this->db->trans_complete();

		return $result;
	}

	public function getDatabases(){
		$data= $this->db->query('show databases');
		return $data->result_array();
	}

	public function getFields($databaseName,$table){
		$data= $this->db->query($this->queryGetFields($databaseName,$table));
		return $data->result_array();
	}

	private function queryGetFields($databaseName,$table){
		return 'select `COLUMN_NAME` from `INFORMATION_SCHEMA`.`COLUMNS` 
		WHERE `TABLE_SCHEMA`="'.$databaseName.'" 
			AND `TABLE_NAME`="'.$table.'";';
	}


}
