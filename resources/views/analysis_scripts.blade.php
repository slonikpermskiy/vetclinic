<script> 

	var needtorefresh = false;

	$(document).ready(function () {
		
		// Инициализация календарей
		var myDataPicker = $('#analisys_visit_date_start').datepicker({ 
			language : 'ru', 
			autoClose : true,
			clearButton : true,
			onSelect: function (dateText, inst) {
				if (!needtorefresh) {
					show_analysis_list();
				}
			}
			
		}).data('datepicker');
		
		var myDataPicker = $('#analisys_visit_date_end').datepicker({ 
			language : 'ru', 
			autoClose : true,
			clearButton : true,
			onSelect: function (dateText, inst) {
				if (!needtorefresh) {
					show_analysis_list();
				}
			}
		}).data('datepicker');
		

		// Слушатели выбора списков в фильтре исследований
			
		$('#doctor_4_filter').on("select2:select", function(e) { if (!needtorefresh) { show_analysis_list(); } });
		
		$('#doctor_4_filter').on("select2:unselect", function (e) { if (!needtorefresh) { show_analysis_list(); } });
		
		$('#analisys_to_visit_filter').on("select2:select", function(e) { if (!needtorefresh) { show_analysis_list(); } });
		
		$('#analisys_to_visit_filter').on("select2:unselect", function (e) { if (!needtorefresh) { show_analysis_list(); } });
		
			
		$('#analisys_name_filter').bind("input propertychange", function (evt) {
			// If it's the propertychange event, make sure it's the value that changed.
			if (window.event && event.type == "propertychange" && event.propertyName != "value")
				return;
			// Clear any previously set timer before setting a fresh one
			window.clearTimeout($(this).data("timeout"));
			$(this).data("timeout", setTimeout(function () {
				// Do your thing here
				if ($('#analisys_name_filter').val().length >= 3 | $('#analisys_name_filter').val().length == 0) {
					if (!needtorefresh) {
						show_analysis_list();
					}
				}
			}, 500));
		});

	});
	
	
	
	// Слушатели кнопок календарик
	function calendarinputAVDS(){  	
		$("#analisys_visit_date_start").trigger("focus");
	}

	function calendarinputAVDE(){  	
		$("#analisys_visit_date_end").trigger("focus");
		
	}
	
	
	// Обновление списка исследований
	function show_analysis_list(){
				
		var AnalisysForm = $("#analisys_form").serialize();
		
		var doctor_fio = '';
		
		var doctordata = $('#doctor_4_filter').select2('data');
		

		if (doctordata[0] != null && doctordata[0].text != 0) {
			doctor_fio = doctordata[0].text;
		}
		
		if ({{ $patient_id }}) {
			
			AnalisysForm = AnalisysForm + '&anymal_id=' + {{ $patient_id }} + '&doctor=' + doctor_fio; 
			
		} 

		
		$('#analisys_list_response').empty();
		
		if($('#analisys_list_response').is(':empty') ||  !$.trim( $('#analisys_list_response').html()).length) {			
			$('#analisys_list_response').append("<div class='d-flex justify-content-center my-4'> <div class='spinner-border text-secondary' role='status'> <span class='visually-hidden'>Loading...</span> </div> </div>");			
		}
		
					
		setTimeout(function () {
			$.ajax({
				url: '/patientcard/get_analysis_list',				
				method:'GET',
				dataType:'json',
				data: AnalisysForm,
				
				success: function(data) {
					
					$('#analisys_list_response').empty();
					
					if($.isEmptyObject(data.error)){
					
						$('#analisys_list_response').append(data.success);

					}else{
						
						$('#analisys_list_response').append('<div class="d-flex justify-content-center align-items-center"> <div class="d-block px-4 py-2">Нет анализов</div></div>');

						toastr.error(data.error);
					}	
				},
					error: function(err) {
						
						$('#analisys_list_response').empty();

						$('#analisys_list_response').append('<div class="d-flex justify-content-center align-items-center"> <div class="d-block px-4 py-2">Нет анализов</div></div>');
						
						toastr.error('Ошибка');
					}
				});
			}, 300);

	}
	
	
	// Открываем анализ из визита
	function open_analysis_from_visit(id){
		
		// Открываем вкладку
		var someTabTriggerEl = document.querySelector('#analisys-tab')
		var tab = new bootstrap.Tab(someTabTriggerEl)
		tab.show();
		
		// Открываем анализ
		open_analysis(id);
		
		document.documentElement.scrollTop = 0;
		
	}

	
	// Отображаем данные исследования
	function open_analysis(id){
				
		analysisopened = id;
		
		$('#one_analisys').empty();
		
		$('#print_analisys_div').empty();

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
				url: '/patientcard/get_one_analysis',
				method:'GET',
				dataType:'json',
				data: {analysis_id: id, staff_id: staff_id, short_name: short_name, anymal_type: anymal_type, anymal_breed: anymal_breed, anymal_sex:anymal_sex, anymal_color: anymal_color, anymal_birthday: anymal_birthday, client_name: client_name, client_address: client_address},
				success: function(data) {
					
					$('#one_analisys').empty();
					
					$('#print_analisys_div').empty();
					
					if($.isEmptyObject(data.error)){
					
						$('#one_analisys').append(data.success);
						
						$('#print_analisys_div').append(data.successprint);
						
					}else{
						toastr.error(data.error);
					}	
				},
					error: function(err) {

						$('#one_analisys').empty();
						
						$('#print_analisys_div').empty();
						
						toastr.error('Ошибка');
					}
				});

		
		$("#analisys_search_list").fadeOut(0);
		$("#one_analisys").fadeIn(300);
	}
	
	
	
	// Возврат к списку исследований
	function close_analysis(){ 
	
		analysisopened = '';
		
		// Обновляем список визитов
		show_analysis_list();
	
		$("#one_analisys").fadeOut(0);
		$("#analisys_search_list").fadeIn(300);
	}
	
	
	// Печать визита
	function print_analysis() {

		// В отдельном окне
		/*var mywindow = window.open('', 'Форма печати анализа', 'height=600,width=900');
		
		mywindow.document.write('<html><head><title></title>');
		mywindow.document.write('<style> @page {size: 210mm 297mm; margin: 1cm;} </style>');			
		mywindow.document.write('<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-BmbxuPwQa2lc/FVzBcNJ7UAyJxM6wuqIj61tLrc4wSX0szH/Ev+nYRRuWlolflfl" crossorigin="anonymous">');
		mywindow.document.write('<link href="https://fonts.googleapis.com/css?family=Titillium+Web:400,400i,600,600i,700&display=swap" rel="stylesheet">');
		mywindow.document.write('</head><body >');
		
		var printContents = document.getElementById('print_analisys_div').innerHTML;
		mywindow.document.write(printContents);
		
		mywindow.document.write('</body></html>');
		
		mywindow.document.close();
		mywindow.focus();
		
		setTimeout(function(){
			mywindow.print(); 
			mywindow.close();
		},300);*/
		
		
		var contents = document.getElementById('print_analisys_div').innerHTML;
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
	function change_analysis(id){
		new_analysis(id);
	}
	
	
	// Диалог - удалить прием
	function delete_analysis_dialog(id){

		$('#delete_analysis').find('#analysisdelete_id').val('0');
		$('#delete_analysis').find('#analysisdelete_id').val(id);
		$('#delete_analysis').modal('show');

	}
	
	
	function delete_analysis(){
			
		var id = $('#delete_analysis').find('#analysisdelete_id').val();
		
		var token;
		token='{{ csrf_token() }}';
		
		$.ajax({
			headers: {'X-CSRF-TOKEN': token},
			url: '/patientcard/delete_analysis',
			method:'POST',
			dataType: 'json',
			data: {id: id},
			success: function(data) {
				
				if($.isEmptyObject(data.error)){

					toastr.info(data.success);					
					
					close_analysis();

					$('#delete_analysis').modal('toggle');					
					
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
	function clear_analysis_form() {
		
		if (($('#analisys_to_visit_filter').val() != 0 & $('#analisys_to_visit_filter').val() != null) | ($('#analisys_name_filter').val()!= 0 & $('#analisys_name_filter').val()!= null) | ($('#doctor_4_filter').val() != 0 & $('#doctor_4_filter').val() != null) | ($('#analisys_visit_date_start').val() != '') | ($('#analisys_visit_date_end').val() != '')) {
		
			needtorefresh = true;

			$('#analisys_form')[0].reset();		
			
			$('#analisys_visit_date_start').datepicker().data('datepicker').clear();
			
			$('#analisys_visit_date_end').datepicker().data('datepicker').clear();
			
			$('#analisys_name_filter').val('').trigger('change');
			
			$('#doctor_4_filter').val(0).trigger('change'); 
			
			$('#analisys_to_visit_filter').val(0).trigger('change');
			
			needtorefresh = false;
			
			show_analysis_list();
		
		}
		
	}
	
	
	
</script> 
