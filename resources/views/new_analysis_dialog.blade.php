<script> 

	var analysis_id = '';
	
	var neworchange = '';
	
	var analysisopened = '';
	
	// Открытие диалога
	function new_analysis(id) {
		
		// Очистка текстов-ошибок в span
		$('#new_analysis').find('.error_response').html("&nbsp;");
		
		$('#new_analysis').find('#new_analysis_form')[0].reset();
		$('#analysis_date').datepicker().data('datepicker').clear();
		$('#new_analysis').find('#analysis_doctor').val(0).trigger('change');
		$('#new_analysis').find('#to_visit_new_analysis').val(0).trigger('change');
		$('#new_analysis').find('#analysis_name').val('').trigger('change');
		$('#new_analysis').find('#analysis_plate').val(0).trigger('change');
		$(".analysis_plate").empty();
		

		if (id) {
			
			neworchange = 1;
			
			analysis_id = id;
			
			$('#new_analysis').find('#new_analysis_title').html("Изменить анализ");
						
			$.ajax({
				url: '/patientcard/get_analysis_data',
				method:'GET',
				dataType:'json',
				data: {analysis_id: id},
				success: function(data) {
										
					if($.isEmptyObject(data.error)){

						// Инициализация календаря
						var myDataPicker_2 = $('#analysis_date').datepicker({ 
							language : 'ru', 
							autoClose : true,
							clearButton: true,
						}).data('datepicker');
						
						// Установка даты
						var dateParts = data.success['date_of_analysis'].split("-");
						var jsDate = new Date(dateParts[0], dateParts[1] - 1, dateParts[2].substr(0,2));
						
						myDataPicker_2.selectDate(jsDate);
						
						// Установка врача (текущий пользователь)
						if (data.doctorid == null) {
							toastr.error('Врач не найден, возможно он был удален');
						} else {
							$('#analysis_doctor').val(data.doctorid).trigger('change');
						}
						
						if (data.success['visit_id'] != null && data.success['visit_id'] != 0) {
							$('#to_visit_new_analysis').val(data.success['visit_id']).trigger('change');
						}
						
						$('#new_analysis').find('#analysis_name').val(data.success['analysis_name']);
						
						if (data.analysis_template_id != null && data.analysis_template_id != 0) {
							$('#analysis_plate').val(data.analysis_template_id).trigger('change');
						}
						
						JSON.parse(data.success['analysis_text']).forEach(function(item, i, arr) {
					  
							if (item.type_todb == 0) {
							  
								var html = "<div class='analisys-titres-group'>"
									+"<div class='row px-0 py-1 align-items-center'>"
																	
										+"<div class='col-lg-4 pe-1 mb-2'>"		
											+"<input name='name_todb' class='form-control form-control-lg' id='name_todb' value='"+item.name_todb+"' placeholder='Название' disabled readonly>"
										+"</div>"

										+"<div class='col-lg-2 mb-2'>"
											+"<input name='result_todb' class='form-control form-control-lg' id='result_todb' value='"+item.result_todb+"' placeholder='Результат' oninput=\"this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\\\..*?)\\\..*/g, '$1');\" />"
										+"</div>"
										
										+"<div class='col-lg-2 pe-1 mb-2'>"		
											+"<input name='edizm_todb' class='form-control form-control-lg' id='edizm_todb' value='"+item.edizm_todb+"' placeholder='Ед. изм.' disabled readonly>"
										+"</div>"
										
										+"<div class='col-lg-2 pe-1 mb-2'>"		
											+"<input name='from_todb' class='form-control form-control-lg' id='from_todb' value='"+item.from_todb+"' disabled readonly placeholder='От' oninput=\"this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\\\..*?)\\\..*/g, '$1');\" />"
										+"</div>"

										+"<div class='col-lg-2 pe-1 mb-2'>"		
											+"<input name='to_todb' class='form-control form-control-lg' id='to_todb' value='"+item.to_todb+"' disabled readonly placeholder='До' oninput=\"this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\\\..*?)\\\..*/g, '$1');\" />"
										+"</div>"

										+"<input name='type_todb' id='type_todb' value='"+item.type_todb+"' hidden='true'/>"
									+"</div>"
								+"</div>";
							  
							} else if (item.type_todb == 1) {
							  
							  
								var html = "<div class='analisys-titres-group'>"
									+"<div class='row px-0 py-1 align-items-center'>"
																	
										+"<div class='col-lg-4 pe-1 mb-2'>"		
											+"<input name='name_todb' class='form-control form-control-lg' id='name_todb' value='"+item.name_todb+"' placeholder='Название' disabled readonly>"
										+"</div>"
										
										+"<div class='col-lg-4 mb-2'>"
											+"<input name='result_todb' class='form-control form-control-lg' id='result_todb' value='"+item.result_todb+"' placeholder='Результат'>"
										+"</div>"
										
										+"<div class='col-lg-4 pe-1 mb-2'>"		
											+"<input name='value_todb' class='form-control form-control-lg' id='value_todb' value='"+item.value_todb+"' placeholder='Значение' disabled readonly>"
										+"</div>"
										
										+"<input class='type_todb' name='type_todb' id='type_todb' value='"+item.type_todb+"' hidden='true'/>"
									+"</div>"
								+"</div>";
  
							}
							
							$(".analysis_plate").append(html);
							  
						});
						
						$('#new_analysis').modal('show');
						
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
			
			analysis_id = '';
			
			$('#new_analysis').find('#new_analysis_title').html("Новый анализ");
								
			// Инициализация календаря и установка даты в приеме
			var myDataPicker_2 = $('#analysis_date').datepicker({ 
				language : 'ru', 
				autoClose : true,
				clearButton: true,
			}).data('datepicker');
			
			myDataPicker_2.selectDate(new Date());
		
			// Установка врача (текущий пользователь)
			$('#analysis_doctor').val({{ Auth::user()->staff_id }}).trigger('change');
			
			$('#new_analysis').modal('show');
						
		}
	
	}
	
	
	
	// Слушатель кнопки календарик
	function calendarinput6(){  	
		$("#analysis_date").trigger("focus");
	}


	
	// Очистка шаблона
	function clear_analysis_plate_data () {
		
		$(".analysis_plate").empty();
		
		$('#new_analysis').find('#analysis_name').val('').trigger('change');
		
	}
	
	
	
	// Сохраняем анализ 
    function save_analysis(){ 

		// Очистка текстов-ошибок в span
		$('#new_analysis').find('.error_response').html("&nbsp;");
		
		
		var form = $('#new_analysis_form')[0];
		
		var formData = new FormData(form);
		
		if ({{ $patient_id }}) {
			formData.append("anymal_id", {{ $patient_id }});
		}
		
		formData.append("analysis_id", analysis_id);
		
		if ($('#analysis_doctor').val()) {
			formData.append("doctor_text", $('#analysis_doctor').select2('data')[0].text);
		}
		
		
		var analysis_titres_data = new Array();
		
		var items = $('.analysis_plate').find('.analisys-titres-group').each(function() {
			
			var hash = {};
			
			var type = $(this).find('#type_todb').val();
			
			if (type == 0) {
				hash['type_todb'] = type;
				hash['name_todb'] = $(this).find('#name_todb').val();
				hash['edizm_todb'] = $(this).find('#edizm_todb').val();	
				hash['name_todb'] = $(this).find('#name_todb').val();
				hash['from_todb'] = $(this).find('#from_todb').val();
				hash['to_todb'] = $(this).find('#to_todb').val();
				hash['result_todb'] = $(this).find('#result_todb').val();
			} else if (type == 1) {
				hash['type_todb'] = type;
				hash['name_todb'] = $(this).find('#name_todb').val();
				hash['value_todb'] = $(this).find('#value_todb').val();
				hash['result_todb'] = $(this).find('#result_todb').val();
			}
			
			analysis_titres_data.push(hash);
		
        });
		
		
		if (analysis_titres_data.length !== 0) {
			formData.append("analysis_titres_data", JSON.stringify(analysis_titres_data));
		}

		// Записываем данные
		$.ajax({
		url: '/patientcard/new_analysis',
		type: 'POST',
		processData: false,
        contentType: false,
		data: formData,
			
		success: function(data) {
							
			if($.isEmptyObject(data.error)){
				
				toastr.info(data.success); 
				
				// Обновляем список анализов
				show_analysis_list();
				
				// Обновляем данные анализа и открываем
				open_analysis(data.analysisid);
				
				$('#new_analysis').modal('toggle');
			
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
	
		
	// Получение ошибок
	function printErrorMsg (msg) {
				
		if (msg.length > 0) {
			
			toastr.error(msg);
		
		} else {
		
		// Заполнение span текстами ошибок 
		$.each(msg,function(field_name,error){
			$('#error_'+field_name).html(error);
        })
		
		}
						
	}
	
</script> 

<div class="modal hide mycontainer" id="new_analysis" name="new_analysis" role="dialog" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false" style="display: none">
	<div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
		<div class="modal-content">
			<div class="modal-header">
				<h5 id="new_analysis_title" name="new_analysis_title" class="modal-title px-3" id="staticBackdropLabel"></h5>
				<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
			</div>
			<div class="modal-body" id="new_analysis_body" name="new_analysis_body">
				
				<div class="px-4">
				
				<form id="new_analysis_form" autocomplete="off" enctype="multipart/form-data">					
				@csrf
				
					<div class="row">
						<div class="col-lg-4 px-2 mb-2 mb-lg-0">
						
							<div class="form-group">
								<label class='control-label font-weight-bold' for="analysis_date">Дата <span class="text-danger">*</span></label>		
								<div class="input-group input-group-lg">
									<input name="analysis_date" id="analysis_date" type="text" class="form-control form-control-lg datepicker-here appearance-none bg-white" data-position='bottom left' placeholder="Выберите дату" readonly />
									<button type="button" onclick="calendarinput6()" class="bg-white input-group-text" id="inputGroup-sizing-lg">
										<img src="{{ asset('images/calendar.png') }}" class="img-responsive"  width="30" height="30" />
									</button>
								</div>	
								<span name="error_analysis_date" id='error_analysis_date' class="error_response underinput-error text-danger d-flex justify-content-left align-items-lg-center">
								&nbsp;
								</span>

							</div>
						
						</div>
						
						<div class="col-lg-8 px-2 mb-2 mb-lg-0">		
							<div class="form-group">
								<label class='control-label font-weight-bold' for="analysis_doctor">Лечащий врач <span class="text-danger">*</span></label>
								
								<select name="analysis_doctor" id="analysis_doctor" class="form-select-lg js-example-basic-single block doctors" style="width: 100%;" placeholder="Не выбрано" data-search="true">
									<option></option>
								</select>
								<span name="error_analysis_doctor" id='error_analysis_doctor' class="error_response underinput-error text-danger d-flex justify-content-left align-items-lg-center">
								&nbsp;
								</span>
							</div>	
		
						</div>
															
					</div>	
					
					<div class="row">
						<div class="col-lg-4 px-2 mb-2 mb-lg-0">
						
							<div class="form-group">
							
							
								<label class='control-label font-weight-bold' for="to_visit_new_analysis">К визиту <span class="text-danger"></span></label>
								
								<select name="to_visit_new_analysis" id="to_visit_new_analysis" class="form-select-lg js-example-basic-single block tovisit" style="width: 100%;" placeholder="Не выбрано" data-search="true">
									<option></option>
								</select>
								<span name="error_to_visit_new_analysis" id='error_to_visit_new_analysis' class="error_response underinput-error text-danger d-flex justify-content-left align-items-lg-center">
								&nbsp;
								</span>

							</div>
						
						</div>
						
						<div class="col-lg-8 px-2 mb-2 mb-lg-0">		
							<div class="form-group">
								<label class='control-label font-weight-bold' for="analysis_name">Наименование <span class="text-danger">*</span></label>
								<input name="analysis_name" class="form-control form-control-lg" id='analysis_name' placeholder="Наименование">
								<span name="error_analysis_name" id='error_analysis_name' class="error_response underinput-error text-danger d-flex justify-content-left align-items-lg-center">
								&nbsp;
								</span>
							</div>	
		
						</div>
															
					</div>	
					
					<div class="row">
				
						<div class="col-lg px-2 mb-2">
							<div class="form-group">			
								<label class='control-label font-weight-bold' for="analysis_result">Шаблон анализа <span class="text-danger"></span></label>
								
								<div class="d-lg-flex justify-content-left align-items-lg-center">					
									
									<div class="col-lg-6 mb-2">	
										<select name="analysis_plate" id="analysis_plate" class="form-select-lg js-example-basic-single analysis_list" style="width: 100%;" placeholder="Не выбрано" data-search="true">
											<option></option>
										</select>
									</div>		
									
								</div>

							</div>							
						</div>
							
					</div>
							
					<div class="row">
					
						<div class="mt-2 analysis_plate px-2" name="analysis_plate" id='analysis_plate'>
							
						</div>
						
						<span name="error_analysis_titres_data" id='error_analysis_titres_data' class="error_response underinput-error text-danger d-flex justify-content-left align-items-lg-center mb-2">
						&nbsp;
						</span>
					
					</div>
					

				</form>

				<script>
				
					// Закрытие всех открытых popup при начале скрола модального окна
					$('.modal-body').scroll(function(){

						// datepicker
						$('#analysis_date').datepicker("hide");
						$('#analysis_date').blur();
						
					});


					// Слушатель выбора шаблона "Анализ"
					$('#analysis_plate').on("select2:select", function(e) { 
											
						$.ajax({
							url: '/guides/getanalisystemplatedata',
							method:'GET',
							dataType:'json',
							data: {query:$('#analysis_plate').val()},
							success: function(data) {
								
								if($.isEmptyObject(data.error)){
									
									$('#new_analysis').find('#error_analysis_titres_data').html("&nbsp;");
									
									$('#new_analysis').find('#analysis_name').val(data.title).trigger('change');
									
									$(".analysis_plate").empty();
									
									data.text.forEach(function(item, i, arr) {
						  
										if (item.type_todb == 0) {
										  
											var html = "<div class='analisys-titres-group'>"
												+"<div class='row px-0 py-1 align-items-center'>"
																				
													+"<div class='col-lg-4 pe-1 mb-2'>"		
														+"<input name='name_todb' class='form-control form-control-lg' id='name_todb' value='"+item.name_todb+"' placeholder='Название' disabled readonly>"
													+"</div>"

													+"<div class='col-lg-2 mb-2'>"
														+"<input name='result_todb' class='form-control form-control-lg' id='result_todb' value='' placeholder='Результат' oninput=\"this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\\\..*?)\\\..*/g, '$1');\" />"
													+"</div>"
													
													+"<div class='col-lg-2 pe-1 mb-2'>"		
														+"<input name='edizm_todb' class='form-control form-control-lg' id='edizm_todb' value='"+item.edizm_todb+"' placeholder='Ед. изм.' disabled readonly>"
													+"</div>"
													
													+"<div class='col-lg-2 pe-1 mb-2'>"		
														+"<input name='from_todb' class='form-control form-control-lg' id='from_todb' value='"+item.from_todb+"' disabled readonly placeholder='От' oninput=\"this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\\\..*?)\\\..*/g, '$1');\" />"
													+"</div>"

													+"<div class='col-lg-2 pe-1 mb-2'>"		
														+"<input name='to_todb' class='form-control form-control-lg' id='to_todb' value='"+item.to_todb+"' disabled readonly placeholder='До' oninput=\"this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\\\..*?)\\\..*/g, '$1');\" />"
													+"</div>"

													+"<input name='type_todb' id='type_todb' value='"+item.type_todb+"' hidden='true'/>"
												+"</div>"
											+"</div>";
										  
										} else if (item.type_todb == 1) {
										  
										  
											var html = "<div class='analisys-titres-group'>"
												+"<div class='row px-0 py-1 align-items-center'>"
																				
													+"<div class='col-lg-4 pe-1 mb-2'>"		
														+"<input name='name_todb' class='form-control form-control-lg' id='name_todb' value='"+item.name_todb+"' placeholder='Название' disabled readonly>"
													+"</div>"
													
													+"<div class='col-lg-4 mb-2'>"
														+"<input name='result_todb' class='form-control form-control-lg' id='result_todb' value='' placeholder='Результат'>"
													+"</div>"
													
													+"<div class='col-lg-4 pe-1 mb-2'>"		
														+"<input name='value_todb' class='form-control form-control-lg' id='value_todb' value='"+item.value_todb+"' placeholder='Значение' disabled readonly>"
													+"</div>"
													
													+"<input class='type_todb' name='type_todb' id='type_todb' value='"+item.type_todb+"' hidden='true'/>"
												+"</div>"
											+"</div>";
			  
										}
										
										$(".analysis_plate").append(html);
										  
									});
																						
								}else{
									toastr.error(data.error);
								}
								
							},
							error: function(err) {	
								//toastr.error(err);
							}
						});
					});
			
				</script>

				</div>
								
			</div>
			<div class="modal-footer pe-4">
				<button type="button" class="btn btn-primary" onclick="save_analysis()">Сохранить</button>		
			</div>
		</div>
	</div>
</div>