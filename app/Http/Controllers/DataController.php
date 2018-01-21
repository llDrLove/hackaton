<?php

namespace App\Http\Controllers;

use DB;
use App\User;
use App\Data;
use App\Events\TestEvent; 
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
            $requestData['pulse'] = explode(',', $request->all()['pulse']);
            $requestData['spo2'] = explode(',', $request->all()['spo2']);
            $responseData = [];
            for($i = 0; $i < count($requestData['pulse']); $i++) {
                $data = new Data();
                $data->spo2 = floatval($requestData['spo2'][$i]) * 100;
                $data->pulse = floatval($requestData['pulse'][$i]) * 100;
                $data->saveOrFail();
                array_push($responseData, $data);
                DB::table('user_data')->insert(
                    ['user_id' => $user->id, 'data_id' => $data->id]
                );
            }
            event(new TestEvent([
                'userId' => $user->id,
                'data' => $responseData  
            ]));
    		return HttpHelper::json(['message' => 'The data was saved successfully'], 200);
    	} catch (Exception $e) {
    		return HttpHelper::json(['message' => 'An error occured !'], 500);
    	}
    }
}
