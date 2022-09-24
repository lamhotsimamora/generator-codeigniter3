ready(()=>{
	Vony({
		id:'directory_project'
	}).focus();
});
new Vue({
	el: '#app',
	data: {
		directory_project: null,
		controller_name: null,
		model_name: null,
		table: null,
		data_tables: null,
		database: null,
		data_databases: null,
		loading: false,
		fields: null,
		data_fields: null,
		loading2: false,
		primaryKey: null,
		select_generate: 3
	},
	mounted() {
	
	},
	methods: {
		enterGenerate: function(e){
			if (e.keyCode==13){
				this.generate()
			}
		},
		reset: function () {
			resetStorage();
			Swal.fire({
				icon: 'success',
				title: 'Reset Success...',
				text: '..',
				footer: ''
			});
			window.location.href = ".";
		},
		getClassField($column) {
			if (this.primaryKey == $column) {
				return `list-group-item list-group-item-action list-group-item-danger`;
			} else {
				return `list-group-item list-group-item-action list-group-item-primary`;
			}
		},
		renderField: function ($column) {
			if (this.primaryKey == $column) {
				return `${$column}<span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
				Primary Key
				<span class="visually-hidden"></span>
			  </span>
			`;
			} else {
				return $column;
			}
		},
		save: function () {
			if (this.directory_project == null || this.directory_project === '') {
				this.$refs.directory_project.focus();
				return;
			}
			if (this.controller_name == null || this.controller_name === '') {
				this.$refs.controller_name.focus();
				return;
			}
			if (this.model_name == null || this.model_name === '') {
				this.$refs.model_name.focus();
				return;
			}
			if (this.directory_project != null || this.controller_name != null || this.model_name) {
				saveStorage('directory_project', this.directory_project);
				saveStorage('controller_name', this.controller_name);
				saveStorage('model_name', this.model_name);
				Swal.fire({
					icon: 'success',
					title: 'Success',
					text: 'Data has been saved !',
					footer: ''
				})

			}
		},
		loadTable: function () {
			this.data_fields = null;
			this.loadTables();
		},
		loadField: function () {
			this.data_fields = null;
			this.loadFields();
		},
		loadFields: function () {
			if (this.database && this.table) {
				this.loading2 = true;
				const $this = this;
				axios({
					method: 'post',
					url: API_LOAD_FIELDS,
					data: {
						database_name: this.database,
						table: this.table
					},
					headers: {
						'X-Requested-With': 'XMLHttpRequest',
						"Content-Type": 'application/json'
					}
				}).then(function (response) {
					$this.loading2 = false;
					if (response.status == 200) {

						$this.data_fields = response.data.fields;
						$this.primaryKey = response.data.primaryKey[0].Column_name;

					}
				});
			}
		},
		loadTables: function () {
			if (this.database) {
				this.loading = true;
				const $this = this;
				axios({
					method: 'post',
					url: API_LOAD_TABLES,
					data: {
						database_name: this.database
					},
					headers: {
						'X-Requested-With': 'XMLHttpRequest',
						"Content-Type": 'application/json'
					}
				}).then(function (response) {
					$this.loading = false;
					if (response.status == 200) {
						var i = 0;
						var remake_data = [];
						response.data.forEach(element => {
							var final = element['Tables_in_' + $this.database];
							remake_data[i] = {
								table: final
							}
							i++;
						});
						$this.data_tables = remake_data;
					}
				});
			}
		},
		loadDatabases: function () {
			const $this = this;
			axios({
				url: API_LOAD_DATABASES
			}).then(function (response) {
				if (response.status == 200) {
					var final_data = response.data;

					$this.data_databases = final_data;
				}
			});
		},
		autoModelName: function () {
			if (this.controller_name != null) {
				this.model_name = this.controller_name + 'Model';
			} else if (this.controller_name===''|| this.controller_name==null) {
				this.model_name = null;
			}
		},
		generate: function () {
			if (this.directory_project == null || this.directory_project === '') {
				this.$refs.directory_project.focus();
				return;
			}
			if (this.controller_name == null || this.controller_name === '') {
				this.$refs.controller_name.focus();
				return;
			}
			if (this.model_name == null || this.model_name === '') {
				this.$refs.model_name.focus();
				return;
			}

			if (this.database == null) {
				Swal.fire({
					icon: 'error',
					title: 'Oops...',
					text: 'Select database !',
					footer: ''
				})
				return;
			}

			if (this.table == null) {
				Swal.fire({
					icon: 'error',
					title: 'Oops...',
					text: 'Select table !',
					footer: ''
				})
				return;
			}

			if (this.primaryKey == null) {
				Swal.fire({
					icon: 'error',
					title: 'Oops...',
					text: 'Select primary key on fields !',
					footer: ''
				})
				return;
			}

			if (this.select_generate==null){
				console.log("Select That You Want To Generate")
				return;
			}
			const $this= this;
			
			Vony({
				id:'btn_generate'
			}).disabled();

			axios({
				method: 'post',
				url: API_GENERATE_CONTROLLER_MODEL,
				data: {
					directory_project: this.directory_project,
					controller_name: this.controller_name,
					model_name: this.model_name,
					database: this.database,
					table: this.table,
					fields: this.data_fields,
					primaryKey: this.primaryKey,
					select_generate : this.select_generate
				},
				headers: {
					'X-Requested-With': 'XMLHttpRequest',
					"Content-Type": 'application/json'
				}
			}).then(function (response) {
				Vony({
					id:'btn_generate'
				}).enabled();
				if (response.status == 200) {

					var result = response.data.result;

					if (result==false){
						Swal.fire({
							icon: 'error',
							title: 'Uppz',
							text: response.data.message,
							footer: ''
						});
						$this.$refs.directory_project.focus()
						return;
					}
				
					var result_model = response.data.result_model;
					var result_controller = response.data.result_controller;

					var message = '';

					if ($this.select_generate==1){
						if (result_controller==true) {
							message = 'Controller Has Been Generated ';
							Swal.fire({
								icon: 'success',
								title: 'Success',
								text: message,
								footer: ''
							});
						} else {
							message = 'Controller Failed Generated ';
							Swal.fire({
								icon: 'error',
								title: 'Uppz',
								text: message,
								footer: ''
							});
						}
					}else if ($this.select_generate==2){
						if (result_model==true) {
							message = 'Model Has Been Generated ';
							Swal.fire({
								icon: 'success',
								title: 'Success',
								text: message,
								footer: ''
							});
						} else {
							message = 'Model Failed Generated ';
							Swal.fire({
								icon: 'error',
								title: 'Uppz',
								text: message,
								footer: ''
							});
						}
						
					}else if ($this.select_generate==3){
						if (result_controller==true) {
							message += 'Controller Has Been Generated - ';
						} else {
							message += 'Controller Failed Generated - ';
						}
	
						if (result_model==true) {
							message += 'Model Has Been Generated ';
						} else {
							message += 'Model Failed Generated ';
						}

						Swal.fire({
							icon: 'info',
							title: 'Info',
							text: message,
							footer: ''
						});
					}
				}
			});

		}
	},
	mounted() {
		this.loadDatabases();

		if (getStorage('directory_project') != false || getStorage('controller_name') != false || getStorage('model_name') != false) {
			this.directory_project = getStorage('directory_project');
			this.controller_name = getStorage('controller_name');
			this.model_name = getStorage('model_name');
		}
	},
})
