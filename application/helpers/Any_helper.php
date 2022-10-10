<?php 


function validate(... $value){
	if ($value===''||$value==null){
		exit(json_encode(array('result'=>false,'message'=>'input is required')));
	}
}

function viewTemplate($filename){
	return '
	<?php
	defined("BASEPATH") or exit("No direct script access allowed");
	?>
	<!DOCTYPE html>
	<html lang="en">
	<head>
		<meta charset="UTF-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<title>'.$filename.'</title>
		<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-Zenh87qX5JnK2Jl0vWa8Ck2rdkQ2Bzep5IDxbcnCeuOxjzrPF/et3URy9Bv1WTRi" crossorigin="anonymous">
		<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-OERcA2EqjJCMA+/3y+gxIOqMEjwtxJY7qPCqsdltbNJuaOe923+mo//f6V8Qbsw3" crossorigin="anonymous"></script>
		<script src="https://cdn.jsdelivr.net/npm/vue@2.7.10/dist/vue.js"></script>
		<script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
		<link rel="icon" type="image/x-icon" href="<?= base_url() ?>/public/img/favicon.ico">
		<link href="//netdna.bootstrapcdn.com/font-awesome/3.2.1/css/font-awesome.css" rel="stylesheet">
		<style>
			[v-cloak] {
				display: none;
			}
		</style>
	</head>
	<body>

		<div id="app" class="container">
		</div>

		<script>
		const $server = "<?= base_url() ?>";
		const _TOKEN_ = "";

		const API_LOAD_DATA   = $server +"api/";
		const API_ADD_DATA    = $server +"api/";
		const API_UPDATE_DATA = $server +"api/";
		const API_DELETE_DATA = $server +"api/";
	
		new Vue({
			el :"#app",
			data:{
				
			},
			methods: {
				save : function(){
					if (this.name == null || this.name === "") {
						this.$refs.name.focus();
						return;
					}
				},
				update: function(){
	
				},
				delete : function(){
	
				},
				loadData : function(){
					axios({
						method: "post",
						url: API_LOAD_DATA,
						data: {
							TOKEN : _TOKEN_
						},
						headers: {
							"X-Requested-With": "XMLHttpRequest",
							"Content-Type": "application/json"
						}
					}).then(function (response) {
				
						if (response.status == 200) {
	
							var final_data = response.data;
	
						}
					});
				}
			},
			mounted() {
				this.loadData()
			},
		})
		</script>
		
	</body>
	</html>
	';
}

function controllerTemplate($controller_name,$model_name,$fields,$primaryKey){

	$add_tmp = '';
	$field_1 = '';
	foreach ($fields as $key => $value) {
		$field = $value['COLUMN_NAME'];

		if ($field===$primaryKey){

		}else{
			$add_tmp .= '$'.$field.' = $this->input->post("'.$field.'");';
		}
		if ($key==1){
			$field_1 = $field;
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

	$search_tmp = '$this->'.$model_name.'->'.$field_1.'= $this->input->post("search");';

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

	public function search(){
		'.$search_tmp.'

		$result = $this->'.$model_name.'->search();

		if ($result){
			echo json_encode(array("result"=>true,"data"=>$result));
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

		return (count($data) > 0) ? $data : null;
	}

	public function searchLike()
	{
		$this->db->select("*")
			->from($this->table)
			->like("'.$field_1.'", $this->'.$field_1.');

		$obj = $this->db->get();
		$data  = $obj->result_array();

		return (count($data) > 0) ? $data : false;
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
