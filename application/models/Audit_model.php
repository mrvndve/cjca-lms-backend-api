<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Audit_model extends CI_Model {
    function __construct()
    {
        $this->load->database();

        parent::__construct();
    }

    public function log($module = '', $activity = '', $message = '', $by_user_id = '')
    {
        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } else {
            $ip = $_SERVER['REMOTE_ADDR'];
        }

        $data = array(
            'module' => $module,
            'user_id' => $by_user_id,
            'activity' => $activity,
            'message' => $message,
            'ip_address' => $ip,
            'created_at' => DATE_TIME,
            'updated_at' => DATE_TIME,
        );

        $this->db->set($data)->insert('audit');
    }

    public function fetch()
    {
        try
        {
            $queryString = "SELECT 
                audit.id, 
                audit.user_id, 
                audit.module, 
                audit.activity, 
                audit.message,
                audit.ip_address,
                users.full_name,
                users.role
                FROM audit LEFT JOIN users ON audit.user_id = users.id ORDER BY audit.id DESC";

            $query = $this->db->query($queryString);

            if ($query)
            {
                $result = $query->result_array();

                return array('status' => 1, 'data' => $result);
            }

            throw new Exception($this->db->error()['message']);
        }
        catch (Exception $e)
        {
            return array('status' => 0, 'message' => $e->getMessage());
        }
    }
}