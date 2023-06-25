<?php

defined('BASEPATH') OR exit('No direct script access allowed');

require (APPPATH . 'libraries/REST_Controller.php');

require (APPPATH . 'libraries/ImplementJwt.php');

class Student extends REST_Controller
{
    public function __construct()
    {
        parent::__construct();

        $this->load->model('Student_model', 'student_model');

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

   public function fetch_data_get()
   {
       $auth = $this->jwt->check_token($this->input->get_request_header('authorization'));

       $api_res = null;

       if ($auth['status'])
       {
           $api_res = $this->student_model->fetch_data($this->input->get());

           $api_status = $api_res['status'] ? 200 : 400;
       }
       else
       {
           $api_res = $auth;
           
           $api_status = 403;
       }
       
       $this->response($api_res, $api_status);
   }

   public function submit_exam_post()
   {
       $auth = $this->jwt->check_token($this->input->get_request_header('authorization'));

       $api_res = null;

       if ($auth['status'])
       {
            $posts = $this->input->post();

            $posts['created_at'] = DATE_TIME;

            $posts['updated_at'] = DATE_TIME;

            $api_res = $this->student_model->submit_exam($posts);

            $api_status = $api_res['status'] ? 200 : 400;
       }
       else
       {
           $api_res = $auth;
           
           $api_status = 403;
       }
       
       $this->response($api_res, $api_status);
   }

   public function submit_quiz_post()
   {
       $auth = $this->jwt->check_token($this->input->get_request_header('authorization'));

       $api_res = null;

       if ($auth['status'])
       {
            $posts = $this->input->post();

            $posts['created_at'] = DATE_TIME;

            $posts['updated_at'] = DATE_TIME;

            $api_res = $this->student_model->submit_quiz($posts);

            $api_status = $api_res['status'] ? 200 : 400;
       }
       else
       {
           $api_res = $auth;
           
           $api_status = 403;
       }
       
       $this->response($api_res, $api_status);
   }

   public function submit_task_performance_post()
   {
        $auth = $this->jwt->check_token($this->input->get_request_header('authorization'));

        $api_res = null;

       if ($auth['status'])
       {
            $api_res = $this->student_model->submit_task_performance($this->input->post());

            $api_status = $api_res['status'] ? 200 : 400;
       }
       else
       {
           $api_res = $auth;
           
           $api_status = 403;
       }
       
       $this->response($api_res, $api_status);
   }

   public function submit_assignment_post()
   {
        $auth = $this->jwt->check_token($this->input->get_request_header('authorization'));

        $api_res = null;

        if ($auth['status'])
        {
            $api_res = $this->student_model->submit_assignment($this->input->post());

            $api_status = $api_res['status'] ? 200 : 400;
        }
        else
        {
            $api_res = $auth;
            
            $api_status = 403;
        }
        
        $this->response($api_res, $api_status);
    }

    public function fetch_scores_get()
    {
        $auth = $this->jwt->check_token($this->input->get_request_header('authorization'));

        $api_res = null;

        if ($auth['status'])
        {
            $api_res = $this->student_model->fetch_scores($this->input->get());

            $api_status = $api_res['status'] ? 200 : 400;
        }
        else
        {
            $api_res = $auth;
            
            $api_status = 403;
        }
        
        $this->response($api_res, $api_status);
    }

    public function set_score_post()
    {
        $auth = $this->jwt->check_token($this->input->get_request_header('authorization'));

        $api_res = null;

        if ($auth['status'])
        {
            $api_res = $this->student_model->set_score($this->input->post());

            $api_status = $api_res['status'] ? 200 : 400;
        }
        else
        {
            $api_res = $auth;
            
            $api_status = 403;
        }
        
        $this->response($api_res, $api_status);
    }

    public function retake_post()
    {
        $auth = $this->jwt->check_token($this->input->get_request_header('authorization'));

        $api_res = null;

        if ($auth['status'])
        {
            $api_res = $this->student_model->retake($this->input->post());

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