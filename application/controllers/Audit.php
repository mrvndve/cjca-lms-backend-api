<?php

defined('BASEPATH') OR exit('No direct script access allowed');

require (APPPATH . 'libraries/REST_Controller.php');

require (APPPATH . 'libraries/ImplementJwt.php');

class Audit extends REST_Controller
{
    public function __construct()
    {
        parent::__construct();

        $this->load->model('Audit_model', 'audit_model');

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

    public function index_get()
    {
    }

    public function fetch_get()
    {
        $auth = $this->jwt->check_token($this->input->get_request_header('authorization'));

        $api_res = null;

        $api_status = null;

        if ($auth['status'])
        {
            $api_res = $this->audit_model->fetch($this->input->get());
        }
        else
        {
            $api_res = $auth;

            $api_status = 403;
        }
        
        $this->response($api_res, $api_status);
    }
}