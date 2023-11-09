<script> 

	var research_id = '';
	
	var neworchange = '';
	
	var researchopened = '';
	
	// Открытие диалога
	function new_research(id) {
		
		// Очистка текстов-ошибок в span
		$('#new_research').find('.error_response').html("&nbsp;");
		
		$('#new_research').find('#new_research_form')[0].reset();
		$('#research_date').datepicker().data('datepicker').clear();
		$('#new_research').find('#research_doctor').val(0).trigger('change');
		$('#new_research').find('#to_visit_new_research').val(0).trigger('change');
		$('#new_research').find('#research_name').val('').trigger('change');
		$('#new_research').find('#research_plate').val(0).trigger('change');
		
		// Перезагрузка редакторов
		editor3.destruct();
		editor3 = new Jodit("#research_plate_text", {
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
		
		editor3.value = '';
		
	
		if (id) {
			
			neworchange = 1;
			
			research_id = id;
			
			$('#new_research').find('#new_research_title').html("Изменить исследование");
						
			$.ajax({
				url: '/patientcard/get_research_data',
				method:'GET',
				dataType:'json',
				data: {research_id: id},
				success: function(data) {
										
					if($.isEmptyObject(data.error)){

						// Инициализация календаря
						var myDataPicker_2 = $('#research_date').datepicker({ 
							language : 'ru', 
							autoClose : true,
							clearButton: true,
						}).data('datepicker');
						
						// Установка даты
						var dateParts = data.success['date_of_research'].split("-");
						var jsDate = new Date(dateParts[0], dateParts[1] - 1, dateParts[2].substr(0,2));
						
						myDataPicker_2.selectDate(jsDate);
						
						// Установка врача (текущий пользователь)
						if (data.doctorid == null) {
							toastr.error('Врач не найден, возможно он был удален');
						} else {
							$('#research_doctor').val(data.doctorid).trigger('change');
						}
						
						if (data.success['visit_id'] != null && data.success['visit_id'] != 0) {
							$('#to_visit_new_research').val(data.success['visit_id']).trigger('change');
						}
						
						$('#new_research').find('#research_name').val(data.success['research_name']);
						
						editor3.value = data.success['research_text'];
						
						$('#new_research').modal('show');
						
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
			
			research_id = '';
			
			$('#new_research').find('#new_research_title').html("Новое исследование");
								
			// Инициализация календаря и установка даты в приеме
			var myDataPicker_2 = $('#research_date').datepicker({ 
				language : 'ru', 
				autoClose : true,
				clearButton: true,
			}).data('datepicker');
			
			myDataPicker_2.selectDate(new Date());
		
			// Установка врача (текущий пользователь)
			$('#research_doctor').val({{ Auth::user()->staff_id }}).trigger('change');
			
			$('#new_research').modal('show');
						
		}
	
	}
	
	
	
	// Слушатель кнопки календарик
	function calendarinput5(){  	
		$("#research_date").trigger("focus");
	}


	
	// Очистка шаблона-осмотра
	function clear_research_plate_text () {
		
		editor3.value = '';
		
		$('#new_research').find('#research_plate').val('0').trigger('change');
		
		$('#new_research').find('#research_name').val('').trigger('change');
		
	}
	
	
	
	// Сохраняем исследование
    function save_research(){ 

		// Очистка текстов-ошибок в span
		$('#new_research').find('.error_response').html("&nbsp;");
		
		var form = $('#new_research_form')[0];
		
		var formData = new FormData(form);
		
		if ({{ $patient_id }}) {
			formData.append("anymal_id", {{ $patient_id }});
		}
		
		formData.append("research_id", research_id);
		
		if ($('#research_doctor').val()) {
			formData.append("doctor_text", $('#research_doctor').select2('data')[0].text);
		}
	
		// Записываем данные
		$.ajax({
		url: '/patientcard/new_research',
		type: 'POST',
		processData: false,
        contentType: false,
		data: formData,
			
		success: function(data) {
							
			if($.isEmptyObject(data.error)){
				
				toastr.info(data.success); 
				
				// Обновляем список исследований
				show_researches_list();
				
				// Обновляем данные исследования и открываем
				open_research(data.researchid);
				
				$('#new_research').modal('toggle');
			
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

<div class="modal hide mycontainer" id="new_research" name="new_research" role="dialog" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false" style="display: none">
	<div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
		<div class="modal-content">
			<div class="modal-header">
				<h5 id="new_research_title" name="new_research_title" class="modal-title px-3" id="staticBackdropLabel"></h5>
				<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
			</div>
			<div class="modal-body" id="new_research_body" name="new_research_body">
				
				<div class="px-4 mb-2">
				
				<form id="new_research_form" autocomplete="off" enctype="multipart/form-data">					
				@csrf
				
					<div class="row">
						<div class="col-lg-4 px-2 mb-2 mb-lg-0">
						
							<div class="form-group">
								<label class='control-label font-weight-bold' for="research_date">Дата <span class="text-danger">*</span></label>		
								<div class="input-group input-group-lg">
									<input name="research_date" id="research_date" type="text" class="form-control form-control-lg datepicker-here appearance-none bg-white" data-position='bottom left' placeholder="Выберите дату" readonly />
									<button type="button" onclick="calendarinput5()" class="bg-white input-group-text" id="inputGroup-sizing-lg">
										<img src="{{ asset('images/calendar.png') }}" class="img-responsive"  width="30" height="30" />
									</button>
								</div>	
								<span name="error_research_date" id='error_research_date' class="error_response underinput-error text-danger d-flex justify-content-left align-items-lg-center">
								&nbsp;
								</span>

							</div>
						
						</div>
						
						<div class="col-lg-8 px-2 mb-2 mb-lg-0">		
							<div class="form-group">
								<label class='control-label font-weight-bold' for="research_doctor">Лечащий врач <span class="text-danger">*</span></label>
								
								<select name="research_doctor" id="research_doctor" class="form-select-lg js-example-basic-single block doctors" style="width: 100%;" placeholder="Не выбрано" data-search="true">
									<option></option>
								</select>
								<span name="error_research_doctor" id='error_research_doctor' class="error_response underinput-error text-danger d-flex justify-content-left align-items-lg-center">
								&nbsp;
								</span>
							</div>	
		
						</div>
															
					</div>	
					
					<div class="row">
						<div class="col-lg-4 px-2 mb-2 mb-lg-0">
						
							<div class="form-group">
							
							
								<label class='control-label font-weight-bold' for="to_visit_new_research">К визиту <span class="text-danger"></span></label>
								
								<select name="to_visit_new_research" id="to_visit_new_research" class="form-select-lg js-example-basic-single block tovisit" style="width: 100%;" placeholder="Не выбрано" data-search="true">
									<option></option>
								</select>
								<span name="error_to_visit_new_research" id='error_to_visit_new_research' class="error_response underinput-error text-danger d-flex justify-content-left align-items-lg-center">
								&nbsp;
								</span>

							</div>
						
						</div>
						
						<div class="col-lg-8 px-2 mb-2 mb-lg-0">		
							<div class="form-group">
								<label class='control-label font-weight-bold' for="research_name">Наименование <span class="text-danger">*</span></label>
								<input name="research_name" class="form-control form-control-lg" id='research_name' placeholder="Наименование">
								<span name="error_research_name" id='error_research_name' class="error_response underinput-error text-danger d-flex justify-content-left align-items-lg-center">
								&nbsp;
								</span>
							</div>	
		
						</div>
															
					</div>	
					
					<div class="row">
				
						<div class="col-lg px-2 mb-2">
							<div class="form-group">			
								<label class='control-label font-weight-bold' for="check_result">Шаблон исследования <span class="text-danger"></span></label>
								
								<div class="d-lg-flex justify-content-left align-items-lg-center">					
									
									<div class="col-lg-6 mb-2">	
										<select name="research_plate" id="research_plate" class="form-select-lg js-example-basic-single researches_list" style="width: 100%;" placeholder="Не выбрано" data-search="true">
											<option></option>
										</select>
									</div>		
									
									<div class="d-flex mb-2 col-lg justify-content-left align-items-lg-center">
																			
										<div name="clear_research_plate_text" id="clear_research_plate_text"  class="d-flex align-items-center px-lg-3" align="center"><button onclick="clear_research_plate_text()" type="button" class="btn btn-secondary">Очистить</button></div>
										
									</div>
									
								</div>
															
								<textarea name="research_plate_text" class="form-control" id='research_plate_text' rows="10"></textarea>
								
							</div>							
						</div>
							
					</div>

				</form>

				<script>
				
					// Закрытие всех открытых popup при начале скрола модального окна
					$('.modal-body').scroll(function(){

						// datepicker
						$('#research_date').datepicker("hide");
						$('#research_date').blur();
						
						// jodit editor
						$('.jodit-popup__content').hide();
						$('.jodit-popup__content').blur();
					});


					var editor3 = new Jodit("#research_plate_text", {
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
				
					
					
					// Слушатель выбора шаблона "Исследование"
					$('#research_plate').on("select2:select", function(e) { 
											
						$.ajax({
							url: '/guides/gettemplatedata',
							method:'GET',
							dataType:'json',
							data: {query:$('#research_plate').val()},
							success: function(data) {
								
								if($.isEmptyObject(data.error)){
									
									if ($('#research_plate_text').val()) {
										var text = $('#new_research').find('#research_plate_text').val() + String.fromCharCode(13, 10) + data.text;
									} else {
										var text = data.text;
									}
									
									editor3.value = text;
									
									setTimeout(function () {
																				
										var researchtitle = $('#new_research').find('#research_plate').select2('data');
										
										$('#new_research').find('#research_name').val(researchtitle[0].text).trigger('change');

										$('#new_research').find('#research_plate').val(0).trigger('change');
										
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
				<button type="button" class="btn btn-primary" onclick="save_research()">Сохранить</button>		
			</div>
		</div>
	</div>
</div>




