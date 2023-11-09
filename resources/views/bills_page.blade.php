@extends('layout')
@section('title', 'Счета')
@section('content')


<!-- Scripts -->
<script src="{{ asset('js/app.js') }}" defer></script>
<meta name="csrf-token" content="{{ csrf_token() }}">


<script> 

	var needtorefresh = false;

	$(document).ready(function () {		
		
		// Показываем все счета
		show_bill_list();
		
		$('#new_bill').find('#animal_choose_plate').removeClass('d-none'); 
		
		
		// Инициализация календарей
		var myDataPicker = $('#pay_date_start').datepicker({ 
			language : 'ru', 
			autoClose : true,
			clearButton : true,
			onSelect: function (dateText, inst) {
				if (!needtorefresh) {
					show_pays_list();
				}
			}
			
		}).data('datepicker');
		
		var myDataPicker = $('#pay_date_end').datepicker({ 
			language : 'ru', 
			autoClose : true,
			clearButton : true,
			onSelect: function (dateText, inst) {
				if (!needtorefresh) {
					show_pays_list();
				}
			}
		}).data('datepicker');
	
		
		$('#animal_pays_choose_filter').on("select2:select", function(e) { if (!needtorefresh) { show_pays_list(); } });
		
		$('#animal_pays_choose_filter').on("select2:unselect", function (e) { if (!needtorefresh) { show_pays_list(); } });
		
		$('#pays_client_id').on("select2:select", function(e) { if (!needtorefresh) { show_pays_list(); } });
		
		$('#pays_client_id').on("select2:unselect", function (e) { if (!needtorefresh) { show_pays_list(); } });
		
		
		// Вкладка счета открыта
		$('#tab-1').click(function(){			
			show_bill_list();
		});
		
		// Вкладка оплаты открыта
		$('#tab-2').click(function(){			
			show_pays_list();
		});

		// Вкладка зарплата открыта
		$('#tab-3').click(function(){

		});
				
	});
	
	
	// Слушатели кнопок календарик
	function calendarinputPDS(){  	
		$("#pay_date_start").trigger("focus");
	}

	function calendarinputPDE(){  	
		$("#pay_date_end").trigger("focus");
		
	}
		
	
	function show_pays_list(){ 
	
		var PaysForm = $("#pays_form").serialize();
		
		var anymal_id = '';
		
		var anymal_id = $('#animal_pays_choose_filter').val();	

		var client_id = '';
		
		var client_id = $('#pays_client_id').val();
		
		PaysForm = PaysForm + '&anymal_id=' + anymal_id + '&client_id=' + client_id + "";
		
		
		$('#pays_list_response').empty();
		
		if($('#pays_list_response').is(':empty') ||  !$.trim( $('#pays_list_response').html()).length) {			
			$('#pays_list_response').append("<div class='d-flex justify-content-center my-4'> <div class='spinner-border text-secondary' role='status'> <span class='visually-hidden'>Loading...</span> </div> </div>");			
		}
		
		
		setTimeout(function () {
			$.ajax({
				url: '/patientcard/get_pays_list',				
				method:'GET',
				dataType:'json',
				data: PaysForm,
				
				success: function(data) {
					
					$('#pays_list_response').empty();
					
					if($.isEmptyObject(data.error)){
					
						$('#pays_list_response').append(data.success);

					}else{
						
						$('#pays_list_response').append('<div class="d-flex justify-content-center align-items-center"> <div class="d-block px-4 py-2">Нет счетов</div></div>');

						toastr.error(data.error);
					}	
				},
					error: function(err) {
						
						$('#pays_list_response').empty();

						$('#pays_list_response').append('<div class="d-flex justify-content-center align-items-center"> <div class="d-block px-4 py-2">Нет счетов</div></div>');
						
						toastr.error('Ошибка');
					}
				});
			}, 300);
	
	}
	
	
	// Открыть счет из списка оплат
	function open_bill_from_pays(id) {
		
		// Открываем вкладку
		var someTabTriggerEl = document.querySelector('#tab-1')
		var tab = new bootstrap.Tab(someTabTriggerEl)
		tab.show();
		
		// Открываем исследование
		open_bill(id);
		
		document.documentElement.scrollTop = 0;
		
	}
	
	
	// Очистка данных
	function clear_pays_form() {

		if (($('#animal_pays_choose_filter').val() != 0 & $('#animal_pays_choose_filter').val() != null) | ($('#pays_client_id').val() != 0 & $('#pays_client_id').val() != null) | ($('#pay_date_start').val() != '') | ($('#pay_date_end').val() != '')) {
	
			needtorefresh = true;

			$('#pay_date_start').datepicker().data('datepicker').clear();
			
			$('#pay_date_end').datepicker().data('datepicker').clear();
			
			$('#animal_pays_choose_filter').val(0).trigger('change');
			
			$('#pays_client_id').val(0).trigger('change');
			
			$('#pays_form')[0].reset();

			needtorefresh = false;		
			
			show_pays_list();
		
		}
		
	}
	
</script>


@php
$patient_id = 0;
$bill_page = 1;
@endphp

<!-- Скрипты для счетов -->
@include('bill_scripts')


<style>

@media (max-width: 991px) {
	.col-md-border:not(:last-child) {
		border-bottom: 1px solid #d7d7d7;
    }
}
    

@media (min-width: 992px) {
	.col-md-border:not(:last-child) {
        border-right: 1px solid #d7d7d7;
    }  
}

.datepicker {
    z-index: 1060 !important; /* has to be larger than 1050 */
}


.form-group label {
    color: #3182CE !important;
	font-weight: 600;
}

.underinput-error{
	font-size:14px;
	font-weight: 600;
}

.custom-control-input {
  transform: scale(1.4);
}

.toggle-label{
    margin-left: 15px;
	font-weight: 600;
}

@media (max-width: 767px) {
	.dropdown-menu {
		min-width:250px;
		white-space: normal;
	}
	.dropdown-menu li a {
		word-wrap: break-word;
		white-space: normal;
	}
}

.to-show {
    display: none;
}
.to-hover:hover > .to-show {
    display: block; 
}


.checkbox-label{
    margin-left: 5px;
}


</style>


<div name="central_area" id="central_area" class="d-none border bg-white shadow rounded p-4">

	@php
	{{
		$staff = \App\Staff::where('staff_id', Auth::user()->staff_id)->first();
		$position = 0;
		
		if ($staff !== null) {
			$position = $staff->position;
		}
							
		if (Auth::user()->staff_id == 0 | $position ==1) {
			
			echo('<div class="border border p-3 rounded mt-3 mb-4">	
				<ul class="nav nav-pills" id="myTab" role="tablist">
					<li class="nav-item" role="presentation">
						<button class="nav-link active" id="tab-1" data-bs-toggle="tab" data-bs-target="#t1" type="button" role="tab" aria-controls="t1" aria-selected="true">Счета</button>
					</li>
					<li class="nav-item" role="presentation">
						<button class="nav-link" id="tab-2" data-bs-toggle="tab" data-bs-target="#t2" type="button" role="tab" aria-controls="t2" aria-selected="false">Оплаты</button>
					</li>
					<li class="nav-item" role="presentation">
						<button class="nav-link" id="tab-3" data-bs-toggle="tab" data-bs-target="#t3" type="button" role="tab" aria-controls="t3" aria-selected="false">Зарплата</button>
					</li>
				</ul>
			</div>');
		}
	
	}}
	@endphp


	<div class="tab-content" id="myTabContent">
		
		<div class="tab-pane fade show active" id="t1" role="tabpanel" aria-labelledby="tab-1">
	
	
			<div name="bills_container" id="bills_container">
			
				
				<div name="bills_search_list" id="bills_search_list">
						
					
				
					<form id="bills_form" autocomplete="off" enctype="multipart/form-data">	

						<div class="row border border px-3 mx-0 mt-2 mb-3 rounded justify-content-center">
												
							<div class="row px-2 pt-4">
							
								<div class="col-lg-4 px-2 mb-2">			
									<div class="form-group">
										<label class='control-label font-weight-bold' for="bill_date_start">Дата счета</label>
										<div class="input-group">
											<input name="bill_date_start" id="bill_date_start" type="text" class="form-control form-control-lg datepicker-here appearance-none bg-white" data-position='bottom left' placeholder="С..." readonly/>
												<button type="button" onclick="calendarinputBDS()" class="bg-white input-group-text" id="inputGroup-sizing-lg">
													<img src="{{ asset('images/calendar.png') }}" class="img-responsive"  width="30" height="30" />
												</button>
											</div>			
									</div>
								</div>
								
								<div class="col-lg-4 px-2 mb-2">			
									<div class="form-group">
										<label class='control-label font-weight-bold' for="bill_date_end">Дата счета</label>
										<div class="input-group">
											<input name="bill_date_end" id="bill_date_end" type="text" class="form-control form-control-lg datepicker-here appearance-none bg-white" data-position='bottom left' placeholder="По..." readonly/>
												<button type="button" onclick="calendarinputBDE()" class="bg-white input-group-text" id="inputGroup-sizing-lg">
													<img src="{{ asset('images/calendar.png') }}" class="img-responsive"  width="30" height="30" />
												</button>
											</div>			
									</div>
								</div>
								
								<div class="col-lg-4 px-2 mb-2">
									<div class="form-group">
									
										<label class='control-label font-weight-bold' for="paied_or_not">Оплата счета<span class="text-danger"></span></label>		
										<select name="paied_or_not" id="paied_or_not" class="form-select-lg js-example-basic-hide" style="width: 100%;" placeholder="Не выбрано" data-search="true">
											<option></option>
											<option value="1">Счет оплачен</option>
											<option value="2">Счет не оплачен</option>
										</select>

									</div>							
								</div>
							
							</div>
							
							<div class="row px-2 pt-0 pt-lg-2">
							
								<div class="col-lg-4 px-2 mb-2">
									<div class="form-group">
									
										<label class='control-label font-weight-bold' for="product_filter">Товар<span class="text-danger"></span></label>		
										<select name="product_filter" id="product_filter" class="form-select-lg js-example-basic-single product_list" style="width: 100%;" placeholder="Не выбрано" data-search="true">
											<option></option>
										</select>

									</div>							
								</div>
							
								<div class="col-lg-4 px-2 mb-2">
									<div class="form-group">
									
										<label class='control-label font-weight-bold' for="service_filter">Услуга<span class="text-danger"></span></label>		
										<select name="service_filter" id="service_filter" class="form-select-lg js-example-basic-single service_list" style="width: 100%;" placeholder="Не выбрано" data-search="true">
											<option></option>
										</select>

									</div>							
								</div>
							
								<div class="col-lg-4 px-2 mb-2">
									
									<div class="form-group">
										<label class="control-label font-weight-bold" for="client_id">Владелец</label>						
										<select name="client_id" id="client_id" class="form-select-lg js-example-basic-single block client_id" style="width: 100%;" placeholder="Не выбрано" data-search="true">
											<option></option>
										</select>
									</div>	
									
								</div>

							</div>
							
							
							<div class="row px-2 pt-0 pt-lg-2">
							
								<div class="col-lg-4 px-2 mb-2">
									
									<div class="form-group">
									
										<label class='control-label font-weight-bold' for="animal_choose_filter">Животное<span class="text-danger"></span></label>		
										
										<select name="animal_choose_filter" id="animal_choose_filter" class="js-example-basic-single form-select-lg animal_choose" placeholder="Не выбрано" data-search="true">
											<option></option>
										</select>

									</div>
									
								</div>
								
								
								<div class="col-lg-4 px-2 mb-2">
									<div class="form-group">
									
										<label class='control-label font-weight-bold' for="naimedornot">Именной / Неименной<span class="text-danger"></span></label>		
										<select name="naimedornot" id="naimedornot" class="form-select-lg js-example-basic-hide" style="width: 100%;" placeholder="Не выбрано" data-search="true">
											<option></option>
											<option value="1">Неименной</option>
											<option value="2">Именной</option>
										</select>

									</div>							
								</div>

							</div>
											
							<div class="d-lg-flex flex-row pt-2 mb-4">	
																																																			
								<div class="p-2"><button type="button" onclick="clear_bills_form()" class="btn btn-secondary">Сбросить</button></div>

								<div class="p-2"><button type="button" class="btn btn-primary" onclick="new_bill()">Новый счет</button></div>

							</div>	
								
						</div>
					
					</form>	

					<div name="bills_list_response" id="bills_list_response" class='row w-full items-center mt-2 mb-2 mx-0 d-flex justify-content-center'>

					</div>
									
				</div>
				
				<div name="one_bill" id="one_bill" style="display: none;">
																
				</div>
								
				<div name="print_bill_div" id="print_bill_div" class="d-none">
						
				</div>
				
			</div>
				
		</div>
	
		<div class="tab-pane fade" id="t2" role="tabpanel" aria-labelledby="tab-2">
		
			<div name="pays_search_list" id="pays_search_list">
			
				<form id="pays_form" autocomplete="off" enctype="multipart/form-data">	

					<div class="row border border px-3 mx-0 mt-2 mb-3 rounded justify-content-center">
											
						<div class="row px-2 pt-4">
						
							<div class="col-lg-4 px-2 mb-2">			
								<div class="form-group">
									<label class='control-label font-weight-bold' for="pay_date_start">Дата оплаты</label>
									<div class="input-group">
										<input name="pay_date_start" id="pay_date_start" type="text" class="form-control form-control-lg datepicker-here appearance-none bg-white" data-position='bottom left' placeholder="С..." readonly/>
											<button type="button" onclick="calendarinputPDS()" class="bg-white input-group-text" id="inputGroup-sizing-lg">
												<img src="{{ asset('images/calendar.png') }}" class="img-responsive"  width="30" height="30" />
											</button>
										</div>			
								</div>
							</div>
							
							<div class="col-lg-4 px-2 mb-2">			
								<div class="form-group">
									<label class='control-label font-weight-bold' for="pay_date_end">Дата оплаты</label>
									<div class="input-group">
										<input name="pay_date_end" id="pay_date_end" type="text" class="form-control form-control-lg datepicker-here appearance-none bg-white" data-position='bottom left' placeholder="По..." readonly/>
											<button type="button" onclick="calendarinputPDE()" class="bg-white input-group-text" id="inputGroup-sizing-lg">
												<img src="{{ asset('images/calendar.png') }}" class="img-responsive"  width="30" height="30" />
											</button>
										</div>			
								</div>
							</div>
							
							<div class="col-lg-4 px-2 mb-2">
								
								<div class="form-group">
									<label class="control-label font-weight-bold" for="pays_client_id">Владелец</label>						
									<select name="pays_client_id" id="pays_client_id" class="form-select-lg js-example-basic-single block client_id" style="width: 100%;" placeholder="Не выбрано" data-search="true">
										<option></option>
									</select>
								</div>	

							</div>
						
						</div>
						
						
						<div class="row px-2 pt-0 pt-lg-2">
						
							<div class="col-lg-4 px-2 mb-2">
								
								<div class="form-group">
								
									<label class='control-label font-weight-bold' for="animal_pays_choose_filter">Животное<span class="text-danger"></span></label>		
									
									<select name="animal_pays_choose_filter" id="animal_pays_choose_filter" class="js-example-basic-single form-select-lg animal_choose" placeholder="Не выбрано" data-search="true">
										<option></option>
									</select>

								</div>
								
							</div>
							

						</div>
										
						<div class="d-lg-flex flex-row pt-2 mb-4">	
																																																		
							<div class="p-2"><button type="button" onclick="clear_pays_form()" class="btn btn-secondary">Сбросить</button></div>

						</div>	
							
					</div>
				
				</form>	

				<div name="pays_list_response" id="pays_list_response" class='row w-full items-center mt-2 mb-2 mx-0 d-flex justify-content-center'>

				</div>
								
			</div>
		
		</div>

		<div class="tab-pane fade" id="t3" role="tabpanel" aria-labelledby="tab-3">

			<div id="app" name="app"> 
				<salary-component ref="salarycomponent"></salary-component>
			</div>
							
		</div>
		
			
	</div>
		

	
	

	<!-- Диалог нового счета -->
	@include('new_bill_dialog')
	
	
	<script>
	
		// Закрытие всех открытых меню при начале скрола страницы
		$(document).scroll(function() {
			
			// datepicker
			$('#bill_date_start').datepicker("hide");
			$('#bill_date_start').blur();
			
			$('#bill_date_end').datepicker("hide");
			$('#bill_date_end').blur();
			
		});
		
		
		// Активация поллей Select2
		// С поиском
		$('.js-example-basic-single').select2({
			placeholder: "Выберите значение",
			theme: "bootstrap-5",
			allowClear: true
		// Не открывать список при сбросе значения
		}).on('select2:unselecting', function() {
			$(this).data('unselecting', true);
		}).on('select2:opening', function(e) {
			if ($(this).data('unselecting')) {
				$(this).removeData('unselecting');
				e.preventDefault();
			}
		});
				
		// Без поиска
		$(".js-example-basic-hide").select2({
			placeholder: "Выберите значение",
			theme: "bootstrap-5",
			allowClear: true,
			minimumResultsForSearch: Infinity
		}).on('select2:unselecting', function() {
			$(this).data('unselecting', true);
		}).on('select2:opening', function(e) {
			if ($(this).data('unselecting')) {
				$(this).removeData('unselecting');
				e.preventDefault();
			}
		});	


		// Установка списка врачей
		$.ajax({
			url: '/staff/search',
			method:'GET',
			dataType:'json',
			success: function(data) {
				$(".doctors").select2({ 
				placeholder: "Выберите значение",
				theme: "bootstrap-5",
				allowClear: true,
				data: data 
				}).on('select2:unselecting', function() {
					$(this).data('unselecting', true);
				}).on('select2:opening', function(e) {
					if ($(this).data('unselecting')) {
						$(this).removeData('unselecting');
						e.preventDefault();
					}
				});				
				
			},
				error: function(err) {	
					//toastr.error('Ошибка загрузки списка врачей');
				}
		});
			
		
		// Заполнение данных - товары				
		$('.product_list').select2({
			minimumInputLength: 3,		
			theme: "bootstrap-5",
			language: {
				inputTooShort: function() {
					return 'Введите не менее 3-х символов';	
				}
			},
			templateResult : formatProductResult,			
			placeholder: "Выберите товар",
			allowClear: true,						
			ajax: {
				url: '/guides/product_search',
				dataType: 'json',
				delay: 300,					
				data: function (params) {		
					return {
						q: $.trim(params.term)
					};
				},
				processResults: function (data) {						
					return {
						results: data
					};							
				},
			}
		}).on('select2:unselecting', function() {
			$(this).data('unselecting', true);					
		}).on('select2:opening', function(e) {
			if ($(this).data('unselecting')) {
				$(this).removeData('unselecting');
				e.preventDefault();
			}
		});
		
		
		// Custom результат для Select2 - поиск товаров
		function formatProductResult (data) {
			if (!data.id) {
				return data.text;
			}
			
			var category = '';

			if (data.category) {
				category = '<br> <span class="fst-italic">' + data.category + '</span>';
			}

			var price = '';

			if (data.price) {
				var price = '<br> <span class="fst-italic">' + data.price + '</span>';
			}

			var $data = $('<span>' + data.text + '</span>' + category + price);

			return $data;
		};


		
		// Заполнение данных - услуги				
		$('.service_list').select2({
			minimumInputLength: 3,		
			theme: "bootstrap-5",
			language: {
				inputTooShort: function() {
					return 'Введите не менее 3-х символов';	
				}
			},
			templateResult : formatServiceResult,	
			placeholder: "Выберите услугу",
			allowClear: true,						
			ajax: {
				url: '/guides/service_search',
				dataType: 'json',
				delay: 300,					
				data: function (params) {		
					return {
						q: $.trim(params.term)
					};
				},
				processResults: function (data) {						
					return {
						results: data
					};							
				},
			}
		}).on('select2:unselecting', function() {
			$(this).data('unselecting', true);					
		}).on('select2:opening', function(e) {
			if ($(this).data('unselecting')) {
				$(this).removeData('unselecting');
				e.preventDefault();
			}
		});


		// Custom результат для Select2 - поиск услуг
		function formatServiceResult (data) {
			if (!data.id) {
				return data.text;
			}
			
			var category = '';

			if (data.category) {
				category = '<br> <span class="fst-italic">' + data.category + '</span>';
			}

			var $data = $('<span>' + data.text + '</span>' + category);

			return $data;
		};
		
			
		// Custom результат для Select2 (поиск животных в счетах)
		function formatResult (data) {
			if (!data.id) {
				return data.text;
			}
						
			if (data.client !== "" & data.type !== "") {
				var $data = $('<span>' + data.text + '</span> <br> <div class="text-xs fst-italic">' + data.type + '</div> <div class="text-xs fst-italic">' + data.client + '</div>');
			} else if (data.client !== "" & data.type === "") {
				var $data = $('<span>' + data.text + '</span> <br> <div class="text-xs fst-italic">' + data.client + '</div>');
			} else if(data.client === "" & data.type !== "") {
				var $data = $('<span>' + data.text + '</span> <br> <div class="text-xs fst-italic">' + data.type + '</div>');
			} else {
				var $data = $('<span>' + data.text + '</span>');
			}
						
			return $data;
		};
					

		// Поиск клиентов
		$('.animal_choose').select2({
			minimumInputLength: 3,
			theme: "bootstrap-5",
			language: {
				inputTooShort: function() {
					return 'Введите не менее 3-х символов';	
				}
			},
			templateResult : formatResult,		
			placeholder: "Кличка",
			allowClear: true,						
			ajax: {
				url: '/patientcard/patientsforbills',
				dataType: 'json',
				delay: 300,					
				data: function (params) {		
					return {
						q: $.trim(params.term)
					};
				},
				processResults: function (data) {			
					return {
						results: data
					};							
				},
			}
		}).on('select2:unselecting', function() {
			$(this).data('unselecting', true);		
		}).on('select2:opening', function(e) {
			if ($(this).data('unselecting')) {
				$(this).removeData('unselecting');
				e.preventDefault();
			}
		});
		
		
		
		// Custom результат для Select2 (поиск клиентов)
		function formatResult2 (data) {
			if (!data.id) {
				return data.text;
			}				  
			if (data.phone) {
				var $data = $('<span>' + data.text + '</span> <br> <div class="fst-italic">' + data.phone + '</div>');
			} else {
				var $data = $('<span>' + data.text + '</span>');
			}			
			return $data;
		};
			
			
		// Поиск клиентов
		$('.client_id').select2({	
			minimumInputLength: 3,
			theme: "bootstrap-5",
			language: {
				inputTooShort: function() {
					return 'Введите не менее 3-х символов';	
				}
			},
			templateResult : formatResult2,		
			placeholder: "Ф.И.О. или телефон",
			allowClear: true,						
			ajax: {
				url: '/addanimal/client/search',
				dataType: 'json',
				delay: 300,					
				data: function (params) {		
					return {
						q: $.trim(params.term),
						page: 'main'
					};
				},
				processResults: function (data) {
					return {
						results: data
					};
				},
			}
		}).on('select2:unselecting', function() {
			$(this).data('unselecting', true);
		}).on('select2:opening', function(e) {				
			if ($(this).data('unselecting')) {
				$(this).removeData('unselecting');
				e.preventDefault();
			}
		});
	
	
	</script>
		
</div>

@endsection