<?php

defined('BASEPATH') OR exit('No direct script access allowed');

require (APPPATH . 'libraries/REST_Controller.php');

require (APPPATH . 'libraries/ImplementJwt.php');

class Users extends REST_Controller
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
            $api_res = $this->user_model->fetch($this->input->get());
        }
        else
        {
            $api_res = $auth;

            $api_status = 403;
        }
        
        $this->response($api_res, $api_status);
    }

    public function insert_post()
    {
        $auth = $this->jwt->check_token($this->input->get_request_header('authorization'));

        $api_res = null;

        $api_status = 200;

        if ($auth['status'])
        {
            $posts = $this->input->post();

            $posts['created_at'] = DATE_TIME;

            $posts['updated_at'] = DATE_TIME;

            $api_res = $this->user_model->insert($posts);

            $api_status = $api_res['status'] ? 200 : 400;
        }
        else
        {
            $api_res = $auth;

            $api_status = 403;
        }
        
        $this->response($api_res, $api_status);
    }

    public function update_post()
    {
        $auth = $this->jwt->check_token($this->input->get_request_header('authorization'));

        $api_res = null;

        if ($auth['status'])
        {
            $posts = $this->input->post();

            $posts['updated_at'] = DATE_TIME;

            $api_res = $this->user_model->update($posts);

            $api_status = $api_res['status'] ? 200 : 400;
        }
        else
        {
            $api_res = $auth;
            
            $api_status = 403;
        }
        
        $this->response($api_res, $api_status);
    }

    public function activate_deactive_post()
    {
        $auth = $this->jwt->check_token($this->input->get_request_header('authorization'));

        $api_res = null;

        if ($auth['status'])
        {
            $api_res = $this->user_model->active_inactive($this->input->post());

            $api_status = $api_res['status'] ? 200 : 400;
        }
        else
        {
            $api_res = $auth;
            $api_status = 403;
        }
        
        $this->response($api_res, $api_status);
    }

    public function reset_password_post()
    {
        $auth = $this->jwt->check_token($this->input->get_request_header('authorization'));

        $api_res = null;

        if ($auth['status'])
        {
            $api_res = $this->user_model->reset_password($this->input->post());

            $api_status = $api_res['status'] ? 200 : 400;
        }
        else
        {
            $api_res = $auth;
            $api_status = 403;
        }
        
        $this->response($api_res, $api_status);
    }

    public function delete_post()
    {
        $auth = $this->jwt->check_token($this->input->get_request_header('authorization'));

        $api_res = null;

        if ($auth['status'])
        {
            $api_res = $this->user_model->delete($this->input->post());

            $api_status = $api_res['status'] ? 200 : 400;
        }
        else
        {
            $api_res = $auth;
            $api_status = 403;
        }
        
        $this->response($api_res, $api_status);
    }

    public function forgot_password_post()
    {
        $api_res = $this->user_model->forgot_password($this->input->post());

        $api_status = $api_res['status'] ? 200 : 400;
        
        $this->response($api_res, $api_status);
    }

    public function fetch_notifications_get()
    {
        $auth = $this->jwt->check_token($this->input->get_request_header('authorization'));

        $api_res = null;

        $api_status = null;

        if ($auth['status'])
        {
            $api_res = $this->user_model->fetch_notifications($this->input->get());
        }
        else
        {
            $api_res = $auth;

            $api_status = 403;
        }
        
        $this->response($api_res, $api_status);
    }

    public function update_notifications_post()
    {
        $auth = $this->jwt->check_token($this->input->get_request_header('authorization'));

        $api_res = null;

        if ($auth['status'])
        {
            $api_res = $this->user_model->update_notifications($this->input->post());

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