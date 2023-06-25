<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require_once(APPPATH . '/helpers/jwt_helper.php');

class ImplementJwt 
{
    public function generate_token($data)
	{
        $jwt = new JWT();

        $token = $jwt->encode($data, SECRET_KEY, 'HS256');

        return $token;
	}

    public function decode_token($token)
	{
        try
        {
            $jwt = new JWT();

            $decoded_token = $jwt->decode($token, SECRET_KEY, 'HS256');
            
            $message = ['status' => 1, 'message' => 'Authorized', 'data' => $decoded_token];
        }
        catch (\Exception $e)
        {
            $message = ['status' => 0, 'message' => $e->getMessage()];
        }

        return $message;
    }
	
    public function check_token($auth)
    {
        $result = null;

        $token = str_replace('Bearer ', '', $auth);

        $result = $this->decode_token($token);

        return $result;
    }
}
