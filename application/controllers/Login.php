<?php

defined('BASEPATH') OR exit('No direct script access allowed');

require (APPPATH . 'libraries/REST_Controller.php');

require (APPPATH . 'libraries/ImplementJwt.php');

class Login extends REST_Controller
{
    public function __construct()
    {
        parent::__construct();

        $this->load->model('User_model', 'user_model');

        $this->jwt = new ImplementJwt();

        header('Access-Control-Allow-Origin: *');

        header('Access-Control-Allow-Methods: HEAD, GET, POST, PUT, PATCH, DELETE, OPTIONS');

        header('Access-Control-Allow-Headers: X-API-KEY, Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method, Access-Control-Request-Headers, Authorization');

        header('Content-Type: application/json');

        $method = $_SERVER['REQUEST_METHOD'];

        if ($method == 'OPTIONS') 
        {
            header('Access-Control-Allow-Origin: *');

            header("Access-Control-Allow-Headers: X-API-KEY, Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method,Access-Control-Request-Headers, Authorization");

            header("HTTP/1.1 200 OK");

            die();
        }
    }

    public function index_post()
    {
        $api_res = null;

        $api_status = null;

        $email = $this->input->post('email');

        $password = $this->input->post('password');

        if ($email && $password)
        {
            $posts = $this->input->post();

            $model = $this->user_model->login($posts);

            if ($model['status']) 
            {
                $model['data']->start = time();

                $model['data']->expire = $model['data']->start + (30 * 60);

                $token = $this->jwt->generate_token($model['data']);

                $model['token'] = $token;
            }

            $api_res = $model;

            $api_status = $api_res['status'] ? 200 : 400;
        }
        else
        {
            $api_res = array('status' => 0, 'message' => 'Incorrect body parameters');
            $api_status = 400;
        }

        $this->response($api_res, $api_status);
    }
}