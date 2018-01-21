<?php

namespace App\Http\Controllers;

use App\User;
use Exception;
use Illuminate\Http\Request;
use App\Algorithms\Helpers\HttpHelper;

class HealthController extends Controller
{
    public function update(Request $request, User $user)
    {
    	try {
    		$user->in_danger = 1;
    		$user->saveOrFail();
    		return HttpHelper::json(['message' => 'The user has been updated successfully !']);
    	} catch (Exception $e) {
    		return HttpHelper::json(['message' => 'An error occured ! :('], 500);
    	}
    }

    public function restore(Request $request, User $user)
    {
        try {
            $user->in_danger = 0;
            $user->saveOrFail();
            return HttpHelper::json(['message' => 'The user has been updated successfully !']);
        } catch (Exception $e) {
            return HttpHelper::json(['message' => 'An error occured ! :('], 500);
        }
    }
}
