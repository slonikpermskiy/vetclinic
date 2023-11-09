@extends('layout')
@section('title', 'Добавить собаку')
@section('content')


<script> 

	// Добавление данных клиента и пациента 
    function submitme(){ 
		
		if (!client_form_opened) {
			toastr.error('Не заполнены данные по владельцу');
		} else {
	
			// Очистка текстов-ошибок в span
			$('.error_response').html("&nbsp;");
		
			// Construct data string
			var dataString = $("#myForm3, #myForm2, #myForm").serialize();
		
			// Записываем данные
			$.ajax({
			url: '/addanimal/client/add',
			type: 'POST',
			data: dataString,
				
			success: function(data) {
								
				if($.isEmptyObject(data.error)){
					
					toastr.info(data.success); 
	
					// Переходим-перегружаем
					setTimeout(function () {
						window.location.href="{{URL::to('patientcard')}}" + "?patient_id=" + data.id;
					}, 300);
				
				}else{
					printErrorMsg(data.error);
				}
				},
			error: function(err) {	
				toastr.error('Ошибка');
				}
			});
		}
				
	}
	
	
	// Получение ошибок
	function printErrorMsg (msg) {
				
		if (msg.length > 0) {
			
		toastr.error(msg);
		
		} else {
		
		// Заполнение span текстами ошибок 
		$.each(msg,function(field_name,error){
			$('#error_'+field_name).html(error);
        })
		
		toastr.error('Ошибки заполнения полей');
		
		}
						
	}
		
		
	// Слушатель кнопки календарик
	function calendarinput1(){  	
		$("#date_of_birth").trigger("focus");
	}
	
	// Открыта или нет форма клиента
	var client_form_opened = false;

	
	// Открытие пустой формы
	function no_client_result_btn() {
			
		// Очистка данных
		$('#client_id').val('').trigger('change');
		$('#myForm2')[0].reset();				
		$('#data_ready').val(0).trigger('change');
		
		// Открытие формы
		$("#myForm2").removeClass('d-none');		
		
		client_form_opened = true;
	}
	
	

	// Получение данных по клиенту 
	function get_one_client_data(id) {
			
		$.ajax({
			url: '/addanimal/client/searchone',
			method:'GET',
			dataType:'json',
			data: {query:id},
			success: function(data) {
				
				if($.isEmptyObject(data.error)){
					
					$('#last_name').val(data.success.last_name);
					$('#first_name').val(data.success.first_name);
					$('#middle_name').val(data.success.middle_name);
					$('#city').val(JSON.parse(data.success.address)['city']);
					$('#address').val(JSON.parse(data.success.address)['address']);
					$('#phone').val(data.success.phone);
					$('#phoneinfo').val(JSON.parse(data.success.phonemore)['phoneinfo']);
					$('#phone2').val(JSON.parse(data.success.phonemore)['phone2']);
					$('#phoneinfo2').val(JSON.parse(data.success.phonemore)['phoneinfo2']);
					$('#email').val(data.success.email);
					if (data.success.data_ready == 1) {
						$('#data_ready_check').prop('checked', true);
						$('#data_ready').val('1').trigger('change'); 
					} else {
						$('#data_ready_check').prop('checked', false);
						$('#data_ready').val('0').trigger('change'); 
					}
					$('#comments').val(data.success.comments);
					
				}else{
					no_client_result();
				}
				
			},
				error: function(err) {	
					no_client_result();
				}
			});
	}
	
	

	// Слушатели Toggle
	$(document).ready(function(){
				
		$('#data_ready_check').change(function() { 		
			if($('#data_ready_check').is(':checked')) { 
				$('#data_ready').val('1').trigger('change');  
            } else {
				$('#data_ready').val('0').trigger('change');
            }				
        }); 
		
		$('#aprox_date_check').change(function() { 		
			if($('#aprox_date_check').is(':checked')) { 
				$('#aprox_date').val('1').trigger('change');  
            } else {
				$('#aprox_date').val('0').trigger('change');
            }				
        });
		
		$('#rip_check').change(function() { 		
			if($('#rip_check').is(':checked')) { 
				$('#rip').val('1').trigger('change');  
            } else {
				$('#rip').val('0').trigger('change');
            }				
        });
		
		// Маски ввода		
		$("#phone").mask("+7(999) 999-9999");
		
		$("#phone2").mask("+7(999) 999-9999");
		
    });
	
	
</script>


<style>

.form-control:focus {
	border-color: #8DB2EC;   
	box-shadow: 0 0 1px 1px #8DB2EC !important;
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

.datepicker {
    z-index: 1060 !important; /* has to be larger than 1050 */
}


</style>

<div name="central_area" id="central_area" class="d-none border bg-white shadow rounded p-4">	
		
	<div class="border-bottom py-2">
		<h3 class="text-body">Владелец</h3>	
	</div>	
	
	<form autocomplete="off" id="myForm3">
	@csrf
		<div class="row pt-4">
			<div class="col-lg px-2">			
				<div class="form-group">
					<label class="control-label font-weight-bold" for="client_id">Найти владельца</label>	
					<div class="d-lg-flex justify-content-left align-items-lg-center">					
						<div class="col-lg-6 mb-2">	
							<select name="client_id" id="client_id" class="form-select-lg js-example-basic-single block" style="width: 100%;" placeholder="Не выбрано" data-search="true">
								<option></option>
							</select>
						</div>		
						<div name="new_client_btn" id="new_client_btn" class="d-none col-lg px-lg-4 mb-2 d-flex justify-content-left align-items-lg-center">
							<div class="" align="center"><button onclick="no_client_result_btn()" type="button" class="btn btn-secondary">Добавить</button></div>
						</div>
					</div>
				</div>					
			</div>
		</div>
	</form>
							
	<form autocomplete="off" id="myForm2" class="d-none">
	@csrf
															
		<div class="mt-2 mb-2">
		
			@include('client_info_card')
		
		</div>
																													
	</form>	

	
	<div class="border-bottom py-2 mt-2">
		<h3 class="text-body">Пациент</h3>	
	</div>	
		
			
	<form id="myForm" autocomplete="off">	
	@csrf

		<div class="pt-4 mb-3">
		
			@include('patient_info_card')
		
		</div>

	</form>	

	<div class="d-lg-flex flex-row mb-4">	

		<div class=""><button type="button" onclick="submitme()" class="btn btn-primary" id="MyButton">Сохранить</button></div>

	</div>	


	<script>


		// Закрытие всех открытых popup при начале скрола страницы
		$(document).scroll(function() {
			// datepicker
			$('#date_of_birth').datepicker("hide");
			$('#date_of_birth').blur();
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
				
				
											
		// Заполнение данных - тип животного				
		function settypes () {
			$.ajax({
				url: '/guides/animaltype_search',
				method:'GET',
				dataType:'json',
				success: function(data) {
					$('#animal_type_id').select2({ 
					placeholder: "Выберите значение",
					theme: "bootstrap-5",
					allowClear: true,
					data: data 
					}).on('select2:unselecting', function() {
						$(this).data('unselecting', true);
						$('#breed_id').empty().trigger("change");
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
									
			// Очистка старых значений
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
							page: ''
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
				
								
		function setcolors () {
			// Установка select2 для цветов				
			$.ajax({
				url: '/guides/animalcolor_search',
				method:'GET',
				dataType:'json',
				success: function(data) {
					$('#color_id').select2({ 
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
						//toastr.error('Ошибка загрузки цветов');
					}
			});
		}


		// Custom результат для Select2
		function formatResult (data) {
			if (!data.id) {
				return data.text;
			}
			if (data.phone) {
				var $data = $('<span>' + data.text + '</span> <br> <div class="text-xs fst-italic">' + data.phone + '</div>');
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
					$("#new_client_btn").addClass('d-none');
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
							
					// Скрываем поля ввода
					$("#myForm2").addClass('d-none');

					client_form_opened = false;
							
					// Очистка данных
					$('#myForm2')[0].reset();				
					$('#data_ready').val(0).trigger('change');
					$('#client_id').val('').trigger('change');
							
					// Показываем-скрываем кнопку
					if(data.length == 0 && $('#client_id').data("select2").dropdown.$search.val().length >= 3) {
						$("#new_client_btn").removeClass('d-none');								
					} else {
						$("#new_client_btn").addClass('d-none');	
					}
								
					return {
						results: data
					};							
				},
			}
		}).on('select2:unselecting', function() {
			$(this).data('unselecting', true);
					
			// Скрываем поля ввода
			$("#myForm2").addClass('d-none');

			client_form_opened = false;
					
			// Очистка данных формы
			$('#myForm2')[0].reset();				
			$('#data_ready').val(0).trigger('change');	
					
		}).on('select2:opening', function(e) {
			if ($(this).data('unselecting')) {
				$(this).removeData('unselecting');
				e.preventDefault();
			}
		});
				
				
		// Слушатель выбора клиента
		$('#client_id').on("select2:select", function(e) { 
			if ($('#client_id').val() != null) {
				$("#myForm2").removeClass('d-none');
				client_form_opened = true;
				get_one_client_data($('#client_id').val());
			} else {
						
			}
		});
		
		
		// Активация DatePicker
		$('#date_of_birth').datepicker({
			language: 'ru',
			autoClose: true,
			clearButton: true,
		});


		// Dadata (все методы ниже)
		var token = "bc755f25f94e8461132a04563fbaefa476e4e143";

		function setConstraints(sgt, kladr_id) {
		  var restrict_value = false;
		  var locations = null;
		  if (kladr_id) {
			locations = { kladr_id: kladr_id };
			restrict_value = true;
		  }
		  sgt.setOptions({
			constraints: {
			  locations: locations
			},
			restrict_value: restrict_value
		  });
		}

		function enforceCity(suggestion) {
		  var sgt = $("#address").suggestions();
		  sgt.clear();
		  if (suggestion) {
			setConstraints(sgt, suggestion.data.kladr_id);
		  } else {
			setConstraints(sgt, null);
		  }
		}

		function restrictAddressValue(suggestion) {
		  var citySgt = $("#city").suggestions();
		  var addressSgt = $("#address").suggestions();
		  if (!citySgt.currentValue) {
			citySgt.setSuggestion(suggestion);
			var city_kladr_id = suggestion.data.kladr_id.substr(0, 13);
			setConstraints(addressSgt, city_kladr_id);
		  }
		}

		function formatSelected(suggestion){
		  var addressValue = makeAddressString(suggestion.data); 
		  return addressValue;
		}

		function makeAddressString(address){
		  return join([
			address.street_with_type,
			join([address.house_type, address.house,
				  address.block_type, address.block], " "),
			join([address.flat_type, address.flat], " ")
		  ]);
		}

		function join(arr /*, separator */) {
		  var separator = arguments.length > 1 ? arguments[1] : ", ";
		  return arr.filter(function(n){return n}).join(separator);
		}

		$("#city").suggestions({
		  token: token,
		  type: "ADDRESS",
		  bounds: "city-settlement",
		  hint: 'Выберите вариант',
		  geoLocation: false,
		  onSelect: enforceCity,
		  onSelectNothing: enforceCity
		});

		$("#address").suggestions({
		  token: token,
		  type: "ADDRESS",
		  hint: 'Выберите вариант',
		  onSelect: restrictAddressValue,
		  formatSelected: formatSelected
		});

		$("#city").suggestions().fixData();
		
		
		// Установка списков
		$(document).ready(function () {
			settypes ();
			setcolors ();
		});
		
	</script>
	
	
			
</div>

@endsection