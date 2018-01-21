<?php

namespace App\Http\Controllers;

use App\User;
use Exception;
use App\Response;
use App\Events\Repondant;
use Illuminate\Http\Request;
use App\Algorithms\Helpers\HttpHelper;

class HealthController extends Controller
{
    const DIVIDER = 1000000;

    public function update(Request $request, User $user)
    {
    	try {
            if (!$user->is_patient || $user->in_danger) {
                throw new Exception('You need to be a patient or not in danger!');
            }
    		$user->in_danger = 1;
    		$user->saveOrFail();
            $respondants = User::where('is_patient', 0)->get();
            $closerRespondant['distance'] = $this->distance($user->latitude / self::DIVIDER, 
                                                $user->longitude / self::DIVIDER, 
                                                $respondants[0]->latitude / self::DIVIDER, 
                                                $respondants[0]->longitude / self::DIVIDER, 
                                                'K');
            $closerRespondant['respondant'] = $respondants[0];
            foreach ($respondants as $key => $respondant) {
                $distance = $this->distance($user->latitude / self::DIVIDER,
                                    $user->longitude / self::DIVIDER,
                                    $respondant->latitude / self::DIVIDER,
                                    $respondant->longitude / self::DIVIDER,
                                    'K');
                if ($respondant->latitude && 
                    $respondant->longitude &&
                    $distance < $closerRespondant['distance']) {
                    $closerRespondant['distance'] = $distance;
                    $closerRespondant['respondant'] = $respondant;
                }
            }
            $response = new Response();
            $response->user_id = $user->id;
            $response->respondant_id = $closerRespondant['respondant']['id'];
            $response->saveOrFail();
            event(new Repondant([
                'type' => 'DYING',
                'user' => $closerRespondant['respondant'],
                'payload' => [
                    'userId' => $user->id,
                    'distance_km' => $closerRespondant['distance'],
                    'response_id' => $response->id,
                    'user_latitude' => $user->latitude / self::DIVIDER,
                    'user_longitude' => $user->longitude / self::DIVIDER
                ]
            ]));
    		return HttpHelper::json(['message' => 'The user has been updated successfully !']);
    	} catch (Exception $e) {
    		return HttpHelper::json(['message' => $e->getMessage()], 500);
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

    private function distance($lat1, $lon1, $lat2, $lon2, $unit) {
      $theta = $lon1 - $lon2;
      $dist = sin(deg2rad($lat1)) * sin(deg2rad($lat2)) +  cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta));
      $dist = acos($dist);
      $dist = rad2deg($dist);
      $miles = $dist * 60 * 1.1515;
      $unit = strtoupper($unit);

      if ($unit == "K") {
        return ($miles * 1.609344);
      } else if ($unit == "N") {
          return ($miles * 0.8684);
        } else {
            return $miles;
          }
    }

    public function accept(Request $request, Response $response)
    {
        try {
            $response->has_responded = 1;
            $response->saveOrFail();
            return HttpHelper::json([
                'message' => 'The response has been accepted by the respondant!',
            ], 200);
        } catch (Exception $e) {
            return HttpHelper::json([
                'message' => $e->getMessage(),
            ], 200);
        }
    }

    public function decline(Request $request , Response $response)
    {
        try {
            $alreadyCalledRespondants = Response::select('respondant_id')->where('user_id', $response->user_id)->get();
            $ids = [];
            foreach ($alreadyCalledRespondants as $key => $respondant) {
                array_push($ids, $respondant['respondant_id']);
            }
            $respondants = User::where('id', '!=', $ids)->where('is_patient', 0)->get();
            if (count($respondants) == 0) {
                throw new Exception('No respondant left! Calling the emergency');
            }
            $user = User::where('id', $response->user_id)->first();
            $closerRespondant['distance'] = $this->distance($user->latitude, 
                                                $user->longitude, 
                                                $respondants[0]->latitude, 
                                                $respondants[0]->longitude, 
                                                'K');
            $closerRespondant['respondant'] = $respondants[0];
            foreach ($respondants as $key => $respondant) {
                $distance = $this->distance($user->latitude,
                                    $user->longitude,
                                    $respondant->latitude,
                                    $respondant->longitude,
                                    'K');
                if ($respondant->latitude && 
                    $respondant->longitude &&
                    $distance < $closerRespondant['distance']) {
                    $closerRespondant['distance'] = $distance;
                    $closerRespondant['respondant'] = $respondant;
                }
            }
            $response = new Response();
            $response->user_id = $user->id;
            $response->respondant_id = $closerRespondant['respondant']['id'];
            $response->saveOrFail();
            event(new Repondant([
                'type' => 'DYING',
                'user' => $closerRespondant['respondant'],
                'payload' => [
                    'userId' => $response->user_id,
                    'distance_km' => $closerRespondant['distance']
                ]
            ]));
        } catch (Exception $e) {
            return HttpHelper::json(['message' => $e->getMessage()], 500);
        }
    }
}
