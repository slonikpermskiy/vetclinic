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
				
	<div class="col-lg-4 px-2 mb-2">
		<div class="form-group">
			<label class='control-label font-weight-bold' for="city">Населенный пункт <span class="text-danger"></span></label>
			<input name="city" class='form-control form-control-lg' id='city' type='text' placeholder='Населенный пункт' />
						
			<span name="error_city" id='error_city' class="error_response underinput-error text-danger d-flex justify-content-left align-items-lg-center">
			&nbsp;
			</span>

		</div>							
	</div>
		
	<div class="col-lg-8 px-2 mb-2">
		<div class="form-group">		
			<label class='control-label font-weight-bold' for="address">Адрес <span class="text-danger"></span></label>
			<input name="address" class='form-control form-control-lg' id='address' type='text' placeholder='Адрес' />
						
			<span name="error_address" id='error_address' class="error_response underinput-error text-danger d-flex justify-content-left align-items-lg-center">
			&nbsp;
			</span>	

		</div>							
	</div>
	
</div>	

<div class="row">
				
	<div class="col-lg-4 px-2 mb-2">
		<div class="form-group">
		
			<label class='control-label font-weight-bold' for="phone">Основной телефон <span class="text-danger"></span></label>
			<input name="phone" class='phone form-control form-control-lg' id='phone' type='text' placeholder='Телефон основной' />
					
			<span name="error_phone" id='error_phone' class="error_response underinput-error text-danger d-flex justify-content-left align-items-lg-center">
			&nbsp;
			</span>

		</div>							
	</div>
		
	<div class="col-lg-8 px-2 mb-2">
		<div class="form-group">

			<label class='control-label font-weight-bold' for="phoneinfo">Комментарий <span class="text-danger"></span></label>
			<input name="phoneinfo" class='form-control form-control-lg' id='phoneinfo' type='text' placeholder='Комментарий к основному телефону' />
					
			<span name="error_phoneinfo" id='error_phoneinfo' class="error_response underinput-error text-danger d-flex justify-content-left align-items-lg-center">
			&nbsp;
			</span>

		</div>							
	</div>
	
</div>	


<div class="row">
				
	<div class="col-lg-4 px-2 mb-2">
		<div class="form-group">
		
			<label class='control-label font-weight-bold' for="phone2">Дополнительный телефон <span class="text-danger"></span></label>
			<input name="phone2" class='phone form-control form-control-lg' id='phone2' type='text' placeholder='Телефон дополнительный' />
					
			<span name="error_phone2" id='error_phone2' class="error_response underinput-error text-danger d-flex justify-content-left align-items-lg-center">
			&nbsp;
			</span>

		</div>							
	</div>
		
	<div class="col-lg-8 px-2 mb-2">
		<div class="form-group">
		
			<label class='control-label font-weight-bold' for="phoneinfo2">Комментарий <span class="text-danger"></span></label>
			<input name="phoneinfo2" class='form-control form-control-lg' id='phoneinfo2' type='text' placeholder='Комментарий к дополнительному телефону' />
					
			<span name="error_phoneinfo2" id='error_phoneinfo2' class="error_response underinput-error text-danger d-flex justify-content-left align-items-lg-center">
			&nbsp;
			</span>

		</div>							
	</div>
	
</div>

<div class="row">
				
	<div class="col-lg-6 px-2 mb-2">
		<div class="form-group">
		
			<label class='control-label font-weight-bold' for="email">Email <span class="text-danger"></span></label>
			<input name="email" class='form-control form-control-lg' id='email' type='text' placeholder='Email' />
					
			<span name="error_email" id='error_email' class="error_response underinput-error text-danger d-flex justify-content-left align-items-lg-center">
			&nbsp;
			</span>

		</div>							
	</div>
		
	<div class="col-lg-6 px-3 mb-4 d-flex justify-content-lg-center align-items-lg-center">
		
		<div class="form-check form-switch d-flex justify-content-lg-center align-items-lg-center">
		
			<input class="form-check-input custom-control-input" type="checkbox" id="data_ready_check" name="data_ready_check">
			<label class="form-check-label toggle-label" for="data_ready_check">Согласен на обработку персональных данных</label>

			<input name="data_ready" id='data_ready' value="0" hidden="true"/>
		</div>
		
	</div>
	
</div>
	
<div class="row">
				
	<div class="col-lg px-2">
		<div class="form-group">				
			<label class='control-label font-weight-bold' for="comments">Комментарии <span class="text-danger"></span></label>
			<textarea name="comments" class="form-control" id='comments' rows="3"></textarea>
			<span name="error_comments" id='error_comments' class="error_response underinput-error text-danger d-flex justify-content-left align-items-lg-center">
			&nbsp;
			</span>
		</div>							
	</div>
		
</div>