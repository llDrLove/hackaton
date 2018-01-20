<?php

namespace App\Http\Controllers;

use DB;
use App\User;
use App\Data;
use Illuminate\Http\Request;
use App\Http\Requests\DataRequest;
use App\Algorithms\Helpers\HttpHelper;

class DataController extends Controller
{
	/**
	 * Handle the incoming data from the receptor
	 */
    // public function index(DataRequest $request, User $user)
    public function index(DataRequest $request, User $user)
    {
    	try {
    		$requestData = $request->all();
    		$data = new Data();
    		$data->spo2 = floatval($requestData['spo2']) * 100;
    		$data->pulse = floatval($requestData['pulse']) * 100;
    		$data->saveOrFail();
    		DB::table('user_data')->insert(
			    ['user_id' => $user->id, 'data_id' => $data->id]
			);
    		return HttpHelper::json(['message' => 'The data was saved successfully'], 200);
    	} catch (Exception $e) {
    		return HttpHelper::json(['message' => 'An error occured !'], 500);
    	}
    }
}
