@extends('layout')
@section('title', 'Карточка пациента')
@section('content')


<script> 

	$(document).ready(function () {
		
		// Удаляем лишние фото
		@php
			echo (new App\Http\Controllers\PatientCard)->deleteNoNeededPhotos($patient_id);
		@endphp

							
		get_one_client_patient_data({{ $patient_id }});
		
		// Список визитов
		show_visits_list();
		
				
		// Слушатель открытия модального окна - изменить данные собаки
		document.getElementById('change_dog_data').addEventListener('shown.bs.modal', function () {	
			
		});
		
		
		// Слушатель открытия модального окна - удалить собаку
		document.getElementById('delete_dog').addEventListener('shown.bs.modal', function () {	
			
			// Закрываем открытые визиты, исследования, анализы, счета
			close_visit();
			close_research();
			close_analysis();
			close_bill();
			
		});
		
		
		// Слушатель открытия модального окна - изменить данные клиента
		document.getElementById('change_client_data').addEventListener('shown.bs.modal', function () {	
						
		});
		
		
		// Слушатель открытия модального окна - новый клиент
		document.getElementById('new_client').addEventListener('shown.bs.modal', function () {
			
			// Закрываем открытые визиты, исследования, анализы, счета
			close_visit();
			close_research();
			close_analysis();
			close_bill();
			
			// Очистка текстов-ошибок в span
			$('#new_client').find('.error_response').html("&nbsp;");
			$('#new_client').find('#myForm3')[0].reset();
			$('#new_client').find('#data_ready_check_2').prop('checked', false);

			$('#new_client').find('input#patient_id').val({{ $patient_id }});	
				
		});
		
		
		// Слушатель открытия модального окна - сменить клиента
		document.getElementById('change_client').addEventListener('shown.bs.modal', function () {
			
			// Закрываем открытые визиты, исследования, анализы, счета
			close_visit();
			close_research();
			close_analysis();
			close_bill();
			
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
			
			
			// Очистка текстов-ошибок в span
			$('#change_client').find('.error_response').html("&nbsp;");

			$('#change_client').find('input#patient_id').val({{ $patient_id }});

			$('#change_client').find('#client_id').val(0).trigger('change');
				
		});
		
		
		// Вкладка визиты открыта
		$('#visit-tab').click(function(){
			
			if (!visits_opened) {
			if (visitopened != '') {
				open_visit(visitopened);
			} else {
				show_visits_list();
			}
			}

		});
		
		
		// Вкладка исследования открыта
		$('#research-tab').click(function(){
			
			if (researchopened != '') {
				open_research(researchopened);
			} else {
				show_researches_list();
			}
		});
		
		
		// Вкладка анализы открыта
		$('#analisys-tab').click(function(){
			
			if (analysisopened != '') {
				open_analysis(analysisopened);
			} else {
				show_analysis_list();
			}
		});
		
		
		// Вкладка вакцины открыта
		$('#vacine-tab').click(function(){		
			show_vacine_list();
		});
		
		
		// Вкладка фото открыта
		$('#photo-tab').click(function(){
			show_photo_notvisit();
		});
		
		
		// Вкладка счета открыта
		$('#bills-tab').click(function(){
			if (billopened != '') {
				open_bill(billopened);
			} else {
				show_bill_list();
			}
		});

		
			
		// Слушатели Toggle
		$('#new_client').find('#data_ready_check_2').change(function() { 		
			if($('#new_client').find('#data_ready_check_2').is(':checked')) { 
				$('#new_client').find('#data_ready_2').val('1').trigger('change');  
            } else {
				$('#new_client').find('#data_ready_2').val('0').trigger('change');
            }				
        }); 
		
		$('#change_client_data').find('#data_ready_check').change(function() { 		
			if($('#change_client_data').find('#data_ready_check').is(':checked')) { 
				$('#change_client_data').find('#data_ready').val('1').trigger('change');  
            } else {
				$('#change_client_data').find('#data_ready').val('0').trigger('change');
            }				
        });
		
		$('#aprox_date_check').change(function() { 		
			if($('#aprox_date_check').is(':checked')) { 
				$('#change_dog_data').find('input#aprox_date').val('1').trigger('change'); 
            } else {
				$('#change_dog_data').find('input#aprox_date').val('0').trigger('change'); 
            }				
        });
		
		$('#rip_check').change(function() { 		
			if($('#rip_check').is(':checked')) { 
				$('#change_dog_data').find('input#rip').val('1').trigger('change');  
            } else {
				$('#change_dog_data').find('input#rip').val('0').trigger('change');  
            }				
        });
				
		
		// Создание-инициализация графика веса
		var myChart;		
		create_graph(null, null);
		
		
		// Маски ввода
		
		//$('#visit_date').mask('99.99.9999');
		
		$('.phone').mask("+7(999) 999-9999");
		
	});
	
	
	// Открытие диалога - изменить данные собаки
	function change_dog_data_dialog() {
		
		// Закрываем открытые визиты, исследования, анализы, счета
		close_visit();
		close_research();
		close_analysis();
		close_bill();
		

		// Установка select2 для типов животных
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
				
				// Установка значения
				$('#change_dog_data').find('#animal_type_id').val(animal_type_id).trigger('change');		
				
				// Установка select2 для пород
				setbreeds();
			},
				error: function(err) {	
					//toastr.error('Ошибка загрузки типов животных');
				}
		});
			
		
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
				
				// Установка значения
				$('#change_dog_data').find('#color_id').val(color_id).trigger('change');	
				
			},
				error: function(err) {	
					//toastr.error('Ошибка загрузки цветов');
				}
		});
		
		
		// Очистка текстов-ошибок в span
		$('#change_dog_data').find('.error_response').html("&nbsp;");
		
		$('#change_dog_data').find('input#patient_id').val({{ $patient_id }});
		
		$('#change_dog_data').find('input#short_name').val(short_name);
		$('#change_dog_data').find('input#full_name').val(full_name);
			
		if (sex_id == 1) {
			$('#change_dog_data').find('#sex_id').val('1').trigger('change');			
		} else if (sex_id == 2) {
			$('#change_dog_data').find('#sex_id').val('2').trigger('change');				
		}
		
		// Установка даты
		if (birth_date) {
		var dateParts = birth_date.split(".");
		var jsDate = new Date(dateParts[2], dateParts[1] - 1, dateParts[0]);
					
		$('#change_dog_data').find('input#date_of_birth').datepicker().data('datepicker').selectDate(jsDate);
		}
							
		if (rip == 1) {
			$('#change_dog_data').find('#rip_check').prop('checked', true);
			$('#change_dog_data').find('#rip').val('1').trigger('change');
		} else {
			$('#change_dog_data').find('#rip_check').prop('checked', false);
			$('#change_dog_data').find('#rip').val('0').trigger('change');
		}
		if (aprox_date == 1) {
			$('#change_dog_data').find('#aprox_date_check').prop('checked', true);
			$('#change_dog_data').find('#aprox_date').val('1').trigger('change');
		} else {
			$('#change_dog_data').find('#aprox_date_check').prop('checked', false);
			$('#change_dog_data').find('#aprox_date').val('0').trigger('change');
		}
		$('#change_dog_data').find('input#tatoo').val(tatoo);
		$('#change_dog_data').find('input#chip').val(chip);
		if (castrated == 2) {
			$('#change_dog_data').find('#castrated').val('2').trigger('change');
		} else if (castrated == 1) {						
			$('#change_dog_data').find('#castrated').val('1').trigger('change');
		}
		$('#change_dog_data').find('#additional_info').val(additional_info);
		
		
		setTimeout(function () {

			var $breed = $("<option selected='selected'></option>").val(breed_id).text(anymal_breed);
			$('#change_dog_data').find('#breed_id').append($breed).trigger('change');						
						
			$('#change_dog_data').modal('show');
			
		}, 300);
				
	}
	
	
	// Открытие диалога - изменить данные клиента
	function change_client_data_dialog() {
		
		// Закрываем открытые визиты, исследования, анализы, счета
		close_visit();
		close_research();
		close_analysis();
		close_bill();
		
		// Очистка текстов-ошибок в span
		$('#change_client_data').find('.error_response').html("&nbsp;");
		
		$('#change_client_data').find('input#owner_id').val(owner_id);
		
		$('#change_client_data').find('input#last_name').val(last_name);
		
		$('#change_client_data').find('input#first_name').val(first_name);
		
		$('#change_client_data').find('input#middle_name').val(middle_name);
		
		$('#change_client_data').find('input#city').val(city);
		
		$('#change_client_data').find('input#address').val(address);
		
		$('#change_client_data').find('input#phone').val(phone);
		
		$('#change_client_data').find('input#phoneinfo').val(phoneinfo);
		
		$('#change_client_data').find('input#phone2').val(phone2);
		
		$('#change_client_data').find('input#phoneinfo2').val(phoneinfo2);
		
		$('#change_client_data').find('input#email').val(email);
		
		if (data_ready == 1) {
			$('#change_client_data').find('#data_ready_check').prop('checked', true);
			$('#change_client_data').find('#data_ready').val('1').trigger('change');
		} else {
			$('#change_client_data').find('#data_ready_check').prop('checked', false);
			$('#change_client_data').find('#data_ready').val('0').trigger('change');
		}
		
		$('#change_client_data').find('#comments').val(comments);
		
		setTimeout(function () {

			$('#change_client_data').modal('show');
			
		}, 300);
		
	}


	var short_name = '';
	var full_name = '';
	var animal_type_id = '';
	var breed_id = '';
	var anymal_breed = '';
	var color_id = '';
	var sex_id = '';
	var birth_date = '';
	var aprox_date = '';
	var tatoo = '';
	var chip = '';
	var rip = '';
	var castrated = '';	
	var additional_info = '';
	
	
	var owner_id = '';
	var last_name = '';
	var first_name = '';
	var middle_name = '';
	var city = '';
	var address = '';
	var phone = '';
	var phoneinfo = '';	
	var phone2 = '';
	var phoneinfo2 = '';
	var email = '';
	var data_ready = '';
	var comments = '';
	
	
	
	// Получение данных
	function get_one_client_patient_data(id) {
			
		// Данные по пациенту
		$.ajax({

			url: '/patientcard/searchpatient',
			method:'GET',
			dataType:'json',
			data: {query:id},
			success: function(data) {
				
				if($.isEmptyObject(data.error)){
					
					$('#short_name_small_card').html(data.success.short_name);
					$('#short_name_fullcard').html(data.success.short_name);
					
					short_name = data.success.short_name;
					
					full_name = data.success.full_name;
										
					if (data.success.full_name) {
						$('#full_name_fullcard').html(data.success.full_name);						
					} else {
						$('#full_name_fullcard').html('Нет');
					}
					
					additional_info = data.success.additional_info;
										
					if (data.success.additional_info) {
						$('#patient_info').html(data.success.additional_info);						
						$('#patient_info2').html(data.success.additional_info);
							
						if (data.success.rip == 1) {
							$('#patient_info').removeClass('mt-0');
							$('#patient_info2').removeClass('mt-0');
							$('#patient_info').addClass('mt-1');
							$('#patient_info2').addClass('mt-1');
						} else {
							$('#patient_info').removeClass('mt-1');
							$('#patient_info2').removeClass('mt-1');
							$('#patient_info').addClass('mt-0');
							$('#patient_info2').addClass('mt-0');
						}
						
						$('#patient_info_fullcard').html(data.success.additional_info);	
						$('#patient_info_fullcard').addClass('text-danger');
						$('#patient_info_fullcard').addClass('fst-italic');
					
					} else {
						$('#patient_info_fullcard').removeClass('text-danger');
						$('#patient_info_fullcard').removeClass('fst-italic');
						$('#patient_info').addClass('d-none');
						$('#patient_info2').addClass('d-none');
						$('#patient_info2_container').addClass('d-none');
						$('#patient_info_fullcard').html('Нет');						
					}
					
					animal_type_id = data.success.animal_type_id;
					
					if (data.anymal_type) {
						$('#anymal_type_small_card').html(data.anymal_type);
						$('#anymal_type_small_card').removeClass('d-none');
						$('#anymal_type_fullcard').html(data.anymal_type);		
					} else {
						$('#anymal_type_small_card').addClass('d-none');
						$('#anymal_type_fullcard').html('Нет');
					}
					
	
					breed_id = data.success.breed_id;
					anymal_breed = data.anymal_breed;
					
					if (data.anymal_breed) {
						$('#anymal_breed_small_card').html(data.anymal_breed);
						$('#anymal_breed_small_card').removeClass('d-none');
						$('#anymal_breed_fullcard').html(data.anymal_breed);
					} else {
						$('#anymal_breed_small_card').addClass('d-none');
						$('#anymal_breed_fullcard').html('Нет');
					}
					
					color_id = data.success.color_id;
					
					if (data.anymal_color) {
						$('#anymal_color_small_card').html(data.anymal_color);
						$('#anymal_color_small_card').removeClass('d-none');
						$('#color_fullcard').html(data.anymal_color);	
					} else {
						$('#anymal_color_small_card').addClass('d-none');
						$('#color_fullcard').html('Нет');
					}
					
					tatoo = data.success.tatoo;
					
					if (data.success.tatoo) {
						$('#tatoo_fullcard').html(data.success.tatoo);		
					} else {
						$('#tatoo_fullcard').html('Нет');
					}
					
					chip = data.success.chip;
					
					if (data.success.chip) {
						$('#chip_fullcard').html(data.success.chip);		
					} else {
						$('#chip_fullcard').html('Нет');
					}
					
					if (data.age) {	
						if (data.success.aprox_date == 1) {
							$('#age_small_card').html(data.age + '<span class="text-danger fw-bold"> ?</span>');
							$('#age_fullcard').html(data.age + '<span class="text-danger fw-bold"> ?</span>');	
						} else {
							$('#age_small_card').html(data.age);
							$('#age_fullcard').html(data.age);
						}
						$('#age_small_card').removeClass('d-none');
					} else {
						$('#age_small_card').addClass('d-none');
						
						if (data.success.rip == 1) {
							$('#age_fullcard').html('R.I.P.');
						} else {
							$('#age_fullcard').html('Нет');
						}
					}
										
						
					rip = data.success.rip;
					
					if (data.success.rip == 1){
						$('#short_name_small_card').addClass('border');
						$('#short_name_small_card').addClass('border-2');
						$('#short_name_small_card').addClass('border-dark');
						$('#short_name_small_card').addClass('p-1');
						$('#short_name_small_card').addClass('rounded');
					} else {
						$('#short_name_small_card').removeClass('border');
						$('#short_name_small_card').removeClass('border-2');
						$('#short_name_small_card').removeClass('border-dark');
						$('#short_name_small_card').removeClass('p-1');
						$('#short_name_small_card').removeClass('rounded');
					}
					
					aprox_date = data.success.aprox_date;
					
					birth_date = data.birth_date;
					
					if (data.birth_date) {
						$('#birth_date_fullcard').html(data.birth_date);	
					} else {
						$('#birth_date_fullcard').html('Нет');
					}
					
					
					if (data.registration) {
						$('#registration_fullcard').html(data.registration);
					} else {
						$('#registration_fullcard').html('Нет');
					}
					
					
					castrated = data.success.castrated;
					
					if (data.success.castrated) {
								
						if (data.success.castrated == 2) {
							$('#castrated_fullcard').html('Да');					
						} else if (data.success.castrated == 1) {
							$('#castrated_fullcard').html('Нет');
						} else {
							$('#castrated_fullcard').html('Нет данных');
						}	
					} else {
						$('#castrated_fullcard').html('Нет данных');
					}
										
		
					// Вес
					if (data.last_weight_date && data.last_weight_date != null && data.last_weight_size != 0 && data.last_weight_size != 1) {
						$('#weight_fullcard').html('<a href="#" onclick="open_weigth_graph(); event.preventDefault();" class="">'+data.last_weight+' кг. </a>&nbsp;&nbsp;<span class="fst-italic">'+data.last_weight_date+'</span>');
					// Если только одно значение, то не показываем ссылку открытия графика
					} else if (data.last_weight_size == 1) {
						$('#weight_fullcard').html(data.last_weight+' кг.&nbsp;&nbsp;<span class="fst-italic">'+data.last_weight_date+'</span>');
					}else {
						$('#weight_fullcard').html('Нет данных');
					}
					
					
					// Последняя вакцинация
					if (data.last_vacine_date && data.last_vacine_date != null && data.last_vacine && data.last_vacine != null ) {
						$('#vactine_fullcard').html(data.last_vacine+'&nbsp;&nbsp;<span class="fst-italic">'+data.last_vacine_date+'</span>');
					}else {
						$('#vactine_fullcard').html('Нет данных');
					}
					
					
					// Диагнозы
					if (data.diagnosis != null) {
						$('#diagnosis_fullcard').html(data.diagnosis);
					} else {
						$('#diagnosis_fullcard').html('Нет данных');
					}
							
					sex_id = data.success.sex_id;
					
					if (data.success.sex_id) {
						
						if (data.success.sex_id == 1) {
							$('#anymal_sex_small_card').html('<img class="img-responsive" width="32" height="32" src="images/male.png">');
							$('#anymal_sex_small_card').removeClass('d-none');
							$('#anymal_sex_fullcard').html('Мужской');
						} else if (data.success.sex_id == 2) {
							$('#anymal_sex_small_card').html('<img class="img-responsive" width="32" height="32" src="images/female.png">');
							$('#anymal_sex_small_card').removeClass('d-none');
							$('#anymal_sex_fullcard').html('Женский');
						} else {
							$('#anymal_sex_small_card').html('');
							$('#anymal_sex_small_card').addClass('d-none');
							$('#anymal_sex_fullcard').html('Нет');		
						}
					} else {
						$('#anymal_sex_small_card').html('');
						$('#anymal_sex_small_card').addClass('d-none');
						$('#anymal_sex_fullcard').html('Нет');
					}
					
						
						// Данные по клиенту
						$.ajax({
						url: '/addanimal/client/searchone',
						method:'GET',
						dataType:'json',
						data: {query:data.success.client_id},
						success: function(data) {
							
							if($.isEmptyObject(data.error)){
								
								var last = '';
								var first = '';
								var middle = '';
								
								if (data.success.last_name) {
									last = data.success.last_name;
								}
								
								if (data.success.first_name) {
									first = data.success.first_name;
								}
								
								if (data.success.middle_name) {
									middle = data.success.middle_name;
								}
								
								var clientname = last + ' ' + first + ' ' + middle;
								
								if (data.success.last_name) {
									$('#client_name').html(clientname);
									$('#client_name_container').removeClass('d-none');
								} else {
									$('#client_name_container').addClass('d-none');
								}
								
								comments = data.success.comments;
								
								if (data.success.comments) {
									$('#client_info').html(data.success.comments);
									$('#client_info_container').removeClass('d-none');
									$('#client_info_container2').removeClass('d-none');
								} else {
									$('#client_info').html('Нет данных');
									$('#client_info_container').addClass('d-none');
									$('#client_info_container2').addClass('d-none');
								}
								
								owner_id = data.success.client_id;
								
								last_name = data.success.last_name;
								
								if (data.success.last_name) {
									$('#last_name_fullcard').html(data.success.last_name);
								} else {
									$('#last_name_fullcard').html('Нет');
								}
								
								first_name = data.success.first_name;
								
								if (data.success.first_name) {
									$('#first_name_fullcard').html(data.success.first_name);
								} else {
									$('#first_name_fullcard').html('Нет');
								}
								
								middle_name = data.success.middle_name;
								
								if (data.success.middle_name) {
									$('#middle_name_fullcard').html(data.success.middle_name);
								} else {
									$('#middle_name_fullcard').html('Нет');
								}
								
								city = JSON.parse(data.success.address)['city'];
								
								if (JSON.parse(data.success.address)['city']) {
									$('#city_fullcard').html(JSON.parse(data.success.address)['city']);
								} else {
									$('#city_fullcard').html('Нет');
								}
								
								address = JSON.parse(data.success.address)['address'];
								
								if (JSON.parse(data.success.address)['address']) {
									$('#address_fullcard').html(JSON.parse(data.success.address)['address']);
								} else {
									$('#address_fullcard').html('Нет');
								}
								
								phone = data.success.phone;
								
								if (data.success.phone) {
									$('#phone_fullcard').html(data.success.phone);
								} else {
									$('#phone_fullcard').html('Нет');
								}
																
								phoneinfo = JSON.parse(data.success.phonemore)['phoneinfo'];
								
								if (JSON.parse(data.success.phonemore)['phoneinfo']) {
									$('#phoneinfo_fullcard').html(JSON.parse(data.success.phonemore)['phoneinfo']);
								} else {
									$('#phoneinfo_fullcard').html('Нет');
								}
								
								phone2 = JSON.parse(data.success.phonemore)['phone2'];
								
								if (JSON.parse(data.success.phonemore)['phone2']) {
									$('#phone2_fullcard').html(JSON.parse(data.success.phonemore)['phone2']);
								} else {
									$('#phone2_fullcard').html('Нет');
								}
								
								phoneinfo2 = JSON.parse(data.success.phonemore)['phoneinfo2'];
								
								if (JSON.parse(data.success.phonemore)['phoneinfo2']) {
									$('#phoneinfo2_fullcard').html(JSON.parse(data.success.phonemore)['phoneinfo2']);
								} else {
									$('#phoneinfo2_fullcard').html('Нет');
								}
								
								email = data.success.email;
								
								if (data.success.email) {
									$('#email_fullcard').html(data.success.email);
								} else {
									$('#email_fullcard').html('Нет');
								}
								
								data_ready = data.success.data_ready;
								
								if (data.success.data_ready) {
									
									if (data.success.data_ready == 1) {
										$('#data_ready_fullcard').html('Согласен на обработку');
									} else {
										$('#data_ready_fullcard').html('Не согласен на обработку');
									}
									
								} else {
									$('#data_ready_fullcard').html('Не согласен на обработку');
								}
								
							}else{
								toastr.error(data.error);
							}
							
						},
							error: function(err) {	
								//toastr.error(err);
							}
						});
							
				}else{
					toastr.error(data.error);
				}
				
			},
				error: function(err) {	
					//toastr.error(err);
				}
			});
	}
	
	
	function show_client_info() {
		toastr.error($('#client_info').html());
	}


	// Открыта или нет карточка
	var patient_card_opened = false;

	function collapse_expand(){ 
	
		// Раскрываем-скрываем карточку
		if(!patient_card_opened) {
			patient_card_opened = true;
			$("#short_card").addClass('d-none');	
			$("#full_card").removeClass('d-none');
			$('#collapse_expand_btn').html('Свернуть');			
		} else {
			patient_card_opened = false;
			$("#short_card").addClass('d-none');
			$("#full_card").addClass('d-none');			
			$("#short_card").removeClass('d-none');	
			$('#collapse_expand_btn').html('Развернуть');
		}
	
	}
	
		
	// Открыт или нет фильтр визитов
	var visit_filter_opened = false;

	function collapse_expand_visit_filter(){ 
	
		// Раскрываем-скрываем карточку
		if(!visit_filter_opened) {
			visit_filter_opened = true;	
			$("#visit_filter").removeClass('d-none');
			$('#visit_filter_btn').html('Скрыть фильтр');			
		} else {
			visit_filter_opened = false;
			$("#visit_filter").addClass('d-none');
			
			clear_form();	
		
			$('#visit_filter_btn').html('Открыть фильтр');
		}
	
	}
	
	
	
	// Открыт или нет фильтр исследований
	var research_filter_opened = false;

	function collapse_expand_research_filter(){ 
	
		// Раскрываем-скрываем карточку
		if(!research_filter_opened) {
			research_filter_opened = true;	
			$("#research_filter").removeClass('d-none');
			$('#research_filter_btn').html('Скрыть фильтр');			
		} else {
			research_filter_opened = false;
			$("#research_filter").addClass('d-none');
			
			clear_research_form();	

			$('#research_filter_btn').html('Открыть фильтр');
		}
	
	}
	
	
	
	// Открыт или нет фильтр анализов
	var analisys_filter_opened = false;

	function collapse_expand_analisys_filter(){ 
	
		// Раскрываем-скрываем карточку
		if(!analisys_filter_opened) {
			analisys_filter_opened = true;	
			$("#analisys_filter").removeClass('d-none');
			$('#analisys_filter_btn').html('Скрыть фильтр');			
		} else {
			analisys_filter_opened = false;
			$("#analisys_filter").addClass('d-none');
			
			clear_analysis_form();	

			$('#analisys_filter_btn').html('Открыть фильтр');
		}
	
	}
	
	
	// Открыт или нет фильтр счетов
	var bills_filter_opened = false;

	function collapse_expand_bills_filter(){ 
	
		// Раскрываем-скрываем карточку
		if(!bills_filter_opened) {
			bills_filter_opened = true;	
			$("#bills_filter").removeClass('d-none');
			$('#bills_filter_btn').html('Скрыть фильтр');			
		} else {
			bills_filter_opened = false;
			$("#bills_filter").addClass('d-none');
			
			clear_bills_form();	

			$('#bills_filter_btn').html('Открыть фильтр');
		}
	
	}
	
	

	// Открыты или нет приемы
	var visits_opened = false;
	
	function collapse_expand_visits(){ 
	
		// Раскрываем-скрываем прием
		if(!visits_opened) {
			visits_opened = true;
			$('.short_visit').addClass('d-none');	
			$('.full_visit').removeClass('d-none');
			$('#collapse_expand_visits').html('Кратко');			
		} else {
			visits_opened = false;
			$('.full_visit').addClass('d-none');
			$('.short_visit').removeClass('d-none');
			$('#collapse_expand_visits').html('Детально');
		}
	
	}
	
	
	// Изменение данных пациента 
    function change_patient_data(){ 
			
		// Очистка текстов-ошибок в span
		$('#change_dog_data').find('.error_response').html("&nbsp;");
	
		// Construct data string
		var dataString = $("#myForm").serialize();
	
		// Записываем данные
		$.ajax({
		url: '/patientcard/change_patient_data',
		type: 'POST',
		data: dataString,
			
		success: function(data) {
							
			if($.isEmptyObject(data.error)){
				
				toastr.info(data.success); 
				
				get_one_client_patient_data({{ $patient_id }});
				
				$('#change_dog_data').modal('toggle');
			
			}else{
				printErrorMsg(data.error, 'change_dog_data');
			}
			},
		error: function(err) {	
			toastr.error('Ошибка');
			}
		});
			
	}
	
	
	// Изменение данных клиента
    function change_client_data(){ 
			
		// Очистка текстов-ошибок в span
		$('#change_client_data').find('.error_response').html("&nbsp;");
	
		// Construct data string
		var dataString = $("#myForm2").serialize();
	
		// Записываем данные
		$.ajax({
		url: '/patientcard/change_client_data',
		type: 'POST',
		data: dataString,
			
		success: function(data) {
							
			if($.isEmptyObject(data.error)){
				
				toastr.info(data.success); 
				
				get_one_client_patient_data({{ $patient_id }});
				
				$('#change_client_data').modal('toggle');

				// Обновить данные
			
			}else{
				printErrorMsg(data.error, 'change_client_data');
			}
			},
		error: function(err) {	
			toastr.error('Ошибка');
			}
		});
			
	}
	
	
	// Новый клиент для пациента 
    function new_client(){ 
			
		// Очистка текстов-ошибок в span
		$('#new_client').find('.error_response').html("&nbsp;");
	
		// Construct data string
		var dataString = $("#myForm3").serialize();
	
		// Записываем данные
		$.ajax({
		url: '/patientcard/new_client',
		type: 'POST',
		data: dataString,
			
		success: function(data) {
							
			if($.isEmptyObject(data.error)){
				
				toastr.info(data.success); 
				
				get_one_client_patient_data({{ $patient_id }});
				
				$('#new_client').modal('toggle');
				
				$('#new_client').find('#myForm3')[0].reset();

				// Обновить данные
			
			}else{
				printErrorMsg(data.error, 'new_client');
			}
			},
		error: function(err) {	
			toastr.error('Ошибка');
			}
		});
			
	}
	
	
	// Изменить клиента для пациента 
    function change_client(){ 
			
		// Очистка текстов-ошибок в span
		$('#change_client').find('.error_response').html("&nbsp;");
	
		// Construct data string
		var dataString = $("#myForm4").serialize();
	
		// Записываем данные
		$.ajax({
		url: '/patientcard/change_client',
		type: 'POST',
		data: dataString,
			
		success: function(data) {
							
			if($.isEmptyObject(data.error)){
				
				toastr.info(data.success); 
				
				get_one_client_patient_data({{ $patient_id }});
				
				$('#change_client').modal('toggle');
				
				// Очистить select
				
				$('#change_client').find('#myForm4')[0].reset();

				// Обновить данные
			
			}else{
				printErrorMsg(data.error, 'change_client');
			}
			},
		error: function(err) {	
			toastr.error('Ошибка');
			}
		});
			
	}
	

	// Получение ошибок
	function printErrorMsg (msg, modal) {
				
		if (msg.length > 0) {
			
		toastr.error(msg);
		
		} else {
		
		// Заполнение span текстами ошибок 
		$.each(msg,function(field_name,error){
			$('#'+modal).find('#error_'+field_name).html(error);
        })
		
		toastr.error('Ошибки заполнения полей');
		
		}
						
	}
	
	
	// Удалить пациента
	function delete_patient () {
		
		var token;
		token='{{ csrf_token() }}';
		
		$.ajax({
			headers: {'X-CSRF-TOKEN': token},
			url: '/patientcard/delete_patient',
			method:'POST',
			dataType: 'json',
			data: {patient_id:{{ $patient_id }}},
			success: function(data) {
				
				if($.isEmptyObject(data.error)){					
					
					toastr.info(data.success);
					
					$('#delete_dog').modal('toggle');

					// Переходим-перегружаем
					setTimeout(function () {
						
						// На главную
						//window.location.href = "/";

						// Откуда пришли
						history.back();
						
					}, 300);
					
				}else{
					toastr.error(data.error);
				}
			},
				error: function(err) {	
					toastr.error('Ошибка');
				}
			});	
	}
	
		
		
	// Слушатель кнопки календарик
	function calendarinput1(){  	
		$("#date_of_birth").trigger("focus");
	}


	
	function upload_photo_notvisit(){ 
	
		$('#upload_photo_notvisit_btn').addClass('disabled');	
		$('#upload_photo_notvisit_btn').html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"> </span>&nbsp;&nbsp;Загрузка...');
	
		var formData = new FormData();
		
		var fileInput = document.getElementById("imagetoloadnotvisit");
		if (fileInput.files.length > 0) {
			formData.append("imagetoload", fileInput.files[0]);
		}
		
		if ({{ $patient_id }}) {
			formData.append("anymal_id", {{ $patient_id }});
		}
		
				
		formData.append("description", $('#descriptionnotvisit').val());		
				
		var token;
		token='{{ csrf_token() }}';
	
		// Записываем данные
		$.ajax({
			headers: {'X-CSRF-TOKEN': token},
			url: '/patientcard/upload_photo',
			type: 'POST',
			processData: false,
			contentType: false,
			data: formData,
			
			/*xhr: function(){
				var xhr = $.ajaxSettings.xhr(); // получаем объект XMLHttpRequest
				xhr.upload.addEventListener('progress', function(evt){ // добавляем обработчик события progress (onprogress)
					if(evt.lengthComputable) { // если известно количество байт
						// высчитываем процент загруженного
						var percentComplete = Math.ceil(evt.loaded / evt.total * 100);
						$('#upload_photo_notvisit_btn').html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"> </span>&nbsp;&nbsp;Загружено ' + percentComplete + '%');
					}
				}, false);
			return xhr;
			},*/
				
			success: function(data) {
				
				$('#upload_photo_notvisit_btn').text('Загрузить');
				$('#upload_photo_notvisit_btn').removeClass('disabled');	
				
				if($.isEmptyObject(data.error)){
					
					// Очистка текстов-ошибок в span
					$('#error_imagetoloadnotvisit').html("");
					
					$('#imagetoloadnotvisit').val('');	
					$('#descriptionnotvisit').val('');
					
					show_photo_notvisit();
					
				}else{
					$('#error_imagetoloadnotvisit').html(data.error);
				}
				},
			error: function(err) {	
			
				$('#upload_photo_notvisit_btn').text('Загрузить');
				$('#upload_photo_notvisit_btn').removeClass('disabled');	
				
				toastr.error('Ошибка');
				}
			});			
	}
	
	
	function show_photo_notvisit(){
		
		$('#uploaded_photo_notvisit_response').empty();
		
		if($('#uploaded_photo_notvisit_response').is(':empty') ||  !$.trim( $('#uploaded_photo_notvisit_response').html()).length) {			
			$('#uploaded_photo_notvisit_response').append("<div class='d-flex justify-content-center my-4'> <div class='spinner-border text-secondary' role='status'> <span class='visually-hidden'>Loading...</span> </div> </div>");			
		}
					
		setTimeout(function () {
			$.ajax({
				url: '/patientcard/get_photo_for_patient',
				method:'GET',
				dataType:'json',
				data: {anymal_id: {{ $patient_id }}},
				success: function(data) {
					
					$('#uploaded_photo_notvisit_response').empty();
					
					if($.isEmptyObject(data.error)){
					
						$('#uploaded_photo_notvisit_response').append(data.success);

					}else{
						toastr.error(data.error);
					}	
				},
					error: function(err) {

						$('#uploaded_photo_notvisit_response').empty();
						
						toastr.error('Ошибка');
					}
				});
			}, 300);

	}
	
	
	function delete_gallery_photo_dialog(id) {
		$('#delete_photo').find('#phototodelete_id').val('0');
		$('#delete_photo').find('#phototodelete_id').val(id);
		$('#delete_photo').modal('show');
	}
	
	
	
	function delete_gallery_photo() {
		 
		var id = $('#delete_photo').find('#phototodelete_id').val();
		
		var token;
		token='{{ csrf_token() }}';
		
		$.ajax({
			headers: {'X-CSRF-TOKEN': token},
			url: '/patientcard/delete_photo',
			method:'POST',
			dataType: 'json',
			data: {id: id},
			success: function(data) {
				
				if($.isEmptyObject(data.error)){					
					
					show_photo_notvisit();	

					$('#delete_photo').modal('toggle');					
					
				}else{
					toastr.error(data.error);
				}
			},
				error: function(err) {	
					toastr.error('Ошибка');
				}
			});	

	 }
	 
	 
	// Скопировать ссылку в буфер обмена
	function copy_url(imageurl) {
		$('#image_url').val(imageurl);
		var text = document.getElementById("image_url");  
		text.select();    
		document.execCommand("copy"); 
		toastr.info('Ссылка скопирована в буфер обмена');
	}
	
	
	
	// Создание графика веса
	function create_graph(dates, weights) {
	
		var ctx = document.getElementById('myChart');
		
		myChart = new Chart(ctx, {
				type: 'line',
				data: {
					labels: dates,
					datasets: [{
						data: weights,
						fill: false,
						borderColor: 'rgb(75, 192, 192)',
						tension: 0.1
					}]
				},
				options: {
					plugins: {
						legend: {
							display: false
						}
					}
				}
			});		
	}
	
	
	
	function open_weigth_graph() {
				
		$.ajax({
			url: '/patientcard/get_weights',			
			method:'GET',
			dataType:'json',
			data: {anymal_id: {{ $patient_id }}},
			success: function(data) {
				
				if($.isEmptyObject(data.error)){
		
					$('#weight_graph').modal('toggle');	
					
					myChart.destroy();
		
					create_graph(data.dates, data.weights);

					$('#weight_graph').modal('show');

				}else{
					toastr.error(data.error);
				}
				
			},
				error: function(err) {	
					//toastr.error(err);
				}
			});
	
	}
	
</script>
	
	
<!-- Скрипты для визитов -->
@include('visit_scripts')

<!-- Скрипты для исследований -->
@include('research_scripts')

<!-- Скрипты для анализов -->
@include('analysis_scripts')

<!-- Скрипты для вакцин -->
@include('vacine_scripts')

@php
$bill_page = 0;
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


</style>


<div name="central_area" id="central_area" class="d-none border bg-white shadow rounded p-4">	
				
	<div class="border border px-3 pt-3 rounded">
	
		
		<div class="d-flex align-items-center">								

			<div class="col-6 d-flex justify-content-start align-items-center">
				<button type="button" onclick="history.back();" class="btn btn-sm border-1 border-secondary text-secondary">&#8592 Назад</button>
			</div>

			<div class=" col-6 d-flex justify-content-end align-items-center" >								
				<button name="collapse_expand_btn" id="collapse_expand_btn"  type="button" onclick="collapse_expand()" class="btn btn-sm border-1 border-primary text-primary">Развернуть</button>
			</div>	
			
		</div>
											
		<div class="py-3" name="short_card" id="short_card">	
		
			<div class="d-lg-flex justify-content-lg-start align-items-lg-center">
									
				<div class="px-2">
					<div class="d-flex justify-content-lg-start position-relative align-items-center">
						<div name="short_name_small_card"  id='short_name_small_card' class="fs-6 fw-bolder text-break mt-1"></div>	
					</div>
					<div name="patient_info"  id='patient_info' class="d-flex d-lg-none justify-content-start align-items-center text-danger fst-italic fs-6 text-break"></div>										
				</div>
						
				<div name="anymal_type_small_card" id='anymal_type_small_card' class="fs-6 text-break px-2 mt-1"></div>
				
				<div name="anymal_breed_small_card"  id='anymal_breed_small_card' class="fs-6 text-break px-2 mt-1"></div>
				
				<div name="anymal_color_small_card"  id='anymal_color_small_card' class="fs-6 text-break px-2 mt-1"></div>
									
				<div name="anymal_sex_small_card"  id='anymal_sex_small_card' class="px-2 mt-1"></div>
				
				<div name="age_small_card"  id='age_small_card' class="fs-6 text-break px-2 mt-1"></div>

				<div name="client_name_container"  id='client_name_container' class="d-flex justify-content-lg-center align-items-lg-center">								

					<div name="client_name"  id='client_name' class="fs-6 text-break fw-bolder px-2 mt-1"></div>
					
					<div name="client_info_container"  id='client_info_container' class="d-none px-2 align-items-lg-center d-flex justify-content-center align-items-center" >						
						<button onclick="show_client_info()" class="btn btn-sm border-2 rounded-circle border-danger fw-weight-bold">
							&nbsp;!&nbsp;
						</button>				
						<div name="client_info"  id='client_info' class="d-none"></div>
					</div>
															
				</div>	

			</div>	
			
			<div name="patient_info2_container"  id='patient_info2_container' class="d-none d-lg-flex justify-content-start align-items-top text-danger fst-italic fs-6 text-break px-2">
				<div name="patient_info2"  id='patient_info2' class=""></div>										
			</div>
		
		</div>	
		
		
		<div class="py-3 d-none" name="full_card" id="full_card">
		
			<div class="row px-3">

				<div class="col-lg col-md-border px-0 pb-2 pb-lg-0">
								
					<div class="d-flex justify-content-lg-start align-items-lg-center me-lg-3">								

						<div class="col-11 d-flex justify-content-lg-start align-items-lg-center">
							<h5 class="text-body align-self-center">Пациент</h5>	
						</div>
		
						<div class=" col-1 align-items-lg-center d-flex justify-content-end align-items-center" >								

							<div class="btn-group dropstart">							  
								<a class="nav-link p-1" data-bs-toggle="dropdown" aria-expanded="false" href="#" role="button">	
									<img class="img-responsive" width="40" height="40" src="images/settings.png">
								</a>
								<ul class="dropdown-menu dropdown-1">
									<li><a class="dropdown-item" role="button" onclick="change_dog_data_dialog()">Изменить данные</a></li>
																		
									@php
									{{
										 $staff = \App\Staff::where('staff_id', Auth::user()->staff_id)->first();
										 $position = 0;
										 
										 if ($staff !== null) {
											$position = $staff->position;
										 }
										 
										
										if (Auth::user()->staff_id == 0 | $position ==1 | $position ==2) {
											echo('<li><a class="dropdown-item" role="button" data-bs-toggle="modal" data-bs-target="#delete_dog">Удалить</a></li>');
										}
									
									}}
									@endphp
									
								</ul>
							</div>
						</div>									
					</div>
					
					<div class="row">
						<div class="col-lg-4">
							<div class="form-group mb-2">
								<label class='control-label font-weight-bold text-break' for="short_name_fullcard">Кличка <span class="text-danger"></span></label>
								<div name="short_name_fullcard"  id='short_name_fullcard' class="text-break">&nbsp;</div>
							</div>
						</div>
						<div class="col-lg-8">
							<div class="form-group mb-2">
								<label class='control-label font-weight-bold text-break' for="full_name_fullcard">Полное имя <span class="text-danger"></span></label>
								<div name="full_name_fullcard"  id='full_name_fullcard' class="text-break">&nbsp;</div>
							</div>	
						</div>	
					</div>

					<div class="row">
						<div class="form-group mb-2">
							<label class='control-label font-weight-bold text-break' for="patient_info_fullcard">Дополнительная информация <span class="text-danger"></span></label>
							<div name="patient_info_fullcard"  id='patient_info_fullcard' class="text-break">&nbsp;</div>
						</div>
					</div>
		
					<div class="row">
					
						<div class="col-lg-4">
						<div class="form-group mb-2">
							<label class='control-label font-weight-bold text-break' for="short_name_fullcard">Вид <span class="text-danger"></span></label>
							<div name="anymal_type_fullcard"  id='anymal_type_fullcard' class="text-break">&nbsp;</div>
						</div>
						</div>
						
						<div class="col-lg-3">
						<div class="form-group mb-2">
							<label class='control-label font-weight-bold text-break' for="anymal_sex_fullcard">Пол <span class="text-danger"></span></label>
							<div name="anymal_sex_fullcard"  id='anymal_sex_fullcard' class="text-break">&nbsp;</div>
						</div>
						</div>
						
						<div class="col-lg-4">
						<div class="form-group mb-2">
							<label class='control-label font-weight-bold text-break' for="anymal_breed_fullcard">Порода <span class="text-danger"></span></label>
							<div name="anymal_breed_fullcard"  id='anymal_breed_fullcard' class="text-break">&nbsp;</div>
						</div>
						</div>							
					
					</div>
					
					<div class="row">
						<div class="col-lg-4">
							<div class="form-group mb-2">
								<label class='control-label font-weight-bold text-break' for="color_fullcard">Окрас <span class="text-danger"></span></label>
								<div name="color_fullcard"  id='color_fullcard' class="text-break">&nbsp;</div>
							</div>
						</div>
						<div class="col-lg-3">
							<div class="form-group mb-2">
								<label class='control-label font-weight-bold text-break' for="tatoo_fullcard">Клеймо <span class="text-danger"></span></label>
								<div name="tatoo_fullcard"  id='tatoo_fullcard' class="text-break">&nbsp;</div>
							</div>
						</div>
						<div class="col-lg-4">
							<div class="form-group mb-2">
								<label class='control-label font-weight-bold text-break' for="chip_fullcard">Чип <span class="text-danger"></span></label>
								<div name="chip_fullcard"  id='chip_fullcard' class="text-break">&nbsp;</div>
							</div>	
						</div>	
					</div>
					
					<div class="row">
						<div class="col-lg-4">
							<div class="form-group mb-2">
								<label class='control-label font-weight-bold text-break' for="birth_date_fullcard">Дата рождения <span class="text-danger"></span></label>
								<div name="birth_date_fullcard"  id='birth_date_fullcard' class="text-break">&nbsp;</div>
							</div>
						</div>
						<div class="col-lg-3">
							<div class="form-group mb-2">
								<label class='control-label font-weight-bold text-break' for="age_fullcard">Возраст <span class="text-danger"></span></label>
								<div name="age_fullcard"  id='age_fullcard' class="text-break">&nbsp;</div>
							</div>
						</div>
						<div class="col-lg-4">
							<div class="form-group mb-2">
								<label class='control-label font-weight-bold text-break' for="registration_fullcard">Регистрация<span class="text-danger"></span></label>
								<div name="registration_fullcard"  id='registration_fullcard' class="text-break">&nbsp;</div>
							</div>	
						</div>	
					</div>
					
					<div class="row">
						<div class="col-lg-4">
							<div class="form-group mb-2">
								<label class='control-label font-weight-bold text-break' for="weight_fullcard">Вес <span class="text-danger"></span></label>
								<div name="weight_fullcard" id='weight_fullcard' class="text-break justify-content-start align-items-center">&nbsp;</div>
							</div>								
						</div>
						<div class="col-lg-8">
							<div class="form-group mb-2">
								<div class="text-truncate">
									<label class='control-label font-weight-bold text-break' for="castrated_fullcard">Кастрирован / Стерилизованная <span class="text-danger"></span></label>
								</div>	
								<div name="castrated_fullcard"  id='castrated_fullcard' class="text-break">&nbsp;</div>
							</div>	
						</div>	
					</div>
				
					<div class="row">
						<div class="form-group mb-2">
							<label class='control-label font-weight-bold text-break' for="diagnosis_fullcard">Постоянные диагнозы <span class="text-danger"></span></label>
							<div name="diagnosis_fullcard"  id='diagnosis_fullcard' class="text-break">&nbsp;</div>
						</div>
					</div>
					
					<div class="row">
						<div class="form-group mb-2">
							<label class='control-label font-weight-bold text-break' for="vactine_fullcard">Последняя вакцинация <span class="text-danger"></span></label>
							<div name="vactine_fullcard"  id='vactine_fullcard' class="text-break">&nbsp;</div>
						</div>
					</div>

				</div>
				
				
				<div class="col-lg px-0 pt-2 pt-lg-0 ps-lg-3">
				
					<div class="d-flex justify-content-lg-start align-items-lg-center">								

						<div class="col-11 d-flex justify-content-lg-start align-items-lg-center">
						
							<div class="d-flex align-items-lg-center d-flex justify-content-start align-items-center" >						
								<h5 class="text-body">Владелец</h5>
							</div>							
							
							<div name="client_info_container2"  id='client_info_container2' class="d-none px-3 align-items-lg-center d-flex justify-content-center align-items-center" >						
								<button onclick="show_client_info()" class="btn btn-sm border-2 rounded-circle border-danger fw-weight-bold">
									&nbsp;!&nbsp;
								</button>				
							</div>
						
						</div>
		
						<div class=" col-1 align-items-lg-center d-flex justify-content-end align-items-center" >
							<div class="btn-group dropstart">							  
								<a class="nav-link p-1" data-bs-toggle="dropdown" aria-expanded="false" href="#" role="button">	
									<img class="img-responsive" width="40" height="40" src="images/settings.png">
								</a>
								<ul class="dropdown-menu dropdown-2">
									<li><a class="dropdown-item text-break" role="button" data-bs-toggle="modal" data-bs-target="#new_client">Новый владелец (нет в базе данных)</a></li>
									<li><a class="dropdown-item text-break" role="button" data-bs-toggle="modal" data-bs-target="#change_client">Сменить владельца (есть в базе)</a></li>
									<li><a class="dropdown-item text-break" role="button" onclick="change_client_data_dialog()">Изменить данные владельца</a></li>
								</ul>
							</div>
						</div>									
					</div>	
				
					<div class="row">
					
						<div class="col-lg-4">
						<div class="form-group mb-2">
							<label class='control-label font-weight-bold text-break' for="last_name_fullcard">Фамилия <span class="text-danger"></span></label>
							<div name="last_name_fullcard"  id='last_name_fullcard' class="text-break">&nbsp;</div>
						</div>
						</div>
						
						<div class="col-lg-4">
						<div class="form-group mb-2">
							<label class='control-label font-weight-bold text-break' for="first_name_fullcard">Имя <span class="text-danger"></span></label>
							<div name="first_name_fullcard"  id='first_name_fullcard' class="text-break">&nbsp;</div>
						</div>
						</div>
						
						<div class="col-lg-4">
						<div class="form-group mb-2">
							<label class='control-label font-weight-bold text-break' for="middle_name_fullcard">Отчество <span class="text-danger"></span></label>
							<div name="middle_name_fullcard"  id='middle_name_fullcard' class="text-break">&nbsp;</div>
						</div>
						</div>							
					
					</div>
					
					<div class="row">							
						<div class="col-lg-4">
						<div class="form-group mb-2">
							<label class='control-label font-weight-bold text-break' for="city_fullcard">Населенный пункт <span class="text-danger"></span></label>
							<div name="city_fullcard"  id='city_fullcard' class="text-break">&nbsp;</div>
						</div>
						</div>
						
						<div class="col-lg-8">
						<div class="form-group mb-2">
							<label class='control-label font-weight-bold text-break' for="address_fullcard">Адрес <span class="text-danger"></span></label>
							<div name="address_fullcard"  id='address_fullcard' class="text-break">&nbsp;</div>
						</div>
						</div>							
					
					</div>
					
					<div class="row">							
						<div class="col-lg-6">
						<div class="form-group mb-2">
							<label class='control-label font-weight-bold text-break' for="phone_fullcard">Основной телефон <span class="text-danger"></span></label>
							<div name="phone_fullcard"  id='phone_fullcard' class="text-break">&nbsp;</div>
						</div>
						</div>
						
						<div class="col-lg-6">
						<div class="form-group mb-2">
							<label class='control-label font-weight-bold text-break' for="phoneinfo_fullcard">Комментарий <span class="text-danger"></span></label>
							<div name="phoneinfo_fullcard"  id='phoneinfo_fullcard' class="text-break">&nbsp;</div>
						</div>
						</div>							
					
					</div>
					
					<div class="row">							
						<div class="col-lg-6">
						<div class="form-group mb-2">
							<label class='control-label font-weight-bold text-break' for="phone2_fullcard">Дополнительный телефон <span class="text-danger"></span></label>
							<div name="phone2_fullcard"  id='phone2_fullcard' class="text-break">&nbsp;</div>
						</div>
						</div>
						
						<div class="col-lg-6">
						<div class="form-group mb-2">
							<label class='control-label font-weight-bold text-break' for="phoneinfo2_fullcard">Комментарий <span class="text-danger"></span></label>
							<div name="phoneinfo2_fullcard"  id='phoneinfo2_fullcard' class="text-break">&nbsp;</div>
						</div>
						</div>							
					
					</div>
					
					<div class="row">							
						<div class="col-lg-6">
						<div class="form-group mb-2">
							<label class='control-label font-weight-bold text-break' for="email_fullcard">Email <span class="text-danger"></span></label>
							<div name="email_fullcard"  id='email_fullcard' class="text-break">&nbsp;</div>
						</div>
						</div>
						
						<div class="col-lg-6">
						<div class="form-group mb-2 mb-lg-2">
							<label class='control-label font-weight-bold text-break' for="data_ready_fullcard">Персональные данные <span class="text-danger"></span></label>
							<div name="data_ready_fullcard"  id='data_ready_fullcard' class="text-break">&nbsp;</div>
						</div>
						</div>							
					
					</div>
				</div>
			</div>	
		</div>	
	</div>	


	<div class="border border p-3 rounded mt-4">
	
		<!-- Вкладки -->
		@include('patient_card_tabs')
		
	</div>
	
	
	
	<!-- Диалоги и модальные окна -->
	@include('patient_card_modals')
	
	
	<!-- Диалог нового приема -->
	@include('new_visit_dialog')
	
	
	<!-- Диалог нового исследования -->
	@include('new_research_dialog')
	
	
	<!-- Диалог нового анализа -->
	@include('new_analysis_dialog')
	
	<!-- Диалог новой вакцинации -->
	@include('new_vacine_dialog')
	
	<!-- Диалог нового счета -->
	@include('new_bill_dialog')
	
	<!-- Скрипты select2 -->
	@include('patient_card_select2')

	
	<script>
		
		// Закрытие всех открытых popup при начале скрола модального окна
		$('.modal-body').scroll(function(){
			// datepicker
			$('#date_of_birth').datepicker("hide");
			$('#date_of_birth').blur();
		});
		
		
		// Закрытие всех открытых меню при начале скрола страницы
		$(document).scroll(function() {
			// datepicker
			$('#visit_date_start').datepicker("hide");
			$('#visit_date_start').blur();
			
			$('#visit_date_end').datepicker("hide");
			$('#visit_date_end').blur();
			
			$('#research_visit_date_start').datepicker("hide");
			$('#research_visit_date_start').blur();
			
			$('#research_visit_date_end').datepicker("hide");
			$('#research_visit_date_end').blur();
			
			$('#analisys_visit_date_start').datepicker("hide");
			$('#analisys_visit_date_start').blur();
			
			$('#analisys_visit_date_end').datepicker("hide");
			$('#analisys_visit_date_end').blur();
			
			$('#bill_date_start').datepicker("hide");
			$('#bill_date_start').blur();
			
			$('#bill_date_end').datepicker("hide");
			$('#bill_date_end').blur();
			
			var cusid_ele = document.getElementsByClassName('dropdown-m1');
			for (var i = 0; i < cusid_ele.length; ++i) {

				if ($('.dropdown-m1-'+i).is(":visible")){
			
					$('.dropdown-m1-'+i).dropdown('toggle');
				}
			}
			
			if ($('.dropdown-1').is(":visible")){				
				$('.dropdown-1').dropdown('toggle');
			} 
			
			if ($('.dropdown-2').is(":visible")){
				$('.dropdown-2').dropdown('toggle');
			}
			
			if ($('.dropdown-3').is(":visible")){
				$('.dropdown-3').dropdown('toggle');
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
				
		
		
		// Dadata для второй формы
		function enforceCity(suggestion) {
		  var sgt = $('#new_client').find("#address_2").suggestions();
		  sgt.clear();
		  if (suggestion) {
			setConstraints(sgt, suggestion.data.kladr_id);
		  } else {
			setConstraints(sgt, null);
		  }
		}

		function restrictAddressValue(suggestion) {
		  var citySgt = $('#new_client').find("#city_2").suggestions();
		  var addressSgt = $('#new_client').find("#address_2").suggestions();
		  if (!citySgt.currentValue) {
			citySgt.setSuggestion(suggestion);
			var city_kladr_id = suggestion.data.kladr_id.substr(0, 13);
			setConstraints(addressSgt, city_kladr_id);
		  }
		}

		$('#new_client').find("#city_2").suggestions({
		  token: token,
		  type: "ADDRESS",
		  bounds: "city-settlement",
		  hint: 'Выберите вариант',
		  geoLocation: false,
		  onSelect: enforceCity,
		  onSelectNothing: enforceCity
		});

		$('#new_client').find("#address_2").suggestions({
		  token: token,
		  type: "ADDRESS",
		  hint: 'Выберите вариант',
		  onSelect: restrictAddressValue,
		  formatSelected: formatSelected
		});

		$("#city_2").suggestions().fixData();
												
	</script>
		
</div>

@endsection

