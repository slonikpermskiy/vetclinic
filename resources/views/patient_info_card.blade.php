<div class="row">					
	<div class="col-lg-4 px-2 mb-2">
		<div class="form-group">
		
			<label class='control-label font-weight-bold' for="short_name">Кличка <span class="text-danger">*</span></label>
			<input name="short_name" class='form-control form-control-lg' id='short_name' type='text' placeholder='Кличка' />
										
			<span name="error_short_name" id='error_short_name' class="error_response underinput-error text-danger d-flex justify-content-left align-items-lg-center">
			&nbsp;
			</span>

		</div>							
	</div>
		
	<div class="col-lg-8 px-2 mb-2">
		<div class="form-group">
		
			<label class='control-label font-weight-bold' for="full_name">Полное имя <span class="text-danger"></span></label>
			<input name="full_name" class='form-control form-control-lg' id='full_name' type='text' placeholder='Полное имя'>
					
			<span name="error_full_name" id='error_full_name' class="error_response underinput-error text-danger d-flex justify-content-left align-items-lg-center">
			&nbsp;
			</span>

		</div>							
	</div>
	
</div>	

<div class="row">
				
	<div class="col-lg-4 px-2 mb-2">
		<div class="form-group">
			
			<label class='control-label font-weight-bold' for="animal_type_id">Вид <span class="text-danger">*</span></label>		
			<select name="animal_type_id" id="animal_type_id" class="form-select-lg js-example-basic-single" style="width: 100%;" placeholder="Не выбрано" data-search="true">
				<option></option>
			</select>				
			<span name="error_animal_type_id" id='error_animal_type_id' class="error_response underinput-error text-danger d-flex justify-content-left align-items-lg-center">
			&nbsp;
			</span>

		</div>							
	</div>
		
	<div class="col-lg-4 px-2 mb-2">
		<div class="form-group">
		
			<label class='control-label font-weight-bold' for="breed_id">Порода <span class="text-danger">*</span></label>									
			<select name="breed_id" id="breed_id" class="form-select-lg js-example-basic-single block" style="width: 100%;" placeholder="Не выбрано" data-search="true">
				<option></option>
			</select>
			<span name="error_breed_id" id='error_breed_id' class="error_response underinput-error text-danger d-flex justify-content-left align-items-lg-center">
			&nbsp;
			</span>	
			
		</div>							
	</div>
	
	<div class="col-lg-4 px-2 mb-2">
		<div class="form-group">
		
			<label class='control-label font-weight-bold' for="color_id">Окрас <span class="text-danger"></span></label>		
			<select name="color_id" id="color_id" class="form-select-lg js-example-basic-single block" style="width: 100%;" placeholder="Не выбрано" data-search="true">
				<option></option>
			</select>	
			<span name="error_color_id" id='error_color_id' class="error_response underinput-error text-danger d-flex justify-content-left align-items-lg-center">
			&nbsp;
			</span>	

		</div>							
	</div>	
</div>	

<div class="row">

	<div class="col-lg-3 px-2 mb-2">
		<div class="form-group">
		
			<label class='control-label font-weight-bold' for="sex_id">Пол <span class="text-danger"></span></label>		
			<select name="sex_id" id="sex_id" class="form-select-lg js-example-basic-hide" style="width: 100%;" placeholder="Не выбрано" data-search="true">
				<option></option>
				<option value="1">Мужской</option>
				<option value="2">Женский</option>
			</select>
			<span name="error_sex_id" id='error_sex_id' class="error_response underinput-error text-danger d-flex justify-content-left align-items-lg-center">
			&nbsp;
			</span>	

		</div>							
	</div>	
				
	<div class="col-lg-3 px-2 mb-2">
		<div class="form-group">
		
			<label class='control-label font-weight-bold' for="date_of_birth">Дата рождения <span class="text-danger">*</span></label>		
			<div class="input-group input-group-lg">
				<input name="date_of_birth" id="date_of_birth" type="text" class="form-control form-control-lg datepicker-here appearance-none bg-white" data-position='bottom left' placeholder="Выберите дату" readonly />
				<button type="button" onclick="calendarinput1()" class="bg-white input-group-text" id="inputGroup-sizing-lg">
					<img src="{{ asset('images/calendar.png') }}" class="img-responsive"  width="30" height="30" />
				</button>
			</div>	
			<span name="error_date_of_birth" id='error_date_of_birth' class="error_response underinput-error text-danger d-flex justify-content-left align-items-lg-center">
			&nbsp;
			</span>

		</div>							
	</div>
		
	<div class="col-lg-3 px-3 mb-4 d-flex justify-content-lg-center align-items-lg-center">
		
		<div class="form-check form-switch d-flex justify-content-lg-center align-items-lg-center">
			<input class="form-check-input custom-control-input" type="checkbox" id="aprox_date_check" name="aprox_date_check">
			<label class="form-check-label toggle-label" for="aprox_date_check">Не точная дата</label>

			<input name="aprox_date" id='aprox_date' value="0" hidden="true"/>
		</div>

	</div>
	
	<div class="col-lg-3 px-3 mb-4 d-flex justify-content-lg-center align-items-lg-center">
		
		<div class="form-check form-switch d-flex justify-content-lg-center align-items-lg-center">
			<input class="form-check-input custom-control-input" type="checkbox" id="rip_check" name="rip_check">
			<label class="form-check-label toggle-label" for="rip_check">R.I.P.</label>

			<input name="rip" id='rip' value="0" hidden="true"/>
		</div>
		
	</div>
	
</div>


<div class="row">
	<div class="col-lg-4 px-2 mb-2">
		<div class="form-group">
		
			<label class='control-label font-weight-bold' for="tatoo">Клеймо <span class="text-danger"></span></label>
			<input name="tatoo" value="" class='form-control form-control-lg' id='tatoo' type='text' placeholder='Клеймо' />
					
			<span name="error_tatoo" id='error_tatoo' class="error_response underinput-error text-danger d-flex justify-content-left align-items-lg-center">
			&nbsp;
			</span>
		
		</div>					
	</div>
				
	<div class="col-lg-4 px-2 mb-2">		
		<div class="form-group">
		
			<label class='control-label font-weight-bold' for="chip">Чип <span class="text-danger"></span></label>
			<input name="chip" value="" class='form-control form-control-lg' id='chip' type='text' placeholder='Чип' />
					
			<span name="error_chip" id='error_chip' class="error_response underinput-error text-danger d-flex justify-content-left align-items-lg-center">
			&nbsp;
			</span>
		
		</div>			
	</div>
				
	<div class="col-lg-4 px-2 mb-2">
		<div class="form-group">
		
			<label class='control-label font-weight-bold' for="castrated">Кастрирован / Стерилизованная<span class="text-danger"></span></label>		
			<select name="castrated" id="castrated" class="form-select-lg js-example-basic-hide" style="width: 100%;" placeholder="Не выбрано" data-search="true">
				<option></option>
				<option value="1">Нет</option>
				<option value="2">Да</option>
			</select>
			<span name="error_castrated" id='error_castrated' class="error_response flex items-center font-medium tracking-wide text-red-500 text-xs mt-1 ml-1">
			&nbsp;
			</span>
		
		</div>							
	</div>
</div>	


<div class="row">
				
	<div class="col-lg px-2">
		<div class="form-group">			
			<label class='control-label font-weight-bold' for="additional_info">Дополнительная информация <span class="text-danger"></span></label>
			<textarea name="additional_info" class="form-control" id='additional_info' rows="3"></textarea>
			
			<span name="error_additional_info" id='error_additional_info' class="error_response underinput-error text-danger d-flex justify-content-left align-items-lg-center">
			&nbsp;
			</span>
		</div>							
	</div>
		
</div>