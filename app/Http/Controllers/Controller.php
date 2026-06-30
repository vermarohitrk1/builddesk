<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

abstract class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

    /**
     * Standardized AJAX Response
     */
    protected function ajaxResponse($status, $message = '', $data = [], $httpCode = 200)
    {        
        $result = [
            'status' => $status,
            'message' => $message,
            'data' => $data,
            'timestamp' => time()
        ];
        
        return response()->json($result, $httpCode);
    }


    /**
     * Validation response
     * @param object $validator Validation
     * @access protected
     * @return json
     */
    protected function validationResponse($validator)
    {
        //errors
        if ($validator->fails()) {
            $errors = $validator->errors();

            $data['errors'] = $errors->all();
            
            $result = [
                'status' => 'error',
                'message' => 'Validation Failed',
                'data' => $data,
                'timestamp' => time()
            ];
            
            return response()->json($result, 409);
        }
    }

}
