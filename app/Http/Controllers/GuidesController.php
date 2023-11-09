<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Animal_types;
use App\Breeds;
use App\Colors;
use App\Patients;
use App\Clients;
use App\ExpirationDate;
use App\DiagnosisTypes;
use App\VacinesTypes;
use App\Products;
use App\ProductCategory;
use App\Services;
use App\ServiceCategory;
use App\Templates;
use App\AnalysisTemplates;
use App\Analysis;
use App\Visits;
use Validator;
use Illuminate\Validation\Rule;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Illuminate\Contracts\Encryption\DecryptException;
use DB;

class GuidesController extends Controller
{
	
	/*public function __construct()
    {
        $this->middleware('auth');
    }*/
	
	
    public function showGuidesForm()
    {
        return view('data-guides');
    }
	
		
	public function importAnimaltypes(){
			
		$jsondata = file_get_contents('dbimport/jivotni.json');
		$data = json_decode($jsondata, true);
	
		foreach ($data[2]['data'] as $row) {
					
			// Замена символов на маленькие с большой первой (+ удаление лишних пробелов в начале и конце строки)
			$convertedstring = Str::ucfirst(Str::lower(trim($row['imevid_'])));
				
			$insertData = array("type_title"=>$convertedstring, "old_id"=>$row['NVIDJ_']);
				   
			// Раскомментировать для записи только уникальных значений
			//if (!Animal_types::where('type_title', '=', $insertData)->exists()) {
				Animal_types::create($insertData);
			//} 	
		}
		  
		  return response()->json(['success'=>'Импорт завершен']);		
	}
	
	
	public function addNewAnimaltype(Request $request){
					
		// Тексты ошибок
		$messages = [
			'type_title.required' => 'Не заполнено поле',
			'type_title.unique' => 'Значение уже существует',
			'type_title.regex' => 'Поле содержит недопустимые символы'			
		];
			
		$idd = $request->get('id');
			
		unset($request['id']);
			
		// Валидация на ошибки в полях
		$validator = Validator::make($request->all(), [			
			'type_title' => ['required', 'max:255', 'regex:/^[^`~#$^&*=\\|\\[\\]{}<>]*$/', Rule::unique('animal_types')->ignore($idd, 'animal_type_id'),],  // /[-`~!#$%^&*()_=+\\\\|\\/\\[\\]{};:"\',<>?]+/'
		], $messages);
		
		
		// Запись в БД
		if ($validator->passes()) {			
			$data = $validator->valid();
			Animal_types::updateOrCreate(['animal_type_id' => $idd], $data);
			return response()->json(['success'=>'Cохранено']);	
		}
			
		return response()->json(['error'=>$validator->messages()->get('*')]);
	
	}
	
	
	
	public function searchAnimalTypes(Request $request){
		
        $formatted_types = [];
		
		if ($request->page !== 'main') {
			$types = Animal_types::all();			
		}	else {	
			$types = Animal_types::take(2)->get();
		}
       
		if($types->count() > 0){
		
			foreach ($types as $type) {
				$formatted_types[] = ['id' => $type->animal_type_id, 'text' => $type->type_title];
			}
			
			if ($request->page == 'main') {
				$formatted_types[] = ['id' => 'drugoe', 'text' => 'Другое'];
			}

			return \Response::json($formatted_types);
		} else {	
			return \Response::json([]);
		}	
			
    }
	
	
	
	public function importAnimalbreeds(){
		
		$jsondata = file_get_contents('dbimport/porodi.json');
		$data = json_decode($jsondata, true);

		foreach ($data[2]['data'] as $row) {
			
			if (!empty($row['PORODA_']) & $row['PORODA_'] != '' & $row['VID_'] != 0 & $row['VID_'] != '0') {
			
				// Получаем новый ID типа животных из наших данных по старому ID из импорта
				$anymaltype = Animal_types::where('old_id', $row['VID_'])->first();  //VID_
				
				$animaltypeid = null;
				
				if ($anymaltype) {
					$animaltypeid = $anymaltype->animal_type_id;
				}
				
				// Замена символов на маленькие с большой первой (+ удаление лишних пробелов в начале и конце строки)
				$convertedstring = Str::ucfirst(Str::lower(trim($row['PORODA_'])));
				  
				$insertData = array("breed_title"=>$convertedstring,'animal_type_id'=>$animaltypeid, "old_id"=>$row['NPO_']);
				   
				// Раскомментировать для записи только уникальных значений
				//if (!Breeds::where('breed_title', '=', $insertData)->exists()) {
					Breeds::create($insertData);
				//} 
			}
			
		}
		  
		return response()->json(['success'=>'Импорт завершен']);		
	}
	
	
	
	public function addNewAnimalbreed(Request $request){
					
		// Тексты ошибок
		$messages = [
			'breed_title.required' => 'Не заполнено поле',
			'breed_title.unique' => 'Значение уже существует',
			'breed_title.regex' => 'Поле содержит недопустимые символы',
			'animal_type_id.required' => 'Не выбрано значение'
			
		];
			
		$idd = $request->get('id');	
		$anymtype = $request->get('animal_type_id');
			
		unset($request['id']);
			
		// Валидация на ошибки в полях
		$validator = Validator::make($request->all(), [	
			'animal_type_id' => 'required',
			'breed_title' => ['required', 'max:255', 'regex:/^[^`~#$^&*=\\|\\[\\]{}<>]*$/', Rule::unique('breeds')->where(static function ($q) use ($anymtype) { return $q->where('animal_type_id', $anymtype); })->ignore($idd, 'breed_id'),],			
		], $messages);
		
		// Запись в БД
		if ($validator->passes()) {			
			$data = $validator->valid();
			Breeds::updateOrCreate(['breed_id' => $idd], $data);
			return response()->json(['success'=>'Cохранено']);	
		} 
		
		return response()->json(['error'=>$validator->messages()->get('*')]);
	
	}
	
	
	// Получение пород для select2 (ajax)
	public function searchAnimalBreeds(Request $request){
		
		$title = trim($request->q);
		$animal = $request->animal;
		$page = $request->page;
		

		//$formatted_breeds[] = ['id' => '1', 'text' => $animal];
		//return \Response::json($formatted_breeds);

        if (empty($title) & !empty($animal)) {
			$breeds = Breeds::where('animal_type_id', $animal)
			->get();
        }  if (empty($title) & !empty($animal) & $animal == 'drugoe') {
			$breeds = Breeds::where('animal_type_id', '!=', 1)
			->where('animal_type_id', '!=', 2)
			->get();
        } else if ($page == 'main' & !empty($animal) & $animal == 'drugoe') {
			$breeds = Breeds::where('breed_title', 'like', '%'.$title.'%')
			->where('animal_type_id', '!=', 1)
			->where('animal_type_id', '!=', 2)
			->get();
		} else if ($page == 'main' & empty($animal)) {
			$breeds = Breeds::where('breed_title', 'like', '%'.$title.'%')
			->get();
		} else {
			$breeds = Breeds::where('breed_title', 'like', '%'.$title.'%')
			->where('animal_type_id', $animal)
			->get();
		}
		
        $formatted_breeds = [];
		
		if($breeds->count() > 0){
			
			if ($page == 'main') {
			$formatted_breeds[] = ['id' => 'notexist', 'text' => 'Не указано'];
			}

			foreach ($breeds as $breed) {
				$formatted_breeds[] = ['id' => $breed->breed_id, 'text' => $breed->breed_title];
			}

			return \Response::json($formatted_breeds);
		
		} else {	
			return \Response::json([]);
		}
		
    }
	
	
	public function importAnimalcolors(){
			
		$jsondata = file_get_contents('dbimport/colors.json');
		$data = json_decode($jsondata, true);
		
		foreach ($data[2]['data'] as $row) {
			
			// Замена символов на маленькие с большой первой (+ удаление лишних пробелов в начале и конце строки)
			$convertedstring = Str::ucfirst(Str::lower(trim($row['COLOR_'])));
				
			$insertData = array("color_title"=>$convertedstring, "old_id"=>$row['id_']);
				   
			// Раскомментировать для записи только уникальных значений
			//if (!Colors::where('color_title', '=', $insertData)->exists()) {
				Colors::create($insertData);
			//} 
			
		}
		  
		  return response()->json(['success'=>'Импорт завершен']);				
	}
	
	
	
	public function addNewAnimalcolor(Request $request){
					
		// Тексты ошибок
		$messages = [
			'color_title.required' => 'Не заполнено поле',
			'color_title.unique' => 'Значение уже существует',
			'color_title.regex' => 'Поле содержит недопустимые символы'				
		];
			
		$idd = $request->get('id');
			
		unset($request['id']);
			
		// Валидация на ошибки в полях
		$validator = Validator::make($request->all(), [			
			'color_title' => ['required', 'max:255', 'regex:/^[^`~#$^&*=\\|\\[\\]{}<>]*$/', Rule::unique('colors')->ignore($idd, 'color_id'),],
		], $messages);
		
		
		// Запись в БД
		if ($validator->passes()) {			
			$data = $validator->valid();
			Colors::updateOrCreate(['color_id' => $idd], $data);
			return response()->json(['success'=>'Cохранено']);	
		}
			
		return response()->json(['error'=>$validator->messages()->get('*')]);
	
	}
	
	
	public function searchAnimalColors(Request $request){
		
        $colors = Colors::all();

        $formatted_colors = [];
       
		if($colors->count() > 0){
		
			foreach ($colors as $color) {
				$formatted_colors[] = ['id' => $color->color_id, 'text' => $color->color_title];
			}

			return \Response::json($formatted_colors);
		} else {	
			return \Response::json([]);
		}		
    }
	
	
	public function importPatientsandClients(){
				
		$jsondata = file_get_contents('dbimport/paci_d.json');
		$data = json_decode($jsondata, true);
			
		// Для теста
		$count = 0;
			
		foreach ($data[2]['data'] as $row) {
					
			if ($row['zvjar_'] != '1' & $row['zvjar_'] != '3' & $row['zvjar_'] != '89024748731' & $row['zvjar_'] != '3 котенка' & $row['zvjar_'] != '5 котов' & $row['zvjar_'] != 'ko' & $row['zvjar_'] != 'а' & $row['zvjar_'] != 'А' & !empty($row['klient_'])) {
								
				// Старый ID
				$patient_old_id = $row['NOMER_'];
				
				// Тип животного
				$old_anymal_type = null;
					
				if ($row['VID_'] == 0) {	
					if ($row['POL_'] == 'Кот' | $row['POL_'] == 'Кошка' | $row['POL_'] == 'Стерилизированная') {
						$old_anymal_type = 2;
					} else {	
						$old_anymal_type = 1;
					}
				// Неверный тип животного - кошки в собаках
				} else if ($patient_old_id == 510 | $patient_old_id == 1468) {
					$old_anymal_type = 2;
				// Неверный тип животного - собаки в кошках
				} else if ($patient_old_id == 26 | $patient_old_id == 1345 | $patient_old_id == 2409 | $patient_old_id == 2831 | $patient_old_id == 2908 | $patient_old_id == 3544 | $patient_old_id == 3549 
				| $patient_old_id == 3584 | $patient_old_id == 4268 | $patient_old_id == 5354 | $patient_old_id == 7335) {
					$old_anymal_type = 1;
				} else {
					$old_anymal_type = $row['VID_'];	
				}
				
				$anymaltype = Animal_types::where('old_id', $old_anymal_type)->first();
				
				$patient_animal_type = null;
				
				if ($anymaltype) {
					$patient_animal_type = $anymaltype->animal_type_id;
				}
				
				// Кличка
				$patient_short_name = trim($row['zvjar_']);
				
				// Ф.И.О.				
				$convertedfio = trim($row['klient_']);
				$convertedfio = preg_replace('/\s+/', ' ', $convertedfio);
				
				$convertedfio = explode(" ", $convertedfio);
						
				$patient_last_name = '';
				$patient_first_name = '';
				$patient_middle_name = '';
				
				if (!empty($convertedfio[0])) {
					$patient_last_name = $convertedfio[0];
				}
				
				if (!empty($convertedfio[1])) {
					$patient_first_name = $convertedfio[1];
				}
				
				if (count($convertedfio) > 2) {
					for ($x=2; $x<count($convertedfio); $x++) {
						$patient_middle_name = $patient_middle_name.' '.$convertedfio[$x];
					}
					$patient_middle_name = trim($patient_middle_name);
				}
				
				// Адрес
				$g = '';
				if (!empty($row['vidgrad_'])) {
					$g = Str::lower(trim($row['vidgrad_']));
					$g = mb_substr($g, 0, 1).'.';
				}
				
				$city = '';	
				if (!empty($row['GRAD_'])) {
					$city = ' '.trim($row['GRAD_']);
				}
				
				$ul = '';
				if (!empty($row['vidulica_'])) {
					$ul = Str::lower(trim($row['vidulica_']));
				}
				
				$street = '';	
				if (!empty($row['ADRES_'])) {
					$street = ' '.Str::ucfirst(trim($row['ADRES_']));					
				}
				
				$dom = '';	
				if (!empty($row['DOM_'])) {
					$dom = ', д. '.trim($row['DOM_']);
				}
				
				$korpus = '';	
				if (!empty($row['KORPUS_'])) {
					$korpus = ', корп. '.trim($row['KORPUS_']);
				}
				
				$kvart = '';	
				if (!empty($row['KVART_'])) {
					$kvart = ', кв. '.trim($row['KVART_']);
				}
								
				$address = ['city' => $g.$city, 'address' => $ul.$street.$dom.$korpus.$kvart];
						
				// Телефон
				$phone = '';
				
				if (!empty($row['TEL_']) & $row['TEL_'] != 0) {
					$phone = trim($row['TEL_']);
					$phone = str_replace("-", "", $phone);
				}
				
				$phonemore = ['phoneinfo' => null, 'phone2' => null, 'phoneinfo2' => null];

				
				// Порода
				$old_breed = Str::ucfirst(Str::lower(trim($row['POR_'])));			
				$anymalbreed = Breeds::where('breed_title', $old_breed)->where('animal_type_id', $patient_animal_type)->first();
				$patient_breed = null;
				if ($anymalbreed) {
					$patient_breed = $anymalbreed->breed_id;
				} 
				
				// Живой или умер
				$rip = 0;
				if ($row['NA_'] == 1 | $row['NA_'] == 2) {
					$rip = 1;
				} else {
					$rip = 0;
				}			
				
				// Пол
				$sex = null;
				
				if ($row['POL_'] == 'Женский' | $row['POL_'] =='Кошка' | $row['POL_'] =='Стерилизированная' | $row['POL_'] =='Сука') {
					$sex = 2;
				} else if ($row['POL_'] == 'Мужской' | $row['POL_'] =='Кот' | $row['POL_'] =='Кастрированный' | $row['POL_'] =='Кобель') {
					$sex = 1;
				}
				
				// Кастрирован или стерилизованна
				$castrated = null;
				
				if ($row['POL_'] == 'Кастрированный' | $row['POL_'] =='Стерилизированная') {
					$castrated = 2;
				}
				
				// Email
				$email = null;
				if (!empty($row['email_']) & strpos($row['email_'], '@') == true) {
					$email = trim($row['email_']);
				}
				
				// Цвет
				$old_color = Str::ucfirst(Str::lower(trim($row['COLOR_'])));			
				$anymalcolor = Colors::where('color_title', $old_color)->first();
				$patient_color = null;
				if ($anymalcolor) {
					$patient_color = $anymalcolor->color_id;
				}

				// Дата регистрации
				$registration_date = null;
				if (!empty($row['DREG_'])) {
					$registration_date = trim($row['DREG_']);
				}
				
				// Дата рождения
				$birth_date = null;
				if (!empty($row['DBIRD_']) & $row['DBIRD_'] !== '0000-00-00') {
					$birth_date = trim($row['DBIRD_']);
				}
				
				// Дополнительная информация по пациенту
				$patient_information = null;
				if (!empty($row['status_'])) {
					$patient_information = trim($row['status_']);
				}
				
				// Пустые данные
				$client_data_ready = 0;
				$client_comments = null;
				$patient_full_name = null;
				$patient_tatoo = null;
				$patient_chip = null;
				$aprox_date = 0;
				
				
				$client_exist = Clients::where('last_name', $patient_last_name)->where('first_name', $patient_first_name)/*->where('middle_name', $patient_middle_name)*/
				->where(function($query) use ($address, $phone) {$query->where('address', json_encode($address))->orWhere('phone', $phone);})->first();
								
				$exist_id = 0;
				
				if ($client_exist) {
					
                  	$exist_id = $client_exist->client_id;
                  
                  	if (empty($patient_middle_name)) {
                    	$patient_middle_name = $client_exist->middle_name;
                    }
                  
				}
				
								
				$id = Clients::updateOrCreate(['client_id' => $exist_id], ['last_name'  => $patient_last_name,'first_name' => $patient_first_name,
				'middle_name' => $patient_middle_name,'address' => json_encode($address),'phone' => $phone,'phonemore' => json_encode($phonemore),'email' => $email,'data_ready' => $client_data_ready,
				'comments' => $client_comments,]);
				
				
				Patients::create(['client_id'  => $id->client_id,'short_name' => $patient_short_name,'full_name' => $patient_full_name,
				'sex_id' => $sex,'animal_type_id' => $patient_animal_type,'breed_id' => $patient_breed,'color_id' => $patient_color,
				'date_of_birth' => $birth_date,'aprox_date' => $aprox_date,'rip' => $rip,'tatoo' => $patient_tatoo,'chip' => $patient_chip, 'castrated' => $castrated,
				'additional_info' => $patient_information,'old_id' => $patient_old_id,'registration_date' => $registration_date,]);
											
				// Для теста (11756)
				/*if ($count == 11755) {
					return response()->json(['success'=>$patient_information]);
					break;	
				}
				$count++;*/
								
			}
										
		}
		
		return response()->json(['success'=>'Импорт завершен']);
		
	}
	
	
	public function importDiagnosis(){
		
		$jsondata = file_get_contents('dbimport/diagnosis.json');
		$data = json_decode($jsondata, true);

		foreach ($data[2]['data'] as $row) {
			
			if (!empty($row['DDIAG_']) & $row['DDIAG_'] != '') {
					
				// Замена символов на маленькие с большой первой (+ удаление лишних пробелов в начале и конце строки)
				$convertedstring = Str::ucfirst(Str::lower(trim($row['DDIAG_'])));
				
				$ids = array();
				unset($ids); 

				$diagnosis = DiagnosisTypes::where('diagnosis_title', '=', $convertedstring);
	 
				if (!$diagnosis->exists()) {
					
					$ids[] = $row['ID_'];
					
					$insertData = array("diagnosis_title"=>$convertedstring,"old_id"=>json_encode($ids));
					
					DiagnosisTypes::create($insertData);
					
				} else {
					
					$existids = json_decode($diagnosis->first()->old_id);
					
					$ids = array_values($existids);
					
					$ids[] = $row['ID_'];
					
					DiagnosisTypes::where('id', $diagnosis->first()->id)->update(['old_id' => json_encode($ids),]);
										
				}
			}
			
		}
		  
		return response()->json(['success'=>'Импорт завершен']);		
	}
	
	
	public function addDiagnosis(Request $request){
					
		// Тексты ошибок
		$messages = [
			'diagnosis_title.required' => 'Не заполнено поле',
			'diagnosis_title.unique' => 'Значение уже существует',
			'diagnosis_title.regex' => 'Поле содержит недопустимые символы'			
		];
			
		// Валидация на ошибки в полях
		$validator = Validator::make($request->all(), [			
			'diagnosis_title' => ['required', 'max:255', 'regex:/^[^`~#$^&*=\\|\\[\\]{}<>]*$/', Rule::unique('diagnosis_types'),],
		], $messages);
				
		// Запись в БД
		if ($validator->passes()) {			
			$data = $validator->valid();
			DiagnosisTypes::create($data);
			return response()->json(['success'=>'Cохранено']);	
		}
			
		return response()->json(['error'=>$validator->messages()->get('*')]);
	
	}
	
	
	public function changeDiagnosis(Request $request){
				
		// Тексты ошибок
		$messages = [
			'diagnosis_title.required' => 'Не заполнено поле',
			'diagnosis_title.unique' => 'Значение уже существует',
			'diagnosis_list.required' => 'Не выбрано значение',
			'diagnosis_title.regex' => 'Поле содержит недопустимые символы'				
		];
		
		$idd = $request->get('diagnosis_list');
				
		// Валидация на ошибки в полях
		$validator = Validator::make($request->all(), [			
			'diagnosis_title' => ['required', 'max:255', 'regex:/^[^`~#$^&*=\\|\\[\\]{}<>]*$/', Rule::unique('diagnosis_types')->ignore($idd, 'id'),],
			'diagnosis_list' => 'required',
		], $messages);
		
		unset($request['diagnosis_list']);
		
		// Запись в БД
		if ($validator->passes()) {			
			$data = $validator->valid();
			DiagnosisTypes::updateOrCreate(['id' => $idd], $data);
			return response()->json(['success'=>'Cохранено']);	
		}
			
		return response()->json(['error'=>$validator->messages()->get('*')]);
	
	}
	
	
	public function searchDiagnosis(Request $request){
		
		$title = trim($request->q);
					 
		$diagnosis = DiagnosisTypes::where('diagnosis_title', 'like', '%'.$title.'%')->get();	
		 
		if($diagnosis->count() > 0){
			
			$formatted_types = [];
		
			foreach ($diagnosis as $diagnos) {
				$formatted_types[] = ['id' => $diagnos->id, 'text' => $diagnos->diagnosis_title];
			}
			
			return \Response::json($formatted_types);
		} else {	
			return \Response::json([]);
		}			
    }
	
	
	public function addVacine(Request $request){
					
		// Тексты ошибок
		$messages = [
			'vacine_title.required' => 'Не заполнено поле',
			'vacine_title.unique' => 'Значение уже существует',
			'vacine_title.regex' => 'Поле содержит недопустимые символы'			
		];
			
		// Валидация на ошибки в полях
		$validator = Validator::make($request->all(), [			
			'vacine_title' => ['required', 'max:255', 'regex:/^[^`~#$^&*=\\|\\[\\]{}<>]*$/', Rule::unique('vacines_types'),],
		], $messages);
				
		// Запись в БД
		if ($validator->passes()) {			
			$data = $validator->valid();
			VacinesTypes::create($data);
			return response()->json(['success'=>'Cохранено']);	
		}
			
		return response()->json(['error'=>$validator->messages()->get('*')]);
	
	}
	
	
	public function changeVacine(Request $request){
				
		// Тексты ошибок
		$messages = [
			'vacine_title.required' => 'Не заполнено поле',
			'vacine_title.unique' => 'Значение уже существует',
			'vacine_list.required' => 'Не выбрано значение',
			'vacine_title.regex' => 'Поле содержит недопустимые символы'				
		];
		
		$idd = $request->get('vacine_list');
				
		// Валидация на ошибки в полях
		$validator = Validator::make($request->all(), [			
			'vacine_title' => ['required', 'max:255', 'regex:/^[^`~#$^&*=\\|\\[\\]{}<>]*$/', Rule::unique('vacines_types')->ignore($idd, 'id'),],
			'vacine_list' => 'required',
		], $messages);
		
		unset($request['vacine_list']);
		
		// Запись в БД
		if ($validator->passes()) {			
			$data = $validator->valid();
			VacinesTypes::updateOrCreate(['id' => $idd], $data);
			return response()->json(['success'=>'Cохранено']);	
		}
			
		return response()->json(['error'=>$validator->messages()->get('*')]);
	
	}
	
	
	public function searchVacine(Request $request){
		
		$title = trim($request->q);
					 
		$vacine = VacinesTypes::where('vacine_title', 'like', '%'.$title.'%')->get();	
		 
		if($vacine->count() > 0){
			
			$formatted_types = [];
		
			foreach ($vacine as $onevacine) {
				$formatted_types[] = ['id' => $onevacine->id, 'text' => $onevacine->vacine_title];
			}
			
			return \Response::json($formatted_types);
		} else {	
			return \Response::json([]);
		}		
    }
	
	
	public function addProductCategory(Request $request){
		
		// Валидация на ошибки в полях
		$validator = Validator::make($request->all(), [	
			'title' => ['required', 'string', 'max:255', 'regex:/^[^`~#$^&*=\\|\\[\\]{}<>]*$/', Rule::unique('product_categories')->ignore($request->get('id'), 'id')],			
			'percent' => ['nullable', 'numeric'],		
		]);
				
		// Запись в БД
		if ($validator->passes()) {	

			$id = ProductCategory::updateOrCreate(['id' => $request->get('id')], ['title'  => $request->get('title'),'percent' => $request->get('percent'),]);

			return response()->json(['success'=>'Данные сохранены', 'newid'=>$id->id]);

		} 

		return response()->json(['errors'=> $validator->errors()], 444);
			
	}
	
	
	public function getProductCategories(Request $request){
		
		$categoriesList = ProductCategory::select('id', 'title', 'percent')->get();
		
        return $categoriesList;	
			
	}
	
	public function deleteProductCategory(Request $request){
			
		
		if ($request->get('id') !== null & $request->get('id') !== '0') {
			
			$result = null;
			
			$result = ProductCategory::destroy($request->get('id'));
			
			Products::where('category', $request->get('id'))->update(['category' => null]);

			if ($result != null) {
				return response()->json(['success'=>'Данные удалены']);
			} else {				
				$err = array();
				array_push($err, "Ошибка удаления, перезагрузите страницу.");
				return response()->json(['errors'=> ['error'=> $err]],444);
			}
				
		} else {
			$err = array();
			array_push($err, "Ошибка удаления, перезагрузите страницу.");
			return response()->json(['errors'=> ['error'=> $err]],444);
		
		}
			
	}
	
	
	public function addProduct(Request $request){
					
		// Тексты ошибок
		$messages = [
			'product_title.required' => 'Не заполнено поле Наименование',
			'product_title.unique' => 'Такое Наименование уже существует',
			'product_title.regex' => 'Поле Наименование содержит недопустимые символы',
			'product_edizm.required' => 'Не заполнено поле Ед. измерения',
			'product_edizm.regex' => 'Поле Ед. измерения содержит недопустимые символы',
			'product_in_price.required' => 'Не заполнено поле Цена закупки',
			'product_out_price.required' => 'Не заполнено поле Цена',
			'product_in_price.numeric' => 'Значение поля Цена закупки должно быть цифровым',
			'product_out_price.numeric' => 'Значение поля Цена должна быть цифровым'
		];
			
		// Валидация на ошибки в полях
		$validator = Validator::make($request->all(), [			
			'product_title' => ['required', 'max:255', 'regex:/^[^`~#$^&*=\\|\\[\\]{}<>]*$/', Rule::unique('products')->where(function ($query) use ($request) {return $query->where('category', $request->get('category'));})->ignore($request->get('id'), 'id'),],
			'product_edizm' => ['required', 'max:255', 'regex:/^[^`~#$^&*=\\|\\[\\]{}<>]*$/',],
			'product_in_price' => ['required', 'numeric'],
			'product_out_price' => ['required', 'numeric'],
		], $messages);
				
		// Запись в БД
		if ($validator->passes()) {			

			$id = Products::updateOrCreate(['id' => $request->get('id')], ['product_title'  => $request->get('product_title'), 'category'  => (int)$request->get('category'), 'product_edizm'  => $request->get('product_edizm'), 'product_in_price' => $request->get('product_in_price'), 'product_out_price' => $request->get('product_out_price'),]);

			return response()->json(['success'=>'Данные сохранены', 'newid'=>$id->id]);

		}

		return response()->json(['errors'=> $validator->errors()], 444);

	}
	
	
	public function deleteProduct(Request $request){
			
		
		if ($request->get('id') !== null & $request->get('id') !== '0') {
			
			$result = null;
			
			$result = Products::destroy($request->get('id'));

			if ($result != null) {
				return response()->json(['success'=>'Данные удалены']);
			} else {				
				$err = array();
				array_push($err, "Ошибка удаления, перезагрузите страницу.");
				return response()->json(['errors'=> ['error'=> $err]],444);
			}
				
		} else {
			$err = array();
			array_push($err, "Ошибка удаления, перезагрузите страницу.");
			return response()->json(['errors'=> ['error'=> $err]],444);
		
		}
			
	}
	
	
	public function getProducts(Request $request){
		
		$categoriesList = Products::select('id', 'category', DB::raw('category as categoryname'), 'product_title', 'product_edizm', 'product_in_price', 'product_out_price')->get();
		
        return $categoriesList;	
			
	}
	
	
	
	public function searchProduct(Request $request){
		
		$title = trim($request->q);
					 
		$product = Products::where('product_title', 'like', '%'.$title.'%')->select('id', 'category', DB::raw('category as categoryname'), 'product_title', 'product_edizm', 'product_in_price', 'product_out_price')->get();	
		 
		if($product->count() > 0){
			
			$formatted_types = [];
		
			foreach ($product as $oneproduct) {
				$formatted_types[] = ['id' => $oneproduct->id, 'text' => $oneproduct->product_title, 'category' => $oneproduct->categoryname, 'price' => number_format($oneproduct->product_in_price, 2, ',', ' ').' / '.number_format($oneproduct->product_out_price, 2, ',', ' ')];
			}
			
			return \Response::json($formatted_types);
		} else {	
			return \Response::json([]);
		}		
    }
	
	
	
	public function oneProductData(Request $request){
		
		if($request->get('query') != '') {
							 
			$product = Products::where('id', $request->get('query'))->first();
		}
		
		if ($product) {
			return response()->json(['id'=>$product->id, 'title'=>$product->product_title, 'edizm'=>$product->product_edizm, 'in_price'=>$product->product_in_price, 'out_price'=>$product->product_out_price]);
		} else {
			return response()->json(['title'=>'', 'edizm'=>'', 'in_price'=>'', 'out_price'=>'']);
		}
		
    }
	
	
	public function changeProduct(Request $request){
		
		// Тексты ошибок
		$messages = [
			'product_title.required' => 'Не заполнено поле',
			'product_title.unique' => 'Уже существует',
			'product_title.regex' => 'Поле содержит недопустимые символы',
			'product_edizm.required' => 'Не заполнено поле',
			'product_edizm.regex' => 'Поле содержит недопустимые символы',
			'product_list.required' => 'Не выбрано значение',
			'product_in_price.required' => 'Не заполнено поле',
			'product_out_price.required' => 'Не заполнено поле'
		];
			
		// Валидация на ошибки в полях
		$validator = Validator::make($request->all(), [			
			'product_title' => ['required', 'max:255', 'regex:/^[^`~#$^&*=\\|\\[\\]{}<>]*$/', Rule::unique('products')->ignore($request->get('product_list'), 'id'),],
			'product_edizm' => ['required', 'max:255', 'regex:/^[^`~#$^&*=\\|\\[\\]{}<>]*$/',],
			'product_in_price' => 'required',
			'product_out_price' => 'required',
			'product_list' => 'required',
		], $messages);
		
		// Запись в БД
		if ($validator->passes()) {			
						
			Products::updateOrCreate(['id' => $request->get('product_list')], ['product_title'  => $request->get('product_title'), 'product_edizm'  => $request->get('product_edizm'), 'product_in_price'  => $request->get('product_in_price'),'product_out_price' => $request->get('product_out_price'),]);
			
			return response()->json(['success'=>'Cохранено']);	
		}
			
		return response()->json(['error'=>$validator->messages()->get('*')]);
	
	}
	
	
	public function addServiceCategory(Request $request){
		
		// Валидация на ошибки в полях
		$validator = Validator::make($request->all(), [	
			'title' => ['required', 'string', 'max:255', 'regex:/^[^`~#$^&*=\\|\\[\\]{}<>]*$/', Rule::unique('service_categories')->ignore($request->get('id'), 'id')],			
			'percent' => ['nullable', 'numeric'],		
		]);
				
		// Запись в БД
		if ($validator->passes()) {	

			$id = ServiceCategory::updateOrCreate(['id' => $request->get('id')], ['title'  => $request->get('title'),'percent' => $request->get('percent'),]);

			return response()->json(['success'=>'Данные сохранены', 'newid'=>$id->id]);

		} 

		return response()->json(['errors'=> $validator->errors()], 444);
			
	}
	
	
	public function getServiceCategories(Request $request){
		
		$categoriesList = ServiceCategory::select('id', 'title', 'percent')->get();
		
        return $categoriesList;	
			
	}
	
	
	public function deleteServiceCategory(Request $request){
			
		
		if ($request->get('id') !== null & $request->get('id') !== '0') {
			
			$result = null;
			
			$result = ServiceCategory::destroy($request->get('id'));
			
			Services::where('category', $request->get('id'))->update(['category' => null]);

			if ($result != null) {
				return response()->json(['success'=>'Данные удалены']);
			} else {				
				$err = array();
				array_push($err, "Ошибка удаления, перезагрузите страницу.");
				return response()->json(['errors'=> ['error'=> $err]],444);
			}
				
		} else {
			$err = array();
			array_push($err, "Ошибка удаления, перезагрузите страницу.");
			return response()->json(['errors'=> ['error'=> $err]],444);
		
		}
			
	}
	
	
	public function addService(Request $request){
		
		// Тексты ошибок
		$messages = [
			'service_title.required' => 'Не заполнено поле Наименование',
			'service_title.unique' => 'Такое Наименование уже существует',
			'service_title.regex' => 'Поле Наименование содержит недопустимые символы',
			'service_price.required' => 'Не заполнено поле Цена',
			'service_price.numeric' => 'Значение поля Цена должна быть цифровым'
		];
			
		// Валидация на ошибки в полях
		$validator = Validator::make($request->all(), [			
			'service_title' => ['required', 'max:255', 'regex:/^[^`~#$^&*=\\|\\[\\]{}<>]*$/', Rule::unique('services')->where(function ($query) use ($request) {return $query->where('category', $request->get('category'));})->ignore($request->get('id'), 'id'),],
			'service_price' => ['required', 'numeric'],
		], $messages);
				
		// Запись в БД
		if ($validator->passes()) {			

			$id = Services::updateOrCreate(['id' => $request->get('id')], ['service_title'  => $request->get('service_title'), 'category'  => (int)$request->get('category'), 'service_price' => $request->get('service_price'),]);

			return response()->json(['success'=>'Данные сохранены', 'newid'=>$id->id]);

		}

		return response()->json(['errors'=> $validator->errors()], 444);

	}
	
	
	public function deleteService(Request $request){
			
		
		if ($request->get('id') !== null & $request->get('id') !== '0') {
			
			$result = null;
			
			$result = Services::destroy($request->get('id'));

			if ($result != null) {
				return response()->json(['success'=>'Данные удалены']);
			} else {				
				$err = array();
				array_push($err, "Ошибка удаления, перезагрузите страницу.");
				return response()->json(['errors'=> ['error'=> $err]],444);
			}
				
		} else {
			$err = array();
			array_push($err, "Ошибка удаления, перезагрузите страницу.");
			return response()->json(['errors'=> ['error'=> $err]],444);
		
		}
			
	}
	
	
	public function getServices(Request $request){
		
		$categoriesList = Services::select('id', 'category', DB::raw('category as categoryname'), 'service_title', 'service_price')->get();
		
        return $categoriesList;	
			
	}
	
	
	public function searchService(Request $request){
		
		$title = trim($request->q);
					 
		$service = Services::where('service_title', 'like', '%'.$title.'%')->select('id', 'category', DB::raw('category as categoryname'), 'service_title', 'service_price')->get();	
		 
		if($service->count() > 0){
			
			$formatted_types = [];
		
			foreach ($service as $oneservice) {
				$formatted_types[] = ['id' => $oneservice->id, 'text' => $oneservice->service_title, 'category' => $oneservice->categoryname];
			}
			
			return \Response::json($formatted_types);
		} else {	
			return \Response::json([]);
		}		
    }
	
	
	public function oneServiceData(Request $request){
		
		if($request->get('query') != '') {
							 
			$service = Services::where('id', $request->get('query'))->first();
		}
		
		if ($service) {
			return response()->json(['id'=>$service->id, 'title'=>$service->service_title, 'price'=>$service->service_price]);
		} else {
			return response()->json(['title'=>'', 'price'=>'']);
		}
		
    }
	
	
	public function changeService(Request $request){
		
		// Тексты ошибок
		$messages = [
			'service_title.required' => 'Не заполнено поле',
			'service_title.unique' => 'Уже существует',
			'service_title.regex' => 'Поле содержит недопустимые символы',
			'service_list.required' => 'Не выбрано значение',
			'service_price.required' => 'Не заполнено поле'
		];
			
		// Валидация на ошибки в полях
		$validator = Validator::make($request->all(), [			
			'service_title' => ['required', 'max:255', 'regex:/^[^`~#$^&*=\\|\\[\\]{}<>]*$/', Rule::unique('services')->ignore($request->get('service_list'), 'id'),],
			'service_price' => 'required',
			'service_list' => 'required',
		], $messages);
		
		// Запись в БД
		if ($validator->passes()) {			
						
			Services::updateOrCreate(['id' => $request->get('service_list')], ['service_title'  => $request->get('service_title'), 'service_price'  => $request->get('service_price'),]);
			
			return response()->json(['success'=>'Cохранено']);	
		}
			
		return response()->json(['error'=>$validator->messages()->get('*')]);
	
	}
	
	
	public function setExpDate(Request $request){
					
		// Тексты ошибок
		$messages = [
			'expiration_date.required' => 'Не заполнено поле',
			'expiration_date.date_format' => 'Неверный формат даты',
		];
			
		// Валидация на ошибки в полях
		$validator = Validator::make($request->all(), [		
			'expiration_date' => ['required', 'date_format:d.m.Y',],
		], $messages);
		
		
		// Конвертируем дату
		$expiration_date = Carbon::parse($request->get('expiration_date'));
		
		// Запись в БД
		if ($validator->passes()) {			
			
			ExpirationDate::truncate();
			
			ExpirationDate::create(['expiration_date' => encrypt($expiration_date),]);
			
			return response()->json(['success'=>'Cохранено']);	
		}
			
		return response()->json(['error'=>$validator->messages()->get('*')]);
	
	}
	
	
	
	public function getExpDate(Request $request){
							
	
		$date = ExpirationDate::all()->first();
		
		if ($date) {
			try {	
				$expiration_date = Carbon::parse(decrypt($date->expiration_date))->format('d.m.Y');
			} catch (DecryptException $e) {
				$expiration_date = '';
			}
		} else {
			$expiration_date = '';
		}
		
		return response()->json(['success'=>$expiration_date]);
	
	}
	
	
	public function newTemplate(Request $request){
										
		// Тексты ошибок
		$messages = [
			'plate_title.required' => 'Не заполнено поле',
			'plate_title.unique' => 'Шаблон уже существует',
			'plate_type.required' => 'Не выбрано значение',	
			'plate_text.required' => 'Не заполнено поле',
			'plate_title.regex' => 'Поле содержит недопустимые символы'
		];
		
		// Валидация на ошибки в полях
		$validator = Validator::make($request->all(), [		
			'plate_title' => ['required', 'regex:/^[^`~#$^&*=\\|\\[\\]{}<>]*$/', Rule::unique('templates')->ignore($request->get('template_id'), 'id')->where(function ($query) use ($request) {return $query->where('plate_title', $request->get('plate_title'))->where('type', $request->get('plate_type'));}),],
			'plate_type' => 'required',			
			'plate_text' => 'required',
		], $messages);

		
		// Запись в БД
		if ($validator->passes()) {	

			Templates::updateOrCreate(['id' => $request->get('template_id')], ['plate_title'  => $request->get('plate_title'),'type'  => $request->get('plate_type'),'text' => $request->get('plate_text'),]);
								
			return response()->json(['success'=>'Cохранено']);	
		} 
			
		return response()->json(['error'=>$validator->messages()->get('*')]);
	
	}
	
	
	public function getTemplates(Request $request){
		
		$title = trim($request->q);
							 
		$templates = Templates::where('type', $request->type)->get();
		 
		if($templates->count() > 0){
			
			$formatted_types = [];
			
			foreach($templates as $row){
				$formatted_types[] = ['id' => $row->id, 'text' => $row->plate_title];
			}
			
			return \Response::json($formatted_types);
			
		} else {
			return \Response::json([]);	
		}	
    }
	
	
	
	public function getTemplateData(Request $request){
		
		if($request->get('query') != '') {
							 
			$template = Templates::where('id', $request->get('query'))->first();
		}
		
		if ($template) {
			return response()->json(['title'=>$template->plate_title, 'type'=>$template->type, 'text'=>$template->text]);
		} else {
			return response()->json(['title'=>'', 'type'=>'', 'text'=>'']);
		}
		
    }
	
	
	public function deleteTemplate(Request $request){
		
		if ($request->get('template_id') !== null & $request->get('template_id') !== '0') {
		
			Templates::destroy($request->get('template_id'));
			
			return response()->json(['success'=>'Удачно удалено']);
		
		} else {
		
			return response()->json(['error'=>'Ошибка удаления, перезагрузите страницу и попробуйте еще раз']);
		
		}
					
	}
	
	
	
	public function getTemplatesList(Request $request){

		$output1 = '';
		$output2 = '';
		$output3 = '';
		
		$templates1 = Templates::where('type', 1)->get();
		$templates2 = Templates::where('type', 2)->get();
		$templates3 = Templates::where('type', 3)->get();
		
		if($templates1->count() > 0){
			
			$output1 = '<div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 row-cols-xl-3 row-cols-xxl-4 g-4">';
			
			$i1 = 0;
			
			foreach($templates1 as $row){
												
				$options1 = '';
								
				$options1 = '<ul class="dropdown-menu dropdown-m1 dropdown-m1-'.$i1.'" name="dropdown-m1-'.$i1.'"  id="dropdown-m1-'.$i1.'">
								<li><a class="dropdown-item" role="button" onclick="new_template('.$row->id.')">Изменить</a></li>
								<li><a class="dropdown-item" role="button" onclick="delete_template_dialog('.$row->id.')">Удалить</a></li>
								</ul>';
				
				$output1 = $output1.'
														
					<div class="col d-flex">
						<div class="card w-100">
						  <div class="card-body p-2 align-items-center d-flex">
						  
							<div class="d-flex justify-content-start align-items-center" style="width: 100%;">								
		
								<h6 class="card-title mb-0 text-wrap text-break">'.$row->plate_title.'</h6>
				
								<div class="btn-group dropstart ms-auto justify-content-end p-1">							  
									<a class="nav-link p-1" data-bs-toggle="dropdown" aria-expanded="false" href="#" role="button">	
										<img class="img-responsive" width="30" height="30" src="images/settings_120.png">
									</a>
									'.$options1.'
								</div>
																		
							</div>	
							
						  </div>
						</div>
					</div>				
				';	

				$i1++;
			}
			
			$output1 = $output1.'</div>';

		} else {
			$output1 = '		
				<div class="d-flex justify-content-center align-items-center">
					<div class="d-block px-4 py-2">
						Нет шаблонов.
					</div>
				</div>';
		}
		
		
		if($templates2->count() > 0){
			
			$output2 = '<div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 row-cols-xl-3 row-cols-xxl-4 g-4">';
			
			$i2 = 0;
			
			foreach($templates2 as $row){
												
				$options2 = '';
								
				$options2 = '<ul class="dropdown-menu dropdown-m2 dropdown-m2-'.$i2.'" name="dropdown-m2-'.$i2.'"  id="dropdown-m2-'.$i2.'">
								<li><a class="dropdown-item" role="button" onclick="new_template('.$row->id.')">Изменить</a></li>
								<li><a class="dropdown-item" role="button" onclick="delete_template_dialog('.$row->id.')">Удалить</a></li>
								</ul>';
				
				$output2 = $output2.'
														
					<div class="col d-flex">
						<div class="card w-100">
						  <div class="card-body p-2 align-items-center d-flex">
						  
							<div class="d-flex justify-content-start align-items-center" style="width: 100%;">								
		
								<h6 class="card-title mb-0 text-wrap text-break">'.$row->plate_title.'</h6>
				
								<div class="btn-group dropstart ms-auto justify-content-end p-1">							  
									<a class="nav-link p-1" data-bs-toggle="dropdown" aria-expanded="false" href="#" role="button">	
										<img class="img-responsive" width="30" height="30" src="images/settings_120.png">
									</a>
									'.$options2.'
								</div>
																		
							</div>	
							
						  </div>
						</div>
					</div>			
				';	

				$i2++;
			}
			
			$output2 = $output2.'</div>';

		} else {
			$output2 = '		
				<div class="d-flex justify-content-center align-items-center">
					<div class="d-block px-4 py-2">
						Нет шаблонов.
					</div>
				</div>';
		}
		
		
		if($templates3->count() > 0){
			
			$output3 = '<div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 row-cols-xl-3 row-cols-xxl-4 g-4">';
			
			$i3 = 0;
			
			foreach($templates3 as $row){
												
				$options3 = '';
								
				$options3 = '<ul class="dropdown-menu dropdown-m3 dropdown-m3-'.$i3.'" name="dropdown-m3-'.$i3.'"  id="dropdown-m3-'.$i3.'">
								<li><a class="dropdown-item" role="button" onclick="new_template('.$row->id.')">Изменить</a></li>
								<li><a class="dropdown-item" role="button" onclick="delete_template_dialog('.$row->id.')">Удалить</a></li>
								</ul>';
				
				$output3 = $output3.'
														
					<div class="col d-flex">
						<div class="card w-100">
						  <div class="card-body p-2 align-items-center d-flex">
						  
							<div class="d-flex justify-content-start align-items-center" style="width: 100%;">								
		
								<h6 class="card-title mb-0 text-wrap text-break">'.$row->plate_title.'</h6>
				
								<div class="btn-group dropstart ms-auto justify-content-end p-1">							  
									<a class="nav-link p-1" data-bs-toggle="dropdown" aria-expanded="false" href="#" role="button">	
										<img class="img-responsive" width="30" height="30" src="images/settings_120.png">
									</a>
									'.$options3.'
								</div>
																		
							</div>	
							
						  </div>
						</div>
					</div>			
				';	

				$i3++;
			}
			
			$output3 = $output3.'</div>';

		} else {
			$output3 = '		
				<div class="d-flex justify-content-center align-items-center">
					<div class="d-block px-4 py-2">
						Нет шаблонов.
					</div>
				</div>';
		}
		
							   
		return response()->json(['output1'=>$output1, 'output2'=>$output2, 'output3'=>$output3]);
		
    }
	
	
	public function newAnalisysTemplate(Request $request){
										
		// Тексты ошибок
		$messages = [
			'analysis_plate_title.required' => 'Не заполнено поле',	
			'analysis_plate_title.unique' => 'Шаблон уже существует',
			'analysis_titres_data.required' => 'Пустой шаблон',
			'analysis_plate_title.regex' => 'Поле содержит недопустимые символы'
		];
		
		// Валидация на ошибки в полях
		$validator = Validator::make($request->all(), [		
			'analysis_plate_title' => ['required', 'regex:/^[^`~#$^&*=\\|\\[\\]{}<>]*$/', Rule::unique('analysis_templates')->ignore($request->get('analysis_template_id'), 'id'),],
			'analysis_titres_data' => ['required',],
		], $messages);

		
		// Запись в БД
		if ($validator->passes()) {	

			AnalysisTemplates::updateOrCreate(['id' => $request->get('analysis_template_id')], ['analysis_plate_title'  => $request->get('analysis_plate_title'),'text' => json_encode($request->get('analysis_titres_data')),]);
								
			return response()->json(['success'=>'Cохранено']);	
		} 
			
		return response()->json(['error'=>$validator->messages()->get('*')]);
	
	}
	
	
	
	public function getAnalisysTemplatesList(Request $request){

		$output = '';
		
		$analisys_templates = AnalysisTemplates::all();
		
		if($analisys_templates->count() > 0){
			
			$output = '<div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 row-cols-xl-3 row-cols-xxl-4 g-4">';
			
			$i1 = 0;
			
			foreach($analisys_templates as $row){
												
				$options = '';
								
				$options = '<ul class="dropdown-menu dropdown-m1 dropdown-m1-'.$i1.'" name="dropdown-m1-'.$i1.'"  id="dropdown-m1-'.$i1.'">
								<li><a class="dropdown-item" role="button" onclick="new_analysis_template('.$row->id.')">Изменить</a></li>
								<li><a class="dropdown-item" role="button" onclick="delete_analysis_template_dialog('.$row->id.')">Удалить</a></li>
								</ul>';
				
				$output = $output.'
														
					<div class="col d-flex">
						<div class="card w-100">
						  <div class="card-body p-2 align-items-center d-flex">
						  
							<div class="d-flex justify-content-start align-items-center" style="width: 100%;">								
		
								<h6 class="card-title mb-0 text-wrap text-break">'.$row->analysis_plate_title.'</h6>
				
								<div class="btn-group dropstart ms-auto justify-content-end p-1">							  
									<a class="nav-link p-1" data-bs-toggle="dropdown" aria-expanded="false" href="#" role="button">	
										<img class="img-responsive" width="30" height="30" src="images/settings_120.png">
									</a>
									'.$options.'
								</div>
																		
							</div>	
							
						  </div>
						</div>
					</div>				
				';	

				$i1++;
			}
			
			$output = $output.'</div>';

		} else {
			$output = '		
				<div class="d-flex justify-content-center align-items-center">
					<div class="d-block px-4 py-2">
						Нет шаблонов.
					</div>
				</div>';
		}
		
		
		return response()->json(['output'=>$output]);
		
    }
	
	
	public function getAnalisysTemplateData(Request $request){
		
		if($request->get('query') != '') {
							 
			$analysistemplate = AnalysisTemplates::where('id', $request->get('query'))->first();
		}
		
		if ($analysistemplate) {
			return response()->json(['title'=>$analysistemplate->analysis_plate_title, 'text'=>json_decode($analysistemplate->text)]);
		} else {
			return response()->json(['title'=>'', 'text'=>'']);
		}
		
    }
	
	
	public function deleteAnalisysTemplate(Request $request){
		
		if ($request->get('analysis_template_id') !== null & $request->get('analysis_template_id') !== '0') {
		
			AnalysisTemplates::destroy($request->get('analysis_template_id'));
			
			return response()->json(['success'=>'Удачно удалено']);
		
		} else {
		
			return response()->json(['error'=>'Ошибка удаления, перезагрузите страницу и попробуйте еще раз']);
		
		}
					
	}
	
	
	
	public function getAnalisysTemplates(Request $request){
		
		$title = trim($request->q);
							 
		$analysistemplate = AnalysisTemplates::all();
		 
		if($analysistemplate->count() > 0){
			
			$formatted_types = [];
			
			foreach($analysistemplate as $row){
				$formatted_types[] = ['id' => $row->id, 'text' => $row->analysis_plate_title];
			}
			
			return \Response::json($formatted_types);
			
		} else {
			return \Response::json([]);	
		}	
    }
	
	
	// Импорт приемов
	public function importVisits(){
				
		$jsondata = file_get_contents('dbimport/lk.json'); // + lk(1).json, lk(2).json
		$data = json_decode($jsondata, true);
				
		$jsondata_2 = file_get_contents('dbimport/ex.json');
		$data_2 = json_decode($jsondata_2, true);
		
		$count_col = 0;
		
		foreach ($data[2]['data'] as $row) {
			
			$patient = Patients::where('old_id', $row['nomer_'])->first();
			
			if ($patient) {
						
				$results = array_filter($data_2[2]['data'], function($oneline) use ($row) {
				  return $oneline['idlk_'] == $row['id_'];
				});
				
				$first = array_shift($results);
				
				$note = '';
				
				if (!empty($first)) {
					$note = trim($first['note_']); 
				}
				
				$date_of_visit = Carbon::parse($row['DATA_'])->format('Y-m-d');
				
				
				
				$inspection = '';				
				if ($row['KZAB_'] != null & $row['KZAB_'] != ' ') {
					$inspection = trim(str_replace(PHP_EOL, '<br>', $row['KZAB_']));
					
					$inspection = preg_replace('/^(<br\s*?\/?>)+/i', '', $inspection);
					
					$inspection = preg_replace('/(<br\s*?\/?>)+$/i', '', $inspection);
					
					$inspection = '<p>'.$inspection.'</p>';
					
				}
				
				$treatment = '';				
				if ($row['LEC_'] != null & $row['LEC_'] != ' ') {
					$treatment = trim(str_replace(PHP_EOL, '<br>', $row['LEC_']));
					
					$treatment = preg_replace('/^(<br\s*?\/?>)+/i', '', $treatment);
					
					$treatment = preg_replace('/(<br\s*?\/?>)+$/i', '', $treatment);
					
					$treatment = '<p>'.$treatment.'</p>';
				}
				
				$part_note = '';				
				if ($row['NOTE_'] != null & $row['NOTE_'] != ' ') {
					$part_note = trim(str_replace(PHP_EOL, '<br>', $row['NOTE_']));
					
					$part_note = preg_replace('/^(<br\s*?\/?>)+/i', '', $part_note);

					$part_note = preg_replace('/(<br\s*?\/?>)+$/i', '', $part_note);
					
					$part_note = '<p>'.$part_note.'</p>';
				}


				
				if (!empty($inspection) | !empty($treatment) | !empty($part_note) | !empty($note)) {
					
					$visit_date_id = Carbon::parse($row['DATA_'])->format('Y-m-d-H-i-s');
					
					// Если совпадает visit_date_id, то добавляем секунды
					//$visit = Visits::where('visit_date_id', $visit_date_id)->first();
				
					//if ($visit) {
						$visit_date_id = Carbon::createFromFormat('Y-m-d-H-i-s', $visit_date_id)->addSeconds($row['id_'])->format('Y-m-d-H-i-s');
					//} 
							
					Visits::create(['date_of_visit'  => $date_of_visit, 'visit_date_id'  => $visit_date_id, 'doctor'  => trim($row['vet_']), 'patient_id' => $patient->patient_id,
					'inspection_results' => $inspection, 'clinic_comments' => $note, 'recomendation' => $treatment.$part_note, 'old_id' => $row['id_'],]);
				
				}
				
			}
			
			$count_col++;
			
			/*if ($count_col == 500) {
				break;
			}*/
			
		}
		  
		return response()->json(['success'=>'Импорт завершен']);	
			
	}
	
	
	// Импорт товаров
	public function importProducts(){
		
		$jsondata = file_get_contents('dbimport/partidi.json');
		$data = json_decode($jsondata, true);

		foreach ($data[2]['data'] as $row) {
			
			if (!empty($row['art_']) & !empty($row['SALE_'])) {
							
				$product_id = 0;
				
				$product = Products::where('product_title', $row['art_'])->first();
				
				if ($product) {
					$product_id = $product->id;
				}
				
				Products::updateOrCreate(['id' => $product_id], ['product_title'  => trim($row['art_']), 'product_edizm'  => trim($row['MR_']), 'product_in_price'  => trim($row['CENA_']), 'product_out_price'  => trim($row['SALE_']),]);

			}
	
		}
		
		  
		return response()->json(['success'=>'Импорт завершен']);		
	}
	
	
	// Импорт услуг
	public function importServices(){
		
		$jsondata = file_get_contents('dbimport/manipul.json');
		$data = json_decode($jsondata, true);

		foreach ($data[2]['data'] as $row) {
			
			if (!empty($row['man_']) & !empty($row['PR_'])) {
				
				
				$service_id = 0;
				
				$service = Services::where('service_title', $row['man_'])->first();
				
				if ($service) {
					$service_id = $service->id;
				}

				Services::updateOrCreate(['id' => $service_id], ['service_title'  => trim($row['man_']), 'service_price'  => trim($row['PR_']),]);
			
			}
	
		}
				
		$jsondata_2 = file_get_contents('dbimport/paneli.json');
		$data_2 = json_decode($jsondata_2, true);

		foreach ($data_2[2]['data'] as $row_2) {

			if (!empty($row_2['MAN_']) & !empty($row_2['PR_']) & $row_2['PR_'] != 0 & ($row_2['VID_'] == 1 | $row_2['VID_'] == 2)) {
				
				
				$vid = '';
				
				if ($row_2['VID_'] == 1) {
					$vid = 'Собаки, ';
				} else if ($row_2['VID_'] == 2) {
					$vid = 'Кошки, ';
				}
				
				$grupa = '';
				
				if ($row_2['GRUPA_'] == 1) {
					$grupa = 'Гематология, ';
				} else if ($row_2['GRUPA_'] == 2) {
					$grupa = 'Биохимия, ';
				} else if ($row_2['GRUPA_'] == 3) {
					$grupa = 'Эндокринология, ';
				} else if ($row_2['GRUPA_'] == 4) {
					$grupa = 'Гистология, ';
				} else if ($row_2['GRUPA_'] == 5) {
					$grupa = 'Серология, ';
				} else if ($row_2['GRUPA_'] == 6) {
					$grupa = 'Исследование мочи, ';
				} else if ($row_2['GRUPA_'] == 7) {
					$grupa = 'Копрология, ';
				} else if ($row_2['GRUPA_'] == 8) {
					$grupa = 'Бактериология, ';
				} else if ($row_2['GRUPA_'] == 9) {
					$grupa = 'Микроскопия, ';
				}
				
				$service_title = $vid.$grupa.trim($row_2['MAN_']);
				
				$service_id_2 = 0;
				
				$service_2 = Services::where('service_title', $service_title)->first();
				
				if ($service_2) {
					$service_id_2 = $service_2->id;
				}
			
				Services::updateOrCreate(['id' => $service_id_2], ['service_title'  => $service_title, 'service_price'  => trim($row_2['PR_']),]);
			
			}

	
		}
		  
		return response()->json(['success'=>'Импорт завершен']);		
	}
	
	
	// Импорт шаблонов
	public function importTemplates(){
		
		$jsondata = file_get_contents('dbimport/instruk.json');
		$data = json_decode($jsondata, true);

		foreach ($data[2]['data'] as $row) {
			
			
			$text = trim(str_replace(PHP_EOL, '<br>', $row['INSTR_']));
			
			$text = preg_replace('/^(<br\s*?\/?>)+/i', '', $text);
					
			$text = preg_replace('/(<br\s*?\/?>)+$/i', '', $text);
			
				
			if ($row['id_'] == 58 | $row['id_'] == 85 | $row['id_'] == 86 | $row['id_'] == 90 | $row['id_'] == 93) {
				
				$plate_title = '';
				
				if ($row['id_'] == 86) {
					$plate_title = trim($row['IMEIN_']).'_2';
				} else {
					$plate_title = trim($row['IMEIN_']);
				}
			
				Templates::create(['plate_title'  => $plate_title, 'type'  => 2, 'text' => $text,]);
			
			} if ($row['id_'] == 51 | $row['id_'] == 53 | $row['id_'] == 73) {
				Templates::create(['plate_title'  => $row['IMEIN_'], 'type'  => 1, 'text' => $text,]);
			} if ($row['id_'] == 7 |$row['id_'] == 8 | $row['id_'] == 10 | $row['id_'] == 12 | $row['id_'] == 22 | $row['id_'] == 44 | $row['id_'] == 45 | $row['id_'] == 48 
				| $row['id_'] == 49 | $row['id_'] == 55 | $row['id_'] == 62 | $row['id_'] == 63 | $row['id_'] == 66 | $row['id_'] == 67 | $row['id_'] == 68 | $row['id_'] == 69 
				| $row['id_'] == 70 | $row['id_'] == 71 | $row['id_'] == 76 | $row['id_'] == 75 | $row['id_'] == 77 | $row['id_'] == 78 | $row['id_'] == 79 | $row['id_'] == 80 
				| $row['id_'] == 82 | $row['id_'] == 87 | $row['id_'] == 88 | $row['id_'] == 91) {
				
				$plate_title = '';
				
				if ($row['id_'] == 48) {
					$plate_title = trim($row['IMEIN_']).'_2';
				} else {
					$plate_title = trim($row['IMEIN_']);
				}
				
				Templates::create(['plate_title'  => $plate_title, 'type'  => 3, 'text' => $text,]);
			}
	
		}
		  
		return response()->json(['success'=>'Импорт завершен']);		
	}
	
	
	
	// Импорт шаблонов анализов
	public function importAnalisysTemplates(){
		
		$jsondata = file_get_contents('dbimport/paneli.json');
		$data = json_decode($jsondata, true);
				
		$jsondata_2 = file_get_contents('dbimport/profili.json'); 
		$data_2 = json_decode($jsondata_2, true);
		

		foreach ($data[2]['data'] as $row) {

			if (!empty($row['MAN_']) & !empty($row['PR_']) & $row['PR_'] != 0 & ($row['VID_'] == 1 | $row['VID_'] == 2)) {
				
				
				$vid = '';
				
				if ($row['VID_'] == 1) {
					$vid = 'Собаки, ';
				} else if ($row['VID_'] == 2) {
					$vid = 'Кошки, ';
				}
				
				$grupa = '';
				
				if ($row['GRUPA_'] == 1) {
					$grupa = 'Гематология, ';
				} else if ($row['GRUPA_'] == 2) {
					$grupa = 'Биохимия, ';
				} else if ($row['GRUPA_'] == 3) {
					$grupa = 'Эндокринология, ';
				} else if ($row['GRUPA_'] == 4) {
					$grupa = 'Гистология, ';
				} else if ($row['GRUPA_'] == 5) {
					$grupa = 'Серология, ';
				} else if ($row['GRUPA_'] == 6) {
					$grupa = 'Исследование мочи, ';
				} else if ($row['GRUPA_'] == 7) {
					$grupa = 'Копрология, ';
				} else if ($row['GRUPA_'] == 8) {
					$grupa = 'Бактериология, ';
				} else if ($row['GRUPA_'] == 9) {
					$grupa = 'Микроскопия, ';
				}
				
				$analisys_title = $vid.$grupa.trim($row['MAN_']);
				
				
				$results = array_filter($data_2[2]['data'], function($oneline) use ($row) {
				  return $oneline['PANEL_'] == $row['NM_'];
				});
				
				
				$analysis_titres_data = [];
				
				foreach ($results as $row_2) { 
				
					$title = trim($row_2['MAN_']);
					$title = preg_replace('/\s+/', ' ', $title);

					$edinici = trim($row_2['EDINICI_']);
					$edinici = preg_replace('/\s+/', ' ', $edinici);

					$data =array();
					$data['type_todb']  = 0;
					$data['name_todb']  = $title;
					$data['edizm_todb'] = $edinici;
					$data['from_todb'] = $row_2['normad_'];
					$data['to_todb'] = $row_2['NORMAG_'];;
					
					array_push($analysis_titres_data, $data);

				}
				
				if (!empty($analysis_titres_data)) {
				
				
				AnalysisTemplates::create(['analysis_plate_title'  => $analisys_title,'text' => json_encode($analysis_titres_data),]);
				
				}				
			
			}
		
		}
		  
		return response()->json(['success'=>'Импорт завершен']);	
			
	}
	
	
	
	// Импорт анализов
	public function importAnalisys(){
		
		$jsondata = file_get_contents('dbimport/result(first).json');  // + result(second).json
		$data = json_decode($jsondata, true);
		
		$jsondata_2 = file_get_contents('dbimport/paneli.json'); 
		$data_2 = json_decode($jsondata_2, true);
		
		$analysis_numbers = array();
		
		foreach ($data[2]['data'] as $row) { 			
			$analysis_numbers[] = (int) $row['NZ_'];
		}
				
		$results = array_unique($analysis_numbers);
		
		asort($results , SORT_NUMERIC);
		
		foreach ($results as $result) {

			$one_analisys = array_filter($data[2]['data'], function($oneline) use ($result) {
				return $oneline['NZ_'] == $result;
			});
			
			$first = array_shift($one_analisys);
			
			$patient = Patients::where('old_id', $first['KNO_'])->first();
			
			if ($patient) {
				
				$patient_id = $patient->patient_id;
				
				$date = $first['DATA_'];
				
				$doctor = 'Нет данных';
				
				$analisys_title = 'Без наименования';
				
				$panel_number = $first['PANEL_'];
				
				if ($panel_number != 0) {
				
					$panel_names = array_filter($data_2[2]['data'], function($oneline) use ($panel_number) {
						return $oneline['NM_'] == $panel_number;
					});
					
					$first_panel = array_shift($panel_names);
					
					
					$analisys_title = trim($first_panel['MAN_']);
				
				}
								
				$analysis_titres_data = [];
				
				foreach ($one_analisys as $analisys) {
				
					$title = trim($analisys['MAN_']);
					$title = preg_replace('/\s+/', ' ', $title);

					$edinici = trim($analisys['EDINICI_']);
					$edinici = preg_replace('/\s+/', ' ', $edinici);

					$analysis_titres_data_element =array();
					$analysis_titres_data_element['type_todb']  = 0;
					$analysis_titres_data_element['name_todb']  = $title;
					$analysis_titres_data_element['edizm_todb'] = $edinici;
					$analysis_titres_data_element['result_todb'] = $analisys['REZULTAT_'];	
					$analysis_titres_data_element['from_todb'] = $analisys['NORMAD_'];
					$analysis_titres_data_element['to_todb'] = $analisys['NORMAG_'];;
					
					array_push($analysis_titres_data, $analysis_titres_data_element);
				
				}
								
				if (!empty($analysis_titres_data)) {
				
					Analysis::create(['date_of_analysis'  => $date, 'doctor'  => $doctor, 'patient_id' => $patient_id, 'visit_id' => null, 'analysis_name' => $analisys_title, 
					'analysis_text' => json_encode($analysis_titres_data),]);
				
				}

			}
									
		}
		
		return response()->json(['success'=>'Импорт завершен']);	
		
	}

}
