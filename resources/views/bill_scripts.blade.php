<script> 

	var needtorefresh = false;

	$(document).ready(function () {
		
		// Инициализация календарей
		var myDataPicker = $('#bill_date_start').datepicker({ 
			language : 'ru', 
			autoClose : true,
			clearButton : true,
			onSelect: function (dateText, inst) {
				if (!needtorefresh) {
					show_bill_list();
				}
			}
			
		}).data('datepicker');
		
		var myDataPicker = $('#bill_date_end').datepicker({ 
			language : 'ru', 
			autoClose : true,
			clearButton : true,
			onSelect: function (dateText, inst) {
				if (!needtorefresh) {
					show_bill_list();
				}
			}
		}).data('datepicker');
		
		
		$('#paied_or_not').on("select2:select", function(e) { if (!needtorefresh) { show_bill_list(); } });
		
		$('#paied_or_not').on("select2:unselect", function (e) { if (!needtorefresh) { show_bill_list(); } });
		
		$('#animal_choose_filter').on("select2:select", function(e) { if (!needtorefresh) { show_bill_list(); } });
		
		$('#animal_choose_filter').on("select2:unselect", function (e) { if (!needtorefresh) { show_bill_list(); } });
		
		$('#client_id').on("select2:select", function(e) { if (!needtorefresh) { show_bill_list(); } });
		
		$('#client_id').on("select2:unselect", function (e) { if (!needtorefresh) { show_bill_list(); } });
		
		$('#naimedornot').on("select2:select", function(e) { if (!needtorefresh) { show_bill_list(); } });
		
		$('#naimedornot').on("select2:unselect", function (e) { if (!needtorefresh) { show_bill_list(); } });

		$('#product_filter').on("select2:select", function(e) { if (!needtorefresh) { show_bill_list(); } });
		
		$('#product_filter').on("select2:unselect", function (e) { if (!needtorefresh) { show_bill_list(); } });
				
		$('#service_filter').on("select2:select", function(e) { if (!needtorefresh) { show_bill_list(); } });
		
		$('#service_filter').on("select2:unselect", function (e) { if (!needtorefresh) { show_bill_list(); } });

	});
	
	
	
	// Слушатели кнопок календарик
	function calendarinputBDS(){  	
		$("#bill_date_start").trigger("focus");
	}

	function calendarinputBDE(){  	
		$("#bill_date_end").trigger("focus");
		
	}
	
	
	// Обновление списка счетов
	function show_bill_list(){
				
		var BillsForm = $("#bills_form").serialize();
		
		
		var product_selected = '';
		
		var product_select = $('#product_filter').select2('data');
		
		if (product_select[0] != null && product_select[0].text != 0) {
			product_selected = product_select[0].text;
		}
		
		
		var service_selected = '';
		
		var service_select = $('#service_filter').select2('data');
		
		if (service_select[0] != null && service_select[0].text != 0) {
			service_selected = service_select[0].text;
		}
		
		
		var bill_paied = '';
		
		var bill_paied =  $('#paied_or_not').val();		
		
		var bill_page_anymal_id = '';
		
		var bill_page_anymal_id = $('#animal_choose_filter').val();	

		var client_id = '';
		
		var client_id = $('#client_id').val();

		var naimedornot = '';
		
		var naimedornot = $('#naimedornot').val();	
		
		
		var staff_id = {{ Auth::user()->staff_id }};
		

		if ({{ $patient_id or 'not-exist' }}) {	
		
			if ({{ $bill_page }} == 1) {
				BillsForm = BillsForm + '&anymal_id=' + bill_page_anymal_id + '&client_id=' + client_id + '&naimedornot=' + naimedornot + '&product_selected=' + product_selected + '&service_selected=' + service_selected + '&bill_paied=' + bill_paied + '&bill_page=' + {{ $bill_page }} + '&staff_id=' + staff_id + ""; 					
			} else  {
				BillsForm = BillsForm + '&anymal_id=' + {{ $patient_id }} + '&product_selected=' + product_selected + '&service_selected=' + service_selected + '&bill_paied=' + bill_paied + '&bill_page=' + {{ $bill_page }} + '&staff_id=' + staff_id + ""; 			
			}
			
		} 
		

		$('#bills_list_response').empty();
		
		if($('#bills_list_response').is(':empty') ||  !$.trim( $('#bills_list_response').html()).length) {			
			$('#bills_list_response').append("<div class='d-flex justify-content-center my-4'> <div class='spinner-border text-secondary' role='status'> <span class='visually-hidden'>Loading...</span> </div> </div>");			
		}
		
					
		setTimeout(function () {
			$.ajax({
				url: '/patientcard/get_bills_list',				
				method:'GET',
				dataType:'json',
				data: BillsForm,
				
				success: function(data) {
					
					$('#bills_list_response').empty();
					
					if($.isEmptyObject(data.error)){
					
						$('#bills_list_response').append(data.success);

					}else{
						
						$('#bills_list_response').append('<div class="d-flex justify-content-center align-items-center"> <div class="d-block px-4 py-2">Нет счетов</div></div>');

						toastr.error(data.error);
					}	
				},
					error: function(err) {
						
						$('#bills_list_response').empty();

						$('#bills_list_response').append('<div class="d-flex justify-content-center align-items-center"> <div class="d-block px-4 py-2">Нет счетов</div></div>');
						
						toastr.error('Ошибка');
					}
				});
			}, 300);

	}


	
	// Отображаем данные счета
	function open_bill(id){
				
		billopened = id;
		
		$('#one_bill').empty();
		
		$('#print_bill_div').empty();

		var staff_id = {{ Auth::user()->staff_id }};
		
		$.ajax({
				url: '/patientcard/get_one_bill',
				method:'GET',
				dataType:'json',
				data: {bill_id: id, staff_id: staff_id},
				success: function(data) {
					
					$('#one_bill').empty();
					
					$('#print_bill_div').empty();
					
					if($.isEmptyObject(data.error)){
					
						$('#one_bill').append(data.success);
						
						$('#print_bill_div').append(data.successprint);
						
					}else{
						toastr.error(data.error);
					}	
				},
					error: function(err) {

						$('#one_bill').empty();
						
						$('#print_bill_div').empty();
						
						toastr.error('Ошибка');
					}
				});

		
		$("#bills_search_list").fadeOut(0);
		$("#one_bill").fadeIn(300);
	}
	
	
	
	// Возврат к списку счетов
	function close_bill(){ 
	
		billopened = '';
		
		// Обновляем список счетов
		show_bill_list();
	
		$("#one_bill").fadeOut(0);
		$("#bills_search_list").fadeIn(300);
	}
	
	
	// Печать счета
	function print_bill() {

		// В отдельном окне
		/*var mywindow = window.open('', 'Форма печати счета', 'height=600,width=900');
		
		mywindow.document.write('<html><head><title></title>');
		mywindow.document.write('<style> @page {size: 210mm 297mm; margin: 1cm;} </style>');			
		mywindow.document.write('<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-BmbxuPwQa2lc/FVzBcNJ7UAyJxM6wuqIj61tLrc4wSX0szH/Ev+nYRRuWlolflfl" crossorigin="anonymous">');
		mywindow.document.write('<link href="https://fonts.googleapis.com/css?family=Titillium+Web:400,400i,600,600i,700&display=swap" rel="stylesheet">');
		mywindow.document.write('</head><body >');
		
		var printContents = document.getElementById('print_bill_div').innerHTML;
		mywindow.document.write(printContents);
		
		mywindow.document.write('</body></html>');
		
		mywindow.document.close();
		mywindow.focus();
		
		setTimeout(function(){
			mywindow.print(); 
			mywindow.close();
		},300);*/
		
		
		var contents = document.getElementById('print_bill_div').innerHTML;
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
	
	
	// Изменить счет
	function change_bill(id){
		new_bill(id);
	}
	
	
	// Диалог - удалить счет
	function delete_bill_dialog(id){

		$('#delete_bill').find('#billdelete_id').val('0');
		$('#delete_bill').find('#billdelete_id').val(id);
		$('#delete_bill').modal('show');

	}
	
	
	function delete_bill(){
			
		var id = $('#delete_bill').find('#billdelete_id').val();
		
		var token;
		token='{{ csrf_token() }}';
		
		$.ajax({
			headers: {'X-CSRF-TOKEN': token},
			url: '/patientcard/delete_bill',
			method:'POST',
			dataType: 'json',
			data: {id: id},
			success: function(data) {
				
				if($.isEmptyObject(data.error)){

					toastr.info(data.success);					
					
					close_bill();

					$('#delete_bill').modal('toggle');					
					
				}else{
					toastr.error(data.error);
				}
			},
				error: function(err) {	
					toastr.error('Ошибка');
				}
			});
	}
	
	
	// Открытие диалога оплаты всех счетов
	function open_pay_selected_dialog (notpaied_bills) {
		
		$('#pay_all_bill').find('#pay_bills_id').val('0');
		$('#pay_all_bill').find('#pay_bills_id').val(JSON.stringify(notpaied_bills));
		$('#pay_all_bill').modal('show');
		
	}
	
	
	// Оплатить счета
	function pay_selected_bills(){
			
		var token;
		token='{{ csrf_token() }}';
		
		$.ajax({
			headers: {'X-CSRF-TOKEN': token},
			url: '/patientcard/pay_all_bills',
			method:'POST',
			dataType: 'json',
			data: {bills: $('#pay_all_bill').find('#pay_bills_id').val()},
			success: function(data) {
				
				if($.isEmptyObject(data.error)){

					toastr.info(data.success);					
					
					// Обновляем список визитов
					show_bill_list();

					$('#pay_all_bill').modal('toggle');					
					
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
	function clear_bills_form() {

		if (($('#animal_choose_filter').val() != 0 & $('#animal_choose_filter').val() != null) | ($('#client_id').val() != 0 & $('#client_id').val() != null) | ($('#naimedornot').val() != 0 & $('#naimedornot').val() != null) | ($('#paied_or_not').val() != 0 & $('#paied_or_not').val() != null) | ($('#service_filter').val()!= 0 & $('#service_filter').val()!= null) | ($('#product_filter').val() != 0 & $('#product_filter').val() != null) | ($('#bill_date_start').val() != '') | ($('#bill_date_end').val() != '')) {
	
			needtorefresh = true;
			
			$('#bill_date_start').datepicker().data('datepicker').clear();
			
			$('#bill_date_end').datepicker().data('datepicker').clear();
			
			$('#animal_choose_filter').val(0).trigger('change');
			
			$('#client_id').val(0).trigger('change');
			
			$('#naimedornot').val(0).trigger('change');

			$('#product_filter').val(0).trigger('change'); 
			
			$('#service_filter').val(0).trigger('change'); 
			
			$('#paied_or_not').val(0).trigger('change');
			
			$('#bills_form')[0].reset();

			needtorefresh = false;		
			
			show_bill_list();
		
		}
		
	}
	
	
</script> 
