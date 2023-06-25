<?php

defined('BASEPATH') OR exit('No direct script access allowed');

require (APPPATH . 'libraries/REST_Controller.php');

require (APPPATH . 'libraries/ImplementJwt.php');

class Settings extends REST_Controller
{
    public function __construct()
    {
        parent::__construct();

        $this->load->model('Settings_model', 'settings_model');

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

    public function change_password_post()
    {
        $auth = $this->jwt->check_token($this->input->get_request_header('authorization'));

        $api_res = null;

        if ($auth['status'])
        {
            $posts = $this->input->post();

            $posts['updated_at'] = DATE_TIME;

            $api_res = $this->settings_model->change_password($posts);

            $api_status = $api_res['status'] ? 200 : 400;
        }
        else
        {
            $api_res = $auth;
            
            $api_status = 403;
        }
        
        $this->response($api_res, $api_status);
    }

    public function update_profile_post()
    {
        $auth = $this->jwt->check_token($this->input->get_request_header('authorization'));

        $api_res = null;

        if ($auth['status'])
        {
            $posts = $this->input->post();

            $posts['updated_at'] = DATE_TIME;

            $api_res = $this->settings_model->update_profile($posts);

            $api_status = $api_res['status'] ? 200 : 400;
        }
        else
        {
            $api_res = $auth;
            
            $api_status = 403;
        }
        
        $this->response($api_res, $api_status);
    }

    public function update_theme_post()
    {
        $auth = $this->jwt->check_token($this->input->get_request_header('authorization'));

        $api_res = null;

        if ($auth['status'])
        {
            $posts = $this->input->post();

            $posts['updated_at'] = DATE_TIME;

            $api_res = $this->settings_model->update_theme($posts);

            $api_status = $api_res['status'] ? 200 : 400;
        }
        else
        {
            $api_res = $auth;
            
            $api_status = 403;
        }
        
        $this->response($api_res, $api_status);
    }
    

    public function fetch_backup_get()
    {
        $auth = $this->jwt->check_token($this->input->get_request_header('authorization'));

        $api_res = null;

        if ($auth['status'])
        {
            $api_res = $this->settings_model->fetch_backup();

            $api_status = $api_res['status'] ? 200 : 400;
        }
        else
        {
            $api_res = $auth;
            
            $api_status = 403;
        }
        
        $this->response($api_res, $api_status);
    }

    public function create_backup_post()
    {
        $auth = $this->jwt->check_token($this->input->get_request_header('authorization'));

        $api_res = null;

        if ($auth['status'])
        { 
            $posts = $this->input->post();

            $posts['updated_at'] = DATE_TIME;

            $api_res = $this->settings_model->create_backup($posts);

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