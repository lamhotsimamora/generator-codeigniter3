<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Api extends CI_Controller {

	private $directory_controller = '/application/controllers/';
	private $directory_model = '/application/models/';

	public function index(){
		$this->load->view('home');
	}

	public function loadTables(){
		$this->load->model("M_database");
		
		$data = json_decode(file_get_contents('php://input'),true); 
		$database_name =  $data['database_name'];

		validate($database_name);

		$result = $this->M_database->getTables($database_name);

		echo json_encode($result);
	}

	public function loadDatabases(){
		$this->load->model("M_database");

		$data = $this->M_database->getDatabases();

		echo json_encode($data);
	}

	public function loadFields(){
		$this->load->model("M_database");

		$data = json_decode(file_get_contents('php://input'),true); 
		$database_name =  $data['database_name'];
		$table =  $data['table'];

		validate($database_name,$table);

		$result['fields'] = $this->M_database->getFields($database_name,$table);
		$result['primaryKey'] = $this->M_database->getPrimaryKey($database_name,$table);

		echo json_encode($result);
	}

	public function generateControllerModel()
	{
		$data = json_decode(file_get_contents('php://input'),true); 
		$directory_project =  $data['directory_project'];
		$controller_name =  $data['controller_name'];
		$model_name =  $data['model_name'];
		$database =  $data['database'];
		$table =  $data['table'];
		$fields =  $data['fields'];
		$primaryKey =  $data['primaryKey'];
		
		
		validate($directory_project,$controller_name,$model_name,$table
				,$fields,$database,$primaryKey);

		$response['result_controller'] = false;
		$response['result_model'] = false;
		$response['message_controller'] = null;
		$response['message_model'] = null;

		$file_controller = $this->checkFile(
							$directory_project.$this->directory_controller.$controller_name.'.php');

		if (!$file_controller){
			// create controller file
			// write controller template
			$this->createAndWriteFile($directory_project.$this->directory_controller.$controller_name,
										controllerTemplate($controller_name,$model_name,$fields,$primaryKey));
			
			$response['result_controller'] = true;
			$response['message_controller'] = 'Controller file has been generated !';
		}else{
			$response['result_controller'] = false;
			$response['message_controller'] = 'Controller file already exist !';
		}

		$file_model = $this->checkFile(
						$directory_project.$this->directory_model.$model_name.'.php');

		if (!$file_model){
			// create model file
			// write model template
			$this->createAndWriteFile($directory_project.$this->directory_model.$model_name,
										modelTemplate($model_name,$table,$primaryKey,$fields));
			$response['result_model'] = true;
			$response['message_model'] = 'Model file has been generated !';
		}else{
			$response['result_model'] = false;
			$response['message_model'] = 'Model file already exist !';
		}

		echo json_encode($response);
			
	}

	private function createAndWriteFile($file,$data){
		$file= $file.".php";
		$file = str_replace('\\\\','',$file);

		if (!$this->checkFile($file)){	
			$theFile= fopen($file, "w"); 
			fwrite($theFile, $data);
		}else{
			exit(json_encode(array('result'=>false,'File '.$file.' already exist')));
		}
	}

	private function checkFile($value){
		$value = str_replace('\\\\','',$value);
		return (file_exists($value)) ? true : false;
	}

	
}
