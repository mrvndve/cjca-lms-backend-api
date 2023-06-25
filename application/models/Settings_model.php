<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Settings_model extends CI_Model 
{
    function __construct()
    {
        parent::__construct();

        $this->load->database();

        $this->load->model('Audit_model', 'audit_model');

        $this->load->model('Email_model', 'email_model');

        $this->load->model('File_handler_model', 'file_handler_model');
    }

    public function change_password($posts)
    {
        try
        {
            $id = $posts['id'];

            $old_password = $posts['old_password'];

            $new_password = $posts['new_password'];

            $confirm_password = $posts['confirm_password'];

            $query = $this->db->query("SELECT * from users WHERE id = '$id'");

            if ($query) 
            {
                $row = $query->row();

                if ($row->password === md5($old_password))
                {
                    if ($new_password === $confirm_password)
                    {
                        $data = array(
                            'password' => md5($new_password)
                        );

                        $update_password = $this->db->where('id', $id)->update('users', $data);

                        if ($update_password)
                        {
                            $message = 'Your password has been changed.';

                            $full_name = $row->full_name;

                            $this->email_model->send($row->email, 'CJCA Change Password', "Hi $full_name, your password has been changed from $old_password to $new_password.");

                            $this->audit_model->log('Settings', 'Change Password', $message, $posts['by_user_id']);

                            return array('status' => 1, 'message' => $message);
                        }

                        throw new Exception($this->db->error()['message']);
                    }
                    else
                    {
                        return array('status' => 0, 'message' => 'New password and Confirm password do not match.');
                    }
                }
                else
                {
                    return array('status' => 0, 'message' => 'Old password is incorrect.');
                }
            }
            
            throw new Exception($this->db->error()['message']);
        }
        catch (Exception $e)
        {
            return array('status' => 0, 'message' => $e->getMessage());
        }
    }

    public function update_profile($posts)
    {
        try
        {
            $validate_email = null;

            $check_email = null;

            $update_email_changed = false;

            $id = $posts['id'];

            $email = (isset($posts['email']) && $posts['email']) ? $posts['email'] : '';

            $row = $this->db->query("SELECT email from users WHERE id = '$id'")->row();

            if ($row->email !== $posts['email'])
            {
                $update_email_changed = true;
            }

            if ($update_email_changed)
            {
                $check_email = $this->db->query("SELECT EXISTS(SELECT `email` FROM users WHERE `email` = '$email') AS `exists`")->row();

                if ($check_email->exists)
                {
                    $validate_email = 'email is already in use, please input a different email.';
                }
            }

            if ($validate_email == null)
            {
                $by_user_id = $posts['by_user_id'];

                unset($posts['by_user_id']);

                $first_name = $posts['first_name'];

                $middle_name = $posts['middle_name'];

                $last_name = $posts['last_name'];

                $suffix = $posts['suffix'];

                $posts['full_name'] = "$first_name $middle_name $last_name $suffix";

                $posts['full_name'] = preg_replace('/\s+/', ' ', $posts['full_name']);

                if (isset($posts['base64']))
                {
                    $base64 = $posts['base64'];

                    $base64_type = $posts['base64_type'];
    
                    $image = $posts['file_name'];
    
                    $posts['image'] = $image;
    
                    unset($posts['base64']);
    
                    unset($posts['base64_type']);
    
                    unset($posts['file_name']);

                    $row = $this->db->query("SELECT image from users WHERE id = '$id'")->row();

                    if (!is_null($row->image))
                    {
                        if ($row->image !== $image)
                        {
                            $this->file_handler_model->delete_file($row->image);
                        }
                    }
    
                    $this->file_handler_model->upload_file($base64, $base64_type, $image);
                }

                $update = $this->db->where('id', $id)->update('users', $posts);

                if ($update)
                {
                    $user = $this->db->query("SELECT * FROM users where id = '$id'");

                    if ($user)
                    {
                        $data = $user->row();

                        $message = 'Your profile has been updated.';

                        $this->audit_model->log('Settings', 'Update Profile', $message, $by_user_id);
    
                        return array('status' => 1, 'data' => $data, 'message' => $message);
                    }

                    throw new Exception($this->db->error()['message']);
                }

                throw new Exception($this->db->error()['message']);
            }
            else
            {
                return array('status' => 0, 'message' => $validate_email);
            }
        }
        catch (Exception $e)
        {
            return array('status' => 0, 'message' => $e->getMessage());
        }
    }

    public function update_theme($posts)
    {
        try
        {
            $id = $posts['id'];

            $by_user_id = $posts['by_user_id'];

            unset($posts['by_user_id']);

            $update = $this->db->where('id', $id)->update('users', $posts);

            if ($update)
            {
                $user = $this->db->query("SELECT * FROM users where id = '$id'");

                if ($user)
                {
                    $data = $user->row();

                    $message = 'Your theme has been updated.';

                    $this->audit_model->log('Settings', 'Update Theme', $message, $by_user_id);

                    return array('status' => 1, 'data' => $data, 'message' => $message);
                }

                throw new Exception($this->db->error()['message']);
            }

            throw new Exception($this->db->error()['message']);
        }
        catch (Exception $e)
        {
            return array('status' => 0, 'message' => $e->getMessage());
        }
    }

    public function fetch_backup()
    {
        try
        {      
            $query = $this->db->get('db_backup');
    
            if ($query)
            {
                $results = $query->result_array();

                return array('status' => 1, 'data' => $results);
            }

            throw new Exception($this->db->error()['message']);
        }
        catch (Exception $e)
        {
            return array('status' => 0, 'message' => $e->getMessage());
        }
    }

    public function create_backup($posts)
    {
        try
        {
            $file_name = $posts['file_name'] . '.zip';

            date_default_timezone_set('Asia/Taipei');

            $date_now = date('Y-m-d');
        
            $this->load->dbutil();

            $backup = $this->dbutil->backup();

            $this->load->helper('file');

            write_file("backup/$date_now-$file_name", $backup);

            $data = array(
                'file_name' => "$date_now-$file_name",
            );
                
            $query = $this->db->set($data)->insert('db_backup');
    
            if ($query)
            {
                $this->audit_model->log('Settings', 'Backup Database', 'Create database backup successful.', $posts['by_user_id']);

                return array('status' => 1, 'message' => 'Create database backup successful.');
            }

            throw new Exception($this->db->error()['message']);
        }
        catch (Exception $e)
        {
            return array('status' => 0, 'message' => $e->getMessage());
        }
    }
}