<?php
defined('BASEPATH') or exit('No direct script access allowed');
?>

<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="UTF-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">

	<link rel="icon" type="image/x-icon" href="<?= base_url('') ?>/favicon.ico">
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-iYQeCzEYFbKjA/T2uDLTpkwGzCiq6soy8tYaI1GyVh/UjpbCx/TYkiZhlZB6+fzT" crossorigin="anonymous">
	<script src="https://cdn.jsdelivr.net/npm/vue@2/dist/vue.js"></script>
	<title>Generator Controller & Model Codeigniter 3</title>
	<script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
	<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
	<script src="public/js/vony.js"></script>
	<style>
		[v-cloak] {
			display: none;
		}
	</style>
</head>

<body>

	<br>


	<div class="container" id="app" v-cloak>

		<div class="card">
			<div class="card-body">
				<span class="badge text-bg-primary">Codeigniter 3</span>

				<span class="badge text-bg-info">

					Generate Controller & Model Only One Click</span>
				<hr>

				<div class="mb-3">
					<label for="directory_project" class="form-label">
						<strong>Directory Project</strong>
					</label>
					<input type="text" @keypress="enterGenerate" v-model="directory_project" ref="directory_project" class="form-control" 
					id="directory_project" placeholder="C:\xampp\htdocs\myproject">
				</div>

				<div class="mb-3">
					<label for="exampleFormControlInput2" class="form-label">
						<strong>Controller Filename</strong>
					</label>
					<input type="text" @keypress="enterGenerate" @keyup="autoModelName" v-model="controller_name" 
					ref="controller_name" class="form-control" id="exampleFormControlInput2" placeholder="Users">
				</div>

				<div class="mb-3">
					<label for="exampleFormControlInput3" class="form-label">
						<strong>Model Filename</strong>
					</label>
					<input type="text" @keypress="enterGenerate" v-model="model_name" ref="model_name" 
					class="form-control" id="exampleFormControlInput3" placeholder="UserModel">
				</div>

				<div class="form-floating">
					<select v-model="database" @change="loadTable" class="form-select" id="floatingSelect" aria-label="">
						<option v-for="data in data_databases" :value="data.Database">{{ data.Database }}</option>
					</select>
					<label for="floatingSelect">Select Database</label>
				</div> <br>

				<div v-if="loading" class="d-flex justify-content-center">
					<div class="spinner-border" role="status">
						<span class="visually-hidden"></span>
					</div>
				</div>

				<div class="form-floating">
					<select v-model="table" class="form-select" @change="loadField" id="floatingSelect1" aria-label="">
						<option v-for="data in data_tables" :value="data.table" selected>{{ data.table }}</option>
					</select>
					<label for="floatingSelect">Select Table</label>
				</div>
				<br>
				<div v-if="loading2" class="d-flex justify-content-center">
					<div class="spinner-border" role="status">
						<span class="visually-hidden"></span>
					</div>
				</div>



				<ul class="list-group">
					<h6>
						<strong>Fields</strong>
					</h6>
					<li v-for="data in data_fields" v-html="renderField(data.COLUMN_NAME)" :class="getClassField(data.COLUMN_NAME)">

					</li>
				</ul>
				<hr>
				<div class="form-check">
					<input class="form-check-input" value="1" v-model="select_generate" type="radio" name="flexRadioDefault">
					<label class="form-check-label" for="flexRadioDefault1">
						Only Controller
					</label>
				</div>
				<div class="form-check">
					<input class="form-check-input" value="2" v-model="select_generate" type="radio" name="flexRadioDefault">
					<label class="form-check-label" for="flexRadioDefault2">
						Only Model
					</label>
				</div>
				<div class="form-check">
					<input class="form-check-input" value="3" v-model="select_generate" checked type="radio" name="flexRadioDefault">
					<label class="form-check-label" for="flexRadioDefault2">
						Controller & Model
					</label>
				</div>
				<hr>
				<button type="button" @click="generate" id="btn_generate" class="btn btn-primary">Generate</button>
				<button type="button" @click="save" class="btn btn-success">Save</button>
				<button type="button" @click="reset" class="btn btn-secondary">Reset</button>
				<hr>
				<a href="#" data-bs-toggle="modal" data-bs-target="#myModal">Read Me</a> |
				<a href="<?= base_url() ?>template/controller" target="_blank">Controller Template</a> |
				<a href="<?= base_url() ?>template/model" target="_blank">Model Template</a>

			</div>
		</div>

	</div>

	<br>

	<div class="container">
		<div class="card">
			<small>
				<strong>
					Made with Love by <a href="https://github.com/lamhotsimamora" target="_blank">LamhotSimamora</a> @2022
				</strong>
			</small>
		</div>
	</div>
	<br><br>

	<div class="modal" id="myModal" tabindex="-1">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title">Generator Codeigniter 3</h5>
					<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
				</div>
				<div class="modal-body">
					<p>
						You can generate controller file and model file automatically which is
						inside model file functions has been generate <br>
					</p>
					<hr>
					<p>
						Developer : <i style="color:chocolate"> Lamhot Simamora </i> <br>
						Github : <i style="color:chocolate">www.github.com/lamhotsimamora </i><br>
						Email : <i style="color:chocolate">lamhotsimamora36@gmail.com</i>
					</p>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
				</div>
			</div>
		</div>
	</div>


	<script>
		const SERVER = "<?= base_url() ?>";
		const API_GENERATE_CONTROLLER_MODEL = SERVER + 'api/generateControllerModel';
		const API_LOAD_TABLES = SERVER + 'api/loadTables';
		const API_LOAD_DATABASES = SERVER + 'api/loadDatabases';
		const API_LOAD_FIELDS = SERVER + 'api/loadFields';
	</script>

	<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.1/dist/js/bootstrap.bundle.min.js"></script>

	<script src="<?= base_url() ?>public/js/init.js"></script>
	<script src="<?= base_url() ?>public/js/app.js"></script>
</body>

</html>
