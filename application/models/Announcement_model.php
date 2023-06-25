<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Announcement_model extends CI_Model 
{
    function __construct()
    {
        parent::__construct();

        $this->load->database();

        $this->load->model('File_handler_model', 'file_handler_model');

        $this->load->model('Audit_model', 'audit_model');
    }

    public function fetch($params)
    {
        try
        {
            $this->db->select('*');

            $this->db->from('announcement');

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

                for ($x = 0; $x < count($result); $x++) {
                    $author_ids = $result[$x]['author_id'];

                    $result[$x]['author'] = $this->db->query("SELECT id, full_name, image FROM users WHERE id = '$author_ids'")->row();
                }

                return array(
                    'status' => 1, 
                    'data' => $result, 
                    'total' => $this->db->count_all_results('announcement'),
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

                $insert_query = $this->db->set($posts)->insert('announcement');

                $insert_query = true;
    
                if ($insert_query)
                {
                    $message = 'Announcement has been added.';

                    $students = $this->db->query("SELECT * FROM users WHERE role = 'STUDENT'")->result_array();

                    for ($x = 0; $x < count($students); $x++) {
                        $notif_post = array(
                            'user_id' => $students[$x]['id'],
                            'type' => 'announcement',
                            'title' => $posts['title'],
                            'message' => $posts['content'],
                            'created_at' => DATE_TIME,
                            'updated_at' => DATE_TIME,
                        );

                        $this->db->set($notif_post)->insert('notifications');
                    }

                    $this->audit_model->log('Announcement', 'Add', $message, $by_user_id);

                    return array('status' => 1, 'message' => $message);
                }

                throw new Exception($this->db->error()['message']);
            }
            else
            {
                return array('status' => 0, 'message' => 'Add announcement failed.', 'validation_errors' => $validation_errors);
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
            $id = $posts['id'];

            $validation_errors = $this->validate_form_fields($posts);

            if ($validation_errors == null)
            {
                $by_user_id = $posts['by_user_id'];

                unset($posts['by_user_id']);

                $update_query = $this->db->where('id', $id)->update('announcement', $posts);

                if ($update_query)
                {
                    $message = "Announcement has been updated.";

                    $this->audit_model->log('Announcement', 'Update', $message, $by_user_id);

                    return array('status' => 1, 'message' => $message);
                }

                throw new Exception($this->db->error()['message']);
            }
            else
            {
                return array('status' => 0, 'message' => 'Update announcement failed.', 'validation_errors' => $validation_errors);
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
                return array('status' => 0, 'message' => 'Delete announcement/s failed.');
            }

            $delete_query = $this->db->where_in('id', json_decode($posts['ids']))->delete('announcement');

            if ($delete_query)
            {
                $this->audit_model->log('Announcement', 'Delete', 'Announcement/s has been deleted.', $posts['by_user_id']);

                return array('status' => 1, 'message' => "Announcement/s has been deleted.");
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

        if (!isset($posts['title']) || empty($posts['title']))
        {
            $validation['title'] = 'title field is required.';
        }

        if (!isset($posts['author_id']) || empty($posts['author_id']))
        {
            $validation['author_id'] = 'author field is required.';
        }

        if (!isset($posts['content']) || empty($posts['content']))
        {
            $validation['content'] = 'content field is required.';
        }

        return $validation;
    }
}