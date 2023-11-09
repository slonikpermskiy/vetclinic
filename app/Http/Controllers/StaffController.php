<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Staff;
use App\User;
use App\Clients;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Validation\Rule;


use Illuminate\Support\Facades\Auth;
use Illuminate\Auth\Events\Registered;

use Illuminate\Support\Str;


class StaffController extends Controller
{
	
	/*public function __construct()
    {
        $this->middleware('auth');
    }*/
	
    public function showStaffPage()
    {
        return view('staff_page');
    }
	
	
	public function register(Request $request)
    {
		
		$variable = $request->get('password');
        	
		$messages = [
			'username.unique' => 'Уже существует такой логин',
			'username.required' => 'Не заполнено поле',
			'password.min' => 'Пароль д.б. не менее 6 символов<br>',
			'password.required' => 'Не заполнено поле',
			/*'password.confirmed' => 'Пароли не совпадают',*/
			'password_confirmation.required' => 'Не заполнено поле',
			'password_confirmation.in' => 'Пароли не совпадают',
			'last_name.required' => 'Не заполнено поле',
			'last_name.regex' => 'Поле содержит недопустимые символы',
			'first_name.regex' => 'Поле содержит недопустимые символы',
			'middle_name.regex' => 'Поле содержит недопустимые символы',
			'username.regex' => 'Поле содержит недопустимые символы',
			'first_name.required' => 'Не заполнено поле',
			'position.required' => 'Не выбрано значение',
		];

				
		if ($request->get('without_access') == "0") {
			// Валидация на ошибки в полях
			$validator = Validator::make($request->all(), [		
				'username' => ['required', 'string', 'max:255', 'unique:users', 'regex:/^[^`~#$^&*=\\|\\[\\]{}<>]*$/',], 
				'password' => 'required|string|min:6',  // |confirmed
				'password_confirmation' => 'required|string|in:'.$variable,
				'last_name' => ['required', 'max:255', 'regex:/^[^`~#$^&*=\\|\\[\\]{}<>]*$/',], 
				'first_name' => ['required', 'max:255', 'regex:/^[^`~#$^&*=\\|\\[\\]{}<>]*$/',],
				'middle_name' => ['nullable', 'max:255', 'regex:/^[^`~#$^&*=\\|\\[\\]{}<>]*$/',],
				'position' => 'required',
			], $messages);
		} else {
			// Валидация на ошибки в полях
			$validator = Validator::make($request->all(), [		
				'last_name' => ['required', 'max:255', 'regex:/^[^`~#$^&*=\\|\\[\\]{}<>]*$/',], 
				'first_name' => ['required', 'max:255', 'regex:/^[^`~#$^&*=\\|\\[\\]{}<>]*$/',],
				'middle_name' => ['nullable', 'max:255', 'regex:/^[^`~#$^&*=\\|\\[\\]{}<>]*$/',],
				'position' => 'required',
			], $messages);
		}
		
		if ($validator->passes()) {
				
			$staff = Staff::create(['last_name'  => $request->get('last_name'),'first_name' => $request->get('first_name'),
			'middle_name' => $request->get('middle_name'),'position' =>  $request->get('position'),]);
			
			if ($request->get('without_access') == "0") {			
				User::create(['staff_id' => $staff->staff_id, 'username' => $request->get('username'), 'password' => Hash::make($request->get('password')),]);
			}

			//$this->guard()->login($user);
			//return $this->registered($request, $user)?: redirect($this->redirectPath());
		
			return response()->json(['success'=>'Сохранено']);
		
		}
		
		return response()->json(['error'=>$validator->messages()->get('*')]);
		
    }
	
	
	public function getWorkerData(Request $request){
			
		if($request->get('query') != '') {						 
			$worker = Staff::where('staff_id', $request->get('query'))->first();
			$user =  User::where('staff_id', $request->get('query'))->first();
		}
		
		if ($worker) { 
			return response()->json(['success'=>$worker, 'user'=>$user]);
		} else {
			return response()->json(['error'=>'nodatafound']);
		}	
    }
	
	
	public function getUserData(Request $request){
			
		if($request->get('query') != '') {						 
			$user =  User::where('staff_id', $request->get('query'))->first();
		}
		
		if ($user) { 
			return response()->json(['success'=>$user]);
		} else {
			return response()->json(['error'=>'nodatafound']);
		}
		
    }
	
	
	public function showUsers(){
					
		$output = '';
		
		$users = Staff::all();
		
		if($users->count() > 0){
			
			$output = '<div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 row-cols-xl-4 row-cols-xxl-5 g-4">';
			
			$i = 0;
			
			foreach($users as $row){
				
				$position = '';
				
				if ($row->position == "1") {
					$position = 'Администратор';
				} else if ($row->position == "2") {
					$position = 'Врач';
				} else if ($row->position == "3") {
					$position = 'Ассистент';
				}
									
				$username = '';
				
				$userauthdata = User::where('staff_id', $row->staff_id)->first();
				
				if ($userauthdata) {
					$username = $userauthdata->username;
				} else {
					$username = 'Нет доступа в систему';
				}
				
				$last_name = '';
				$first_name = '';
				$middle_name = '';
					
				if ($row->last_name) {
					$last_name = $row->last_name;
				}
				
				if ($row->first_name) {
					$first_name = ' '.$row->first_name;
				}
				
				if ($row->middle_name) {
					$middle_name = ' '.$row->middle_name;
				}
						
				$fullname = $last_name.$first_name.$middle_name;
				
				
				$options = '';
				
				if ($row->position == "1") {
					$options = '<ul class="dropdown-menu dropdown-m  dropdown-'.$i.'" name="dropdown-'.$i.'"  id="dropdown-'.$i.'">
								<li><a class="dropdown-item" role="button" onclick="change_staff_data_dialog('.$row->staff_id.')">Изменить данные</a></li>
								<li><a class="dropdown-item" role="button" onclick="change_login_password_dialog('.$row->staff_id.')">Сменить логин/пароль</a></li>
								<li><a class="dropdown-item" role="button" onclick="delete_staff_dialog('.$row->staff_id.')">Удалить</a></li>
								</ul>';
				} else {
					
					if ($userauthdata) {
						$options = '<ul class="dropdown-menu dropdown-m  dropdown-'.$i.'" name="dropdown-'.$i.'"  id="dropdown-'.$i.'">
								<li><a class="dropdown-item" role="button" onclick="change_staff_data_dialog('.$row->staff_id.')">Изменить данные</a></li>
								<li><a class="dropdown-item" role="button" onclick="change_login_password_dialog('.$row->staff_id.')">Сменить логин/пароль</a></li>
								<li><a class="dropdown-item" role="button" onclick="deny_access_dialog('.$row->staff_id.')">Запретить доступ</a></li>
								<li><a class="dropdown-item" role="button" onclick="delete_staff_dialog('.$row->staff_id.')">Удалить</a></li>
								</ul>';
					} else {
						$options = '<ul class="dropdown-menu dropdown-m dropdown-'.$i.'" name="dropdown-'.$i.'"  id="dropdown-'.$i.'">
								<li><a class="dropdown-item" role="button" onclick="change_staff_data_dialog('.$row->staff_id.')">Изменить данные</a></li>
								<li><a class="dropdown-item" role="button" onclick="change_login_password_dialog('.$row->staff_id.')">Предоставить доступ</a></li>
								<li><a class="dropdown-item" role="button" onclick="delete_staff_dialog('.$row->staff_id.')">Удалить</a></li>
								</ul>';
					}
					
				}
				
								
				$output = $output.'
														
					<div class="col d-flex">
						<div class="card w-100">
						  <div class="card-body">
						  
							<div name="client_name_container"  id="client_name_container" class="d-flex justify-content-start align-items-center">								

								<h5 class="card-title mb-0">'.$position.'</h5>
								
								<div class="btn-group dropstart ms-auto">							  
									<a class="nav-link p-1" data-bs-toggle="dropdown" aria-expanded="false" role="button">	
										<img class="img-responsive" width="30" height="30" src="images/settings_120.png">
									</a>
									'.$options.'
								</div>
																		
							</div>	
							<div class="card-text mb-1">'.$fullname.'</div>
							<div class="card-text mb-1 fst-italic">'.$username.'</div>
						  </div>
						</div>
					</div>				
				';	

				$i++;
				
			}
			
			$output = $output.'</div>';

		} else {
			$output = '		
				<div class="d-flex justify-content-center align-items-center">
					<div class="d-block px-4 py-2">
						Данные не найдены.
					</div>
				</div>';
		}
								   
		return response()->json(['success'=>$output]);	
    }
	
	
	public function deleteUser(Request $request){
										
		if ($request->get('staff_id') !== null & $request->get('staff_id') !== '0') {		
			
			Staff::destroy($request->get('staff_id'));
			User::where('staff_id', $request->get('staff_id'))->delete();
			
			return response()->json(['success'=>'Удачно удалено']);
		
		} else {
			return response()->json(['error'=>'Ошибка удаления, перезагрузите страницу и попробуйте еще раз']);
		}
	}
	
	
	public function changeUser(Request $request){
										
		$messages = [
			'last_name.regex' => 'Поле содержит недопустимые символы',
			'first_name.regex' => 'Поле содержит недопустимые символы',
			'middle_name.regex' => 'Поле содержит недопустимые символы',
			'position.required' => 'Не выбрано значение',
	
		];
		
		$validator = Validator::make($request->all(), [		
			'last_name' => ['required', 'max:255', 'regex:/^[^`~#$^&*=\\|\\[\\]{}<>]*$/',], 
			'first_name' => ['required', 'max:255', 'regex:/^[^`~#$^&*=\\|\\[\\]{}<>]*$/',],
			'middle_name' => ['nullable', 'max:255', 'regex:/^[^`~#$^&*=\\|\\[\\]{}<>]*$/',],
			'position' => 'required',
		], $messages);

		
		if ($validator->passes()) {
					
			Staff::where('staff_id', $request->get('change_user_id'))->update(['last_name'  => $request->get('last_name'),'first_name' => $request->get('first_name'),
			'middle_name' => $request->get('middle_name'),'position' =>  $request->get('position'),]);
		
			return response()->json(['success'=>'Сохранено']);
		
		}
		
		return response()->json(['error'=>$validator->messages()->get('*')]);		
	}
	
	
	
	public function changeLoginPassword(Request $request)
    {
						
		$variable = $request->get('password');
        	
		$messages = [
			'username.unique' => 'Уже существует такой логин',
			'username.required' => 'Не заполнено поле',
			'username.regex' => 'Поле содержит недопустимые символы',
			'password.min' => 'Пароль д.б. не менее 6 символов<br>',
			'password.required' => 'Не заполнено поле',
			'password_confirmation.required' => 'Не заполнено поле',
			'password_confirmation.in' => 'Пароли не совпадают',	
		];

				
		// Валидация на ошибки в полях
		$validator = Validator::make($request->all(), [		
			'username' => ['required', 'max:255', 'string', 'regex:/^[^`~#$^&*=\\|\\[\\]{}<>]*$/', Rule::unique('users')->ignore($request->get('change_login_password_user_id'), 'staff_id'),], 
			'password' => 'required|string|min:6',
			'password_confirmation' => 'required|string|in:'.$variable,
		], $messages);
			
		
		if ($validator->passes()) {
						
			$oldid = 0;
			
			$user = User::where('staff_id', $request->get('change_login_password_user_id'))->first();
			
			if ($user) {
				$oldid = $user->id;
			}
			
			User::updateOrCreate(['id' => $oldid], ['staff_id' => $request->get('change_login_password_user_id'), 'username' => $request->get('username'), 'password' => Hash::make($request->get('password')),]);
			
			return response()->json(['success'=>'Сохранено']);
		
		}
		
		return response()->json(['error'=>$validator->messages()->get('*')]);
		
    }
	
	
	
	public function denyAccess(Request $request){
											
		if ($request->get('staff_id') !== null & $request->get('staff_id') !== '0') {		
			
			User::where('staff_id', $request->get('staff_id'))->delete();
			
			return response()->json(['success'=>'Доступ запрещен']);
		
		} else {
			return response()->json(['error'=>'Ошибка, перезагрузите страницу и попробуйте еще раз']);
		}
	}
	
	
	
	public function searchStaff(Request $request){
		
		$title = trim($request->q);
							 
		$doctors = Staff::where('position', 2)->get();
		 
		if($doctors->count() > 0){
			
			$formatted_types = [];
			
			foreach($doctors as $row){
				$fio = $row->last_name.' '.$row->first_name.' '.$row->middle_name;
				$formatted_types[] = ['id' => $row->staff_id, 'text' => $fio];
			}
			
			return \Response::json($formatted_types);
			
		} else {
			return \Response::json([]);	
		}	
    }
	
}
