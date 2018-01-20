<?php

namespace App\Algorithms\Helpers;

use Response;

class HttpHelper
{
    /**
     * Return a new JSON response from the application.
     *
     * @param string|array $data
     * @param int $status
     * @param array $headers
     * @param int $options
     * @return \Symfony\Component\HttpFoundation\Response 
     * @static 
     */
     public static function json($data = array(), $status = 200, $headers = array(), $options = 0){
        return Response::json($data, $status, $headers, $options);
     }
}
