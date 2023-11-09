<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Validator;
use Illuminate\Validation\Rule;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Session;
use Artisan;
use Intervention\Image\ImageManagerStatic as Image;
use App\UploadedPhoto;
use App\Visits;
use App\DiagnosisVisits;
use App\DiagnosisTypes;
use App\AnimalWeight;
use App\Staff;
use App\Researches;
use App\Analysis;
use App\AnalysisTemplates;
use App\Vacines;
use App\VacinesTypes;
use App\Patients;
use App\Animal_types;
use App\Breeds;
use App\Colors;
use App\Clients;
use App\Bills;
use App\Pays;
use App\ProductCategory;
use App\ServiceCategory;
use App\Products;
use App\Services;


class PatientCard extends Controller
{
    
	public function newVisit(Request $request){
		
		$messages = [
			'visit_date_id.required' => 'Ошибка сохранения',
			'anymal_id.required' => 'Ошибка сохранения',
			'visit_date.required' => 'Не заполнено поле',
			'visit_date.date_format' => 'Неверный формат даты',
			'client_complaints.regex' => 'Поле содержит недопустимые символы',
			'clinic_comments.regex' => 'Поле содержит недопустимые символы',
			'doctor.required' => 'Не заполнено поле'
		];
		
		$validator = Validator::make($request->all(), [	
			'anymal_id' => 'required',		
			'visit_date_id' => 'required',
			'visit_date' => ['required', 'date_format:d.m.Y',],
			'client_complaints' => ['nullable', 'max:1000', 'regex:/^[^`~#$^&*=\\|\\[\\]{}<>]*$/',],
			'clinic_comments' => ['nullable', 'max:1000', 'regex:/^[^`~#$^&*=\\|\\[\\]{}<>]*$/',],
			'doctor' => 'required',
        ], $messages);
		
		
		if ($validator->passes()) {
			
			// Конвертируем дату
			$date_of_visit = Carbon::parse($request->get('visit_date'))->format('Y-m-d');
						
			$research_needed = [];
			
			if (count((array) $request->get('researches_todb')) != 0) {
				
				for($i = 0; $i < count((array) $request->get('researches_todb')); ++$i) {
					
					$research = $request->get('researches_todb')[$i];
					
					$research_needed[$i] = $research;
				
				}
			}
			
			$analisys_needed = [];
			
			if (count((array) $request->get('analisys_todb')) != 0) {
				
				for($i = 0; $i < count((array) $request->get('analisys_todb')); ++$i) {
					
					$analis = $request->get('analisys_todb')[$i];
					
					$analisys_needed[$i] = $analis;
				
				}
			}
			
		
			$visitid = Visits::updateOrCreate(['visit_date_id' => $request->visit_date_id], ['visit_date_id'  => $request->visit_date_id, 'date_of_visit'  => $date_of_visit, 'doctor'  => $request->doctor_text, 'patient_id' => $request->anymal_id,
			'visit_purpose' => $request->porpose, 'visit_type' => $request->visit_type, 'complaints' => $request->client_complaints, 'inspection_results' => $request->check_result, 
			'clinic_comments' => $request->clinic_comments, 'research_needed' => json_encode($research_needed), 'analisys_needed' => json_encode($analisys_needed), 'recomendation' => $request->recomendation,]);

			
			// Удаляем фото
			$phototodelete = (array) json_decode($request->get('phototodelete'));
				
			foreach($phototodelete as $value){
				
				$photo = UploadedPhoto::where('id', $value)->first();
			
				if ($photo) { 
				
					$image_path = public_path('uploaded_photos/'.$photo->anymal_id.'/' .$photo->image_name); 
						if(\File::exists($image_path)) {
						\File::delete($image_path);
					}
					
					UploadedPhoto::destroy($value);
						
				}
				
			}
			

			// Вновь загруженные фото (при изменении приема)
			UploadedPhoto::where('visit_date_id', $request->visit_date_id.'_change')->update(['visit_date_id' => $request->visit_date_id]);


			// Удалить диагнозы
			DiagnosisVisits::where('visit_date_id', $request->visit_date_id)->delete();
			
			// Если есть диагнозы
			if (count((array) $request->get('diagnosis_todb')) != 0) {
				
				for($i = 0; $i < count((array) $request->get('diagnosis_todb')); ++$i) {
					
					$diagnosis = $request->get('diagnosis_todb')[$i];
					$need_aprove_todb = $request->get('need_aprove_todb')[$i];
					$permanent_todb = $request->get('permanent_todb')[$i];
					
					DiagnosisVisits::create(['visit_date_id'  => $request->visit_date_id,'anymal_id' => $request->anymal_id,
					'diagnosis_id' => $diagnosis,'need_aprove_id' => $need_aprove_todb,'permanent_id'  => $permanent_todb,]);
				}
			}
			
			// Удалить вес
			AnimalWeight::where('visit_date_id', $request->visit_date_id)->delete();
			
			// Если указан вес. Замена запятых на точки в float и запись в БД.
			if ($request->has('animal_weight')) {
				if ($request->animal_weight != null) {
				$newweight = str_replace(",",".",$request->animal_weight);
				$newweight = preg_replace('/\.(?=.*\.)/', '', $newweight);			
				$request->merge(['animal_weight'=>$newweight]);
								
				AnimalWeight::create(['visit_date_id'  => $request->visit_date_id, 'date_of_visit'  => $date_of_visit, 'anymal_id' => $request->anymal_id,
				'weight' => $request->animal_weight,]);
				}
			}
			
			return response()->json(['success'=>'Сохранено', 'visitid'=>$visitid->visit_id]);
			
		}
		
		return response()->json(['error'=>$validator->messages()->get('*')]);			
					
	}
	
	
	// Удаление приема (в т.ч. вес, диагнозы, фото)
	public function deleteVisit(Request $request){
		
		
		if ($request->get('id') !== null & $request->get('id') !== '0') {		
						
			$visittodel = Visits::where('visit_id', $request->get('id'))->first();
			
			if ($visittodel) {
				
				AnimalWeight::where('visit_date_id', $visittodel->visit_date_id)->delete();
				
				DiagnosisVisits::where('visit_date_id', $visittodel->visit_date_id)->delete();
					
				$photostodel = UploadedPhoto::where('visit_date_id', $visittodel->visit_date_id)->get();
				
				if($photostodel->count() > 0){ 
				
					foreach($photostodel as $phototodel){
						
						$image_path = public_path('uploaded_photos/'.$phototodel->anymal_id.'/' .$phototodel->image_name); 
						if(\File::exists($image_path)) {
							\File::delete($image_path);
						}
	
						UploadedPhoto::destroy($phototodel->id);
						
					}
				}
				
				
				// Удаление привязки исследований к приему
				$researches = Researches::where('visit_id', $request->get('id'))->update(['visit_id' => 0]);
				
				// Удаление привязки анализов к приему
				$analysis = Analysis::where('visit_id', $request->get('id'))->update(['visit_id' => 0]);
								
				
				Visits::destroy($request->get('id'));
				
				return response()->json(['success'=>'Удачно удалено']);
				
				
			} else {
				
				return response()->json(['error'=>'Ошибка удаления, перезагрузите страницу и попробуйте еще раз']);
				
			}

		} else {
			return response()->json(['error'=>'Ошибка удаления, перезагрузите страницу и попробуйте еще раз']);
		}	
		
	}
	
	
	// Получение данных о весе для графика (5 последних значений)
	public function getWeights(Request $request){
		
		$dates_for_graph = [];
		$weights_for_graph = [];
	
		$weights = AnimalWeight::where('anymal_id', $request->anymal_id)->latest('date_of_visit')->get();  
		 
		$weights = $weights->unique('date_of_visit')->slice(0, 5)->reverse();
		
		if($weights->count() > 0){

			foreach($weights as $row){	
			
				array_push($dates_for_graph, Carbon::parse($row->date_of_visit)->format('d.m.Y'));
				array_push($weights_for_graph, $row->weight);

			}
		}
		
		return response()->json(['dates'=>$dates_for_graph, 'weights'=>$weights_for_graph]);
		
	}
	
	
	// Список визитов
	public function getVisitsList(Request $request){
		
			
		// Удаляем записи в БД animal_weights если нет таких приемов (визитов)
		$weightstodelete = AnimalWeight::where('anymal_id', $request->get('anymal_id'))->get();		
		if($weightstodelete->count() > 0){
			foreach($weightstodelete as $row){
				if ($row->visit_date_id) {
					$thereidvisit = Visits::where('visit_date_id', $row->visit_date_id)->first();
					if (!$thereidvisit) {
						AnimalWeight::destroy($row->id);						
					} 
				}
			}
		}
		
		// Удаляем записи в БД diagnosis_visits если нет таких приемов (визитов)
		$diagnosystodelete = DiagnosisVisits::where('anymal_id', $request->get('anymal_id'))->get();		
		if($diagnosystodelete->count() > 0){
			foreach($diagnosystodelete as $row){
				if ($row->visit_date_id) {
					$thereidvisit = Visits::where('visit_date_id', $row->visit_date_id)->first();
					if (!$thereidvisit) {
						DiagnosisVisits::destroy($row->id);						
					} 
				}
			}
		}
		
			
		$output = '';
		
		
		$porpose = $request->porpose_2;
		
		$diagnose = $request->diagnosis_list_2;
		
		$doctor = $request->doctor;
		
		$visit_date_start = '';
		
						
		if (!empty($request->visit_date_start)) {
			$visit_date_start = Carbon::parse($request->get('visit_date_start'))->format('Y-m-d');
		}			
		
		if (!empty($request->visit_date_end)) {
			$visit_date_end = Carbon::parse($request->get('visit_date_end'))->format('Y-m-d');
		} else {
			$visit_date_end = Carbon::now()->format('Y-m-d');;
		}
		
		if (!empty($request->visit_date_start) && !empty($request->visit_date_end) && Carbon::createFromFormat('Y-m-d', $visit_date_start)->gt(Carbon::createFromFormat('Y-m-d', $visit_date_end))) {
			return response()->json(['error'=>'Некорректный выбор дат']);
		}


		$visits = Visits::where('patient_id', $request->get('anymal_id'))
		
			->when(!empty($request->visit_date_start), function ($query) use ($visit_date_start, $visit_date_end) {
			$query->whereBetween('date_of_visit', [$visit_date_start, $visit_date_end]);})	
		
			->when(empty($request->visit_date_start) & !empty($request->visit_date_end), function ($query) use ($visit_date_end) {
			$query->whereDate('date_of_visit', '<=', $visit_date_end);})
			
			->when(!empty($porpose), function ($query) use ($porpose) {
			$query->where('visit_purpose', $porpose);})
			
			->when(!empty($doctor), function ($query) use ($doctor) {
			$query->where('doctor', $doctor);})
			
			
			->when(!empty($diagnose), function ($query) use ($diagnose) {										
				$query->join('diagnosis_visits','visits.visit_date_id','diagnosis_visits.visit_date_id')
				->where('diagnosis_visits.diagnosis_id', $diagnose)
			;})
	
		->latest('date_of_visit')
		->orderBy('visit_id', 'desc')
		->get(); 
		
		
		if($visits->count() > 0){
			
			
			$output = '<div class="d-flex align-items-center px-0 my-2 pb-2">								
				<div class=" col-12 d-flex justify-content-end align-items-center" >								
					<button name="collapse_expand_visits" id="collapse_expand_visits"  type="button" onclick="collapse_expand_visits()" class="btn btn-sm border-1 border-primary text-primary">Детально</button>
				</div>	
			</div>';
			
						
			$output = $output.'
				
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
						
						.hoverDiv {
							background: #fff;
						}
						
						.hoverDiv:hover {
							background: #f5f5f5;
						}

					</style>
					
					<div class="d-none d-lg-block border-bottom p-0 m-0">
						<div class="row justify-content-center mx-2 pb-2 short_visit">
							<div class="table-title text-dark col-1 px-2 justify-content-center d-flex align-items-center text-break">ID</div>
							<div class="table-title text-dark col px-2 justify-content-center d-flex align-items-center text-break">Дата</div>
							<div class="table-title text-dark col px-2 justify-content-center d-flex align-items-center text-break">Врач</div>
							<div class="table-title text-dark col px-2 justify-content-center d-flex align-items-center text-break">Цель обращения</div>
							<div class="table-title text-dark col px-2 justify-content-center d-flex align-items-center text-break">Тип обращения</div>
							<div class="table-title text-dark col px-2 justify-content-center d-flex align-items-center text-break">Диагноз</div>
							<div class="table-title text-dark col px-2 justify-content-center d-flex align-items-center text-break">Создан</div>					
						</div>
					</div>
				';
			
			foreach($visits as $row){

				$visit_purpose = '';
				
				if ($row->visit_purpose == 1) {
					$visit_purpose = 'Консультация';
				} else if ($row->visit_purpose == 2) {
					$visit_purpose = 'Манипуляции';
				} else if ($row->visit_purpose == 3) {
					$visit_purpose = 'Манипуляции (для другой клиники)';
				} else if ($row->visit_purpose == 4) {
					$visit_purpose = 'Операция';
				} else if ($row->visit_purpose == 5) {
					$visit_purpose = 'Стационар';
				} else if ($row->visit_purpose == 6) {
					$visit_purpose = 'Гигиенические процедуры';
				} else {
					$visit_purpose = 'Нет данных';
				}
				
				
				$visit_type = '';
				
				if ($row->visit_type == 1) {
					$visit_type = 'Первичное';
				} else if ($row->visit_type == 2) {
					$visit_type = 'Повторное';
				} else {
					$visit_type = 'Нет данных';
				}
				
				
				// Диагнозы
				$diagnosislist = '';
				
				$diagnosis = DiagnosisVisits::where('visit_date_id', $row->visit_date_id)->get();
				
				if($diagnosis->count() > 0){
					
					foreach($diagnosis as $diagnose){
						
						$onediagnose = DiagnosisTypes::where('id', $diagnose->diagnosis_id)->first();
						
						if ($onediagnose) {
							
							$diagnosislist = $diagnosislist.'<div class="text-break d-flex align-items-lg-center justify-content-lg-center"><p class="mb-0 mt-0 mb-lg-2 mt-lg-2 text-start text-lg-center" style="font-size: 1em;">'.\Str::words($onediagnose->diagnosis_title, 3);  // с обрезкой строки до 3 слов

							
							if ($diagnose->need_aprove_id == 1 | $diagnose->permanent_id == 1) {
									
								$diagnosislist = $diagnosislist.'<span class="fst-italic text-primary">&nbsp;(';
								
								if ($diagnose->need_aprove_id == 1) {
									
									$diagnosislist = $diagnosislist.'неточный';
								}
								
								if ($diagnose->permanent_id  == 1 & $diagnose->need_aprove_id == 1) {
									
									$diagnosislist = $diagnosislist.',&nbsp;хронический';
									
								} else if ($diagnose->permanent_id  == 1 & $diagnose->need_aprove_id == 0) {
									
									$diagnosislist = $diagnosislist.'хронический';
									
								}
								
								$diagnosislist = $diagnosislist.')</span>';
																
							}
							
							$diagnosislist = $diagnosislist.'</p></div>';
								
						}
						
					}
					
				} else {
					
					$diagnosislist = 'Нет данных';
					
				}

				
				// Дата создания
				$createdat = Carbon::createFromFormat('Y-m-d-H-i-s', $row->visit_date_id);

								
				$output = $output.'
															
					<a href="" class="short_visit m-0 p-0 text-decoration-none" onclick="open_visit('.$row->visit_id.'); return false;" class="text-decoration-none block leading-5 text-gray-700 transition duration-150 ease-in-out hover:bg-gray-100 focus:outline-none focus:bg-gray-100 p-0">

						<div class="hoverDiv border-bottom m-0 p-0">

							<div class="row justify-content-center mx-2 py-2">
									
								<div class="col-lg-1 px-0 px-lg-2 py-1 d-flex justify-content-lg-center align-items-lg-center" align="center">
									<div class="d-flex d-lg-none text-dark table-title-notlg align-self-center">ID:</div>
									<div class="table-text text-body text-break align-self-center text-start text-lg-center">'.$row->visit_id.'</div>
								</div>
								
								<div class="col-lg px-0 px-lg-2 py-1 d-flex justify-content-lg-center align-items-lg-center" align="center">
									<div class="d-flex d-lg-none text-dark table-title-notlg align-self-center">Дата:</div>
									<div class="table-text text-body text-break align-self-center text-start text-lg-center">'.Carbon::parse($row->date_of_visit)->format('d.m.Y').'</div>
								</div>
								
								<div class="col-lg px-0 px-lg-2 py-1 d-flex justify-content-lg-center align-items-lg-center" align="center">
									<div class="d-flex d-lg-none text-dark table-title-notlg align-self-center">Врач:</div>
									<div class="table-text text-body text-break align-self-center text-start text-lg-center">'.$row->doctor.'</div>
																		
								</div>
											
								<div class="col-lg px-0 px-lg-2 py-1 d-flex justify-content-lg-center align-items-lg-center" align="center">
									<div class="d-flex d-lg-none text-dark table-title-notlg align-self-center">Цель обращения:</div>
									<div class="table-text text-body text-break align-self-center text-start text-lg-center">'.$visit_purpose.'</div>
								</div>
								
								<div class="col-lg px-0 px-lg-2 py-1 d-flex justify-content-lg-center align-items-lg-center" align="center">
									<div class="d-flex d-lg-none text-dark table-title-notlg align-self-center">Тип обращения:</div>
									<div class="table-text text-body text-break align-self-center text-start text-lg-center">'.$visit_type.'</div>
								</div>
								
								<div class="col-lg px-0 px-lg-2 py-1 d-flex justify-content-start justify-content-lg-center align-items-lg-center" align="center">
									<div class="d-flex d-lg-none text-dark table-title-notlg align-self-center">Диагноз:</div>
									<div class="flex-row table-text text-body text-break align-self-center">'.$diagnosislist.'</div>
								</div>
								
								<div class="col-lg px-0 px-lg-2 py-1 d-flex justify-content-lg-center align-items-lg-center" align="center">
									<div class="d-flex d-lg-none text-dark table-title-notlg align-self-center">Создан:</div>
									<div class="table-text text-body text-break align-self-center fst-italic text-start text-lg-center">'.Carbon::parse($createdat)->format('d.m.Y H:i:s').'</div>
								</div>
								
							</div>

						</div>
			
					</a>';
					
					
				$output = $output.'<div class="border-bottom m-0 p-0 full_visit d-none">

					<div class="flex-none mt-2 mb-3">
						<div class="align-middle justify-content-start d-flex px-2"><span class="fw-bold">ID:&nbsp;</span>'.$row->visit_id.'</div>
						<div class="align-middle justify-content-start d-flex px-2"><span class="fw-bold">Дата:&nbsp;</span>'.Carbon::parse($row->date_of_visit)->format('d.m.Y').'</div>
						<div class="align-middle justify-content-start d-flex px-2"><span class="fw-bold">Врач:&nbsp;</span>'.$row->doctor.'</div>
						<div class="align-middle justify-content-start d-flex px-2"><span class="fw-bold">Цель обращения:&nbsp;</span>'.$visit_purpose.'</div>
						<div class="align-middle justify-content-start d-flex px-2"><span class="fw-bold">Тип обращения:&nbsp;</span>'.$visit_type.'</div>
					</div>';
				
				if ($row->complaints) {
		
					$output = $output.'<div class="flex-none mb-3">
						<div class="align-middle justify-content-start d-flex px-2"><span class="fw-bold">Жалобы:&nbsp;</span></div>
						<div class="border border rounded mx-2 p-2 align-middle justify-content-start">'.$row->complaints.'</div>
					</div>';
								
				}
				
				if ($row->inspection_results) {
		
					$output = $output.' <div class="flex-none mb-3">
						<div class="align-middle justify-content-start d-flex px-2"><span class="fw-bold">Результат осмотра:&nbsp;</span></div>
						<div class="border border rounded mx-2 p-2 align-middle justify-content-start">'.$row->inspection_results.'</div>
					</div>';
					
				}				
				
				if ($row->clinic_comments) {	
				
					$output = $output.' <div class="flex-none mb-3 d-print-none">
						<div class="align-middle justify-content-start d-flex px-2"><span class="fw-bold">Комментарий клиники:&nbsp;</span></div>
						<div class="border border rounded mx-2 p-2 align-middle justify-content-start">'.$row->clinic_comments.'</div>
					</div>';
				
				}
				
				// Вес
				$weight = AnimalWeight::where('visit_date_id', $row->visit_date_id)->first();
					
				if ($weight) {
					
					$output = $output.'<div class="flex-none mb-3">
						<div class="align-middle justify-content-start px-2 d-flex"><span class="fw-bold">Вес животного:&nbsp;</span>'.$weight->weight.' кг.</div>
					</div>';
					
				}
				
				// Фото
				$nofilesatall = true;
				
				$photoshtml = '';
				
				$photos = UploadedPhoto::where('visit_date_id', $row->visit_date_id)->get();

				if($photos->count() > 0){
					
					$photoshtml = $photoshtml.'<div class="flex-none mb-1">
						<div class="align-middle justify-content-start d-flex px-2"><span class="fw-bold">Фото:&nbsp;</span></div>
						<div class="p-2 align-middle justify-content-start d-flex">
						<div class="row mt-0 row-cols-2 row-cols-md-4 row-cols-lg-6 row-cols-xl-8 row-cols-xxl-10 g-3 d-flex align-items-center">';
												
					foreach($photos as $photo){
						
						$imageurl = public_path('uploaded_photos/'.$photo->anymal_id.'/' .$photo->image_name);
						
						
						if(\File::exists($imageurl)) {
							
							$nofilesatall = false;
							
							$description = '';
								
							if ($photo->description) {
								$description = $photo->description;
							}
							
										
							$photoshtml = $photoshtml.'																
								<div class="col mt-0 mb-3">
									<a href="uploaded_photos/'.$photo->anymal_id.'/'.$photo->image_name.'" class="fresco" data-fresco-group="visit_photo" data-fresco-caption="'.$description.'" data-fresco-group-options="thumbnails: false">
										<img src="uploaded_photos/'.$photo->anymal_id.'/'.$photo->image_name.'" class="img-fluid rounded">
									</a>
								</div>
							';	
						
						}
								
					}
					
					$photoshtml = $photoshtml.'</div> </div> </div>';
										
					// Если записи в БД есть, а файлов нет
					if ($nofilesatall) {
						
						$photoshtml = '';
						
					}
										
					$output = $output.$photoshtml;

				}
				
				
				// Диагнозы
				$diagnosislistfull = '';
				
				$diagnosisfull = DiagnosisVisits::where('visit_date_id', $row->visit_date_id)->get();
				
				if($diagnosisfull->count() > 0){
					
					$diagnosislistfull = $diagnosislistfull.'<div class="flex-none mb-3 px-2"> <div class="align-middle justify-content-start d-flex"><span class="fw-bold">Диагнозы:&nbsp;</span></div>';
					
					foreach($diagnosisfull as $diagnosefull){
						
						$onediagnosefull = DiagnosisTypes::where('id', $diagnosefull->diagnosis_id)->first();
						
						if ($onediagnosefull) {
							
							$diagnosislistfull = $diagnosislistfull.'<div class="justify-content-start align-items-center text-break">'.$onediagnosefull->diagnosis_title;
							
							
							if ($diagnosefull->need_aprove_id == 1 | $diagnosefull->permanent_id == 1) {
									
								$diagnosislistfull = $diagnosislistfull.'<span class="fst-italic text-primary">&nbsp;(';
								
								if ($diagnosefull->need_aprove_id == 1) {
									
									$diagnosislistfull = $diagnosislistfull.'неточный';
								}
								
								if ($diagnosefull->permanent_id  == 1 & $diagnosefull->need_aprove_id == 1) {
									
									$diagnosislistfull = $diagnosislistfull.',&nbsp;хронический';
									
								} else if ($diagnosefull->permanent_id  == 1 & $diagnosefull->need_aprove_id == 0) {
									
									$diagnosislistfull = $diagnosislistfull.'хронический';
									
								}
								
								$diagnosislistfull = $diagnosislistfull.')</span>';
																
							}
							
							$diagnosislistfull = $diagnosislistfull.';</div>';
														
						}
						
					}
					
					$diagnosislistfull = $diagnosislistfull.'</div>';
					
					$output = $output.$diagnosislistfull;
									
				} 


				// Назначены исследования
				$researchlist = '';
				
				$researches = json_decode($row->research_needed, TRUE);
								
				if(count((array)$researches) > 0){
					
					$researchlist = $researchlist.'<div class="flex-none mb-3 px-2"> <div class="align-middle justify-content-start d-flex"><span class="fw-bold">Назначены исследования:&nbsp;</span></div>';
					
					foreach($researches as $research){
						
						if ($research) {
							
							$researchlist = $researchlist.'<div class="justify-content-start align-items-center text-break">'.$research.';</div>';
								
						}
						
					}
					
					$researchlist = $researchlist.'</div>';
					
					$output = $output.$researchlist;
									
				}
				
				
				// Выполнены исследования
				$researchesmade = '';
				
				$researches = Researches::where('visit_id', $row->visit_id)->get();
				
				if($researches->count() > 0){
					
					$researchesmade = $researchesmade.'<div class="flex-none mb-3 px-2"> <div class="align-middle justify-content-start d-flex"><span class="fw-bold">Выполнены исследования:&nbsp;</span></div>';
					
					foreach($researches as $research){
						
						$researchesmade = $researchesmade.'<div class="justify-content-start align-items-center text-break"><a href="#" onclick="open_research_from_visit('.$research->research_id.'); event.preventDefault();" class="">'.$research->research_name.'</a>;</div>';
						
					}
					
					$researchesmade = $researchesmade.'</div>';
					
				}
				
				$output = $output.$researchesmade;
				
				
				// Назначены анализы
				$analislist = '';
				
				$analisys = json_decode($row->analisys_needed, TRUE);
								
				if(count((array)$analisys) > 0){
					
					$analislist = $analislist.'<div class="flex-none mb-3 px-2"> <div class="align-middle justify-content-start d-flex"><span class="fw-bold">Назначены анализы:&nbsp;</span></div>';
					
					foreach($analisys as $analis){
						
						if ($analis) {
							
							$analislist = $analislist.'<div class="justify-content-start align-items-center text-break">'.$analis.';</div>';
								
						}
						
					}
					
					$analislist = $analislist.'</div>';
					
					$output = $output.$analislist;
									
				}
				
				
				// Результаты анализов

				$analysismade = '';
				
				$analysis = Analysis::where('visit_id', $row->visit_id)->get();
				
				if($analysis->count() > 0){
					
					$analysismade = $analysismade.'<div class="flex-none mb-3 px-2"> <div class="align-middle justify-content-start d-flex"><span class="fw-bold">Результаты анализов:&nbsp;</span></div>';
					
					foreach($analysis as $analys){
						
						$analysismade = $analysismade.'<div class="justify-content-start align-items-center text-break"><a href="#" onclick="open_analysis_from_visit('.$analys->analysis_id.'); event.preventDefault();" class="">'.$analys->analysis_name.'</a>;</div>';
						
					}
					
					$analysismade = $analysismade.'</div>';
					
				}
				
				$output = $output.$analysismade;


				if ($row->recomendation) {
		
					$output = $output.' <div class="flex-none mb-3">
						<div class="align-middle justify-content-start d-flex px-2"><span class="fw-bold">Лечение и рекомендации:&nbsp;</span></div>
						<div class="border border rounded mx-2 p-2 align-middle justify-content-start">'.$row->recomendation.'</div>
					</div>';
					
				}
				
				$output = $output.'</div>';	

				$output = $output.'</div>';

			}
						
			$output = $output.'</div>';

		} else {
						
			$output = '		
				<div class="d-flex justify-content-center align-items-center">
					<div class="d-block px-4 py-2">
						Нет приемов
					</div>
				</div>';
		}
								   
		return response()->json(['success'=>$output]);
		
	} 
	
	
	// Список визитов для Select2
	public function visitsForSelect(Request $request){
		
		$visits = Visits::where('patient_id', $request->anymal_id)->latest('date_of_visit')->orderBy('visit_id', 'desc')->get();
		 
		if($visits->count() > 0){
			
			$formatted_types = [];
			
			foreach($visits as $row){
				$formatted_types[] = ['id' => $row->visit_id, 'text' => Carbon::parse($row->date_of_visit)->format('d.m.Y').'; ID:'.$row->visit_id];
			}
			
			return \Response::json($formatted_types);
			
		} else {
			return \Response::json([]);	
		}
		
    }
	
	
	
	public function getVisitData (Request $request){
		
		$visit = Visits::where('visit_id', $request->get('visit_id'))->first();

		if($visit){
			
			// Удаляем файлы _change, т.к. их не должно быть если прием не был изменен (сохранен)
			$photostodelete = UploadedPhoto::where('visit_date_id', $visit->visit_date_id.'_change')->get();		
			if($photostodelete->count() > 0){
				foreach($photostodelete as $row){
					$image_path = public_path('uploaded_photos/'.$row->anymal_id.'/' .$row->image_name);  // Value is not URL but directory file path
					if(\File::exists($image_path)) {
						\File::delete($image_path);
					}
					UploadedPhoto::destroy($row->id);
				}
			}
			
			
			// Поиск id доктора
			$doctorid = null;
			
			$doctors = Staff::where('position', 2)->get();
		 
			if($doctors->count() > 0){
				
				foreach($doctors as $row){
					
					$last_name = '';
					if ($row->last_name) {
						$last_name = $row->last_name.' ';
					}
					
					$first_name = '';
					if ($row->first_name && $row->middle_name) {
						$first_name = $row->first_name.' ';
					} else {
						$first_name = $row->first_name;
					}
					
					$middle_name = '';
					if ($row->middle_name) {
						$middle_name = $row->middle_name;
					}
					
					
					$fio = $last_name.$first_name.$middle_name;
					
					if ($visit->doctor == $fio) {
						$doctorid = $row->staff_id;	
					}
				}				
			}
			
			$anymal_weight = '';
			
			// Вес
			$weight = AnimalWeight::where('visit_date_id', $visit->visit_date_id)->first();
			
			if ($weight) {
				$anymal_weight = number_format($weight->weight, 2, '.', ' ');
			}
			
			
			// Диагнозы
			$diagnosishtml = '';
			
			$diagnosis = DiagnosisVisits::where('visit_date_id', $visit->visit_date_id)->get();
					
			if($diagnosis->count() > 0){
									
				foreach($diagnosis as $diagnose){
					
					$onediagnose = DiagnosisTypes::where('id', $diagnose->diagnosis_id)->first();
					
					if ($onediagnose) {
												
						$diagnosishtml = $diagnosishtml.'<div class="row diagnosis-group"> <div class="d-flex px-0"> <div class="justify-content-left align-self-enter align-items-center d-flex px-2 mb-2 fs-6 fw-bolder text-break">'.$onediagnose->diagnosis_title.'</div>';
								
						if ($diagnose->need_aprove_id == 1 | $diagnose->permanent_id == 1) {
														
							$diagnosishtml = $diagnosishtml.'<div class="justify-content-left align-self-center align-items-center flex-column px-2 mb-2">';
													
							if ($diagnose->need_aprove_id == 1) {		
								$diagnosishtml = $diagnosishtml.'<div class="justify-content-left align-self-center align-items-center d-flex fst-italic fs-6 text-break">Требует уточнения</div>';								
							}
														
							if ($diagnose->permanent_id == 1) {											
								$diagnosishtml = $diagnosishtml.'<div class="justify-content-left align-self-center align-items-center d-flex fst-italic fs-6 text-break">Хронический</div>';						
							}
														
							$diagnosishtml = $diagnosishtml.'</div>';
															
						}
						
						$diagnosishtml = $diagnosishtml.'<div class="mb-2 px-2 justify-content-left align-items-center align-self-center d-flex px-2 mb-2"> <a class="nav-link p-1 remove-diagnosis" role="button">	<img class="img-responsive" width="25" height="25" src="images/delete_photo.png"> </a> </div> <input name="diagnosis_todb[]" id="diagnosis_todb[]" value="'.$onediagnose->id.'" hidden="true"/> <input name="need_aprove_todb[]" id="need_aprove_todb[]" value="'.$diagnose->need_aprove_id.'" hidden="true"/> <input name="permanent_todb[]" id="permanent_todb[]" value="'.$diagnose->permanent_id.'" hidden="true"/> </div> </div>';
			
					}
					
				}
									
			} 
			
			
			// Назначены исследования
			$researchlist = '';
			
			$researches = json_decode($visit->research_needed, TRUE);
							
			if(count((array)$researches) > 0){
								
				foreach($researches as $research){
					
					if ($research) {
						
						$researchlist = $researchlist.'<div class="row researches-group"> <div class="d-flex px-0"> <div class="justify-content-left align-self-enter align-items-center d-flex px-2 mb-2 fs-6 fw-bolder text-break">'.$research.'</div> <div class="mb-2 px-2 justify-content-left align-items-center align-self-center d-flex px-2 mb-2"> <a class="nav-link p-1 remove-researches" role="button"> <img class="img-responsive" width="25" height="25" src="images/delete_photo.png"> </a> </div> <input name="researches_todb[]" id="researches_todb[]" value="'.$research.'" hidden="true"/> </div> </div>';
							
					}
					
				}
								
			}
			
			
			// Назначены анализы
			$analislist = '';
			
			$analisys = json_decode($visit->analisys_needed, TRUE);
							
			if(count((array)$analisys) > 0){
								
				foreach($analisys as $analis){
					
					if ($analis) {
						
						$analislist = $analislist.'<div class="row analisys-group"> <div class="d-flex px-0"> <div class="justify-content-left align-self-enter align-items-center d-flex px-2 mb-2 fs-6 fw-bolder text-break">'.$analis.'</div> <div class="mb-2 px-2 justify-content-left align-items-center align-self-center d-flex px-2 mb-2"> <a class="nav-link p-1 remove-analisys" role="button"> <img class="img-responsive" width="25" height="25" src="images/delete_photo.png"> </a> </div> <input name="analisys_todb[]" id="analisys_todb[]" value="'.$analis.'" hidden="true"/> </div> </div>';
							
					}
					
				}
							
			}
			
				
			return response()->json(['success'=>$visit, 'doctorid'=>$doctorid, 'weight'=>$anymal_weight, 'diagnosis'=>$diagnosishtml, 'researches'=>$researchlist, 'analisys'=>$analislist]);
			
		} else {
			
			return response()->json(['error'=>'Ошибка загрузки данных']);
			
		}
		
	}
	
	
	public function getOneVisit(Request $request){
			
		$output = '';
		
		$outputprint = '';
		
		$position = 0;
		
		$worker = Staff::where('staff_id', $request->get('staff_id'))->first();
		
		if ($worker) {
			$position = $worker->position;
		}
		
			
		if ($request->get('staff_id') == 0 | $position == 1 | $position == 2) {
			
			$output = '<div class="d-flex align-items-center">								

					<div class="col-6 d-flex justify-content-start align-items-center">
						<button type="button" onclick="close_visit()" class="btn btn-sm border-1 border-secondary text-secondary">&#8592 К списку</button>
					</div>

					<div class=" col-6 d-flex justify-content-end align-items-center" >								
						<a class="nav-link p-1" onclick="print_visit()" role="button">	
							<img class="img-responsive" width="40" height="40" src="images/print.png">
						</a>
						
						<div class="btn-group dropstart dropdown">							  
							<a class="nav-link p-1" data-bs-toggle="dropdown" aria-expanded="false" role="button">	
								<img class="img-responsive" width="40" height="40" src="images/settings.png">
							</a>
							<ul class="dropdown-menu dropdown-3">
								<li><a class="dropdown-item" role="button" onclick="change_visit('.$request->get('visit_id').')">Изменить прием</a></li>
								<li><a class="dropdown-item" role="button" onclick="delete_visit_dialog('.$request->get('visit_id').')">Удалить прием</a></li>
							</ul>
						</div>
					</div>	
					
				</div>';
			
		} else {
			
			$output = '<div class="d-flex align-items-center">								

					<div class="col-6 d-flex justify-content-start align-items-center">
						<button type="button" onclick="close_visit()" class="btn btn-sm border-1 border-secondary text-secondary">&#8592 К списку</button>
					</div>

					<div class=" col-6 d-flex justify-content-end align-items-center" >								
						<a class="nav-link p-1" onclick="print_visit()" role="button">	
							<img class="img-responsive" width="40" height="40" src="images/print.png">
						</a>
					</div>	
					
				</div>';
			
		}
		
			
		$output = $output.'

			<div name="visit" id="visit" class="border border rounded p-2 mt-2">
				
				<div class="flex-none my-3 mx-2">
						
					<div class="row px-2">
					
						<div class="col-lg px-0 pb-2 pb-lg-0 align-items-center">

							<div class="d-flex justify-content-lg-start align-items-center">								
								<img src="images/dogicon.png" height="50" alt="Доктор Вет">	
								<div class="h3 align-middle d-inline-flex px-2">Доктор Вет</div>
							</div>										

						</div>
						
						<div class="col-lg px-0">
						
							<div class="flex-none justify-content-lg-start align-items-lg-center">								
								<div class="align-middle justify-content-start justify-content-lg-end d-flex px-2">г. Пермь, ул. Лебедева 25 Б</div>
								<div class="align-middle justify-content-start justify-content-lg-end d-flex px-2">Email: Doktorvet.perm@mail.ru</div>
								<div class="align-middle justify-content-start justify-content-lg-end d-flex px-2">Тел.: +7(342)247-01-09</div>
							</div>
						
						</div>
					</div>	
				</div>
										
				<div class="h3 align-middle justify-content-center d-flex px-2 my-3">Прием пациента</div>';	
				
		
		$outputprint = $outputprint.'

			<div class="flex-none my-3 mx-2">
									
				<div class="row px-2">
				
					<div class="col px-0 pb-2 pb-0 align-items-center">

						<div class="d-flex justify-content-start align-items-center">								
							<img src="images/dogicon.png" height="50" alt="Доктор Вет">	
							<div class="h3 align-middle d-inline-flex px-2">Доктор Вет</div>
						</div>										

					</div>
					
					<div class="col px-0">
					
						<div class="flex-none justify-content-start align-items-center">								
							<div class="align-middle justify-content-start justify-content-end d-flex px-2">г. Пермь, ул. Лебедева 25 Б</div>
							<div class="align-middle justify-content-start justify-content-end d-flex px-2">Email: Doktorvet.perm@mail.ru</div>
							<div class="align-middle justify-content-start justify-content-end d-flex px-2">Тел.: +7(342)247-01-09</div>
						</div>
					
					</div>
				</div>	
			</div>
									
			<div class="h3 align-middle justify-content-center d-flex px-2 my-3">Прием пациента</div>';	
		

		$visit = Visits::where('visit_id', $request->get('visit_id'))->first();

		if($visit){
						
			$date = '';
			
			if ($visit->date_of_visit) {
				$date = Carbon::parse($visit->date_of_visit)->format('d.m.Y');
			}
						
			$doctor = '';
			
			if ($visit->doctor) {
				$doctor = $visit->doctor;
			}
	
			$purpose = '';
				
			if ($visit->visit_purpose == 1) {
				$purpose = 'Консультация';
			} else if ($visit->visit_purpose == 2) {
				$purpose = 'Манипуляции';
			} else if ($visit->visit_purpose == 3) {
				$purpose = 'Манипуляции (для другой клиники)';
			} else if ($visit->visit_purpose == 4) {
				$purpose = 'Операция';
			} else if ($visit->visit_purpose == 5) {
				$purpose = 'Стационар';
			} else if ($visit->visit_purpose == 6) {
				$purpose = 'Гигиенические процедуры';
			} else {
				$purpose = 'Нет данных';
			}
			
			$type = '';
			
			if ($visit->visit_type == 1) {
				$type = 'Первичное';
			} else if ($visit->visit_type == 2) {
				$type = 'Повторное';
			} else {
				$type = 'Нет данных';
			}
								
			$output = $output.'
						
				<div class="flex-none mb-3">
					<div class="align-middle justify-content-start d-flex px-2"><span class="fw-bold">ID:&nbsp;</span>'.$visit->visit_id.'</div>
					<div class="align-middle justify-content-start d-flex px-2"><span class="fw-bold">Дата:&nbsp;</span>'.$date.'</div>
					<div class="align-middle justify-content-start d-flex px-2"><span class="fw-bold">Врач:&nbsp;</span>'.$doctor.'</div>
					<div class="align-middle justify-content-start d-flex px-2"><span class="fw-bold">Цель обращения:&nbsp;</span>'.$purpose.'</div>
					<div class="align-middle justify-content-start d-flex px-2"><span class="fw-bold">Тип обращения:&nbsp;</span>'.$type.'</div>
				</div>';
					
			$outputprint = $outputprint.'
						
				<div class="flex-none mb-3">
					<div class="align-middle justify-content-start d-flex px-2"><span class="fw-bold">Дата:&nbsp;</span>'.$date.'</div>
					<div class="align-middle justify-content-start d-flex px-2"><span class="fw-bold">Врач:&nbsp;</span>'.$doctor.'</div>
					<div class="align-middle justify-content-start d-flex px-2"><span class="fw-bold">Цель обращения:&nbsp;</span>'.$purpose.'</div>
					<div class="align-middle justify-content-start d-flex px-2"><span class="fw-bold">Тип обращения:&nbsp;</span>'.$type.'</div>
				</div>';	
					
			$output = $output.'<div class="flex-none mb-3 mx-2">
					
					<div class="row px-2">
					
						<div class="col-lg col-md-border px-0 pb-2 pb-lg-0">
				
							<div class="d-flex justify-content-lg-start align-items-lg-center me-lg-3">								
								<h5 class="text-body align-self-center px-1">Пациент</h5>	
							</div>										
							
							<div class="justify-content-lg-start align-items-lg-center me-lg-3 px-1 d-flex"><span class="fw-bold">Кличка:&nbsp;</span><span class="text-break">'.$request->get('short_name').'</span></div>
							<div class="justify-content-lg-start align-items-lg-center me-lg-3 px-1 d-flex"><span class="fw-bold">Вид:&nbsp;</span><span class="text-break">'.$request->get('anymal_type').'</span></div>  
							<div class="justify-content-lg-start align-items-lg-center me-lg-3 px-1 d-flex"><span class="fw-bold">Порода:&nbsp;</span><span class="text-break">'.$request->get('anymal_breed').'</span></div> 
							<div class="justify-content-lg-start align-items-lg-center me-lg-3 px-1 d-flex"><span class="fw-bold">Пол:&nbsp;</span><span class="text-break">'.$request->get('anymal_sex').'</span></div> 
							<div class="justify-content-lg-start align-items-lg-center me-lg-3 px-1 d-flex"><span class="fw-bold">Окрас:&nbsp;</span><span class="text-break">'.$request->get('anymal_color').'</span></div>
							<div class="justify-content-lg-start align-items-lg-center me-lg-3 px-1 d-flex"><span class="fw-bold">Дата рождения:&nbsp;</span><span class="text-break">'.$request->get('anymal_birthday').'</span></div> 
						
						</div>
						
						<div class="col-lg px-0 pt-2 pt-lg-0 ps-lg-3 px-0">
						
							<div class="d-flex justify-content-lg-start align-items-lg-center me-lg-3">								
								<h5 class="text-body align-self-center px-1">Владелец</h5>
							</div>
							<div class="d-flex justify-content-lg-start align-items-lg-center me-lg-3">	
								<div class="justify-content-lg-start align-items-lg-center me-lg-3 px-1 d-flex"><span class="fw-bold">Ф.И.О.:&nbsp;</span><span class="text-break">'.$request->get('client_name').'</span></div> 				
							</div>
							<div class="d-flex justify-content-lg-start align-items-lg-center me-lg-3">	
								<div class="justify-content-lg-start align-items-lg-center me-lg-3 px-1 d-flex"><span class="fw-bold">Адрес:&nbsp;</span><span class="text-break">'.$request->get('client_address').'</span></div> 			
							</div>
							
						</div>
					</div>	
				</div>';
								
				$outputprint = $outputprint.'<div class="flex-none mb-3 mx-2">
								
					<div class="row px-2">
					
						<div class="col border-end px-0 me-3">
				
							<div class="d-flex justify-content-start align-items-center me-3">								
								<h5 class="text-body align-self-center px-1">Пациент</h5>	
							</div>										
							
							<div class="justify-content-start align-items-center px-1 d-flex"><span class="fw-bold">Кличка:&nbsp;</span><span class="text-break">'.$request->get('short_name').'</span></div>
							<div class="justify-content-start align-items-center px-1 d-flex"><span class="fw-bold">Вид:&nbsp;</span><span class="text-break">'.$request->get('anymal_type').'</span></div>
							<div class="justify-content-start align-items-center px-1 d-flex"><span class="fw-bold">Порода:&nbsp;</span><span class="text-break">'.$request->get('anymal_breed').'</span></div>
							<div class="justify-content-start align-items-center px-1 d-flex"><span class="fw-bold">Пол:&nbsp;</span><span class="text-break">'.$request->get('anymal_sex').'</span></div>
							<div class="justify-content-start align-items-center px-1 d-flex"><span class="fw-bold">Окрас:&nbsp;</span><span class="text-break">'.$request->get('anymal_color').'</span></div>
							<div class="justify-content-start align-items-center px-1 d-flex"><span class="fw-bold">Дата рождения:&nbsp;</span><span class="text-break">'.$request->get('anymal_birthday').'</span></div>
						
						</div>
						
						<div class="col px-0 ps-3">
						
							<div class="d-flex justify-content-start align-items-center me-3">								
								<h5 class="text-body align-self-center px-1">Владелец</h5>
							</div>
								<div class="justify-content-start align-items-center px-1 d-flex"><span class="fw-bold">Ф.И.О.:&nbsp;</span><span class="text-break">'.$request->get('client_name').'</span></div>				
								<div class="justify-content-start align-items-center px-1 d-flex"><span class="fw-bold">Адрес:&nbsp;</span><span class="text-break">'.$request->get('client_address').'</span></div>				
							
						</div>
					</div>	
				</div>';
								
				if ($visit->complaints) {
					
					$output = $output.'<div class="flex-none mb-3">
						<div class="align-middle justify-content-start d-flex px-2"><span class="fw-bold">Жалобы:&nbsp;</span></div>
						<div class="border border rounded mx-2 p-2 align-middle justify-content-start text-wrap text-break">'.$visit->complaints.'</div>
					</div>';
					
					$outputprint = $outputprint.'<div class="flex-none mb-3">
						<div class="align-middle justify-content-start d-flex px-2"><span class="fw-bold">Жалобы:&nbsp;</span></div>
						<div class="border border rounded mx-2 p-2 align-middle justify-content-start text-wrap text-break">'.$visit->complaints.'</div>
					</div>';
					
				}	

				if ($visit->inspection_results) {
					
					$output = $output.' <div class="flex-none mb-3">
						<div class="align-middle justify-content-start d-flex px-2"><span class="fw-bold">Результат осмотра:&nbsp;</span></div>
						<div class="border border rounded mx-2 p-2 align-middle justify-content-start text-wrap text-break">'.$visit->inspection_results.'</div>
					</div>';
					
					$outputprint = $outputprint.'<div class="flex-none mb-3">
						<div class="align-middle justify-content-start d-flex px-2"><span class="fw-bold">Результат осмотра:&nbsp;</span></div>
						<div class="border border rounded mx-2 p-2 align-middle justify-content-start text-wrap text-break">'.$visit->inspection_results.'</div>
					</div>';
					
				}


				if ($visit->recomendation) {
					
					$output = $output.' <div class="flex-none mb-3">
						<div class="align-middle justify-content-start d-flex px-2"><span class="fw-bold">Лечение и рекомендации:&nbsp;</span></div>
						<div class="border border rounded mx-2 p-2 align-middle justify-content-start text-wrap text-break">'.$visit->recomendation.'</div>
					</div>';
					
					$outputprint = $outputprint.'<div class="flex-none mb-3">
						<div class="align-middle justify-content-start d-flex px-2"><span class="fw-bold">Лечение и рекомендации:&nbsp;</span></div>
						<div class="border border rounded mx-2 p-2 align-middle justify-content-start text-wrap text-break">'.$visit->recomendation.'</div>
					</div>';
					
				}
				
				
				if ($visit->clinic_comments) {	
				
					$output = $output.' <div class="flex-none mb-3 d-print-none">
						<div class="align-middle justify-content-start d-flex px-2"><span class="fw-bold">Комментарий клиники:&nbsp;</span></div>
						<div class="border border rounded mx-2 p-2 align-middle justify-content-start text-wrap text-break">'.$visit->clinic_comments.'</div>
					</div>';
				
				}
									
				// Вес
				$weight = AnimalWeight::where('visit_date_id', $visit->visit_date_id)->first();
					
				if ($weight) {
					
					$output = $output.'<div class="flex-none mb-3">
						<div class="align-middle justify-content-start px-2 d-flex"><span class="fw-bold">Вес животного:&nbsp;</span>'.$weight->weight.' кг.</div>
					</div>';
					
					$outputprint = $outputprint.'<div class="flex-none mb-3">
						<div class="align-middle justify-content-start px-2 d-flex"><span class="fw-bold">Вес животного:&nbsp;</span>'.$weight->weight.' кг.</div>
					</div>';
					
				}
															
				// Фото
				$nofilesatall = true;
				
				$photoshtml = '';
				
				//$photoshtmlprint = '';
				
				$photos = UploadedPhoto::where('visit_date_id', $visit->visit_date_id)->get();
		
				if($photos->count() > 0){
					
					$photoshtml = $photoshtml.'<div class="flex-none mb-1">
						<div class="align-middle justify-content-start d-flex px-2"><span class="fw-bold">Фото:&nbsp;</span></div>
						<div class="p-2 align-middle justify-content-start d-flex">
						<div class="row mt-0 row-cols-2 row-cols-md-4 row-cols-lg-6 row-cols-xl-8 row-cols-xxl-10 g-3 d-flex align-items-center">';
						
						
					/*$photoshtmlprint = $photoshtmlprint.'<div class="flex-none mb-1">
						<div class="align-middle justify-content-start d-flex px-2"><span class="fw-bold">Фото:&nbsp;</span></div>
						<div class="p-2 align-middle justify-content-start d-flex">
						<div class="row mt-0 row-cols-2 g-3 d-flex align-items-center">';*/
					
					foreach($photos as $row){
						
						$imageurl = public_path('uploaded_photos/'.$row->anymal_id.'/' .$row->image_name);
						
						
						if(\File::exists($imageurl)) {
							
							$nofilesatall = false;
							
							$description = '';
								
							if ($row->description) {
								$description = $row->description;
							}
							
										
							$photoshtml = $photoshtml.'																
								<div class="col mt-0 mb-3">
									<a href="uploaded_photos/'.$row->anymal_id.'/'.$row->image_name.'" class="fresco" data-fresco-group="visit_photo" data-fresco-caption="'.$description.'" data-fresco-group-options="thumbnails: false">
										<img src="uploaded_photos/'.$row->anymal_id.'/'.$row->image_name.'" class="img-fluid rounded">
									</a>
								</div>
							';	
							
							/*$photoshtmlprint = $photoshtmlprint.'																
								<div class="col mt-0 mb-3">
									<img src="uploaded_photos/'.$row->anymal_id.'/'.$row->image_name.'" class="img-fluid rounded">
								</div>
							';*/	
						
						}
								
					}
					
					$photoshtml = $photoshtml.'</div> </div> </div>';
					
					//$photoshtmlprint = $photoshtmlprint.'</div> </div> </div>';
					
					
					// Если записи в БД есть, а файлов нет
					if ($nofilesatall) {
						
						$photoshtml = '';
						
						//$photoshtmlprint = '';
						
					}
										
					$output = $output.$photoshtml;
					
					//$outputprint = $outputprint.$photoshtmlprint;

				}
								
				// Диагнозы
				$diagnosislist = '';
				
				$diagnosis = DiagnosisVisits::where('visit_date_id', $visit->visit_date_id)->get();
				
				if($diagnosis->count() > 0){
					
					$diagnosislist = $diagnosislist.'<div class="flex-none mb-3 px-2"> <div class="align-middle justify-content-start d-flex"><span class="fw-bold">Диагнозы:&nbsp;</span></div>';
					
					foreach($diagnosis as $diagnose){
						
						$onediagnose = DiagnosisTypes::where('id', $diagnose->diagnosis_id)->first();
						
						if ($onediagnose) {
							
							$diagnosislist = $diagnosislist.'<div class="justify-content-start align-items-center text-break">'.$onediagnose->diagnosis_title;
							
							
							if ($diagnose->need_aprove_id == 1 | $diagnose->permanent_id == 1) {
									
								$diagnosislist = $diagnosislist.'<span class="fst-italic text-primary">&nbsp;(';
								
								if ($diagnose->need_aprove_id == 1) {
									
									$diagnosislist = $diagnosislist.'неточный';
								}
								
								if ($diagnose->permanent_id  == 1 & $diagnose->need_aprove_id == 1) {
									
									$diagnosislist = $diagnosislist.',&nbsp;хронический';
									
								} else if ($diagnose->permanent_id  == 1 & $diagnose->need_aprove_id == 0) {
									
									$diagnosislist = $diagnosislist.'хронический';
									
								}
								
								$diagnosislist = $diagnosislist.')</span>';
																
							}
							
							$diagnosislist = $diagnosislist.';</div>';
														
						}
						
					}
					
					$diagnosislist = $diagnosislist.'</div>';
					
					$output = $output.$diagnosislist;
					
					$outputprint = $outputprint.$diagnosislist;
									
				} 
				
								
				// Назначены исследования
				$researchlist = '';
				
				$researches = json_decode($visit->research_needed, TRUE);
								
				if(count((array)$researches) > 0){
					
					$researchlist = $researchlist.'<div class="flex-none mb-3 px-2"> <div class="align-middle justify-content-start d-flex"><span class="fw-bold">Назначены исследования:&nbsp;</span></div>';
					
					foreach($researches as $research){
						
						if ($research) {
							
							$researchlist = $researchlist.'<div class="justify-content-start align-items-center text-break">'.$research.';</div>';
								
						}
						
					}
					
					$researchlist = $researchlist.'</div>';
					
					$output = $output.$researchlist;
					
					$outputprint = $outputprint.$researchlist;
									
				}
				
					
				// Выполнены исследования
				$researchesmade = '';
				
				$researches = Researches::where('visit_id', $visit->visit_id)->get();
				
				if($researches->count() > 0){
					
					$researchesmade = $researchesmade.'<div class="flex-none mb-3 px-2"> <div class="align-middle justify-content-start d-flex"><span class="fw-bold">Выполнены исследования:&nbsp;</span></div>';
					
					foreach($researches as $research){
						
						$researchesmade = $researchesmade.'<div class="justify-content-start align-items-center text-break"><a href="#" onclick="open_research_from_visit('.$research->research_id.'); event.preventDefault();" class="">'.$research->research_name.'</a>;</div>';
						
					}
					
					$researchesmade = $researchesmade.'</div>';
					
				}
				
				
				$output = $output.$researchesmade;
				
				$outputprint = $outputprint.$researchesmade;
							
								
				// Назначены анализы
				$analislist = '';
				
				$analisys = json_decode($visit->analisys_needed, TRUE);
								
				if(count((array)$analisys) > 0){
					
					$analislist = $analislist.'<div class="flex-none mb-3 px-2"> <div class="align-middle justify-content-start d-flex"><span class="fw-bold">Назначены анализы:&nbsp;</span></div>';
					
					foreach($analisys as $analis){
						
						if ($analis) {
							
							$analislist = $analislist.'<div class="justify-content-start align-items-center text-break">'.$analis.';</div>';
								
						}
						
					}
					
					$analislist = $analislist.'</div>';
					
					$output = $output.$analislist;
					
					$outputprint = $outputprint.$analislist;
									
				}
				
				
				// Результаты анализов

				$analysismade = '';
				
				$analysis = Analysis::where('visit_id', $visit->visit_id)->get();
				
				if($analysis->count() > 0){
					
					$analysismade = $analysismade.'<div class="flex-none mb-3 px-2"> <div class="align-middle justify-content-start d-flex"><span class="fw-bold">Результаты анализов:&nbsp;</span></div>';
					
					foreach($analysis as $analys){
						
						$analysismade = $analysismade.'<div class="justify-content-start align-items-center text-break"><a href="#" onclick="open_analysis_from_visit('.$analys->analysis_id.'); event.preventDefault();" class="">'.$analys->analysis_name.'</a>;</div>';
						
					}
					
					$analysismade = $analysismade.'</div>';
					
				}
				
				
				$output = $output.$analysismade;
				
				$outputprint = $outputprint.$analysismade;

				
			$output = $output.'</div>';	
			
		} else {
					
		$output = '	

			<div class="d-flex align-items-center">								
				<div class="col-6 d-flex justify-content-start align-items-center">
					<button type="button" onclick="close_visit()" class="btn btn-sm border-1 border-secondary text-secondary">&#8592 К списку</button>
				</div>
			</div>
			
			<div class="d-flex justify-content-center align-items-center">
				<div class="d-block px-4 py-2">
					Данные не найдены
				</div>
			</div>';
			
		}
						   
		return response()->json(['success'=>$output, 'successprint'=>$outputprint]);
		
	} 
	
	

	public function uploadPhoto(Request $request){
		
		$messages = [
			'imagetoload.required' => 'Не выбран файл',
			'imagetoload.image' => 'Неверный формат файла',
			'description.regex' => 'Описание содержит недопустимые символы',
			'anymal_id.required' => 'Ошибка сохранения'
		];
		
		$validator = Validator::make($request->all(), [	
			'imagetoload' => 'required',		
			'imagetoload' => 'required|image',
			'description' => ['nullable', 'max:1000', 'regex:/^[^`~#$^&*=\\|\\[\\]{}"<>]*$/',],
			'anymal_id' => 'required',	
        ], $messages);
		
		if ($validator->passes()) {	
		
			$file = $request->file('imagetoload');			
			
			// Имя и разрешение файла
			$ext = $file->extension();
			$name=uniqid().'.'.$ext;
			
			//$file->move(public_path().'/uploaded_photos/'.$request->patient_id.'/', $name);
			
			// Создание папки, если нет
			$path = public_path().'/uploaded_photos/'.$request->anymal_id;
			\File::isDirectory($path) or \File::makeDirectory($path, 0777, true, true);
			
			$image_resize = Image::make($file ->getRealPath());

			// Чтобы не менялась ориентация
			$image_resize->orientate();			

			// Меняем размер фото
            $width = 1000; // your max width
			$height = 1000; // your max height

			$image_resize->height() > $image_resize->width() ? $width=null : $height=null;
			$image_resize->resize($width, $height, function ($constraint) {
				$constraint->aspectRatio();
			});			
			
			// Сохраняем фото
			$image_resize->save(public_path('uploaded_photos/'.$request->anymal_id.'/' .$name));
			
			$visit_date_id = '';
			
			if ($request->visit_date_id) {
				$visit_date_id = $request->visit_date_id;
			}
			
			UploadedPhoto::create(['visit_date_id'  => $visit_date_id,'anymal_id' => $request->anymal_id,
			'image_name' => $name,'image_old_name' =>  $file->getClientOriginalName(),'description'  => $request->description,]);
			
			return response()->json(['success'=>'Сохранено']);	
			
		} 
		
		return response()->json(['error'=>$validator->messages()->first()]);
		
	}
	
	
	public function getPhotoforVisit(Request $request){
	
		$output = '';
			
		if ($request->get('neworchange') == 0) {
			$photos = UploadedPhoto::where('visit_date_id', $request->get('visit_date_id'))->get();
		} else if ($request->get('neworchange') == 1) {
			$photos = UploadedPhoto::where('visit_date_id', $request->get('visit_date_id'))->orWhere('visit_date_id', $request->get('visit_date_id').'_change')->get(); 
		}		
		if($photos->count() > 0){
			
			
			
			$output = '<div class="row mt-0 row-cols-1 row-cols-md-2 row-cols-lg-3 row-cols-xl-4 row-cols-xxl-5 g-3 px-2 d-flex align-items-center">';
			
			foreach($photos as $row){
								
				// Фото для удаления
				$phototodelete = (array) json_decode($request->get('phototodelete'));
				
				$contains = false;
				
				foreach($phototodelete as $value){
					if($row->id == $value){
						$contains = true;
					}
				}
				
				// Не показываем фото для удаления
				if (!$contains) {
					
					$imageurl = public_path('uploaded_photos/'.$row->anymal_id.'/' .$row->image_name);
								
					if(\File::exists($imageurl)) {
									
						$output = $output.'
																
							<div class="col mt-0 mb-3">
								<div class="card w-100">			
									<div class="card-header bg-transparent border-transparent ps-2 pe-1 py-1">		
										<div class="d-flex justify-content-start align-items-center">										
											<div class="fst-italic text-break fs-6">'.$row->image_old_name.'</div>	
											<div class="btn-group ms-auto">							  
												<a class="nav-link p-1" onclick="delete_photo('.$row->id.'); return false;" role="button">	
													<img class="img-responsive" width="25" height="25" src="images/delete_photo.png">
												</a>
											</div>										
										</div>	
									</div>
									<img src="uploaded_photos/'.$row->anymal_id.'/'.$row->image_name.'" class="card-img-bottom">
								</div>
							</div>			
						';	
					} else {
					
						UploadedPhoto::destroy($row->id);
					
					}
			
				}
			
			}
			
			$output = $output.'</div>';

		}
								   
		return response()->json(['success'=>$output]);
	
	}
	
	
	
	public function getPhotoforPatient(Request $request){
		
		// Удаляем лишние фото
		$this->deleteNoNeededPhotos($request->get('anymal_id'));
		
		
		$output = '';
		
		$nofilesatall = true;
	
		$photos = UploadedPhoto::where('anymal_id', $request->get('anymal_id'))->orderBy('id', 'desc')->get();
		
		if($photos->count() > 0){
				
			$output = '<div class="row mt-0 row-cols-2 row-cols-md-4 row-cols-lg-6 row-cols-xl-8 row-cols-xxl-10 g-3 px-2 d-flex align-items-center">';
			
			foreach($photos as $row){
				
				$imageurl = public_path('uploaded_photos/'.$row->anymal_id.'/' .$row->image_name);
				
				//$imageurl = request()->getSchemeAndHttpHost().'/uploaded_photos/'.$row->anymal_id.'/'.$row->image_name; 
				
				if(\File::exists($imageurl)) {
					
					$nofilesatall = false;
	
					$description = '';
					$visit_date = '';
					$doctor = '';
					$caption = '';
						
					if ($row->description) {
						$description = $row->description;
					}
					
					if ($row->visit_date_id) {
						
						$visit = Visits::where('visit_date_id', $row->visit_date_id)->first();
						if ($visit) {
							if ($visit->date_of_visit) {
								if ($description) {
									$visit_date = '<br>Дата приема: '.Carbon::parse($visit->date_of_visit)->format('d.m.Y');
								} else {
									$visit_date = 'Дата приема: '.Carbon::parse($visit->date_of_visit)->format('d.m.Y');
								}
							}
							if ($visit->doctor) {
								if ($description | $visit_date) {
									$doctor = '<br>Лечащий врач: '.$visit->doctor;
								} else {
									$doctor = 'Лечащий врач: '.$visit->doctor;
								}
							}
						}
					}
							
					$caption = $description.$visit_date.$doctor;
									
					$output = $output.'
															
						<div class="col mt-0 mb-3">
							
							<div class="inline-block position-relative">
							
								<a href="uploaded_photos/'.$row->anymal_id.'/'.$row->image_name.'" class="fresco" data-fresco-group="photogroup" data-fresco-caption="'.$caption.'" data-fresco-group-options="thumbnails: false">
									<img src="uploaded_photos/'.$row->anymal_id.'/'.$row->image_name.'" class="img-fluid rounded">
								</a>
														
								<div class="position-absolute top-0 end-0 me-2 mt-2">
									<a class="nav-link p-0" onclick="delete_gallery_photo_dialog('.$row->id.'); return false;" role="button">	
										<img class="img-responsive" width="30" height="30" src="images/trash.png"/>
									</a>
									
									<a class="nav-link p-0 mt-1" onclick="copy_url(\''.$imageurl.'\'); return false;" role="button">	
										<img class="img-responsive" width="30" height="30" src="images/imageurl.png"/>
									</a>							
								</div>
								
							</div>	
							
						</div>
					';	
				} else {
					
					UploadedPhoto::destroy($row->id);
					
				}
			}


			$output = $output.'</div>';
			
			
			// Если записи в БД есть, а файлов нет
			if ($nofilesatall) {
				
				$output = '		
				<div class="d-flex justify-content-center align-items-center">
					<div class="d-block px-4 py-2">
						Нет фото
					</div>
				</div>';
				
			}
			

		} else {
			
			// Если в БД вообще нет записей о фото, то удаляем всю папку.
			$folder_path = public_path('uploaded_photos/'.$request->anymal_id);
			\File::deleteDirectory($folder_path);
			
			$output = '		
				<div class="d-flex justify-content-center align-items-center">
					<div class="d-block px-4 py-2">
						Нет фото
					</div>
				</div>';
		}
								   
		return response()->json(['success'=>$output]);
	
	}
	
	
	
	public function deleteNoNeededPhotos(string $id){
		
		// Удаляем файлы если есть запись в поле id приема и нет такого приема
		$photostodelete = UploadedPhoto::where('anymal_id', $id)->get();		
		if($photostodelete->count() > 0){
			foreach($photostodelete as $row){
				if ($row->visit_date_id) {
					$thereidvisit =Visits::where('visit_date_id', $row->visit_date_id)->first();
					if (!$thereidvisit) {
						$image_path = public_path('uploaded_photos/'.$row->anymal_id.'/' .$row->image_name);  // Value is not URL but directory file path
						if(\File::exists($image_path)) {
							\File::delete($image_path);
						}
						UploadedPhoto::destroy($row->id);
					} 
				}
			}
		}
		
		
		// Удаляем файлы, которых нет в БД.
		$folder_path = public_path('uploaded_photos/'.$id);
		
		
		if (\File::exists($folder_path)) {
			$files = \File::allFiles($folder_path);

			foreach($files as $file){
				
				$filethereisinbd =UploadedPhoto::where('image_name', $file->getFilename())->first();
				
				if (!$filethereisinbd) {
					$image_path = public_path('uploaded_photos/'.$id.'/' .$file->getFilename()); 
					if(\File::exists($image_path)) {
						\File::delete($image_path);
					}
				}
				
			}
		
		}
		
	}
	
	

	public function deletePhoto(Request $request){
										
		if ($request->get('id') !== null & $request->get('id') !== '0') {		
			
			$photo = UploadedPhoto::where('id', $request->get('id'))->first();
			
			if ($photo) { 
			
				$image_path = public_path('uploaded_photos/'.$photo->anymal_id.'/' .$photo->image_name); 
					if(\File::exists($image_path)) {
					\File::delete($image_path);
				}
				
				UploadedPhoto::destroy($request->get('id'));
				
				return response()->json(['success'=>'Удачно удалено']);
					
			} else {
				
				return response()->json(['error'=>'Ошибка удаления, перезагрузите страницу и попробуйте еще раз']);
				
			}
		
		} else {
			return response()->json(['error'=>'Ошибка удаления, перезагрузите страницу и попробуйте еще раз']);
		}
	}
	
	
	
	public function newResearch(Request $request){
		
		$messages = [
			'anymal_id.required' => 'Ошибка сохранения',
			'research_date.required' => 'Не заполнено поле',
			'research_date.date_format' => 'Неверный формат даты',
			'research_name.regex' => 'Поле содержит недопустимые символы',
			'research_name.required' => 'Не заполнено поле',
			'research_doctor.required' => 'Не выбрано значение'
		];
		
		$validator = Validator::make($request->all(), [	
			'anymal_id' => 'required',		
			'research_date' => ['required', 'date_format:d.m.Y',],
			'research_name' => ['required', 'max:1000', 'regex:/^[^`~#$^&*=\\|\\[\\]{}<>]*$/',],
			'research_doctor' => 'required',
        ], $messages);
		
		
		if ($validator->passes()) {
						
			// Конвертируем дату
			$date_of_research = Carbon::parse($request->get('research_date'))->format('Y-m-d');
	
			$researchid = Researches::updateOrCreate(['research_id' => $request->research_id], ['date_of_research'  => $date_of_research, 'doctor'  => $request->doctor_text, 'patient_id' => $request->anymal_id,
			'visit_id' => $request->to_visit_new_research, 'research_name' => $request->research_name, 'research_text' => $request->research_plate_text,]);
			
			return response()->json(['success'=>'Сохранено', 'researchid'=>$researchid->research_id]);
			
		}
		
		return response()->json(['error'=>$validator->messages()->get('*')]);			
					
	}
	
	
	
	// Список исследований
	public function getResearchesList(Request $request){
		
		
		$output = '';
		
		
		$research_name_filter = $request->research_name_filter;
		
		$to_visit_filter = $request->to_visit_filter;
		
		$doctor_filter = $request->doctor;
		
		$research_visit_date_start = '';
		
						
		if (!empty($request->research_visit_date_start)) {
			$research_visit_date_start = Carbon::parse($request->get('research_visit_date_start'))->format('Y-m-d');
		}			
		
		if (!empty($request->research_visit_date_end)) {
			$research_visit_date_end = Carbon::parse($request->get('research_visit_date_end'))->format('Y-m-d');
		} else {
			$research_visit_date_end = Carbon::now()->format('Y-m-d');;
		}
		
		if (!empty($request->research_visit_date_start) && !empty($request->research_visit_date_end) && Carbon::createFromFormat('Y-m-d', $research_visit_date_start)->gt(Carbon::createFromFormat('Y-m-d', $research_visit_date_end))) {
			return response()->json(['error'=>'Некорректный выбор дат']);
		}


		$researches = Researches::where('patient_id', $request->get('anymal_id'))
		
			->when(!empty($request->research_visit_date_start), function ($query) use ($research_visit_date_start, $research_visit_date_end) {
			$query->whereBetween('date_of_research', [$research_visit_date_start, $research_visit_date_end]);})	
		
			->when(empty($request->research_visit_date_start) & !empty($request->research_visit_date_end), function ($query) use ($research_visit_date_end) {
			$query->whereDate('date_of_research', '<=', $research_visit_date_end);})
			
			->when(!empty($doctor_filter), function ($query) use ($doctor_filter) {
			$query->where('doctor', $doctor_filter);})
									
			->when(!empty($to_visit_filter), function ($query) use ($to_visit_filter) {
			$query->where('visit_id', $to_visit_filter);})
			
			->when(!empty($research_name_filter), function ($query) use ($research_name_filter) {
			$query->where('research_name', 'like', '%'.$research_name_filter.'%');})
	
		->latest('date_of_research')
		->orderBy('research_id', 'desc')
		->get(); 
		
		
		if($researches->count() > 0){
			
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
						
						.hoverDiv {
							background: #fff;
						}
						
						.hoverDiv:hover {
							background: #f5f5f5;
						}

					</style>
					
					<div class="d-none d-lg-block border-bottom p-0 m-0">
						<div class="row justify-content-center mx-2 py-2">
							<div class="table-title text-dark col-1 px-2 justify-content-center d-flex align-items-center text-break">ID</div>
							<div class="table-title text-dark col px-2 justify-content-center d-flex align-items-center text-break">Дата</div>
							<div class="table-title text-dark col px-2 justify-content-center d-flex align-items-center text-break">Врач</div>
							<div class="table-title text-dark col px-2 justify-content-center d-flex align-items-center text-break">Наименование</div>
							<div class="table-title text-dark col px-2 justify-content-center d-flex align-items-center text-break">К визиту</div>				
						</div>
					</div>
				';
			
			foreach($researches as $row){
				
				
				$tovisit = '';
				
				if ($row->visit_id) {
				
					$visit = Visits::where('visit_id', $row->visit_id)->first();
					
					if ($visit) {
						
						$tovisit = '<span>'.Carbon::parse($visit->date_of_visit)->format('d.m.Y').';</span><span class="">&nbspID: '.$visit->visit_id.'</span>';
						
					}
				
				}
					
				$output = $output.'
															
					<a href="" onclick="open_research('.$row->research_id.'); return false;" class="text-decoration-none block leading-5 text-gray-700 transition duration-150 ease-in-out hover:bg-gray-100 focus:outline-none focus:bg-gray-100 p-0">

						<div class="hoverDiv border-bottom mx-0">

							<div class="row justify-content-center mx-2 py-2">
									
								<div class="col-lg-1 px-0 px-lg-2 py-1 d-flex justify-content-lg-center align-items-lg-center" align="center">
									<div class="d-flex d-lg-none text-dark table-title-notlg align-self-center">ID:</div>
									<div class="table-text text-body text-break align-self-center text-start text-lg-center">'.$row->research_id.'</div>
								</div>
								
								<div class="col-lg px-0 px-lg-2 py-1 d-flex justify-content-lg-center align-items-lg-center" align="center">
									<div class="d-flex d-lg-none text-dark table-title-notlg align-self-center">Дата:</div>
									<div class="table-text text-body text-break align-self-center text-start text-lg-center">'.Carbon::parse($row->date_of_research)->format('d.m.Y').'</div>
								</div>
								
								<div class="col-lg px-0 px-lg-2 py-1 d-flex justify-content-lg-center align-items-lg-center" align="center">
									<div class="d-flex d-lg-none text-dark table-title-notlg align-self-center">Врач:</div>
									<div class="table-text text-body text-break align-self-center text-start text-lg-center">'.$row->doctor.'</div>
																		
								</div>
											
								<div class="col-lg px-0 px-lg-2 py-1 d-flex justify-content-lg-center align-items-lg-center" align="center">
									<div class="d-flex d-lg-none text-dark table-title-notlg align-self-center">Наименование:</div>
									<div class="table-text text-body text-break align-self-center text-start text-lg-center">'.$row->research_name.'</div>
								</div>
								
								<div class="col-lg px-0 px-lg-2 py-1 d-flex justify-content-lg-center align-items-lg-center" align="center">
									<div class="d-flex d-lg-none text-dark table-title-notlg align-self-center">К визиту:</div>
									<div class="table-text text-body text-break align-self-center text-start text-lg-center">'.$tovisit.'</div>
								</div>
															
							</div>

						</div>
			
					</a>
				
				';	

			}
						
			$output = $output.'</div>';

		} else {
						
			$output = '		
				<div class="d-flex justify-content-center align-items-center">
					<div class="d-block px-4 py-2">
						Нет исследований
					</div>
				</div>';
		}
								   
		return response()->json(['success'=>$output]);
		
	} 
	
	
	
	public function getOneResearch(Request $request){ 
			
		$output = '';
		
		$outputprint = '';
		
		
		$position = 0;
		
		$worker = Staff::where('staff_id', $request->get('staff_id'))->first();
		
		if ($worker) {
			$position = $worker->position;
		}
		
		if ($request->get('staff_id') == 0 | $position == 1 | $position == 2) {
			
			$output = '<div class="d-flex align-items-center">								

					<div class="col-6 d-flex justify-content-start align-items-center">
						<button type="button" onclick="close_research()" class="btn btn-sm border-1 border-secondary text-secondary">&#8592 К списку</button>
					</div>

					<div class=" col-6 d-flex justify-content-end align-items-center" >								
						<a class="nav-link p-1" onclick="print_research()" role="button">	
							<img class="img-responsive" width="40" height="40" src="images/print.png">
						</a>
						
						<div class="btn-group dropstart dropdown">							  
							<a class="nav-link p-1" data-bs-toggle="dropdown" aria-expanded="false" role="button">	
								<img class="img-responsive" width="40" height="40" src="images/settings.png">
							</a>
							<ul class="dropdown-menu dropdown-3">
								<li><a class="dropdown-item" role="button" onclick="change_research('.$request->get('research_id').')">Изменить исследование</a></li>
								<li><a class="dropdown-item" role="button" onclick="delete_research_dialog('.$request->get('research_id').')">Удалить исследование</a></li>
							</ul>
						</div>
					</div>	
					
				</div>';
			
		} else {
			
			$output = '<div class="d-flex align-items-center">								

					<div class="col-6 d-flex justify-content-start align-items-center">
						<button type="button" onclick="close_research()" class="btn btn-sm border-1 border-secondary text-secondary">&#8592 К списку</button>
					</div>

					<div class=" col-6 d-flex justify-content-end align-items-center" >								
						<a class="nav-link p-1" onclick="print_research()" role="button">	
							<img class="img-responsive" width="40" height="40" src="images/print.png">
						</a>
					</div>	
					
				</div>';
			
		}
		
			
		$output = $output.'

			<div name="visit" id="visit" class="border border rounded p-2 mt-2">
				
				<div class="flex-none my-3 mx-2">
						
					<div class="row px-2">
					
						<div class="col-lg px-0 pb-2 pb-lg-0 align-items-center">

							<div class="d-flex justify-content-lg-start align-items-center">								
								<img src="images/dogicon.png" height="50" alt="Доктор Вет">	
								<div class="h3 align-middle d-inline-flex px-2">Доктор Вет</div>
							</div>										

						</div>
						
						<div class="col-lg px-0">
						
							<div class="flex-none justify-content-lg-start align-items-lg-center">								
								<div class="align-middle justify-content-start justify-content-lg-end d-flex px-2">г. Пермь, ул. Лебедева 25 Б</div>
								<div class="align-middle justify-content-start justify-content-lg-end d-flex px-2">Email: Doktorvet.perm@mail.ru</div>
								<div class="align-middle justify-content-start justify-content-lg-end d-flex px-2">Тел.: +7(342)247-01-09</div>
							</div>
						
						</div>
					</div>	
				</div>
										
				<div class="h3 align-middle justify-content-center d-flex px-2 my-3">Исследование</div>';	
				
		
		$outputprint = $outputprint.'

			<div class="flex-none my-3 mx-2">
									
				<div class="row px-2">
				
					<div class="col px-0 pb-2 pb-0 align-items-center">

						<div class="d-flex justify-content-start align-items-center">								
							<img src="images/dogicon.png" height="50" alt="Доктор Вет">	
							<div class="h3 align-middle d-inline-flex px-2">Доктор Вет</div>
						</div>										

					</div>
					
					<div class="col px-0">
					
						<div class="flex-none justify-content-start align-items-center">								
							<div class="align-middle justify-content-start justify-content-end d-flex px-2">г. Пермь, ул. Лебедева 25 Б</div>
							<div class="align-middle justify-content-start justify-content-end d-flex px-2">Email: Doktorvet.perm@mail.ru</div>
							<div class="align-middle justify-content-start justify-content-end d-flex px-2">Тел.: +7(342)247-01-09</div>
						</div>
					
					</div>
				</div>	
			</div>
									
			<div class="h3 align-middle justify-content-center d-flex px-2 my-3">Исследование</div>';	
		

		$research = Researches::where('research_id', $request->get('research_id'))->first();

		if($research){
						
			$date = '';
			
			if ($research->date_of_research) {
				$date = Carbon::parse($research->date_of_research)->format('d.m.Y');
			}
						
			$doctor = '';
			
			if ($research->doctor) {
				$doctor = $research->doctor;
			}
			
			
			$tovisit = '';
				
			if ($research->visit_id) {
			
				$visit = Visits::where('visit_id', $research->visit_id)->first();
				
				if ($visit) {
					
					$tovisit = '<div class="align-middle justify-content-start d-flex px-2"><span class="fw-bold">К визиту:&nbsp;</span> <span>'.Carbon::parse($visit->date_of_visit)->format('d.m.Y').';</span><span class="">&nbspID: '.$visit->visit_id.'</span> </div>';

				}
			
			}
				
								
			$output = $output.'
						
				<div class="flex-none mb-3">
					<div class="align-middle justify-content-start d-flex px-2"><span class="fw-bold">ID:&nbsp;</span>'.$research->research_id.'</div>
					<div class="align-middle justify-content-start d-flex px-2"><span class="fw-bold">Дата:&nbsp;</span>'.$date.'</div>
					<div class="align-middle justify-content-start d-flex px-2"><span class="fw-bold">Врач:&nbsp;</span>'.$doctor.'</div>
					<div class="align-middle justify-content-start d-flex px-2"><span class="fw-bold">Наименование:&nbsp;</span>'.$research->research_name.'</div>
					'.$tovisit.'
				</div>';
					
			$outputprint = $outputprint.'
						
				<div class="flex-none mb-3">
					<div class="align-middle justify-content-start d-flex px-2"><span class="fw-bold">Дата:&nbsp;</span>'.$date.'</div>
					<div class="align-middle justify-content-start d-flex px-2"><span class="fw-bold">Врач:&nbsp;</span>'.$doctor.'</div>
					<div class="align-middle justify-content-start d-flex px-2"><span class="fw-bold">Наименование:&nbsp;</span>'.$research->research_name.'</div>
					'.$tovisit.'
				</div>';	
					
			$output = $output.'<div class="flex-none mb-3 mx-2">
					
					<div class="row px-2">
					
						<div class="col-lg col-md-border px-0 pb-2 pb-lg-0">
				
							<div class="d-flex justify-content-lg-start align-items-lg-center me-lg-3">								
								<h5 class="text-body align-self-center px-1">Пациент</h5>	
							</div>										
							
							<div class="justify-content-lg-start align-items-lg-center me-lg-3 px-1 d-flex"><span class="fw-bold">Кличка:&nbsp;</span><span class="text-break">'.$request->get('short_name').'</span></div>
							<div class="justify-content-lg-start align-items-lg-center me-lg-3 px-1 d-flex"><span class="fw-bold">Вид:&nbsp;</span><span class="text-break">'.$request->get('anymal_type').'</span></div>  
							<div class="justify-content-lg-start align-items-lg-center me-lg-3 px-1 d-flex"><span class="fw-bold">Порода:&nbsp;</span><span class="text-break">'.$request->get('anymal_breed').'</span></div> 
							<div class="justify-content-lg-start align-items-lg-center me-lg-3 px-1 d-flex"><span class="fw-bold">Пол:&nbsp;</span><span class="text-break">'.$request->get('anymal_sex').'</span></div> 
							<div class="justify-content-lg-start align-items-lg-center me-lg-3 px-1 d-flex"><span class="fw-bold">Окрас:&nbsp;</span><span class="text-break">'.$request->get('anymal_color').'</span></div>
							<div class="justify-content-lg-start align-items-lg-center me-lg-3 px-1 d-flex"><span class="fw-bold">Дата рождения:&nbsp;</span><span class="text-break">'.$request->get('anymal_birthday').'</span></div> 
						
						</div>
						
						<div class="col-lg px-0 pt-2 pt-lg-0 ps-lg-3 px-0">
						
							<div class="d-flex justify-content-lg-start align-items-lg-center me-lg-3">								
								<h5 class="text-body align-self-center px-1">Владелец</h5>
							</div>
							<div class="d-flex justify-content-lg-start align-items-lg-center me-lg-3">	
								<div class="justify-content-lg-start align-items-lg-center me-lg-3 px-1 d-flex"><span class="fw-bold">Ф.И.О.:&nbsp;</span><span class="text-break">'.$request->get('client_name').'</span></div> 				
							</div>
							<div class="d-flex justify-content-lg-start align-items-lg-center me-lg-3">	
								<div class="justify-content-lg-start align-items-lg-center me-lg-3 px-1 d-flex"><span class="fw-bold">Адрес:&nbsp;</span><span class="text-break">'.$request->get('client_address').'</span></div> 			
							</div>
							
						</div>
					</div>	
				</div>';
								
				$outputprint = $outputprint.'<div class="flex-none mb-3 mx-2">
								
					<div class="row px-2">
					
						<div class="col border-end px-0 me-3">
				
							<div class="d-flex justify-content-start align-items-center me-3">								
								<h5 class="text-body align-self-center px-1">Пациент</h5>	
							</div>										
							
							<div class="justify-content-start align-items-center px-1 d-flex"><span class="fw-bold">Кличка:&nbsp;</span><span class="text-break">'.$request->get('short_name').'</span></div>
							<div class="justify-content-start align-items-center px-1 d-flex"><span class="fw-bold">Вид:&nbsp;</span><span class="text-break">'.$request->get('anymal_type').'</span></div>
							<div class="justify-content-start align-items-center px-1 d-flex"><span class="fw-bold">Порода:&nbsp;</span><span class="text-break">'.$request->get('anymal_breed').'</span></div>
							<div class="justify-content-start align-items-center px-1 d-flex"><span class="fw-bold">Пол:&nbsp;</span><span class="text-break">'.$request->get('anymal_sex').'</span></div>
							<div class="justify-content-start align-items-center px-1 d-flex"><span class="fw-bold">Окрас:&nbsp;</span><span class="text-break">'.$request->get('anymal_color').'</span></div>
							<div class="justify-content-start align-items-center px-1 d-flex"><span class="fw-bold">Дата рождения:&nbsp;</span><span class="text-break">'.$request->get('anymal_birthday').'</span></div>
						
						</div>
						
						<div class="col px-0 ps-3">
						
							<div class="d-flex justify-content-start align-items-center me-3">								
								<h5 class="text-body align-self-center px-1">Владелец</h5>
							</div>
								<div class="justify-content-start align-items-center px-1 d-flex"><span class="fw-bold">Ф.И.О.:&nbsp;</span><span class="text-break">'.$request->get('client_name').'</span></div>				
								<div class="justify-content-start align-items-center px-1 d-flex"><span class="fw-bold">Адрес:&nbsp;</span><span class="text-break">'.$request->get('client_address').'</span></div>				
							
						</div>
					</div>	
				</div>';

				if ($research->research_text) {
					
					$output = $output.' <div class="flex-none mb-3">
						<div class="align-middle justify-content-start d-flex px-2"><span class="fw-bold">Результат исследования:&nbsp;</span></div>
						<div class="border border rounded mx-2 p-2 align-middle justify-content-start text-wrap text-break">'.$research->research_text.'</div>
					</div>';
					
					$outputprint = $outputprint.'<div class="flex-none mb-3">
						<div class="align-middle justify-content-start d-flex px-2"><span class="fw-bold">Результат исследования:&nbsp;</span></div>
						<div class="border border rounded mx-2 p-2 align-middle justify-content-start text-wrap text-break">'.$research->research_text.'</div>
					</div>';
					
				}				
				
			$output = $output.'</div>';	
			
		} else {
					
		$output = '	

			<div class="d-flex align-items-center">								
				<div class="col-6 d-flex justify-content-start align-items-center">
					<button type="button" onclick="close_research()" class="btn btn-sm border-1 border-secondary text-secondary">&#8592 К списку</button>
				</div>
			</div>
			
			<div class="d-flex justify-content-center align-items-center">
				<div class="d-block px-4 py-2">
					Данные не найдены
				</div>
			</div>';
			
		}
						   
		return response()->json(['success'=>$output, 'successprint'=>$outputprint]);
		
	} 
	
	
	
	// Удаление исследования
	public function deleteResearch(Request $request){
				
		if ($request->get('id') !== null & $request->get('id') !== '0') {		

			$researchtodel = Researches::where('research_id', $request->get('id'))->first();
			
			if ($researchtodel) {
				
				Researches::destroy($request->get('id'));
				
				return response()->json(['success'=>'Удачно удалено']);
				
				
			} else {
				
				return response()->json(['error'=>'Ошибка удаления, перезагрузите страницу и попробуйте еще раз']);
				
			}

		} else {
			return response()->json(['error'=>'Ошибка удаления, перезагрузите страницу и попробуйте еще раз']);
		}	
		
	}
	
	
	
	public function getResearchData (Request $request){
		
		$research = Researches::where('research_id', $request->get('research_id'))->first();

		if($research){
			
			// Поиск id доктора
			$doctorid = null;
			
			$doctors = Staff::where('position', 2)->get();
		 
			if($doctors->count() > 0){
				
				foreach($doctors as $row){
					
					$last_name = '';
					if ($row->last_name) {
						$last_name = $row->last_name.' ';
					}
					
					$first_name = '';
					if ($row->first_name && $row->middle_name) {
						$first_name = $row->first_name.' ';
					} else {
						$first_name = $row->first_name;
					}
					
					$middle_name = '';
					if ($row->middle_name) {
						$middle_name = $row->middle_name;
					}
					
					
					$fio = $last_name.$first_name.$middle_name;
					
					if ($research->doctor == $fio) {
						$doctorid = $row->staff_id;	
					}
				}				
			}
			
				
			return response()->json(['success'=>$research, 'doctorid'=>$doctorid]);
			
		} else {
			
			return response()->json(['error'=>'Ошибка загрузки данных']);
			
		}
		
	}
	
	
	// Новый анализ
	public function newAnalysis(Request $request){
		
		$messages = [
			'anymal_id.required' => 'Ошибка сохранения',
			'analysis_date.required' => 'Не заполнено поле',
			'analysis_date.date_format' => 'Неверный формат даты',
			'analysis_name.regex' => 'Поле содержит недопустимые символы',
			'analysis_name.required' => 'Не заполнено поле',
			'analysis_titres_data.required' => 'Не выбран шаблон',
			'analysis_doctor.required' => 'Не выбрано значение'
		];
		
		$validator = Validator::make($request->all(), [	
			'anymal_id' => 'required',		
			'analysis_date' => ['required', 'date_format:d.m.Y',],
			'analysis_name' => ['required', 'max:1000', 'regex:/^[^`~#$^&*=\\|\\[\\]{}<>]*$/',],
			'analysis_titres_data' => ['required',],
			'analysis_doctor' => 'required',
        ], $messages);
		
		
		if ($validator->passes()) {
						
			// Конвертируем дату
			$date_of_analysis = Carbon::parse($request->get('analysis_date'))->format('Y-m-d');
	
			$analysisid = Analysis::updateOrCreate(['analysis_id' => $request->analysis_id], ['date_of_analysis'  => $date_of_analysis, 'doctor'  => $request->doctor_text, 'patient_id' => $request->anymal_id,
			'visit_id' => $request->to_visit_new_analysis, 'analysis_name' => $request->analysis_name, 'analysis_text' => $request->analysis_titres_data,]);
			
			return response()->json(['success'=>'Сохранено', 'analysisid'=>$analysisid->analysis_id]);
			
		}
		
		return response()->json(['error'=>$validator->messages()->get('*')]);		
					
	}
	
	
	
	// Список анализов
	public function getAnalysisList(Request $request){
		
		
		$output = '';
		
		
		$analisys_name_filter = $request->analisys_name_filter;
		
		$analisys_to_visit_filter = $request->analisys_to_visit_filter;
		
		$doctor_filter = $request->doctor;
		
		$analisys_visit_date_start = '';
		
						
		if (!empty($request->analisys_visit_date_start)) {
			$analisys_visit_date_start = Carbon::parse($request->get('analisys_visit_date_start'))->format('Y-m-d');
		}			
		
		if (!empty($request->analisys_visit_date_end)) {
			$analisys_visit_date_end = Carbon::parse($request->get('analisys_visit_date_end'))->format('Y-m-d');
		} else {
			$analisys_visit_date_end = Carbon::now()->format('Y-m-d');;
		}
		
		if (!empty($request->analisys_visit_date_start) && !empty($request->analisys_visit_date_end) && Carbon::createFromFormat('Y-m-d', $analisys_visit_date_start)->gt(Carbon::createFromFormat('Y-m-d', $analisys_visit_date_end))) {
			return response()->json(['error'=>'Некорректный выбор дат']);
		}


		$analysis = Analysis::where('patient_id', $request->get('anymal_id'))
		
			->when(!empty($request->analisys_visit_date_start), function ($query) use ($analisys_visit_date_start, $analisys_visit_date_end) {
			$query->whereBetween('date_of_analysis', [$analisys_visit_date_start, $analisys_visit_date_end]);})	
		
			->when(empty($request->analisys_visit_date_start) & !empty($request->analisys_visit_date_end), function ($query) use ($analisys_visit_date_end) {
			$query->whereDate('date_of_analysis', '<=', $analisys_visit_date_end);})
			
			->when(!empty($doctor_filter), function ($query) use ($doctor_filter) {
			$query->where('doctor', $doctor_filter);})
									
			->when(!empty($analisys_to_visit_filter), function ($query) use ($analisys_to_visit_filter) {
			$query->where('visit_id', $analisys_to_visit_filter);})
			
			->when(!empty($analisys_name_filter), function ($query) use ($analisys_name_filter) {
			$query->where('analysis_name', 'like', '%'.$analisys_name_filter.'%');})
	
		->latest('date_of_analysis')
		->orderBy('analysis_id', 'desc')
		->get(); 
		
		
		if($analysis->count() > 0){
			
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
						
						.hoverDiv {
							background: #fff;
						}
						
						.hoverDiv:hover {
							background: #f5f5f5;
						}

					</style>
					
					<div class="d-none d-lg-block border-bottom p-0 m-0">
						<div class="row justify-content-center mx-2 py-2">
							<div class="table-title text-dark col-1 px-2 justify-content-center d-flex align-items-center text-break">ID</div>
							<div class="table-title text-dark col px-2 justify-content-center d-flex align-items-center text-break">Дата</div>
							<div class="table-title text-dark col px-2 justify-content-center d-flex align-items-center text-break">Врач</div>
							<div class="table-title text-dark col px-2 justify-content-center d-flex align-items-center text-break">Наименование</div>
							<div class="table-title text-dark col px-2 justify-content-center d-flex align-items-center text-break">К визиту</div>				
						</div>
					</div>
				';
			
			foreach($analysis as $row){
				
				
				$tovisit = '';
				
				if ($row->visit_id) {
				
					$visit = Visits::where('visit_id', $row->visit_id)->first();
					
					if ($visit) {
						
						$tovisit = '<span>'.Carbon::parse($visit->date_of_visit)->format('d.m.Y').';</span><span class="">&nbspID: '.$visit->visit_id.'</span>';
						
					}
				
				}
					
				$output = $output.'
															
					<a href="" onclick="open_analysis('.$row->analysis_id.'); return false;" class="text-decoration-none block leading-5 text-gray-700 transition duration-150 ease-in-out hover:bg-gray-100 focus:outline-none focus:bg-gray-100 p-0">

						<div class="hoverDiv border-bottom mx-0">

							<div class="row justify-content-center mx-2 py-2">
									
								<div class="col-lg-1 px-0 px-lg-2 py-1 d-flex justify-content-lg-center align-items-lg-center" align="center">
									<div class="d-flex d-lg-none text-dark table-title-notlg align-self-center">ID:</div>
									<div class="table-text text-body text-break align-self-center text-start text-lg-center">'.$row->analysis_id.'</div>
								</div>
								
								<div class="col-lg px-0 px-lg-2 py-1 d-flex justify-content-lg-center align-items-lg-center" align="center">
									<div class="d-flex d-lg-none text-dark table-title-notlg align-self-center">Дата:</div>
									<div class="table-text text-body text-break align-self-center text-start text-lg-center">'.Carbon::parse($row->date_of_analysis)->format('d.m.Y').'</div>
								</div>
								
								<div class="col-lg px-0 px-lg-2 py-1 d-flex justify-content-lg-center align-items-lg-center" align="center">
									<div class="d-flex d-lg-none text-dark table-title-notlg align-self-center">Врач:</div>
									<div class="table-text text-body text-break align-self-center text-start text-lg-center">'.$row->doctor.'</div>
																		
								</div>
											
								<div class="col-lg px-0 px-lg-2 py-1 d-flex justify-content-lg-center align-items-lg-center" align="center">
									<div class="d-flex d-lg-none text-dark table-title-notlg align-self-center">Наименование:</div>
									<div class="table-text text-body text-break align-self-center text-start text-lg-center">'.$row->analysis_name.'</div>
								</div>
								
								<div class="col-lg px-0 px-lg-2 py-1 d-flex justify-content-lg-center align-items-lg-center" align="center">
									<div class="d-flex d-lg-none text-dark table-title-notlg align-self-center">К визиту:</div>
									<div class="table-text text-body text-break align-self-center text-start text-lg-center">'.$tovisit.'</div>
								</div>
															
							</div>

						</div>
			
					</a>
				
				';	

			}
						
			$output = $output.'</div>';

		} else {
						
			$output = '		
				<div class="d-flex justify-content-center align-items-center">
					<div class="d-block px-4 py-2">
						Нет анализов
					</div>
				</div>';
		}
								   
		return response()->json(['success'=>$output]);
		
	} 
	
	
	
	
	public function getOneAnalysis(Request $request){
			
		$output = '';
		
		$outputprint = '';
		
		$position = 0;
		
		$worker = Staff::where('staff_id', $request->get('staff_id'))->first();
		
		if ($worker) {
			$position = $worker->position;
		}
		
		if ($request->get('staff_id') == 0 | $position == 1 | $position == 2) {
			
			$output = '<div class="d-flex align-items-center">								

					<div class="col-6 d-flex justify-content-start align-items-center">
						<button type="button" onclick="close_analysis()" class="btn btn-sm border-1 border-secondary text-secondary">&#8592 К списку</button>
					</div>

					<div class=" col-6 d-flex justify-content-end align-items-center" >								
						<a class="nav-link p-1" onclick="print_analysis()" role="button">	
							<img class="img-responsive" width="40" height="40" src="images/print.png">
						</a>
						
						<div class="btn-group dropstart dropdown">							  
							<a class="nav-link p-1" data-bs-toggle="dropdown" aria-expanded="false" role="button">	
								<img class="img-responsive" width="40" height="40" src="images/settings.png">
							</a>
							<ul class="dropdown-menu dropdown-3">
								<li><a class="dropdown-item" role="button" onclick="change_analysis('.$request->get('analysis_id').')">Изменить анализ</a></li>
								<li><a class="dropdown-item" role="button" onclick="delete_analysis_dialog('.$request->get('analysis_id').')">Удалить анализ</a></li>
							</ul>
						</div>
					</div>	
					
				</div>';
			
		} else {
			
			$output = '<div class="d-flex align-items-center">								

					<div class="col-6 d-flex justify-content-start align-items-center">
						<button type="button" onclick="close_analysis()" class="btn btn-sm border-1 border-secondary text-secondary">&#8592 К списку</button>
					</div>

					<div class=" col-6 d-flex justify-content-end align-items-center" >								
						<a class="nav-link p-1" onclick="print_analysis()" role="button">	
							<img class="img-responsive" width="40" height="40" src="images/print.png">
						</a>
					</div>	
					
				</div>';
			
		}
		
			
		$output = $output.'

			<div name="visit" id="visit" class="border border rounded p-2 mt-2 pb-4">
				
				<div class="flex-none my-3 mx-2">
						
					<div class="row px-2">
					
						<div class="col-lg px-0 pb-2 pb-lg-0 align-items-center">

							<div class="d-flex justify-content-lg-start align-items-center">								
								<img src="images/dogicon.png" height="50" alt="Доктор Вет">	
								<div class="h3 align-middle d-inline-flex px-2">Доктор Вет</div>
							</div>										

						</div>
						
						<div class="col-lg px-0">
						
							<div class="flex-none justify-content-lg-start align-items-lg-center">								
								<div class="align-middle justify-content-start justify-content-lg-end d-flex px-2">г. Пермь, ул. Лебедева 25 Б</div>
								<div class="align-middle justify-content-start justify-content-lg-end d-flex px-2">Email: Doktorvet.perm@mail.ru</div>
								<div class="align-middle justify-content-start justify-content-lg-end d-flex px-2">Тел.: +7(342)247-01-09</div>
							</div>
						
						</div>
					</div>	
				</div>
										
				<div class="h3 align-middle justify-content-center d-flex px-2 my-3">Анализ</div>';	
				
		
		$outputprint = $outputprint.'

			<div class="flex-none my-3 mx-2">
									
				<div class="row px-2">
				
					<div class="col px-0 pb-2 pb-0 align-items-center">

						<div class="d-flex justify-content-start align-items-center">								
							<img src="images/dogicon.png" height="50" alt="Доктор Вет">	
							<div class="h3 align-middle d-inline-flex px-2">Доктор Вет</div>
						</div>										

					</div>
					
					<div class="col px-0">
					
						<div class="flex-none justify-content-start align-items-center">								
							<div class="align-middle justify-content-start justify-content-end d-flex px-2">г. Пермь, ул. Лебедева 25 Б</div>
							<div class="align-middle justify-content-start justify-content-end d-flex px-2">Email: Doktorvet.perm@mail.ru</div>
							<div class="align-middle justify-content-start justify-content-end d-flex px-2">Тел.: +7(342)247-01-09</div>
						</div>
					
					</div>
				</div>	
			</div>
									
			<div class="h3 align-middle justify-content-center d-flex px-2 my-3">Анализ</div>';	
		

		$analysis = Analysis::where('analysis_id', $request->get('analysis_id'))->first();

		if($analysis){
						
			$date = '';
			
			if ($analysis->date_of_analysis) {
				$date = Carbon::parse($analysis->date_of_analysis)->format('d.m.Y');
			}
						
			$doctor = '';
			
			if ($analysis->doctor) {
				$doctor = $analysis->doctor;
			}
			
			
			$tovisit = '';
				
			if ($analysis->visit_id) {
			
				$visit = Visits::where('visit_id', $analysis->visit_id)->first();
				
				if ($visit) {
					
					$tovisit = '<div class="align-middle justify-content-start d-flex px-2"><span class="fw-bold">К визиту:&nbsp;</span> <span>'.Carbon::parse($visit->date_of_visit)->format('d.m.Y').';</span><span class="">&nbspID: '.$visit->visit_id.'</span> </div>';

				}
			
			}
				
								
			$output = $output.'
						
				<div class="flex-none mb-3">
					<div class="align-middle justify-content-start d-flex px-2"><span class="fw-bold">ID:&nbsp;</span>'.$analysis->analysis_id.'</div>
					<div class="align-middle justify-content-start d-flex px-2"><span class="fw-bold">Дата:&nbsp;</span>'.$date.'</div>
					<div class="align-middle justify-content-start d-flex px-2"><span class="fw-bold">Врач:&nbsp;</span>'.$doctor.'</div>
					<div class="align-middle justify-content-start d-flex px-2"><span class="fw-bold">Наименование:&nbsp;</span>'.$analysis->analysis_name.'</div>
					'.$tovisit.'
				</div>';
					
			$outputprint = $outputprint.'
						
				<div class="flex-none mb-3">
					<div class="align-middle justify-content-start d-flex px-2"><span class="fw-bold">Дата:&nbsp;</span>'.$date.'</div>
					<div class="align-middle justify-content-start d-flex px-2"><span class="fw-bold">Врач:&nbsp;</span>'.$doctor.'</div>
					<div class="align-middle justify-content-start d-flex px-2"><span class="fw-bold">Наименование:&nbsp;</span>'.$analysis->analysis_name.'</div>
					'.$tovisit.'
				</div>';	
					
			$output = $output.'<div class="flex-none mb-3 mx-2">
					
					<div class="row px-2">
					
						<div class="col-lg col-md-border px-0 pb-2 pb-lg-0">
				
							<div class="d-flex justify-content-lg-start align-items-lg-center me-lg-3">								
								<h5 class="text-body align-self-center px-1">Пациент</h5>	
							</div>										
							
							<div class="justify-content-lg-start align-items-lg-center me-lg-3 px-1 d-flex"><span class="fw-bold">Кличка:&nbsp;</span><span class="text-break">'.$request->get('short_name').'</span></div>
							<div class="justify-content-lg-start align-items-lg-center me-lg-3 px-1 d-flex"><span class="fw-bold">Вид:&nbsp;</span><span class="text-break">'.$request->get('anymal_type').'</span></div>  
							<div class="justify-content-lg-start align-items-lg-center me-lg-3 px-1 d-flex"><span class="fw-bold">Порода:&nbsp;</span><span class="text-break">'.$request->get('anymal_breed').'</span></div> 
							<div class="justify-content-lg-start align-items-lg-center me-lg-3 px-1 d-flex"><span class="fw-bold">Пол:&nbsp;</span><span class="text-break">'.$request->get('anymal_sex').'</span></div> 
							<div class="justify-content-lg-start align-items-lg-center me-lg-3 px-1 d-flex"><span class="fw-bold">Окрас:&nbsp;</span><span class="text-break">'.$request->get('anymal_color').'</span></div>
							<div class="justify-content-lg-start align-items-lg-center me-lg-3 px-1 d-flex"><span class="fw-bold">Дата рождения:&nbsp;</span><span class="text-break">'.$request->get('anymal_birthday').'</span></div> 
						
						</div>
						
						<div class="col-lg px-0 pt-2 pt-lg-0 ps-lg-3 px-0">
						
							<div class="d-flex justify-content-lg-start align-items-lg-center me-lg-3">								
								<h5 class="text-body align-self-center px-1">Владелец</h5>
							</div>
							<div class="d-flex justify-content-lg-start align-items-lg-center me-lg-3">	
								<div class="justify-content-lg-start align-items-lg-center me-lg-3 px-1 d-flex"><span class="fw-bold">Ф.И.О.:&nbsp;</span><span class="text-break">'.$request->get('client_name').'</span></div> 				
							</div>
							<div class="d-flex justify-content-lg-start align-items-lg-center me-lg-3">	
								<div class="justify-content-lg-start align-items-lg-center me-lg-3 px-1 d-flex"><span class="fw-bold">Адрес:&nbsp;</span><span class="text-break">'.$request->get('client_address').'</span></div> 			
							</div>
							
						</div>
					</div>	
				</div>';
								
				$outputprint = $outputprint.'<div class="flex-none mb-3 mx-2">
								
					<div class="row px-2">
					
						<div class="col border-end px-0 me-3">
				
							<div class="d-flex justify-content-start align-items-center me-3">								
								<h5 class="text-body align-self-center px-1">Пациент</h5>	
							</div>										
							
							<div class="justify-content-start align-items-center px-1 d-flex"><span class="fw-bold">Кличка:&nbsp;</span><span class="text-break">'.$request->get('short_name').'</span></div>
							<div class="justify-content-start align-items-center px-1 d-flex"><span class="fw-bold">Вид:&nbsp;</span><span class="text-break">'.$request->get('anymal_type').'</span></div>
							<div class="justify-content-start align-items-center px-1 d-flex"><span class="fw-bold">Порода:&nbsp;</span><span class="text-break">'.$request->get('anymal_breed').'</span></div>
							<div class="justify-content-start align-items-center px-1 d-flex"><span class="fw-bold">Пол:&nbsp;</span><span class="text-break">'.$request->get('anymal_sex').'</span></div>
							<div class="justify-content-start align-items-center px-1 d-flex"><span class="fw-bold">Окрас:&nbsp;</span><span class="text-break">'.$request->get('anymal_color').'</span></div>
							<div class="justify-content-start align-items-center px-1 d-flex"><span class="fw-bold">Дата рождения:&nbsp;</span><span class="text-break">'.$request->get('anymal_birthday').'</span></div>
						
						</div>
						
						<div class="col px-0 ps-3">
						
							<div class="d-flex justify-content-start align-items-center me-3">								
								<h5 class="text-body align-self-center px-1">Владелец</h5>
							</div>
								<div class="justify-content-start align-items-center px-1 d-flex"><span class="fw-bold">Ф.И.О.:&nbsp;</span><span class="text-break">'.$request->get('client_name').'</span></div>				
								<div class="justify-content-start align-items-center px-1 d-flex"><span class="fw-bold">Адрес:&nbsp;</span><span class="text-break">'.$request->get('client_address').'</span></div>				
							
						</div>
					</div>	
				</div>';

				if ($analysis->analysis_text && json_decode($analysis->analysis_text, true)) {
										
					$output = $output.'
					
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

					</style>
					
					<div class="d-none d-lg-block border-bottom p-0 mx-2 m-0">
						<div class="row justify-content-center mx-2 py-2">
							<div class="table-title text-dark col-4 px-2 justify-content-center d-flex align-items-center text-break">Показатель</div>
							<div class="table-title text-dark col-4 px-2 justify-content-center d-flex align-items-center text-break">Значение</div>
							<div class="table-title text-dark col-4 px-2 justify-content-center d-flex align-items-center text-break">Норма</div>
						</div>
					</div>';
					
					$outputprint = $outputprint.'
					
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

					</style>
					
					<div class="d-block border-bottom p-0 mx-2 m-0">
						<div class="row justify-content-center mx-2 py-2">
							<div class="table-title text-dark col-4 px-2 justify-content-center d-flex align-items-center text-break">Показатель</div>
							<div class="table-title text-dark col-4 px-2 justify-content-center d-flex align-items-center text-break">Значение</div>
							<div class="table-title text-dark col-4 px-2 justify-content-center d-flex align-items-center text-break">Норма</div>
						</div>
					</div>';
					
					foreach((array) json_decode($analysis->analysis_text) as $oneline){
												
						if ($oneline->type_todb == 0) {
							
							$notinlimit = '';
							
							if (floatval(str_replace(',', '.', $oneline->result_todb)) < floatval(str_replace(',', '.', $oneline->from_todb)) | floatval(str_replace(',', '.', $oneline->result_todb)) >= floatval(str_replace(',', '.', $oneline->to_todb))) {
								
								$notinlimit = '<span class="text-danger fw-bold">   !</span>';
								
							}
						
							$output = $output.'<div class="border-bottom mx-2">

								<div class="row justify-content-center mx-0 mx-lg-2 py-2">
										
									<div class="col-lg-4 px-0 px-lg-2 py-1 d-flex justify-content-lg-center align-items-lg-center" align="center">
										<div class="d-flex d-lg-none text-dark table-title-notlg align-self-center">Показатель:</div>
										<div class="table-text text-body text-break align-self-center text-start text-lg-center">'.$oneline->name_todb.'</div>
									</div>
									
									<div class="col-lg-2 px-0 px-lg-2 py-1 d-flex justify-content-lg-end align-items-lg-center" align="center">
										<div class="d-flex d-lg-none text-dark table-title-notlg align-self-center">Значение:</div>
										<div class="table-text text-body text-break align-self-center text-start text-lg-center">'.$oneline->result_todb.$notinlimit.'</div>
									</div>
									
									<div class="col-lg-2 px-0 px-lg-2 py-1 d-flex justify-content-lg-start align-items-lg-center" align="center">
										<div class="d-flex d-lg-none text-dark table-title-notlg align-self-center">Единица измерения:</div>
										<div class="table-text text-body text-break align-self-center text-start text-lg-center">'.$oneline->edizm_todb.'</div>
									</div>
									
									<div class="col-lg-4 px-0 px-lg-2 py-1 d-flex justify-content-lg-center align-items-lg-center" align="center">
										<div class="d-flex d-lg-none text-dark table-title-notlg align-self-center">Норма:</div>
										<div class="table-text text-body text-break align-self-center text-start text-lg-center fst-italic">'.$oneline->from_todb.' - '.$oneline->to_todb.'</div>
									</div>
							
								</div>

							</div>';
														
							$outputprint = $outputprint.'<div class="border-bottom mx-2">

								<div class="row justify-content-center mx-2 py-2">
										
									<div class="col-4 px-0 px-2 py-1 d-flex justify-content-center align-items-center" align="center">
										<div class="table-text text-body text-break align-self-center text-start text-center">'.$oneline->name_todb.'</div>
									</div>
									
									<div class="col-2 px-0 px-2 py-1 d-flex justify-content-end align-items-center" align="center">
										<div class="table-text text-body text-break align-self-center text-start text-center">'.$oneline->result_todb.$notinlimit.'</div>
									</div>
									
									<div class="col-2 px-0 px-2 py-1 d-flex justify-content-start align-items-center" align="center">
										<div class="table-text text-body text-break align-self-center text-start text-center">'.$oneline->edizm_todb.'</div>
									</div>
																		
									<div class="col-4 px-0 px-2 py-1 d-flex justify-content-center align-items-center" align="center">
										<div class="table-text text-body text-break align-self-center text-start text-center fst-italic">'.$oneline->from_todb.' - '.$oneline->to_todb.'</div>
									</div>
							
								</div>

							</div>';
						
						} else if ($oneline->type_todb == 1) {
							
							$notinlimit_2 = '';
								
							if ($oneline->result_todb <> $oneline->value_todb) {
								
								$notinlimit_2 = '<span class="text-danger fw-bold">   !</span>';
								
							}
						
							$output = $output.'<div class="border-bottom mx-2">

								<div class="row justify-content-center mx-0 mx-lg-2 py-2">
										
									<div class="col-lg-4 px-0 px-lg-2 py-1 d-flex justify-content-lg-center align-items-lg-center" align="center">
										<div class="d-flex d-lg-none text-dark table-title-notlg align-self-center">Показатель:</div>
										<div class="table-text text-body text-break align-self-center text-start text-lg-center">'.$oneline->name_todb.'</div>
									</div>
									
									<div class="col-lg-4 px-0 px-lg-2 py-1 d-flex justify-content-lg-center align-items-lg-center" align="center">
										<div class="d-flex d-lg-none text-dark table-title-notlg align-self-center">Показатель:</div>
										<div class="table-text text-body text-break align-self-center text-start text-lg-center">'.$oneline->result_todb.$notinlimit_2.'</div>
									</div>
																		
									<div class="col-lg-4 px-0 px-lg-2 py-1 d-flex justify-content-lg-center align-items-lg-center" align="center">
										<div class="d-flex d-lg-none text-dark table-title-notlg align-self-center">Норма:</div>
										<div class="table-text text-body text-break align-self-center text-start text-lg-center fst-italic">'.$oneline->value_todb.'</div>
									</div>
								
								</div>

							</div>';			
							
							$outputprint = $outputprint.'<div class="border-bottom mx-2">

								<div class="row justify-content-center mx-2 py-2">
								
									<div class="col-4 px-0 px-2 py-1 d-flex justify-content-center align-items-center" align="center">										
										<div class="table-text text-body text-break align-self-center text-start text-center">'.$oneline->name_todb.'</div>
									</div>
									
									<div class="col-4 px-0 px-2 py-1 d-flex justify-content-center align-items-center" align="center">										
										<div class="table-text text-body text-break align-self-center text-start text-center">'.$oneline->result_todb.$notinlimit_2.'</div>
									</div>
																		
									<div class="col-4 px-0 px-2 py-1 d-flex justify-content-center align-items-center" align="center">										
										<div class="table-text text-body text-break align-self-center text-start text-center fst-italic">'.$oneline->value_todb.'</div>
									</div>

								</div>

							</div>';
							
						}
												
					}
					
				}				
				
			$output = $output.'</div>';	
			
		} else {
					
		$output = '	

			<div class="d-flex align-items-center">								
				<div class="col-6 d-flex justify-content-start align-items-center">
					<button type="button" onclick="close_analysis()" class="btn btn-sm border-1 border-secondary text-secondary">&#8592 К списку</button>
				</div>
			</div>
			
			<div class="d-flex justify-content-center align-items-center">
				<div class="d-block px-4 py-2">
					Данные не найдены
				</div>
			</div>';
			
		}
						   
		return response()->json(['success'=>$output, 'successprint'=>$outputprint]);
		
	} 
	
	
	public function getAnalysisData (Request $request){
		
		$analysis = Analysis::where('analysis_id', $request->get('analysis_id'))->first();

		if($analysis){
			
			// Поиск id доктора
			$doctorid = null;
			
			$doctors = Staff::where('position', 2)->get();
		 
			if($doctors->count() > 0){
				
				foreach($doctors as $row){
					
					$last_name = '';
					if ($row->last_name) {
						$last_name = $row->last_name.' ';
					}
					
					$first_name = '';
					if ($row->first_name && $row->middle_name) {
						$first_name = $row->first_name.' ';
					} else {
						$first_name = $row->first_name;
					}
					
					$middle_name = '';
					if ($row->middle_name) {
						$middle_name = $row->middle_name;
					}
					
					
					$fio = $last_name.$first_name.$middle_name;
					
					if ($analysis->doctor == $fio) {
						$doctorid = $row->staff_id;	
					}
				}				
			}
			
			
			// Получаем id шаблона анализа.
			$analysis_template = AnalysisTemplates::where('analysis_plate_title', $analysis->analysis_name)->first();
			
			$analysis_template_id = 0;
			
			if ($analysis_template) {
				$analysis_template_id = $analysis_template->id;
			}
			
				
			return response()->json(['success'=>$analysis, 'doctorid'=>$doctorid, 'analysis_template_id'=>$analysis_template_id]);
			
		} else {
			
			return response()->json(['error'=>'Ошибка загрузки данных']);
			
		}
		
	}
	
	
	// Удаление исследования
	public function deleteAnalysis(Request $request){
				
		if ($request->get('id') !== null & $request->get('id') !== '0') {		

			$analysistodel = Analysis::where('analysis_id', $request->get('id'))->first();
			
			if ($analysistodel) {
				
				Analysis::destroy($request->get('id'));
				
				return response()->json(['success'=>'Удачно удалено']);
				
				
			} else {
				
				return response()->json(['error'=>'Ошибка удаления, перезагрузите страницу и попробуйте еще раз']);
				
			}

		} else {
			return response()->json(['error'=>'Ошибка удаления, перезагрузите страницу и попробуйте еще раз']);
		}	
		
	}
	
	
	
	// Новый анализ
	public function newVacine(Request $request){
		
		$messages = [
			'anymal_id.required' => 'Ошибка сохранения',
			'vacine_date.required' => 'Не заполнено поле',
			'vacine_date.date_format' => 'Неверный формат даты',
			'vacine_name.required' => 'Не выбрано значение',
			'vacine_doctor.required' => 'Не выбрано значение'
		];
		
		$validator = Validator::make($request->all(), [	
			'anymal_id' => 'required',		
			'vacine_date' => ['required', 'date_format:d.m.Y',],		
			'vacine_name' => ['required',],
			'vacine_doctor' => 'required',
        ], $messages);
		
		
		if ($validator->passes()) {
						
			// Конвертируем дату
			$date_of_vacine = Carbon::parse($request->get('vacine_date'))->format('Y-m-d');
	
			$vacineid = Vacines::updateOrCreate(['vacines_id' => $request->vacine_id], ['date_of_vacine'  => $date_of_vacine, 'doctor'  => $request->doctor_text, 'patient_id' => $request->anymal_id,
			'vacine_name' => $request->vacine_text,]);
			
			return response()->json(['success'=>'Сохранено', 'vacineid'=>$vacineid->vacines_id]);
			
		}
		
		return response()->json(['error'=>$validator->messages()->get('*')]);		
					
	}
	
	
	
	// Список вакцинаций
	public function getVacinesList(Request $request){
		
		$output = '';
		
		$adminmenutable = '';
				
		$position = 0;
		
		$worker = Staff::where('staff_id', $request->get('staff_id'))->first();
		
		if ($worker) {
			$position = $worker->position;
		}
		
		if ($request->get('staff_id') == 0 | $position == 1 | $position == 2) {
			$adminmenutable = '<div class="table-title text-dark col-1 px-2 justify-content-center d-flex align-items-center text-break"></div>';
		}
		
		
		$vacines = Vacines::where('patient_id', $request->get('anymal_id'))->latest('date_of_vacine')->orderBy('vacines_id', 'desc')->get(); 
		
		
		if($vacines->count() > 0){
			
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
						
						.hoverDiv {
							background: #fff;
						}
						
						.hoverDiv:hover {
							background: #f5f5f5;
						}

					</style>
					
					<div class="d-none d-lg-block border-bottom p-0 m-0">
						<div class="row justify-content-center mx-2 py-2">
							<div class="table-title text-dark col-1 px-2 justify-content-center d-flex align-items-center text-break">ID</div>
							<div class="table-title text-dark col px-2 justify-content-center d-flex align-items-center text-break">Дата</div>
							<div class="table-title text-dark col px-2 justify-content-center d-flex align-items-center text-break">Врач</div>
							<div class="table-title text-dark col px-2 justify-content-center d-flex align-items-center text-break">Наименование</div>
							'.$adminmenutable.'				
						</div>
					</div>
				';
			
			$i1 = 0;
			
			foreach($vacines as $row){
				
				$adminmenu = '';
				
				if ($request->get('staff_id') == 0 | $position == 1 | $position == 2) {
					
					$adminmenu = '<div class="col-lg-1 px-0 px-lg-2 py-1 d-flex justify-content-lg-center align-items-lg-center" align="center">
									
						<div class="btn-group dropstart">							  
							<a class="nav-link p-0" data-bs-toggle="dropdown" aria-expanded="false" href="#" role="button">	
								<img class="img-responsive" width="30" height="30" src="images/settings_120.png">
							</a>
							<ul class="dropdown-menu dropdown-m1 dropdown-m1-'.$i1.'" name="dropdown-m1-'.$i1.'"  id="dropdown-m1-'.$i1.'">
								<li><a class="dropdown-item" role="button" onclick="new_vacine('.$row->vacines_id.')">Изменить</a></li>
								<li><a class="dropdown-item" role="button" onclick="delete_vacine_dialog('.$row->vacines_id.')">Удалить</a></li>
							</ul>
						</div>
						
					</div>';
					
				}
									
				$output = $output.'
				
					<div class="border-bottom p-0 m-0">
																
						<div class="row justify-content-center mx-2 py-2">
								
							<div class="col-lg-1 px-0 px-lg-2 py-1 d-flex justify-content-lg-center align-items-lg-center" align="center">
								<div class="d-flex d-lg-none text-dark table-title-notlg align-self-center">ID:</div>
								<div class="table-text text-body text-break align-self-center text-start text-lg-center">'.$row->vacines_id.'</div>
							</div>
							
							<div class="col-lg px-0 px-lg-2 py-1 d-flex justify-content-lg-center align-items-lg-center" align="center">
								<div class="d-flex d-lg-none text-dark table-title-notlg align-self-center">Дата:</div>
								<div class="table-text text-body text-break align-self-center text-start text-lg-center">'.Carbon::parse($row->date_of_vacine)->format('d.m.Y').'</div>
							</div>
							
							<div class="col-lg px-0 px-lg-2 py-1 d-flex justify-content-lg-center align-items-lg-center" align="center">
								<div class="d-flex d-lg-none text-dark table-title-notlg align-self-center">Врач:</div>
								<div class="table-text text-body text-break align-self-center text-start text-lg-center">'.$row->doctor.'</div>
																	
							</div>
										
							<div class="col-lg px-0 px-lg-2 py-1 d-flex justify-content-lg-center align-items-lg-center" align="center">
								<div class="d-flex d-lg-none text-dark table-title-notlg align-self-center">Наименование:</div>
								<div class="table-text text-body text-break align-self-center text-start text-lg-center">'.$row->vacine_name.'</div>
							</div>
							
							'.$adminmenu.'	

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
						Нет вакцинаций
					</div>
				</div>';
		}
								   
		return response()->json(['success'=>$output]);
		
	} 
	
	
	
	public function getVacineData (Request $request){
		
		$vacines = Vacines::where('vacines_id', $request->get('vacines_id'))->first();

		if($vacines){
			
			// Поиск id доктора
			$doctorid = null;
			
			$doctors = Staff::where('position', 2)->get();
		 
			if($doctors->count() > 0){
				
				foreach($doctors as $row){
					
					$last_name = '';
					if ($row->last_name) {
						$last_name = $row->last_name.' ';
					}
					
					$first_name = '';
					if ($row->first_name && $row->middle_name) {
						$first_name = $row->first_name.' ';
					} else {
						$first_name = $row->first_name;
					}
					
					$middle_name = '';
					if ($row->middle_name) {
						$middle_name = $row->middle_name;
					}
					
					
					$fio = $last_name.$first_name.$middle_name;
					
					if ($vacines->doctor == $fio) {
						$doctorid = $row->staff_id;	
					}
				}				
			}
			
			
			// Получаем id шаблона анализа.
			$vacines_type = VacinesTypes::where('vacine_title', $vacines->vacine_name)->first();
			
			$vacines_type_id = 0;
			
			if ($vacines_type) {
				$vacines_type_id = $vacines_type->id;
			}
			
				
			return response()->json(['success'=>$vacines, 'doctorid'=>$doctorid, 'vacines_type_id'=>$vacines_type_id]);
			
		} else {
			
			return response()->json(['error'=>'Ошибка загрузки данных']);
			
		}
		
	}
	
	
	// Удаление вакцины
	public function deleteVacine(Request $request){
				
		if ($request->get('id') !== null & $request->get('id') !== '0') {		

			$vacinetodel = Vacines::where('vacines_id', $request->get('id'))->first();
			
			if ($vacinetodel) {
				
				Vacines::destroy($request->get('id'));
				
				return response()->json(['success'=>'Удачно удалено']);
				
				
			} else {
				
				return response()->json(['error'=>'Ошибка удаления, перезагрузите страницу и попробуйте еще раз']);
				
			}

		} else {
			return response()->json(['error'=>'Ошибка удаления, перезагрузите страницу и попробуйте еще раз']);
		}	
		
	}
	
	
	// Поиск животных для выпадающего списка
	public function patientsForBills(Request $request){
		
		$title = trim($request->q);
										 
		$patients = Patients::where('short_name', 'like', '%'.$title.'%')->get();	
		 
		if($patients->count() > 0){
			
			$formatted_types = [];
			
			foreach($patients as $row){
				
				$anymal_type = '';
				if (!empty($row->animal_type_id)) {
					$anymal_type = Animal_types::where('animal_type_id', $row->animal_type_id)->first()->type_title;
				}
								
				$client = Clients::where('client_id', $row->client_id)->first();

				$fio = '';
				
				if ($client) {
					$last_name = '';
					if (!empty($client->last_name)) {
						$last_name = $client->last_name;
					}
					$first_name = '';
					if (!empty($client->first_name)) {
						$first_name = ' '.$client->first_name;
					}
					$middle_name = '';
					if (!empty($client->middle_name)) {
						$middle_name = ' '.$client->middle_name;
					}
					
					$fio = $last_name.$first_name.$middle_name;
				}
	
				$formatted_types[] = ['id' => $row->patient_id, 'text' => $row->short_name, 'client' => $fio, 'type' => $anymal_type];
				
			}
			
			return \Response::json($formatted_types);
		} else {
			return \Response::json([]);	
		}	
    }
	
	
	
	// Новый счет
	public function newBill(Request $request){
		
		$messages = [
			//'anymal_id.required' => 'Ошибка сохранения',
			'bill_date.required' => 'Не заполнено поле',
			'bill_date.date_format' => 'Неверный формат даты',
			'products_bill_list.required_without' => 'Не выбраны товары или услуги',
			'services_bill_list.required_without' => 'Не выбраны товары или услуги'
		];
		
		$validator = Validator::make($request->all(), [	
			//'anymal_id' => 'required',		
			'bill_date' => ['required', 'date_format:d.m.Y',],
			'products_bill_list' => ['required_without:services_bill_list',],
			'services_bill_list' => ['required_without:products_bill_list',],
        ], $messages);
		
		
		if ($validator->passes()) {
						
			// Конвертируем дату
			$date_of_bill = Carbon::parse($request->get('bill_date'))->format('Y-m-d');
	
			$billid = Bills::updateOrCreate(['bill_id' => $request->bill_id], ['date_of_bill'  => $date_of_bill, 'staff'  => $request->staff, 'staff_id'  => $request->staff_id, 'patient_id' => $request->anymal_id,
			'product_text' => $request->products_bill_list, 'product_discount' => $request->product_discount, 'product_summ' => $request->product_summ, 'service_text' => $request->services_bill_list, 'service_discount' => $request->service_discount, 'service_summ' => $request->service_summ, 
			'bill_summ' => $request->bill_summ, 'paied' => $request->bill_pay,]);
						
			// Удаляем все оплаты, чтобы перезаписать
			Pays::where('bill_id', $billid->bill_id)->delete();
			
			if (!empty($request->pays_list)) {
						
				$pays_list = json_decode($request->pays_list, true);

				if (!empty($pays_list) & count((array)$pays_list) > 0){

					foreach($pays_list as $onepay){
						
						$date_of_pay = Carbon::parse($onepay['pay_date_todb'])->format('Y-m-d');
						
						Pays::create(['bill_id' => $billid->bill_id, 'patient_id' => $request->anymal_id, 'date_of_pay'  => $date_of_pay, 'pay_summ' => $onepay['pay_summ_todb'],]);

					}
				
				}
			
			}
		
			return response()->json(['success'=>'Сохранено', 'billid'=>$billid->bill_id]);
			
		}
		
		return response()->json(['error'=>$validator->messages()->get('*')]);		
					
	}
	
	
	
	// Список счетов
	public function getBillsList(Request $request){
		
		$output = '';
		
		
		if (empty($request->bill_date_start) & empty($request->bill_date_end) & empty($request->bill_paied)& empty($request->product_filter)& empty($request->service_filter)& empty($request->anymal_id) & empty($request->client_id) & empty($request->naimedornot)) {   
			
			$output = '<div class="d-flex justify-content-center align-items-center mt-3">
						<div class="d-block px-4 py-2">
							Не заданы параметры поиска.
						</div>
					</div>';		
		} else {
		
			$bill_date_start = '';
			
			$product_selected = $request->product_selected;
			
			$service_selected = $request->service_selected;
			
			$bill_paied = $request->get('bill_paied');
			
			$page = $request->page;
			
			$summ = 0;
			
			$paied = 0;
			
			$debt = 0;
			
			// Неоплаченные счета
			$notpaied_bills = [];
			
							
			if (!empty($request->bill_date_start)) {
				$bill_date_start = Carbon::parse($request->get('bill_date_start'))->format('Y-m-d');
			}			
			
			if (!empty($request->bill_date_end)) {
				$bill_date_end = Carbon::parse($request->get('bill_date_end'))->format('Y-m-d');
			} else {
				$bill_date_end = Carbon::now()->format('Y-m-d');;
			}
			
			if (!empty($request->bill_date_start) && !empty($request->bill_date_end) && Carbon::createFromFormat('Y-m-d', $bill_date_start)->gt(Carbon::createFromFormat('Y-m-d', $bill_date_end))) {
				return response()->json(['error'=>'Некорректный выбор дат']);
			}


			$anymal_id = $request->get('anymal_id');
			
			$client = $request->get('client_id');
			
			$naimedornot = $request->get('naimedornot');
			
			
			$position = 0;
			
			$worker = Staff::where('staff_id', $request->get('staff_id'))->first();
			
			if ($worker) {
				$position = $worker->position;
			}
			
			
			$bills = Bills::when(!empty($anymal_id) & $anymal_id != 0, function ($query) use ($anymal_id) {
				$query->where('bills.patient_id', $anymal_id);})	

				->when(!empty($client) & $client != 0, function ($query) use ($client) {
					$query->join('patients','bills.patient_id', '=', 'patients.patient_id')
					->where('patients.client_id', $client)
				;})
				
				->when(!empty($naimedornot) & $naimedornot != 0, function ($query) use ($naimedornot) {				
					
					if ($naimedornot == 1) {
						
						return $query->where(function($query) {
							return $query->whereNull('bills.patient_id')->orWhere('bills.patient_id', '=', 0);
						});
						
					} else if ($naimedornot == 2) {
						
						return $query->where(function($query) {
							return $query->whereNotNull('bills.patient_id')->where('bills.patient_id', '!=', 0);
						});
						
					}
				})
					
				->when(!empty($request->bill_date_start), function ($query) use ($bill_date_start, $bill_date_end) {
				$query->whereBetween('date_of_bill', [$bill_date_start, $bill_date_end]);})	
			
				->when(empty($request->bill_date_start) & !empty($request->bill_date_end), function ($query) use ($bill_date_end) {
				$query->whereDate('date_of_bill', '<=', $bill_date_end);})
				
				->when(!empty($bill_paied) & $bill_paied != 0, function ($query) use ($bill_paied) {				
									
					if ($bill_paied == 1) {					
						$query->whereColumn('bill_summ', 'paied');
					} else if ($bill_paied == 2) {
						$query->whereColumn('bill_summ', '!=', 'paied');
					}

				})

			->latest('date_of_bill')
			->orderBy('bill_id', 'desc')
			->get(); 

			
			// Для страницы всех счетов
			$table_dogdata_for_billpage = '';
						
			if ($request->bill_page) {
				$table_dogdata_for_billpage = '<div class="table-title text-dark col px-2 justify-content-center d-flex align-items-center text-break">Вид</div>
					<div class="table-title text-dark col px-2 justify-content-center d-flex align-items-center text-break">Кличка</div>
					<div class="table-title text-dark col px-2 justify-content-center d-flex align-items-center text-break">Владелец</div>';			
			}
			
			
			if($bills->count() > 0){
				
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
							
							.hoverDiv {
								background: #fff;
							}
							
							.hoverDiv:hover {
								background: #f5f5f5;
							}

						</style>
						
						<div class="d-none d-lg-block border-bottom p-0 m-0">
							<div class="d-lg-flex flex-row justify-content-center mx-2 py-2">
								<div class="table-title text-dark col-1 px-2 justify-content-center d-flex align-items-center text-break">ID</div>
								<div class="table-title text-dark col px-2 justify-content-center d-flex align-items-center text-break">Дата</div>
								<div class="table-title text-dark col px-2 justify-content-center d-flex align-items-center text-break">Сотрудник</div>							
								'.$table_dogdata_for_billpage.'
								<div class="table-title text-dark col px-2 justify-content-center d-flex align-items-center text-break">Сумма</div>
								<div class="table-title text-dark col px-2 justify-content-center d-flex align-items-center text-break">Оплачено</div>				
							</div>
						</div>
					';
					
				// Для проверки наличия счетов после проверки JSON
				$thereis_bills = false;	
				
				$bills_count = 0;
				
				
				foreach($bills as $row){
					
					// Фильтр товаров
					$thereis_product = false;
					
					if ($row->product_text != '') {

						$products_json = (array) json_decode($row->product_text);
						
						foreach($products_json as $product){
							
							if ($request->product_filter != 0 & $product->product_name_todb == $product_selected) {
								$thereis_product = true;							
							}	
						}
					} 
					
					
					// Фильтр услуг
					$thereis_service = false;
					
					if ($row->service_text != '') {

						$servises_json = (array) json_decode($row->service_text);
						
						foreach($servises_json as $service){
							
							if ($request->service_filter != 0 & $service->service_name_todb == $service_selected) {
								$thereis_service = true;							
							}	
						}
					} 

					// Поиск по значению в JSON, т.к. MySQL ниже 5.7 не поддерживает работу с JSON
					if (($request->product_filter == 0 & $request->service_filter == 0) | ($request->product_filter != 0 & $request->service_filter != 0 & $thereis_product & $thereis_service) | ($request->product_filter != 0 & $request->service_filter == 0 & $thereis_product & !$thereis_service) | ($request->product_filter == 0 & $request->service_filter != 0 & !$thereis_product & $thereis_service)) {

					
						// Для проверки наличия счетов после проверки JSON
						$thereis_bills = true;
					
					
						$summ = $summ + $row->bill_summ;
						
						
						// Оплата счетов
						$one_paied = 0;					
											
						$pays = Pays::where('bill_id', $row->bill_id)->get();
				
						if($pays->count() > 0){
						
							foreach($pays as $pay){
								
								$one_paied = $one_paied + $pay->pay_summ;

							}
						
						}					
						
						// Суммма общей оплаты счетов
						$paied = $paied + $one_paied;
					
						$waspaid = '';
						
						if ($one_paied < $row->bill_summ) {					
							
							$waspaid = '<span class="text-danger">'.number_format($one_paied, 2, ',', ' ').'</span>';
							
							// Сумма общего долга
							$debt = $debt + ($row->bill_summ - $one_paied);
													
							// Складываем неоплаченные счета в массив
							array_push($notpaied_bills, $row->bill_id);

						} else {
							
							$waspaid = '<span class="text-success">'.number_format($one_paied, 2, ',', ' ').'</span>';
							
						}

						// start Для страницы всех счетов
						$dogdata_for_billpage = '';
						
						if ($request->bill_page) {
						
							if ($row->patient_id) {
							
								$short_name = '';
								$anymal_type = '';
								$fio = '';
								
								$patient = Patients::where('patient_id', $row->patient_id)->first();	
				 
								if($patient){
									
									$short_name = $patient->short_name;
								
									if (!empty($patient->animal_type_id)) {
										$anymal_type = Animal_types::where('animal_type_id', $patient->animal_type_id)->first()->type_title;
									}
									
									$client = Clients::where('client_id', $patient->client_id)->first();
									
									if ($client) {
										$last_name = '';
										if (!empty($client->last_name)) {
											$last_name = $client->last_name;
										}
										$first_name = '';
										if (!empty($client->first_name)) {
											$first_name = ' '.$client->first_name;
										}
										$middle_name = '';
										if (!empty($client->middle_name)) {
											$middle_name = ' '.$client->middle_name;
										}
										
										$fio = $last_name.$first_name.$middle_name;
																							
									}
								}


								$dogdata_for_billpage = '<div class="col-lg px-0 px-lg-2 py-1 d-flex justify-content-lg-center align-items-lg-center" align="center">
									<div class="d-flex d-lg-none text-dark table-title-notlg align-self-center">Вид:</div>
									<div class="table-text text-body text-break align-self-center text-start text-lg-center">'.$anymal_type.'</div>																												
								</div>							
								<div class="col-lg px-0 px-lg-2 py-1 d-flex justify-content-lg-center align-items-lg-center" align="center">
									<div class="d-flex d-lg-none text-dark table-title-notlg align-self-center">Кличка:</div>
									<div class="table-text text-body text-break align-self-center text-start text-lg-center">'.$short_name.'</div>																																								
								</div>
								<div class="col-lg px-0 px-lg-2 py-1 d-flex justify-content-lg-center align-items-lg-center" align="center">
									<div class="d-flex d-lg-none text-dark table-title-notlg align-self-center">Владелец:</div>
									<div class="table-text text-body text-break align-self-center text-start text-lg-center">'.$fio.'</div>																																								
								</div>';							

							} else {
								
								$dogdata_for_billpage = '<div class="col-lg px-0 px-lg-2 py-1 d-flex justify-content-lg-center align-items-lg-center" align="center">
									<div class="d-flex d-lg-none text-dark table-title-notlg align-self-center">Вид:</div>
									<div class="table-text text-body text-break align-self-center text-start text-lg-center"></div>												
								</div>
								<div class="col-lg px-0 px-lg-2 py-1 d-flex justify-content-lg-center align-items-lg-center" align="center">
									<div class="d-flex d-lg-none text-dark table-title-notlg align-self-center">Кличка:</div>
									<div class="table-text text-body text-break align-self-center text-start text-lg-center"></div>																																								
								</div>
								<div class="col-lg px-0 px-lg-2 py-1 d-flex justify-content-lg-center align-items-lg-center" align="center">
									<div class="d-flex d-lg-none text-dark table-title-notlg align-self-center">Владелец:</div>
									<div class="table-text text-body text-break align-self-center text-start text-lg-center"></div>																																								
								</div>';
								
							}									
				
						}
						// end Для страницы всех счетов
						
												
						$output = $output.'
																	
							<a href="" onclick="open_bill('.$row->bill_id.'); return false;" class="text-decoration-none block leading-5 text-gray-700 transition duration-150 ease-in-out hover:bg-gray-100 focus:outline-none focus:bg-gray-100 p-0">

								<div class="hoverDiv border-bottom mx-0">

									<div class="d-lg-flex flex-row justify-content-center mx-2 py-2">
											
										<div class="col-lg-1 px-0 px-lg-2 py-1 d-flex justify-content-lg-center align-items-lg-center" align="center">
											<div class="d-flex d-lg-none text-dark table-title-notlg align-self-center">ID:</div>
											<div class="table-text text-body text-break align-self-center text-start text-lg-center">'.$row->bill_id.'</div>
										</div>
										
										<div class="col-lg px-0 px-lg-2 py-1 d-flex justify-content-lg-center align-items-lg-center" align="center">
											<div class="d-flex d-lg-none text-dark table-title-notlg align-self-center">Дата:</div>
											<div class="table-text text-body text-break align-self-center text-start text-lg-center">'.Carbon::parse($row->date_of_bill)->format('d.m.Y').'</div>
										</div>
										
										<div class="col-lg px-0 px-lg-2 py-1 d-flex justify-content-lg-center align-items-lg-center" align="center">
											<div class="d-flex d-lg-none text-dark table-title-notlg align-self-center">Сотрудник:</div>
											<div class="table-text text-body text-break align-self-center text-start text-lg-center">'.$row->staff.'</div>															
										</div>
										
										'.$dogdata_for_billpage.'
												
										<div class="col-lg px-0 px-lg-2 py-1 d-flex justify-content-lg-center align-items-lg-center" align="center">
											<div class="d-flex d-lg-none text-dark table-title-notlg align-self-center">Сумма:</div>
											<div class="table-text text-body text-break align-self-center text-start text-lg-center">'.number_format($row->bill_summ, 2, ',', ' ').'</div>
										</div>
										
										<div class="col-lg px-0 px-lg-2 py-1 d-flex justify-content-lg-center align-items-lg-center" align="center">
											<div class="d-flex d-lg-none text-dark table-title-notlg align-self-center">Оплачен:</div>
											<div class="table-text text-body text-break align-self-center text-start text-lg-center">'.$waspaid.'</div>
										</div>
																	
									</div>

								</div>
					
							</a>
						
						';
						
						
						$bills_count++;
						
						/*if ($bills_count >= 50) {
							break;
						}*/

					}

				}
				
				
				$output = $output.'</div>';
				
							
				if ($request->get('staff_id') == 0 | $position == 1) { 
				
					// Общая сумма долга, если есть неоплаченные счета
					$total_debt = '';
					
					if ($debt != 0) {
						$total_debt = '<div class="d-flex flex-row-reverse justify-content-start align-items-center mx-0 px-0">				

							<div class="">
								<div class="text-center fw-bold text-danger text-nowrap">'.number_format($debt, 2, ',', ' ').'</div>				
							</div>
							<div class="me-2">
								<div class="text-center fw-bold text-danger">Не оплачено (долг) руб.</div>
							</div>					
							<div class="col d-flex justify-content-end align-items-center me-3 text-nowrap" >								
								<button name="open_pay_selected_dialog_btn" id="open_pay_selected_dialog_btn"  type="button" onclick="open_pay_selected_dialog('.json_encode($notpaied_bills).')" class="btn btn-sm border-1 border-danger text-danger">Оплатить все</button>
							</div>
			
						</div>';
					}
				
					$output = $output.'<div class="align-items-center mt-2 mx-0 px-0 pe-4">								
						<div class="d-flex flex-row-reverse justify-content-start py-1 align-items-center mx-0 px-0">				
							<div class="">
								<div class="text-center fw-bold text-nowrap">'.number_format($summ, 2, ',', ' ').'</div>
							</div>
							<div class="me-2">
								<div class="text-center fw-bold">Общая сумма руб.</div>
							</div>				
						</div>
						
						<div class="d-flex flex-row-reverse justify-content-start py-1 align-items-center mx-0 px-0">				
							<div class="">
								<div class="text-center fw-bold text-nowrap">'.number_format($paied, 2, ',', ' ').'</div>
							</div>
							<div class="me-2">
								<div class="text-center fw-bold">Оплачено руб.</div>
							</div>				
						</div>

						'.$total_debt.'

					</div>';
				
				}
					
				
				/*if($bills_count >= 50){
					$output = $output.'
					<div class="d-block px-4 py-2 morethen50-text table-text-phone text-danger">
					* Найдено более 50 значений, показаны только первые 50. Уточните параметры поиска.
					</div>
					';
				}*/
				
				
				// Проверка наличия счетов после проверки JSON
				if (!$thereis_bills) {
					
					$output = '		
					<div class="d-flex justify-content-center align-items-center">
						<div class="d-block px-4 py-2">
							Нет счетов
						</div>
					</div>';
				
				}
				
			} else {
							
				$output = '		
					<div class="d-flex justify-content-center align-items-center">
						<div class="d-block px-4 py-2">
							Нет счетов
						</div>
					</div>';

			}
			
		}
		
		return response()->json(['success'=>$output]);
								   
	} 
	
	
	
	public function getOneBill(Request $request){
			
		$output = '';
		
		$outputprint = '';
		
		$position = 0;
		
		$worker = Staff::where('staff_id', $request->get('staff_id'))->first();
		
		if ($worker) {
			$position = $worker->position;
		}
		
		if ($request->get('staff_id') == 0 | $position == 1 | $position == 2) {
			
			$output = '<div class="d-flex align-items-center">								

					<div class="col-6 d-flex justify-content-start align-items-center">
						<button type="button" onclick="close_bill()" class="btn btn-sm border-1 border-secondary text-secondary">&#8592 К списку</button>
					</div>

					<div class=" col-6 d-flex justify-content-end align-items-center" >								
						<a class="nav-link p-1" onclick="print_bill()" role="button">	
							<img class="img-responsive" width="40" height="40" src="images/print.png">
						</a>
						
						<div class="btn-group dropstart dropdown">							  
							<a class="nav-link p-1" data-bs-toggle="dropdown" aria-expanded="false" role="button">	
								<img class="img-responsive" width="40" height="40" src="images/settings.png">
							</a>
							<ul class="dropdown-menu dropdown-3">
								<li><a class="dropdown-item" role="button" onclick="change_bill('.$request->get('bill_id').')">Изменить счет</a></li>
								<li><a class="dropdown-item" role="button" onclick="delete_bill_dialog('.$request->get('bill_id').')">Удалить счет</a></li>
							</ul>
						</div>
					</div>	
					
				</div>';
			
		} else {
			
			$output = '<div class="d-flex align-items-center">								

					<div class="col-6 d-flex justify-content-start align-items-center">
						<button type="button" onclick="close_bill()" class="btn btn-sm border-1 border-secondary text-secondary">&#8592 К списку</button>
					</div>

					<div class=" col-6 d-flex justify-content-end align-items-center" >								
						<a class="nav-link p-1" onclick="print_bill()" role="button">	
							<img class="img-responsive" width="40" height="40" src="images/print.png">
						</a>
					</div>	
					
				</div>';
			
		}
		
			
		$output = $output.'
		
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

			</style>

			<div name="visit" id="visit" class="border border rounded p-2 mt-2 pb-4">
				
				<div class="flex-none my-3 mx-2">
						
					<div class="row px-2">
					
						<div class="col-lg px-0 pb-2 pb-lg-0 align-items-center">

							<div class="d-flex justify-content-lg-start align-items-center">								
								<img src="images/dogicon.png" height="50" alt="Доктор Вет">	
								<div class="h3 align-middle d-inline-flex px-2">Доктор Вет</div>
							</div>										

						</div>
						
						<div class="col-lg px-0">
						
							<div class="flex-none justify-content-lg-start align-items-lg-center">								
								<div class="align-middle justify-content-start justify-content-lg-end d-flex px-2">г. Пермь, ул. Лебедева 25 Б</div>
								<div class="align-middle justify-content-start justify-content-lg-end d-flex px-2">Email: Doktorvet.perm@mail.ru</div>
								<div class="align-middle justify-content-start justify-content-lg-end d-flex px-2">Тел.: +7(342)247-01-09</div>
							</div>
						
						</div>
					</div>	
				</div>
										
				<div class="h3 align-middle justify-content-center d-flex px-2 my-3">Счет</div>';	
				
		
		$outputprint = $outputprint.'
		
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

			</style>

			<div class="flex-none my-3 mx-2">
									
				<div class="row px-2">
				
					<div class="col px-0 pb-2 pb-0 align-items-center">

						<div class="d-flex justify-content-start align-items-center">								
							<img src="images/dogicon.png" height="50" alt="Доктор Вет">	
							<div class="h3 align-middle d-inline-flex px-2">Доктор Вет</div>
						</div>										

					</div>
					
					<div class="col px-0">
					
						<div class="flex-none justify-content-start align-items-center">								
							<div class="align-middle justify-content-start justify-content-end d-flex px-2">г. Пермь, ул. Лебедева 25 Б</div>
							<div class="align-middle justify-content-start justify-content-end d-flex px-2">Email: Doktorvet.perm@mail.ru</div>
							<div class="align-middle justify-content-start justify-content-end d-flex px-2">Тел.: +7(342)247-01-09</div>
						</div>
					
					</div>
				</div>	
			</div>
									
			<div class="h3 align-middle justify-content-center d-flex px-2 my-3">Счет</div>';	
		
		$bill = Bills::where('bill_id', $request->get('bill_id'))->first();

		if($bill){
						
			$date = '';
			
			if ($bill->date_of_bill) {
				$date = Carbon::parse($bill->date_of_bill)->format('d.m.Y');
			}
						
			$staff = '-';
			
			if ($bill->staff && $bill->staff != 'Нет данных') {
				$staff = $bill->staff;
			}
			
			
			$pays_summ = 0;
			
			
			$waspaid = '';
						
			$pays = Pays::where('bill_id', $bill->bill_id)->oldest('date_of_pay')->get();
			
			if($pays->count() > 0){
			
				foreach($pays as $pay){
					
					$waspaid = $waspaid.'<span class="">'.number_format($pay->pay_summ, 2, ',', ' ').'&nbsp;руб.&nbsp;</span>'.'<span class="fst-italic">'.Carbon::parse($pay->date_of_pay)->format('d.m.Y').';&nbsp;</span>';
			
					$pays_summ = $pays_summ + $pay->pay_summ;
			
				}
			
			}
			
									
			$output = $output.'
						
				<div class="flex-none mb-3">
					<div class="align-middle justify-content-start d-flex px-2"><span class="fw-bold">ID:&nbsp;</span>'.$bill->bill_id.'</div>
					<div class="align-middle justify-content-start d-flex px-2"><span class="fw-bold">Дата:&nbsp;</span>'.$date.'</div>
					<div class="align-middle justify-content-start d-flex px-2 text-break"><span class="fw-bold">Сотрудник:&nbsp;</span>'.$staff.'</div>
					<div class="align-middle justify-content-start d-flex flex-wrap px-2 text-break"><span class="fw-bold">Оплаты:&nbsp;</span>'.$waspaid.'</div>
				</div>';
					
			$outputprint = $outputprint.'
						
				<div class="flex-none mb-3">
					<div class="align-middle justify-content-start d-flex px-2"><span class="fw-bold">Дата:&nbsp;</span>'.$date.'</div>
					<div class="align-middle justify-content-start d-flex px-2 text-break"><span class="fw-bold">Сотрудник:&nbsp;</span>'.$staff.'</div>
				</div>';	
								
						
			if($bill->patient_id != '' & $bill->patient_id != 0 & $bill->patient_id != null & !empty($bill->patient_id)) {
							 
				$onepatient = Patients::where('patient_id', $bill->patient_id)->first();
			
				if ($onepatient) {
					
					$onepatient_short_name = '';
					
					if (!empty($onepatient->short_name)) {
						$onepatient_short_name = $onepatient->short_name;
					}
					
					$onepatient_anymal_type = '';
					if (!empty($onepatient->animal_type_id)) {
						$onepatient_anymal_type = Animal_types::where('animal_type_id', $onepatient->animal_type_id)->first()->type_title;
					}
					
					$onepatient_anymal_breed = '';
					if (!empty($onepatient->breed_id)) {
						$onepatient_anymal_breed = Breeds::where('breed_id', $onepatient->breed_id)->first()->breed_title;
					}
					
					$onepatient_anymal_color = '';
					if (!empty($onepatient->color_id)) {
						$onepatient_anymal_color = Colors::where('color_id', $onepatient->color_id)->first()->color_title;
					}

					$onepatient_sex = '';
					if (!empty($onepatient->sex_id)) {
						if ($onepatient->sex_id == 1) {
							$onepatient_sex = 'Мужской';
						} else if ($onepatient->sex_id == 2) {
							$onepatient_sex = 'Женский';
						}
					}

					$onepatient_birth_date = '';
					if (!empty($onepatient->date_of_birth)) {					
						$onepatient_birth_date = Carbon::parse($onepatient->date_of_birth)->format('d.m.Y');
					}


					if($onepatient->client_id != '' & $onepatient->client_id != 0 & $onepatient->client_id != null & !empty($onepatient->client_id)) {
								 
						$oneclient = Clients::where('client_id', $onepatient->client_id)->first();
					}
					
					if ($oneclient) {
						
						$oneclient_name = '';
						
						$last = '';
						$first = '';
						$middle = '';
						
						if (!empty($oneclient->last_name)) {
							$last = $oneclient->last_name;
						}
						
						if (!empty($oneclient->first_name)) {
							$first = ' '.$oneclient->first_name;
						}
						
						if (!empty($oneclient->middle_name)) {
							$middle = ' '.$oneclient->middle_name;
						}
						
						$oneclient_name = $last.$first.$middle;
						
						
						$oneclient_address = '';
						
						if (!empty($oneclient->address)) {
							
							$address_city_part = '';
							
							if (!empty(json_decode($oneclient->address)->city)) {
								$address_city_part = json_decode($oneclient->address)->city;
							}
							
							$address_part = '';
							
							if (!empty(json_decode($oneclient->address)->address)) {
								$address_part = json_decode($oneclient->address)->address;
							}
							
							
							$address_comma_part = '';
						
							if (!empty($address_city_part) && !empty($address_part)) {			
								$address_comma_part = ', ';
							}
							
							$oneclient_address = $address_city_part.$address_comma_part.$address_part;						
							
						}					
						
					}				
					
					
					$output = $output.'<div class="flex-none mb-3 mx-2">
						
						<div class="row px-2">
						
							<div class="col-lg col-md-border px-0 pb-2 pb-lg-0">
					
								<div class="d-flex justify-content-lg-start align-items-lg-center me-lg-3">								
									<h5 class="text-body align-self-center px-1">Пациент</h5>	
								</div>										
								
								<div class="justify-content-lg-start align-items-lg-center me-lg-3 px-1 d-flex"><span class="fw-bold">Кличка:&nbsp;</span><span class="text-break">'.$onepatient_short_name.'</span></div>
								<div class="justify-content-lg-start align-items-lg-center me-lg-3 px-1 d-flex"><span class="fw-bold">Вид:&nbsp;</span><span class="text-break">'.$onepatient_anymal_type.'</span></div>  
								<div class="justify-content-lg-start align-items-lg-center me-lg-3 px-1 d-flex"><span class="fw-bold">Порода:&nbsp;</span><span class="text-break">'.$onepatient_anymal_breed.'</span></div> 
								<div class="justify-content-lg-start align-items-lg-center me-lg-3 px-1 d-flex"><span class="fw-bold">Пол:&nbsp;</span><span class="text-break">'.$onepatient_sex.'</span></div> 
								<div class="justify-content-lg-start align-items-lg-center me-lg-3 px-1 d-flex"><span class="fw-bold">Окрас:&nbsp;</span><span class="text-break">'.$onepatient_anymal_color.'</span></div>
								<div class="justify-content-lg-start align-items-lg-center me-lg-3 px-1 d-flex"><span class="fw-bold">Дата рождения:&nbsp;</span><span class="text-break">'.$onepatient_birth_date.'</span></div> 
							
							</div>
							
							<div class="col-lg px-0 pt-2 pt-lg-0 ps-lg-3 px-0">
							
								<div class="d-flex justify-content-lg-start align-items-lg-center me-lg-3">								
									<h5 class="text-body align-self-center px-1">Владелец</h5>
								</div>
								<div class="d-flex justify-content-lg-start align-items-lg-center me-lg-3">	
									<div class="justify-content-lg-start align-items-lg-center me-lg-3 px-1 d-flex"><span class="fw-bold">Ф.И.О.:&nbsp;</span><span class="text-break">'.$oneclient_name.'</span></div> 				
								</div>
								<div class="d-flex justify-content-lg-start align-items-lg-center me-lg-3">	
									<div class="justify-content-lg-start align-items-lg-center me-lg-3 px-1 d-flex"><span class="fw-bold">Адрес:&nbsp;</span><span class="text-break">'.$oneclient_address.'</span></div> 			
								</div>
								
							</div>
						</div>	
					</div>';
									
					$outputprint = $outputprint.'<div class="flex-none mb-3 mx-2">
									
						<div class="row px-2">
						
							<div class="col border-end px-0 me-3">
					
								<div class="d-flex justify-content-start align-items-center me-3">								
									<h5 class="text-body align-self-center px-1">Пациент</h5>	
								</div>										
								
								<div class="justify-content-start align-items-center px-1 d-flex"><span class="fw-bold">Кличка:&nbsp;</span><span class="text-break">'.$onepatient_short_name.'</span></div>
								<div class="justify-content-start align-items-center px-1 d-flex"><span class="fw-bold">Вид:&nbsp;</span><span class="text-break">'.$onepatient_anymal_type.'</span></div>
								<div class="justify-content-start align-items-center px-1 d-flex"><span class="fw-bold">Порода:&nbsp;</span><span class="text-break">'.$onepatient_anymal_breed.'</span></div>
								<div class="justify-content-start align-items-center px-1 d-flex"><span class="fw-bold">Пол:&nbsp;</span><span class="text-break">'.$onepatient_sex.'</span></div>
								<div class="justify-content-start align-items-center px-1 d-flex"><span class="fw-bold">Окрас:&nbsp;</span><span class="text-break">'.$onepatient_anymal_color.'</span></div>
								<div class="justify-content-start align-items-center px-1 d-flex"><span class="fw-bold">Дата рождения:&nbsp;</span><span class="text-break">'.$onepatient_birth_date.'</span></div>
							
							</div>
							
							<div class="col px-0 ps-3">
							
								<div class="d-flex justify-content-start align-items-center me-3">								
									<h5 class="text-body align-self-center px-1">Владелец</h5>
								</div>
									<div class="justify-content-start align-items-center px-1 d-flex"><span class="fw-bold">Ф.И.О.:&nbsp;</span><span class="text-break">'.$oneclient_name.'</span></div>				
									<div class="justify-content-start align-items-center px-1 d-flex"><span class="fw-bold">Адрес:&nbsp;</span><span class="text-break">'.$oneclient_address.'</span></div>				
								
							</div>
						</div>	
					</div>';

				}
				
			}
			
				
			if ($bill->product_text && json_decode($bill->product_text, true)) {
										
				$output = $output.'
				
				<div class="h5 align-middle justify-content-center d-flex px-2 mt-3 mb-1">Товары</div>
				
				<div class="d-none d-lg-block border-bottom p-0 mx-2 m-0">
					<div class="d-lg-flex flex-row justify-content-center mx-2 py-2">
						<div class="table-title text-dark col-4 px-2 justify-content-start d-flex align-items-center text-break">Название</div>
						<div class="table-title text-dark col-2 px-2 justify-content-center d-flex align-items-center text-break">Ед. измерения</div>
						<div class="table-title text-dark col-2 px-2 justify-content-center d-flex align-items-center text-break">Цена</div>
						<div class="table-title text-dark col-2 px-2 justify-content-center d-flex align-items-center text-break">Количество</div>
						<div class="table-title text-dark col-2 px-2 justify-content-center d-flex align-items-center text-break">Стоимость</div>
					</div>
				</div>';
				
				$outputprint = $outputprint.'
				
				<div class="h5 align-middle justify-content-center d-flex px-2 mt-3 mb-1">Товары</div>
				
				<div class="d-block border-bottom p-0 mx-2 m-0">
					<div class="d-lg-flex flex-row justify-content-center mx-2 py-2">
						<div class="table-title text-dark col-4 px-2 justify-content-start d-flex align-items-center text-break">Название</div>
						<div class="table-title text-dark col-2 px-2 justify-content-center d-flex align-items-center text-break">Ед. измерения</div>
						<div class="table-title text-dark col-2 px-2 justify-content-center d-flex align-items-center text-break">Цена</div>
						<div class="table-title text-dark col-2 px-2 justify-content-center d-flex align-items-center text-break">Количество</div>
						<div class="table-title text-dark col-2 px-2 justify-content-center d-flex align-items-center text-break">Стоимость</div>
					</div>
				</div>';
				
				foreach((array) json_decode($bill->product_text) as $oneline){
											
					
					$output = $output.'<div class="border-bottom mx-2">

						<div class="d-lg-flex flex-row justify-content-center mx-0 mx-lg-2 py-2">
								
							<div class="col-lg-4 px-0 px-lg-2 py-1 d-flex justify-content-start align-items-lg-center" align="center">
								<div class="d-flex d-lg-none text-dark table-title-notlg align-self-center">Название:</div>
								<div class="table-text text-body text-break align-self-center text-start text-lg-center">'.$oneline->product_name_todb.'</div>
							</div>
							
							<div class="col-lg-2 px-0 px-lg-2 py-1 d-flex justify-content-lg-center align-items-lg-center" align="center">
								<div class="d-flex d-lg-none text-dark table-title-notlg align-self-center">Ед. измерения:</div>
								<div class="table-text text-body text-break align-self-center text-start text-lg-center">'.$oneline->product_edizm_todb.'</div>
							</div>
							
							<div class="col-lg-2 px-0 px-lg-2 py-1 d-flex justify-content-lg-center align-items-lg-center" align="center">
								<div class="d-flex d-lg-none text-dark table-title-notlg align-self-center">Цена:</div>
								<div class="table-text text-body text-break align-self-center text-start text-lg-center">'.number_format($oneline->product_price_todb, 2, ',', ' ').'</div>
							</div>
							
							<div class="col-lg-2 px-0 px-lg-2 py-1 d-flex justify-content-lg-center align-items-lg-center" align="center">
								<div class="d-flex d-lg-none text-dark table-title-notlg align-self-center">Количество:</div>
								<div class="table-text text-body text-break align-self-center text-start text-lg-center">'.$oneline->product_count_todb.'</div>
							</div>
																
							<div class="col-lg-2 px-0 px-lg-2 py-1 d-flex justify-content-lg-center align-items-lg-center" align="center">
								<div class="d-flex d-lg-none text-dark table-title-notlg align-self-center">Стоимость:</div>
								<div class="table-text text-body text-break align-self-center text-start text-lg-center">'.number_format($oneline->product_price_todb * $oneline->product_count_todb, 2, ',', ' ').'</div>
							</div>
					
						</div>

					</div>';
												
					$outputprint = $outputprint.'<div class="border-bottom mx-2">

						<div class="d-lg-flex flex-row justify-content-center mx-2 py-2">
								
							<div class="col-4 px-0 px-2 py-1 d-flex justify-content-start align-items-center" align="center">
								<div class="table-text text-body text-break align-self-center text-start text-center">'.$oneline->product_name_todb.'</div>
							</div>
							
							<div class="col-2 px-0 px-2 py-1 d-flex justify-content-center align-items-center" align="center">
								<div class="table-text text-body text-break align-self-center text-start text-center">'.$oneline->product_edizm_todb.'</div>
							</div>
							
							<div class="col-2 px-0 px-2 py-1 d-flex justify-content-center align-items-center" align="center">
								<div class="table-text text-body text-break align-self-center text-start text-center">'.number_format($oneline->product_price_todb, 2, ',', ' ').'</div>
							</div>
							
							<div class="col-2 px-0 px-2 py-1 d-flex justify-content-center align-items-center" align="center">
								<div class="table-text text-body text-break align-self-center text-start text-center">'.$oneline->product_count_todb.'</div>
							</div>
																
							<div class="col-2 px-0 px-2 py-1 d-flex justify-content-center align-items-center" align="center">
								<div class="table-text text-body text-break align-self-center text-start text-center">'.number_format($oneline->product_price_todb * $oneline->product_count_todb, 2, ',', ' ').'</div>
							</div>
					
						</div>

					</div>';
				}
				
								
				if ($bill->product_discount != 0) {
					
					$output = $output.'<div class="d-flex flex-row-reverse justify-content-start mx-3 py-1 pe-3">					
						<div class="px-0 px-2 py-1 d-flex justify-content-center align-items-center">
							<div class="text-center fw-bold text-nowrap">'.number_format($bill->product_discount, 0, ',', ' ').'</div>
						</div>
						<div class="px-0 px-2 py-1 d-flex justify-content-end align-items-center">
							<div class="text-center fw-bold">Скидка %</div>
						</div>
									
					</div>';
						
					$outputprint = $outputprint.'<div class="d-flex flex-row-reverse justify-content-start mx-3 py-1 pe-3">					
						<div class="px-0 px-2 py-1 d-flex justify-content-center align-items-center">
							<div class="text-center fw-bold text-nowrap">'.number_format($bill->product_discount, 0, ',', ' ').'</div>
						</div>
						<div class="px-0 px-2 py-1 d-flex justify-content-end align-items-center">
							<div class="text-center fw-bold">Скидка %</div>
						</div>
									
					</div>';

				}
				
				
				$output = $output.'<div class="d-flex flex-row-reverse justify-content-start mx-3 pb-2 pe-3">				
					<div class="px-0 px-2 py-1 d-flex justify-content-center align-items-center">
						<div class="text-center fw-bold text-nowrap">'.number_format($bill->product_summ, 2, ',', ' ').'</div>
					</div>
					<div class="px-0 px-2 py-1 d-flex justify-content-end align-items-center">
						<div class="text-center fw-bold">Итого товаров руб.</div>
					</div>	
								
				</div>';
					
				$outputprint = $outputprint.'<div class="d-flex flex-row-reverse justify-content-start mx-3 pb-2 pe-3">					
					<div class="px-0 px-2 py-1 d-flex justify-content-center align-items-center">
						<div class="text-center fw-bold text-nowrap">'.number_format($bill->product_summ, 2, ',', ' ').'</div>
					</div>
					<div class="px-0 px-2 py-1 d-flex justify-content-end align-items-center">
						<div class="text-center fw-bold">Итого товаров руб.</div>
					</div>
								
				</div>';
					
			}
			
			
			if ($bill->service_text && json_decode($bill->service_text, true)) {
										
				$output = $output.'
				
				<div class="h5 align-middle justify-content-center d-flex px-2 mt-3 mb-1">Услуги</div>
				
				<div class="d-none d-lg-block border-bottom p-0 mx-2 m-0">
					<div class="d-lg-flex flex-row justify-content-center mx-2 py-2">
						<div class="table-title text-dark col-3 px-2 justify-content-start d-flex align-items-center text-break">Название</div>
						<div class="table-title text-dark col-3 px-2 justify-content-center d-flex align-items-center text-break">Сотрудник</div>
						<div class="table-title text-dark col-2 px-2 justify-content-center d-flex align-items-center text-break">Цена</div>
						<div class="table-title text-dark col-2 px-2 justify-content-center d-flex align-items-center text-break">Количество</div>
						<div class="table-title text-dark col-2 px-2 justify-content-center d-flex align-items-center text-break">Стоимость</div>
					</div>
				</div>';
				
				$outputprint = $outputprint.'
				
				<div class="h5 align-middle justify-content-center d-flex px-2 mt-3 mb-1">Услуги</div>
				
				<div class="d-block border-bottom p-0 mx-2 m-0">
					<div class="d-lg-flex flex-row justify-content-center mx-2 py-2">
						<div class="table-title text-dark col-6 px-2 justify-content-start d-flex align-items-center text-break">Название</div>
						<div class="table-title text-dark col-2 px-2 justify-content-center d-flex align-items-center text-break">Цена</div>
						<div class="table-title text-dark col-2 px-2 justify-content-center d-flex align-items-center text-break">Количество</div>
						<div class="table-title text-dark col-2 px-2 justify-content-center d-flex align-items-center text-break">Стоимость</div>
					</div>
				</div>';
				
				
				foreach((array) json_decode($bill->service_text) as $oneline){

					$service_doctor = '';
					
					if (isset($oneline->service_doctor_text_todb)) {
						$service_doctor = $oneline->service_doctor_text_todb;
					}
											
					
					$output = $output.'<div class="border-bottom mx-2">

						<div class="d-lg-flex flex-row justify-content-center mx-0 mx-lg-2 py-2">
								
							<div class="col-lg-3 px-0 px-lg-2 py-1 d-flex justify-content-start align-items-lg-center" align="center">
								<div class="d-flex d-lg-none text-dark table-title-notlg align-self-center">Название:</div>
								<div class="table-text text-body text-break align-self-center text-start">'.$oneline->service_name_todb.'</div>
							</div>

							<div class="col-lg-3 px-0 px-lg-2 py-1 d-flex justify-content-lg-center align-items-lg-center" align="center">
								<div class="d-flex d-lg-none text-dark table-title-notlg align-self-center">Сотрудник:</div>
								<div class="table-text text-body text-break align-self-center text-start text-lg-center">'.$service_doctor.'</div>
							</div>
							
							<div class="col-lg-2 px-0 px-lg-2 py-1 d-flex justify-content-lg-center align-items-lg-center" align="center">
								<div class="d-flex d-lg-none text-dark table-title-notlg align-self-center">Цена:</div>
								<div class="table-text text-body text-break align-self-center text-start text-lg-center">'.number_format($oneline->service_price_todb, 2, ',', ' ').'</div>
							</div>
							
							<div class="col-lg-2 px-0 px-lg-2 py-1 d-flex justify-content-lg-center align-items-lg-center" align="center">
								<div class="d-flex d-lg-none text-dark table-title-notlg align-self-center">Количество:</div>
								<div class="table-text text-body text-break align-self-center text-start text-lg-center">'.$oneline->service_count_todb.'</div>
							</div>
																
							<div class="col-lg-2 px-0 px-lg-2 py-1 d-flex justify-content-lg-center align-items-lg-center" align="center">
								<div class="d-flex d-lg-none text-dark table-title-notlg align-self-center">Стоимость:</div>
								<div class="table-text text-body text-break align-self-center text-start text-lg-center">'.number_format($oneline->service_price_todb * $oneline->service_count_todb, 2, ',', ' ').'</div>
							</div>
					
						</div>

					</div>';
												
					$outputprint = $outputprint.'<div class="border-bottom mx-2">

						<div class="d-lg-flex flex-row justify-content-center mx-2 py-2">
								
							<div class="col-6 px-0 px-2 py-1 d-flex justify-content-start align-items-center" align="center">
								<div class="table-text text-body text-break align-self-center text-start text-center">'.$oneline->service_name_todb.'</div>
							</div>
							
							<div class="col-2 px-0 px-2 py-1 d-flex justify-content-center align-items-center" align="center">
								<div class="table-text text-body text-break align-self-center text-start text-center">'.number_format($oneline->service_price_todb, 2, ',', ' ').'</div>
							</div>
							
							<div class="col-2 px-0 px-2 py-1 d-flex justify-content-center align-items-center" align="center">
								<div class="table-text text-body text-break align-self-center text-start text-center">'.$oneline->service_count_todb.'</div>
							</div>
																
							<div class="col-2 px-0 px-2 py-1 d-flex justify-content-center align-items-center" align="center">
								<div class="table-text text-body text-break align-self-center text-start text-center">'.number_format($oneline->service_price_todb * $oneline->service_count_todb, 2, ',', ' ').'</div>
							</div>
					
						</div>

					</div>';
				}
				
							
				if ($bill->service_discount != 0) {
					
					$output = $output.'<div class="d-flex flex-row-reverse justify-content-start mx-3 py-1 pe-3">					
						<div class="px-0 px-2 py-1 d-flex justify-content-center align-items-center">
							<div class="text-center fw-bold text-nowrap">'.number_format($bill->service_discount, 0, ',', ' ').'</div>
						</div>
						<div class="px-0 px-2 py-1 d-flex justify-content-end align-items-center">
							<div class="text-center fw-bold">Скидка %</div>
						</div>
									
					</div>';
						
					$outputprint = $outputprint.'<div class="d-flex flex-row-reverse justify-content-start mx-3 py-1 pe-3">					
						<div class="px-0 px-2 py-1 d-flex justify-content-center align-items-center">
							<div class="text-center fw-bold text-nowrap">'.number_format($bill->service_discount, 0, ',', ' ').'</div>
						</div>
						<div class="px-0 px-2 py-1 d-flex justify-content-end align-items-center">
							<div class="text-center fw-bold">Скидка %</div>
						</div>
									
					</div>';

				}
				
				
				$output = $output.'<div class="d-flex flex-row-reverse justify-content-start mx-3 pb-2 pe-3">				
					<div class="px-0 px-2 py-1 d-flex justify-content-center align-items-center">
						<div class="text-center fw-bold text-nowrap">'.number_format($bill->service_summ, 2, ',', ' ').'</div>
					</div>
					<div class="px-0 px-2 py-1 d-flex justify-content-end align-items-center">
						<div class="text-center fw-bold">Итого услуг руб.</div>
					</div>	
								
				</div>';
					
				$outputprint = $outputprint.'<div class="d-flex flex-row-reverse justify-content-start mx-3 pb-2 pe-3">					
					<div class="px-0 px-2 py-1 d-flex justify-content-center align-items-center">
						<div class="text-center fw-bold text-nowrap">'.number_format($bill->service_summ, 2, ',', ' ').'</div>
					</div>
					<div class="px-0 px-2 py-1 d-flex justify-content-end align-items-center">
						<div class="text-center fw-bold">Итого услуг руб.</div>
					</div>
								
				</div>';
					
			}
			
			
			if ($bill->bill_summ != 0) {
					
					$output = $output.'<div class="d-flex flex-row-reverse justify-content-start mx-3 py-1 pe-3">	
						<div class="px-0 px-2 py-1 d-flex justify-content-center align-items-center">
							<div class="h5 text-center fw-bold text-nowrap">'.number_format($bill->bill_summ, 2, ',', ' ').'</div>
						</div>
						<div class="px-0 px-2 py-1 d-flex justify-content-end align-items-center">
							<div class="h5 text-center fw-bold">Всего по счету руб.</div>
						</div>					
					</div>
					
					<div class="d-flex flex-row-reverse justify-content-start mx-3 py-1 pe-3">		
						<div class="px-0 px-2 py-1 d-flex justify-content-center align-items-center">
							<div class="h5 text-center fw-bold text-nowrap">'.number_format($pays_summ, 2, ',', ' ').'</div>
						</div>
						<div class="px-0 px-2 py-1 d-flex justify-content-end align-items-center">
							<div class="h5 text-center fw-bold">Оплачено по счету руб.</div>
						</div>						
					</div>
					
					<div class="d-flex flex-row-reverse justify-content-start mx-3 py-1 pe-3">	
						<div class="px-0 px-2 py-1 d-flex justify-content-center align-items-center">
							<div class="h5 text-center fw-bold text-nowrap">'.number_format($bill->bill_summ - $pays_summ, 2, ',', ' ').'</div>
						</div>					
						<div class="px-0 px-2 py-1 d-flex justify-content-end align-items-center">
							<div class="h5 text-center fw-bold">Осталось оплатить по счету руб.</div>
						</div>	
						
									
					</div>';
					
						
					$outputprint = $outputprint.'<div class="d-flex flex-row-reverse justify-content-start mx-3 py-1 pe-3">					
						<div class="px-0 px-2 py-1 d-flex justify-content-center align-items-center">
							<div class="h5 text-center fw-bold text-nowrap">'.number_format($bill->bill_summ, 2, ',', ' ').'</div>
						</div>
						<div class="px-0 px-2 py-1 d-flex justify-content-end align-items-center">
							<div class="h5 text-center fw-bold">Всего по счету руб.</div>
						</div>
									
					</div>
					
					<div class="d-flex flex-row-reverse justify-content-start mx-3 py-1 pe-3">					
						<div class="px-0 px-2 py-1 d-flex justify-content-center align-items-center">
							<div class="h5 text-center fw-bold text-nowrap">'.number_format($pays_summ, 2, ',', ' ').'</div>
						</div>
						<div class="px-0 px-2 py-1 d-flex justify-content-end align-items-center">
							<div class="h5 text-center fw-bold">Оплачено по счету руб.</div>
						</div>
									
					</div>
					
					<div class="d-flex flex-row-reverse justify-content-start mx-3 py-1 pe-3">					
						<div class="px-0 px-2 py-1 d-flex justify-content-center align-items-center">
							<div class="h5 text-center fw-bold text-nowrap">'.number_format($bill->bill_summ - $pays_summ, 2, ',', ' ').'</div>
						</div>
						<div class="px-0 px-2 py-1 d-flex justify-content-end align-items-center">
							<div class="h5 text-center fw-bold">Осталось оплатить по счету руб.</div>
						</div>
									
					</div>';

				}
				
			$output = $output.'</div>';
			
		} else {
					
		$output = '	

			<div class="d-flex align-items-center">								
				<div class="col-6 d-flex justify-content-start align-items-center">
					<button type="button" onclick="close_bill()" class="btn btn-sm border-1 border-secondary text-secondary">&#8592 К списку</button>
				</div>
			</div>
			
			<div class="d-flex justify-content-center align-items-center">
				<div class="d-block px-4 py-2">
					Данные не найдены
				</div>
			</div>';
			
		}
						   
		return response()->json(['success'=>$output, 'successprint'=>$outputprint]);
		
	}


	
	public function getBillData (Request $request){
		
		$bill = Bills::where('bill_id', $request->get('bill_id'))->first();

		if($bill){
			
			$shortname = '';
			
			$patient = Patients::where('patient_id', $bill->patient_id)->first();
			
			if ($patient) {
				$shortname = $patient->short_name;
			}
			
			
			$pays = Pays::where('bill_id', $bill->bill_id)->oldest('date_of_pay')->get();
			
			
			$pays_json = '';
			
			
			if($pays->count() > 0){
				
				$all_pays = [];
			
				foreach($pays as $pay){
		
					array_push($all_pays, array("date_of_pay"=>Carbon::parse($pay->date_of_pay)->format('d.m.Y'),"pay_summ"=>$pay->pay_summ));
									
				}
				
				$pays_json = json_encode($all_pays);
			
			}
			
							
			return response()->json(['success'=>$bill, 'shortname'=>$shortname, 'pays'=>$pays_json]);
			
		} else {
			
			return response()->json(['error'=>'Ошибка загрузки данных']);
			
		}
		
	}
	
	
	// Удаление счета
	public function deleteBill(Request $request){
				
		if ($request->get('id') !== null & $request->get('id') !== '0') {		

			$billtodel = Bills::where('bill_id', $request->get('id'))->first();
			
			if ($billtodel) {
				
				// Удаляем все оплаты
				Pays::where('bill_id', $request->get('id'))->delete();
				
				Bills::destroy($request->get('id'));
				
				return response()->json(['success'=>'Удачно удалено']);
				
				
			} else {
				
				return response()->json(['error'=>'Ошибка удаления, перезагрузите страницу и попробуйте еще раз']);
				
			}

		} else {
			return response()->json(['error'=>'Ошибка удаления, перезагрузите страницу и попробуйте еще раз']);
		}
		
	}
	
	
	// Счета оплачены
	public function payAllBills(Request $request){
				
		if ($request->get('bills') !== null & count($request->get('bills')) !== '0') {		

			foreach((array) json_decode($request->get('bills')) as $row){
								
				$onebilltopay = Bills::where('bill_id', $row)->first();
				
				$onebilltopay->update(['paied' => $onebilltopay->bill_summ]);
			
			}
			
			return response()->json(['success'=>'Изменения внесены']);
			
		} else {
			return response()->json(['error'=>'Ошибка, перезагрузите страницу и попробуйте еще раз']);
		}
		
	}
	
	
	
	public function getPaysList(Request $request){
				
		$output = '';
		
		
		if (empty($request->pay_date_start) & empty($request->pay_date_end) & empty($request->anymal_id) & empty($request->client_id)) {   
			
			$output = '<div class="d-flex justify-content-center align-items-center mt-3">
						<div class="d-block px-4 py-2">
							Не заданы параметры поиска.
						</div>
					</div>';		
		} else {
		

			$pay_date_start = '';
			
			$summ = 0;
							
			if (!empty($request->pay_date_start)) {
				$pay_date_start = Carbon::parse($request->get('pay_date_start'))->format('Y-m-d');
			}			
			
			if (!empty($request->pay_date_end)) {
				$pay_date_end = Carbon::parse($request->get('pay_date_end'))->format('Y-m-d');
			} else {
				$pay_date_end = Carbon::now()->format('Y-m-d');;
			}
			
			if (!empty($request->pay_date_start) && !empty($request->pay_date_end) && Carbon::createFromFormat('Y-m-d', $pay_date_start)->gt(Carbon::createFromFormat('Y-m-d', $pay_date_end))) {
				return response()->json(['error'=>'Некорректный выбор дат']);
			}


			$anymal_id = $request->get('anymal_id');
			
			$client = $request->get('client_id');
			
			
			$pays = Pays::when(!empty($anymal_id) & $anymal_id != 0, function ($query) use ($anymal_id) {
				$query->where('pays.patient_id', $anymal_id);})	

				->when(!empty($client) & $client != 0, function ($query) use ($client) {
					$query->join('patients','pays.patient_id', '=', 'patients.patient_id')
					->where('patients.client_id', $client)
				;})
					
				->when(!empty($request->pay_date_start), function ($query) use ($pay_date_start, $pay_date_end) {
				$query->whereBetween('date_of_pay', [$pay_date_start, $pay_date_end]);})	
			
				->when(empty($request->pay_date_start) & !empty($request->pay_date_end), function ($query) use ($pay_date_end) {
				$query->whereDate('date_of_pay', '<=', $pay_date_end);})

			->latest('date_of_pay')
			->orderBy('pay_id', 'desc')
			//->take(100)
			->get(); 
			
			if($pays->count() > 0){
				
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
							
							.hoverDiv {
								background: #fff;
							}
							
							.hoverDiv:hover {
								background: #f5f5f5;
							}

						</style>
						
						<div class="d-none d-lg-block border-bottom p-0 m-0">
							<div class="d-lg-flex flex-row justify-content-center mx-2 py-2">
								<div class="table-title text-dark col-1 px-2 justify-content-center d-flex align-items-center text-break">ID</div>
								<div class="table-title text-dark col-1 px-2 justify-content-center d-flex align-items-center text-break">ID счета</div>
								<div class="table-title text-dark col px-2 justify-content-center d-flex align-items-center text-break">Дата</div>						
								<div class="table-title text-dark col px-2 justify-content-center d-flex align-items-center text-break">Вид</div>
								<div class="table-title text-dark col px-2 justify-content-center d-flex align-items-center text-break">Кличка</div>
								<div class="table-title text-dark col px-2 justify-content-center d-flex align-items-center text-break">Владелец</div>
								<div class="table-title text-dark col px-2 justify-content-center d-flex align-items-center text-break">Сумма</div>				
							</div>
						</div>
					';
								
				foreach($pays as $row){
									
					$summ = $summ + $row->pay_summ;
					
					
					// start Для страницы всех счетов
					$dogdata = '';
					
					
					if ($row->patient_id) {
						
						$short_name = '';
						$anymal_type = '';
						$fio = '';
						
						$patient = Patients::where('patient_id', $row->patient_id)->first();	
		 
						if($patient){
							
							$short_name = $patient->short_name;
						
							if (!empty($patient->animal_type_id)) {
								$anymal_type = Animal_types::where('animal_type_id', $patient->animal_type_id)->first()->type_title;
							}
							
							$client = Clients::where('client_id', $patient->client_id)->first();
							
							if ($client) {
								$last_name = '';
								if (!empty($client->last_name)) {
									$last_name = $client->last_name;
								}
								$first_name = '';
								if (!empty($client->first_name)) {
									$first_name = ' '.$client->first_name;
								}
								$middle_name = '';
								if (!empty($client->middle_name)) {
									$middle_name = ' '.$client->middle_name;
								}
								
								$fio = $last_name.$first_name.$middle_name;
																					
							}
						}


						$dogdata = '<div class="col-lg px-0 px-lg-2 py-1 d-flex justify-content-lg-center align-items-lg-center" align="center">
							<div class="d-flex d-lg-none text-dark table-title-notlg align-self-center">Вид:</div>
							<div class="table-text text-body text-break align-self-center text-start text-lg-center">'.$anymal_type.'</div>																												
						</div>							
						<div class="col-lg px-0 px-lg-2 py-1 d-flex justify-content-lg-center align-items-lg-center" align="center">
							<div class="d-flex d-lg-none text-dark table-title-notlg align-self-center">Кличка:</div>
							<div class="table-text text-body text-break align-self-center text-start text-lg-center">'.$short_name.'</div>																																								
						</div>
						<div class="col-lg px-0 px-lg-2 py-1 d-flex justify-content-lg-center align-items-lg-center" align="center">
							<div class="d-flex d-lg-none text-dark table-title-notlg align-self-center">Владелец:</div>
							<div class="table-text text-body text-break align-self-center text-start text-lg-center">'.$fio.'</div>																																								
						</div>';							

					} else {
						
						$dogdata = '<div class="col-lg px-0 px-lg-2 py-1 d-flex justify-content-lg-center align-items-lg-center" align="center">
							<div class="d-flex d-lg-none text-dark table-title-notlg align-self-center">Вид:</div>
							<div class="table-text text-body text-break align-self-center text-start text-lg-center"></div>												
						</div>
						<div class="col-lg px-0 px-lg-2 py-1 d-flex justify-content-lg-center align-items-lg-center" align="center">
							<div class="d-flex d-lg-none text-dark table-title-notlg align-self-center">Кличка:</div>
							<div class="table-text text-body text-break align-self-center text-start text-lg-center"></div>																																								
						</div>
						<div class="col-lg px-0 px-lg-2 py-1 d-flex justify-content-lg-center align-items-lg-center" align="center">
							<div class="d-flex d-lg-none text-dark table-title-notlg align-self-center">Владелец:</div>
							<div class="table-text text-body text-break align-self-center text-start text-lg-center"></div>																																								
						</div>';
						
					}

											
					$output = $output.'
																
						<div class="border-bottom mx-0 px-0">

							<div class="d-lg-flex flex-row justify-content-center mx-2 py-2">
									
								<div class="col-lg-1 px-0 px-lg-2 py-1 d-flex justify-content-lg-center align-items-lg-center" align="center">
									<div class="d-flex d-lg-none text-dark table-title-notlg align-self-center">ID:</div>
									<div class="table-text text-body text-break align-self-center text-start text-lg-center">'.$row->pay_id.'</div>
								</div>
								
								<div class="col-lg-1 px-0 px-lg-2 py-1 d-flex justify-content-lg-center align-items-lg-center" align="center">
									<div class="d-flex d-lg-none text-dark table-title-notlg align-self-center">ID:</div>
									<div class="table-text text-body text-break align-self-center text-start text-lg-center"><a href="#" onclick="open_bill_from_pays('.$row->bill_id.'); event.preventDefault();" class="">'.$row->bill_id.'</a></div>
								</div>
								
								<div class="col-lg px-0 px-lg-2 py-1 d-flex justify-content-lg-center align-items-lg-center" align="center">
									<div class="d-flex d-lg-none text-dark table-title-notlg align-self-center">Дата:</div>
									<div class="table-text text-body text-break align-self-center text-start text-lg-center">'.Carbon::parse($row->date_of_pay)->format('d.m.Y').'</div>
								</div>
								
								'.$dogdata.'
										
								<div class="col-lg px-0 px-lg-2 py-1 d-flex justify-content-lg-center align-items-lg-center" align="center">
									<div class="d-flex d-lg-none text-dark table-title-notlg align-self-center">Сумма:</div>
									<div class="table-text text-body text-break align-self-center text-start text-lg-center">'.number_format($row->pay_summ, 2, ',', ' ').'</div>
								</div>
															
							</div>

						</div>
					';	
				}
				
				
				$output = $output.'</div>';
					
				
				$output = $output.'<div class="align-items-center mt-2 mx-0 px-0 pe-4">								
					<div class="d-flex flex-row-reverse justify-content-start py-1 align-items-center mx-0 px-0">				
						<div class="">
							<div class="text-center fw-bold text-nowrap">'.number_format($summ, 2, ',', ' ').'</div>
						</div>
						<div class="me-2">
							<div class="text-center fw-bold">Общая сумма руб.</div>
						</div>				
					</div>				
					
				</div>';
					
				
				/*if($pays->count() >= 100){
					$output = $output.'
					<div class="d-block px-4 py-2 morethen50-text table-text-phone text-danger">
					* Найдено более 100 значений, показаны только первые 100. Уточните параметры поиска.
					</div>
					';
				}*/
							
			} else {
							
				$output = '		
					<div class="d-flex justify-content-center align-items-center">
						<div class="d-block px-4 py-2">
							Нет счетов
						</div>
					</div>';

			}
			
		}
		
		return response()->json(['success'=>$output]);
								   
	} 


	// Список счетов
	public function getSalaryData(Request $request){
		

		$salary_data = [];
				
		$bill_date_start = Carbon::parse($request->get('startdate'))->format('Y-m-d');

		$bill_date_end = Carbon::parse($request->get('enddate'))->format('Y-m-d');

		$staff = $request->get('staff');


		$bills = Bills::whereBetween('date_of_bill', [$bill_date_start, $bill_date_end])->get();


		$productcategoryes = ProductCategory::all();
				
				
		$servicecategoryes = ServiceCategory::all();

		
		$doctors = '';
		
		if ($staff == 0) {
		
			$doctors = Staff::where('position', 2)->get();

		} else {

			$doctors = Staff::where('position', 2)->where('staff_id', $staff)->get();

		}

		if($doctors->count() > 0){

			foreach($doctors as $row){
				
				$doctorid = $row->staff_id;
				
				$fio = $row->last_name.' '.$row->first_name.' '.$row->middle_name;
								
				$product_data = [];

				$product_data_no_doctor = [];

				foreach($productcategoryes as $prodcat){

					
					$product_category_summ = 0;

					$product_no_category_summ = 0;

					$product_no_doctor_summ = 0;

					$product_no_doctor_no_category_summ = 0;


					foreach($bills as $billprodcount){


						if ($billprodcount->product_text && json_decode($billprodcount->product_text, true)) {


							foreach((array) json_decode($billprodcount->product_text) as $oneprodline){

								$prodforcat = 0;


								if (array_key_exists('product_id_todb', (array) $oneprodline) && $oneprodline->product_id_todb != null && $oneprodline->product_id_todb != 'undefined') {
									$catid = 0;
									$catid = $oneprodline->product_id_todb;
									$prodforcat = Products::where('id', $catid)->first();
								} else if (array_key_exists('product_name_todb', (array) $oneprodline)) {
									$catname = 0;
									$catname = $oneprodline->product_name_todb;
									$prodforcat = Products::where('product_title', 'like', '%'.$catname.'%')->first();

								}

								if ($prodforcat) {

									if ($billprodcount->staff_id == $doctorid && $prodforcat->category == $prodcat->id) {

										$product_category_summ = $product_category_summ + $oneprodline->product_price_todb*$oneprodline->product_count_todb;

									} else if ($prodforcat->category == null | $prodforcat->category == 0) {

										if ($billprodcount->staff_id == $doctorid) {

											$product_no_category_summ = $product_no_category_summ + $oneprodline->product_price_todb*$oneprodline->product_count_todb;
	
										} else if ($billprodcount->staff_id == null) {
											$product_no_doctor_no_category_summ = $product_no_doctor_no_category_summ + $oneprodline->product_price_todb*$oneprodline->product_count_todb;
										}

									} else if ($billprodcount->staff_id == null && $prodforcat->category == $prodcat->id) {

										$product_no_doctor_summ = $product_no_doctor_summ + $oneprodline->product_price_todb*$oneprodline->product_count_todb;

									} 

								} 

							}

						}
												
					}

					array_push($product_data, ["productname"=>$prodcat->title,"summ"=>$product_category_summ,"percent"=>$prodcat->percent]);

					array_push($product_data_no_doctor, ["productname"=>$prodcat->title,"summ"=>$product_no_doctor_summ,"percent"=>$prodcat->percent]);
									
				}

				array_push($product_data, ["productname"=>"Нет категории","summ"=>$product_no_category_summ,"percent"=>0]);

				array_push($product_data_no_doctor, ["productname"=>"Нет категории","summ"=>$product_no_doctor_no_category_summ,"percent"=>0]);


				$service_data = [];

				$service_data_no_doctor = [];

				foreach($servicecategoryes as $servcat){

					$service_category_summ = 0;

					$service_no_category_summ = 0;

					$service_no_doctor_summ = 0;

					$service_no_doctor_no_category_summ = 0;

					foreach($bills as $billservcount){

						if ($billservcount->service_text && json_decode($billservcount->service_text, true)) {

							foreach((array) json_decode($billservcount->service_text) as $oneservline){

								$servforcat = '';

								if (array_key_exists('service_id_todb', (array) $oneservline) && $oneservline->service_id_todb != null && $oneservline->service_id_todb != 'undefined') {
									$catid = 0;
									$catid = $oneservline->service_id_todb;
									$servforcat = Services::where('id', $catid)->first();
								} else if (array_key_exists('service_name_todb', (array) $oneservline)) {
									$catname = 0;
									$catname = $oneservline->service_name_todb;
									$servforcat = Services::where('service_title', 'like', '%'.$catname.'%')->first();

								}

								if ($servforcat) {
									
									if (array_key_exists('service_doctor_todb', (array) $oneservline)) {

										if ($oneservline->service_doctor_todb == $doctorid && $servforcat->category == $servcat->id) {

											$service_category_summ = $service_category_summ + $oneservline->service_price_todb*$oneservline->service_count_todb;

										} else if ($servforcat->category == null | $servforcat->category == 0) {

											if ($oneservline->service_doctor_todb == $doctorid) {
		
												$service_no_category_summ = $service_no_category_summ + $oneservline->service_price_todb*$oneservline->service_count_todb;
		
											} 
										} 

									} else {

										if ($servforcat->category == $servcat->id) {

											$service_no_doctor_summ = $service_no_doctor_summ + $oneservline->service_price_todb*$oneservline->service_count_todb;

										} else if ($servforcat->category == null | $servforcat->category == 0) {

											$service_no_doctor_no_category_summ = $service_no_doctor_no_category_summ + $oneservline->service_price_todb*$oneservline->service_count_todb;

										}

									}

								} 

							}

						}
												
					}

					array_push($service_data, ["servicename"=>$servcat->title,"summ"=>$service_category_summ,"percent"=>$servcat->percent]);

					array_push($service_data_no_doctor, ["servicename"=>$servcat->title,"summ"=>$service_no_doctor_summ,"percent"=>$servcat->percent]);
				
				}

				array_push($service_data, ["servicename"=>"Нет категории","summ"=>$service_no_category_summ,"percent"=>0]);

				array_push($service_data_no_doctor, ["servicename"=>"Нет категории","summ"=>$service_no_doctor_no_category_summ,"percent"=>0]);
								
				
				$staffdata = ["staffid"=>$doctorid,"staffname"=>$fio,"products"=>$product_data, "services"=>$service_data];
				
				array_push($salary_data, $staffdata);

			}

			if ($staff == 0) {
				$nodoctordata = ["staffid"=>0,"staffname"=>"Сотрудник не указан","products"=>$product_data_no_doctor, "services"=>$service_data_no_doctor];
				array_push($salary_data, $nodoctordata);
			}

		}

		return response()->json(['success'=>$salary_data]);
								   
	}
	
}
