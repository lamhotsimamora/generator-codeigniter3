<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Template extends CI_Controller {


	public function controller(){
		$fields = array(
			[
				"COLUMN_NAME"=>'Column1',
				"COLUMN_NAME"=>'Column2'
			]
		);
		$tmp= controllerTemplate('Example','ExampleModel',$fields,'id_example');
		
		echo htmlspecialchars($tmp);
	}

	public function model(){
		$fields = array(['COLUMN_NAME'=>'example','COLUMN_NAME'=>'time_created']);
		$tmp= modelTemplate('ExampleModel','Example','id_example',$fields);
		
		echo htmlspecialchars($tmp);
	}

}
