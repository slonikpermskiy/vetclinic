
@extends('layout')
@section('title', 'Ветеринарная клиника')
@section('content')


<script>

	// Поиск пациентов
	function search_patients() {
		
		$('#patient_response').empty();
		
		if($('#patient_response').is(':empty') ||  !$.trim( $('#patient_response').html()).length) {			
			$('#patient_response').append("<div class='d-flex justify-content-center py-5'> <div class='spinner-border text-secondary' role='status'> <span class='visually-hidden'>Loading...</span> </div> </div>");			
		}
		
		// Construct data string
		var dataString = $("#myForm").serialize();
		
		
		var service_selected = '';
		
		var service_select = $('#service').select2('data');
		
		if (service_select[0] != null && service_select[0].text != 0) {
			service_selected = service_select[0].text;
		}
		
		
		dataString = dataString + '&service_selected=' + service_selected;
		
		
		$.ajax({
			url: '/searchpatients',
			method:'GET',
			dataType:'json',
			data: dataString,
			success: function(data) {
				
				if($.isEmptyObject(data.error)){
				
					$('#patient_response').html("");
					$('#patient_response').append(data.success);
					
					// Запоминаем все значения фильтров !!!
					
					var client_id = '';
					var client_data = '';
					
					if (!isEmpty($('#client_id').val()) && !isEmpty($('#client_id').select2('data')[0].text)) {
						client_id = $('#client_id').val();
						client_data = $('#client_id').select2('data')[0].text;
					}
					
					var short_name = '';
					
					if (!isEmpty($('#short_name').val())) {
						short_name = $('#short_name').val();
					}
										
					var animal_type_id = '';
					var animal_type_data = '';
					
					if (!isEmpty($('#animal_type_id').val()) && !isEmpty($('#animal_type_id').select2('data')[0].text)) {
						animal_type_id = $('#animal_type_id').val();
						animal_type_data = $('#animal_type_id').select2('data')[0].text;
					}
										
					var breed_id = '';
					var breed_data = '';
					
					if (!isEmpty($('#breed_id').val()) && !isEmpty($('#breed_id').select2('data')[0].text)) {
						breed_id = $('#breed_id').val();
						breed_data = $('#breed_id').select2('data')[0].text;
					}
					
					var sex_id = '';
					
					if (!isEmpty($('#sex_id').val())) {
						sex_id = $('#sex_id').val();
					}
					
					
					var visit_date_start = '';
					var visit_date_end = '';
					
					if (!isEmpty($('#visit_date_start').val())) {
						visit_date_start = $('#visit_date_start').val();
					}
					
					if (!isEmpty($('#visit_date_end').val())) {
						visit_date_end = $('#visit_date_end').val();
					}
										
					var service_id = '';
					var service_data = '';
					
					if (!isEmpty($('#service').val()) && !isEmpty($('#service').select2('data')[0].text)) {
						service_id = $('#service').val();
						service_data = $('#service').select2('data')[0].text;
					}
					

					if (history.replaceState) {
								
						/*history.replaceState({"id":100}, null, '?client_id='+client_id+
						'&client_data='+client_data+
						'&short_name='+short_name+
						'&animal_type_id='+animal_type_id+
						'&animal_type_data='+animal_type_data+
						'&breed_id='+breed_id+
						'&breed_data='+breed_data+
						'&sex_id='+sex_id+
						'&visit_date_start='+visit_date_start+
						'&visit_date_end='+visit_date_end+
						'&service='+service);*/
						
						history.replaceState({"client_id":client_id, "client_data":client_data, 
						"short_name":short_name, "animal_type_id":animal_type_id,
						"animal_type_data":animal_type_data, "breed_id":breed_id,
						"breed_data":breed_data, "sex_id":sex_id,
						"visit_date_start":visit_date_start, "visit_date_end":visit_date_end, "service_id":service_id, "service_data":service_data}, null, '/');
						
					} 
					
				}else{
					
					$('#patient_response').html("");
					
					$('#patient_response').append('<div class="d-flex justify-content-center align-items-center mt-3"> <div class="d-block px-4 py-2">Данные не найдены.</div></div>');
				
					toastr.error(data.error);
				}	
			},
				error: function(err) {
					
					$('#patient_response').html("");

					$('#patient_response').append('<div class="d-flex justify-content-center align-items-center mt-3"> <div class="d-block px-4 py-2">Данные не найдены.</div></div>');
					
					toastr.error('Ошибка');
				}
			});
	}
	
		
	// Очистка данных
	function clear_form() {		
		// Очистка данных формы
		$('#myForm')[0].reset();
		$('#client_id').val('').trigger('change');		
		$('#animal_type_id').val('').trigger('change');
		$('#breed_id').val('').trigger('change');
		$('#sex_id').val('').trigger('change');
		$('#service').val('').trigger('change');
		$('#visit_date_start').datepicker().data('datepicker').clear();	
		$('#visit_date_end').datepicker().data('datepicker').clear();
		// Очистка остального
		$('#patient_response').html("");
		
		// Очистка данных фильтров в ссылке
		history.replaceState(null, null, '/');
		
		search_patients();
	}
	
	
	// Открываем страницу добавления животного
	function add_animal() {
		window.location.href = "addanimal"; 				
	}
	
	

	// Слушатели кнопок календариков
	function calendarinput1(){  	
		$("#visit_date_start").trigger("focus");
	}
	
	function calendarinput2(){  	
		$("#visit_date_end").trigger("focus");
	}
	
	
	// Открываем карточку пациента
	function open_client_card(patient_id) {
		window.location.href="{{URL::to('patientcard')}}" + "?patient_id=" + patient_id;	
	}
	

</script>

<style>

.form-group label {
    color: #3182CE !important;
	font-weight: 600;
}

.datepicker {
    z-index: 1060 !important; /* has to be larger than 1050 */
}

</style>

<div name="central_area" id="central_area" class="d-none border bg-white shadow rounded p-4">

			
	<form autocomplete="off" id="myForm">			
		
		<div class="border border px-3 rounded">
								
			<div class="row px-2 pt-4">
				<div class="col-lg-3 px-2 mb-4">
					<div class="form-group">
						<label class="control-label font-weight-bold" for="client_id">Владелец</label>						
						<select name="client_id" id="client_id" class="form-select-lg js-example-basic-single block" style="width: 100%;" placeholder="Не выбрано" data-search="true">
							<option></option>
						</select>
					</div>					
				</div>
						
				<div class="col-lg-3 px-2 mb-4">		
					<div class="form-group">
						<label class='control-label font-weight-bold' for="short_name">Кличка</label>
						<input name="short_name" class="form-control form-control-lg" id='short_name' placeholder="Кличка">
					</div>			
				</div>
						
				<div class="col-lg-3 px-2 mb-4">
					<div class="form-group">
						<label class='control-label font-weight-bold' for="animal_type_id">Вид</label>						
						<select name="animal_type_id" id="animal_type_id" class="form-select-lg js-example-basic-single block2" style="width: 100%;" placeholder="Не выбрано" data-search="true">
							<option></option>
						</select>
					</div>							
				</div>
				
				<div class="col-lg-3 px-2 mb-4">
					<div class="form-group">
						<label class='control-label font-weight-bold' for="breed_id">Порода</label>						
						<select name="breed_id" id="breed_id" class="form-select-lg js-example-basic-single block" style="width: 100%;" placeholder="Не выбрано" data-search="true">
							<option></option>
						</select>
					</div>							
				</div>
				
			</div>
					
			<div class="row px-2">															
				
				<div class="col-lg-3 px-2 mb-4">
					<div class="form-group">
						<label class='control-label font-weight-bold' for="sex_id">Пол</label>						
						<select name="sex_id" id="sex_id" class="form-select-lg js-example-basic-hide" style="width: 100%;" placeholder="Не выбрано" data-search="true">
							<option></option>
							<option value="1">Мужской</option>
							<option value="2">Женский</option>
						</select>
					</div>							
				</div>
		
				<div class="col-lg-3 px-2 mb-4">			
					<div class="form-group">
						<label class='control-label font-weight-bold' for="visit_date_start">Дата посещения</label>
						<div class="input-group input-group-lg">
							<input name="visit_date_start" id="visit_date_start" type="text" class="form-control form-control-lg datepicker-here appearance-none bg-white" data-position='bottom left' placeholder="С..." readonly />
								<button type="button" onclick="calendarinput1()" class="bg-white input-group-text" id="inputGroup-sizing-lg">
									<img src="{{ asset('images/calendar.png') }}" class="img-responsive"  width="30" height="30" />
								</button>
							</div>			
					</div>
				</div>
				
				<div class="col-lg-3 px-2 mb-4">			
					<div class="form-group">
						<label class='control-label font-weight-bold' for="visit_date_end">Дата посещения</label>
						<div class="input-group input-group-lg">
							<input name="visit_date_end" id="visit_date_end" type="text" class="form-control form-control-lg datepicker-here appearance-none bg-white" data-position='bottom left' placeholder="По..." readonly />
								<button type="button" onclick="calendarinput2()" class="bg-white input-group-text" id="inputGroup-sizing-lg">
									<img src="{{ asset('images/calendar.png') }}" class="img-responsive"  width="30" height="30" />
								</button>
							</div>			
					</div>
				</div>
				
				<div class="col-lg-3 px-2 mb-4">
					<div class="form-group">
						<label class='control-label font-weight-bold' for="service">Услуга</label>						
						<select name="service" id="service" class="form-select-lg js-example-basic-hide service_list" style="width: 100%;" placeholder="Не выбрано" data-search="true">
							<option></option>
						</select>
					</div>							
				</div>

			</div>
					
				<div class="d-lg-flex flex-row mb-4">	
																																																
					<div class="p-2"><button type="button" onclick="clear_form()" class="btn btn-secondary">Сбросить</button></div>

					<div class="p-2"><button type="button" onclick="add_animal()" class="btn btn-dark">Добавить</button></div>

				</div>	
				
		</div>
						
	</form>	

	<div name="patient_response" id="patient_response" class='w-full min-h-full items-center justify-between'>
							
	</div>
					
		
	<script>
	
		// Закрытие всех открытых popup при начале скрола страницы
		$(document).scroll(function() {
			// datepicker
			$('#visit_date_start').datepicker("hide");
			$('#visit_date_start').blur();
			
			$('#visit_date_end').datepicker("hide");
			$('#visit_date_end').blur();
		});
		
		
		// Активация DatePickers
		$('#visit_date_start').datepicker({
			language: 'ru',
			autoClose: true,
			clearButton : true,
			onSelect: function (dateText, inst) {
				search_patients();
			}
		});
			
		$('#visit_date_end').datepicker({
			language: 'ru',
			autoClose: true,
			clearButton : true,
			onSelect: function (dateText, inst) {
				search_patients();
			}
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
			
			
		// Custom результат для Select2
		function formatResult (data) {
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
		$('#client_id').select2({	
			minimumInputLength: 3,
			theme: "bootstrap-5",
			language: {
				inputTooShort: function() {
					return 'Введите не менее 3-х символов';	
				}
			},
			templateResult : formatResult,		
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
			

														
		// Тип животного				
		function settypes () {
			$.ajax({
				url: '/guides/animaltype_search',
				method:'GET',
				dataType:'json',
				data: {page:'main'},
				success: function(data) {
					$('#animal_type_id').select2({ 
					placeholder: "Выберите значение",
					theme: "bootstrap-5",
					allowClear: true,
					// Не показывать поле поиска
					minimumResultsForSearch: Infinity,
					data: data 
					}).on('select2:unselecting', function() {
						$(this).data('unselecting', true);
					}).on('select2:opening', function(e) {
						if ($(this).data('unselecting')) {
							$(this).removeData('unselecting');
							e.preventDefault();
						}
					});
					// Установка select2 для пород
					setbreeds();
					
				},
					error: function(err) {	
						//toastr.error('Ошибка загрузки типов животных');
					}
			});
		}
				
						
			
		// Слушатель выбора типа животного
		$('#animal_type_id').on("select2:select", function(e) { 
								
			// Очистка старых значений ()
			$('#breed_id').empty().trigger("change");

			// Установка select2 для пород
			setbreeds();
				
		});
			
			
		// Установка select2 для динамического поиска пород
		function setbreeds () {
				
			// https://laraget.com/blog/select2-and-laravel-ajax-autocomplete - пример
			$('#breed_id').select2({	
				//minimumInputLength: 2,
				placeholder: "Выберите значение",
				theme: "bootstrap-5",
				allowClear: true,						
				ajax: {
					url: '/guides/animalbreed_search',
					dataType: 'json',
					delay: 300,
						
					data: function (params) {
							
						return {
							q: $.trim(params.term),
							animal: $('#animal_type_id').val(),
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
	
		}
		
		
		// Заполнение данных - услуги				
		$('.service_list').select2({
			minimumInputLength: 3,		
			theme: "bootstrap-5",
			language: {
				inputTooShort: function() {
					return 'Введите не менее 3-х символов';	
				}
			},	
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
		

		
		// Установка списков и фильтров
		$(document).ready(function () {
									
			settypes ();

			// Слушатели выбора списков в фильтре приемов
			
			$('#client_id').on("select2:select", function(e) { search_patients(); });
		
			$('#client_id').on("select2:unselect", function (e) { search_patients(); });
			
			$('#animal_type_id').on("select2:select", function(e) { search_patients(); });
		
			$('#animal_type_id').on("select2:unselect", function (e) { search_patients(); });
			
			$('#breed_id').on("select2:select", function(e) { search_patients(); });
		
			$('#breed_id').on("select2:unselect", function (e) { search_patients(); });
			
			$('#sex_id').on("select2:select", function(e) { search_patients(); });
		
			$('#sex_id').on("select2:unselect", function (e) { search_patients(); });
			
			$('#service').on("select2:select", function(e) { search_patients(); });
		
			$('#service').on("select2:unselect", function (e) { search_patients(); });
			
			$('#short_name').bind("input propertychange", function (evt) {
				// If it's the propertychange event, make sure it's the value that changed.
				if (window.event && event.type == "propertychange" && event.propertyName != "value")
					return;
				// Clear any previously set timer before setting a fresh one
				window.clearTimeout($(this).data("timeout"));
				$(this).data("timeout", setTimeout(function () {
					// Do your thing here
					if ($('#short_name').val().length >= 3 | $('#short_name').val().length == 0) {
						search_patients();
					}
				}, 500));
			});
			
			
			// Устанавливаем все значения фильтров !!!
			
			var data = getUrlVars();
			
			if (window.history.state) {
			
				if (!isEmpty(window.history.state.short_name)) {
					$('#short_name').val(window.history.state.short_name).trigger('change');
				}
				
				if (!isEmpty(window.history.state.visit_date_start)) {

					// Установка даты
					var dateParts = window.history.state.visit_date_start.split(".");
					var jsDate = new Date(dateParts[2], dateParts[1] - 1, dateParts[0]);
					
					$('#visit_date_start').datepicker().data('datepicker').selectDate(jsDate);					
					
				}
				
				if (!isEmpty(window.history.state.visit_date_end)) {
					
					// Установка даты
					var dateParts = window.history.state.visit_date_end.split(".");
					var jsDate = new Date(dateParts[2], dateParts[1] - 1, dateParts[0]);
					
					$('#visit_date_end').datepicker().data('datepicker').selectDate(jsDate);
										
				}
				
				setTimeout(function () {
						
					if (!isEmpty(window.history.state.client_id) & !isEmpty(window.history.state.client_data)) {
						var $client = $("<option selected='selected'></option>").val(window.history.state.client_id).text(window.history.state.client_data);
						$('#client_id').append($client).trigger('change');
					}
					
					if (!isEmpty(window.history.state.animal_type_id) & !isEmpty(window.history.state.animal_type_data)) {
						var $animal = $("<option selected='selected'></option>").val(window.history.state.animal_type_id).text(window.history.state.animal_type_data);
						$('#animal_type_id').append($animal).trigger('change');
					}
					
									
					if (!isEmpty(window.history.state.breed_id) & !isEmpty(window.history.state.breed_data)) {
						var $breed = $("<option selected='selected'></option>").val(window.history.state.breed_id).text(window.history.state.breed_data);
						$('#breed_id').append($breed).trigger('change');
					}
										
					if (!isEmpty(window.history.state.sex_id)) {
						$('#sex_id').val(window.history.state.sex_id).trigger('change');
					}
					
					if (!isEmpty(window.history.state.service_id) & !isEmpty(window.history.state.service_data)) {
						var $client = $("<option selected='selected'></option>").val(window.history.state.service_id).text(window.history.state.service_data);
						$('#service').append($client).trigger('change');
					}
					
					if (isEmpty(window.history.state.short_name) & isEmpty(window.history.state.animal_type_id) & isEmpty(window.history.state.breed_id) & isEmpty(window.history.state.sex_id) & isEmpty(window.history.state.client_id) & isEmpty(window.history.state.visit_date_start) & isEmpty(window.history.state.visit_date_end) & isEmpty(window.history.state.service)) {
						search_patients();
					} else {
						search_patients();
					}

				}, 50);
			
			} else {
				
				search_patients();
				
			}
			
		});
				
		
		function isEmpty(str) {
			return (!str || str.length === 0 );
		}
		
		
				
		// Метод получения параметров из url - не использую 
		function getUrlVars()
		{
			var vars = [], hash;
			var hashes = window.location.href.slice(window.location.href.indexOf('?') + 1).split('&');
			for(var i = 0; i < hashes.length; i++)
			{
				hash = hashes[i].split('=');

				if($.inArray(hash[0], vars)>-1)
				{
					vars[hash[0]]+=","+hash[1];
				}
				else
				{
					vars.push(hash[0]);
					vars[hash[0]] = hash[1];
				}
			}

			return vars;
		}
		
									
		</script>
			
</div>

@endsection
