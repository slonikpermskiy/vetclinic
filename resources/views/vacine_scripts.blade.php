<script> 

	$(document).ready(function () {
		
	});
	
	
	
	// Обновление списка вакцинаций
	function show_vacine_list(){
						
		$('#vacine_list_response').empty();
		
		if($('#vacine_list_response').is(':empty') ||  !$.trim( $('#vacine_list_response').html()).length) {			
			$('#vacine_list_response').append("<div class='d-flex justify-content-center my-4'> <div class='spinner-border text-secondary' role='status'> <span class='visually-hidden'>Loading...</span> </div> </div>");			
		}
		
		var staff_id = {{ Auth::user()->staff_id }};
		
					
		setTimeout(function () {
			$.ajax({
				url: '/patientcard/get_vacines_list',				
				method:'GET',
				dataType:'json',
				data: '&anymal_id=' + {{ $patient_id }} + '&staff_id=' + staff_id,
				
				success: function(data) {
					
					$('#vacine_list_response').empty();
					
					if($.isEmptyObject(data.error)){
					
						$('#vacine_list_response').append(data.success);

					}else{
						
						$('#vacine_list_response').append('<div class="d-flex justify-content-center align-items-center"> <div class="d-block px-4 py-2">Нет вакцинаций</div></div>');

						toastr.error(data.error);
					}	
				},
					error: function(err) {
						
						$('#vacine_list_response').empty();

						$('#vacine_list_response').append('<div class="d-flex justify-content-center align-items-center"> <div class="d-block px-4 py-2">Нет вакцинаций</div></div>');
						
						toastr.error('Ошибка');
					}
				});
			}, 300);

	}
	

		
	// Изменить прием
	function change_vacine(id){
		new_vacine(id);
	}
	
	
	// Диалог - удалить прием
	function delete_vacine_dialog(id){

		$('#delete_vacine').find('#vacinedelete_id').val('0');
		$('#delete_vacine').find('#vacinedelete_id').val(id);
		$('#delete_vacine').modal('show');

	}
	
	
	function delete_vacine(){
			
		var id = $('#delete_vacine').find('#vacinedelete_id').val();
		
		var token;
		token='{{ csrf_token() }}';
		
		$.ajax({
			headers: {'X-CSRF-TOKEN': token},
			url: '/patientcard/delete_vacine',
			method:'POST',
			dataType: 'json',
			data: {id: id},
			success: function(data) {
				
				if($.isEmptyObject(data.error)){

					toastr.info(data.success);

					// Обновляем данные пациента-клиента на странице
					get_one_client_patient_data({{ $patient_id }});						
					
					// Обновляем список вакцинаций
					show_vacine_list();	

					$('#delete_vacine').modal('toggle');					
					
				}else{
					toastr.error(data.error);
				}
			},
				error: function(err) {	
					toastr.error('Ошибка');
				}
			});
	}	
	
</script> 