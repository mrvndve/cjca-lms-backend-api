<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Department_model extends CI_Model 
{
    function __construct()
    {
        parent::__construct();

        $this->load->database();

        $this->load->model('Audit_model', 'audit_model');
    }

    public static $check_code = null;

    public static $validation_type = null;

    public static $update_code_changed = false;

    public function fetch($params)
    {
        try
        {
            $this->db->select('*');

            $this->db->from('department');

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
                    'total' => $this->db->count_all_results('department'),
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
            self::$validation_type = 'insert';

            $code = (isset($posts['code']) && $posts['code']) ? $posts['code'] : '';

            self::$check_code = $this->db->query("SELECT EXISTS(SELECT `code` FROM department WHERE `code` = '$code') AS `exists`")->row();

            $validation_errors = $this->validate_form_fields($posts);

            if ($validation_errors == null)
            {
                $by_user_id = $posts['by_user_id'];

                unset($posts['by_user_id']);

                $insert_query = $this->db->set($posts)->insert('department');
    
                if ($insert_query)
                {
                    $message = 'Department has been added.';

                    $this->audit_model->log('Department', 'Add', $message, $by_user_id);

                    return array('status' => 1, 'message' => $message);
                }

                throw new Exception($this->db->error()['message']);
            }
            else
            {
                return array('status' => 0, 'message' => 'Add department failed.', 'validation_errors' => $validation_errors);
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
            self::$validation_type = 'update';

            $id = $posts['id'];

            $code = (isset($posts['code']) && $posts['code']) ? $posts['code'] : '';

            $row = $this->db->query("SELECT code from department WHERE id = '$id'")->row();

            if ($row->code !== $posts['code']) 
            {
                self::$update_code_changed = true;

                self::$check_code = $this->db->query("SELECT EXISTS(SELECT `code` FROM department WHERE `code` = '$code') AS `exists`")->row();
            }

            $validation_errors = $this->validate_form_fields($posts);

            if ($validation_errors == null)
            {
                $by_user_id = $posts['by_user_id'];

                unset($posts['by_user_id']);

                $update_query = $this->db->where('id', $id)->update('department', $posts);

                if ($update_query)
                {
                    $message = "Department has been updated.";

                    $this->audit_model->log('Department', 'Update', $message, $by_user_id);

                    return array('status' => 1, 'message' => $message);
                }

                throw new Exception($this->db->error()['message']);
            }
            else
            {
                return array('status' => 0, 'message' => 'Update department failed.', 'validation_errors' => $validation_errors);
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
                return array('status' => 0, 'message' => 'Delete department/s failed.');
            }

            $delete_query = $this->db->where_in('id', json_decode($posts['ids']))->delete('department');

            if ($delete_query)
            {
                $this->audit_model->log('Department', 'Delete', 'Department/s has been deleted.', $posts['by_user_id']);

                return array('status' => 1, 'message' => "Department/s has been deleted.");
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

        if (self::$validation_type == 'insert')
        {
            if (self::$check_code->exists)
            {
                $validation['code'] = 'code is already in use. please input a different code.';
            }
        } 
        else if (self::$validation_type == 'update') 
        {
            if (self::$update_code_changed)
            {
                if (self::$check_code->exists)
                {
                    $validation['code'] = 'code is already in use. please input a different code.';
                }
            }
        }

        if (!isset($posts['code']) || empty($posts['code']))
        {
            $validation['code'] = 'code field is required.';
        }

        if (!isset($posts['name']) || empty($posts['name']))
        {
            $validation['name'] = 'name field is required.';
        }

        if (!isset($posts['person_in_charge']) || empty($posts['person_in_charge']))
        {
            $validation['person_in_charge'] = 'person in charge field is required.';
        }

        return $validation;
    }
}