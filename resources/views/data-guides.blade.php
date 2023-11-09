@extends('layout')
@section('title', 'Справочники')
@section('content')


<!-- Scripts -->
<script src="{{ asset('js/app.js') }}" defer></script>
<meta name="csrf-token" content="{{ csrf_token() }}">


<script> 

	var template_shown_tab = 1;


	$(document).ready(function () {
		
		var staff_id = {{ Auth::user()->staff_id }};
		
		if (staff_id == 0) {
			
			$('#developer-container').removeClass('d-none');
			$('#developer-btn').removeClass('d-none');				

			$.ajax({
			url: '/guides/getexpdate',
			method:'GET',
			dataType:'json',
			success: function(data) {
				
				if($.isEmptyObject(data.error)){

					// Установка даты
					if (data.success) {
						var dateParts = data.success.split(".");
						var jsDate = new Date(dateParts[2], dateParts[1] - 1, dateParts[0]);
						$('#exp_date').datepicker().data('datepicker').selectDate(jsDate);
					}
										
				}
				
			},
				error: function(err) {	
				}
			});
			
		} 
		
				
		// Вкладка шаблоны открыта
		$('#tab-2').click(function(){
			
			// Установка списка шаблонов
			settemplates ();

		});
		
		
		// Вкладка анализы открыта
		$('#tab-4').click(function(){
			
			setanalisystemplates ();
			
		});
		
		
		$('.radio_analisys').change(function(){
			// Радио-кнопка нажата
        });
		
		
		// Вкладка осмотры открыта
		$('#check-tab').click(function(){
			template_shown_tab = 1;
		});
		
		// Вкладка исследования открыта
		$('#research-tab').click(function(){
			template_shown_tab = 2;
		});
		
		// Вкладка рекомендации открыта
		$('#advice-tab').click(function(){
			template_shown_tab = 3;
		});
		
	});	
	
	
	
	function importAnimaltypes () {

		$.ajax({
			url: '/guides/importanimaltypes',
			method:'GET',
			dataType:'json',
			//data: {query:query},
			success: function(data) {
				toastr.info(data.success);
			},
				error: function(err) {	
					toastr.error('Ошибка');
				}
			});
			
	}
	
	
	function addAnimaltype () {
		
		// Очистка текстов-ошибок в span
		$('#error_type_title').html("&nbsp;");
		
		type = $('#type_title').val();

		var token;
		token='{{ csrf_token() }}';
				
		$.ajax({
			headers: {'X-CSRF-TOKEN': token},
			url: '/guides/addanimaltype',
			method:'POST',
			dataType: 'json',
			data: {type_title:type, id:'0'},
			success: function(data) {
				
				if($.isEmptyObject(data.error)){
					toastr.info(data.success); 
					$('#type_title').val('');
					
					// Очистка списка животных
					$('#animal_type_id').empty();
					
					// Добавление одного пустого поля для placeholder
					var $empty = $("<option selected='selected'></option>");
					$('#animal_type_id').append($empty).trigger('change');	
		
					// Заполнение данных - тип животного				
					$.ajax({
						url: '/guides/animaltype_search',
						method:'GET',
						dataType:'json',
						success: function(data) {
							$('#animal_type_id').select2({ 
								placeholder: "Выберите значение",
								theme: "bootstrap-5",
								data: data });
							},
							error: function(err) {	
								//toastr.error('Ошибка загрузки типов животных');
							}
					});
		
				}else{
					printErrorMsg(data.error);
				}
			},
				error: function(err) {	
					toastr.error('Ошибка');
				}
			});	
	}
	
	
	
	function importAnimalbreeds () {

		$.ajax({
			url: '/guides/importanimalbreeds',
			method:'GET',
			dataType:'json',
			//data: {query:query},
			success: function(data) {
				toastr.info(data.success);
			},
				error: function(err) {	
					toastr.error('Ошибка');
				}
			});
			
	}
	
		
		
	function addAnimalbreed () {
		
		// Очистка текстов-ошибок в span
		$('#error_breed_title').html("&nbsp;");
		$('#error_animal_type_id').html("&nbsp;");
				
		breed = $('#breed_title').val();
		animal = $('#animal_type_id').val();

		var token;
		token='{{ csrf_token() }}';
		
		$.ajax({
			headers: {'X-CSRF-TOKEN': token},
			url: '/guides/addanimalbreed',
			method:'POST',
			dataType: 'json',
			data: {breed_title:breed, animal_type_id:animal,id:'0'},
			success: function(data) {
				
				if($.isEmptyObject(data.error)){
					toastr.info(data.success); 
					$('#breed_title').val('');
					$('#animal_type_id').val('').trigger('change');
				}else{
					printErrorMsg(data.error);
				}
			},
				error: function(err) {	
					toastr.error('Ошибка');
				}
			});	
	}
	
	
	
	function importAnimalcolors () {

		$.ajax({
			url: '/guides/importanimalcolors',
			method:'GET',
			dataType:'json',
			//data: {query:query},
			success: function(data) {
				toastr.info(data.success);
			},
				error: function(err) {	
					toastr.error('Ошибка');
				}
			});
			
	}
	
	
	
	function addAnimalcolor () {
		
		// Очистка текстов-ошибок в span
		$('#error_color_title').html("&nbsp;");
		
		color = $('#color_title').val();

		var token;
		token='{{ csrf_token() }}';
		
		$.ajax({
			headers: {'X-CSRF-TOKEN': token},
			url: '/guides/addanimalcolor',
			method:'POST',
			dataType: 'json',
			data: {color_title:color, id:'0'},
			success: function(data) {
				
				if($.isEmptyObject(data.error)){
					toastr.info(data.success); 
					$('#color_title').val('');
				}else{
					printErrorMsg(data.error);
				}
			},
				error: function(err) {	
					toastr.error('Ошибка');
				}
			});	
	}
	

	
	function importPatientsandClients () {

		$.ajax({
			url: '/guides/importpatientsandclients',
			method:'GET',
			dataType:'json',
			//data: {query:query},
			success: function(data) {		
				toastr.info(data.success);			
				//console.log(JSON.stringify(data.success))
			},
				error: function(err) {	
					toastr.error('Ошибка');
				}
			});
			
	}
	
	
	function importDiagnosis () {

		$.ajax({
			url: '/guides/importdiagnosis',
			method:'GET',
			dataType:'json',
			//data: {query:query},
			success: function(data) {
				toastr.info(data.success);
			},
				error: function(err) {	
					toastr.error('Ошибка');
				}
			});
			
	}
	
	
	function addDiagnosis () {
		
		// Очистка текстов-ошибок в span
		$('#error_diagnosis_title').html("&nbsp;");
		
		var diagnosis = $('#diagnosis_title').val();

		var token;
		token='{{ csrf_token() }}';
		
		$.ajax({
			headers: {'X-CSRF-TOKEN': token},
			url: '/guides/adddiagnosis',
			method:'POST',
			dataType: 'json',
			data: {diagnosis_title:diagnosis},
			success: function(data) {
				
				if($.isEmptyObject(data.error)){
					toastr.info(data.success); 
					$('#diagnosis_title').val('');
				}else{
					printErrorMsg(data.error);
				}
			},
				error: function(err) {	
					toastr.error('Ошибка');
				}
			});	
	}
	
	
	
	function changeDiagnosis () {
		
		// Очистка текстов-ошибок в span
		$('#error_change_diagnosis_list').html("&nbsp;");
		$('#error_change_diagnosis_title').html("&nbsp;");
				
		var diagnosis = $('#change_diagnosis_title').val();
		var id = $('#diagnosis_list').val();
		
		var token;
		token='{{ csrf_token() }}';
		
		$.ajax({
			headers: {'X-CSRF-TOKEN': token},
			url: '/guides/changediagnosis',
			method:'POST',
			dataType: 'json',
			data: {diagnosis_title:diagnosis, diagnosis_list:id},
			success: function(data) {
				
				if($.isEmptyObject(data.error)){
					toastr.info(data.success); 
					
					// Очистка списка
					$('#diagnosis_list').empty();
					
					// Добавление одного пустого поля для placeholder
					var $empty = $("<option selected='selected'></option>");
					$('#diagnosis_list').append($empty).trigger('change');

					$('#change_diagnosis_title').val('').trigger('change');
					
				}else{
					
					// Заполнение span текстами ошибок 
					$.each(data.error,function(field_name,error){						
						$('#error_change_'+field_name).html(error);		
					})

				}
			},
				error: function(err) {	
					toastr.error('Ошибка');
				}
			});	
	}
	
	
	function addVacine () {
		
		// Очистка текстов-ошибок в span
		$('#error_vacine_title').html("&nbsp;");
		
		var vacine = $('#vacine_title').val();

		var token;
		token='{{ csrf_token() }}';
		
		$.ajax({
			headers: {'X-CSRF-TOKEN': token},
			url: '/guides/addvacine',
			method:'POST',
			dataType: 'json',
			data: {vacine_title:vacine},
			success: function(data) {
				
				if($.isEmptyObject(data.error)){
					toastr.info(data.success); 
					$('#vacine_title').val('');
				}else{
					printErrorMsg(data.error);
				}
			},
				error: function(err) {	
					toastr.error('Ошибка');
				}
			});	
	}
	
	
	
	function changeVacine () {
		
		// Очистка текстов-ошибок в span
		$('#error_change_vacine_list').html("&nbsp;");
		$('#error_change_vacine_title').html("&nbsp;");
				
		var vacine = $('#change_vacine_title').val();
		var id = $('#vacine_list').val();
		
		var token;
		token='{{ csrf_token() }}';
		
		$.ajax({
			headers: {'X-CSRF-TOKEN': token},
			url: '/guides/changevacine',
			method:'POST',
			dataType: 'json',
			data: {vacine_title:vacine, vacine_list:id},
			success: function(data) {
				
				if($.isEmptyObject(data.error)){
					toastr.info(data.success); 
					
					// Очистка списка
					$('#vacine_list').empty();
					
					// Добавление одного пустого поля для placeholder
					var $empty = $("<option selected='selected'></option>");
					$('#vacine_list').append($empty).trigger('change');

					$('#change_vacine_title').val('').trigger('change');
					
				}else{
					
					// Заполнение span текстами ошибок 
					$.each(data.error,function(field_name,error){						
						$('#error_change_'+field_name).html(error);		
					})

				}
			},
				error: function(err) {	
					toastr.error('Ошибка');
				}
			});	
	}
	

	function changeExpirationDate() {
		
		// Очистка текстов-ошибок в span
		$('#error_exp_date').html("&nbsp;");
		
		date = $('#exp_date').val();

		var token;
		token='{{ csrf_token() }}';
		
		$.ajax({
			headers: {'X-CSRF-TOKEN': token},
			url: '/guides/setexpdate',
			method:'POST',
			dataType: 'json',
			data: {expiration_date:date},
			success: function(data) {
				
				if($.isEmptyObject(data.error)){
					toastr.info(data.success); 
				}else{
					printErrorMsg(data.error);
				}
			},
				error: function(err) {	
					toastr.error('Ошибка');
				}
			});	
			
	}
	
	
	function save_template () {
				
		// Очистка текстов-ошибок в span
		$('#error_plate_title').html("&nbsp;");
		$('#error_plate_type').html("&nbsp;");
		$('#error_plate_text').html("&nbsp;");
		
		template_id = $('#template_id').val();
		plate_title = $('#plate_title').val();
		plate_type = $('#plate_type').val();
		plate_text = $('#plate_text').val();
		

		var token;
		token='{{ csrf_token() }}';
		
		$.ajax({
			headers: {'X-CSRF-TOKEN': token},
			url: '/guides/newtemplate',
			method:'POST',
			dataType: 'json',
			data: {plate_title:plate_title, template_id:template_id, plate_type:plate_type, plate_text:plate_text},
			success: function(data) {
				
				if($.isEmptyObject(data.error)){
					
					toastr.info(data.success); 
					
					settemplates ();
					
					$('#new_template').modal('toggle');
					
				}else{
					printErrorMsg(data.error);
				}
			},
				error: function(err) {	
					toastr.error('Ошибка');
				}
			});	
	}
	
	
	
	// Получение ошибок
	function printErrorMsg (msg) {			
		// Заполнение span текстами ошибок 
		$.each(msg,function(field_name,error){	
			$('#error_'+field_name).html(error);		
        })					
	}
	
	
	// Слушатель кнопки календарик
	function calendarinput1(){  	
		$("#exp_date").trigger("focus");
	}
	
	
	// Открытие диалога - создание шаблона
	function new_template(id) {
		
		// Очистка текстов-ошибок в span
		$('#new_template').find('.error_response').html("&nbsp;");
		
		$('#new_template').find('#myForm')[0].reset();
		
		$('#new_template').find('#plate_type').val(1).trigger('change');

		// Перезагрузка редактора
		editor.destruct();
		editor = new Jodit("#plate_text", {
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
		
		if (id) {
			
			$('#new_template').find('#template_id').val(id);
			
			$.ajax({
				url: '/guides/gettemplatedata',
				method:'GET',
				dataType:'json',
				data: {query:id},
				success: function(data) {
					
					if($.isEmptyObject(data.error)){
						
						$('#new_template').find('#plate_title').val(data.title);
						
						$('#new_template').find('#plate_type').val(data.type).trigger('change');
						
						editor.value = data.text;
						
						$('#new_template').find('#staticBackdropLabel').html('Редактор шаблона');

						$('#new_template').modal('show');
								
					}else{
						toastr.error(data.error);
					}
					
				},
				error: function(err) {	
					//toastr.error(err);
				}
			});
			
		} else {
			
			$('#new_template').find('#staticBackdropLabel').html('Создание шаблона');
			
			$('#new_template').find('#plate_type').val(template_shown_tab).trigger('change');
			
			$('#new_template').modal('show');
		}

	}
	
	
	// Удалить шаблон
	function delete_template_dialog (id) {
		
		$('#delete_template').find('#template_id').val(id);
		$('#delete_template').modal('show');

	}
	
	function delete_template () {
		
		var id = $('#delete_template').find('#template_id').val();
		
		if (id != 0) {
			var token;
			token='{{ csrf_token() }}';
			
			$.ajax({
				headers: {'X-CSRF-TOKEN': token},
				url: '/guides/deletetemplate',
				method:'POST',
				dataType: 'json',
				data: {template_id:id},
				success: function(data) {
					
					if($.isEmptyObject(data.error)){					
						
						settemplates ();
						
						toastr.info(data.success);
						
						$('#delete_template').modal('toggle');
	
					}else{
						toastr.error(data.error);
					}
				},
					error: function(err) {	
						toastr.error(err.status);
					}
				});	
		}else {
			toastr.error('Ошибка удаления');
		}
	}
	
	
	// Открытие диалога - создание анализа
	function new_analysis_template(id) {
		
		// Очистка текстов-ошибок в span
		$('#new_analysis_template').find('.error_response').html("&nbsp;");
		
		$('#new_analysis_template').find('#AnalysisForm')[0].reset();
		
		$('#new_analysis_template').find('#analysis_template_id').val('');
		
		$(".analysis_titres").empty();
		
		if (id) {
		
			$('#new_analysis_template').find('#analysis_template_id').val(id);
			
			$.ajax({
				url: '/guides/getanalisystemplatedata',
				method:'GET',
				dataType:'json',
				data: {query:id},
				success: function(data) {
					
					if($.isEmptyObject(data.error)){
						
						$('#new_analysis_template').find('#staticBackdropLabel').html('Редактор шаблона анализа');
						
						$('#new_analysis_template').find('#analysis_plate_title').val(data.title);
									
						data.text.forEach(function(item, i, arr) {
						  
							if (item.type_todb == 0) {
							  
								var html = "<div class='analisys-titres-group'>"
									+"<div class='row px-0 py-1 align-items-center'>"
																	
										+"<div class='col-lg-4 pe-1 mb-2'>"		
											+"<input name='name_todb' class='form-control form-control-lg' id='name_todb' value='"+item.name_todb+"' placeholder='Название'>"
										+"</div>"
										
										+"<div class='col-lg-2 pe-1 mb-2'>"		
											+"<input name='edizm_todb' class='form-control form-control-lg' id='edizm_todb' value='"+item.edizm_todb+"' placeholder='Ед. изм.'>"
										+"</div>"
										
										+"<div class='col-lg-2 pe-1 mb-2'>"		
											+"<input name='from_todb' class='form-control form-control-lg' id='from_todb' value='"+item.from_todb+"' placeholder='От' oninput=\"this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\\\..*?)\\\..*/g, '$1');\" />"
										+"</div>"
										
										+"<div class='col-lg-2 pe-1 mb-2'>"		
											+"<input name='to_todb' class='form-control form-control-lg' id='to_todb' value='"+item.to_todb+"' placeholder='До' oninput=\"this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\\\..*?)\\\..*/g, '$1');\" />"
										+"</div>"
										
										+"<div class='mb-2 justify-content-left align-items-center align-self-center d-flex col-lg'>"
											+"<a class='nav-link p-1 up-analisys' role='button'>"
												+"<img class='img-responsive' width='25' height='25' src='images/up.png'>"
											+"</a>"
											+"<a class='nav-link p-1 down-analisys align-self-center' role='button'>"
												+"<img class='img-responsive' width='25' height='25' src='images/down.png'>"
											+"</a>"
											+"<a class='nav-link p-1 remove-analisys-titr align-self-center' role='button'>"
												+"<img class='img-responsive' width='25' height='25' src='images/delete_photo.png'>"
											+"</a>"
										+"</div>"
										
										+"<input name='type_todb' id='type_todb' value='"+item.type_todb+"' hidden='true'/>"
									+"</div>"
								+"</div>";
							  
							} else if (item.type_todb == 1) {
							  
							  
								var html = "<div class='analisys-titres-group'>"
									+"<div class='row px-0 py-1 align-items-center'>"
																	
										+"<div class='col-lg-4 pe-1 mb-2'>"		
											+"<input name='name_todb' class='form-control form-control-lg' id='name_todb' value='"+item.name_todb+"' placeholder='Название'>"
										+"</div>"
										
										+"<div class='col-lg-6 pe-1 mb-2'>"		
											+"<input name='value_todb' class='form-control form-control-lg' id='value_todb' value='"+item.value_todb+"' placeholder='Значение'>"
										+"</div>"
										
										+"<div class='mb-2 justify-content-left align-items-center align-self-center d-flex col-lg'>"
											+"<a class='nav-link p-1 up-analisys' role='button'>"
												+"<img class='img-responsive' width='25' height='25' src='images/up.png'>"
											+"</a>"
											+"<a class='nav-link p-1 down-analisys align-self-center' role='button'>"
												+"<img class='img-responsive' width='25' height='25' src='images/down.png'>"
											+"</a>"
											+"<a class='nav-link p-1 remove-analisys-titr align-self-center' role='button'>"
												+"<img class='img-responsive' width='25' height='25' src='images/delete_photo.png'>"
											+"</a>"
										+"</div>"
										
										+"<input class='type_todb' name='type_todb' id='type_todb' value='"+item.type_todb+"' hidden='true'/>"
									+"</div>"
								+"</div>";
  
							}
							
							$(".analysis_titres").append(html);
						  	  
						});
						
						$('#new_analysis_template').modal('show');
								
					}else{
						toastr.error(data.error);
					}
					
				},
				error: function(err) {	
					//toastr.error(err);
				}
			});

			
		} else {
			
			$('#new_analysis_template').find('#staticBackdropLabel').html('Создание шаблона анализа');
			
			$('#new_analysis_template').modal('show');
		}

	}
	
	
	// Добавить титр анализа
	function add_analisys_titr() {
		
		var value = $("input[type='radio'][name='countable_material_checkbox']:checked").val();
			
		if (value == 0) {
			
			var html = "<div class='analisys-titres-group'>"
				+"<div class='row px-0 py-1 align-items-center'>"
												
					+"<div class='col-lg-4 pe-1 mb-2'>"		
						+"<input name='name_todb' class='form-control form-control-lg' id='name_todb' placeholder='Название'>"
					+"</div>"
					
					+"<div class='col-lg-2 pe-1 mb-2'>"		
						+"<input name='edizm_todb' class='form-control form-control-lg' id='edizm_todb' placeholder='Ед. изм.'>"
					+"</div>"
					
					+"<div class='col-lg-2 pe-1 mb-2'>"		
						+"<input name='from_todb' class='form-control form-control-lg' id='from_todb' placeholder='От' oninput=\"this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\\\..*?)\\\..*/g, '$1');\" />"
					+"</div>"
					
					+"<div class='col-lg-2 pe-1 mb-2'>"		
						+"<input name='to_todb' class='form-control form-control-lg' id='to_todb' placeholder='До' oninput=\"this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\\\..*?)\\\..*/g, '$1');\" />"
					+"</div>"
					
					+"<div class='mb-2 justify-content-left align-items-center align-self-center d-flex col-lg'>"
						+"<a class='nav-link p-1 up-analisys' role='button'>"
							+"<img class='img-responsive' width='25' height='25' src='images/up.png'>"
						+"</a>"
						+"<a class='nav-link p-1 down-analisys align-self-center' role='button'>"
							+"<img class='img-responsive' width='25' height='25' src='images/down.png'>"
						+"</a>"
						+"<a class='nav-link p-1 remove-analisys-titr align-self-center' role='button'>"
							+"<img class='img-responsive' width='25' height='25' src='images/delete_photo.png'>"
						+"</a>"
					+"</div>"
					
					+"<input name='type_todb' id='type_todb' value='"+value+"' hidden='true'/>"
				+"</div>"
			+"</div>";
			
		} else {
			
			var html = "<div class='analisys-titres-group'>"
				+"<div class='row px-0 py-1 align-items-center'>"
												
					+"<div class='col-lg-4 pe-1 mb-2'>"		
						+"<input name='name_todb' class='form-control form-control-lg' id='name_todb' placeholder='Название'>"
					+"</div>"
					
					+"<div class='col-lg-6 pe-1 mb-2'>"		
						+"<input name='value_todb' class='form-control form-control-lg' id='value_todb' placeholder='Значение'>"
					+"</div>"
					
					+"<div class='mb-2 justify-content-left align-items-center align-self-center d-flex col-lg'>"
						+"<a class='nav-link p-1 up-analisys' role='button'>"
							+"<img class='img-responsive' width='25' height='25' src='images/up.png'>"
						+"</a>"
						+"<a class='nav-link p-1 down-analisys align-self-center' role='button'>"
							+"<img class='img-responsive' width='25' height='25' src='images/down.png'>"
						+"</a>"
						+"<a class='nav-link p-1 remove-analisys-titr align-self-center' role='button'>"
							+"<img class='img-responsive' width='25' height='25' src='images/delete_photo.png'>"
						+"</a>"
					+"</div>"
					
					+"<input class='type_todb' name='type_todb' id='type_todb' value='"+value+"' hidden='true'/>"
				+"</div>"
			+"</div>";
			
		}
								
		$(".analysis_titres").append(html);
		
	}
	
	
	// Очистить один титр анализа
	$("body").on("click",".remove-analisys-titr",function(){ 
		$(this).parents(".analisys-titres-group").remove();
	});
	
	
	// Сдвинуть вверх титр анализа
	$("body").on("click",".up-analisys",function(){ 
		
		var div = $(this).parents(".analisys-titres-group");
		
		if (div.prev().length != 0) {
						
			div.insertBefore(div.prev());
			
		}
		
		return false;
				
	});
	
	
	// Сдвинуть вниз титр анализа
	$("body").on("click",".down-analisys",function(){ 
		
		var div = $(this).parents(".analisys-titres-group");
		
		if (div.next().length != 0) {
			
			div.insertAfter(div.next());
			
		}
		
		return false;
				
	});
	
	
	// Сохранить шаблон анализа
	function save_analisys_template () {
		
		// Очистка текстов-ошибок в span
		$('#error_analysis_plate_title').html("&nbsp;");
		$('#error_analysis_titres_data').html("&nbsp;");
		
		var analysis_template_id = $('#new_analysis_template').find('#analysis_template_id').val();
		
		
		analysis_plate_title = $('#analysis_plate_title').val();
		
		var analysis_titres_data = new Array();
		
		var emptyvalues = false;
		
		
		
		var items = $('.analysis_titres').find('.analisys-titres-group').each(function() {
			
			var type = $(this).find('#type_todb').val();
			
			var name = '';
			if ($(this).find('#name_todb').val()) {
				$(this).find('#name_todb').removeClass('is-invalid');
				name = $(this).find('#name_todb').val();
			} else {
				$(this).find('#name_todb').addClass('is-invalid');
				emptyvalues = true;
			}
			
			var hash = {};
			
			if (type == 0) {
				
				var edizm = '';
				if ($(this).find('#edizm_todb').val()) {
					$(this).find('#edizm_todb').removeClass('is-invalid');
					edizm = $(this).find('#edizm_todb').val();
				} else {
					$(this).find('#edizm_todb').addClass('is-invalid');
					emptyvalues = true;
				}
				
				var from = '';
				if ($(this).find('#from_todb').val()) {
					$(this).find('#from_todb').removeClass('is-invalid');
					from = $(this).find('#from_todb').val();
				} else {
					$(this).find('#from_todb').addClass('is-invalid');
					emptyvalues = true;
				}
				
				var to = '';
				if ($(this).find('#to_todb').val()) {
					$(this).find('#to_todb').removeClass('is-invalid');
					to = $(this).find('#to_todb').val();
				} else {
					$(this).find('#to_todb').addClass('is-invalid');
					emptyvalues = true;
				}
				
				hash['type_todb'] = type;
				hash['name_todb'] = name;
				hash['edizm_todb'] = edizm;	
				hash['from_todb'] = from;
				hash['to_todb'] = to;
				
			} else if (type == 1) {
								
				var value = '';
				if ($(this).find('#value_todb').val()) {
					$(this).find('#value_todb').removeClass('is-invalid');
					value = $(this).find('#value_todb').val();
				} else {
					$(this).find('#value_todb').addClass('is-invalid');
					emptyvalues = true;
				}
								
				hash['type_todb'] = type;
				hash['name_todb'] = name;
				hash['value_todb'] = value;	
			}
			
			analysis_titres_data.push(hash);
		
        });
		
		
		if (analysis_titres_data.length == 0) {
			analysis_titres_data == null;
		}
		
		if (!emptyvalues) {

			var token;
			token='{{ csrf_token() }}';
			
			$.ajax({
				headers: {'X-CSRF-TOKEN': token},
				url: '/guides/newanalisystemplate',
				method:'POST',
				dataType: 'json',
				data: {analysis_plate_title:analysis_plate_title, analysis_titres_data:analysis_titres_data, analysis_template_id:analysis_template_id},
				success: function(data) {
					
					if($.isEmptyObject(data.error)){
						
						toastr.info(data.success); 
						
						setanalisystemplates ();
						
						$('#new_analysis_template').modal('toggle');
						
					}else{
						printErrorMsg(data.error);
					}
				},
					error: function(err) {	
						toastr.error('Ошибка');
					}
				});
			
		} else {
			
			toastr.error('Не заполнены поля');
			
		}
	}
	
	
	// Удалить шаблон
	function delete_analysis_template_dialog (id) {
		
		$('#delete_analysis_template').find('#analysis_template_id').val(id);
		$('#delete_analysis_template').modal('show');

	}
	
	function delete_analysis_template () {
		
		var id = $('#delete_analysis_template').find('#analysis_template_id').val();
		
		if (id != 0) {
			var token;
			token='{{ csrf_token() }}';
			
			$.ajax({
				headers: {'X-CSRF-TOKEN': token},
				url: '/guides/deleteanalisystemplate',
				method:'POST',
				dataType: 'json',
				data: {analysis_template_id:id},
				success: function(data) {
					
					if($.isEmptyObject(data.error)){					
						
						setanalisystemplates ();
						
						toastr.info(data.success);
						
						$('#delete_analysis_template').modal('toggle');
	
					}else{
						toastr.error(data.error);
					}
				},
					error: function(err) {	
						toastr.error(err.status);
					}
				});	
		}else {
			toastr.error('Ошибка удаления');
		}
	}
	
	
	// Импорт приемов
	function importVisits () {

		$.ajax({
			url: '/guides/importvisits',
			method:'GET',
			dataType:'json',
			//data: {query:query},
			success: function(data) {
				toastr.info(data.success);
			},
				error: function(err) {	
					toastr.error('Ошибка');
				}
			});
			
	}
	
	
	// Импорт товаров
	function importProducts () {

		$.ajax({
			url: '/guides/importproducts',
			method:'GET',
			dataType:'json',
			//data: {query:query},
			success: function(data) {
				toastr.info(data.success);
			},
				error: function(err) {	
					toastr.error('Ошибка');
				}
			});
			
	}
	
	
	// Импорт услуг
	function importServices () {

		$.ajax({
			url: '/guides/importservices',
			method:'GET',
			dataType:'json',
			//data: {query:query},
			success: function(data) {
				toastr.info(data.success);
			},
				error: function(err) {	
					toastr.error('Ошибка');
				}
			});
			
	}
	
	
	// Импорт шаблонов
	function importTemplates () {

		$.ajax({
			url: '/guides/importtemplates',
			method:'GET',
			dataType:'json',
			//data: {query:query},
			success: function(data) {
				toastr.info(data.success);
			},
				error: function(err) {	
					toastr.error('Ошибка');
				}
			});
			
	}
	
	
	// Импорт шаблонов анализов
	function importAnalisysTemplates () {

		$.ajax({
			url: '/guides/importanalisystemplates',
			method:'GET',
			dataType:'json',
			//data: {query:query},
			success: function(data) {
				toastr.info(data.success);
			},
				error: function(err) {	
					toastr.error('Ошибка');
				}
			});
			
	}
	
	
	// Импорт анализов
	function importAnalisys () {

		$.ajax({
			url: '/guides/importanalisys',
			method:'GET',
			dataType:'json',
			//data: {query:query},
			success: function(data) {
				toastr.info(data.success);
			},
				error: function(err) {	
					toastr.error('Ошибка');
				}
			});
			
	}
	
</script>


<!-- .form-control:focus {
	border-color: #8DB2EC;   
	box-shadow: 0 0 1px 1px #8DB2EC !important;
} -->


<style>

.form-group label {
    color: #3182CE !important;
	font-weight: 600;
}

.underinput-error{
	font-size:14px;
	font-weight: 600;
}

.checkbox-label{
    margin-left: 10px;
}

</style>



<div name="central_area" id="central_area" class="d-none border bg-white shadow rounded p-4">	

	
			
		<div class="border border p-3 rounded mt-4">
		
			<ul class="nav nav-pills" id="myTab" role="tablist">
				<li class="nav-item" role="presentation">
					<button class="nav-link active" id="tab-1" data-bs-toggle="tab" data-bs-target="#t1" type="button" role="tab" aria-controls="t1" aria-selected="true">Типы, породы, окрасы</button>
				</li>
				<li class="nav-item" role="presentation">
					<button class="nav-link" id="tab-2" data-bs-toggle="tab" data-bs-target="#t2" type="button" role="tab" aria-controls="t2" aria-selected="false">Шаблоны</button>
				</li>
				<li class="nav-item" role="presentation">
					<button class="nav-link" id="tab-3" data-bs-toggle="tab" data-bs-target="#t3" type="button" role="tab" aria-controls="t3" aria-selected="false">Диагнозы</button>
				</li>
				<li class="nav-item" role="presentation">
					<button class="nav-link" id="tab-33" data-bs-toggle="tab" data-bs-target="#t33" type="button" role="tab" aria-controls="t33" aria-selected="false">Вакцины</button>
				</li>
				<li class="nav-item" role="presentation">
					<button class="nav-link" id="tab-4" data-bs-toggle="tab" data-bs-target="#t4" type="button" role="tab" aria-controls="t4" aria-selected="false">Анализы</button>
				</li>
				<li class="nav-item" role="presentation">
					<button class="nav-link" id="tab-44" data-bs-toggle="tab" data-bs-target="#t44" type="button" role="tab" aria-controls="t44" aria-selected="false">Товары и услуги</button>
				</li>
				<li class="nav-item d-none" role="presentation" name="developer-btn" id='developer-btn'>
					<button class="nav-link" id="tab-5" data-bs-toggle="tab" data-bs-target="#t5" type="button" role="tab" aria-controls="t5" aria-selected="false">Разработчик</button>
				</li>
			</ul>
			
		</div>
			
		<div class="tab-content" id="myTabContent">
			
			<div class="tab-pane fade show active pt-3 px-2" id="t1" role="tabpanel" aria-labelledby="tab-1">
			
				<div class="border-bottom py-2">
					<h4 class="text-body">Новый тип животного</h4>	
				</div>
				
				<div class="row mt-3">
									
					<div class='col-lg-6'>	
					
						<div class="form-group">
							<label class='control-label font-weight-bold' for="type_title">Добавить новый тип<span class="text-danger"></span></label>
							<div class="input-group">
								<input name="type_title" autocomplete="off" class='form-control form-control-lg' id='type_title' type='text'/>
								<button type="button" onclick="addAnimaltype()" class="btn btn-primary" id="MyButton">Сохранить</button>
							</div>
							<span name="error_type_title" id='error_type_title' class="mb-1 mb-lg-0 error_response underinput-error text-danger d-flex justify-content-left align-items-lg-center">
							&nbsp;
							</span>

						</div>
					
					</div>

				</div>
						

				<div class="border-bottom py-2 mt-1">
					<h4 class="text-body">Новая порода</h4>	
				</div>
				
				
				<div class="row mt-3">
				
				
					<div class='col-lg-6'>		
						<div class="form-group">
							
							<label class='control-label font-weight-bold' for="breed_title">Тип животного<span class="text-danger"></span></label>
							<select name="animal_type_id" id="animal_type_id" class="form-select-lg js-example-basic-single block2" style="width: 100%;" placeholder="Не выбрано" data-search="true">
								<option></option>
							</select>

							<span name="error_animal_type_id" id='error_animal_type_id' class="mb-1 mb-lg-0 error_response underinput-error text-danger d-flex justify-content-left align-items-lg-center">
							&nbsp;
							</span>
						
						</div>				
					</div>

					<div class='col-lg-6'>	
					
						<div class="form-group">
							<label class='control-label font-weight-bold' for="breed_title">Добавить новую породу<span class="text-danger"></span></label>
							<div class="input-group">
								<input name="breed_title" autocomplete="off" class='form-control form-control-lg' id='breed_title' type='text'/>
								<button type="button" onclick="addAnimalbreed()" class="btn btn-primary" id="MyButton2">Сохранить</button>
							</div>
							<span name="error_breed_title" id='error_breed_title' class="mb-1 mb-lg-0 error_response underinput-error text-danger d-flex justify-content-left align-items-lg-center">
							&nbsp;
							</span>

						</div>
					
					</div>				

				</div>
							
				
				<div class="border-bottom py-2 mt-1">
					<h4 class="text-body">Новый окрас</h4>	
				</div>
				
				
				<div class="row mt-3">
				
					<div class='col-lg-6'>	
					
						<div class="form-group">
							<label class='control-label font-weight-bold' for="color_title">Добавить новый окрас<span class="text-danger"></span></label>
							<div class="input-group">
								<input name="color_title" autocomplete="off" class='form-control form-control-lg' id='color_title' type='text'/>
								<button type="button" onclick="addAnimalcolor()" class="btn btn-primary" id="MyButton3">Сохранить</button>
							</div>
							<span name="error_color_title" id='error_color_title' class="mb-1 mb-lg-0 error_response underinput-error text-danger d-flex justify-content-left align-items-lg-center">
							&nbsp;
							</span>

						</div>
					
					</div>	

				</div>
			
			</div>
			
			<div class="tab-pane fade pt-3 px-2" id="t2" role="tabpanel" aria-labelledby="tab-2">
			
				<div class="border-bottom py-2">
					<h3 class="text-body">Шаблоны</h3>	
				</div>
				
				<div class="d-flex col-lg justify-content-left align-items-lg-center my-4">
					<div name="clear_plate_text" id="clear_plate_text"  class="d-flex align-items-center" align="center"><button onclick="new_template()" type="button" class="btn btn-success">Новый шаблон</button></div>	
				</div>
				
				
				<div class="mt-2 mb-4">
				
					<ul class="nav nav-tabs" id="myTab" role="tablist">
						<li class="nav-item" role="presentation">
							<button class="nav-link active" id="check-tab" data-bs-toggle="tab" data-bs-target="#check" type="button" role="tab" aria-controls="check" aria-selected="true">Осмотр</button>
						</li>
						<li class="nav-item" role="presentation">
							<button class="nav-link" id="research-tab" data-bs-toggle="tab" data-bs-target="#research" type="button" role="tab" aria-controls="research" aria-selected="false">Исследование</button>
						</li>
						<li class="nav-item" role="presentation">
							<button class="nav-link" id="advice-tab" data-bs-toggle="tab" data-bs-target="#advice" type="button" role="tab" aria-controls="advice" aria-selected="false">Рекомендации</button>
						</li>
					</ul>
					
					<div class="tab-content" id="myTabContent">
						<div class="tab-pane fade show active pt-3 px-1" id="check" role="tabpanel" aria-labelledby="check-tab">
						
							<div name="templates1" id="templates1" class=""></div>
						
						</div>
						
						<div class="tab-pane fade show pt-3 px-1" id="research" role="tabpanel" aria-labelledby="research-tab">
						
							<div name="templates2" id="templates2" class=""></div>
						
						</div>
						
						<div class="tab-pane fade show pt-3 px-1" id="advice" role="tabpanel" aria-labelledby="advice-tab">
						
							<div name="templates3" id="templates3" class=""></div>
						
						</div>

					</div>
				</div>
			
			
			</div>

			<div class="tab-pane fade pt-3 px-2" id="t3" role="tabpanel" aria-labelledby="tab-3">
				
				<div class="border-bottom py-2">
					<h4 class="text-body">Новый диагноз</h4>	
				</div>
							
				<div class="row mt-3">

					<div class='col-lg-8'>		
						<div class="form-group">
							<label class='control-label font-weight-bold' for="diagnosis_title">Добавить новый диагноз<span class="text-danger"></span></label>	
							<div class="input-group">
								<input type="text" class="form-control form-control-lg" aria-describedby="btn-add-diagnosis" name="diagnosis_title" autocomplete="off" id='diagnosis_title'>
								<button class="btn btn-primary" type="button" id="btn-add-diagnosis" onclick="addDiagnosis()">Сохранить</button>
							</div>
							<span name="error_diagnosis_title" id='error_diagnosis_title' class="mb-1 mb-lg-0 error_response underinput-error text-danger d-flex justify-content-left align-items-lg-center">
							&nbsp;
							</span>

						</div>				
					</div>	
			
				</div>
						
				<div class="border-bottom mt-2">
					<h4 class="text-body">Изменить существующий диагноз</h4>	
				</div>
				
				
				<div class="row mt-3">

						<div class='col-lg-6'>		
							<div class="form-group">
								<label class='control-label font-weight-bold' for="diagnosis_list">Старое название<span class="text-danger"></span></label>
								<select name="diagnosis_list" id="diagnosis_list" class="form-select-lg js-example-basic-single block" style="width: 100%;" placeholder="Не выбрано" data-search="true">
									<option></option>
								</select>
								<span name="error_change_diagnosis_list" id='error_change_diagnosis_list' class="mb-lg-0 error_response underinput-error text-danger d-flex justify-content-left align-items-lg-center">
								&nbsp;
								</span>

							</div>				
						</div>	
						<div class='col-lg-6 mb-lg-0 mb-2'>		
							<div class="form-group">
								<label class='control-label font-weight-bold' for="change_diagnosis_title">Новое название<span class="text-danger"></span></label>	
								<div class="input-group">
									<input type="text" class="form-control form-control-lg" aria-describedby="btn-change-diagnosis" name="change_diagnosis_title" autocomplete="off" id='change_diagnosis_title'>
									<button class="btn btn-primary" type="button" id="btn-change-diagnosis" onclick="changeDiagnosis()">Сохранить</button>
								</div>
								<span name="error_change_diagnosis_title" id='error_change_diagnosis_title' class="mb-1 mb-lg-0 error_response underinput-error text-danger d-flex justify-content-left align-items-lg-center">
								&nbsp;
								</span>

							</div>				
						</div>		
					
				</div>
				
			</div>
			
			
			<div class="tab-pane fade pt-3 px-2" id="t33" role="tabpanel" aria-labelledby="tab-33">
				
				<div class="border-bottom py-2">
					<h4 class="text-body">Новая вакцина</h4>	
				</div>
							
				<div class="row mt-3">

					<div class='col-lg-8'>		
						<div class="form-group">
							<label class='control-label font-weight-bold' for="vacine_title">Добавить новую вакцину<span class="text-danger"></span></label>	
							<div class="input-group">
								<input type="text" class="form-control form-control-lg" aria-describedby="btn-add-vacine" name="vacine_title" autocomplete="off" id='vacine_title'>
								<button class="btn btn-primary" type="button" id="btn-add-vacine" onclick="addVacine()">Сохранить</button>
							</div>
							<span name="error_vacine_title" id='error_vacine_title' class="mb-1 mb-lg-0 error_response underinput-error text-danger d-flex justify-content-left align-items-lg-center">
							&nbsp;
							</span>

						</div>				
					</div>	
			
				</div>
						
				<div class="border-bottom mt-2">
					<h4 class="text-body">Изменить существующую вакцину</h4>	
				</div>
				
				
				<div class="row mt-3">

						<div class='col-lg-6'>		
							<div class="form-group">
								<label class='control-label font-weight-bold' for="vacine_list">Старое название<span class="text-danger"></span></label>
								<select name="vacine_list" id="vacine_list" class="form-select-lg js-example-basic-single block vacine_list" style="width: 100%;" placeholder="Не выбрано" data-search="true">
									<option></option>
								</select>
								<span name="error_change_vacine_list" id='error_change_vacine_list' class="mb-lg-0 error_response underinput-error text-danger d-flex justify-content-left align-items-lg-center">
								&nbsp;
								</span>

							</div>				
						</div>	
						<div class='col-lg-6 mb-lg-0 mb-2'>		
							<div class="form-group">
								<label class='control-label font-weight-bold' for="change_vacine_title">Новое название<span class="text-danger"></span></label>	
								<div class="input-group">
									<input type="text" class="form-control form-control-lg" aria-describedby="btn-change-diagnosis" name="change_vacine_title" autocomplete="off" id='change_vacine_title'>
									<button class="btn btn-primary" type="button" id="btn-change-diagnosis" onclick="changeVacine()">Сохранить</button>
								</div>
								<span name="error_change_vacine_title" id='error_change_vacine_title' class="mb-1 mb-lg-0 error_response underinput-error text-danger d-flex justify-content-left align-items-lg-center">
								&nbsp;
								</span>

							</div>				
						</div>		
					
				</div>
				
			</div>
			
			
			<div class="tab-pane fade pt-3 px-2" id="t4" role="tabpanel" aria-labelledby="tab-4">
			
			
				<div class="border-bottom py-2">
					<h3 class="text-body">Шаблоны анализов</h3>	
				</div>
				
				<div class="d-flex col-lg justify-content-left align-items-lg-center my-4">
					<div name="clear_plate_text" id="clear_plate_text"  class="d-flex align-items-center" align="center"><button onclick="new_analysis_template()" type="button" class="btn btn-success">Новый шаблон анализа</button></div>	
				</div>
							
				<div class="mt-2 mb-4">
				
					<div name="analisystemplates" id="analisystemplates" class=""></div>
					
				</div>
						
			</div>
			
			<div class="tab-pane fade pt-3 px-2" id="t44" role="tabpanel" aria-labelledby="tab-44">
			
				<div id="app"> 
					<productsservicesguide-component></productsservicesguide-component>					
				</div>	

			</div>
						
			<div class="tab-pane fade pt-3 px-2" id="t5" role="tabpanel" aria-labelledby="tab-5">
					
				<div name="developer-container" id='developer-container' class="d-none">	
				
					<div class="border-bottom py-2 mt-3 mt-lg-1">
						<h3 class="text-body">Импорт данных</h3>	
					</div>		
					
					<div class='d-inline-flex flex-wrap mt-4 mb-lg-2'>	
										
						<div class=""><button type="button" onclick="importAnimaltypes()" class="btn btn-secondary mb-3 me-3">Типы животных</button></div>
						
						<div class=""><button type="button" onclick="importAnimalbreeds()" class="btn btn-secondary mb-3 me-3">Породы</button></div>

						<div class=""><button type="button" onclick="importAnimalcolors()" class="btn btn-secondary mb-3 me-3">Окрасы</button></div>

						<div class=""><button type="button" onclick="importPatientsandClients()" class="btn btn-secondary mb-3 me-3">Пациенты и клиенты</button></div>
						
						<div class=""><button type="button" onclick="importDiagnosis()" class="btn btn-secondary mb-3 me-3">Диагнозы</button></div>
						
						<div class=""><button type="button" onclick="importVisits()" class="btn btn-secondary mb-3 me-3">Приемы</button></div>
						
						<div class=""><button type="button" onclick="importProducts()" class="btn btn-secondary mb-3 me-3">Товары</button></div>
						
						<div class=""><button type="button" onclick="importServices()" class="btn btn-secondary mb-3 me-3">Услуги</button></div>
						
						<div class=""><button type="button" onclick="importTemplates()" class="btn btn-secondary mb-3 me-3">Шаблоны</button></div>
						
						<div class=""><button type="button" onclick="importAnalisysTemplates()" class="btn btn-secondary mb-3 me-3">Шаблоны анализов</button></div>
						
						<div class=""><button type="button" onclick="importAnalisys()" class="btn btn-secondary mb-3 me-3">Анализы</button></div>
						
					</div>

					<div class="border-bottom py-2 mt-2">
						<h3 class="text-body">Установить срок использования</h3>	
					</div>

					<div class="row mt-3">
						
						<div class='d-lg-flex form-group'>
							<label class='control-label font-weight-bold' for="exp_date">Выбрать дату<span class="text-danger"></span></label>			
						</div>						
						<div class='d-lg-flex'>		
							<div class='col-lg-4 px-0'>		
								<div class="form-group">
									
									<div class="input-group input-group-lg">
										<input name="exp_date" id="exp_date" type="text" class="form-control datepicker-here appearance-none bg-white" data-position='top left' placeholder="Выберите дату" readonly />
										<button type="button" onclick="calendarinput1()" class="bg-white input-group-text" id="inputGroup-sizing-lg">
											<img src="{{ asset('images/calendar.png') }}" class="img-responsive"  width="30" height="30" />
										</button>
									</div>	
									
									<span name="error_exp_date" id='error_exp_date' class="error_response underinput-error text-danger d-flex justify-content-left align-items-lg-center">
									&nbsp;
									</span>

								</div>				
							</div>	
							<div class='d-flex justify-content-lg-center align-items-lg-top mt-1 mt-lg-3 ms-lg-4'>
								<div class=""><button type="button" onclick="changeExpirationDate()" class="btn btn-primary">Сохранить</button></div>
							</div>
						</div>

					</div>
					
				</div>
				
			</div>
			
		</div>
		
		
		<!-- Модальные окна -->	

		<!-- Создать-изменить шаблон -->
		<div class="modal hide mycontainer" id="new_template" role="dialog" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false" style="display: none">
			<div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
				<div class="modal-content">
					<div class="modal-header">
						<h5 class="modal-title px-3" id="staticBackdropLabel">Создание шаблона</h5>
						<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
					</div>
					<div class="modal-body">
						
						<div class="px-4">
						
						<form id="myForm" autocomplete="off">					
						@csrf
						
							<input name="template_id" id='template_id' value="0" hidden="true"/>

							<div class="">
		
								<div class="row d-lg-flex justify-content-left align-items-lg-center">					
															
									<div class="col-lg-6 mb-2 mb-lg-0 form-group">	
										<label class='control-label font-weight-bold text-break' for="plate_title">Название <span class="text-danger">*</span></label>
										<input name="plate_title" autocomplete="off" class='form-control form-control-lg' id='plate_title' type='text' placeholder="Введите наименование"/>		
										<span name="error_plate_title" id='error_plate_title' class="error_response underinput-error text-danger d-flex justify-content-left align-items-lg-center">
										&nbsp;
										</span>
									</div>	

									<div class="col-lg-6 mb-2 mb-lg-0 form-group">	
										<label class='control-label font-weight-bold text-break' for="plate_title">Тип <span class="text-danger">*</span></label>
										<select name="plate_type" id="plate_type" class="form-select-lg js-example-basic-hide" style="width: 100%;" placeholder="Не выбрано">
											<option value="1" selected="selected">Осмотр</option>
											<option value="2">Исследование</option>
											<option value="3">Рекомендации</option>
										</select>
										<span name="error_plate_type" id='error_plate_type' class="error_response underinput-error text-danger d-flex justify-content-left align-items-lg-center">
										&nbsp;
										</span>
									</div>
									
								</div>
							
							</div>
							
							<div class="col-lg mt-lg-1">
								<textarea name="plate_text" class="form-control" id='plate_text' rows="10"></textarea>	
								<span name="error_plate_text" id='error_plate_text' class="mb-1 error_response underinput-error text-danger d-flex justify-content-left align-items-lg-center">
								&nbsp;
								</span>		
							</div>
				

						</form>

						</div>
										
					</div>
					<div class="modal-footer pe-4">
						<button type="button" class="btn btn-primary" onclick="save_template()">Сохранить</button>		
					</div>
				</div>
			</div>
		</div>
		
		
		<!-- Удалить шаблон -->
		<div class="modal hide mycontainer" id="delete_template" role="dialog" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false" style="display: none">
			<div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
				<div class="modal-content">
					<div class="modal-header">
						<h5 class="modal-title px-3" id="staticBackdropLabel">Удалить шаблон</h5>
						<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
					</div>
					<div class="modal-body">
						
						<div class="px-3">
						
							<div class="text-break">Вы действительно хотите удалить шаблон ?</div>
							
							<input name="template_id" id='template_id' value="0" hidden="true"/>

						</div>
										
					</div>
					<div class="modal-footer pe-4">
						<button type="button" class="btn btn-danger" onclick="delete_template()">Удалить</button>		
					</div>
				</div>
			</div>
		</div>
		
		
		<!-- Создать-изменить шаблон анализа -->
		<div class="modal hide mycontainer" id="new_analysis_template" role="dialog" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false" style="display: none">
			<div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
				<div class="modal-content">
					<div class="modal-header">
						<h5 class="modal-title px-3" id="staticBackdropLabel">Создание шаблона анализа</h5>
						<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
					</div>
					<div class="modal-body">
						
						<div class="px-4">
						
						<form id="AnalysisForm" autocomplete="off">					
						@csrf
						
							<input name="analysis_template_id" id='analysis_template_id' value="0" hidden="true"/>


							<div class="row d-lg-flex justify-content-left align-items-lg-center">					
														
								<div class="col-lg-6 mb-1 mb-lg-0 form-group">	
									<label class='control-label font-weight-bold text-break' for="analysis_plate_title">Название <span class="text-danger">*</span></label>
									<input name="analysis_plate_title" autocomplete="off" class='form-control form-control-lg' id='analysis_plate_title' type='text' placeholder="Введите наименование"/>		
									<span name="error_analysis_plate_title" id='error_analysis_plate_title' class="error_response underinput-error text-danger d-flex justify-content-left align-items-lg-center">
									&nbsp;
									</span>
								</div>	
								
							</div>
													
							<div class="d-lg-flex flex-row">
														
								<div class='d-lg-flex px-0 align-items-center'> 
																							
									<div class='justify-content-left align-self-center align-items-lg-end flex-column'>
									
										<div class="form-check align-self-end align-items-lg-center d-flex">
											<input class="form-check-input align-self radio_analisys" type="radio" value="0" name="countable_material_checkbox" id="countable_material_checkbox_1" checked>
											<label class="form-check-label checkbox-label align-self" for="countable_material_checkbox_1">
												Количественный показатель
											</label>
											
											
										</div>	
													
										<div class="form-check align-self-end align-items-lg-center d-flex">
											<input class="form-check-input align-self radio_analisys" type="radio" value="1" name="countable_material_checkbox" id="countable_material_checkbox_2">
											<label class="form-check-label checkbox-label align-self" for="countable_material_checkbox_2">
												Качественный показатель
											</label>
											
										</div>

									</div>
									
									<div class='justify-content-left align-items-lg-end align-self-center d-flex px-lg-2 px-0 mt-lg-0 mt-2'> 
										<button type="button" onclick="add_analisys_titr()" class="btn btn btn-success btn-add-diagnosis">
											Добавить
										</button>
									</div>	

								</div>

							</div>	
							
							<div class="mt-3 analysis_titres" name="analysis_titres" id='analysis_titres'>
								
							</div>
							
							<span name="error_analysis_titres_data" id='error_analysis_titres_data' class="error_response underinput-error text-danger d-flex justify-content-left align-items-lg-center">
							&nbsp;
							</span>
				

						</form>

						</div>
										
					</div>
					<div class="modal-footer pe-4">
						<button type="button" class="btn btn-primary" onclick="save_analisys_template()">Сохранить</button>		
					</div>
				</div>
			</div>
		</div>
		
		
		
		<!-- Удалить шаблон анализа -->
		<div class="modal hide mycontainer" id="delete_analysis_template" role="dialog" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false" style="display: none">
			<div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
				<div class="modal-content">
					<div class="modal-header">
						<h5 class="modal-title px-3" id="staticBackdropLabel">Удалить шаблон анализа</h5>
						<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
					</div>
					<div class="modal-body">
						
						<div class="px-3">
						
							<div class="text-break">Вы действительно хотите удалить шаблон анализа ?</div>
							
							<input name="analysis_template_id" id='analysis_template_id' value="0" hidden="true"/>

						</div>
										
					</div>
					<div class="modal-footer pe-4">
						<button type="button" class="btn btn-danger" onclick="delete_analysis_template()">Удалить</button>		
					</div>
				</div>
			</div>
		</div>
	
	
	
	
	<script>

		// Закрытие всех открытых popup при начале скрола страницы
		$(document).scroll(function() {

			// datepicker
			$('#exp_date').datepicker("hide");
			$('#exp_date').blur();

			var cusid_ele = document.getElementsByClassName('dropdown-m1');
			for (var i = 0; i < cusid_ele.length; ++i) {

				if ($('.dropdown-m1-'+i).is(":visible")){
			
					$('.dropdown-m1-'+i).dropdown('toggle');
				}
			}	

			var cusid_ele = document.getElementsByClassName('dropdown-m2');
			for (var i = 0; i < cusid_ele.length; ++i) {

				if ($('.dropdown-m2-'+i).is(":visible")){
			
					$('.dropdown-m2-'+i).dropdown('toggle');
				}
			}	
			
			var cusid_ele = document.getElementsByClassName('dropdown-m3');
			for (var i = 0; i < cusid_ele.length; ++i) {

				if ($('.dropdown-m3-'+i).is(":visible")){
			
					$('.dropdown-m3-'+i).dropdown('toggle');
				}
			}
			
			var cusid_ele = document.getElementsByClassName('dropdown-m4');
			for (var i = 0; i < cusid_ele.length; ++i) {

				if ($('.dropdown-m4-'+i).is(":visible")){
			
					$('.dropdown-m4-'+i).dropdown('toggle');
				}
			}
			
		});

		
		// Закрытие всех открытых popup при начале скрола модального окна
		$('.modal-body').scroll(function(){

			// jodit editor
			$('.jodit-popup__content').hide();
			
		});
		
		
		var editor = new Jodit("#plate_text", {
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
	
		
		// Активация DatePicker
		$('#exp_date').datepicker({
			language: 'ru',
			autoClose: true,
			clearButton: true 			
		});
				
				
		// Активация поллей Select2
		// С поиском
		$('.js-example-basic-single').select2({
			placeholder: "Выберите значение",
			theme: "bootstrap-5"
		});
		
		
		$('#plate_type').select2({
			minimumResultsForSearch: Infinity,
			theme: "bootstrap-5"
		});
		
		
		// Заполнение данных - тип животного				
		$.ajax({
			url: '/guides/animaltype_search',
			method:'GET',
			dataType:'json',
			success: function(data) {
				$('#animal_type_id').select2({ 
					placeholder: "Выберите значение",
					theme: "bootstrap-5",
					allowClear: true,
					data: data })
						.on('select2:unselecting', function() {
							$(this).data('unselecting', true);					
							$('#breed_title').val('').trigger('change');
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
		
		
		// Слушатель выбора диагноза
		$('#animal_type_id').on("select2:select", function(e) { 
			$('#breed_title').val('').trigger('change');
		});
		
		// Заполнение данных - диагнозы				
		$('#diagnosis_list').select2({
			minimumInputLength: 3,
			theme: "bootstrap-5",
			language: {
				inputTooShort: function() {
					return 'Введите не менее 3-х символов';	
				}
			},	
			placeholder: "Выберите диагноз",
			theme: "bootstrap-5",
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
			$('#change_diagnosis_title').val('').trigger('change');
		}).on('select2:opening', function(e) {
			if ($(this).data('unselecting')) {
				$(this).removeData('unselecting');
				e.preventDefault();
			}
		});
		
		
		// Слушатель выбора диагноза
		$('#diagnosis_list').on("select2:select", function(e) { 
			if ($('#diagnosis_list').val() != null) {
				var data = $('#diagnosis_list').select2('data');
				$('#change_diagnosis_title').val(data[0].text).trigger('change');				
			} else {
						
			}
		});
		
	
	
		// Заполнение данных - вакцины				
		$('#vacine_list').select2({
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
			$('#change_vacine_title').val('').trigger('change');
		}).on('select2:opening', function(e) {
			if ($(this).data('unselecting')) {
				$(this).removeData('unselecting');
				e.preventDefault();
			}
		});
				
			
		// Слушатель выбора вакцины
		$('#vacine_list').on("select2:select", function(e) { 
			if ($('#vacine_list').val() != null) {
				var data = $('#vacine_list').select2('data');
				$('#change_vacine_title').val(data[0].text).trigger('change');				
			} else {
						
			}
		});
		

		// Custom результат для Select2 - поиск товаров
		function formatProductResult (data) {
			if (!data.id) {
				return data.text;
			}				  
			if (data.price) {
				var $data = $('<span>' + data.text + '</span> <br> <div class="fst-italic">' + data.price + '</div>');
			} else {
				var $data = $('<span>' + data.text + '</span>');
			}			
			return $data;
		};
		
		
		// Установка списка шаблонов
		function settemplates () {
						
			$('#templates1').empty();
			
			if($('#templates1').is(':empty') ||  !$.trim( $('#templates1').html()).length) {			
				$('#templates1').append("<div class='d-flex justify-content-center my-4'> <div class='spinner-border text-secondary' role='status'> <span class='visually-hidden'>Loading...</span> </div> </div>");			
			}
			
			$('#templates2').empty();
			
			if($('#templates2').is(':empty') ||  !$.trim( $('#templates2').html()).length) {			
				$('#templates2').append("<div class='d-flex justify-content-center my-4'> <div class='spinner-border text-secondary' role='status'> <span class='visually-hidden'>Loading...</span> </div> </div>");			
			}
			
			$('#templates3').empty();
			
			if($('#templates3').is(':empty') ||  !$.trim( $('#templates3').html()).length) {			
				$('#templates3').append("<div class='d-flex justify-content-center my-4'> <div class='spinner-border text-secondary' role='status'> <span class='visually-hidden'>Loading...</span> </div> </div>");			
			}
			
		
			setTimeout(function () {
				$.ajax({
				url: '/guides/gettemplateslist',
				method:'GET',
				dataType:'json',
				success: function(data) {
					
					if($.isEmptyObject(data.error)){
					
						
						$('#templates1').empty();
						$('#templates1').append(data.output1);
						
						$('#templates2').empty();
						$('#templates2').append(data.output2);
						
						$('#templates3').empty();
						$('#templates3').append(data.output3);
						
						//$('#templates4').html("");
						//$('#templates4').append(data.output4);
						
						
					}else{
						toastr.error(data.error);
					}	
				},
					error: function(err) {	
						toastr.error('Ошибка');
					}
				});
				
			}, 300);
			
		}
		
		
		
		// Установка списка шаблонов
		function setanalisystemplates () {
			
			
			$('#analisystemplates').empty();
			
			if($('#analisystemplates').is(':empty') ||  !$.trim( $('#analisystemplates').html()).length) {			
				$('#analisystemplates').append("<div class='d-flex justify-content-center my-4'> <div class='spinner-border text-secondary' role='status'> <span class='visually-hidden'>Loading...</span> </div> </div>");			
			}
		
			setTimeout(function () {
				$.ajax({
				url: '/guides/getanalisystemplateslist',
				method:'GET',
				dataType:'json',
				success: function(data) {
					
					if($.isEmptyObject(data.error)){
					
						$('#analisystemplates').empty();
						$('#analisystemplates').append(data.output);
						
					}else{
						toastr.error(data.error);
					}	
				},
					error: function(err) {	
						toastr.error('Ошибка');
					}
				});
				
			}, 300);
			
		}
		
		
						
	</script>

</div>
@endsection