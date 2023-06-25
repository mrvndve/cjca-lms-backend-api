<?php

defined('BASEPATH') OR exit('No direct script access allowed');

require (APPPATH . 'libraries/REST_Controller.php');

require (APPPATH . 'libraries/ImplementJwt.php');

class Dashboard extends REST_Controller
{
    public function __construct()
    {
        parent::__construct();

        $this->load->model('Dashboard_model', 'dashboard_model');

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

    public function fetch_admin_dashboard_get()
    {
        $auth = $this->jwt->check_token($this->input->get_request_header('authorization'));

        $api_res = null;

        if ($auth['status'])
        {
            $api_res = $this->dashboard_model->fetch_admin_dashboard();

            $api_status = $api_res['status'] ? 200 : 400;
        }
        else
        {
            $api_res = $auth;
            
            $api_status = 403;
        }
        
        $this->response($api_res, $api_status);
    }

    public function fetch_teacher_dashboard_get()
    {
        $auth = $this->jwt->check_token($this->input->get_request_header('authorization'));

        $api_res = null;

        if ($auth['status'])
        {
            $api_res = $this->dashboard_model->fetch_teacher_dashboard($this->input->get());

            $api_status = $api_res['status'] ? 200 : 400;
        }
        else
        {
            $api_res = $auth;
            
            $api_status = 403;
        }
        
        $this->response($api_res, $api_status);
    }

    public function fetch_student_dashboard_get()
    {
        $auth = $this->jwt->check_token($this->input->get_request_header('authorization'));

        $api_res = null;

        if ($auth['status'])
        {
            $api_res = $this->dashboard_model->fetch_student_dashboard($this->input->get());

            $api_status = $api_res['status'] ? 200 : 400;
        }
        else
        {
            $api_res = $auth;
            
            $api_status = 403;
        }
        
        $this->response($api_res, $api_status);
    }
}