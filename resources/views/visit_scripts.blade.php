<script> 

	var needtorefresh = false;


	$(document).ready(function () {
				
		// Инициализация календарей
		var myDataPicker = $('#visit_date_start').datepicker({ 
			language : 'ru', 
			autoClose : true,
			clearButton : true,
			onSelect: function (dateText, inst) {
				if (!needtorefresh) {
					show_visits_list();
				}
			}
			
		}).data('datepicker');
		
		var myDataPicker = $('#visit_date_end').datepicker({ 
			language : 'ru', 
			autoClose : true,
			clearButton : true,
			onSelect: function (dateText, inst) {
				if (!needtorefresh) {
					show_visits_list();
				}
			}
		}).data('datepicker');
		
	
		// Слушатели выбора списков в фильтре приемов
			
		$('#doctor_2').on("select2:select", function(e) { if (!needtorefresh) { show_visits_list(); } });
		
		$('#doctor_2').on("select2:unselect", function (e) { if (!needtorefresh) { show_visits_list(); } });
		
		$('#porpose_2').on("select2:select", function(e) { if (!needtorefresh) { show_visits_list(); } });
		
		$('#porpose_2').on("select2:unselect", function (e) { if (!needtorefresh) { show_visits_list(); } });
		
		$('#diagnosis_list_2').on("select2:select", function(e) { if (!needtorefresh) { show_visits_list(); } });
		
		$('#diagnosis_list_2').on("select2:unselect", function (e) { if (!needtorefresh) { show_visits_list(); } });
		
	});



	// Слушатели кнопок календарик
	function calendarinputVDS(){  	
		$("#visit_date_start").trigger("focus");
	}

	function calendarinputVDE(){  	
		$("#visit_date_end").trigger("focus");
	}



	function show_visits_list(){
		
		var SearchForm = $("#search_form").serialize();
		
		
		var doctor_fio = '';
		
		var doctordata = $('#doctor_2').select2('data');
		

		if (doctordata[0] != null && doctordata[0].text != 0) {
			doctor_fio = doctordata[0].text;
		}
		
		if ({{ $patient_id }}) {
			
			SearchForm = SearchForm + '&anymal_id=' + {{ $patient_id }} + '&doctor=' + doctor_fio; 
			
		} 

		$('#visits_list_response').empty();
		
		// Скрываем детальное отображение визитов
		visits_opened = false;		
		$('.full_visit').addClass('d-none');
		$('.short_visit').removeClass('d-none');
		$('#collapse_expand_visits').html('Детально');
		
		
		if($('#visits_list_response').is(':empty') ||  !$.trim( $('#visits_list_response').html()).length) {			
			$('#visits_list_response').append("<div class='d-flex justify-content-center my-4'> <div class='spinner-border text-secondary' role='status'> <span class='visually-hidden'>Loading...</span> </div> </div>");			
		}
		
					
		setTimeout(function () {
			$.ajax({
				url: '/patientcard/get_visits_list',				
				method:'GET',
				dataType:'json',
				data: SearchForm,
				
				success: function(data) {
					
					$('#visits_list_response').empty();
					
					if($.isEmptyObject(data.error)){
					
						$('#visits_list_response').append(data.success);

					}else{
						
						$('#visits_list_response').append('<div class="d-flex justify-content-center align-items-center"> <div class="d-block px-4 py-2">Нет приемов</div></div>');

						toastr.error(data.error);
					}	
				},
					error: function(err) {

						$('#visits_list_response').empty();
						
						$('#visits_list_response').append('<div class="d-flex justify-content-center align-items-center"> <div class="d-block px-4 py-2">Нет приемов</div></div>');
						
						toastr.error('Ошибка');
					}
				});
			}, 300);

	}

	
	// Отображаем данные приема
	function open_visit(id){
		
		visitopened = id;
		
		$('#one_visit').empty();
		
		$('#print_visit_div').empty();

		var staff_id = {{ Auth::user()->staff_id }};
		
		var short_name = $('#short_name_fullcard').text();
		
		var anymal_type = $('#anymal_type_fullcard').text();
		
		var anymal_breed = $('#anymal_breed_fullcard').text(); 
		
		var anymal_sex = $('#anymal_sex_fullcard').text(); 
		
		var anymal_color = $('#color_fullcard').text(); 
		
		var anymal_birthday = $('#birth_date_fullcard').text(); 
		
		var client_name = $('#client_name').text(); 
		
		var address_city_part = $('#city_fullcard').text();
		
		if (address_city_part == 'Нет') {			
			address_city_part = '';
		}
		
		var address_part = $('#address_fullcard').text();
		
		if (address_part == 'Нет') {			
			address_part = '';
		}
		
		var address_comma_part = '';
		
		if (address_city_part && address_part) {			
			address_comma_part = ', ';
		}
			
		var client_address = address_city_part + address_comma_part + address_part;
		
		$.ajax({
				url: '/patientcard/get_one_visit',
				method:'GET',
				dataType:'json',
				data: {visit_id: id, staff_id: staff_id, short_name: short_name, anymal_type: anymal_type, anymal_breed: anymal_breed, anymal_sex:anymal_sex, anymal_color: anymal_color, anymal_birthday: anymal_birthday, client_name: client_name, client_address: client_address},
				success: function(data) {
					
					$('#one_visit').empty();
					
					$('#print_visit_div').empty();
					
					if($.isEmptyObject(data.error)){
					
						$('#one_visit').append(data.success);
						
						$('#print_visit_div').append(data.successprint);
						
					}else{
						toastr.error(data.error);
					}	
				},
					error: function(err) {

						$('#one_visit').empty();
						
						$('#print_visit_div').empty();
						
						toastr.error('Ошибка');
					}
				});

		
		$("#visit_list").fadeOut(0);
		$("#one_visit").fadeIn(300);
	}
	
	
	// Возврат к списку визитов
	function close_visit(){ 
	
		visitopened = '';
		
		// Обновляем список визитов
		show_visits_list();
	
		$("#one_visit").fadeOut(0);
		$("#visit_list").fadeIn(300);
	}
	
	
	// Печать визита
	function print_visit() {

		// В отдельном окне
		/*var mywindow = window.open('', 'Форма печати приема', 'height=600,width=900');
		
		mywindow.document.write('<html><head><title></title>');
		mywindow.document.write('<style> @page {size: 210mm 297mm; margin: 1cm;} </style>');			
		mywindow.document.write('<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-BmbxuPwQa2lc/FVzBcNJ7UAyJxM6wuqIj61tLrc4wSX0szH/Ev+nYRRuWlolflfl" crossorigin="anonymous">');
		mywindow.document.write('<link href="https://fonts.googleapis.com/css?family=Titillium+Web:400,400i,600,600i,700&display=swap" rel="stylesheet">');
		mywindow.document.write('</head><body >');
		
		var printContents = document.getElementById('print_visit_div').innerHTML;
		mywindow.document.write(printContents);
		
		mywindow.document.write('</body></html>');
		
		//mywindow.document.close();
		mywindow.focus();
		
		setTimeout(function(){
			mywindow.print(); 
			mywindow.close();	
		},300);*/
		
		
		var contents = document.getElementById('print_visit_div').innerHTML;
		var frame1 = document.createElement('iframe');
		frame1.name = "frame1";
		frame1.style.position = "absolute";
		frame1.style.top = "-1000000px";
		document.body.appendChild(frame1);
		var frameDoc = frame1.contentWindow ? frame1.contentWindow : frame1.contentDocument.document ? frame1.contentDocument.document : frame1.contentDocument;
		frameDoc.document.open();
		frameDoc.document.write('<!doctype html><html lang="en"><head><title>&nbsp;</title>');
		frameDoc.document.write('<style> @page {size: 252.45mm 357mm; margin: 1cm;}</style>');
		frameDoc.document.write('<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-BmbxuPwQa2lc/FVzBcNJ7UAyJxM6wuqIj61tLrc4wSX0szH/Ev+nYRRuWlolflfl" crossorigin="anonymous">');
		frameDoc.document.write('<link href="https://fonts.googleapis.com/css?family=Titillium+Web:400,400i,600,600i,700&display=swap" rel="stylesheet">');
		frameDoc.document.write('</head><body>');
		frameDoc.document.write(contents);
		frameDoc.document.write('</body></html>');
		frameDoc.document.close();
				
		setTimeout(function () {
			window.frames["frame1"].focus();
			window.frames["frame1"].print();
			document.body.removeChild(frame1);
		}, 300);
		
		return false;
		
	}
	
	
	// Изменить прием
	function change_visit(id){
		new_visit(id);
	}
	
	
	// Диалог - удалить прием
	function delete_visit_dialog(id){

		$('#delete_visit').find('#visitdelete_id').val('0');
		$('#delete_visit').find('#visitdelete_id').val(id);
		$('#delete_visit').modal('show');

	}
	
	
	function delete_visit(){
			
		var id = $('#delete_visit').find('#visitdelete_id').val();
		
		var token;
		token='{{ csrf_token() }}';
		
		$.ajax({
			headers: {'X-CSRF-TOKEN': token},
			url: '/patientcard/delete_visit',
			method:'POST',
			dataType: 'json',
			data: {id: id},
			success: function(data) {
				
				if($.isEmptyObject(data.error)){

					toastr.info(data.success);					
					
					close_visit();	
					
					// Обновляем данные пациента-клиента на странице
					get_one_client_patient_data({{ $patient_id }});
					
					
					// Обновление списка приемов
					$(".tovisit").empty();
						
					// Добавление одного пустого поля для placeholder
					var $empty = $("<option selected='selected'></option>");
					$(".tovisit").append($empty).trigger('change');	
		
					// Установка списка - список визитов
					set_visits_list();
					

					$('#delete_visit').modal('toggle');					
					
				}else{
					toastr.error(data.error);
				}
			},
				error: function(err) {	
					toastr.error('Ошибка');
				}
			});	
	}
	
	
	// Очистка данных
	function clear_form() {

		if (($('#porpose_2').val() != 0 & $('#porpose_2').val() != null) | ($('#diagnosis_list_2').val()!= 0 & $('#diagnosis_list_2').val()!= null) | ($('#doctor_2').val() != 0 & $('#doctor_2').val() != null) | ($('#visit_date_start').val() != '') | ($('#visit_date_end').val() != '')) {
					
			needtorefresh = true;		
		
			$('#search_form')[0].reset();
			
			$('#visit_date_start').datepicker().data('datepicker').clear();
			
			$('#visit_date_end').datepicker().data('datepicker').clear();
			
			$('#porpose_2').val(0).trigger('change');
			
			$('#diagnosis_list_2').val(0).trigger('change');
			
			$('#doctor_2').val(0).trigger('change');
			
			needtorefresh = false;	
			
			show_visits_list();
		
		}
		
	}
	
</script> 
