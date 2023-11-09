<script>

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
	
	
	$('#porpose').select2({
		theme: "bootstrap-5"
	});
	
	$('#visit_type').select2({
		minimumResultsForSearch: Infinity,
		theme: "bootstrap-5"
	});

	
	// Custom результат для Select2 - поиск владельца (клиента)
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
			
			
	// Слушатель выбора клиента
	$('#client_id').on("select2:select", function(e) { 
		if ($('#client_id').val() != null) {
			$("#myForm2").removeClass('d-none');
			client_form_opened = true;
			get_one_client_data($('#client_id').val());
		} else {
					
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
			
	
	// Установка списка шаблонов - осмотры
	$.ajax({
		url: '/guides/gettemplates',
		method:'GET',
		dataType:'json',
		data: {type:'1'},
		success: function(data) {
			$('#check_result_plate').select2({ 
			placeholder: "Выберите шаблон",
			theme: "bootstrap-5",
			allowClear: true,
			data: data 
			}).on('select2:unselecting', function() {
				$(this).data('unselecting', true);
				//$('#add_plate_text').addClass('d-none');
			}).on('select2:opening', function(e) {
				if ($(this).data('unselecting')) {
					$(this).removeData('unselecting');
					e.preventDefault();
				}
			});
		},
			error: function(err) {	
				//toastr.error('Ошибка загрузки');
			}
	});
	
	
	// Установка списка исследований
	$.ajax({
		url: '/guides/gettemplates',
		method:'GET',
		dataType:'json',
		data: {type:'2'},
		success: function(data) {
			$(".researches_list").select2({ 
			placeholder: "Выберите значение",
			theme: "bootstrap-5",
			allowClear: true,
			data: data 
			}).on('select2:unselecting', function() {
				$(this).data('unselecting', true);
				//$('#add_plate_text').addClass('d-none');
			}).on('select2:opening', function(e) {
				if ($(this).data('unselecting')) {
					$(this).removeData('unselecting');
					e.preventDefault();
				}
			});
		},
			error: function(err) {	
				//toastr.error('Ошибка загрузки');
			}
	});
	
	
	// Установка списка шаблонов - рекомендации
	$.ajax({
		url: '/guides/gettemplates',
		method:'GET',
		dataType:'json',
		data: {type:'3'},
		success: function(data) {
			$('#recomendation_plate').select2({ 
			placeholder: "Выберите шаблон",
			theme: "bootstrap-5",
			allowClear: true,
			data: data 
			}).on('select2:unselecting', function() {
				$(this).data('unselecting', true);
				//$('#add_plate_text').addClass('d-none');
			}).on('select2:opening', function(e) {
				if ($(this).data('unselecting')) {
					$(this).removeData('unselecting');
					e.preventDefault();
				}
			});
		},
			error: function(err) {	
				//toastr.error('Ошибка загрузки');
			}
	});
	
	
	
	// Установка списка шаблонов анализов
	$.ajax({
		url: '/guides/getanalisystemplates',
		method:'GET',
		dataType:'json',
		success: function(data) {
			$('.analysis_list').select2({ 
			placeholder: "Выберите шаблон",
			theme: "bootstrap-5",
			allowClear: true,
			data: data 
			}).on('select2:unselecting', function() {
				$(this).data('unselecting', true);
				clear_analysis_plate_data ();				
			}).on('select2:opening', function(e) {
				if ($(this).data('unselecting')) {
					$(this).removeData('unselecting');
					e.preventDefault();
				}
			});
		},
			error: function(err) {	
				//toastr.error('Ошибка загрузки');
			}
	});
	
	
	
	// Установка списка вакцин	
	$('#vacine_name').select2({
		minimumInputLength: 3,
		theme: "bootstrap-5",
		language: {
			inputTooShort: function() {
				return 'Введите не менее 3-х символов';	
			}
		},	
		placeholder: "Выберите вакцину",
		allowClear: true,						
		ajax: {
			url: '/guides/vacine_search',
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
	
	
	
	// Установка списка - диагнозы				
	$(".diagnosis_list").select2({
		minimumInputLength: 3,	
		theme: "bootstrap-5",
		language: {
			inputTooShort: function() {
				return 'Введите не менее 3-х символов';	
			}
		},	
		placeholder: "Выберите диагноз",
		allowClear: true,						
		ajax: {
			url: '/guides/diagnosis_search',
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
				
		// Здесь нужно очистить input
		$("#myForm2").addClass('d-none');
			
	}).on('select2:opening', function(e) {
		if ($(this).data('unselecting')) {
			$(this).removeData('unselecting');
			e.preventDefault();
		}
	});
	

	// Установка списка - список визитов
	set_visits_list();
	
	
	function set_visits_list(){
		
		// Установка списка - список визитов
		$.ajax({
			url: '/patientcard/visits_for_select',
			method:'GET',
			dataType:'json',
			data: {anymal_id:{{ $patient_id }}},
			success: function(data) {
				$(".tovisit").select2({ 
				placeholder: "Выберите визит",
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
		
</script>