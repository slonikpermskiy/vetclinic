<?php

namespace App\Http\Middleware;

use Closure;
use App\Staff;
use App\User;
use Illuminate\Support\Facades\Auth;

class AccessControl
{
	
	public function handle($request, Closure $next)
	{
		
		$User = Staff::where('staff_id', Auth::user()->staff_id)->first();
	
	
		$UserRole = 0;
		
		if ($User) {
			$UserRole = $User->position;
		}
    
		$fullAccess = false;
    
		if ($UserRole == 0 | $UserRole == 1) {
			$fullAccess = true;
		}

		if( ! $fullAccess )
		{
			if ($request->ajax()) {
				return response('Unauthorized.', 401);
			} else {
				return redirect()->back(); //todo h peut-etre une fenetre modale pour dire acces refusĂ© ici...
			}
		}		
		
		return $next($request);
	}
	
		//return redirect('/');
		//return abort(404);

}
