<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Api extends CI_Controller
{

	private $result_controller = false;
	private $result_model = false;
	private $result_view = false;

	private $directory_controller = '/application/controllers/';
	private $directory_model = '/application/models/';

	public function index()
	{
		$this->load->view('home');
	}

	public function loadTables()
	{
		$this->load->model("M_database");

		$data = json_decode(file_get_contents('php://input'), true);
		$database_name =  $data['database_name'];

		validate($database_name);

		$result = $this->M_database->getTables($database_name);

		echo json_encode($result);
	}

	public function loadDatabases()
	{
		$this->load->model("M_database");

		$data = $this->M_database->getDatabases();

		echo json_encode($data);
	}

	public function loadFields()
	{
		$this->load->model("M_database");

		$data = json_decode(file_get_contents('php://input'), true);
		$database_name =  $data['database_name'];
		$table =  $data['table'];

		validate($database_name, $table);

		$result['fields'] = $this->M_database->getFields($database_name, $table);
		$result['primaryKey'] = $this->M_database->getPrimaryKey($database_name, $table);

		echo json_encode($result);
	}

	public function generateControllerModel()
	{
		$data = json_decode(file_get_contents('php://input'), true);

		$directory_project =  $data['directory_project'];
		$controller_name =  $data['controller_name'];
		$model_name =  $data['model_name'];
		$database =  $data['database'];
		$table =  $data['table'];
		$fields =  $data['fields'];
		$primaryKey =  $data['primaryKey'];
		$select_generate =  $data['select_generate'];

		validate(
			$directory_project,
			$controller_name,
			$model_name,
			$table,
			$fields,
			$database,
			$primaryKey,
			$select_generate
		);

		$is_dir = is_dir($directory_project);

		if (! $is_dir){
			exit(json_encode(array('result' => false, 'message'=>'Directory is invalid')));
		}

		if ($select_generate == 1) 
		{
			$this->generateController($directory_project,$controller_name,$fields,$model_name,$primaryKey);
		} else if ($select_generate == 2) 
		{
			$this->generateModel($directory_project,$model_name,$fields,$table,$primaryKey);
		} else if ($select_generate == 3) 
		{
			$this->generateController($directory_project,$controller_name,$fields,$model_name,$primaryKey);
			$this->generateModel($directory_project,$model_name,$fields,$table,$primaryKey);
		}
	}

	private function generateModel($directory_project,$model_name,$fields,$table,$primaryKey){
		

		$file_model = $this->checkFile(
			$directory_project . $this->directory_model . $model_name . '.php'
		);

		if (!$file_model) {
			// create model file
			// write model template
			$this->createAndWriteFile(
				$directory_project . $this->directory_model . $model_name,
				modelTemplate($model_name, $table, $primaryKey, $fields)
			);
			$this->result_model = true;
		}
		$data['result_model'] = $this->result_model;
		echo json_encode($data);
	}

	private function createView($directory_project,$filename){
		$final_path= $directory_project .'/application/views/'. $filename;

		$file_view = $this->checkFile(
			$final_path.'.php'
		);

		if (!$file_view) {
			// create view file
			// write view template
			$this->createAndWriteFile(
				$final_path,
				viewTemplate($filename)
			);

			$this->result_view = true;
		}
		$data['result_view'] = $this->result_view;
		echo json_encode($data);
	}

	private function generateController($directory_project,$controller_name,$fields,$model_name,$primaryKey)
	{

		$file_controller = $this->checkFile(
			$directory_project . $this->directory_controller . $controller_name . '.php'
		);

		if (!$file_controller) {
			// create controller file
			// write controller template
			$this->createAndWriteFile(
				$directory_project . $this->directory_controller . $controller_name,
				controllerTemplate($controller_name, $model_name, $fields, $primaryKey)
			);

			$this->result_controller = true;
		}
		$data['result_controller'] = $this->result_controller;
		echo json_encode($data);
	}

	private function createAndWriteFile($file, $data)
	{
		$file = $file . ".php";
		$file = str_replace('\\\\', '', $file);

		
		if (!$this->checkFile($file)) {
			$theFile = fopen($file, "w");
			fwrite($theFile, $data);
		} else {
			exit(json_encode(array('result' => false, 'File ' . $file . ' already exist')));
		}
	}

	private function checkFile($value)
	{
		$value = str_replace('\\\\', '', $value);
		return (file_exists($value)) ? true : false;
	}

	public function generateView()
	{
		$data = json_decode(file_get_contents('php://input'), true);

		$directory_project =  $data['directory_project'];
		$filename =  $data['filename'];

		validate(
			$directory_project,
			$filename
		);

		$is_dir = is_dir($directory_project);

		if (! $is_dir){
			exit(json_encode(array('result' => false, 'message'=>'Directory is invalid')));
		}

		$this->createView($directory_project,$filename);
		
	}

}
