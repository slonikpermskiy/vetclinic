<script> 

	var vacine_id = '';
	
	var neworchange = '';
	
	// Открытие диалога
	function new_vacine(id) {
		
		// Очистка текстов-ошибок в span
		$('#new_vacine').find('.error_response').html("&nbsp;");
		
		$('#new_vacine').find('#new_vacine_form')[0].reset();
		$('#vacine_date').datepicker().data('datepicker').clear();
		$('#new_vacine').find('#vacine_doctor').val(0).trigger('change');		
		$('#new_vacine').find('#vacine_name').val(0).trigger('change');

		if (id) {
			
			neworchange = 1;
			
			vacine_id = id;
			
			$('#new_vacine').find('#new_vacine_title').html("Изменить вакцинацию");
						
			$.ajax({
				url: '/patientcard/get_vacine_data',
				method:'GET',
				dataType:'json',
				data: {vacines_id: id},
				success: function(data) {
										
					if($.isEmptyObject(data.error)){

						// Инициализация календаря
						var myDataPicker_2 = $('#vacine_date').datepicker({ 
							language : 'ru', 
							autoClose : true,
							clearButton: true,
						}).data('datepicker');
						
						// Установка даты
						var dateParts = data.success['date_of_vacine'].split("-");
						var jsDate = new Date(dateParts[0], dateParts[1] - 1, dateParts[2].substr(0,2));
						
						myDataPicker_2.selectDate(jsDate);
						
						// Установка врача (текущий пользователь)
						if (data.doctorid == null) {
							toastr.error('Врач не найден, возможно он был удален');
						} else {
							$('#vacine_doctor').val(data.doctorid).trigger('change');
						}
						
						
						
						if (data.vacines_type_id != null && data.vacines_type_id != 0) {

							var $vacine = $("<option selected='selected'></option>").val(data.vacines_type_id).text(data.success['vacine_name']);
							$('#vacine_name').append($vacine).trigger('change');
							
						}
						
						
						setTimeout(function () {
						
							$('#new_vacine').modal('show');
						
						}, 300);
						
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
			
			vacine_id = '';
			
			$('#new_vacine').find('#new_vacine_title').html("Новая вакцинация");
								
			// Инициализация календаря и установка даты в приеме
			var myDataPicker_2 = $('#vacine_date').datepicker({ 
				language : 'ru', 
				autoClose : true,
				clearButton: true,
			}).data('datepicker');
			
			myDataPicker_2.selectDate(new Date());
		
			// Установка врача (текущий пользователь)
			$('#vacine_doctor').val({{ Auth::user()->staff_id }}).trigger('change');
			
			$('#new_vacine').modal('show');
						
		}
	
	}
	
	
	
	// Слушатель кнопки календарик
	function calendarinput7(){  	
		$("#vacine_date").trigger("focus");
	}

	
	
	// Сохраняем анализ 
    function save_vacine(){ 

		// Очистка текстов-ошибок в span
		$('#new_vacine').find('.error_response').html("&nbsp;");
		
		
		var form = $('#new_vacine_form')[0];
		
		var formData = new FormData(form);
		
		if ({{ $patient_id }}) {
			formData.append("anymal_id", {{ $patient_id }});
		}
		
		formData.append("vacine_id", vacine_id);
		
		if ($('#vacine_doctor').val()) {
			formData.append("doctor_text", $('#vacine_doctor').select2('data')[0].text);
		}
		
		
		if ($('#vacine_name').val()) {
			formData.append("vacine_text", $('#vacine_name').select2('data')[0].text);
		}

		
		// Записываем данные
		$.ajax({
		url: '/patientcard/new_vacine',
		type: 'POST',
		processData: false,
        contentType: false,
		data: formData,
			
		success: function(data) {
							
			if($.isEmptyObject(data.error)){
				
				toastr.info(data.success);

				// Обновляем данные пациента-клиента на странице
				get_one_client_patient_data({{ $patient_id }});				
				
				// Обновляем список вакцин
				show_vacine_list();
								
				$('#new_vacine').modal('toggle');
			
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

<div class="modal hide mycontainer" id="new_vacine" name="new_vacine" role="dialog" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false" style="display: none">
	<div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
		<div class="modal-content">
			<div class="modal-header">
				<h5 id="new_vacine_title" name="new_vacine_title" class="modal-title px-3" id="staticBackdropLabel"></h5>
				<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
			</div>
			<div class="modal-body" id="new_vacine_body" name="new_vacine_body">
				
				<div class="px-4">
				
				<form id="new_vacine_form" autocomplete="off" enctype="multipart/form-data">					
				@csrf
				
					<div class="row">
						<div class="col-lg-4 px-2 mb-2 mb-lg-0">
						
							<div class="form-group">
								<label class='control-label font-weight-bold' for="vacine_date">Дата <span class="text-danger">*</span></label>		
								<div class="input-group input-group-lg">
									<input name="vacine_date" id="vacine_date" type="text" class="form-control form-control-lg datepicker-here appearance-none bg-white" data-position='bottom left' placeholder="Выберите дату" readonly />
									<button type="button" onclick="calendarinput7()" class="bg-white input-group-text" id="inputGroup-sizing-lg">
										<img src="{{ asset('images/calendar.png') }}" class="img-responsive"  width="30" height="30" />
									</button>
								</div>	
								<span name="error_vacine_date" id='error_vacine_date' class="error_response underinput-error text-danger d-flex justify-content-left align-items-lg-center">
								&nbsp;
								</span>

							</div>
						
						</div>
						
						<div class="col-lg-8 px-2 mb-2 mb-lg-0">		
							<div class="form-group">
								<label class='control-label font-weight-bold' for="vacine_doctor">Лечащий врач <span class="text-danger">*</span></label>
								
								<select name="vacine_doctor" id="vacine_doctor" class="form-select-lg js-example-basic-single block doctors" style="width: 100%;" placeholder="Не выбрано" data-search="true">
									<option></option>
								</select>
								<span name="error_vacine_doctor" id='error_vacine_doctor' class="error_response underinput-error text-danger d-flex justify-content-left align-items-lg-center">
								&nbsp;
								</span>
							</div>	
		
						</div>
															
					</div>	
					
					<div class="row">
				
						<div class="col-lg px-2 mb-2">
							<div class="form-group">			
								<label class='control-label font-weight-bold' for="vacine_name">Выберите вакцину <span class="text-danger">*</span></label>
								
								<div class="d-lg-flex justify-content-left align-items-lg-center">					
									
									<div class="col-lg-6 mb-2">	
										<select name="vacine_name" id="vacine_name" class="form-select-lg js-example-basic-single" style="width: 100%;" placeholder="Не выбрано" data-search="true">
											<option></option>
										</select>
										<span name="error_vacine_name" id='error_vacine_name' class="error_response underinput-error text-danger d-flex justify-content-left align-items-lg-center">
										&nbsp;
										</span>
									</div>		
									
								</div>

							</div>							
						</div>
							
					</div>
							
				</form>

				<script>
				
					// Закрытие всех открытых popup при начале скрола модального окна
					$('.modal-body').scroll(function(){

						// datepicker
						$('#vacine_date').datepicker("hide");
						$('#vacine_date').blur();
						
					});

				</script>

				</div>
								
			</div>
			<div class="modal-footer pe-4">
				<button type="button" class="btn btn-primary" onclick="save_vacine()">Сохранить</button>		
			</div>
		</div>
	</div>
</div>