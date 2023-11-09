<?php

namespace App\Http\Controllers;

use App\Patients;
use App\Clients;
use App\Animal_types;
use App\Breeds;
use App\Colors;
use App\AnimalWeight;
use App\Visits;
use App\DiagnosisVisits;
use App\DiagnosisTypes;
use App\Researches;
use App\Analysis;
use App\Vacines;
use App\Bills;
use App\Pays;
use App\UploadedPhoto;
use Illuminate\Http\Request;
use Validator;
use Illuminate\Validation\Rule;
use Carbon\Carbon;
use Illuminate\Support\Str;
use Session;
use Artisan;


class MainController extends Controller
{
	
	/*public function __construct()
    {
        $this->middleware('auth');

		//$this->middleware('log', ['only' => ['fooAction', 'barAction']]);
        //$this->middleware('subscribed', ['except' => ['fooAction', 'barAction']]);
    }*/
    
	
	// Главная страница
	public function mainPage(){
        return view('welcome');
    }
	
	// Страница добавления пациента
	public function showAddDogForm(){
        return view('add-dog');
    }
	
	// Открытие карточки клиента
	public function openPatientCard(Request $request){		  
		return view('patient_card', ['patient_id' => $request->get('patient_id')]);
    }

	
	public function saveClient(Request $request){
								
		// Тексты ошибок
		$messages = [
			// По клиенту
			'last_name.required' => 'Не заполнено поле',
			'last_name.regex' => 'Поле содержит недопустимые символы',
			'first_name.required' => 'Не заполнено поле',
			'first_name.regex' => 'Поле содержит недопустимые символы',
			'middle_name.regex' => 'Поле содержит недопустимые символы',
			'phone.unique' => 'Значение уже существует',
			'phone.regex' => 'Поле содержит недопустимые символы',
			'city.regex' => 'Поле содержит недопустимые символы',
			'address.regex' => 'Поле содержит недопустимые символы',
			'phoneinfo.regex' => 'Поле содержит недопустимые символы',
			'phone2.regex' => 'Поле содержит недопустимые символы',
			'phoneinfo2.regex' => 'Поле содержит недопустимые символы',
			'email.regex' => 'Поле содержит недопустимые символы',
			'comments.regex' => 'Поле содержит недопустимые символы',		
			// По пациенту
			'short_name.required' => 'Не заполнено поле',
			'short_name.regex' => 'Поле содержит недопустимые символы',
			'full_name.regex' => 'Поле содержит недопустимые символы',
			'animal_type_id.required' => 'Не выбрано значение',
			'breed_id.required' => 'Не выбрано значение',
			//'color_id.required' => 'Не выбрано значение',
			'date_of_birth.required' => 'Не заполнено поле',
			'date_of_birth.date_format' => 'Неверный формат даты',
			'tatoo.unique' => 'Значение уже существует',
			'tatoo.regex' => 'Поле содержит недопустимые символы',
			'chip.unique' => 'Значение уже существует',
			'chip.regex' => 'Поле содержит недопустимые символы',
			'additional_info.regex' => 'Поле содержит недопустимые символы'
		];
		
		
		// Замена запятых на точки в float 
		/*if ($request->has('weight')) {
			if ($request->weight != null) {
			$newweight = str_replace(",",".",$request->weight);
            $newweight = preg_replace('/\.(?=.*\.)/', '', $newweight);			
			$request->merge(['weight'=>$newweight]);
			}
		}*/

		
		// Валидация на ошибки в полях
		$validator = Validator::make($request->all(), [		
			// По клиенту
			'last_name' => ['required', 'max:255', 'regex:/^[^`~#$^&*=\\|\\[\\]{}<>]*$/',],  
			'first_name' => ['required', 'max:255', 'regex:/^[^`~#$^&*=\\|\\[\\]{}<>]*$/',],
			'middle_name' => ['nullable', 'max:255', 'regex:/^[^`~#$^&*=\\|\\[\\]{}<>]*$/',],
			'phone' => ['nullable', 'max:255', 'regex:/^[^`~#$^&*=\\|\\[\\]{}<>]*$/',],
			'city' => ['nullable', 'max:255', 'regex:/^[^`~#$^&*=\\|\\[\\]{}<>]*$/',],	
			'address' => ['nullable', 'max:255', 'regex:/^[^`~#$^&*=\\|\\[\\]{}<>]*$/',],	
			'phoneinfo' => ['nullable', 'max:255', 'regex:/^[^`~#$^&*=\\|\\[\\]{}<>]*$/',],	
			'phone2' => ['nullable', 'max:255', 'regex:/^[^`~#$^&*=\\|\\[\\]{}<>]*$/',],	
			'phoneinfo2' => ['nullable', 'max:255', 'regex:/^[^`~#$^&*=\\|\\[\\]{}<>]*$/',],				
			// 'phone' => ['nullable', 'max:255', Rule::unique('clients')->ignore($request->get('client_id'), 'client_id'),],
			'email' => ['nullable', 'max:255', 'regex:/^[^`~#$^&*=\\|\\[\\]{}<>]*$/',],	
			'comments' => ['nullable', 'max:1000', 'regex:/^[^`~#$^&*=\\|\\[\\]{}<>]*$/',],			
			// По собаке
			'short_name' => ['required', 'max:255', 'regex:/^[^`~#$^&*=\\|\\[\\]{}<>]*$/',],
			'full_name' => ['nullable', 'max:255', 'regex:/^[^`~#$^&*=\\|\\[\\]{}<>]*$/',],
			'animal_type_id' => 'required',
			'breed_id' => 'required',
			//'color_id' => 'required',
			'date_of_birth' => ['required', 'date_format:d.m.Y',],
			'aprox_date' => 'nullable',
			'rip' => 'nullable',
			'tatoo' => ['nullable', 'max:255', 'regex:/^[^`~#$^&*=\\|\\[\\]{}<>]*$/',],
            'chip' => ['nullable', 'max:255', 'regex:/^[^`~#$^&*=\\|\\[\\]{}<>]*$/',],
			'castrated' => 'nullable',
			'additional_info' => ['nullable', 'max:1000', 'regex:/^[^`~#$^&*=\\|\\[\\]{}<>]*$/',],			
        ], $messages);
		
		
		$address = ['city' => $request->get('city'), 'address' => $request->get('address')];
		
		$phonemore = ['phoneinfo' => $request->get('phoneinfo'), 'phone2' => $request->get('phone2'), 'phoneinfo2' => $request->get('phoneinfo2')];

		// Конвертируем дату
		$date_of_birth = Carbon::parse($request->get('date_of_birth'))->format('Y-m-d');


		// Запись в БД
        if ($validator->passes()) {	

			
			$thereisclient = Clients::where('last_name', $request->get('last_name'))
			->where('first_name', $request->get('first_name'))
			->where('phone', $request->get('phone'))
			->first();
			
			//$data = $validator->valid();
			//$id = Clients::create($data);
			
			if (empty($request->get('client_id')) && $thereisclient) {
				return response()->json(['error'=>'Уже есть такой клиент. Воспользуйтесь поиском.']);
			} else {
				$clientid = Clients::updateOrCreate(['client_id' => $request->get('client_id')], ['last_name'  => $request->get('last_name'),'first_name' => $request->get('first_name'),
				'middle_name' => $request->get('middle_name'),'address' =>  json_encode($address),'phone' => $request->get('phone'),'phonemore' => json_encode($phonemore),
				'email' => $request->get('email'),'data_ready' => $request->get('data_ready'),'comments' => $request->get('comments'),]);
				
				$patientid = Patients::create(['client_id'  => $clientid->client_id,'short_name' => $request->get('short_name'),
				'full_name' => $request->get('full_name'),'sex_id' => $request->get('sex_id'),'animal_type_id' => $request->get('animal_type_id'),
				'breed_id' => $request->get('breed_id'),'color_id' => $request->get('color_id'),'date_of_birth' => $date_of_birth,
				'aprox_date' => $request->get('aprox_date'),'rip' => $request->get('rip'),'tatoo' => $request->get('tatoo'),'chip' => $request->get('chip'),
				'castrated' => $request->get('castrated'),'additional_info' => $request->get('additional_info'),]);
				
				return response()->json(['success'=>'Сохранено', 'id'=>$patientid->patient_id]);
			}

        }
    	//return response()->json(['error'=>$validator->errors()->all()]);
		
		return response()->json(['error'=>$validator->messages()->get('*')]);
	
					
	}
	
	
	public function clientsForSelect(Request $request){
		
		$title = trim($request->q);
		$page = $request->page;
			
		$convertedquery = trim($title);
		$convertedquery = preg_replace('/\s+/', ' ', $convertedquery);
		$convertedquery = explode(" ", $convertedquery);
			
		$last_name = '';
		$first_name = '';
		$middle_name = '';
							
		if (!empty($convertedquery[0])) {
			$last_name = $convertedquery[0];
		}
					
		if (!empty($convertedquery[1])) {
			$first_name = $convertedquery[1];
		}
					
		if (!empty($convertedquery[2])) {
			$middle_name = $convertedquery[2];
		}
							 
		$clients = Clients::when(!empty($last_name), function ($query) use ($last_name) {				
				$query->where('last_name', 'like', '%'.$last_name.'%');})
			->when(!empty($first_name), function ($query) use ($first_name, $last_name) {
				$query->where('last_name', $last_name)->where('first_name', 'like', '%'.$first_name.'%');})
			->when(!empty($middle_name), function ($query) use ($middle_name, $first_name, $last_name) {
				$query->where('last_name', $last_name)->where('first_name', $first_name)->where('middle_name', 'like', '%'.$middle_name.'%');})
			->orWhere('phone', 'like', '%'.$title.'%')
			//->take(50)
			->get();	
		 
		if($clients->count() > 0){
			
			$formatted_types = [];
			
			foreach($clients as $row){
				$fio = $row->last_name.' '.$row->first_name.' '.$row->middle_name;
				$phone = $row->phone;
				$formatted_types[] = ['id' => $row->client_id, 'text' => $fio, 'phone' => $phone];
			}
			
			return \Response::json($formatted_types);
		} else {
			return \Response::json([]);	
		}	
    }
	
	
	
	public function searchOneClient(Request $request){
			
		if($request->get('query') != '') {
							 
			$client = Clients::where('client_id', $request->get('query'))->first();
		}
		
		if ($client) { 
			return response()->json(['success'=>$client]);
		} else {
			return response()->json(['error'=>'Данные не найдены']);
		}
		
    }
	
	
	public function searchOnePatient(Request $request){
			
		if($request->get('query') != '') {
							 
			$patient = Patients::where('patient_id', $request->get('query'))->first();
		}
		
		if ($patient) { 
		
			if (!empty($patient->animal_type_id) & $patient->animal_type_id !== null & $patient->animal_type_id !== '') {
			
				$anymal_type = '';
				if (!empty($patient->animal_type_id)) {
					$anymal_type = Animal_types::where('animal_type_id', $patient->animal_type_id)->first()->type_title;
				}				
				
				$anymal_breed = '';
				if (!empty($patient->breed_id)) {
					$anymal_breed = Breeds::where('breed_id', $patient->breed_id)->first()->breed_title;
				}
				
				$anymal_color = '';
				if (!empty($patient->color_id)) {
					$anymal_color = Colors::where('color_id', $patient->color_id)->first()->color_title;
				}
				
				$age = '';
				$birth_date = '';
				if (!empty($patient->date_of_birth)) {
					$date = Carbon::parse($patient->date_of_birth);
					$now = Carbon::now();	
					$total_years = $date->diffInYears($now);
					$total_monthes = $date ->diffInMonths($now); 
					$last_monthes = $total_monthes - ($total_years * 12); 
						
					if ($patient->rip == 1) {
						$age = '';
					} else {
						$age = $total_years.' л '.$last_monthes.' мес';
					}
					
					$birth_date = Carbon::parse($patient->date_of_birth)->format('d.m.Y');
				}
				
				$registration = '';
				if (!empty($patient->registration_date)) {
					$registration = Carbon::parse($patient->registration_date)->format('d.m.Y');
				}
				
				$last_weight = null;
				$last_weight_date = date(null);
				
				$weights = AnimalWeight::where('anymal_id', $request->get('query'))->get();
				
				if($weights->count() > 0){
					foreach($weights as $row){	
						if ($last_weight_date == null) {
							$last_weight_date = Carbon::parse($row->date_of_visit)->format('d.m.Y');
							$last_weight = number_format($row->weight, 2, '.', ' ');
						} else {							
							if (Carbon::createFromFormat('Y-m-d', $row->date_of_visit)->gte(Carbon::parse($last_weight_date)->format('Y-m-d'))) {						
								$last_weight = number_format($row->weight, 2, '.', ' ');
								$last_weight_date = Carbon::parse($row->date_of_visit)->format('d.m.Y');
							}
						}
					}
				}
				
				// Для подсчета количества
				$weightscount = $weights->unique('date_of_visit');
				
				
				// Последняя вакцинация
				$last_vacine = null;
				$last_vacine_date = date(null);
				
				$onevacine = Vacines::where('patient_id', $request->get('query'))->latest('date_of_vacine')->first();

				if ($onevacine) {
					$last_vacine = $onevacine->vacine_name;
					$last_vacine_date = Carbon::parse($onevacine->date_of_vacine)->format('d.m.Y');
				}
				
				
				// Диагнозы
				$diagnosislist = '';
				
				$diagnosis = DiagnosisVisits::where('anymal_id', $request->get('query'))->where('permanent_id', 1)->get();
				
				if($diagnosis->count() > 0){
					
					foreach($diagnosis as $diagnose){
						
						$onediagnose = DiagnosisTypes::where('id', $diagnose->diagnosis_id)->first();
						
						if ($onediagnose) {
							
							$diagnosislist = $diagnosislist.'<div class="text-break d-flex"><p class="mb-0 mt-0 text-start">'.$onediagnose->diagnosis_title.';</p></div>';
								
						}
						
					}
					
				} else {
					
					$diagnosislist = 'Нет данных';
					
				}
				
			}
			
			return response()->json(['success'=>$patient, 'anymal_type'=>$anymal_type, 'anymal_breed'=>$anymal_breed, 'anymal_color'=>$anymal_color, 'age'=>$age, 'birth_date'=>$birth_date, 'registration'=>$registration, 'last_weight_date'=>$last_weight_date, 'last_weight'=>$last_weight, 'last_weight_size'=>$weightscount->count(), 'diagnosis'=>$diagnosislist, 'last_vacine_date'=>$last_vacine_date, 'last_vacine'=>$last_vacine]);
		} else {
			return response()->json(['error'=>'Данные не найдены']);
		}
		
    }
	
	
	
	public function searchPatients(Request $request){
				
		$output = '';
		
		if (empty($request->short_name) & empty($request->animal_type_id) & empty($request->breed_id)& empty($request->color_id)& empty($request->sex_id)& empty($request->client_id) & empty($request->visit_date_start) & empty($request->visit_date_end) & empty($request->service)) {   
			
			$output = '<div class="d-flex justify-content-center align-items-center mt-3">
						<div class="d-block px-4 py-2">
							Не заданы параметры поиска.
						</div>
					</div>';
					
		} else {
			
			$short = $request->short_name;
			$type = $request->animal_type_id;
			$breed = $request->breed_id;
			$sex = $request->sex_id;
			$client = $request->client_id;
			$service_selected = $request->service_selected;
			$visit_date_start = '';
			
			if (!empty($request->visit_date_start)) {
				$visit_date_start = Carbon::parse($request->visit_date_start)->format('Y-m-d');
			}			
			
			if (!empty($request->visit_date_end)) {
				$visit_date_end = Carbon::parse($request->visit_date_end)->format('Y-m-d');
			} else {
				$visit_date_end = Carbon::now()->format('Y-m-d');;
			}
			
			
			if (!empty($request->visit_date_start) && !empty($request->visit_date_end) && Carbon::createFromFormat('Y-m-d', $visit_date_start)->gt(Carbon::createFromFormat('Y-m-d', $visit_date_end))) {
				return response()->json(['error'=>'Некорректный выбор дат']);
			}


			$patients = Patients::when(!empty($short), function ($query) use ($short) {
					$query->where('short_name', 'like', '%'.$short.'%');})
				->when(!empty($type) & $type != 'drugoe', function ($query) use ($type) {
					$query->where('animal_type_id', $type);})
				->when(!empty($type) & $type == 'drugoe', function ($query) use ($type) {
					$query->where('animal_type_id', '!=', 1)->where('animal_type_id', '!=', 2);})
				->when(!empty($breed), function ($query) use ($breed) {	
					if ($breed == 'notexist') {
						$query->whereNull('breed_id')->orWhere('breed_id', '')->orWhere('breed_id', 0);
					} else {
						$query->where('breed_id', $breed);
					}
				})			
				->when(!empty($sex), function ($query) use ($sex) {
					$query->where('sex_id', $sex);})				
				->when(!empty($client), function ($query) use ($client) {					
					$query->where('client_id', $client)
				;})	
				// Поиск в таблице Patients по значениям из таблицы Clients через связку по client_id
				/*->when(!empty($client), function ($query) use ($client) {					
					$query->join('clients','patients.client_id','clients.client_id')
					->where('clients.client_id', $client)
					//->orWhere('last_name', 'like', '%'.$client.'%')
					//->orWhere('phone', 'like', '%'.$client.'%')
					
					// Пример поиска по значению JSON (только целиком)
					//->whereJsonContains('address', ['address' => $query])
					//->whereJsonContains('address->address', $query)
				;})*/	
				->when(!empty($request->visit_date_start), function ($query) use ($visit_date_start, $visit_date_end) {										
					$query->join('visits','patients.patient_id','visits.patient_id')
					->whereBetween('visits.date_of_visit', [$visit_date_start, $visit_date_end])
				;})
				->when(empty($request->visit_date_start) & !empty($request->visit_date_end), function ($query) use ($visit_date_end) {										
					$query->join('visits','patients.patient_id','visits.patient_id')
					->whereDate('visits.date_of_visit', '<=', $visit_date_end)
				;})
								
				->when(!empty($request->service) & $request->service != 0, function ($query) use ($visit_date_end) {										
					$query->join('bills', function ($join) {
						$join->on('patients.patient_id', '=', 'bills.patient_id');
					})	
				;})
				
				//->take(50)
				->get();
				
				
			// Убираем повторяющиеся записи после поиска по датам приема
			$patients = $patients->unique('patient_id');
			
			
			if($patients->count() > 0){
				
				$output = '
				
					<style>
					
						.table-title{
							font-size:16px;
							font-weight: 600;
						}
						
						.table-title-notlg{
							font-size:16px;
							font-weight: 600;
							margin-right: 10px;
						}
						
						.table-text{
							font-size:16px;
						}
						
						.table-text-phone{
							font-size:16px;
							font-style: italic !important;
						}
						
						.morethen50-text{
							font-size:16px;
							font-style: italic !important;
						}
						
						.hoverDiv {
							background: #fff;
						}
						
						.hoverDiv:hover {
							background: #f5f5f5;
						}

					</style>
					
					<div class="">
					
					<div class="d-none d-lg-block border-bottom mt-3">
					<div class="row justify-content-center mx-2 py-2">
						<div class="table-title text-primary col-1 px-2 justify-content-center d-flex align-items-center text-break">ID</div>
						<div class="table-title text-primary col px-2 justify-content-center d-flex align-items-center text-break">ВЛАДЕЛЕЦ</div>
						<div class="table-title text-primary col px-2 justify-content-center d-flex align-items-center text-break">КЛИЧКА</div>
						<div class="table-title text-primary col px-2 justify-content-center d-flex align-items-center text-break">ВИД</div>
						<div class="table-title text-primary col px-2 justify-content-center d-flex align-items-center text-break">ПОРОДА</div>
						<div class="table-title text-primary col-1 px-2 justify-content-center d-flex align-items-center text-break">ПОЛ</div>
						<div class="table-title text-primary col px-2 justify-content-center d-flex align-items-center text-break">ВОЗРАСТ</div>					
					</div>
					</div>
				';
				
								
				// Для проверки наличия пациентов после проверки JSON
				$thereis_patients = false;	
			
				$patients_count = 0;
				
				
				foreach($patients as $row){
					
					// Фильтр услуг
					$thereis_service = false;
					
					// Получить счета по patient_id, проверить что они есть и перебрать циклом !!!
					
					$bills = Bills::where('patient_id', $row->patient_id)->get();
					
					if($bills->count() > 0){
						
						foreach($bills as $bill){
							
							if ($bill->service_text != '') {

								$servises_json = json_decode($bill->service_text);
								
								foreach((array) $servises_json as $service){
									
									if ($request->service != 0 & $service->service_name_todb == $service_selected) {
										$thereis_service = true;							
									}	
								}
							}
							
						}						
					}					
					
					// Поиск по значению в JSON, т.к. MySQL ниже 5.7 не поддерживает работу с JSON
					if (($request->service == 0) | ($request->service != 0 & $thereis_service)) {

				
						// Для проверки наличия счетов после проверки JSON
						$thereis_patients = true;
					
									
						// Пример поиска по значению в JSON, т.к. MySQL ниже 5.7 не поддерживает работу с JSON
						//if (Str::contains(Str::lower(json_decode($row->address)->address), Str::lower($query))) {}

						$sexresult = '';
				
						if($row->sex_id == 1){
							$sexresult = '<img class="img-responsive" width="25" height="25" src="images/male1.png">';
						} else if($row->sex_id == 2) {
							$sexresult = '<img class="img-responsive" width="25" height="25" src="images/female1.png">';
						} 
						
						$clientname = '';
						$client_data = Clients::where('client_id', $row->client_id)->first();
						$clientname = $client_data->last_name.' '.$client_data->first_name.' '.$client_data->middle_name;
						
						$clientphone = '';
						$clientphone = $client_data->phone;
						
						$rip = $row->rip;
						$stylerip = '';
						if ($rip == 1){
							$stylerip = 'border border-2 border-dark p-1 rounded';
						} else {
							$stylerip = '';
						}
						
						
						$anymal_type = '';
						if (!empty($row->animal_type_id) & $row->animal_type_id !== null & $row->animal_type_id !== '') {
							$type = Animal_types::where('animal_type_id', $row->animal_type_id)->first();
							if ($type) {
								$anymal_type = $type->type_title;
							}
						}
						
						$anymal_breed = '';
						if (!empty($row->breed_id) & $row->breed_id !== null & $row->breed_id !== '') {
							$breed = Breeds::where('breed_id', $row->breed_id)->first();
							if ($breed) {
								$anymal_breed = $breed->breed_title;
							}
						}
						
						$anymal_color = '';
						if (!empty($row->color_id) & $row->color_id !== null & $row->color_id !== '') {
							$color = Colors::where('color_id', $row->color_id)->first();
							if ($color) {
								$anymal_color = $color->color_title;
							}
						}
						
						$age = '';
						if (!empty($row->date_of_birth)) {
							$date = Carbon::parse($row->date_of_birth);
							$now = Carbon::now();
							$total_years = $date->diffInYears($now);
							$total_monthes = $date ->diffInMonths($now); 
							$last_monthes = $total_monthes - ($total_years * 12); 

								
							if ($row->rip == 1) {
								$age = '';
							} else {
								$age = $total_years.' л '.$last_monthes.' мес';
							}
						}
											
						if ($row->aprox_date == 1 & $age !== '') {
							$aprox_age = ' ?';
						} else {
							$aprox_age = '';
						}
						
						
						$output = $output.'
																

							<a href="" onclick="open_client_card('.$row->patient_id.'); return false;" class="text-decoration-none block leading-5 text-gray-700 transition duration-150 ease-in-out hover:bg-gray-100 focus:outline-none focus:bg-gray-100 p-0">

								<div class="hoverDiv border-bottom">

									<div class="row justify-content-center mx-2 py-2">
											
										<div class="col-lg-1 px-0 px-lg-2 py-1 d-flex justify-content-lg-center align-items-lg-center" align="center">
											<div class="d-flex d-lg-none text-primary table-title-notlg align-self-center">ID:</div>
											<div class="table-text text-body text-break align-self-center text-start text-lg-center">'.$row->patient_id.'</div>
										</div>
										
										<div class="col-lg px-0 px-lg-2 py-1 d-flex justify-content-lg-center align-items-lg-center" align="center">
											<div class="d-flex d-lg-none text-primary table-title-notlg align-self-center">ВЛАДЕЛЕЦ:</div>
										
											<div class="flex-none text-break">
												<div class="table-text d-flex justify-content-lg-center text-body text-break mt-1 align-self-center text-start text-lg-center">'.$clientname.'</div>
												<div class="table-text-phone d-flex justify-content-lg-center text-primary text-break align-self-center mt-1 text-start text-lg-center">'.$clientphone.'</div>
											</div>	
																				
										</div>
													
										<div class="col-lg px-0 px-lg-2 py-1 d-flex justify-content-lg-center align-items-lg-center" align="center">
											<div class="d-flex d-lg-none text-primary table-title-notlg align-self-center">КЛИЧКА:</div>
											<div class="flex-none text-break">
												<div class="d-flex justify-content-lg-center position-relative align-items-center" align="center">	
													<div class="table-text d-flex justify-content-lg-center text-body text-break mt-1 align-self-center '.$stylerip.' text-start text-lg-center">'.$row->short_name.'</div>
												</div>
												<div class="table-text-phone d-flex justify-content-lg-center text-break align-self-center mt-1 text-danger text-start text-lg-center">'.$row->additional_info.'</div>										
											</div>
										</div>
										
										<div class="col-lg px-0 px-lg-2 py-1 d-flex justify-content-lg-center align-items-lg-center" align="center">
											<div class="d-flex d-lg-none text-primary table-title-notlg align-self-center">ВИД:</div>
											<div class="table-text text-body text-break align-self-center text-start text-lg-center">'.$anymal_type.'</div>
										</div>
										<div class="col-lg px-0 px-lg-2 py-1 d-flex justify-content-lg-center align-items-lg-center" align="center">
											<div class="d-flex d-lg-none text-primary table-title-notlg align-self-center">ПОРОДА:</div>
											<div class="table-text text-body text-break align-self-center text-start text-lg-center">'.$anymal_breed.'</div>
										</div>
										<div class="col-lg-1 px-0 px-lg-2 py-1 d-flex justify-content-lg-center align-items-lg-center" align="center">
											<div class="d-flex d-lg-none text-primary table-title-notlg align-self-center text-start text-lg-center">ПОЛ:</div>	
												'.$sexresult.'	
										</div>
										<div class="col-lg px-0 px-lg-2 py-1 d-flex justify-content-lg-center align-items-lg-center" align="center">
											<div class="d-flex d-lg-none text-primary table-title-notlg align-self-center">ВОЗРАСТ:</div>
											<div class="table-text text-body text-break align-self-center">'.$age.'<span class="text-danger fw-bold fs-6 text-start text-lg-center">'.$aprox_age.'</span></div>
										</div>

									</div>
		
								</div>
					
							</a>
						
						';
						
						
						$patients_count++;
					
						if ($patients_count >= 50) {
							break;
						}
						
						
					}
				}
				
				
				if($patients_count >= 50){
					$output = $output.'
					<div class="d-block px-4 py-4 morethen50-text table-text-phone text-danger">
					* Найдено более 50 значений, показаны только первые 50. Уточните параметры поиска.
					</div>
					';
				}
				
				
				// Проверка наличия счетов после проверки JSON
				if (!$thereis_patients) {
					
					$output = '		
					<div class="d-flex justify-content-center align-items-center mt-3">
						<div class="d-block px-4 py-2">
							Данные не найдены.
						</div>
					</div>
				
					<div class="d-flex justify-content-center align-items-center mt-1">
						<div class="p-2"><button type="button" onclick="add_animal()" class="btn btn-secondary p-2" id="NoClient">Добавить</button></div>
					</div>';
				
				}
				
				

				/*if($patients->count() >= 50){
					$output = $output.'
					<div class="d-block px-4 py-4 morethen50-text table-text-phone text-danger">
					* Найдено более 50 значений, показаны только первые 50. Уточните параметры поиска.
					</div>
					';
				}*/	
				
			} else {
				$noid = 'noid';
				$output = '		
					<div class="d-flex justify-content-center align-items-center mt-3">
						<div class="d-block px-4 py-2">
							Данные не найдены.
						</div>
					</div>
				
					<div class="d-flex justify-content-center align-items-center mt-1">
						<div class="p-2"><button type="button" onclick="add_animal()" class="btn btn-secondary p-2" id="NoClient">Добавить</button></div>
					</div>';
			}
	
		}
		
		return response()->json(['success'=>$output]);
		
    }
	
	
	
	public function changePatientData(Request $request){
								
		// Тексты ошибок
		$messages = [
			'short_name.required' => 'Не заполнено поле',
			'short_name.regex' => 'Поле содержит недопустимые символы',
			'full_name.regex' => 'Поле содержит недопустимые символы',
			'animal_type_id.required' => 'Не выбрано значение',
			'breed_id.required' => 'Не выбрано значение',
			//'color_id.required' => 'Не выбрано значение',
			'date_of_birth.required' => 'Не заполнено поле',
			'date_of_birth.date_format' => 'Неверный формат даты',
			'tatoo.unique' => 'Значение уже существует',
			'tatoo.regex' => 'Поле содержит недопустимые символы',
			'chip.unique' => 'Значение уже существует',
			'chip.regex' => 'Поле содержит недопустимые символы',
			'additional_info.regex' => 'Поле содержит недопустимые символы'		
		];

		
		// Валидация на ошибки в полях
		$validator = Validator::make($request->all(), [			
			'short_name' => ['required', 'max:255', 'regex:/^[^`~#$^&*=\\|\\[\\]{}<>]*$/',],
			'full_name' => ['nullable', 'max:255', 'regex:/^[^`~#$^&*=\\|\\[\\]{}<>]*$/',],
			'animal_type_id' => 'required',
			'breed_id' => 'required',
			//'color_id' => 'required',
			'date_of_birth' => ['required', 'date_format:d.m.Y',],
			'aprox_date' => 'nullable',
			'rip' => 'nullable',
			'tatoo' => ['nullable', 'max:255', 'regex:/^[^`~#$^&*=\\|\\[\\]{}<>]*$/',],
            'chip' => ['nullable', 'max:255', 'regex:/^[^`~#$^&*=\\|\\[\\]{}<>]*$/',],
			'castrated' => 'nullable',
			'additional_info' => ['nullable', 'max:1000', 'regex:/^[^`~#$^&*=\\|\\[\\]{}<>]*$/',],			
        ], $messages);
		
	
		// Конвертируем дату
		$date_of_birth = Carbon::parse($request->get('date_of_birth'))->format('Y-m-d');


		// Запись в БД
        if ($validator->passes()) {	
		
			if ($request->get('patient_id') !== null & $request->get('patient_id') !== '0') {
			
				Patients::where('patient_id', $request->get('patient_id'))->update(['short_name' => $request->get('short_name'),
				'full_name' => $request->get('full_name'),'sex_id' => $request->get('sex_id'),'animal_type_id' => $request->get('animal_type_id'),
				'breed_id' => $request->get('breed_id'),'color_id' => $request->get('color_id'),'date_of_birth' => $date_of_birth,
				'aprox_date' => $request->get('aprox_date'),'rip' => $request->get('rip'),'tatoo' => $request->get('tatoo'),'chip' => $request->get('chip'),
				'castrated' => $request->get('castrated'),'additional_info' => $request->get('additional_info'),]);
				
				return response()->json(['success'=>'Данные изменены']);
			
			} else {
			
				return response()->json(['error'=>'Ошибка изменения данных, перезагрузите страницу']);
			
			}

        } 
		
		return response()->json(['error'=>$validator->messages()->get('*')]);
	
					
	}
	
	
	
	public function deletePatient(Request $request){
										
		if ($request->get('patient_id') !== null & $request->get('patient_id') !== '0') {
		
			Patients::destroy($request->get('patient_id'));
						
			AnimalWeight::where('anymal_id', $request->get('patient_id'))->delete();
			
			DiagnosisVisits::where('anymal_id', $request->get('patient_id'))->delete();
			
			Researches::where('patient_id', $request->get('patient_id'))->delete();
			
			Analysis::where('patient_id', $request->get('patient_id'))->delete();
			
			Vacines::where('patient_id', $request->get('patient_id'))->delete();
			
			Bills::where('patient_id', $request->get('patient_id'))->delete();

			Pays::where('patient_id', $request->get('patient_id'))->delete();
			
			
			UploadedPhoto::where('anymal_id', $request->get('patient_id'))->delete();
			
			$folder_path = public_path('uploaded_photos/'.$request->get('patient_id'));
			\File::deleteDirectory($folder_path);
						
			Visits::where('patient_id', $request->get('patient_id'))->delete();
					
			return response()->json(['success'=>'Удачно удалено']);
		
		} else {
		
			return response()->json(['error'=>'Ошибка удаления, перезагрузите страницу и попробуйте еще раз']);
		
		}
					
	}
	
	
	public function changeClientData(Request $request){
								
		// Тексты ошибок
		$messages = [
			// По клиенту
			'last_name.required' => 'Не заполнено поле',
			'last_name.regex' => 'Поле содержит недопустимые символы',
			'first_name.required' => 'Не заполнено поле',
			'first_name.regex' => 'Поле содержит недопустимые символы',
			'middle_name.regex' => 'Поле содержит недопустимые символы',
			'phone.unique' => 'Значение уже существует',
			'phone.regex' => 'Поле содержит недопустимые символы',
			'city.regex' => 'Поле содержит недопустимые символы',
			'address.regex' => 'Поле содержит недопустимые символы',
			'phoneinfo.regex' => 'Поле содержит недопустимые символы',
			'phone2.regex' => 'Поле содержит недопустимые символы',
			'phoneinfo2.regex' => 'Поле содержит недопустимые символы',
			'email.regex' => 'Поле содержит недопустимые символы',
			'comments.regex' => 'Поле содержит недопустимые символы'	
		];
		
	
		// Валидация на ошибки в полях
		$validator = Validator::make($request->all(), [		
			// По клиенту
			'last_name' => ['required', 'max:255', 'regex:/^[^`~#$^&*=\\|\\[\\]{}<>]*$/',],  
			'first_name' => ['required', 'max:255', 'regex:/^[^`~#$^&*=\\|\\[\\]{}<>]*$/',],
			'middle_name' => ['nullable', 'max:255', 'regex:/^[^`~#$^&*=\\|\\[\\]{}<>]*$/',],
			'phone' => ['nullable', 'max:255', 'regex:/^[^`~#$^&*=\\|\\[\\]{}<>]*$/',],
			'city' => ['nullable', 'max:255', 'regex:/^[^`~#$^&*=\\|\\[\\]{}<>]*$/',],	
			'address' => ['nullable', 'max:255', 'regex:/^[^`~#$^&*=\\|\\[\\]{}<>]*$/',],	
			'phoneinfo' => ['nullable', 'max:255', 'regex:/^[^`~#$^&*=\\|\\[\\]{}<>]*$/',],	
			'phone2' => ['nullable', 'max:255', 'regex:/^[^`~#$^&*=\\|\\[\\]{}<>]*$/',],	
			'phoneinfo2' => ['nullable', 'max:255', 'regex:/^[^`~#$^&*=\\|\\[\\]{}<>]*$/',],				
			'email' => ['nullable', 'max:255', 'regex:/^[^`~#$^&*=\\|\\[\\]{}<>]*$/',],	
			'comments' => ['nullable', 'max:1000', 'regex:/^[^`~#$^&*=\\|\\[\\]{}<>]*$/',],				
        ], $messages);

	
		$address = ['city' => $request->get('city'), 'address' => $request->get('address')];
		
		$phonemore = ['phoneinfo' => $request->get('phoneinfo'), 'phone2' => $request->get('phone2'), 'phoneinfo2' => $request->get('phoneinfo2')];


		// Запись в БД
        if ($validator->passes()) {	
		
			if ($request->get('owner_id') !== null & $request->get('owner_id') !== '0') {
				
				Clients::where('client_id', $request->get('owner_id'))->update(['last_name'  => $request->get('last_name'),'first_name' => $request->get('first_name'),
				'middle_name' => $request->get('middle_name'),'address' =>  json_encode($address),'phone' => $request->get('phone'),'phonemore' => json_encode($phonemore),
				'email' => $request->get('email'),'data_ready' => $request->get('data_ready'),'comments' => $request->get('comments'),]);
				
				return response()->json(['success'=>'Данные изменены']);
			
			} else {
			
				return response()->json(['error'=>'Ошибка изменения данных, перезагрузите страницу']);
			
			}

        } 
		
		return response()->json(['error'=>$validator->messages()->get('*')]);
		
	}
	
	
	
	public function newClient(Request $request){
		
								
		// Тексты ошибок
		$messages = [
			// По клиенту
			'last_name_2.required' => 'Не заполнено поле',
			'last_name_2.regex' => 'Поле содержит недопустимые символы',
			'first_name_2.required' => 'Не заполнено поле',
			'first_name_2.regex' => 'Поле содержит недопустимые символы',
			'middle_name_2.regex' => 'Поле содержит недопустимые символы',
			'phone_2.unique' => 'Значение уже существует',
			'phone_2.regex' => 'Поле содержит недопустимые символы',
			'city_2.regex' => 'Поле содержит недопустимые символы',
			'address_2.regex' => 'Поле содержит недопустимые символы',
			'phoneinfo_2.regex' => 'Поле содержит недопустимые символы',
			'phone2_2.regex' => 'Поле содержит недопустимые символы',
			'phoneinfo2_2.regex' => 'Поле содержит недопустимые символы',
			'email_2.regex' => 'Поле содержит недопустимые символы',
			'comments_2.regex' => 'Поле содержит недопустимые символы'		
		];
		
		// Валидация на ошибки в полях
		$validator = Validator::make($request->all(), [		
			// По клиенту
			'last_name_2' => ['required', 'max:255', 'regex:/^[^`~#$^&*=\\|\\[\\]{}<>]*$/',],  
			'first_name_2' => ['required', 'max:255', 'regex:/^[^`~#$^&*=\\|\\[\\]{}<>]*$/',],
			'middle_name_2' => ['nullable', 'max:255', 'regex:/^[^`~#$^&*=\\|\\[\\]{}<>]*$/',],
			'phone_2' => ['nullable', 'max:255', 'regex:/^[^`~#$^&*=\\|\\[\\]{}<>]*$/',],
			'city_2' => ['nullable', 'max:255', 'regex:/^[^`~#$^&*=\\|\\[\\]{}<>]*$/',],	
			'address_2' => ['nullable', 'max:255', 'regex:/^[^`~#$^&*=\\|\\[\\]{}<>]*$/',],	
			'phoneinfo_2' => ['nullable', 'max:255', 'regex:/^[^`~#$^&*=\\|\\[\\]{}<>]*$/',],	
			'phone2_2' => ['nullable', 'max:255', 'regex:/^[^`~#$^&*=\\|\\[\\]{}<>]*$/',],	
			'phoneinfo2_2' => ['nullable', 'max:255', 'regex:/^[^`~#$^&*=\\|\\[\\]{}<>]*$/',],				
			'email_2' => ['nullable', 'max:255', 'regex:/^[^`~#$^&*=\\|\\[\\]{}<>]*$/',],	
			'comments_2' => ['nullable', 'max:1000', 'regex:/^[^`~#$^&*=\\|\\[\\]{}<>]*$/',],			
        ], $messages);
		
		
		$address = ['city' => $request->get('city_2'), 'address' => $request->get('address_2')];
		
		$phonemore = ['phoneinfo' => $request->get('phoneinfo_2'), 'phone2' => $request->get('phone2_2'), 'phoneinfo2' => $request->get('phoneinfo2_2')];


		// Запись в БД
        if ($validator->passes()) {	
		
			if ($request->get('patient_id') !== null & $request->get('patient_id') !== '0') {
				
				$clientid = Clients::create(['last_name'  => $request->get('last_name_2'),'first_name' => $request->get('first_name_2'),
				'middle_name' => $request->get('middle_name_2'),'address' =>  json_encode($address),'phone' => $request->get('phone_2'),'phonemore' => json_encode($phonemore),
				'email' => $request->get('email_2'),'data_ready' => $request->get('data_ready_2'),'comments' => $request->get('comments_2'),]);
				
				Patients::where('patient_id', $request->get('patient_id'))->update(['client_id'  => $clientid->client_id,]);
				
				return response()->json(['success'=>'Сохранено']);
			
			} else {
			
				return response()->json(['error'=>'Ошибка записи данных, перезагрузите страницу']);
			
			}

        } 
		
		return response()->json(['error'=>$validator->messages()->get('*')]);
		
	}
	
	
	public function changeClient(Request $request){
								
		
		// Тексты ошибок
		$messages = [
			// По клиенту
			'client_id.required' => 'Не выбрано значение'		
		];
		
		// Валидация на ошибки в полях
		$validator = Validator::make($request->all(), [		
			// По клиенту
			'client_id' => 'required',				
        ], $messages);
		
		

		// Запись в БД
        if ($validator->passes()) {	
		
			if ($request->get('patient_id') !== null & $request->get('patient_id') !== '0') {
				
				Patients::where('patient_id', $request->get('patient_id'))->update(['client_id'  => $request->get('client_id'),]);
				
				return response()->json(['success'=>'Сохранено']);
			
			} else {
			
				return response()->json(['error'=>'Ошибка записи данных, перезагрузите страницу']);
			
			}

        }
		
		return response()->json(['error'=>$validator->messages()->get('*')]);
		
	}
	
		
	/*public function saveDataToSession(Request $request){
		
		$request->session()->put('short_name', $request->short_name);		
		// Запись в файл
		Session::save();		
		return response()->json(['success'=>'Сохранено']);
 		
    }*/
	
	
	/*public function getDataFromSession(Request $request){
				
		//$value = $request->session()->pull('short_name', '');		
		//return response()->json(['success'=>$value]);
				
		return response([
			'sessionData' => session()->all()
		]);
 		
    }*/
	
	
	/*public function destroyDataFromSession(Request $request){
				
		$request->session()->forget('short_name');
		
		// Запись в файл
		Session::save();
		
		// Очистка cash в сессии
		Artisan::call('cache:clear');
 		
    }*/
	
	
	public function showBillsPage()
    {
        return view('bills_page');
    }
	
}
