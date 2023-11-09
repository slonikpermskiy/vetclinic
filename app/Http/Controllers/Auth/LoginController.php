<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use App\ExpirationDate;
use Carbon\Carbon;
use Illuminate\Contracts\Encryption\DecryptException;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */
	
	protected $maxAttempts = 100; // Default is 5
	protected $decayMinutes = 1; // Default is 1

    use AuthenticatesUsers;
	
	
	protected function attemptLogin(Request $request)
	{
		
		// Проверка на истечение срока ипользования
		$access = true;
		
		$date = ExpirationDate::all()->first();
		
		$expiration_date = '';
		
		if ($date) {
			try {	
				$expiration_date = Carbon::parse(decrypt($date->expiration_date));
			} catch (DecryptException $e) {
			}
		}
		
		if ($expiration_date) {
			
			// Сегодня минус день 
			$now = Carbon::now()->subDay();
			
			// Если больше
			if($now->gt($expiration_date)) {
				if ($request->username !== 'superadmin') {
					$access = false;
				}
			}			
		}
		
		
		if ($access) {
			return (auth()->attempt(['username' => $request->username, 'password' => $request->password]));
		} else {

		}
	}
	
	protected function sendFailedLoginResponse(Request $request)
    {
        $errors = [$this->username() => trans('Неверный логин или пароль')];

        // Load user from database
        $user = \App\User::where($this->username(), $request->{$this->username()})->first();

        if ($user && \Hash::check($request->password, $user->password)) {
            $errors = [$this->username() => 'Доступ запрещен, свяжитесь с разработчиком'];
        }

        if ($request->expectsJson()) {
            return response()->json($errors, 422);
        }

        return redirect()->back()
            ->withInput($request->only($this->username(), 'remember'))
            ->withErrors($errors);
    }
	
	

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }
	
	
	public function username()
	{
		return 'username';
	}
	
}
