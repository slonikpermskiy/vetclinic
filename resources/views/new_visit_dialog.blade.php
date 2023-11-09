<script> 

	var visit_time_id = '';
	
	var neworchange = '';
	
	var phototodelete = new Array();
	
	var visitopened = '';
	
	// Открытие диалога
	function new_visit(id) {
		
		phototodelete = new Array();
		
		// Очистка текстов-ошибок в span
		$('#new_visit').find('.error_response').html("&nbsp;");
		
		$('#new_visit').find('#error_imagetoload').html("");
		
		$('#new_visit').find('#new_visit_form')[0].reset();
		$('#visit_date').datepicker().data('datepicker').clear();
		$('#new_visit').find('#doctor').val(0).trigger('change');
		$('#new_visit').find('#porpose').val(1).trigger('change');
		$('#new_visit').find('#visit_type').val(1).trigger('change');
		$('#new_visit').find('#check_result_plate').val(0).trigger('change');
		$('#new_visit').find('#uploaded_photo_response').html("");
		$('#new_visit').find('#diagnosis_list').val('').trigger("change");
		$('#new_visit').find('#research_list').val('').trigger("change");
		$('#new_visit').find('#analis_list').val('').trigger("change");
		$('#new_visit').find(".diagnosis").empty();
		$('#new_visit').find(".researches").empty();
		$('#new_visit').find(".analisys").empty();
		$('#new_visit').find('#recomendation_plate').val(0).trigger('change');
		
		// Перезагрузка редакторов
		editor.destruct();
		editor = new Jodit("#check_result", {
			  "useSearch": false,
			  "spellcheck": false,
			  "language": "ru",
			  "toolbarSticky": false,
			  "showCharsCounter": false,
			  "showWordsCounter": false,
			  "showXPathInStatusbar": false,
			  "height": null,
			  "minHeight": 400,
			  "maxHeight": 400,
			  "minWidth": null,
			  "enableDragAndDropFileToEditor":false,
			  "buttons": "source,bold,italic,underline,strikethrough,eraser,superscript,subscript,ul,ol,indent,outdent,left,font,fontsize,paragraph,brush,image,copyformat,selectall,hr,table,undo,redo,print",
			  "buttonsMD": "source,bold,italic,underline,strikethrough,eraser,superscript,subscript,ul,ol,indent,outdent,left,font,fontsize,paragraph,brush,image,copyformat,selectall,hr,table,undo,redo,print",
			  "buttonsSM": "source,bold,italic,underline,strikethrough,eraser,superscript,subscript,ul,ol,indent,outdent,left,font,fontsize,paragraph,brush,image,copyformat,selectall,hr,table,undo,redo,print",
			  "buttonsXS": "source,bold,italic,underline,strikethrough,eraser,superscript,subscript,ul,ol,indent,outdent,left,font,fontsize,paragraph,brush,image,copyformat,selectall,hr,table,undo,redo,print"
			});	
		
		editor.value = '';
		
		editor2.destruct();
		editor2 = new Jodit("#recomendation", {
			  "useSearch": false,
			  "spellcheck": false,
			  "language": "ru",
			  "toolbarSticky": false,
			  "showCharsCounter": false,
			  "showWordsCounter": false,
			  "showXPathInStatusbar": false,
			  "height": null,
			  "minHeight": 400,
			  "maxHeight": 400,
			  "minWidth": null,
			  "enableDragAndDropFileToEditor":false,
			  "buttons": "source,bold,italic,underline,strikethrough,eraser,superscript,subscript,ul,ol,indent,outdent,left,font,fontsize,paragraph,brush,image,copyformat,selectall,hr,table,undo,redo,print",
			  "buttonsMD": "source,bold,italic,underline,strikethrough,eraser,superscript,subscript,ul,ol,indent,outdent,left,font,fontsize,paragraph,brush,image,copyformat,selectall,hr,table,undo,redo,print",
			  "buttonsSM": "source,bold,italic,underline,strikethrough,eraser,superscript,subscript,ul,ol,indent,outdent,left,font,fontsize,paragraph,brush,image,copyformat,selectall,hr,table,undo,redo,print",
			  "buttonsXS": "source,bold,italic,underline,strikethrough,eraser,superscript,subscript,ul,ol,indent,outdent,left,font,fontsize,paragraph,brush,image,copyformat,selectall,hr,table,undo,redo,print"
			});	
		
		editor2.value = '';
		
	
		if (id) {
			
			neworchange = 1;
			
			$('#new_visit').find('#new_visit_title').html("Изменить прием");
						
			$.ajax({
				url: '/patientcard/get_visit_data',
				method:'GET',
				dataType:'json',
				data: {visit_id: id},
				success: function(data) {
										
					if($.isEmptyObject(data.error)){
						
						visit_time_id = data.success['visit_date_id'];

						// Отображение фото
						show_photos_for_visit();
												
						// Инициализация календаря
						var myDataPicker = $('#visit_date').datepicker({ 
							language : 'ru', 
							autoClose : true,
							clearButton: true,
						}).data('datepicker');
						
						// Установка даты
						var dateParts = data.success['date_of_visit'].split("-");
						var jsDate = new Date(dateParts[0], dateParts[1] - 1, dateParts[2].substr(0,2));
						
						myDataPicker.selectDate(jsDate);
						
						// Установка врача (текущий пользователь)
						if (data.doctorid == null) {
							toastr.error('Врач не найден, возможно он был удален');
						} else {
							$('#doctor').val(data.doctorid).trigger('change');
						}
												
						$('#porpose').val(data.success['visit_purpose']).trigger('change');
						
						$('#visit_type').val(data.success['visit_type']).trigger('change');
						
						$('#new_visit').find('#client_complaints').val(data.success['complaints']);
												
						editor.value = data.success['inspection_results'];
						
						$('#new_visit').find('#clinic_comments').val(data.success['clinic_comments']);
						
						$('#new_visit').find('#animal_weight').val(data.weight);						

						$(".diagnosis").append(data.diagnosis);
						
						$(".researches").append(data.researches);
						
						$(".analisys").append(data.analisys);
						
						editor2.value = data.success['recomendation'];
						
						$('#new_visit').modal('show');
						
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
			
			$('#new_visit').find('#new_visit_title').html("Новый прием");
						
			// Временная метка для идентификации номера визита и связки с загружаемыми фото
			var currentdate = new Date();
			visit_time_id = currentdate.getFullYear() + "-" + (currentdate.getMonth()+1) + "-" + currentdate.getDate() + "-" + currentdate.getHours() + "-" + (currentdate.getMinutes()<10?'0':'') + currentdate.getMinutes() + "-" + (currentdate.getSeconds()<10?'0':'') + currentdate.getSeconds();
			
			// Инициализация календаря и установка даты в приеме
			var myDataPicker = $('#visit_date').datepicker({ 
				language : 'ru', 
				autoClose : true,
				clearButton: true,
			}).data('datepicker');
			
			myDataPicker.selectDate(new Date());
		
			// Установка врача (текущий пользователь)
			$('#doctor').val({{ Auth::user()->staff_id }}).trigger('change');
			
			$('#new_visit').modal('show');
						
		}
	
	}
	
	
	
	// Слушатель кнопки календарик
	function calendarinput3(){  	
		$("#visit_date").trigger("focus");
	}


	
	// Очистка шаблона-осмотра
	function clear_plate_text () {
		editor.value = '';
		$('#new_visit').find('#check_result_plate').val('0').trigger('change');
	}
	
	
	// Очистка шаблона-рекомендации
	function clear_recomendation_plate_text () {
		editor2.value = '';
		$('#new_visit').find('#recomendation_result_plate').val('0').trigger('change');
	}
	
	
	// Сохраняем прием 
    function save_visit(){ 

		// Очистка текстов-ошибок в span
		$('#new_visit').find('.error_response').html("&nbsp;");
		
		var form = $('#new_visit_form')[0];
		
		var formData = new FormData(form);
		
		if ({{ $patient_id }}) {
			formData.append("anymal_id", {{ $patient_id }});
		}
		
		if (visit_time_id) {
			formData.append("visit_date_id", visit_time_id);
		}
		
		formData.append("phototodelete", JSON.stringify(phototodelete)); 
		
		if ($('#doctor').val()) {
			formData.append("doctor_text", $('#doctor').select2('data')[0].text);
		}
	
		// Записываем данные
		$.ajax({
		url: '/patientcard/new_visit',
		type: 'POST',
		processData: false,
        contentType: false,
		data: formData,
			
		success: function(data) {
							
			if($.isEmptyObject(data.error)){
				
				toastr.info(data.success); 
				
				// Обновляем данные пациента-клиента на странице
				get_one_client_patient_data({{ $patient_id }});
				
				// Обновляем список визитов
				show_visits_list();
				
				// Обновляем данные визита и открываем
				open_visit(data.visitid);
				
				
				// Обновление списка приемов
				$(".tovisit").empty();
					
				// Добавление одного пустого поля для placeholder
				var $empty = $("<option selected='selected'></option>");
				$(".tovisit").append($empty).trigger('change');	
	
				// Установка списка - список визитов
				set_visits_list();
					
					
				$('#new_visit').modal('toggle');
			
			}else{

				if ((data.error['visit_date_id'] && data.error['visit_date_id'].length > 0) | (data.error['anymal_id'] && data.error['anymal_id'].length > 0)) {
					toastr.error('Ошибка сохранения');
				} else {
					printErrorMsg(data.error);
					toastr.error('Ошибки заполнения полей');
				}
				
			}
			},
		error: function(err) {	
			toastr.error('Ошибка');
			}
		});
				
	}
	
	
	function upload_photo(){ 
	
		$('#upload_photo_btn').addClass('disabled');	
		$('#upload_photo_btn').html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"> </span>&nbsp;&nbsp;Загрузка...');
	
		var formData = new FormData();
		
		var fileInput = document.getElementById("imagetoload");
		if (fileInput.files.length > 0) {
			formData.append("imagetoload", fileInput.files[0]);
		}
		
		if ({{ $patient_id }}) {
			formData.append("anymal_id", {{ $patient_id }});
		}
		
		if (visit_time_id) {
			if (neworchange == 0) {
				formData.append("visit_date_id", visit_time_id);
			} else if (neworchange == 1) {
				formData.append("visit_date_id", visit_time_id+'_change');
			}
		}
				
		formData.append("description", $('#description').val());		
				
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
						$('#upload_photo_btn').html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"> </span>&nbsp;&nbsp;Загружено ' + percentComplete + '%');
					}
				}, false);
			return xhr;
			},*/
				
			success: function(data) {
				
				$('#upload_photo_btn').text('Загрузить');
				$('#upload_photo_btn').removeClass('disabled');	
				
				if($.isEmptyObject(data.error)){
					
					// Очистка текстов-ошибок в span
					$('#new_visit').find('#error_imagetoload').html("");
					
					$('#imagetoload').val('');	
					$('#description').val('');
					
					show_photos_for_visit();
					
				}else{
					$('#new_visit').find('#error_imagetoload').html(data.error);
				}
				},
			error: function(err) {	
			
				$('#upload_photo_btn').text('Загрузить');
				$('#upload_photo_btn').removeClass('disabled');	
				
				toastr.error('Ошибка');
				}
			});			
	}
	
	
	// Показываем все фото
	 function show_photos_for_visit(){ 
	 
		$.ajax({
			url: '/patientcard/get_photo_for_visit',
			method:'GET',
			dataType:'json',
			data: {visit_date_id: visit_time_id, neworchange: neworchange, phototodelete: JSON.stringify(phototodelete)},
			success: function(data) {
				
				if($.isEmptyObject(data.error)){

					$('#uploaded_photo_response').empty().append(data.success);

				}else{
					toastr.error(data.error);
				}	
			},
				error: function(err) {	
					toastr.error('Ошибка');
				}
			});
	 }
	 
	 
	 function delete_photo(id) {
			
		phototodelete.push(id);
		
		show_photos_for_visit();

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

<style>

.checkbox-label{
    margin-left: 5px;
}

</style>

<div class="modal hide mycontainer" id="new_visit" name="new_visit" role="dialog" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false" style="display: none">
	<div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
		<div class="modal-content">
			<div class="modal-header">
				<h5 id="new_visit_title" name="new_visit_title" class="modal-title px-3" id="staticBackdropLabel"></h5>
				<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
			</div>
			<div class="modal-body" id="new_visit_body" name="new_visit_body">
				
				<div class="px-4 mb-2">
				
				<form id="new_visit_form" autocomplete="off" enctype="multipart/form-data">					
				@csrf
				
					<div class="row">
						<div class="col-lg-4 px-2 mb-2 mb-lg-0">
						
							<div class="form-group">
								<label class='control-label font-weight-bold' for="visit_date">Дата <span class="text-danger">*</span></label>		
								<div class="input-group input-group-lg">
									<input name="visit_date" id="visit_date" type="text" class="form-control form-control-lg datepicker-here appearance-none bg-white" data-position='bottom left' placeholder="Выберите дату" readonly />
									<button type="button" onclick="calendarinput3()" class="bg-white input-group-text" id="inputGroup-sizing-lg">
										<img src="{{ asset('images/calendar.png') }}" class="img-responsive"  width="30" height="30" />
									</button>
								</div>	
								<span name="error_visit_date" id='error_visit_date' class="error_response underinput-error text-danger d-flex justify-content-left align-items-lg-center">
								&nbsp;
								</span>

							</div>
						
						</div>
						
						<div class="col-lg-8 px-2 mb-2 mb-lg-0">		
							<div class="form-group">
								<label class='control-label font-weight-bold' for="doctor">Лечащий врач <span class="text-danger">*</span></label>
								
								<select name="doctor" id="doctor" class="form-select-lg js-example-basic-single block doctors" style="width: 100%;" placeholder="Не выбрано" data-search="true">
									<option></option>
								</select>
								<span name="error_doctor" id='error_doctor' class="error_response underinput-error text-danger d-flex justify-content-left align-items-lg-center">
								&nbsp;
								</span>
							</div>	
		
						</div>
															
					</div>	
					
					
					<div class="row">

						<div class="col-lg-6 px-2 mb-2 mb-lg-0">
							<div class="form-group">
							
								<label class='control-label font-weight-bold' for="porpose">Цель обращения <span class="text-danger"></span></label>		
								<select name="porpose" id="porpose" class="form-select-lg js-example-basic-single" style="width: 100%;" placeholder="Не выбрано" data-search="true">
									<option value="1" selected="selected">Консультация</option>
									<option value="2">Манипуляции</option>
									<option value="3">Манипуляции (для другой клиники)</option>
									<option value="4">Операция</option>
									<option value="5">Стационар</option>
									<option value="6">Гигиенические процедуры</option>
								</select>
								<span name="error_porpose" id='error_porpose' class="error_response underinput-error text-danger d-flex justify-content-left align-items-lg-center">
								&nbsp;
								</span>	

							</div>							
						</div>

						<div class="col-lg-6 px-2 mb-2 mb-lg-0">
							<div class="form-group">
							
								<label class='control-label font-weight-bold' for="visit_type">Тип обращения <span class="text-danger"></span></label>		
								<select name="visit_type" id="visit_type" class="form-select-lg js-example-basic-hide" style="width: 100%;" placeholder="Не выбрано" data-search="true">
									<option value="1" selected="selected">Первичное</option>
									<option value="2">Повторное</option>
								</select>
								<span name="error_visit_type" id='error_visit_type' class="error_response underinput-error text-danger d-flex justify-content-left align-items-lg-center">
								&nbsp;
								</span>	

							</div>							
						</div>	
															
					</div>
					
					<div class="row">
				
						<div class="col-lg px-2 mb-1">
							<div class="form-group">			
								<label class='control-label font-weight-bold' for="client_complaints">Жалобы (со слов пациента) <span class="text-danger"></span></label>
								<textarea name="client_complaints" class="form-control" id='client_complaints' rows="3"></textarea>
								<span name="error_client_complaints" id='error_client_complaints' class="error_response underinput-error text-danger d-flex justify-content-left align-items-lg-center">
								&nbsp;
								</span>
							</div>							
						</div>
							
					</div>
					
					<div class="row">
				
						<div class="col-lg px-2 mb-4">
							<div class="form-group">			
								<label class='control-label font-weight-bold' for="check_result">Результат осмотра <span class="text-danger"></span></label>
								
								<div class="d-lg-flex justify-content-left align-items-lg-center">					
									
									<div class="col-lg-6 mb-2">	
										<select name="check_result_plate" id="check_result_plate" class="form-select-lg js-example-basic-single" style="width: 100%;" placeholder="Не выбрано" data-search="true">
											<option></option>
										</select>
									</div>		
									
									<div class="d-flex mb-2 col-lg justify-content-left align-items-lg-center">
																			
										<div name="clear_plate_text" id="clear_plate_text"  class="d-flex align-items-center px-lg-3" align="center"><button onclick="clear_plate_text()" type="button" class="btn btn-secondary">Очистить</button></div>
										
									</div>
									
								</div>
															
								<textarea name="check_result" class="form-control" id='check_result' rows="10"></textarea>
								
							</div>							
						</div>
							
					</div>
										
					<div class="row">
				
						<div class="col-lg px-2 mb-4">
							<div class="form-group">			
								<label class='control-label font-weight-bold' for="recomendation">Лечение и рекомендации <span class="text-danger"></span></label>
								
								<div class="d-lg-flex justify-content-left align-items-lg-center">					
									
									<div class="col-lg-6 mb-2">	
										<select name="recomendation_plate" id="recomendation_plate" class="form-select-lg js-example-basic-single" style="width: 100%;" placeholder="Не выбрано" data-search="true">
											<option></option>
										</select>
									</div>		
									
									<div class="d-flex mb-2 col-lg justify-content-left align-items-lg-center">
																			
										<div name="clear_recomendation_plate_text" id="clear_recomendation_plate_text"  class="d-flex align-items-center px-lg-3" align="center"><button onclick="clear_recomendation_plate_text()" type="button" class="btn btn-secondary">Очистить</button></div>
										
									</div>
									
								</div>
															
								<textarea name="recomendation" class="form-control" id='recomendation' rows="10"></textarea>
								
							</div>							
						</div>
							
					</div>
					
					
					<div class="row">
				
						<div class="col-lg px-2 mb-1">
							<div class="form-group">			
								<label class='control-label font-weight-bold' for="clinic_comments">Комментарии клиники <span class="text-danger"></span></label>
								<textarea name="clinic_comments" class="form-control" id='clinic_comments' rows="3"></textarea>
								<span name="error_clinic_comments" id='error_clinic_comments' class="error_response underinput-error text-danger d-flex justify-content-left align-items-lg-center">
								&nbsp;
								</span>
							</div>							
						</div>
							
					</div>
					
					<div class="row">
						<div class="col-lg-4 px-2 mb-4">
							<div class="form-group">
								<label class='control-label font-weight-bold' for="animal_weight">Вес животного (кг.) <span class="text-danger"></span></label>
								<input name="animal_weight" class='form-control form-control-lg' id='animal_weight' placeholder='Вес' oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*?)\..*/g, '$1');" />
							</div>					
						</div>
									
					</div>
					

					<div class="row">				
						<div class="col-lg px-2">						

							<div class="form-group">
								<label class='control-label font-weight-bold'>Фото <span class="text-danger"></span></label>
							</div>	

						</div>			
					</div>
					
					<div class="row">	
						<span name="error_imagetoload" id='error_imagetoload' class="error_imagetoload underinput-error text-danger d-flex justify-content-left align-items-lg-center">
						</span>
					</div>
					
					<div class='row mb-2'> 
									
						<div class='col-lg-5 mb-2 px-2'> 
							<input type='file' name='imagetoload' id='imagetoload' class='form-control'> 
						</div> 
						<div class='mb-2 px-2 col-lg-4 justify-content-left align-items-lg-center'> 
							<input name='description' id='description' class='form-control' placeholder='Описание'/> 
						</div> 
						<div class='mb-2 px-2 col-lg-3 justify-content-left align-items-lg-center'> 
							<button name='upload_photo_btn' id='upload_photo_btn' class="btn btn-success" type="button" onclick="upload_photo()">
								Загрузить
							</button>
						</div>
						
					</div>
					
					<div name="uploaded_photo_response" id="uploaded_photo_response" class='row w-full items-center mb-2 d-flex justify-content-center'>
							
					</div>
					
					
					<div class='row mb-2'> 
					
						<div class='d-lg-flex px-0'> 
														
							<div class="col-lg-6 px-2 mb-2">
								<div class="form-group">
								
									<label class='control-label font-weight-bold' for="diagnosis_list">Диагноз <span class="text-danger"></span></label>		
									<select name="diagnosis_list" id="diagnosis_list" class="form-select-lg js-example-basic-single block diagnosis_list" style="width: 100%;" placeholder="Не выбрано" data-search="true">
										<option></option>
									</select>
								</div>							
							</div>	
						
							<div class='justify-content-left align-self-end align-items-lg-end flex-column px-2 mb-2'>
							
								<div class="form-check align-self-end align-items-lg-center">
									<input class="form-check-input align-self" type="checkbox" value="" id="need_aprove_checkbox">
									<label class="form-check-label checkbox-label align-self" for="need_aprove_checkbox">
										Требует уточнения
									</label>
									
									<input name="need_aprove" id='need_aprove' value="0" hidden="true"/>
									
								</div>	
											
								<div class="form-check align-self-end align-items-lg-center">
									<input class="form-check-input align-self" type="checkbox" value="" id="permanent_checkbox">
									<label class="form-check-label checkbox-label align-self" for="permanent_checkbox">
										Хронический
									</label>
									
									<input name="permanent" id='permanent' value="0" hidden="true"/>
									
								</div>

							</div>

							<div class='mb-3 px-2 justify-content-left align-items-lg-end align-self-end d-flex px-2 mb-2'> 
								<button name='add_diagnosis_btn' id='add_diagnosis_btn' class="btn btn-primary align-self-end btn-add-diagnosis" type="button">
									Добавить
								</button>
							</div>							

						</div>
					
					</div>
					
					<div class='diagnosis mb-3'> 
					
					</div>
					
					
					<div class='row mb-2'> 
					
						<div class="col-lg px-2">						

							<div class="form-group">
								<label class='control-label font-weight-bold' for="research_list">Назначены исследования <span class="text-danger"></span></label>	
							</div>	

						</div>
						
						<div class='d-lg-flex px-0'> 
														
							<div class="col-lg-6 px-2 mb-2">
								<div class="form-group">
									<select name="research_list" id="research_list" class="form-select-lg js-example-basic-single block researches_list" style="width: 100%;" placeholder="Не выбрано" data-search="true">
										<option></option>									
									</select>
								</div>							
							</div>	

							<div class='px-2 justify-content-left align-items-lg-center align-self-center d-flex px-2 mb-2'> 
								<button name='add_research_btn' id='add_research_btn' class="btn btn-primary align-self-center btn-add-research" type="button">
									Добавить
								</button>
							</div>							

						</div>
					
					</div>
					
					<div class='researches mb-3'> 
					
					</div>
					
					
					<div class='row mb-2'> 
					
						<div class="col-lg px-2">						

							<div class="form-group">
								<label class='control-label font-weight-bold' for="analis_list">Назначены анализы <span class="text-danger"></span></label>	
							</div>	

						</div>
						
						<div class='d-lg-flex px-0'> 
														
							<div class="col-lg-6 px-2 mb-2">
								<div class="form-group">
									<select name="analis_list" id="analis_list" class="form-select-lg js-example-basic-single block analysis_list" style="width: 100%;" placeholder="Не выбрано" data-search="true">
										<option></option>
									</select>
								</div>							
							</div>	

							<div class='px-2 justify-content-left align-items-lg-center align-self-center d-flex px-2 mb-2'> 
								<button name='add_analis_btn' id='add_analis_btn' class="btn btn-primary align-self-center btn-add-analis" type="button">
									Добавить
								</button>
							</div>							

						</div>
					
					</div>
					
					<div class='analisys mb-3'> 
					
					</div>

				</form>
				
				<script>
				
					$('#need_aprove_checkbox').on('change', function(){
						if (this.checked) {
							$('#need_aprove').val('1');
						} else {
							$('#need_aprove').val('0');
						}
					});
					
					$('#permanent_checkbox').on('change', function(){
						if (this.checked) {
							$('#permanent').val('1');
						} else {
							$('#permanent').val('0');
						}
					});

					
					// Очистить один диагноз
					$("body").on("click",".remove-diagnosis",function(){ 
						$(this).parents(".diagnosis-group").remove();
					});


					// Добавить один диагноз
					$(".btn-add-diagnosis").click(function(){ 
					 
						var data = $('#diagnosis_list').select2('data');
						
						if (data[0].text != 0) {
						
							var firstpart = "<div class='row diagnosis-group'> <div class='d-flex px-0'> <div class='justify-content-left align-self-enter align-items-center d-flex px-2 mb-2 fs-6 fw-bolder text-break'>"+data[0].text+"</div>";
							
							var lastpart = "<div class='mb-2 px-2 justify-content-left align-items-center align-self-center d-flex px-2 mb-2'> <a class='nav-link p-1 remove-diagnosis' role='button'>	<img class='img-responsive' width='25' height='25' src='images/delete_photo.png'> </a> </div> <input name='diagnosis_todb[]' id='diagnosis_todb[]' value='"+data[0].id+"' hidden='true'/> <input name='need_aprove_todb[]' id='need_aprove_todb[]' value='"+$('#need_aprove').val()+"' hidden='true'/> <input name='permanent_todb[]' id='permanent_todb[]' value='"+$('#permanent').val()+"' hidden='true'/> </div> </div>";
							
							var middlepart = "";
							
							if ($('#need_aprove').val() != '0' | $('#permanent').val() != '0') {
							
								var middlepart1 = "<div class='justify-content-left align-self-center align-items-center flex-column px-2 mb-2'>";
								
								var middlepart2 = "";
								
								if ($('#need_aprove').val() != '0') {
									middlepart2 = "<div class='justify-content-left align-self-center align-items-center d-flex fst-italic fs-6 text-break'>Требует уточнения</div>";
								}
								
								var middlepart3 = "";
								
								if ($('#permanent').val() != '0') {
									middlepart3 = "<div class='justify-content-left align-self-center align-items-center d-flex fst-italic fs-6 text-break'>Хронический</div>";
								}
								
								var middlepart4 = "</div>";
														
								middlepart = middlepart1+middlepart2+middlepart3+middlepart4;
							
							}
							
							var html = firstpart+middlepart+lastpart;
							
							$(".diagnosis").append(html);
													
							// Очистка списка
							$('#new_visit').find('#diagnosis_list').val('').trigger("change");
							
							$('#need_aprove_checkbox').prop('checked', false);
							$('#need_aprove').val('0');
							
							$('#permanent_checkbox').prop('checked', false);
							$('#permanent').val('0');
						
						}						

					});
					
					
					
					// Добавить одно исследование
					$(".btn-add-research").click(function(){

						var data = $('#research_list').select2('data');
						
						if (data[0].text != 0) {
							var html = "<div class='row researches-group'> <div class='d-flex px-0'> <div class='justify-content-left align-self-enter align-items-center d-flex px-2 mb-2 fs-6 fw-bolder text-break'>"+data[0].text+"</div> <div class='mb-2 px-2 justify-content-left align-items-center align-self-center d-flex px-2 mb-2'> <a class='nav-link p-1 remove-researches' role='button'>	<img class='img-responsive' width='25' height='25' src='images/delete_photo.png'> </a> </div> <input name='researches_todb[]' id='researches_todb[]' value='"+data[0].text+"' hidden='true'/> </div> </div>";
							
							$(".researches").append(html);
													
							// Очистка списка
							$('#new_visit').find('#research_list').val('').trigger("change");
						}
						
											
					});
					
					
					// Очистить одно исследование
					$("body").on("click",".remove-researches",function(){ 
						$(this).parents(".researches-group").remove();
					});
					
					
					// Добавить один анализ
					$(".btn-add-analis").click(function(){

						var data = $('#analis_list').select2('data');
						
						if (data[0].text != 0) {
							var html = "<div class='row analisys-group'> <div class='d-flex px-0'> <div class='justify-content-left align-self-enter align-items-center d-flex px-2 mb-2 fs-6 fw-bolder text-break'>"+data[0].text+"</div> <div class='mb-2 px-2 justify-content-left align-items-center align-self-center d-flex px-2 mb-2'> <a class='nav-link p-1 remove-analisys' role='button'> <img class='img-responsive' width='25' height='25' src='images/delete_photo.png'> </a> </div> <input name='analisys_todb[]' id='analisys_todb[]' value='"+data[0].text+"' hidden='true'/> </div> </div>";
							
							$(".analisys").append(html);
													
							// Очистка списка
							$('#new_visit').find('#analis_list').val('').trigger("change");
						}
						
											
					});
					
					
					// Очистить один анализ
					$("body").on("click",".remove-analisys",function(){ 
						$(this).parents(".analisys-group").remove();
					});
				
				
					// Закрытие всех открытых popup при начале скрола модального окна
					$('.modal-body').scroll(function(){

						// datepicker
						$('#visit_date').datepicker("hide");
						$('#visit_date').blur();
						
						// jodit editor
						$('.jodit-popup__content').hide();
						$('.jodit-popup__content').blur();
					});


					var editor = new Jodit("#check_result", {
					  "useSearch": false,
					  "spellcheck": false,
					  "language": "ru",
					  "toolbarSticky": false,
					  "showCharsCounter": false,
					  "showWordsCounter": false,
					  "showXPathInStatusbar": false,
					  "height": null,
					  "minHeight": 400,
					  "maxHeight": 400,
					  "minWidth": null,
					  "enableDragAndDropFileToEditor":false,
					  "buttons": "source,bold,italic,underline,strikethrough,eraser,superscript,subscript,ul,ol,indent,outdent,left,font,fontsize,paragraph,brush,image,copyformat,selectall,hr,table,undo,redo,print",
					  "buttonsMD": "source,bold,italic,underline,strikethrough,eraser,superscript,subscript,ul,ol,indent,outdent,left,font,fontsize,paragraph,brush,image,copyformat,selectall,hr,table,undo,redo,print",
					  "buttonsSM": "source,bold,italic,underline,strikethrough,eraser,superscript,subscript,ul,ol,indent,outdent,left,font,fontsize,paragraph,brush,image,copyformat,selectall,hr,table,undo,redo,print",
					  "buttonsXS": "source,bold,italic,underline,strikethrough,eraser,superscript,subscript,ul,ol,indent,outdent,left,font,fontsize,paragraph,brush,image,copyformat,selectall,hr,table,undo,redo,print"
					});	


					var editor2 = new Jodit("#recomendation", {
					  "useSearch": false,
					  "spellcheck": false,
					  "language": "ru",
					  "toolbarSticky": false,
					  "showCharsCounter": false,
					  "showWordsCounter": false,
					  "showXPathInStatusbar": false,
					  "height": null,
					  "minHeight": 400,
					  "maxHeight": 400,
					  "minWidth": null,
					  "enableDragAndDropFileToEditor":false,
					  "buttons": "source,bold,italic,underline,strikethrough,eraser,superscript,subscript,ul,ol,indent,outdent,left,font,fontsize,paragraph,brush,image,copyformat,selectall,hr,table,undo,redo,print",
					  "buttonsMD": "source,bold,italic,underline,strikethrough,eraser,superscript,subscript,ul,ol,indent,outdent,left,font,fontsize,paragraph,brush,image,copyformat,selectall,hr,table,undo,redo,print",
					  "buttonsSM": "source,bold,italic,underline,strikethrough,eraser,superscript,subscript,ul,ol,indent,outdent,left,font,fontsize,paragraph,brush,image,copyformat,selectall,hr,table,undo,redo,print",
					  "buttonsXS": "source,bold,italic,underline,strikethrough,eraser,superscript,subscript,ul,ol,indent,outdent,left,font,fontsize,paragraph,brush,image,copyformat,selectall,hr,table,undo,redo,print"
					});	
				
				
					// Слушатель выбора цели обращения
					$('#porpose').on("select2:select", function(e) { 
						if ($('#porpose').val() != null) {
							
							//toastr.error($('#porpose').val());
	
						} else {
									
						}
					});
					
					
					// Слушатель выбора шаблона "Результат осмотра"
					$('#check_result_plate').on("select2:select", function(e) { 
											
						$.ajax({
							url: '/guides/gettemplatedata',
							method:'GET',
							dataType:'json',
							data: {query:$('#check_result_plate').val()},
							success: function(data) {
								
								if($.isEmptyObject(data.error)){
									
									if ($('#check_result').val()) {
										var text = $('#new_visit').find('#check_result').val() + String.fromCharCode(13, 10) + data.text;
									} else {
										var text = data.text;
									}
									
									editor.value = text;
									
									setTimeout(function () {
										$('#new_visit').find('#check_result_plate').val(0).trigger('change');
									}, 100);
											
											
								}else{
									toastr.error(data.error);
								}
								
							},
							error: function(err) {	
								//toastr.error(err);
							}
						});
					});
					
					
					// Слушатель выбора шаблона "Рекомендации"
					$('#recomendation_plate').on("select2:select", function(e) { 
											
						$.ajax({
							url: '/guides/gettemplatedata',
							method:'GET',
							dataType:'json',
							data: {query:$('#recomendation_plate').val()},
							success: function(data) {
								
								if($.isEmptyObject(data.error)){
									
									if ($('#recomendation').val()) {
										var text = $('#new_visit').find('#recomendation').val() + String.fromCharCode(13, 10) + data.text;
									} else {
										var text = data.text;
									}
									
									editor2.value = text;
									
									setTimeout(function () {
										$('#new_visit').find('#recomendation_plate').val(0).trigger('change');	
									}, 100);
														
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
				<button type="button" class="btn btn-primary" onclick="save_visit()">Сохранить</button>		
			</div>
		</div>
	</div>
</div>
