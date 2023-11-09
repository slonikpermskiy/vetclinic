<script> 

	var bill_id = '';
	
	var neworchange = '';
	
	var billopened = '';

	var doctorsforselect = '';

	$.ajax({
		url: '/staff/search',
		method:'GET',
		dataType:'json',
		success: function(data) {
			doctorsforselect = data;
		},
			error: function(err) {	
				//toastr.error('Ошибка загрузки списка врачей');
			}
	});
			
	// Открытие диалога
	function new_bill(id) {
		
		// Очистка текстов-ошибок в span
		$('#new_bill').find('#error_bill_date').html("&nbsp;");
		$('#new_bill').find('#error_services_products_bill_list').empty();
		$('#error_services_products_bill_list').addClass('d-none'); 
		
		$('#new_bill').find('#new_bill_form')[0].reset();		
		$('#bill_date').datepicker().data('datepicker').clear();
		$('#new_bill').find('#paied').val('0').trigger('change');
		$('#new_bill').find('#animal_choose').val(0).trigger('change');
		$('#new_bill').find('#product_choose').val(0).trigger('change');
		$('#new_bill').find('#service_choose').val(0).trigger('change');

		$('#new_bill').find('#bill_doctor').val(0).trigger('change');
		
		$('#new_bill').find('#pay_bill').val('');
		$('#new_bill').find('#pay_date').val('');
		$(".pay_list_plate").empty();
		
	
		$(".product_list_bills_plate").empty();
		
		$('#summ_product_bills_plate').addClass('d-none');  
		$('#title_product_bills_plate').addClass('d-none'); 
		
		$(".service_list_bills_plate").empty();
		
		$('#summ_service_bills_plate').addClass('d-none');  
		$('#title_service_bills_plate').addClass('d-none'); 


		// Инициализация календаря и установка даты в оплате
		var myDataPicker_5 = $('#pay_date').datepicker({ 
			language : 'ru', 
			autoClose : true,
			clearButton: true,
		}).data('datepicker');
		
		myDataPicker_5.selectDate(new Date());		
		
		if (id) {
			
			neworchange = 1;
			
			bill_id = id;
			
			$('#new_bill').find('#new_bill_title').html("Изменить счет");
						
			$.ajax({
				url: '/patientcard/get_bill_data',
				method:'GET',
				dataType:'json',
				data: {bill_id: id},
				success: function(data) {
										
					if($.isEmptyObject(data.error)){

						if (data.success['patient_id']) {
							
							var $patient = $("<option selected='selected'></option>").val(data.success['patient_id']).text(data.shortname);
							$('#animal_choose').append($patient).trigger('change');
							
							
							if ({{ $bill_page }} == 1) {
								
							} else  {			
								$('#animal_choose').select2("enable", false);
							}	
						}


						// Установка врача
						if (data.success['staff_id']) {
							$('#bill_doctor').val(data.success['staff_id']).trigger('change');
						} else {
							toastr.error('Врач не найден, возможно он был удален');
						}

						
						// Инициализация календаря и установка даты в счете
						var myDataPicker_2 = $('#bill_date').datepicker({ 
							language : 'ru', 
							autoClose : true,
							clearButton: true,
						}).data('datepicker');

						
						// Установка даты
						var dateParts = data.success['date_of_bill'].split("-");
						var jsDate = new Date(dateParts[0], dateParts[1] - 1, dateParts[2].substr(0,2));
						
						myDataPicker_2.selectDate(jsDate);
						
						
						if (data.pays != null && data.pays != '') {
						
							JSON.parse(data.pays).forEach(function(item, i, arr) {
								

								
								var html = "<div class='row pay-group'>" 
									+"<div class='d-flex px-0 align-items-center'>" 
										+"<div class='justify-content-left align-self-enter align-items-center d-flex px-2 fs-6 fw-bolder text-break' name='pay_summ_todb' id='pay_summ_todb'>"+parseFloat(item.pay_summ).toFixed(2)+"</div>"
										+"<div class='justify-content-left align-self-enter align-items-center d-flex pe-2 fs-6 fw-bolder'>руб.</div>"
										+"<div class='justify-content-left align-self-center align-items-center d-flex fst-italic fs-6 text-break' name='pay_date_todb' id='pay_date_todb'>"+item.date_of_pay+"</div>"
										+"<div class='justify-content-left align-items-center align-self-center d-flex px-2'>" 
											+"<a class='nav-link p-1 remove-pay' role='button'>"	
												+"<img class='img-responsive' width='25' height='25' src='images/delete_photo.png'>" 
											+"</a>"
										+"</div>"
									+"</div>" 
								+"</div>";
																	
								$(".pay_list_plate").append(html);
													
								
								
							});
						
						}
						
												
						if (data.success['product_text'] != null) {
						
							$('#summ_product_bills_plate').removeClass('d-none');  

							$('#title_product_bills_plate').removeClass('d-none'); 
														
							if (data.success['product_discount'] != 0) {
								$('#new_bill').find('#product_discount').val(data.success['product_discount'])
							}
							
							JSON.parse(data.success['product_text']).forEach(function(item, i, arr) {
							
								var html = "<div class='bill_product_group pt-2'>"
									+"<div class='d-flex flex-wrap px-0 align-items-center'>"							
										+"<div class='pe-2 mb-2' style='width:445px;'>"	
											+"<input name='product_name_todb' value='"+item.product_name_todb+"' class='form-control form-control-lg' id='product_name_todb' placeholder='Название' disabled readonly>"	
											+"<input name='product_id_todb' value='"+item.product_id_todb+"' class='d-none' id='product_id_todb' disabled readonly>"
										+"</div>"
										+"<div class='pe-2 mb-2' style='width:130px;'>"	
											+"<input name='product_edizm_todb' value='"+item.product_edizm_todb+"' class='form-control form-control-lg' id='product_edizm_todb' placeholder='Название' disabled readonly>"	
										+"</div>"
										+"<div class='pe-2 mb-2' style='width:130px;'>"		
											+"<input name='product_price_todb' value='"+parseFloat(item.product_price_todb).toFixed(2)+"' class='form-control form-control-lg' id='product_price_todb' placeholder='Цена' disabled readonly>"	
										+"</div>"	
										+"<div class='pe-2 mb-2' style='width:180px;'>"		
											+"<div class='input-group'>"	
												+"<button class='btn btn-danger btn-number' onclick='minusone_product.call(this)' type='button' id='button-plus' data-type='minus' data-field='quant'>"									  
													+"<i data-feather='minus'></i>"	
												+"</button>"
												+"<input type='text' name='product_count_todb' id='product_count_todb' class='form-control form-control-lg text-center input-number' value='"+item.product_count_todb+"' min='0' max='999' oninput=\"this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\\\..*?)\\\..*/g, '$1');\" pattern=\"^\\\d{0,3}(\\\.\\\d{0,2})?$\">"	//   
												+"<button class='btn btn-success btn-number' onclick='plusone_product.call(this)'  type='button' id='button-minus' data-type='plus' data-field='quant'>"	  
													+"<i data-feather='plus'></i>"	
												+"</button>"	
											+"</div>"	
										+"</div>"	
										+"<div class='pe-2 mb-2' style='width:130px;'>"		
											+"<input name='product_price_count' class='form-control form-control-lg product_price_count' id='product_price_count' placeholder='Стоимость' disabled readonly>"	
										+"</div>"	
										+"<div class='mb-2 justify-content-left align-items-center align-self-center d-flex' style='width:25px;'>"	
											+"<a class='nav-link p-1 remove-product align-self-center' role='button'>"	
												+"<img class='img-responsive' width='25' height='25' src='images/delete_photo.png'>"	
											+"</a>"	
										+"</div>"	
										
									+"</div>"	
								+"</div>";		
								
														
								$(".product_list_bills_plate").append(html);
									
							});
							
							// Инициализация иконок
							feather.replace();
							
							initialize_count_limits();
							
							product_count_summ();

						}
						
						
						if (data.success['service_text'] != null) {
						
							$('#summ_service_bills_plate').removeClass('d-none');  

							$('#title_service_bills_plate').removeClass('d-none'); 
														
							if (data.success['service_discount'] != 0) {
								$('#new_bill').find('#service_discount').val(data.success['service_discount'])
							}
							
							JSON.parse(data.success['service_text']).forEach(function(item, i, arr) {
							
								var html = "<div class='bill_service_group pt-2'>"
									+"<div class='d-flex flex-wrap px-0 align-items-center'>"							
										+"<div class='pe-2 mb-2' style='width:300px;'>"	
											+"<input name='service_name_todb' value='"+item.service_name_todb+"' class='form-control form-control-lg' id='service_name_todb' placeholder='Название' disabled readonly>"	
											+"<input name='service_id_todb' value='"+item.service_id_todb+"' class='d-none' id='service_id_todb' disabled readonly>"
										+"</div>"
										+"<div class='pe-2 mb-2' style='width:275px;'>"	
											+"<select name='service_doctor_todb' id='service_doctor_todb_"+i+"' class='form-select-lg block' style='width: 100%;' placeholder='Выбрать сотрудника' data-search='true'>"
												+"<option></option>"
											+"</select>"
										+"</div>"	
										+"<div class='pe-2 mb-2' style='width:130px;'>"		
											+"<input name='service_price_todb' value='"+parseFloat(item.service_price_todb).toFixed(2)+"' class='form-control form-control-lg input-number-service' id='service_price_todb' placeholder='Цена' autocomplete='off' oninput=\"this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\\\..*?)\\\..*/g, '$1');\" pattern=\"^\\\d{0,6}(\\\.\\\d{0,2})?$\">"
										+"</div>"	
										+"<div class='pe-2 mb-2' style='width:180px;'>"		
											+"<div class='input-group'>"	
												+"<button class='btn btn-danger btn-number' onclick='minusone_service.call(this)' type='button' id='button-plus' data-type='minus' data-field='quant'>"									  
													+"<i data-feather='minus'></i>"	
												+"</button>"
												+"<input type='text' name='service_count_todb' id='service_count_todb' class='form-control form-control-lg text-center input-number' value='"+item.service_count_todb+"' min='0' max='999' oninput=\"this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\\\..*?)\\\..*/g, '$1');\" pattern=\"^\\\d{0,3}(\\\.\\\d{0,2})?$\">"	//   
												+"<button class='btn btn-success btn-number' onclick='plusone_service.call(this)'  type='button' id='button-minus' data-type='plus' data-field='quant'>"	  
													+"<i data-feather='plus'></i>"	
												+"</button>"	
											+"</div>"	
										+"</div>"	
										+"<div class='pe-2 mb-2' style='width:130px;'>"		
											+"<input name='service_price_count' class='form-control form-control-lg product_price_count' id='service_price_count' placeholder='Стоимость' disabled readonly>"	
										+"</div>"	
										+"<div class='mb-2 justify-content-left align-items-center align-self-center d-flex' style='width:25px;'>"	
											+"<a class='nav-link p-1 remove-service align-self-center' role='button'>"	
												+"<img class='img-responsive' width='25' height='25' src='images/delete_photo.png'>"	
											+"</a>"	
										+"</div>"	
										
									+"</div>"	
								+"</div>";		
																						
								$(".service_list_bills_plate").append(html);

								// Установка списка врачей
								$("#service_doctor_todb_"+i).select2({ 
									placeholder: "Выберите сотрудника",
									theme: "bootstrap-5",
									allowClear: true,
									data: doctorsforselect 
									}).on('select2:unselecting', function() {
										$(this).data('unselecting', true);
									}).on('select2:opening', function(e) {
										if ($(this).data('unselecting')) {
											$(this).removeData('unselecting');
											e.preventDefault();
										}
									});	
								
								// Выбор врача
								$("#service_doctor_todb_"+i).val(item.service_doctor_todb).trigger('change');

							});
							
							// Инициализация иконок
							feather.replace();
										
							initialize_count_limits();
	
							service_count_summ();

						}
									
						$('#new_bill').modal('show');
						
					}else{
						toastr.error(data.error);
					}	
				},
					error: function(err) {					
						toastr.error('Ошибка');
					}
				});
						
		} else {
			
			neworchange = 0;
			
			bill_id = '';
			
			$('#new_bill').find('#new_bill_title').html("Новый счёт");

			// Установка врача (текущий пользователь)
			$('#bill_doctor').val({{ Auth::user()->staff_id }}).trigger('change');
			
			
			if ({{ $patient_id or 'not-exist' }}) {
								
				if ({{ $bill_page }} == 1) {
					
				} else  {
					var $patient = $("<option selected='selected'></option>").val({{ $patient_id }}).text($('#short_name_fullcard').text());
					$('#animal_choose').append($patient).trigger('change');
					
					$('#animal_choose').select2("enable", false);
				}			
				
			}
			
								
			// Инициализация календаря и установка даты в счете
			var myDataPicker_2 = $('#bill_date').datepicker({ 
				language : 'ru', 
				autoClose : true,
				clearButton: true,
			}).data('datepicker');
			
			myDataPicker_2.selectDate(new Date());
					
			$('#new_bill').modal('show');
						
		}
	
	}
	
	
	
	// Слушатель кнопки календарик
	function calendarinput_bill(){  	
		$("#bill_date").trigger("focus");
	}
	
	// Слушатель кнопки календарик
	function calendarinput_pay_bill(){  	
		$("#pay_date").trigger("focus");
	}
	

	// Количество значений в перечне услуг
    function service_bill_count(){
		return $('.service_list_bills_plate').find('.bill_service_group').length;
	}


	// Количество значений в перечне услуг
    function service_bill_count_after(){
		return $('.service_list_bills_plate').find('.bill_service_group').length-1;
	}
	
	
	// Сохраняем счет
    function save_bill(){
		
		var pay_summ = Number(0);
					
		var pay_items = $('.pay_list_plate').find('.pay-group').each(function() {
			// Если пустое значение, то 0, если нет, то расчет
			if ($(this).find('#pay_summ_todb').html() !== '') {
				pay_summ = Number(parseFloat(pay_summ).toFixed(2)) + Number(parseFloat($(this).find('#pay_summ_todb').html()).toFixed(2));
			} else {
				pay_summ = Number(parseFloat(pay_summ).toFixed(2)) + Number((0).toFixed(2));
			}
		});		

		if (pay_summ > Number(parseFloat($('#new_bill').find('#bill_summ').val()).toFixed(2))) {
			toastr.error('Сумма оплат больше суммы счета')
		} else {

			// Очистка текстов-ошибок в span
			$('#new_bill').find('#error_bill_date').html("&nbsp;");
			$('#new_bill').find('#error_services_products_bill_list').empty();
			$('#error_services_products_bill_list').addClass('d-none'); 
			
			var form = $('#new_bill_form')[0];
			
			var formData = new FormData(form);
			
					
			if ($('#animal_choose').val()) {
				formData.append("anymal_id", $('#animal_choose').val());
			}

			
			if ($('#bill_doctor').val()) {
				formData.append("staff", $('#bill_doctor').select2('data')[0].text);
			}

			if ($('#bill_doctor').val()) {
				formData.append("staff_id", $('#bill_doctor').val());
			}
			
			formData.append("bill_id", bill_id);
			
			
			var products_bill_list = new Array();
			
			var product_items = $('.product_list_bills_plate').find('.bill_product_group').each(function() {
						
				var hash = {};
				
				hash['product_name_todb'] = $(this).find('#product_name_todb').val();
				hash['product_id_todb'] = $(this).find('#product_id_todb').val();
				hash['product_edizm_todb'] = $(this).find('#product_edizm_todb').val();
				hash['product_price_todb'] = $(this).find('#product_price_todb').val();	
				hash['product_count_todb'] = Number($(this).find('#product_count_todb').val()).toString();
				
				products_bill_list.push(hash);
			
			});
			

			if (products_bill_list.length !== 0) {			
				formData.append("products_bill_list", JSON.stringify(products_bill_list));
			}		
					
			formData.append("product_discount", $('#new_bill').find('#product_discount').val());

			formData.append("product_summ", $('#new_bill').find('#product_summ').val());
			
			
			var services_bill_list = new Array();
			
			var service_items = $('.service_list_bills_plate').find('.bill_service_group').each(function(index, value) {
				
				var hash = {};
				
				hash['service_name_todb'] = $(this).find('#service_name_todb').val();
				hash['service_id_todb'] = $(this).find('#service_id_todb').val();
				hash['service_price_todb'] = $(this).find('#service_price_todb').val();	
				hash['service_count_todb'] = Number($(this).find('#service_count_todb').val()).toString();
				if ($(this).find('#service_doctor_todb_'+ Number(index).toString()).val()) {
					hash['service_doctor_todb'] = $(this).find('#service_doctor_todb_'+ Number(index).toString()).val();
					hash['service_doctor_text_todb'] = $(this).find('#service_doctor_todb_'+ Number(index).toString()).select2('data')[0].text;
				}
				services_bill_list.push(hash);
			
			});
			
			
			if (services_bill_list.length !== 0) {
				formData.append("services_bill_list", JSON.stringify(services_bill_list));
			}

			formData.append("service_discount", $('#new_bill').find('#service_discount').val());

			formData.append("service_summ", $('#new_bill').find('#service_summ').val());
			
			
			formData.append("bill_summ", $('#new_bill').find('#bill_summ').val());
			

			// Оплаты
			var pays_list = new Array();
			
			var pay_items = $('.pay_list_plate').find('.pay-group').each(function() {
				
				var hash = {};
				
				hash['pay_summ_todb'] = $(this).find('#pay_summ_todb').html();
				hash['pay_date_todb'] = $(this).find('#pay_date_todb').html();	
				
				pays_list.push(hash);
				
			});
			
			
			if (pays_list.length !== 0) {
				formData.append("pays_list", JSON.stringify(pays_list));
			}
			
			
			formData.append("bill_pay", pay_summ);
						
		
			// Записываем данные
			$.ajax({
			url: '/patientcard/new_bill',
			type: 'POST',
			processData: false,
			contentType: false,
			data: formData,
				
			success: function(data) {
								
				if($.isEmptyObject(data.error)){
					
					toastr.info(data.success); 
					
					// Обновляем список счетов
					show_bill_list();
					
					// Обновляем данные счета и открываем
					open_bill(data.billid);
					
					$('#new_bill').modal('toggle');
				
				}else{

					if (data.error['anymal_id'] && data.error['anymal_id'].length > 0) {
						toastr.error('Ошибка сохранения');
					} else {
						printErrorMsg(data.error);
						toastr.error('Ошибки заполнения полей');
					}
					
				}
				},
			error: function(xhr, status, error) {	
				toastr.error(error);
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
				
				if (field_name == 'products_bill_list' | field_name == 'services_bill_list' ) {
					$('#error_services_products_bill_list').html(error);
					$('#error_services_products_bill_list').removeClass('d-none'); 
				} else {
					$('#error_'+field_name).html(error);
				}
				
			})
		
		}
						
	}
	

	// Плюс один продукт
	var plusone_product = function(){		
		var currentVal = parseInt($(this).parents('.bill_product_group').find('.input-number').val());
		if(currentVal < $(this).parents('.bill_product_group').find('.input-number').attr('max')) {
			$(this).parents('.bill_product_group').find('.input-number').val(currentVal + 1).trigger('change');
		}
		
		product_count_summ();
	};
	
	
	// Минус один продукт
	var minusone_product = function(){
		var currentVal = parseInt($(this).parents('.bill_product_group').find('.input-number').val());	
		if(currentVal > $(this).parents('.bill_product_group').find('.input-number').attr('min')) {
			$(this).parents('.bill_product_group').find('.input-number').val(currentVal - 1).trigger('change');
		}

		product_count_summ();		
		
	};
	
	
	
	// Очистить один товар
	$("body").on("click",".remove-product",function(){ 
		
		$(this).parents(".bill_product_group").remove();
		
		product_count_summ();
		
		if ($('.product_list_bills_plate').find('.bill_product_group').length == 0) {
			$('#summ_product_bills_plate').addClass('d-none');  
			$('#title_product_bills_plate').addClass('d-none'); 
		}
		
	});
	
	
	// Плюс одна услуга
	var plusone_service = function(){		
		var currentVal = parseInt($(this).parents('.bill_service_group').find('.input-number').val());
		if(currentVal < $(this).parents('.bill_service_group').find('.input-number').attr('max')) {
			$(this).parents('.bill_service_group').find('.input-number').val(currentVal + 1).trigger('change');
		}
		
		service_count_summ();
	};
	
	// Минус одна услуга
	var minusone_service = function(){
		var currentVal = parseInt($(this).parents('.bill_service_group').find('.input-number').val());	
		if(currentVal > $(this).parents('.bill_service_group').find('.input-number').attr('min')) {
			$(this).parents('.bill_service_group').find('.input-number').val(currentVal - 1).trigger('change');
		}

		service_count_summ();		
		
	};
	
	
	
	// Очистить одну услугу
	$("body").on("click",".remove-service",function(){ 
		
		$(this).parents(".bill_service_group").remove();
		
		service_count_summ();
		
		if ($('.service_list_bills_plate').find('.bill_service_group').length == 0) {
			$('#summ_service_bills_plate').addClass('d-none');  
			$('#title_service_bills_plate').addClass('d-none'); 
		}
		
	});
	
	
	// Вычисления - товары
	function product_count_summ(){
			
		var product_summ = Number(0);
		
		var product_discount = Number(0);

		var items = $('.product_list_bills_plate').find('.bill_product_group').each(function() {
				
			var one_product_summ = Number(0);
			
			// Если пустое значение, то 0, если нет, то расчет
			if ($(this).find('#product_count_todb').val() !== '') {
				one_product_summ  = (parseFloat($(this).find('#product_price_todb').val()) * parseFloat($(this).find('#product_count_todb').val())).toFixed(2);
			} else {
				one_product_summ  = (0).toFixed(2);
			}
			
			$(this).find('#product_price_count').val(one_product_summ);

			product_summ = Number(parseFloat(product_summ)) + Number(parseFloat(one_product_summ));

		});
		
		if ($('#new_bill').find('#product_discount').val() !== '') {
			product_discount = parseInt($('#new_bill').find('#product_discount').val());
		} 

		product_summ = (product_summ * ((100 - Number(product_discount)) / 100)).toFixed(2);				
		
		$('#new_bill').find('#product_summ').val(product_summ);
		
		var service_summ = Number(0);
		
		if ($('#new_bill').find('#service_summ').val() !== '') {
			service_summ = parseFloat($('#new_bill').find('#service_summ').val());
		}
				
		var bill_summ = parseFloat(Number(product_summ) + Number(service_summ)).toFixed(2);
		
		$('#new_bill').find('#bill_summ').val(bill_summ);
		
				
		var pay_summ = Number(0);
		
		var items = $('.pay_list_plate').find('.pay-group').each(function() {
				
			var one_pay_summ = Number(0);
			
			// Если пустое значение, то 0, если нет, то расчет
			if ($(this).find('#pay_summ_todb').html() !== '') {
				one_pay_summ  = parseFloat($(this).find('#pay_summ_todb').html()).toFixed(2);
			} else {
				one_pay_summ  = (0).toFixed(2);
			}

			pay_summ = Number(parseFloat(pay_summ)) + Number(parseFloat(one_pay_summ));

		});
		
		$('#new_bill').find('#bill_pay').val(parseFloat(pay_summ).toFixed(2));
				
		var pay_last = Number(0);
		
		pay_last = parseFloat(Number(parseFloat(bill_summ)) - Number(parseFloat(pay_summ))).toFixed(2);
		
		$('#new_bill').find('#bill_last').val(parseFloat(pay_last).toFixed(2));

	}
	
	
	// Вычисления - услуги
	function service_count_summ(){
				
		var service_summ = Number(0);
		
		var service_discount = Number(0);
		
		var items = $('.service_list_bills_plate').find('.bill_service_group').each(function() {
				
			var one_service_summ = Number(0);
			
			// Если пустое значение, то 0, если нет, то расчет
			if ($(this).find('#service_count_todb').val() !== '') {
				one_service_summ  = (parseFloat($(this).find('#service_price_todb').val()) * parseFloat($(this).find('#service_count_todb').val())).toFixed(2);
			} else {
				one_service_summ  = (0).toFixed(2);
			}

			$(this).find('#service_price_count').val(one_service_summ);

			service_summ = Number(parseFloat(service_summ)) + Number(parseFloat(one_service_summ));

		});
		
		if ($('#new_bill').find('#service_discount').val() !== '') {
			service_discount = parseInt($('#new_bill').find('#service_discount').val());
		} 

		service_summ = (service_summ * ((100 - Number(service_discount)) / 100)).toFixed(2);			
		
		$('#new_bill').find('#service_summ').val(service_summ);
		
		
		var product_summ = Number(0);
		
		if ($('#new_bill').find('#product_summ').val() !== '') {
			product_summ = parseFloat($('#new_bill').find('#product_summ').val());
		}
				
		var bill_summ = parseFloat(Number(product_summ) + Number(service_summ)).toFixed(2);
		
		$('#new_bill').find('#bill_summ').val(bill_summ);
		
		
		var pay_summ = Number(0);
		
		var items = $('.pay_list_plate').find('.pay-group').each(function() {
				
			var one_pay_summ = Number(0);
			
			// Если пустое значение, то 0, если нет, то расчет
			if ($(this).find('#pay_summ_todb').html() !== '') {
				one_pay_summ  = parseFloat($(this).find('#pay_summ_todb').html()).toFixed(2);
			} else {
				one_pay_summ  = (0).toFixed(2);
			}

			pay_summ = Number(parseFloat(pay_summ)) + Number(parseFloat(one_pay_summ));

		});
		
		$('#new_bill').find('#bill_pay').val(parseFloat(pay_summ).toFixed(2));
				
		var pay_last = Number(0);
		
		pay_last = parseFloat(parseFloat(Number(bill_summ)) - parseFloat(Number(pay_summ))).toFixed(2);
		
		$('#new_bill').find('#bill_last').val(parseFloat(pay_last).toFixed(2));

	}
	
	
	function addPay(){
				
		if ($('#new_bill').find('#pay_summ').val() == null | $('#new_bill').find('#pay_summ').val() == 0 | $('#new_bill').find('#pay_date').val() == null | $('#new_bill').find('#pay_date').val() == '') {
			toastr.error('Не заполнены значения');
		} else {
			
			
			var html = "<div class='row pay-group'>" 
				+"<div class='d-flex px-0 align-items-center'>" 
					+"<div class='justify-content-left align-self-enter align-items-center d-flex px-2 fs-6 fw-bolder text-break' name='pay_summ_todb' id='pay_summ_todb'>"+parseFloat($('#new_bill').find('#pay_summ').val()).toFixed(2)+"</div>"
					+"<div class='justify-content-left align-self-enter align-items-center d-flex pe-2 fs-6 fw-bolder'>руб.</div>"
					+"<div class='justify-content-left align-self-center align-items-center d-flex fst-italic fs-6 text-break' name='pay_date_todb' id='pay_date_todb'>"+$('#new_bill').find('#pay_date').val()+"</div>"
					+"<div class='justify-content-left align-items-center align-self-center d-flex px-2'>" 
						+"<a class='nav-link p-1 remove-pay' role='button'>"	
							+"<img class='img-responsive' width='25' height='25' src='images/delete_photo.png'>" 
						+"</a>"
					+"</div>"
				+"</div>" 
			+"</div>";
												
			$(".pay_list_plate").append(html);
			
			$('#new_bill').find('#pay_summ').val('');

			product_count_summ();
			service_count_summ();			
			
		}
		
	}
	

</script> 


<div class="modal hide mycontainer" id="new_bill" name="new_bill" role="dialog" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false" style="display: none">
	<div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
		<div class="modal-content">
			<div class="modal-header">
				<h5 id="new_bill_title" name="new_bill_title" class="modal-title px-3" id="staticBackdropLabel"></h5>
				<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
			</div>
			<div class="modal-body" id="new_bill_body" name="new_bill_body">
				
				<div class="px-4">
				
				<form id="new_bill_form" autocomplete="off" enctype="multipart/form-data">					
				@csrf
				
					<div class="row">
						<div class="col-lg-4 px-2 mb-2 mb-lg-0">
						
							<div class="form-group">
								<label class='control-label font-weight-bold' for="bill_date">Дата <span class="text-danger">*</span></label>		
								<div class="input-group input-group-lg">
									<input name="bill_date" id="bill_date" type="text" class="form-control form-control-lg datepicker-here appearance-none bg-white" data-position='bottom left' placeholder="Выберите дату" readonly />
									<button type="button" onclick="calendarinput_bill()" class="bg-white input-group-text" id="inputGroup-sizing-lg">
										<img src="{{ asset('images/calendar.png') }}" class="img-responsive"  width="30" height="30" />
									</button>
								</div>	
								<span name="error_bill_date" id='error_bill_date' class="error_response underinput-error text-danger d-flex justify-content-left align-items-lg-center">
								&nbsp;
								</span>

							</div>
						
						</div>

						<div class="col-lg-8 px-2 mb-2 mb-lg-0">		
							<div class="form-group">
								<label class='control-label font-weight-bold' for="bill_doctor">Врач <span class="text-danger"></span></label>
								
								<select name="bill_doctor" id="bill_doctor" class="form-select-lg js-example-basic-single block doctors" style="width: 100%;" placeholder="Не выбрано" data-search="true">
									<option></option>
								</select>
								<span name="error_bill_doctor" id='error_bill_doctor' class="error_response underinput-error text-danger d-flex justify-content-left align-items-lg-center">
								&nbsp;
								</span>
							</div>	
		
						</div>
																					
					</div>


					<div class="row">
					
						<label class="control-label font-weight-bold" for="product_title" style="color: #3182CE !important; font-weight: 600;">Оплата счета</label>						
				
						<div class="col-lg-4 px-2 mb-4 mb-lg-2">
							<div class="form-group">
								<input type="text" class="form-control form-control-lg input-number-pay" name="pay_summ" autocomplete="off" id='pay_summ' placeholder="Сумма" value="" oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*?)\..*/g, '$1');" pattern="^\d{0,6}(\.\d{0,2})?$">
							</div>					
						</div>
							
						<div class="col-lg-4 px-2 mb-4 mb-lg-2">		
							<div class="form-group">
								<div class="input-group input-group-lg">
									<input name="pay_date" id="pay_date" type="text" class="form-control form-control-lg datepicker-here appearance-none bg-white" data-position='bottom left' placeholder="Дата" readonly />
									<button type="button" onclick="calendarinput_pay_bill()" class="bg-white input-group-text" id="inputGroup-sizing-lg">
										<img src="{{ asset('images/calendar.png') }}" class="img-responsive"  width="30" height="30" />
									</button>
								</div>	
							</div>			
						</div>
						
						<div class="col-lg px-2 d-flex align-items-center mb-3 mb-lg-2">
							<button class="btn btn-primary btn-lg" type="button" id="btn-add-product" onclick="addPay()">Добавить</button>
						</div>
						
						
						<div class="pay_list_plate mb-3" name="pay_list_plate" id='pay_list_plate'>
														
						</div>
							
					</div>
					
										
					<div class="row">
						<div class="col-lg-6 px-2 me-lg-4 mb-4 d-none" name="animal_choose_plate" id="animal_choose_plate">
							
							<div class="form-group">
							
								<label class='control-label font-weight-bold' for="animal_choose">Выбрать животное<span class="text-danger"></span></label>		
								
								<select name="animal_choose" id="animal_choose" class="js-example-basic-single form-select-lg animal_choose" placeholder="Не выбрано" data-search="true">
									<option></option>
								</select>

							</div>
							
						</div>
															
					</div>	
					
					
					<div class="row">
						
						<div class="col-lg-6 px-2 mb-4">
							<div class="form-group input-group" >
							
								<label class='control-label font-weight-bold' for="product_choose">Выбрать товар<span class="text-danger"></span></label>

								<div class="input-group">
                
									<div class="input-group-text">
										<input name="product_choose_checkbox" id="product_choose_checkbox" class="form-check-input mt-0" type="checkbox" value="" aria-label="Checkbox for following text input">
									</div>
		  
									<select name="product_choose" id="product_choose" class="form-select-lg js-example-basic-single product_list" placeholder="Не выбрано" data-search="true">
										<option></option>
									</select>
								</div>
								
								<span class="underinput-error text-primary d-flex justify-content-left align-items-lg-center">
									* Нажмите флажок для получения закупочной цены
								</span>

							</div>							
						</div>
						
						<div class="col-lg-6 px-2 mb-4">
							<div class="form-group">
							
								<label class='control-label font-weight-bold' for="service_choose">Выбрать услугу<span class="text-danger"></span></label>		
								
								<select name="service_choose" id="service_choose" class="service_list js-example-basic-single form-select-lg" placeholder="Не выбрано" data-search="true">
									<option></option>
								</select>

							</div>							
						</div>
															
					</div>
							
					<div class="row">

						<span name="error_services_products_bill_list" id='error_services_products_bill_list' class="error_response underinput-error text-danger d-flex justify-content-left align-items-lg-center mb-4">

						</span>
					
						<div class="product_bills_plate px-2" name="product_bills_plate" id='product_bills_plate'>
												
							<div class="border-bottom my-2 pb-1 d-none" name="title_product_bills_plate" id='title_product_bills_plate'>
								<h5 class="text-body">Товары</h5>	
							</div>
											
							<div class="product_list_bills_plate" name="product_list_bills_plate" id='product_list_bills_plate'>
														
							</div>
	
							<div class='border-top align-items-center mt-2 mx-0 px-0 d-none' name="summ_product_bills_plate" id='summ_product_bills_plate'>

								
								<div class='d-flex flex-row-reverse py-1 align-items-center mx-0 px-0' >
								
									<div class='mb-2 pt-2'>
										<input name='product_discount' class='form-control form-control-lg text-center input-number' id='product_discount' style="width:200px;" value="0" min="0" max="99" oninput="this.value = this.value.replace(/[^0-9]/g, '').replace(/(\..*?)\..*/g, '$1');" pattern="^\d{0,2}?$">
									</div>
									
									<div class='mb-2 me-2'>
										<h5 class="text-body">Скидка %</h5>
									</div>
								
								</div>
								
								<div class='d-flex flex-row-reverse py-1 align-items-center mx-0 px-0'>
								
									<div class='mb-2'>
										<input name='product_summ' class='form-control form-control-lg text-center' id='product_summ' disabled readonly style="width:200px;">
								
									</div>
									
									<div class='mb-2 me-2'>
										<h5 class="text-body">Итого товаров руб.</h5>
									</div>
								
								</div>

							</div>

						</div>
						
						
						
						<div class="service_bills_plate px-2" name="service_bills_plate" id='service_bills_plate'>
												
							<div class="border-bottom my-2 py-1 d-none" name="title_service_bills_plate" id='title_service_bills_plate'>
								<h5 class="text-body">Услуги</h5>	
							</div>
											
							<div class="service_list_bills_plate" name="service_list_bills_plate" id='service_list_bills_plate'>
														
							</div>
	
							<div class='border-top align-items-center mt-2 mx-0 px-0 d-none' name="summ_service_bills_plate" id='summ_service_bills_plate'>

								
								<div class='d-flex flex-row-reverse py-1 align-items-center mx-0 px-0' >
								
									<div class='mb-2 pt-2'>
										<input name='service_discount' class='form-control form-control-lg text-center input-number' id='service_discount' style="width:200px;" value="0" min="0" max="99" oninput="this.value = this.value.replace(/[^0-9]/g, '').replace(/(\..*?)\..*/g, '$1');" pattern="^\d{0,2}?$">	
									</div>
									
									<div class='mb-2 me-2'>
										<h5 class="text-body">Скидка %</h5>
									</div>
								
								</div>
								
								<div class='d-flex flex-row-reverse py-1 align-items-center mx-0 px-0'>
								
									<div class='mb-2'>
										<input name='service_summ' class='form-control form-control-lg text-center' id='service_summ' disabled readonly style="width:200px;">
								
									</div>
									
									<div class='mb-2 me-2'>
										<h5 class="text-body">Итого услуг руб.</h5>
									</div>
								
								</div>

							</div>

						</div>
						
						
						<div class="mt-2 summ_bills_plate mx-0 px-2" name="summ_bills_plate" id='summ_bills_plate'>
						
							<div class='border-top align-items-center mx-0 px-0'>
									
								<div class='d-flex flex-row-reverse py-1 align-items-center mx-0 px-0 mt-2'>
								
									<div class='mb-2'>
										<input name='bill_summ' value='0.00' class='form-control form-control-lg text-center' id='bill_summ' disabled readonly style="width:200px;">
								
									</div>
									
									<div class='mb-2 me-2'>
										<h5 class="text-body">Всего по счету руб.</h5>
									</div>
								
								</div>
								
								<div class='d-flex flex-row-reverse py-1 align-items-center mx-0 px-0 mt-2'>
								
									<div class='mb-2'>
										<input name='bill_pay' value='0.00' class='form-control form-control-lg text-center' id='bill_pay' disabled readonly style="width:200px;">
								
									</div>
									
									<div class='mb-2 me-2'>
										<h5 class="text-body">Оплачено по счету руб.</h5>
									</div>
								
								</div>
								
								<div class='d-flex flex-row-reverse py-1 align-items-center mx-0 px-0 mt-2'>
								
									<div class='mb-2'>
										<input name='bill_last' value='0.00' class='form-control form-control-lg text-center' id='bill_last' disabled readonly style="width:200px;">
								
									</div>
									
									<div class='mb-2 me-2'>
										<h5 class="text-body">Осталось оплатить по счету руб.</h5>
									</div>
								
								</div>

							</div>
						
						</div>
						
					
					</div>
					
				</form>

				<script>
				
					$(document).ready(function () {		
		
						initialize_count_limits(); 
						
						// Очистить один платеж
						$("body").on("click",".remove-pay",function(){ 
							$(this).parents(".pay-group").remove();
							
							product_count_summ();
							service_count_summ();
						});
				
					});
									
				
					// Ограничение ввода в input количества товара
					var oldVal = '';

					function initialize_count_limits() {
					
						$('.input-number').on('input',function(e){
							
							// Если пустое значение, то 0
							if ($(this).val() === '') {
								$(this).val(0);
							}  

							var regex = new RegExp($(this).attr('pattern'), 'g');
							var newVal = $(this).val();

							if(regex.test(newVal) & newVal !== '.'){
								oldVal = $(this).val();	
							} else {
								$(this).val(oldVal);
							}
							
							product_count_summ();
							service_count_summ();

						});
						
						
						$('.input-number-service').on('input',function(e){
							
							// Если пустое значение, то 0
							if ($(this).val() === '') {
								$(this).val(0);
							}  

							var regex = new RegExp($(this).attr('pattern'), 'g');
							var newVal = $(this).val();

							if(regex.test(newVal) & newVal !== '.'){
								oldVal = $(this).val();	
							} else {
								$(this).val(oldVal);
							}
							
							product_count_summ();
							service_count_summ();

						});
						
						
						$('.input-number-pay').on('input',function(e){
							
							// Если пустое значение, то 0
							if ($(this).val() === '') {
								$(this).val(0);
							}  

							var regex = new RegExp($(this).attr('pattern'), 'g');
							var newVal = $(this).val();

							if(regex.test(newVal) & newVal !== '.'){
								oldVal = $(this).val();	
							} else {
								$(this).val(oldVal);
							}

						});

					}
					
		
					// Закрытие всех открытых popup при начале скрола модального окна
					$('.modal-body').scroll(function(){

						// datepicker
						$('#bill_date').datepicker("hide");
						$('#bill_date').blur();
						
					});
					
					
								
					// Слушатель выбора товара
					$('#product_choose').on("select2:select", function(e) {

						if ($('#product_choose').val() != null) {
							
							$.ajax({
								url: '/guides/one_product_data',
								method:'GET',
								dataType:'json',
								data: {query:$('#product_choose').val()},
								success: function(data) {
																	
									if($.isEmptyObject(data.error)){
										
										var price = 0;
										
										if($('#new_bill').find('#product_choose_checkbox').is(':checked')) {
											price = parseFloat(data.in_price).toFixed(2);
										} else {
											price = parseFloat(data.out_price).toFixed(2);
										}
										
										$('#new_bill').find('#error_services_products_bill_list').empty();

										$('#error_services_products_bill_list').addClass('d-none'); 
										
										$('#new_bill').find('#product_choose').val(0).trigger('change');

										$('#new_bill').find('#product_choose_checkbox').prop('checked', false);	
																			
										$('#summ_product_bills_plate').removeClass('d-none');  

										$('#title_product_bills_plate').removeClass('d-none'); 
										
										var html = "<div class='bill_product_group pt-2'>"
											+"<div class='d-flex flex-wrap px-0 align-items-center'>"							
												+"<div class='pe-2 mb-2' style='width:445px;'>"	
													+"<input name='product_name_todb' value='"+data.title+"' class='form-control form-control-lg' id='product_name_todb' placeholder='Название' disabled readonly>"	
													+"<input name='product_id_todb' value='"+data.id+"' class='d-none' id='product_id_todb' disabled readonly>"
												+"</div>"	
												+"<div class='pe-2 mb-2' style='width:130px;'>"	
													+"<input name='product_edizm_todb' value='"+data.edizm+"' class='form-control form-control-lg' id='product_edizm_todb' placeholder='Название' disabled readonly>"	
												+"</div>"
												+"<div class='pe-2 mb-2' style='width:130px;'>"		
													+"<input name='product_price_todb' value='"+parseFloat(price).toFixed(2)+"' class='form-control form-control-lg' id='product_price_todb' placeholder='Цена' disabled readonly>"	
												+"</div>"	
												+"<div class='pe-2 mb-2' style='width:180px;'>"		
													+"<div class='input-group'>"	
														+"<button class='btn btn-danger btn-number' onclick='minusone_product.call(this)' type='button' id='button-plus' data-type='minus' data-field='quant'>"									  
															+"<i data-feather='minus'></i>"	
														+"</button>"
														+"<input type='text' name='product_count_todb' id='product_count_todb' class='form-control form-control-lg text-center input-number product_count_todb' value='1' min='0' max='999' oninput=\"this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\\\..*?)\\\..*/g, '$1');\" pattern=\"^\\\d{0,3}(\\\.\\\d{0,2})?$\">"	//   
														+"<button class='btn btn-success btn-number' onclick='plusone_product.call(this)'  type='button' id='button-minus' data-type='plus' data-field='quant'>"	  
															+"<i data-feather='plus'></i>"	
														+"</button>"	
													+"</div>"	
												+"</div>"	
												+"<div class='pe-2 mb-2' style='width:130px;'>"		
													+"<input name='product_price_count' class='form-control form-control-lg product_price_count' id='product_price_count' placeholder='Стоимость' disabled readonly>"	
												+"</div>"	
												+"<div class='mb-2 justify-content-left align-items-center align-self-center d-flex' style='width:25px;'>"	
													+"<a class='nav-link p-1 remove-product align-self-center' role='button'>"	
														+"<img class='img-responsive' width='25' height='25' src='images/delete_photo.png'>"	
													+"</a>"	
												+"</div>"	
												
											+"</div>"	
										+"</div>";		
										
																
										$(".product_list_bills_plate").append(html);
										
										// Инициализация иконок
										feather.replace();
										
										initialize_count_limits();
										
										product_count_summ();
			
									}else{
										
										$('#new_bill').find('#product_choose').val(0).trigger('change');
										$('#new_bill').find('#product_choose_checkbox').prop('checked', false);

										product_count_summ();										
										
										toastr.error(data.error);
									}
									
								},
								error: function(err) {	
									//toastr.error(err);
									
									product_count_summ();
									
								}
							});
					
						} 

					});
					
					
	
					// Слушатель выбора услуги
					$('#service_choose').on("select2:select", function(e) { 
						
						if ($('#service_choose').val() != null) {
								
							$.ajax({
								url: '/guides/one_service_data',
								method:'GET',
								dataType:'json',
								data: {query:$('#service_choose').val()},
								success: function(data) {
																	
									if($.isEmptyObject(data.error)){
										
										$('#new_bill').find('#error_services_products_bill_list').empty();

										$('#error_services_products_bill_list').addClass('d-none'); 
										
										$('#new_bill').find('#service_choose').val(0).trigger('change');
																			
										$('#summ_service_bills_plate').removeClass('d-none');  

										$('#title_service_bills_plate').removeClass('d-none'); 
										
										var html = "<div class='bill_service_group pt-2'>"
											+"<div class='d-flex flex-wrap px-0 align-items-center'>"							
												+"<div class='pe-2 mb-2' style='width:300px;'>"	
													+"<input name='service_name_todb' value='"+data.title+"' class='form-control form-control-lg' id='service_name_todb' placeholder='Название' disabled readonly>"	
													+"<input name='service_id_todb' value='"+data.id+"' class='d-none' id='service_id_todb' disabled readonly>"
												+"</div>"

												+"<div class='pe-2 mb-2' style='width:275px;'>"	
													+"<select name='service_doctor_todb' id='service_doctor_todb_"+service_bill_count()+"' class='form-select-lg block' style='width: 100%;' placeholder='Выбрать сотрудника' data-search='true'>"
														+"<option></option>"
													+"</select>"
												+"</div>"
																							
												+"<div class='pe-2 mb-2' style='width:130px;'>"		
													+"<input name='service_price_todb' value='"+parseFloat(data.price).toFixed(2)+"' class='form-control form-control-lg input-number-service' id='service_price_todb' placeholder='Цена' autocomplete='off' oninput=\"this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\\\..*?)\\\..*/g, '$1');\" pattern=\"^\\\d{0,6}(\\\.\\\d{0,2})?$\">"
												+"</div>"	
												+"<div class='pe-2 mb-2' style='width:180px;'>"		
													+"<div class='input-group'>"	
														+"<button class='btn btn-danger btn-number' onclick='minusone_service.call(this)' type='button' id='button-plus' data-type='minus' data-field='quant'>"									  
															+"<i data-feather='minus'></i>"	
														+"</button>"
														+"<input type='text' name='service_count_todb' id='service_count_todb' class='form-control form-control-lg text-center input-number' value='1' min='0' max='999' oninput=\"this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\\\..*?)\\\..*/g, '$1');\" pattern=\"^\\\d{0,3}(\\\.\\\d{0,2})?$\">"	//   
														+"<button class='btn btn-success btn-number' onclick='plusone_service.call(this)'  type='button' id='button-minus' data-type='plus' data-field='quant'>"	  
															+"<i data-feather='plus'></i>"	
														+"</button>"	
													+"</div>"	
												+"</div>"	
												+"<div class='pe-2 mb-2' style='width:130px;'>"		
													+"<input name='service_price_count' class='form-control form-control-lg product_price_count' id='service_price_count' placeholder='Стоимость' disabled readonly>"	
												+"</div>"	
												+"<div class='mb-2 justify-content-left align-items-center align-self-center d-flex' style='width:25px;'>"	
													+"<a class='nav-link p-1 remove-service align-self-center' role='button'>"	
														+"<img class='img-responsive' width='25' height='25' src='images/delete_photo.png'>"	
													+"</a>"	
												+"</div>"	
												
											+"</div>"	
										+"</div>";	
										
																
										$(".service_list_bills_plate").append(html);
										
										// Инициализация иконок
										feather.replace();
										
										initialize_count_limits();

										
										
										// Установка списка врачей
										$("#service_doctor_todb_"+service_bill_count_after()).select2({ 
											placeholder: "Выберите сотрудника",
											theme: "bootstrap-5",
											allowClear: true,
											data: doctorsforselect 
											}).on('select2:unselecting', function() {
												$(this).data('unselecting', true);
											}).on('select2:opening', function(e) {
												if ($(this).data('unselecting')) {
													$(this).removeData('unselecting');
													e.preventDefault();
												}
											});
											
										// Установка врача (текущий пользователь)
										$("#service_doctor_todb_"+service_bill_count_after()).val({{ Auth::user()->staff_id }}).trigger('change');


										service_count_summ();

									}else{
										
										$('#new_bill').find('#product_choose').val(0).trigger('change');
										
										service_count_summ();
										
										toastr.error(data.error);
									}
									
								},
								error: function(err) {	
									//toastr.error(err);
									
									service_count_summ();
								}
							});						
							
						} 

					});
					
			
				</script>

				</div>
								
			</div>
			<div class="modal-footer pe-4">
				<button type="button" class="btn btn-primary" onclick="save_bill()">Сохранить</button>		
			</div>
		</div>
	</div>
</div>


<!-- Удалить счет -->
<div class="modal hide mycontainer" id="delete_bill" role="dialog" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false" style="display: none">
	<div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title px-3" id="staticBackdropLabel">Удалить счет</h5>
				<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
			</div>
			<div class="modal-body">
				
				<div class="px-3">
				
					<input name="billdelete_id" id='billdelete_id' value="0" hidden="true"/>
					
					<div class="text-break">Вы действительно хотите удалить счет ?</div>

				</div>
								
			</div>
			<div class="modal-footer pe-4">
				<button type="button" class="btn btn-danger" onclick="delete_bill()">Удалить</button>		
			</div>
		</div>
	</div>
</div>


<!-- Оплатить счета -->
<div class="modal hide mycontainer" id="pay_all_bill" role="dialog" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false" style="display: none">
	<div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title px-3" id="staticBackdropLabel">Изменить статус счетов на "ОПЛАЧЕН"</h5>
				<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
			</div>
			<div class="modal-body">
				
				<div class="px-3">
				
					<input name="pay_bills_id" id='pay_bills_id' value="0" hidden="true"/>
					
					<div class="text-break">Вы действительно хотите изменить статус выбранных счетов на "ОПЛАЧЕН" ?</div>

				</div>
								
			</div>
			<div class="modal-footer pe-4">
				<button type="button" class="btn btn-danger" onclick="pay_selected_bills()">Подтвердить</button>		
			</div>
		</div>
	</div>
</div>

