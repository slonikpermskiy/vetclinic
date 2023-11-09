<?php

namespace App\Http\Middleware;

use Closure;
use App\User;
use Illuminate\Support\Facades\Auth;

class SuperAdmin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {	
		$superAdmin = false;
    
		if (Auth::user()->staff_id == 0) {
			
			$superAdmin = true;
		}

		if( ! $superAdmin )
		{
			if ($request->ajax()) {
				return response('Unauthorized.', 401);
			} else {
				return redirect()->back(); //todo h peut-etre une fenetre modale pour dire acces refusĂ© ici...
			}
		}
		
		return $next($request);
    }
}
