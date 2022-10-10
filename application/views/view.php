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
	<title>Generator View</title>
	<script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
	<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
	<link href="//netdna.bootstrapcdn.com/font-awesome/3.2.1/css/font-awesome.css" rel="stylesheet">
	<script src="<?= base_url() ?>public/js/vony.js"></script>
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

					Generate View Codeigniter 3</span>
				<hr>
				<h4>FrontEnd Assets</h4>
				<ol class="list-group list-group-numbered">
					<li class="list-group-item"><a href="https://getbootstrap.com/">Bootstrap 4</a></li>
					<li class="list-group-item"><a href="https://sweetalert2.github.io">Sweet Alert 3</a></li>
					<li class="list-group-item"><a href="https://v2.vuejs.org/v2/guide/installation.html">Vue JS 2</a></li>
					<li class="list-group-item"><a href="https://fontawesome.com/v3/">Font Awesome v3</a></li>
				</ol>

				<hr>

				<div class="mb-3">
					<label for="directory_project" class="form-label">
						<strong>Directory Project</strong>
					</label>
					<input type="text" @keypress="enterGenerate" readonly v-model="directory_project" ref="directory_project" class="form-control" id="directory_project" placeholder="C:\xampp\htdocs\myproject">
				</div>


				<div class="mb-3">
					<label for="exampleFormControlInput2" class="form-label">
						<strong>Filename</strong>
					</label>
					<input type="text" @keypress="enterGenerate" v-model="filename" ref="filename" class="form-control" id="exampleFormControlInput2" placeholder="">
				</div>



				<button type="button" @click="generate" id="btn_generate" class="btn btn-primary">
					<i class="icon-legal"></i> Generate</button>
				<br><br>
				<a href="<?= base_url() ?>">Back</a>

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


	<script>
		const SERVER = "<?= base_url() ?>";
		const API_GENERATE_VIEW = SERVER + 'api/generateView';
	</script>

	<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.1/dist/js/bootstrap.bundle.min.js"></script>

	<script src="<?= base_url() ?>public/js/init.js"></script>

	<script>
		var $btn_generate = Vony({
			id: 'btn_generate'
		});
		new Vue({
			el: '#app',
			data: {
				filename: null,
				directory_project: null
			},
			mounted() {
				if (getStorage('directory_project') != false) {
					this.directory_project = getStorage('directory_project');
				}
			},
			methods: {
				generate: function() {
					if (this.directory_project == null || this.directory_project === '') {
						this.$refs.directory_project.focus();
						return;
					}

					if (this.filename == null || this.filename === '') {
						this.$refs.filename.focus();
						return;
					}
					$btn_generate.disabled();

					const $this = this;
					axios({
						method: 'post',
						url: API_GENERATE_VIEW,
						data: {
							directory_project: this.directory_project,
							filename: this.filename
						},
						headers: {
							'X-Requested-With': 'XMLHttpRequest',
							"Content-Type": 'application/json'
						}
					}).then(function(response) {

						$btn_generate.enabled()

						if (response.status == 200) {

							var result = response.data.result_view;

							if (result) {
								Swal.fire({
									icon: 'success',
									title: 'Success',
									text: 'File view ' + $this.filename + ' has been generated',
									footer: ''
								});
								$this.$refs.filename.focus()
								return;
							}
						}
					});

				},
				enterGenerate: function(e) {
					if (e.keyCode == 13) {
						this.generate()
					}
				}
			},
		})
	</script>
</body>

</html>
