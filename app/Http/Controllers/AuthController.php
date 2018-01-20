<?php

namespace App\Http\Controllers;

use Auth;
use App\User;
use Illuminate\Http\Request;
use App\Http\Requests\AuthRequest;
use App\Algorithms\Helpers\HttpHelper;

class AuthController extends Controller
{
	/**
	 * Login or create the new user
	 */
    public function index(AuthRequest $request)
    {
    	try {
    		if (Auth::attempt(['email' => $request['email'], 'password' => $request['password']])) {
    			return HttpHelper::json([
    				'userId' => Auth::user()
    			]);
    		} else {
    			$user = new User();
    			$user->name = $request['name'];
    			$user->email = $request['email'];
    			$user->password = bcrypt($request['password']);
    			$user->saveOrFail();
    			return HttpHelper::json([
    				'userId' => $user->id,
    				'message' => 'Account created !'
    			], 200);
    		}	
		} catch (Exception $e) {
			return HttpHelper::json(['message' => 'An error occured!'], 500);
		}	
    }
}
