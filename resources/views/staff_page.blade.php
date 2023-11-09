@extends('layout')
@section('title', 'Сотрудники')
@section('content')


<script> 

	$(document).ready(function () {
		
		// Слушатель открытия модального окна
		document.getElementById('new_user').addEventListener('shown.bs.modal', function () {
			
			// Очистка всего
			$('#new_user').find('.error_response').html("&nbsp;");
			$('#new_user').find('#myForm')[0].reset();
			$('#new_user').find('#without_access_check').prop('checked', false);
			$('#new_user').find('#without_access_form').addClass('d-none');
			$('#new_user').find('#login_form').removeClass('d-none');
			$('#new_user').find('#position').val('0').trigger('change');
			
		});
			
				
		// Слушатели Toggle
		$('#new_user').find('#without_access_check').change(function() { 		
			if($('#new_user').find('#without_access_check').is(':checked')) { 
				$('#new_user').find('#without_access').val('1').trigger('change');  
				$('#new_user').find('#username').val('');
				$('#new_user').find('#password').val('');			
				$('#new_user').find('#password_confirmation').val('');
				$('#new_user').find('#login_form').addClass('d-none');
			} else {
				$('#new_user').find('#without_access').val('0').trigger('change');
				$('#new_user').find('#login_form').removeClass('d-none');
			}				
		}); 
		
		
		// Показываем всех пользователей
		show_users();
		
	});
	
	
	 // Показываем всех пользователей
	 function show_users(){ 
	 
		$.ajax({
			url: '/staff/showusers',
			method:'GET',
			dataType:'json',
			success: function(data) {
				
				if($.isEmptyObject(data.error)){
				
					$('#staff_response').html("");
					$('#staff_response').append(data.success);

				}else{
					toastr.error(data.error);
				}	
			},
				error: function(err) {	
					toastr.error('Ошибка');
				}
			});
	 }



	// Добавление сотрудника 
    function new_user(){ 
			
		// Очистка текстов-ошибок в span
		$('#new_user').find('.error_response').html("&nbsp;");
	
		// Construct data string
		var dataString = $("#myForm").serialize();
	
		// Записываем данные
		$.ajax({
		url: '/register',
		type: 'GET',
		data: dataString,
			
		success: function(data) {			
							
			if($.isEmptyObject(data.error)){
				
				toastr.info(data.success);
				
				$('#new_user').modal('toggle');
				
				show_users();
	
			}else{
				printErrorMsg(data.error, 'new_user');
			}
			},
		error: function(err) {	
		
			toastr.error('Ошибка');
					
			}
		});
				
	}
	
	
	// Получение ошибок
	function printErrorMsg (msg, modal) {
		
		if (msg.length > 0) {
			
		toastr.error(msg);
		
		} else {
		
		// Заполнение span текстами ошибок 
		$.each(msg,function(field_name,error){
			$('#'+modal).find('#error_'+field_name).html(error);
        })
		
		toastr.error('Ошибки заполнения полей');
		
		}

	}
	
	
	// Диалог - удалить сотрудника
	function delete_staff_dialog(staff_id) {
		
		var auth_staff_id = {{ Auth::user()->staff_id }};
		
		if (auth_staff_id == staff_id) {
			toastr.error('Вы не можете удалить свою учетную запись');
		} else {
			$('#delete_staff').find('input#delete_staff_id').val(staff_id);			
			$('#delete_staff').modal("show");			
		}
	}
	
	
	// Метод - удалить сотрудника
	function delete_staff() {
		
		var token;
		token='{{ csrf_token() }}';
		
		var staff_id = $('#delete_staff').find('input#delete_staff_id').val();
		
		$.ajax({
			headers: {'X-CSRF-TOKEN': token},
			url: '/staff/delete_user',
			method:'POST',
			dataType: 'json',
			data: {staff_id: staff_id},
			success: function(data) {
				
				if($.isEmptyObject(data.error)){					
					
					toastr.info(data.success);
					
					$('#delete_staff').modal('toggle');
					
					show_users();
					
				}else{
					toastr.error(data.error);
				}
			},
				error: function(err) {	
					toastr.error('Ошибка');
				}
			});	
		
	}
	


	// Изменить данные сотрудника
	function change_staff_data_dialog(staff_id) {
									
		$.ajax({
		url: '/staff/getworkerdata',
		method:'GET',
		dataType:'json',
		data: {query:staff_id},
		success: function(data) {
			
			if($.isEmptyObject(data.error)){
				
				// Очистка текстов-ошибок в span
				$('#change_user').find('.error_response').html("&nbsp;");
								
				// Очистка всего
				$('#change_user').find('#myForm2')[0].reset();
				$('#change_user').find('#position').val('0').trigger('change');
				
				$('#change_user').find('input#change_user_id').val(staff_id);
									
				if (data.success.last_name) {
					$('#change_user').find('input#last_name_ch').val(data.success.last_name);
				}
				
				if (data.success.first_name) {
					$('#change_user').find('input#first_name_ch').val(data.success.first_name);
				}
				
				if (data.success.middle_name) {
					$('#change_user').find('input#middle_name_ch').val(data.success.middle_name);
				}
				
				
				if (data.user) {
					$('#change_user').find("#position_ch>option[value='1']").removeAttr('disabled');
				} else {
					$('#change_user').find("#position_ch>option[value='1']").attr('disabled','disabled');
				}
							
				$('#change_user').find('#position_ch').val(data.success.position).trigger('change');
					
				$('#change_user').modal('show');
		
			}else{
				
			}
			
		},
			error: function(err) {	
				
			}
		});
	
	}


	
	 function change_staff_data(){
		 
		// Очистка текстов-ошибок в span
		$('#change_user').find('.error_response').html("&nbsp;");
	
		// Construct data string
		var dataString = $("#myForm2").serialize();
	
		// Записываем данные
		$.ajax({
		url: '/staff/change_user',
		type: 'POST',
		data: dataString,
			
		success: function(data) {

			if($.isEmptyObject(data.error)){
				
				toastr.info(data.success);
				
				$('#change_user').modal('toggle');
				
				show_users();
				
				// Сменить данные в шапке сайта
				var auth_staff_id = {{ Auth::user()->staff_id }};
				var changed_staff_id = $('#change_user').find('input#change_user_id').val();

				if (auth_staff_id == changed_staff_id) {
					$('#logout_link').click();				
				} 
				
			}else{
				printErrorMsg(data.error, 'change_user');
			}
			},
		error: function(err) {	
		
			toastr.error('Ошибка');
					
			}
		});
		 
	 }
	 
	 
	 
	 // Изменить логин-пароль
	function change_login_password_dialog(staff_id) {
									
		$.ajax({
		url: '/staff/getworkerdata',
		method:'GET',
		dataType:'json',
		data: {query:staff_id},
		success: function(data) {
			
			if($.isEmptyObject(data.error)){
				
				// Очистка текстов-ошибок в span
				$('#change_login_password').find('.error_response').html("&nbsp;");
								
				// Очистка всего
				$('#change_login_password').find('#myForm3')[0].reset();
				
				$('#change_login_password').find('input#change_login_password_user_id').val(staff_id);
				
				
				
				if (data.user) {
					$('#change_login_password').find('input#username_ch').val(data.user.username);
					$('#change_login_password').find('#title').html('Изменить логин и пароль');
				} else {
					$('#change_login_password').find('#title').html('Введите логин и пароль');
				}
				
				$('#change_login_password').modal('show');
		
			}else{
				
			}
			
		},
			error: function(err) {	
				
			}
		});
	
	}
	
	
	
	function change_login_password_data(){
		
		// Очистка текстов-ошибок в span
		$('#change_login_password').find('.error_response').html("&nbsp;");
	
		// Construct data string
		var dataString = $("#myForm3").serialize();
	
		// Записываем данные
		$.ajax({
		url: '/staff/change_login_password',
		type: 'POST',
		data: dataString,
			
		success: function(data) {
							
			if($.isEmptyObject(data.error)){
				
				toastr.info(data.success);
				
				$('#change_login_password').modal('toggle');
				
				show_users();
				
				// Если администратор сменил пароль у себя, то logout
				var auth_staff_id = {{ Auth::user()->staff_id }};
				var changed_staff_id = $('#change_login_password').find('input#change_login_password_user_id').val();

				if (auth_staff_id == changed_staff_id) {
					$('#logout_link').click();				
				} 

			}else{
				printErrorMsg(data.error, 'change_login_password');
			}
			},
		error: function(err) {	
		
			toastr.error('Ошибка');
					
			}
		});
		 
	 }
	 	
	
	
	// Диалог - запретить доступ
	function deny_access_dialog(staff_id) {
		
		$('#deny_access').find('input#deny_access_staff_id').val(staff_id);	
		$('#deny_access').modal("show");	
		
	}
	
	
	// Диалог - запретить доступ
	function deny_access() {

		var token;
		token='{{ csrf_token() }}';
		
		var staff_id = $('#deny_access').find('input#deny_access_staff_id').val();
		
		$.ajax({
			headers: {'X-CSRF-TOKEN': token},
			url: '/staff/deny_access',
			method:'POST',
			dataType: 'json',
			data: {staff_id: staff_id},
			success: function(data) {
				
				if($.isEmptyObject(data.error)){					
					
					toastr.info(data.success);
					
					$('#deny_access').modal('toggle');
					
					show_users();
					
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

<style>

.form-group label {
    color: #3182CE !important;
	font-weight: 600;
}

.underinput-error{
	font-size:14px;
	font-weight: 600;
}

.custom-control-input {
  transform: scale(1.4);
}

.toggle-label{
    margin-left: 15px;
	font-weight: 600;
}


</style>


<div name="central_area" id="central_area" class="d-none border bg-white shadow rounded p-4">	
		
		
	<div class="d-flex justify-content-end align-items-top">
		<button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#new_user">Создать</button>		
	</div>	
	
	<div name="staff_response" id="staff_response" class='w-full min-h-full items-center justify-between mt-4 mb-1'>
		
	</div>
	


	<!-- Новый пользователь -->
	<div class="modal hide mycontainer" id="new_user" role="dialog" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false" style="display: none">
		<div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title px-3" id="staticBackdropLabel">Новый пользователь</h5>
					<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
				</div>
				<div class="modal-body">
					
					<div class="px-4">
					
					<form class="mb-0" id="myForm" autocomplete="off">					
					@csrf
					
					
						<div class="row">
							<div class="col-lg-4 px-2 mb-2">
								<div class="form-group">
									<label class='control-label font-weight-bold' for="last_name">Фамилия <span class="text-danger">*</span></label>
									<input name="last_name" class='form-control form-control-lg' id='last_name' type='text' placeholder='Фамилия' />
									<span name="error_last_name" id='error_last_name' class="error_response underinput-error text-danger d-flex justify-content-left align-items-lg-center">
									&nbsp;
									</span>
								</div>					
							</div>
							
							<div class="col-lg-4 px-2 mb-2">		
								<div class="form-group">
									<label class='control-label font-weight-bold' for="first_name">Имя <span class="text-danger">*</span></label>
									<input name="first_name" class='form-control form-control-lg' id='first_name' type='text' placeholder='Имя' />
											
									<span name="error_first_name" id='error_first_name' class="error_response underinput-error text-danger d-flex justify-content-left align-items-lg-center">
									&nbsp;
									</span>
								</div>			
							</div>
										
							<div class="col-lg-4 px-2 mb-2">
								<div class="form-group">
									<label class='control-label font-weight-bold' for="middle_name">Отчество <span class="text-danger"></span></label>
									<input name="middle_name" class='form-control form-control-lg' id='middle_name' type='text' placeholder='Отчество' />
											
									<span name="error_middle_name" id='error_middle_name' class="error_response underinput-error text-danger d-flex justify-content-left align-items-lg-center">
									&nbsp;
									</span>
								</div>							
							</div>
						</div>	
						
						<div class="row">
				
							<div class="col-lg-6 px-2 mb-2">
								<div class="form-group">
								
									<label class='control-label font-weight-bold' for="position">Должность (роль) <span class="text-danger">*</span></label>		
									<select name="position" id="position" class="form-select-lg js-example-basic-hide" style="width: 100%;" placeholder="Не выбрано" data-search="true">
										<option></option>
										<option value="1">Администратор</option>
										<option value="2">Врач</option>
										<option value="3">Ассистент</option>				
									</select>
									<span name="error_position" id='error_position' class="error_response underinput-error text-danger d-flex justify-content-left align-items-lg-center">
									&nbsp;
									</span>	

								</div>							
							</div>	
								
							<div name="without_access_form" id="without_access_form" class="d-none col-lg-6 px-3 mb-4 d-flex justify-content-lg-center align-items-lg-center">
								
								<div class="form-check form-switch d-flex justify-content-lg-center align-items-lg-center">
								
									<input class="form-check-input custom-control-input" type="checkbox" id="without_access_check" name="without_access_check">
									<label class="form-check-label toggle-label" for="without_access_check">Запретить доступ в систему</label>

									<input name="without_access" id='without_access' value="0" hidden="true"/>
								</div>
								
							</div>
							
						</div>	

						<div name="login_form" id="login_form" class="row">
						
						
							<div class="col-lg-4 px-2 mb-2">
								<div class="form-group">
									<label class='control-label font-weight-bold' for="username">Логин <span class="text-danger">*</span></label>
									<input name="username" class='form-control form-control-lg' id='username' type='text' placeholder='Логин' />

									<span name="error_username" id='error_username' class="error_response underinput-error text-danger d-flex justify-content-left align-items-lg-center">
									&nbsp;
									</span>
								</div>					
							</div>
										
							<div class="col-lg-4 px-2 mb-2">		
								<div class="form-group">
									<label class='control-label font-weight-bold' for="password">Пароль <span class="text-danger">*</span></label>
									<input name="password" type="password" class='form-control form-control-lg' id='password' type='text' placeholder='Пароль' required />

											
									<span name="error_password" id='error_password' class="error_response underinput-error text-danger d-flex justify-content-left align-items-lg-center">
									&nbsp;
									</span>
								</div>			
							</div>
										
							<div class="col-lg-4 px-2 mb-lg-2">
								<div class="form-group">
									<label class='control-label font-weight-bold' for="password_confirmation">Подтверждение пароля <span class="text-danger">*</span></label>
									<input name="password_confirmation" type="password"  class='form-control form-control-lg' id='password_confirmation' type='text' placeholder='Подтверждение пароля' required autocomplete="new-password" />
											
									<span name="error_password_confirmation" id='error_password_confirmation' class="error_response underinput-error text-danger d-flex justify-content-left align-items-lg-center">
									&nbsp;
									</span>
								</div>							
							</div>
							
							
						</div>	

					</form>

					</div>
									
				</div>
				<div class="modal-footer pe-4">
					<button type="button" class="btn btn-primary" onclick="new_user()">Сохранить</button>		
				</div>
			</div>
		</div>
	</div>


	<!-- Удалить сотрудника -->
	<div class="modal hide mycontainer" id="delete_staff" role="dialog" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false" style="display: none">
		<div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title px-3" id="staticBackdropLabel">Удалить Сотрудника</h5>
					<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
				</div>
				<div class="modal-body">
					
					<div class="px-3">
					
						<input name="delete_staff_id" id="delete_staff_id" value="0" hidden="true"/>
					
						<div class="text-break">Вы действительно хотите удалить сотрудника ?</div>

					</div>
									
				</div>
				<div class="modal-footer pe-4">
					<button type="button" class="btn btn-danger" onclick="delete_staff()">Удалить</button>		
				</div>
			</div>
		</div>
	</div>


	<!-- Изменить данные пользователя -->
	<div class="modal hide mycontainer" id="change_user" role="dialog" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false" style="display: none">
		<div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title px-3" id="staticBackdropLabel">Изменить данные пользователя</h5>
					<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
				</div>
				<div class="modal-body">
					
					<div class="px-4">
					
					<form class="mb-0" id="myForm2" autocomplete="off">					
					@csrf
					
						<input name="change_user_id" id="change_user_id" value="0" hidden="true"/>
					
						<div class="row">
							<div class="col-lg-4 px-2 mb-2">
								<div class="form-group">
									<label class='control-label font-weight-bold' for="last_name_ch">Фамилия <span class="text-danger">*</span></label>
									<input name="last_name" class='form-control form-control-lg' id='last_name_ch' type='text' placeholder='Фамилия' />
									<span name="error_last_name" id='error_last_name' class="error_response underinput-error text-danger d-flex justify-content-left align-items-lg-center">
									&nbsp;
									</span>
								</div>					
							</div>
							
							<div class="col-lg-4 px-2 mb-2">		
								<div class="form-group">
									<label class='control-label font-weight-bold' for="first_name_ch">Имя <span class="text-danger">*</span></label>
									<input name="first_name" class='form-control form-control-lg' id='first_name_ch' type='text' placeholder='Имя' />
											
									<span name="error_first_name" id='error_first_name' class="error_response underinput-error text-danger d-flex justify-content-left align-items-lg-center">
									&nbsp;
									</span>
								</div>			
							</div>
										
							<div class="col-lg-4 px-2 mb-2">
								<div class="form-group">
									<label class='control-label font-weight-bold' for="middle_name_ch">Отчество <span class="text-danger"></span></label>
									<input name="middle_name" class='form-control form-control-lg' id='middle_name_ch' type='text' placeholder='Отчество' />
											
									<span name="error_middle_name" id='error_middle_name' class="error_response underinput-error text-danger d-flex justify-content-left align-items-lg-center">
									&nbsp;
									</span>
								</div>							
							</div>
						</div>	
						
						<div class="row">
				
							<div class="col-lg-6 px-2 mb-2">
								<div class="form-group">
								
									<label class='control-label font-weight-bold' for="position_ch">Должность (роль) <span class="text-danger">*</span></label>		
									<select name="position" id="position_ch" class="form-select-lg js-example-basic-hide" style="width: 100%;" placeholder="Не выбрано" data-search="true">
										<option></option>
										<option value="1">Администратор</option>
										<option value="2">Врач</option>
										<option value="3">Ассистент</option>				
									</select>
									<span name="error_position" id='error_position' class="error_response underinput-error text-danger d-flex justify-content-left align-items-lg-center">
									&nbsp;
									</span>	

								</div>							
							</div>	
							
						</div>	


					</form>

					</div>
									
				</div>
				<div class="modal-footer pe-4">
					<button type="button" class="btn btn-primary" onclick="change_staff_data()">Сохранить</button>		
				</div>
			</div>
		</div>
	</div>


	<!-- Сменить логин-пароль -->
	<div class="modal hide mycontainer" id="change_login_password" role="dialog" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false" style="display: none">
		<div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
			<div class="modal-content">
				<div class="modal-header">
					<h5 name="title" id="title" class="modal-title px-3" id="staticBackdropLabel">Изменить логин и пароль</h5>
					<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
				</div>
				<div class="modal-body">
					
					<div class="px-4">
					
					<form class="mb-0" id="myForm3" autocomplete="off">					
					@csrf
					
						<input name="change_login_password_user_id" id="change_login_password_user_id" value="0" hidden="true"/>
					
						<div name="login_form" id="login_form" class="row">
						
						
							<div class="col-lg-4 px-2 mb-2">
								<div class="form-group">
									<label class='control-label font-weight-bold' for="username_ch">Логин <span class="text-danger">*</span></label>
									<input name="username" class='form-control form-control-lg' id='username_ch' type='text' placeholder='Логин' />

									<span name="error_username" id='error_username' class="error_response underinput-error text-danger d-flex justify-content-left align-items-lg-center">
									&nbsp;
									</span>
								</div>					
							</div>
										
							<div class="col-lg-4 px-2 mb-2">		
								<div class="form-group">
									<label class='control-label font-weight-bold' for="password_ch">Пароль <span class="text-danger">*</span></label>
									<input name="password" type="password" class='form-control form-control-lg' id='password_ch' type='text' placeholder='Пароль' required />
											
									<span name="error_password" id='error_password' class="error_response underinput-error text-danger d-flex justify-content-left align-items-lg-center">
									&nbsp;
									</span>
								</div>			
							</div>
										
							<div class="col-lg-4 px-2 mb-lg-2">
								<div class="form-group">
									<label class='control-label font-weight-bold' for="password_confirmation_ch">Подтверждение пароля <span class="text-danger">*</span></label>
									<input name="password_confirmation" type="password"  class='form-control form-control-lg' id='password_confirmation_ch' type='text' placeholder='Подтверждение пароля' required autocomplete="new-password" />
											
									<span name="error_password_confirmation" id='error_password_confirmation' class="error_response underinput-error text-danger d-flex justify-content-left align-items-lg-center">
									&nbsp;
									</span>
								</div>							
							</div>		
						</div>	
					</form>

					</div>
									
				</div>
				<div class="modal-footer pe-4">
					<button type="button" class="btn btn-primary" onclick="change_login_password_data()">Сохранить</button>		
				</div>
			</div>
		</div>
	</div>


	<!-- Запретить доступ -->
	<div class="modal hide mycontainer" id="deny_access" role="dialog" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false" style="display: none">
		<div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title px-3" id="staticBackdropLabel">Запретить доступ</h5>
					<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
				</div>
				<div class="modal-body">
					
					<div class="px-3">
					
						<input name="staff_id" id="deny_access_staff_id" value="0" hidden="true"/>
					
						<div class="text-break">Вы действительно хотите запретить доступ ?</div>

					</div>
									
				</div>
				<div class="modal-footer pe-4">
					<button type="button" class="btn btn-danger" onclick="deny_access()">Запретить</button>		
				</div>
			</div>
		</div>
	</div>
	
	<script>
	
		// Закрытие всех открытых popup при начале скрола страницы
		$(document).scroll(function() {
			
			var cusid_ele = document.getElementsByClassName('dropdown-menu');
			for (var i = 0; i < cusid_ele.length; ++i) {

				if ($('.dropdown-'+i).is(":visible")){
			
					$('.dropdown-'+i).dropdown('toggle');
				}
			}			
		});
		
			
		// Без поиска
		$(".js-example-basic-hide").select2({
			placeholder: "Выберите значение",
			theme: "bootstrap-5",
			allowClear: true,
			minimumResultsForSearch: Infinity
		}).on('select2:unselecting', function() {
			$(this).data('unselecting', true);
			$('#new_user').find('#without_access_form').addClass('d-none');
			$('#new_user').find('#without_access_check').prop('checked', false);
			$('#new_user').find('#without_access').val('0').trigger('change');
			$('#new_user').find('#login_form').removeClass('d-none');		
		}).on('select2:opening', function(e) {
			if ($(this).data('unselecting')) {
				$(this).removeData('unselecting');
				e.preventDefault();
			}
		});	
		
		
		// Слушатель выбора должности
		$('#new_user').find('#position').on("select2:select", function(e) { 
			if ($('#new_user').find('#position').val() !== '1') { 
				$('#new_user').find('#without_access_form').removeClass('d-none');
			} else {
				$('#new_user').find('#without_access_form').addClass('d-none');
				$('#new_user').find('#without_access_check').prop('checked', false);
				$('#new_user').find('#without_access').val('0').trigger('change');
				$('#new_user').find('#login_form').removeClass('d-none');
			}		
		});
	
	</script>
		
</div>

@endsection