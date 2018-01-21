<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;
use App\Http\Requests\LocationRequest;
use App\Algorithms\Helpers\HttpHelper;

class LocationController extends Controller
{
	/**
	 * Update the location of the user
	 */
    public function update(LocationRequest $request, User $user)
    {
    	try {
    		$data = $request->all();
    		$user->latitude  = $data['latitude'] * 1000000;
    		$user->longitude = $data['longitude'] * 1000000;
    		$user->saveOrFail();
    		return HttpHelper::json(['message' => 'The location was saved successfully ! :)'], 201);
    	} catch (Exception $e) {
    		return HttpHelper::json([
    			'data' => null,
    			'message' => 'An error occured!'
    		]);
    	}
    }
}
