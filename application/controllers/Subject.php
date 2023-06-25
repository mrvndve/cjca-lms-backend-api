<?php

defined('BASEPATH') OR exit('No direct script access allowed');

require (APPPATH . 'libraries/REST_Controller.php');

require (APPPATH . 'libraries/ImplementJwt.php');

class Subject extends REST_Controller
{
    public function __construct()
    {
        parent::__construct();

        $this->load->model('Subject_model', 'subject_model');

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
            $api_res = $this->subject_model->fetch($this->input->get());
        }
        else
        {
            $api_res = $auth;

            $api_status = 403;
        }
        
        $this->response($api_res, $api_status);
    }

    public function fetch_teachers_get()
    {
        $auth = $this->jwt->check_token($this->input->get_request_header('authorization'));

        $api_res = null;

        $api_status = null;

        if ($auth['status'])
        {
            $api_res = $this->subject_model->fetch_teachers($this->input->get());
        }
        else
        {
            $api_res = $auth;

            $api_status = 403;
        }
        
        $this->response($api_res, $api_status);
    }

    public function fetch_teacher_subjects_get()
    {
        $auth = $this->jwt->check_token($this->input->get_request_header('authorization'));

        $api_res = null;

        $api_status = null;

        if ($auth['status'])
        {
            $api_res = $this->subject_model->fetch_teacher_subjects($this->input->get());
        }
        else
        {
            $api_res = $auth;

            $api_status = 403;
        }
        
        $this->response($api_res, $api_status);
    }

    public function fetch_subject_details_get()
    {
        $auth = $this->jwt->check_token($this->input->get_request_header('authorization'));

        $api_res = null;

        $api_status = null;

        if ($auth['status'])
        {
            $api_res = $this->subject_model->fetch_subject_details($this->input->get());
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

            $api_res = $this->subject_model->insert($posts);

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

            $api_res = $this->subject_model->update($posts);

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
            $api_res = $this->subject_model->delete($this->input->post());

            $api_status = $api_res['status'] ? 200 : 400;
        }
        else
        {
            $api_res = $auth;
            $api_status = 403;
        }
        
        $this->response($api_res, $api_status);
    }

    public function add_lesson_post()
    {
        $auth = $this->jwt->check_token($this->input->get_request_header('authorization'));

        $api_res = null;

        $api_status = 200;

        if ($auth['status'])
        {
            $posts = $this->input->post();

            $posts['created_at'] = DATE_TIME;

            $posts['updated_at'] = DATE_TIME;

            $api_res = $this->subject_model->add_lesson($posts);

            $api_status = $api_res['status'] ? 200 : 400;
        }
        else
        {
            $api_res = $auth;

            $api_status = 403;
        }
        
        $this->response($api_res, $api_status);
    }

    public function delete_lesson_post()
    {
        $auth = $this->jwt->check_token($this->input->get_request_header('authorization'));

        $api_res = null;

        if ($auth['status'])
        {
            $api_res = $this->subject_model->delete_lesson($this->input->post());

            $api_status = $api_res['status'] ? 200 : 400;
        }
        else
        {
            $api_res = $auth;
            $api_status = 403;
        }
        
        $this->response($api_res, $api_status);
    }

    public function add_assignment_post()
    {
        $auth = $this->jwt->check_token($this->input->get_request_header('authorization'));

        $api_res = null;

        $api_status = 200;

        if ($auth['status'])
        {
            $posts = $this->input->post();

            $posts['created_at'] = DATE_TIME;

            $posts['updated_at'] = DATE_TIME;

            $api_res = $this->subject_model->add_assignment($posts);

            $api_status = $api_res['status'] ? 200 : 400;
        }
        else
        {
            $api_res = $auth;

            $api_status = 403;
        }
        
        $this->response($api_res, $api_status);
    }

    public function delete_assignment_post()
    {
        $auth = $this->jwt->check_token($this->input->get_request_header('authorization'));

        $api_res = null;

        if ($auth['status'])
        {
            $api_res = $this->subject_model->delete_assignment($this->input->post());

            $api_status = $api_res['status'] ? 200 : 400;
        }
        else
        {
            $api_res = $auth;
            $api_status = 403;
        }
        
        $this->response($api_res, $api_status);
    }

    public function add_teacher_post()
    {
        $auth = $this->jwt->check_token($this->input->get_request_header('authorization'));

        $api_res = null;

        $api_status = 200;

        if ($auth['status'])
        {
            $posts = $this->input->post();

            $posts['created_at'] = DATE_TIME;

            $posts['updated_at'] = DATE_TIME;

            $api_res = $this->subject_model->add_teacher($posts);

            $api_status = $api_res['status'] ? 200 : 400;
        }
        else
        {
            $api_res = $auth;

            $api_status = 403;
        }
        
        $this->response($api_res, $api_status);
    }

    public function delete_teacher_post()
    {
        $auth = $this->jwt->check_token($this->input->get_request_header('authorization'));

        $api_res = null;

        if ($auth['status'])
        {
            $api_res = $this->subject_model->delete_teacher($this->input->post());

            $api_status = $api_res['status'] ? 200 : 400;
        }
        else
        {
            $api_res = $auth;
            $api_status = 403;
        }
        
        $this->response($api_res, $api_status);
    }

    public function add_exam_post()
    {
        $auth = $this->jwt->check_token($this->input->get_request_header('authorization'));

        $api_res = null;

        $api_status = 200;

        if ($auth['status'])
        {
            $posts = $this->input->post();

            $posts['created_at'] = DATE_TIME;

            $posts['updated_at'] = DATE_TIME;

            $api_res = $this->subject_model->add_exam($posts);

            $api_status = $api_res['status'] ? 200 : 400;
        }
        else
        {
            $api_res = $auth;

            $api_status = 403;
        }
        
        $this->response($api_res, $api_status);
    }

    public function delete_exam_post()
    {
        $auth = $this->jwt->check_token($this->input->get_request_header('authorization'));

        $api_res = null;

        if ($auth['status'])
        {
            $api_res = $this->subject_model->delete_exam($this->input->post());

            $api_status = $api_res['status'] ? 200 : 400;
        }
        else
        {
            $api_res = $auth;
            $api_status = 403;
        }
        
        $this->response($api_res, $api_status);
    }

    public function add_quiz_post()
    {
        $auth = $this->jwt->check_token($this->input->get_request_header('authorization'));

        $api_res = null;

        $api_status = 200;

        if ($auth['status'])
        {
            $posts = $this->input->post();

            $posts['created_at'] = DATE_TIME;

            $posts['updated_at'] = DATE_TIME;

            $api_res = $this->subject_model->add_quiz($posts);

            $api_status = $api_res['status'] ? 200 : 400;
        }
        else
        {
            $api_res = $auth;

            $api_status = 403;
        }
        
        $this->response($api_res, $api_status);
    }

    public function delete_quiz_post()
    {
        $auth = $this->jwt->check_token($this->input->get_request_header('authorization'));

        $api_res = null;

        if ($auth['status'])
        {
            $api_res = $this->subject_model->delete_quiz($this->input->post());

            $api_status = $api_res['status'] ? 200 : 400;
        }
        else
        {
            $api_res = $auth;
            $api_status = 403;
        }
        
        $this->response($api_res, $api_status);
    }

    public function add_task_performance_post()
    {
        $auth = $this->jwt->check_token($this->input->get_request_header('authorization'));

        $api_res = null;

        $api_status = 200;

        if ($auth['status'])
        {
            $posts = $this->input->post();

            $posts['created_at'] = DATE_TIME;

            $posts['updated_at'] = DATE_TIME;

            $api_res = $this->subject_model->add_task_performance($posts);

            $api_status = $api_res['status'] ? 200 : 400;
        }
        else
        {
            $api_res = $auth;

            $api_status = 403;
        }
        
        $this->response($api_res, $api_status);
    }

    public function delete_task_performance_post()
    {
        $auth = $this->jwt->check_token($this->input->get_request_header('authorization'));

        $api_res = null;

        if ($auth['status'])
        {
            $api_res = $this->subject_model->delete_task_performance($this->input->post());

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