<template>

	<v-app fill-height class="d-flex" height="100%">
		<v-main app fill-height class="d-flex" height="100%">
			<v-container fluid fill-height class="align-start ma-0 pa-0" style="all: unset;">								
				<v-container fluid class="ma-0 pa-0" style="all: unset;">
					<v-sheet class="ma-0 pa-5 d-flex justify-center rounded" outlined>
						<v-row class="my-0 py-0">
							
							<v-col
								cols="12"
								lg="6">
								
								<v-dialog
									ref="dialog"
									v-model="datesmenu"
									persistent
									width="290px">

									<template v-slot:activator="{ on, attrs }">
										<v-text-field
											outlined
											dense
											v-model="computedDatesRange"
											prepend-inner-icon="mdi-calendar"
											readonly
											hide-details
											v-bind="attrs"
											v-on="on">
										</v-text-field>
									</template>

									<v-date-picker
										v-model="dates"
										range
										no-title
										@input="closepicker"
										:first-day-of-week="1"
										locale="ru-ru">

										<v-spacer></v-spacer>
										
										<v-btn
											text
											color="primary"
											@click="closepicker">
											Отмена
										</v-btn>

									</v-date-picker>

								</v-dialog>
							</v-col>

							<v-col
								cols="12"
								lg="6">

								<v-select
									:items="staffs"
									hide-details
									no-data-text="Нет данных"
									placeholder="Выберите сотрудника"
									item-value="id"
									item-text="text"										
									outlined
									dense
									clearable
									v-model="selected_item"
									:loading="loading_select"
									class="d-flex align-center">
								</v-select>
							</v-col>
						</v-row>
					</v-sheet>

					<div class="text-center">
						<v-progress-circular
							v-show="loading_salarydata"
							:size="50"
							color="primary"
							indeterminate
							class="fill-height mt-8">
						</v-progress-circular>
					</div>

					<div v-if="items.length == 0 & loading_salarydata == false" class="my-0 py-0 mt-8 d-flex justify-center">Нет данных.</div>

					<v-data-iterator
						:items="items"
						hide-default-footer
						class="my-4"
						v-if="items.length != 0 && loading_salarydata == false">

						<template v-slot:default="props">
							<v-row>
								<v-col
									v-for="item in items"
									:key="item.staffname"
									cols="12">
								
									<v-card>
										<v-card-title class="subheading font-weight-bold">
											{{ item.staffname }}
										</v-card-title>

										<v-divider class="my-0 py-0"></v-divider>

										<v-card-title class="pb-0">Услуги</v-card-title>

										<v-simple-table>
											<template v-slot:default>
												<thead>
													<tr>
														<th class="text-left">
															Наименование
														</th>
														<th class="text-left">
															Сумма
														</th>
														<th class="text-left">
															Процент
														</th>
														<th class="text-left">
															Расчет
														</th>
													</tr>
												</thead>
												<tbody>
													<tr v-for="service in item.services">
														<td>{{ service.servicename }}</td>
														<td>{{ service.summ.toFixed(2) }}</td>
														<td>{{ service.percent.toFixed(2) }}</td>
														<td>{{ (service.summ/100*service.percent).toFixed(2) }}</td>
													</tr>
												</tbody>
											</template>
										</v-simple-table>

										<v-card-title class="py-0">Итого услуги: {{ countitogo(item.services).toFixed(2) }}</v-card-title>

										<v-card-title class="pt-0">Итого услуги (расчет): {{ countitogorasch(item.services).toFixed(2) }}</v-card-title>

										<v-card-title class="py-0">Товары</v-card-title>

										<v-simple-table>
											<template v-slot:default>
												<thead>
													<tr>
														<th class="text-left">
															Наименование
														</th>
														<th class="text-left">
															Сумма
														</th>
														<th class="text-left">
															Процент
														</th>
														<th class="text-left">
															Расчет
														</th>
													</tr>
												</thead>
												<tbody>
													<tr v-for="product in item.products">
														<td>{{ product.productname }}</td>
														<td>{{ product.summ.toFixed(2) }}</td>
														<td>{{ product.percent.toFixed(2) }}</td>
														<td>{{ (product.summ/100*product.percent).toFixed(2) }}</td>
													</tr>
												</tbody>
											</template>
										</v-simple-table>

										<v-card-title class="py-0">Итого товары: {{ countitogo(item.products).toFixed(2) }}</v-card-title>
								
										<v-card-title class="py-0">Итого товары (расчет): {{ countitogorasch(item.products).toFixed(2) }}</v-card-title>

										<v-card-title class="py-0 pt-5 pb-7">ВСЕГО (расчет): {{ countvsego(item).toFixed(2) }}</v-card-title>
								
									</v-card>
								</v-col>
							</v-row>
						</template>
					</v-data-iterator>	
				</v-container>
			</v-container>
		</v-main>
	</v-app>

</template>




<script>


	import {DateTime} from "luxon";

	
	export default {
        
		data: () => ({
			
			datesmenu: false,
			dates: [DateTime.local().startOf('month').toFormat('yyyy-MM-dd'), DateTime.local().endOf('month').toFormat('yyyy-MM-dd')],
			staffs: [],
			selected_item: 0,			
			loading_select: false,
			loading_salarydata: true,
      		
			items: [],

		}),
		
		mounted() {
			
			// Добавляем стили Vuetify только для этого компонента
			let style = document.createElement('link');
			style.type = "text/css";
			style.rel = "stylesheet";
			style.href = 'css/vuetify.css';
			document.head.appendChild(style);

			window.thisvue=this;

		},
		
		watch: {

			// слушатель изменения переменной
			selected_item(newSelected_item, oldSelected_item) {
				this.getSalaryData();
			},
			
		},
		
		computed: {

			computedDatesRange () {
				if (this.dates.length == 2) {					
					if (DateTime.fromISO(this.dates[0]) < DateTime.fromISO(this.dates[1])) {
						return this.formatDate(this.dates[0]) + " ~ " + this.formatDate(this.dates[1])
					} else {
						return this.formatDate(this.dates[1]) + " ~ " + this.formatDate(this.dates[0])
					}
				} else {
					return this.formatDate(this.dates[0])
				}
			},

		}, 
		
		methods: {

			initialize () {	
				this.setStaffList();
				//this.getSalaryData();
			},

			formatDate(date) {
				if (date === null || date === undefined) {
					return ''
				} else {
					return new Date(date).toLocaleString([], {year: 'numeric', month: 'numeric', day: 'numeric'})
				}
			},

			closepicker() {	
				if (this.dates.length == 2) {
					this.datesmenu = false
					this.getSalaryData();
				}
			},

			setStaffList() {
								
				this.loading_select = true;
				
				axios.get('/staff/search', { params: {} })
					.then((response) => {					
						this.staffs = response.data;

						if (Object.keys(this.staffs).length != 0) {
							this.selected_item = this.staffs[0].id;
						}

					})
					.catch(error => {})
					.finally(() => {
						this.loading_select = false;
					}); 
			},

			getSalaryData() {

				this.loading_salarydata = true;

				var startdate = '';
				var enddate = '';

				if (this.dates.length == 2) {					
					if (DateTime.fromISO(this.dates[0]) < DateTime.fromISO(this.dates[1])) {
						startdate = this.dates[0];
						enddate = this.dates[1];
					} else {
						startdate = this.dates[1];
						enddate = this.dates[0];
					}
				}

				axios.get('/patientcard/get_salary_data', {
					params: {
						startdate: startdate,
						enddate: enddate,
						staff: this.selected_item,
					}
					})
				.then((response) => {

					this.items = [];
					
					for (var i = 0; i < response.data.success.length; i++) {

						var feed = {staffname: response.data.success[i].staffname, services: response.data.success[i].services, products: response.data.success[i].products};

						this.items.push(feed);

					}
					
				})
				.catch(error => {})
				.finally(() => {
					this.loading_salarydata = false;
				}); 

			},

			countitogo (items) {

				var count = 0;
				items.forEach(function(value, index) {
					count += +value.summ;
				});

				return count ;
			},

			countitogorasch (items) {

				var count = 0;
				items.forEach(function(value, index) {
					count += +value.summ/100*value.percent;
				});

				return count ;
			},

			countvsego (items) {

				var count = 0;
				items.services.forEach(function(value, index) {
					count += +value.summ/100*value.percent;
				});
				
				items.products.forEach(function(value, index) {
					count += +value.summ/100*value.percent;
				});

				return count ;
			},

		},
		
		created () {

			this.initialize()

			// Слушатель открытия вкладки Зарплата
			document.getElementById('tab-3').onclick = function() {
				thisvue.getSalaryData();
			};

		},
    }

</script>



<style>

  .v-application--wrap {
    min-height: 0vh !important;
  }
  
  .v-menu {
	  z-index: 10000;
  }
  
  .dialogzindex {
	  z-index: 10000;
  }
  
</style>






