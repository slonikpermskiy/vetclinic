<!-- Модальные окна -->	

<!-- Изменить данные животного -->
<div class="modal hide mycontainer" id="change_dog_data" role="dialog" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false" style="display: none">
	<div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title px-3" id="staticBackdropLabel">Изменить данные животного</h5>
				<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
			</div>
			<div class="modal-body">
				
				<div class="px-4 mb-2">
				
				<form id="myForm" autocomplete="off">					
				@csrf
				
					<input name="patient_id" id='patient_id' value="0" hidden="true"/>

					@include('patient_info_card')
		

				</form>

				</div>
								
			</div>
			<div class="modal-footer pe-4">
				<button type="button" class="btn btn-primary" onclick="change_patient_data()">Сохранить</button>		
			</div>
		</div>
	</div>
</div>


<!-- Удалить животное -->
<div class="modal hide mycontainer" id="delete_dog" role="dialog" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false" style="display: none">
	<div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title px-3" id="staticBackdropLabel">Удалить животное</h5>
				<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
			</div>
			<div class="modal-body">
				
				<div class="px-3">
				
					<div class="text-break">При удалении пациента, также будут удалены все связанные с ним данные.</div>
					
					<div class="text-break mt-2 fw-bold">Вы действительно хотите удалить пациента ?</div>

				</div>
								
			</div>
			<div class="modal-footer pe-4">
				<button type="button" class="btn btn-danger" onclick="delete_patient()">Удалить</button>		
			</div>
		</div>
	</div>
</div>


<!-- Изменить данные клиента -->
<div class="modal hide mycontainer" id="change_client_data" role="dialog" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false" style="display: none">
	<div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title px-3" id="staticBackdropLabel">Изменить данные владельца</h5>
				<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
			</div>
			<div class="modal-body">
				
				<div class="px-4 mb-2">
				
				<form id="myForm2" autocomplete="off">					
				@csrf
				
					<input name="owner_id" id='owner_id' value="0" hidden="true"/>
					
					<div class="row">
					<div class="col px-2 mb-3 justify-content-start align-items-center text-danger fs-6 text-break">Внимание, если у владельца несколько собак, то данные изменятся во всех записях !!!</div>										
					</div>

					@include('client_info_card')
		
				</form>

				</div>
								
			</div>
			<div class="modal-footer pe-4">
				<button type="button" class="btn btn-primary" onclick="change_client_data()">Сохранить</button>		
			</div>
		</div>
	</div>
</div>


<!-- Новый клиент -->
<div class="modal hide mycontainer" id="new_client" role="dialog" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false" style="display: none">
	<div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title px-3" id="staticBackdropLabel">Новый владелец</h5>
				<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
			</div>
			<div class="modal-body">
				
				<div class="px-4 mb-2">
				
				<form id="myForm3" autocomplete="off">					
				@csrf
				
					<input name="patient_id" id='patient_id' value="0" hidden="true"/>

					@include('client_info_card_2')
		
				</form>

				</div>
								
			</div>
			<div class="modal-footer pe-4">
				<button type="button" class="btn btn-primary" onclick="new_client()">Сохранить</button>		
			</div>
		</div>
	</div>
</div>


<!-- Сменить клиента -->
<div class="modal hide mycontainer" id="change_client" role="dialog" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false" style="display: none">
	<div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title px-3" id="staticBackdropLabel">Сменить владельца</h5>
				<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
			</div>
			<div class="modal-body">
				
				<div class="px-4 mb-2">
				
				<form id="myForm4" autocomplete="off">					
				@csrf
				
					<input name="patient_id" id='patient_id' value="0" hidden="true"/>
				
					<div class="row">
						<div class="col-lg px-2">			
							<div class="form-group">
								<label class="control-label font-weight-bold" for="client_id">Найти владельца</label>	
								<div class="d-lg-flex justify-content-left align-items-lg-center">					
									<div class="col-lg-6">	
										<select name="client_id" id="client_id" class="form-select-lg js-example-basic-single block" style="width: 100%;" placeholder="Не выбрано" data-search="true">
											<option></option>
										</select>
										<span name="error_client_id" id='error_client_id' class="error_response underinput-error text-danger d-flex justify-content-left align-items-lg-center">
										&nbsp;
										</span>
									</div>		
									<div name="new_client_btn" id="new_client_btn" class="d-none col-lg px-lg-4 mb-3 d-flex justify-content-left align-items-lg-center">
										<div class="" align="center"><button data-bs-dismiss="modal" data-bs-toggle="modal" data-bs-target="#new_client" type="button" class="btn btn-secondary">Добавить</button></div>
									</div>
								</div>
							</div>					
						</div>
					</div>

		
				</form>

				</div>
								
			</div>
			<div class="modal-footer pe-4">
				<button type="button" class="btn btn-primary" onclick="change_client()">Сохранить</button>		
			</div>
		</div>
	</div>
</div>


<!-- Удалить фото -->
<div class="modal hide mycontainer" id="delete_photo" role="dialog" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false" style="display: none">
	<div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title px-3" id="staticBackdropLabel">Удалить фото</h5>
				<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
			</div>
			<div class="modal-body">
				
				<div class="px-3">
				
					<input name="phototodelete_id" id='phototodelete_id' value="0" hidden="true"/>
					
					<div class="text-break">Вы действительно хотите удалить фото ?</div>

				</div>
								
			</div>
			<div class="modal-footer pe-4">
				<button type="button" class="btn btn-danger" onclick="delete_gallery_photo()">Удалить</button>		
			</div>
		</div>
	</div>
</div>


<!-- Удалить прием -->
<div class="modal hide mycontainer" id="delete_visit" role="dialog" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false" style="display: none">
	<div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title px-3" id="staticBackdropLabel">Удалить прием</h5>
				<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
			</div>
			<div class="modal-body">
				
				<div class="px-3">
				
					<input name="visitdelete_id" id='visitdelete_id' value="0" hidden="true"/>
					
					<div class="text-break">Вы действительно хотите удалить прием ?</div>

				</div>
								
			</div>
			<div class="modal-footer pe-4">
				<button type="button" class="btn btn-danger" onclick="delete_visit()">Удалить</button>		
			</div>
		</div>
	</div>
</div>


<!-- Динамика веса -->
<div class="modal hide mycontainer" id="weight_graph" role="dialog" aria-hidden="true" data-bs-keyboard="false" style="display: none">
	<div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title px-3" id="staticBackdropLabel">Динамика веса</h5>
				<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
			</div>
			<div class="modal-body">
				
				<div class="px-3">
					
					<div class="text-break justify-content-start align-items-center">
					
					
					<canvas id="myChart" width="400" height="400"></canvas>
						
						
					
					</div>

				</div>
								
			</div>
			
		</div>
	</div>
</div>


<!-- Удалить исследование -->
<div class="modal hide mycontainer" id="delete_research" role="dialog" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false" style="display: none">
	<div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title px-3" id="staticBackdropLabel">Удалить исследование</h5>
				<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
			</div>
			<div class="modal-body">
				
				<div class="px-3">
				
					<input name="researchdelete_id" id='researchdelete_id' value="0" hidden="true"/>
					
					<div class="text-break">Вы действительно хотите удалить исследование ?</div>

				</div>
								
			</div>
			<div class="modal-footer pe-4">
				<button type="button" class="btn btn-danger" onclick="delete_research()">Удалить</button>		
			</div>
		</div>
	</div>
</div>


<!-- Удалить анализ -->
<div class="modal hide mycontainer" id="delete_analysis" role="dialog" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false" style="display: none">
	<div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title px-3" id="staticBackdropLabel">Удалить анализ</h5>
				<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
			</div>
			<div class="modal-body">
				
				<div class="px-3">
				
					<input name="analysisdelete_id" id='analysisdelete_id' value="0" hidden="true"/>
					
					<div class="text-break">Вы действительно хотите удалить анализ ?</div>

				</div>
								
			</div>
			<div class="modal-footer pe-4">
				<button type="button" class="btn btn-danger" onclick="delete_analysis()">Удалить</button>		
			</div>
		</div>
	</div>
</div>


<!-- Удалить вакцинацию -->
<div class="modal hide mycontainer" id="delete_vacine" role="dialog" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false" style="display: none">
	<div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title px-3" id="staticBackdropLabel">Удалить вакцинацию</h5>
				<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
			</div>
			<div class="modal-body">
				
				<div class="px-3">
				
					<input name="vacinedelete_id" id='vacinedelete_id' value="0" hidden="true"/>
					
					<div class="text-break">Вы действительно хотите удалить вакцинацию ?</div>

				</div>
								
			</div>
			<div class="modal-footer pe-4">
				<button type="button" class="btn btn-danger" onclick="delete_vacine()">Удалить</button>		
			</div>
		</div>
	</div>
</div>


