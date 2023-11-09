<ul class="nav nav-tabs" id="myTab" role="tablist">
	<li class="nav-item" role="presentation">
		<button class="nav-link active" id="visit-tab" data-bs-toggle="tab" data-bs-target="#visit" type="button" role="tab" aria-controls="visit" aria-selected="true">Визиты</button>
	</li>
	<li class="nav-item" role="presentation">
		<button class="nav-link" id="vacine-tab" data-bs-toggle="tab" data-bs-target="#vacine" type="button" role="tab" aria-controls="vacine" aria-selected="false">Вакцинация</button>
	</li>
	<li class="nav-item" role="presentation">
		<button class="nav-link" id="research-tab" data-bs-toggle="tab" data-bs-target="#research" type="button" role="tab" aria-controls="research" aria-selected="false">Исследования</button>
	</li>
	<li class="nav-item" role="presentation">
		<button class="nav-link" id="analisys-tab" data-bs-toggle="tab" data-bs-target="#analisys" type="button" role="tab" aria-controls="analisys" aria-selected="false">Анализы</button>
	</li>
	<li class="nav-item" role="presentation">
		<button class="nav-link" id="photo-tab" data-bs-toggle="tab" data-bs-target="#photo" type="button" role="tab" aria-controls="photo" aria-selected="false">Фото</button>
	</li>
	<li class="nav-item" role="presentation">
		<button class="nav-link" id="bills-tab" data-bs-toggle="tab" data-bs-target="#bills" type="button" role="tab" aria-controls="bills" aria-selected="false">Счета</button>
	</li>
</ul>

<div class="tab-content" id="myTabContent">

	<div class="tab-pane fade show active pt-3" id="visit" role="tabpanel" aria-labelledby="visit-tab">
	
		<div name="visit_container" id="visit_container">
			
			<div name="visit_list" id="visit_list">
			
				<form id="search_form" autocomplete="off" enctype="multipart/form-data">


					<div class="d-flex align-items-center py-2">								

						<div class="col-6 d-flex justify-content-start align-items-center">
							<div class=""><button type="button" class="btn btn-primary" onclick="new_visit()">Новый визит</button></div>
						</div>

						<div class=" col-6 d-flex justify-content-end align-items-center" >								
							<button name="visit_filter_btn" id="visit_filter_btn"  type="button" onclick="collapse_expand_visit_filter()" class="btn btn-sm border-1 border-primary text-primary">Открыть фильтр</button>
						</div>	
						
					</div>


					<div class="row border border px-3 mx-0 mt-2 mb-3 rounded justify-content-center d-none" name="visit_filter" id="visit_filter">
											
						<div class="row px-2 pt-4">
						
							<div class="col-lg-3 px-2 mb-2">			
								<div class="form-group">
									<label class='control-label font-weight-bold' for="visit_date_start">Дата визита</label>
									<div class="input-group">
										<input name="visit_date_start" id="visit_date_start" type="text" class="form-control form-control-lg datepicker-here appearance-none bg-white" data-position='bottom left' placeholder="С..." readonly/>
											<button type="button" onclick="calendarinputVDS()" class="bg-white input-group-text" id="inputGroup-sizing-lg">
												<img src="{{ asset('images/calendar.png') }}" class="img-responsive"  width="30" height="30" />
											</button>
										</div>			
								</div>
							</div>
							
							<div class="col-lg-3 px-2 mb-2">			
								<div class="form-group">
									<label class='control-label font-weight-bold' for="visit_date_end">Дата визита</label>
									<div class="input-group">
										<input name="visit_date_end" id="visit_date_end" type="text" class="form-control form-control-lg datepicker-here appearance-none bg-white" data-position='bottom left' placeholder="По..." readonly/>
											<button type="button" onclick="calendarinputVDE()" class="bg-white input-group-text" id="inputGroup-sizing-lg">
												<img src="{{ asset('images/calendar.png') }}" class="img-responsive"  width="30" height="30" />
											</button>
										</div>			
								</div>
							</div>
							
						
							<div class="col-lg-6 px-2 mb-2">		
								<div class="form-group">
									<label class='control-label font-weight-bold' for="doctor_2">Врач</label>
									
									<select name="doctor_2" id="doctor_2" class="form-select-lg js-example-basic-single block doctors" style="width: 100%;" placeholder="Не выбрано" data-search="true">
										<option></option>
									</select>

								</div>	
			
							</div>
																		
						</div>
						
						
						<div class="row px-2 pt-0 pt-lg-2">
						
							<div class="col-lg-4 px-2 mb-2">
								<div class="form-group">
								
									<label class='control-label font-weight-bold' for="porpose_2">Цель визита<span class="text-danger"></span></label>		
									<select name="porpose_2" id="porpose_2" class="form-select-lg js-example-basic-single" style="width: 100%;" placeholder="Не выбрано" data-search="true">
										<option></option>
										<option value="1">Консультация</option>
										<option value="2">Манипуляции</option>
										<option value="3">Манипуляции (для другой клиники)</option>
										<option value="4">Операция</option>
										<option value="5">Стационар</option>
										<option value="6">Гигиенические процедуры</option>
									</select>

								</div>							
							</div>
							
							<div class="col-lg-8 px-2 mb-2">
								<div class="form-group">
								
									<label class='control-label font-weight-bold' for="diagnosis_list_2">Диагноз <span class="text-danger"></span></label>		
									<select name="diagnosis_list_2" id="diagnosis_list_2" class="form-select-lg js-example-basic-single block diagnosis_list" style="width: 100%;" placeholder="Не выбрано" data-search="true">
										<option></option>
									</select>
								</div>							
							</div>	
						
						
						</div>
								

								
						<div class="d-lg-flex flex-row pt-2 mb-4">	
																																																		
							<div class="p-2"><button type="button" onclick="clear_form()" class="btn btn-secondary">Сбросить</button></div>


						</div>	
							
					</div>
				
				</form>	


				<div name="visits_list_response" id="visits_list_response" class='row w-full items-center mt-2 mb-2 mx-0 d-flex justify-content-center'>

				</div>
								
			</div>
			

			<div name="one_visit" id="one_visit" style="display: none;">
															
			</div>
			
			
			<div name="print_visit_div" id="print_visit_div" class="d-none">
					
			</div>
			
		</div>
		
	</div>
		
	<div class="tab-pane fade pt-3" id="vacine" role="tabpanel" aria-labelledby="vacine-tab">
	
		<div class="row w-full"> 
		
				<div class='mb-2 px-2 justify-content-left align-items-lg-center'> 
					<button type="button" class="btn btn-primary" onclick="new_vacine()">Добавить</button>
				</div>

		</div>
		
		<div name="vacine_list_response" id="vacine_list_response" class='row w-full items-center mt-2 mb-2 mx-0 d-flex justify-content-center'>

		</div>
	
	</div>
	
	
	<div class="tab-pane fade pt-3" id="research" role="tabpanel" aria-labelledby="research-tab">
	
		<div name="research_container" id="research_container">
			
			<div name="research_search_list" id="research_search_list">
			
				<form id="research_form" autocomplete="off" enctype="multipart/form-data">


					<div class="d-flex align-items-center py-2">								

						<div class="col-6 d-flex justify-content-start align-items-center">
							<div class="p-2"><button type="button" class="btn btn-primary" onclick="new_research()">Новое исследование</button></div>
						</div>

						<div class=" col-6 d-flex justify-content-end align-items-center" >								
							<button name="research_filter_btn" id="research_filter_btn"  type="button" onclick="collapse_expand_research_filter()" class="btn btn-sm border-1 border-primary text-primary">Открыть фильтр</button>
						</div>	
						
					</div>
				

					<div class="row border border px-3 mx-0 mt-2 mb-3 rounded justify-content-center d-none" name="research_filter" id="research_filter">
											
						<div class="row px-2 pt-4">
						
							<div class="col-lg-3 px-2 mb-2">			
								<div class="form-group">
									<label class='control-label font-weight-bold' for="research_visit_date_start">Дата исследования</label>
									<div class="input-group">
										<input name="research_visit_date_start" id="research_visit_date_start" type="text" class="form-control form-control-lg datepicker-here appearance-none bg-white" data-position='bottom left' placeholder="С..." readonly/>
											<button type="button" onclick="calendarinputRVDS()" class="bg-white input-group-text" id="inputGroup-sizing-lg">
												<img src="{{ asset('images/calendar.png') }}" class="img-responsive"  width="30" height="30" />
											</button>
										</div>			
								</div>
							</div>
							
							<div class="col-lg-3 px-2 mb-2">			
								<div class="form-group">
									<label class='control-label font-weight-bold' for="research_visit_date_end">Дата исследования</label>
									<div class="input-group">
										<input name="research_visit_date_end" id="research_visit_date_end" type="text" class="form-control form-control-lg datepicker-here appearance-none bg-white" data-position='bottom left' placeholder="По..." readonly/>
											<button type="button" onclick="calendarinputRVDE()" class="bg-white input-group-text" id="inputGroup-sizing-lg">
												<img src="{{ asset('images/calendar.png') }}" class="img-responsive"  width="30" height="30" />
											</button>
										</div>			
								</div>
							</div>
							
							<div class="col-lg-6 px-2 mb-2">		
								<div class="form-group">
									<label class='control-label font-weight-bold' for="doctor_3_filter">Врач</label>
									
									<select name="doctor_3_filter" id="doctor_3_filter" class="form-select-lg js-example-basic-single block doctors" style="width: 100%;" placeholder="Не выбрано" data-search="true">
										<option></option>
									</select>

								</div>	
			
							</div>
																		
						</div>
						
						<div class="row px-2 pt-0 pt-lg-2">
						
							<div class="col-lg-4 px-2 mb-2">
								<div class="form-group">
								
									<label class='control-label font-weight-bold' for="to_visit_filter">К визиту<span class="text-danger"></span></label>		
									<select name="to_visit_filter" id="to_visit_filter" class="form-select-lg js-example-basic-single tovisit" style="width: 100%;" placeholder="Не выбрано" data-search="true">
										<option></option>
									</select>

								</div>							
							</div>
							
							<div class="col-lg-8 px-2 mb-2">
								<div class="form-group">
								
									<label class='control-label font-weight-bold' for="research_name_filter">Наименование <span class="text-danger"></span></label>		
									<input name="research_name_filter" class="form-control form-control-lg" id='research_name_filter' placeholder="Наименование">
								</div>							
							</div>	
						
						
						</div>
										
						<div class="d-lg-flex flex-row pt-2 mb-4">	
																																																		
							<div class="p-2"><button type="button" onclick="clear_research_form()" class="btn btn-secondary">Сбросить</button></div>

						</div>	
							
					</div>
				
				</form>	


				<div name="researches_list_response" id="researches_list_response" class='row w-full items-center mt-2 mb-2 mx-0 d-flex justify-content-center'>

				</div>
								
			</div>
			

			<div name="one_research" id="one_research" style="display: none;">
															
			</div>
			
			
			<div name="print_research_div" id="print_research_div" class="d-none">
					
			</div>
			
		</div>

	</div>
	
	
	<div class="tab-pane fade pt-3" id="analisys" role="tabpanel" aria-labelledby="analisys-tab">

		<div name="analisys_container" id="analisys_container">
			
			<div name="analisys_search_list" id="analisys_search_list">
			
				<form id="analisys_form" autocomplete="off" enctype="multipart/form-data">


					<div class="d-flex align-items-center py-2">								

						<div class="col-6 d-flex justify-content-start align-items-center">
							<div class="p-2"><button type="button" class="btn btn-primary" onclick="new_analysis()">Новый анализ</button></div>
						</div>

						<div class=" col-6 d-flex justify-content-end align-items-center" >								
							<button name="analisys_filter_btn" id="analisys_filter_btn"  type="button" onclick="collapse_expand_analisys_filter()" class="btn btn-sm border-1 border-primary text-primary">Открыть фильтр</button>
						</div>	
						
					</div>
				

					<div class="row border border px-3 mx-0 mt-2 mb-3 rounded justify-content-center d-none" name="analisys_filter" id="analisys_filter">
											
						<div class="row px-2 pt-4">
						
							<div class="col-lg-3 px-2 mb-2">			
								<div class="form-group">
									<label class='control-label font-weight-bold' for="analisys_visit_date_start">Дата анализа</label>
									<div class="input-group">
										<input name="analisys_visit_date_start" id="analisys_visit_date_start" type="text" class="form-control form-control-lg datepicker-here appearance-none bg-white" data-position='bottom left' placeholder="С..." readonly/>
											<button type="button" onclick="calendarinputAVDS()" class="bg-white input-group-text" id="inputGroup-sizing-lg">
												<img src="{{ asset('images/calendar.png') }}" class="img-responsive"  width="30" height="30" />
											</button>
										</div>			
								</div>
							</div>
							
							<div class="col-lg-3 px-2 mb-2">			
								<div class="form-group">
									<label class='control-label font-weight-bold' for="analisys_visit_date_end">Дата анализа</label>
									<div class="input-group">
										<input name="analisys_visit_date_end" id="analisys_visit_date_end" type="text" class="form-control form-control-lg datepicker-here appearance-none bg-white" data-position='bottom left' placeholder="По..." readonly/>
											<button type="button" onclick="calendarinputAVDE()" class="bg-white input-group-text" id="inputGroup-sizing-lg">
												<img src="{{ asset('images/calendar.png') }}" class="img-responsive"  width="30" height="30" />
											</button>
										</div>			
								</div>
							</div>
							
							<div class="col-lg-6 px-2 mb-2">		
								<div class="form-group">
									<label class='control-label font-weight-bold' for="doctor_4_filter">Врач</label>
									
									<select name="doctor_4_filter" id="doctor_4_filter" class="form-select-lg js-example-basic-single block doctors" style="width: 100%;" placeholder="Не выбрано" data-search="true">
										<option></option>
									</select>

								</div>	
			
							</div>
																		
						</div>
						
						<div class="row px-2 pt-0 pt-lg-2">
						
							<div class="col-lg-4 px-2 mb-2">
								<div class="form-group">
								
									<label class='control-label font-weight-bold' for="analisys_to_visit_filter">К визиту<span class="text-danger"></span></label>		
									<select name="analisys_to_visit_filter" id="analisys_to_visit_filter" class="form-select-lg js-example-basic-single tovisit" style="width: 100%;" placeholder="Не выбрано" data-search="true">
										<option></option>
									</select>

								</div>							
							</div>
							
							<div class="col-lg-8 px-2 mb-2">
								<div class="form-group">
								
									<label class='control-label font-weight-bold' for="analisys_name_filter">Наименование <span class="text-danger"></span></label>		
									<input name="analisys_name_filter" class="form-control form-control-lg" id='analisys_name_filter' placeholder="Наименование">
								</div>							
							</div>	
						
						
						</div>
										
						<div class="d-lg-flex flex-row pt-2 mb-4">	
																																																		
							<div class="p-2"><button type="button" onclick="clear_analysis_form()" class="btn btn-secondary">Сбросить</button></div>

						</div>	
							
					</div>
				
				</form>	


				<div name="analisys_list_response" id="analisys_list_response" class='row w-full items-center mt-2 mb-2 mx-0 d-flex justify-content-center'>

				</div>
								
			</div>
			

			<div name="one_analisys" id="one_analisys" style="display: none;">
															
			</div>
			
			
			<div name="print_analisys_div" id="print_analisys_div" class="d-none">
					
			</div>
			
		</div>
	
	</div>
	
	
	
	<div class="tab-pane fade pt-3 px-1" id="photo" role="tabpanel" aria-labelledby="photo-tab">
	
		<div class="row">			
			<span name="error_imagetoloadnotvisit" id='error_imagetoloadnotvisit' class="error_imagetoloadnotvisit underinput-error text-danger d-flex justify-content-left align-items-lg-center mb-1">
			</span>
		</div>
	
		<div class="row w-full"> 
	
			<div class='col-lg-5 mb-2 px-2'> 
				<input type='file' name='imagetoloadnotvisit' id='imagetoloadnotvisit' class='form-control'> 
			</div> 
			<div class='mb-2 px-2 col-lg-4 justify-content-left align-items-lg-center'> 
				<input name='descriptionnotvisit' id='descriptionnotvisit' class='form-control' placeholder='Описание'/> 
			</div> 
			<div class='mb-2 px-2 col-lg-3 justify-content-left align-items-lg-center'> 
				<button name='upload_photo_notvisit_btn' id='upload_photo_notvisit_btn' class="btn btn-success" type="button" onclick="upload_photo_notvisit()">
					Загрузить
				</button>
			</div>

		</div>
		
		<div name="uploaded_photo_notvisit_response" id="uploaded_photo_notvisit_response" class='row w-full items-center mt-2 mb-1 px-0 d-flex justify-content-center'>
			
		</div>
		
		<input name="image_url" id='image_url' value="" type='text' style='position:absolute; top:-500px; opacity:0; z-index: -1'/>

	</div>


	<div class="tab-pane fade pt-3" id="bills" role="tabpanel" aria-labelledby="bills-tab">

		<div name="bills_container" id="bills_container">
			
			<div name="bills_search_list" id="bills_search_list">
			
				<form id="bills_form" autocomplete="off" enctype="multipart/form-data">	
				
				
					<div class="d-flex align-items-center py-2">								

						<div class="col-6 d-flex justify-content-start align-items-center">
							<div class="p-2"><button type="button" class="btn btn-primary" onclick="new_bill()">Новый счет</button></div>
						</div>

						<div class=" col-6 d-flex justify-content-end align-items-center" >								
							<button name="bills_filter_btn" id="bills_filter_btn"  type="button" onclick="collapse_expand_bills_filter()" class="btn btn-sm border-1 border-primary text-primary">Открыть фильтр</button>
						</div>	
						
					</div>


					<div class="row border border px-3 mx-0 mt-2 mb-3 rounded justify-content-center d-none" name="bills_filter" id="bills_filter">
											
						<div class="row px-2 pt-4">
						
							<div class="col-lg-3 px-2 mb-2">			
								<div class="form-group">
									<label class='control-label font-weight-bold' for="bill_date_start">Дата счета</label>
									<div class="input-group">
										<input name="bill_date_start" id="bill_date_start" type="text" class="form-control form-control-lg datepicker-here appearance-none bg-white" data-position='bottom left' placeholder="С..." readonly/>
											<button type="button" onclick="calendarinputBDS()" class="bg-white input-group-text" id="inputGroup-sizing-lg">
												<img src="{{ asset('images/calendar.png') }}" class="img-responsive"  width="30" height="30" />
											</button>
										</div>			
								</div>
							</div>
							
							<div class="col-lg-3 px-2 mb-2">			
								<div class="form-group">
									<label class='control-label font-weight-bold' for="bill_date_end">Дата счета</label>
									<div class="input-group">
										<input name="bill_date_end" id="bill_date_end" type="text" class="form-control form-control-lg datepicker-here appearance-none bg-white" data-position='bottom left' placeholder="По..." readonly/>
											<button type="button" onclick="calendarinputBDE()" class="bg-white input-group-text" id="inputGroup-sizing-lg">
												<img src="{{ asset('images/calendar.png') }}" class="img-responsive"  width="30" height="30" />
											</button>
										</div>			
								</div>
							</div>
							
							<div class="col-lg-6 px-2 mb-2">
								<div class="form-group">
								
									<label class='control-label font-weight-bold' for="product_filter">Товар<span class="text-danger"></span></label>		
									<select name="product_filter" id="product_filter" class="form-select-lg js-example-basic-single product_list" style="width: 100%;" placeholder="Не выбрано" data-search="true">
										<option></option>
									</select>

								</div>							
							</div>
											
						</div>
						
						<div class="row px-2 pt-0 pt-lg-2">

							<div class="col-lg-6 px-2 mb-2">
								<div class="form-group">
								
									<label class='control-label font-weight-bold' for="service_filter">Услуга<span class="text-danger"></span></label>		
									<select name="service_filter" id="service_filter" class="form-select-lg js-example-basic-single service_list" style="width: 100%;" placeholder="Не выбрано" data-search="true">
										<option></option>
									</select>

								</div>							
							</div>
							
							<div class="col-lg-4 px-2 mb-2">
								<div class="form-group">
								
									<label class='control-label font-weight-bold' for="paied_or_not">Оплата счета<span class="text-danger"></span></label>		
									<select name="paied_or_not" id="paied_or_not" class="form-select-lg js-example-basic-hide" style="width: 100%;" placeholder="Не выбрано" data-search="true">
										<option></option>
										<option value="1">Счет оплачен</option>
										<option value="2">Счет не оплачен</option>
									</select>

								</div>							
							</div>
						
						
						</div>
										
						<div class="d-lg-flex flex-row pt-2 mb-4">	
																																																		
							<div class="p-2"><button type="button" onclick="clear_bills_form()" class="btn btn-secondary">Сбросить</button></div>

						</div>	
							
					</div>
				
				</form>	

				<div name="bills_list_response" id="bills_list_response" class='row w-full items-center mt-2 mb-2 mx-0 d-flex justify-content-center'>

				</div>
								
			</div>
			

			<div name="one_bill" id="one_bill" style="display: none;">
															
			</div>
			
			
			<div name="print_bill_div" id="print_bill_div" class="d-none">
					
			</div>
			
		</div>
		
	</div>
	
</div>


<script> 
	
</script> 
