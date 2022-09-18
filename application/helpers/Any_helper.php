<?php 


function validate(... $value){
	if ($value===''||$value==null){
		exit(json_encode(array('result'=>false,'message'=>'input is required')));
	}
}

function controllerTemplate($controller_name,$model_name,$fields,$primaryKey){

	$add_tmp = '';

	foreach ($fields as $key => $value) {
		$field = $value['COLUMN_NAME'];

		if ($field===$primaryKey){

		}else{
			$add_tmp .= '$'.$field.' = $this->input->post("'.$field.'");';
		}
	}

	$add_tmp .= ' ';

	foreach ($fields as $key => $value) {
		$field = $value['COLUMN_NAME'];

		if ($field===$primaryKey){

		}else{
			$add_tmp .= '$this->'.$model_name.'->'.$field.' = $'.$field.';';
		}
	}


	$update_tmp = '';

	foreach ($fields as $key => $value) {
		$field = $value['COLUMN_NAME'];

		$update_tmp .= '$'.$field.' = $this->input->post("'.$field.'");';
	}

	$update_tmp .= ' ';

	foreach ($fields as $key => $value) {
		$field = $value['COLUMN_NAME'];

		$update_tmp .= '$this->'.$model_name.'->'.$field.' = $'.$field.';';
	}

	return '
<?php
defined("BASEPATH") OR exit("No direct script access allowed");

class '.$controller_name.' extends CI_Controller 
{
	public function __construct()
	{
		parent::__construct();
		$this->load->model("'.$model_name.'");
	}

	public function index(){	
		$this->load->view("'.$controller_name.'");
	}

	public function show(){
		$data = $this->'.$model_name.'->loadData();
		echo json_encode($data);
	}

	public function add(){

		'.$add_tmp.'

		$result = $this->'.$model_name.'->add();

		if ($result){
			echo json_encode(array("result"=>true,"message"=>"Success"));
		}else{
			echo json_encode(array("result"=>false,"message"=>"Failed"));
		}
	}

	public function update(){
		'.$update_tmp.'

		$result = $this->'.$model_name.'->update();

		if ($result){
			echo json_encode(array("result"=>true,"message"=>"Success"));
		}else{
			echo json_encode(array("result"=>false,"message"=>"Failed"));
		}
	}

	public function delete(){
		$'.$primaryKey.' = $this->input->post("'.$primaryKey.'");

		$this->'.$model_name.'->'.$primaryKey.' = $'.$primaryKey.';

		$result = $this->'.$model_name.'->delete();

		if ($result){
			echo json_encode(array("result"=>true,"message"=>"Success"));
		}else{
			echo json_encode(array("result"=>false,"message"=>"Failed"));
		}
	}

}
';
}

function modelTemplate($model_name,$table,$primaryKey,$fields){

	$fields_tmp = '';
	$add_update_tmp = '';

	$field_1 = '';
	
	foreach ($fields as $key => $value) {
		$column = $value['COLUMN_NAME'];

		$fields_tmp .= 'public $'.$column.';';

		if ($key==1){
			$field_1 = $column;
		}
	}

	foreach ($fields as $key => $value) {
		$column = $value['COLUMN_NAME'];
		if ($column===$primaryKey){

		}else{
			$add_update_tmp .= '"'.$column.'" => $this->'.$column.',';
		}
	}
	
	return '
<?php
defined("BASEPATH") or exit("No direct script access allowed");

class '.$model_name.' extends CI_Model
{
	// Definisi field/colomn tabel
	'.$fields_tmp.'
	//

	// Definisi nama tabel
	protected $table      = "'.$table.'";

	protected $primaryKey ="'.$primaryKey.'";
	protected $useAutoIncrement = true;

	protected $useTimestamps = false;
	protected $createdField  = "created_at";
	protected $updatedField  = "updated_at";
	protected $deletedField  = "deleted_at";

	public function loadData()
	{
		$this->db->select("*")
				->from($this->table);
		$obj = $this->db->get();
		return $obj->result_array();
	}

	public function loadDataById()
	{
		$this->db->select("*")
				->from($this->table)
				->where([$this->primaryKey => $this->'.$primaryKey.']);

		$obj = $this->db->get();
		$data  = $obj->result_array();
		return (count($data)) > 0 ? $data[0] : null;
	}

	public function countData(){
		$this->db->select($this->primaryKey)
				->from($this->table);
		$obj = $this->db->get();
		$data =$obj->result_array();
		return count($data);
	}

	public function checkDataById()
	{
		$this->db->select($this->primaryKey)
			->from($this->table)
			->where([$this->primaryKey => $this->'.$primaryKey.']);

		$obj = $this->db->get();
		$data  = $obj->result_array();
		return count($data) > 0 ? true : false;
	}

	public function searchWhere()
	{
		$this->db->select("*")
			->from($this->table)
			->where(["'.$field_1.'" => $this->'.$field_1.']);

		$obj = $this->db->get();
		$data  = $obj->result_array();

		return (count($data) > 0) ? $data[0] : null;
	}

	public function searchLike()
	{
		$this->db->select("*")
			->from($this->table)
			->like("'.$field_1.'", $this->'.$field_1.');

		$obj = $this->db->get();
		$data  = $obj->result_array();

		return (count($data) > 0) ? $data[0] : false;
	}

	public function add()
	{
		$data = array(
			'.$add_update_tmp.'
		);
		return $this->db->insert($this->table, $data);
	}

	public function update()
	{
		$data = array(
			'.$add_update_tmp.'
		);
		$this->db->where($this->primaryKey, $this->'.$primaryKey.');
		return $this->db->update($this->table, $data);
	}

	public function delete()
	{
		return $this->db->delete($this->table, array($this->primaryKey => $this->'.$primaryKey.'));
	}
}
';
}
