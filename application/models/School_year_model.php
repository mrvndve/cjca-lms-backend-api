<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class School_year_model extends CI_Model 
{
    function __construct()
    {
        parent::__construct();

        $this->load->database();

        $this->load->model('Audit_model', 'audit_model');
    }

    public function fetch($params)
    {
        try
        {
            $this->db->select('*');

            $this->db->from('school_year');

            if (isset($params['search']) && isset($params['search_by']) && isset($params['size']) && isset($params['page']))
            {
                if ($params['search_by'] !== '' && $params['search'] !== '')
                {
                    $this->db->like($params['search_by'], $params['search']);
                }
    
                if ($params['size'] !== '' && $params['page'] !== '')
                {
                    $this->db->limit($params['size'], ($params['page'] - 1) * $params['size']);
                }
            }

            $query = $this->db->order_by('id', 'DESC')->get();

            if ($query)
            {
                $result = $query->result_array();

                return array(
                    'status' => 1, 
                    'data' => $result, 
                    'total' => $this->db->count_all_results('school_year'),
                );
            }

            throw new Exception($this->db->error()['message']);
        }
        catch (Exception $e)
        {
            return array('status' => 0, 'message' => $e->getMessage());
        }
    }

    public function insert($posts) 
    {
        try
        {
            $validation_errors = $this->validate_form_fields($posts);

            if ($validation_errors == null)
            {
                $by_user_id = $posts['by_user_id'];

                unset($posts['by_user_id']);

                $insert_query = $this->db->set($posts)->insert('school_year');
    
                if ($insert_query)
                {
                    $message = 'School year has been added.';

                    $this->audit_model->log('School Year', 'Add', $message, $by_user_id);

                    return array('status' => 1, 'message' => $message);
                }

                throw new Exception($this->db->error()['message']);
            }
            else
            {
                return array('status' => 0, 'message' => 'Add school year failed.', 'validation_errors' => $validation_errors);
            }

            throw new Exception($this->db->error()['message']);
        }
        catch (Exception $e)
        {
            return array('status' => 0, 'message' => $e->getMessage());
        }
    }

    public function update($posts)
    {
        try
        {
            $validation_errors = $this->validate_form_fields($posts);

            if ($validation_errors == null)
            {
                $by_user_id = $posts['by_user_id'];

                unset($posts['by_user_id']);

                $update_query = $this->db->where('id', $posts['id'])->update('school_year', $posts);

                if ($update_query)
                {
                    $message = "School year has been updated.";

                    $this->audit_model->log('School Year', 'Update', $message, $by_user_id);

                    return array('status' => 1, 'message' => $message);
                }

                throw new Exception($this->db->error()['message']);
            }
            else
            {
                return array('status' => 0, 'message' => 'Update school year failed.', 'validation_errors' => $validation_errors);
            }
        }
        catch (Exception $e)
        {
            return array('status' => 0, 'message' => $e->getMessage());
        }
    }

    public function delete($posts)
    {
        try
        {
            if (!isset($posts['ids']))
            {
                return array('status' => 0, 'message' => 'Delete school year/s failed.');
            }

            $delete_query = $this->db->where_in('id', json_decode($posts['ids']))->delete('school_year');

            if ($delete_query)
            {
                $this->audit_model->log('School Year', 'Delete', 'School Year/s has been deleted.', $posts['by_user_id']);

                return array('status' => 1, 'message' => "School Year/s has been deleted.");
            }

            throw new Exception($this->db->error()['message']);
        }
        catch (Exception $e)
        {
            return array('status' => 0, 'message' => $e->getMessage());
        }
    }

    public function validate_form_fields($posts)
    {
        $validation = null;

        if (!isset($posts['name']) || empty($posts['name']))
        {
            $validation['name'] = 'name field is required.';
        }

        return $validation;
    }
}