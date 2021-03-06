<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;
use App\Http\Requests\LocationRequest;
use App\Algorithms\Helpers\HttpHelper;

class LocationController extends Controller
{
    const DIVIDER = 1000000;
	/**
	 * Update the location of the user
	 */
    public function update(LocationRequest $request, User $user)
    {
    	try {
    		$data = $request->all();
    		$user->latitude  = $data['latitude'] * self::DIVIDER;
    		$user->longitude = $data['longitude'] * self::DIVIDER;
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
