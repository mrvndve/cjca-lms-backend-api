<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class User_model extends CI_Model 
{
    function __construct()
    {
        parent::__construct();

        $this->load->database();

        $this->load->model('Audit_model', 'audit_model');

        $this->load->model('Email_model', 'email_model');

        $this->load->model('File_handler_model', 'file_handler_model');
    }
    
    public static $check_email = null;

    public static $check_code = null;

    public static $update_code_changed = false;

    public static $update_email_changed = false;

    public static $validation_type = null;

    public function login($posts) 
    {
        try 
        {
            $email = $posts['email'];

            $password = md5($posts['password']);

            $message = '';

            $query = $this->db->query("SELECT * FROM users WHERE email = '$email' AND (`password` = '$password') AND (is_active = '1')");

            if ($query)
            {
                $row = $query->row();

                if ($row)
                {
                    if (!is_null($row->department_id))
                    {
                        $department_id = $row->department_id;
                        $row->department = $this->db->query("SELECT * FROM department WHERE id = '$department_id'")->row();
                    }

                    if (!is_null($row->section_id))
                    {
                        $section_id = $row->section_id;
                        $row->section = $this->db->query("SELECT * FROM section WHERE id = '$section_id'")->row();
                    }

                    $message = 'Login Successful.';

                    $this->audit_model->log('Login Form', 'Login', $message, $row->id);

                    return array('status' => 1, 'data' => $row);
                }
                else
                {
                    $message = 'Incorrect email or password please try again.';

                    return array('status' => 0, 'message' => $message);
                }
            }

            throw new Exception($this->db->error()['message']);
        }
        catch (Exception $e)
        {
            return array('status' => 0, 'message' => $e->getMessage());
        }
    }

    public function fetch($params)
    {
        try
        {
            $this->db->select('*');

            $this->db->from('users');

            if (isset($params['search']) && isset($params['search_by']))
            {

                if ($params['search_by'] !== '' && $params['search'] !== '')
                {
                    $this->db->like($params['search_by'], $params['search']);
                }
                
                // if ($params['size'] !== '' && $params['page'] !== '')
                // {
                //     $this->db->limit($params['size'], ($params['page'] - 1) * $params['size']);
                // }
            }

            if (isset($params['role']))
            {
                if ($params['role'] !== '')
                {
                    $this->db->where('role', strtoupper($params['role']));
                }
            }

            $user_query = $this->db->order_by('id', 'DESC')->get();

            if ($user_query)
            {
                $user_data = $user_query->result_array();

                for ($x = 0; $x < count($user_data); $x++)
                {
                    $ids = $user_data[$x]['department_id']; 

                    if (!is_null($ids)) 
                    {
                        $user_data[$x]['department'] = $this->db->query("SELECT * FROM department WHERE id = '$ids'")->row();
                    }
                }

                for ($x = 0; $x < count($user_data); $x++)
                {
                    $ids = $user_data[$x]['section_id']; 
                    
                    if (!is_null($ids))
                    {
                        $user_data[$x]['section'] = $this->db->query("SELECT * FROM section WHERE id = '$ids'")->row();
                    }
                }

                for ($x = 0; $x < count($user_data); $x++)
                {
                    $ids = $user_data[$x]['school_year_id']; 
                    
                    if (!is_null($ids))
                    {
                        $user_data[$x]['school_year'] = $this->db->query("SELECT * FROM school_year WHERE id = '$ids'")->row();
                    }
                }

                for ($x = 0; $x < count($user_data); $x++)
                {
                    $ids = $user_data[$x]['grade_level_id']; 
                    
                    if (!is_null($ids))
                    {
                        $user_data[$x]['grade_level'] = $this->db->query("SELECT * FROM grade_level WHERE id = '$ids'")->row();
                    }
                }

                for ($x = 0; $x < count($user_data); $x++)
                {
                    $ids = $user_data[$x]['strand_course_id']; 
                    
                    if (!is_null($ids))
                    {
                        $user_data[$x]['strand_course'] = $this->db->query("SELECT * FROM strand_course WHERE id = '$ids'")->row();
                    }
                }

                return array(
                    'status' => 1, 
                    'data' => $user_data, 
                    'total' => count($user_data),
                );
            }

            throw new Exception($this->db->error()['message']);
        }
        catch (Exception $e)
        {
            return array('status' => 0, 'message' => $e->getMessage());
        }
    }
    
    public function fetch_notifications($params)
    {
        try
        {
            $user_id = $params['user_id'];

            $query = $this->db->where('user_id', $user_id)->order_by('id', 'DESC')->get('notifications');
    
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
    
    public function update_notifications($posts)
    {
        try
        {
            $user_id = $posts['user_id'];

            $data = array(
                'is_read' => 1,
                'updated_at' => DATE_TIME,
            );

            $query = $this->db->where('user_id', $user_id)->update('notifications', $data);

            if ($query)
            {
                $result = $this->db->where('user_id', $user_id)->get('notifications')->result_array();

                return array('status' => 1, 'data' => $result);
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
            $insert_type = strtolower($posts['role']);

            $email = (isset($posts['email']) && $posts['email']) ? $posts['email'] : '';

            $code = (isset($posts['code']) && $posts['code']) ? $posts['code'] : '';
            
            self::$validation_type = 'insert';
            
            self::$check_email = $this->db->query("SELECT EXISTS(SELECT `email` FROM users WHERE `email` = '$email') AS `exists`")->row();

            self::$check_code = $this->db->query("SELECT EXISTS(SELECT `code` FROM users WHERE `code` = '$code') AS `exists`")->row();

            $validation_errors = $this->validate_form_fields($posts);

            if ($validation_errors == null)
            {
                $password = $posts['password'];
    
                $posts['password'] = md5($posts['password']);

                $first_name = $posts['first_name'];

                $middle_name = $posts['middle_name'];

                $last_name = $posts['last_name'];

                $suffix = $posts['suffix'];

                $posts['full_name'] = "$first_name $middle_name $last_name $suffix";

                $posts['full_name'] = preg_replace('/\s+/', ' ', $posts['full_name']);

                $full_name = $posts['full_name'];

                $by_user_id = $posts['by_user_id'];
                
                unset($posts['by_user_id']);

                if (isset($posts['base64']))
                {
                    $base64 = $posts['base64'];

                    $base64_type = $posts['base64_type'];
    
                    $image = $posts['file_name'];
    
                    $posts['image'] = $image;
    
                    unset($posts['base64']);
    
                    unset($posts['base64_type']);
    
                    unset($posts['file_name']);
    
                    $this->file_handler_model->upload_file($base64, $base64_type, $image);
                }
                
                $insert_query = $this->db->set($posts)->insert('users');

                $this->email_model->send($posts['email'], 'CJCA New User', "Hi! $full_name here is your password. $password");
    
                if ($insert_query)
                {
                    $message = $posts['email'] . " has been added to $insert_type, with a password of " .  $password;

                    $this->audit_model->log(ucfirst($insert_type), 'Insert', $message, $by_user_id);

                    return array('status' => 1, 'message' => $message);
                }

                throw new Exception($this->db->error()['message']);
            }
            else
            {
                return array('status' => 0, 'message' => "Add $insert_type failed.", 'validation_errors' => $validation_errors);
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

            $update_type = strtolower($posts['role']);

            $id = $posts['id'];

            $email = (isset($posts['email']) && $posts['email']) ? $posts['email'] : '';

            $code = (isset($posts['code']) && $posts['code']) ? $posts['code'] : '';

            $prev_data = $this->db->query("SELECT code, email, school_year_id from users WHERE id = '$id'")->row();

            if ($prev_data->code !== $posts['code']) 
            {
                self::$update_code_changed = true;

                self::$check_code = $this->db->query("SELECT EXISTS(SELECT `code` FROM users WHERE `code` = '$code') AS `exists`")->row();
            }

            if ($prev_data->email !== $posts['email'])
            {
                self::$update_email_changed = true;

                self::$check_email = $this->db->query("SELECT EXISTS(SELECT `email` FROM users WHERE `email` = '$email') AS `exists`")->row();
            }

            $validation_errors = $this->validate_form_fields($posts);

            if ($validation_errors == null)
            {
                $first_name = $posts['first_name'];

                $middle_name = $posts['middle_name'];

                $last_name = $posts['last_name'];

                $suffix = $posts['suffix'];

                $posts['full_name'] = "$first_name $middle_name $last_name $suffix";

                $posts['full_name'] = preg_replace('/\s+/', ' ', $posts['full_name']);

                $by_user_id = $posts['by_user_id'];

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

                unset($posts['by_user_id']);

                if ($update_type === 'student')
                {
                    $gl_id = $posts['grade_level_id'];

                    $grade_level = $this->db->query("SELECT * FROM grade_level WHERE id = '$gl_id'")->row();

                    if ($grade_level->is_senior_high == 0) 
                    {
                        $posts['semester'] = null;
                        $posts['strand_course_id'] = null;
                    }
                }

                if ($update_type === 'teacher')
                {
                    if ($prev_data->school_year_id != $posts['school_year_id'])
                    {
                        $subj_teachers_count = $this->db->where('user_id', $id)->count_all_results('subject_teachers');

                        if ($subj_teachers_count > 0)
                        {
                            $prev_subjs = $this->db->where('school_year_id', $prev_data->school_year_id)->get('subject')->result_array();

                            if (count($prev_subjs) > 0)
                            {
                                for ($x = 0; $x < count($prev_subjs); $x++)
                                {
                                    $this->db->where_in('subject_id', $prev_subjs[$x]['id'])->where('user_id', $id)->delete('subject_teachers');
                                }
                            }

                            $new_subjs = $this->db->where('school_year_id', $posts['school_year_id'])->get('subject')->result_array();

                            if (count($new_subjs) > 0)
                            {
                                for ($x = 0; $x < count($new_subjs); $x++)
                                {
                                    $data = array(
                                        'subject_id' => $new_subjs[$x]['id'],
                                        'user_id' => $id,
                                        'created_at' => DATE_TIME,
                                        'updated_at' => DATE_TIME,
                                    );

                                    $this->db->set($data)->insert('subject_teachers');
                                }
                            }
                        }
                    }
                }

                $update_query = $this->db->where('id', $posts['id'])->update('users', $posts);

                if ($update_query)
                {
                    $message = ucfirst($update_type) . " user has been updated.";

                    $this->audit_model->log(ucfirst($update_type), 'Update', $message, $by_user_id);

                    return array('status' => 1, 'message' => $message);
                }

                throw new Exception($this->db->error()['message']);
            }
            else
            {
                return array('status' => 0, 'message' => "Update $update_type failed.", 'validation_errors' => $validation_errors);
            }
        }
        catch (Exception $e)
        {
            return array('status' => 0, 'message' => $e->getMessage());
        }
    }

    public function active_inactive($posts)
    {
        try
        {
            $update_type = strtolower($posts['role']);

            if (!isset($posts['ids']))
            {
                return array('status' => 0, 'message' => ($posts['is_active'] ? "Activate " . "$update_type user/s failed." : "Deactivate " . "$update_type user/s failed."));
            }

            if (!isset($posts['is_active']))
            {
                return array('status' => 0, 'message' => ($posts['is_active'] ? "Activate " . "$update_type user/s failed." : "Deactivate " . "$update_type user/s failed."));
            }

            $data = array(
                'is_active' => $posts['is_active'],
                'updated_at' => DATE_TIME,
            );

            $active_query = $this->db->where_in('id', json_decode($posts['ids']))->update('users', $data);

            if ($active_query)
            {
                $is_active = ($posts['is_active'] ? 'activated' : 'deactivated');

                $message = ucfirst($update_type) . " user/s has been " . $is_active . '.';

                $this->audit_model->log(ucfirst($update_type), ucfirst($is_active), $message, $posts['by_user_id']);

                return array('status' => 1, 'message' => $message);
            }

            throw new Exception($this->db->error()['message']);
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
            $delete_type = strtolower($posts['role']);

            if (!isset($posts['ids']))
            {
                return array('status' => 0, 'message' => "Delete $delete_type/s failed.");
            }

            $ids = json_decode($posts['ids']);

            for ($x = 0; $x < count($ids); $x++) 
            {
                $idsx = $ids[$x];

                $row = $this->db->query("SELECT * FROM users where id = '$idsx'")->row();

                if (isset($row->image))
                {
                    $this->file_handler_model->delete_file($row->image);
                }
            }

            $delete_query = $this->db->where_in('id', $ids)->delete('users');

            if ($delete_query)
            {
                $this->audit_model->log(ucfirst($delete_type), 'Delete',  ucfirst($delete_type) . "/s has been deleted.", $posts['by_user_id']);

                return array('status' => 1, 'message' => ucfirst($delete_type) . " user/s has been deleted.");
            }

            throw new Exception($this->db->error()['message']);
        }
        catch (Exception $e)
        {
            return array('status' => 0, 'message' => $e->getMessage());
        }
    }

    public function reset_password($posts)
    {
        try
        {
            $reset_type = strtolower($posts['role']);

            if (!isset($posts['ids']))
            {
                return array('status' => 0, 'message' => "Reset $reset_type/s password Failed.");
            }

            $ids = json_decode($posts['ids']);

            $password = substr(sha1(mt_rand()), 17, 10);

            $users = $this->db->where_in('id', $ids)->get('users')->result_array();

            for ($x = 0; $x < count($users); $x++)
            {
                $full_name = $users[$x]['full_name'];

                $this->email_model->send($users[$x]['email'], 'CJCA Reset Password', "Hi! $full_name here is your new password. $password");
            }
            
            $data = array(
                'password' => md5($password),
                'updated_at' => DATE_TIME,
            );

            $reset_query = $this->db->where_in('id', $ids)->update('users', $data);

            if ($reset_query)
            {
                $message = ucfirst($reset_type) . "/s password has been updated.";

                $this->audit_model->log(ucfirst($reset_type), 'Reset Password', $message, $posts['by_user_id']);

                return array('status' => 1, 'message' => $message);
            }
        }
        catch (Exception $e)
        {
            return array('status' => 0, 'message' => $e->getMessage());
        }
    }

    public function forgot_password($posts)
    {
        try
        {
            $email = $posts['email'];

            $check_email = $this->db->query("SELECT EXISTS(SELECT `email` FROM users WHERE `email` = '$email') AS `exists`")->row();
    
            if ($check_email->exists)
            {
                $user = $this->db->query("SELECT * FROM users WHERE email = '$email'");
    
                if ($user)
                {
                    $row = $user->row();

                    $full_name = $row->full_name;

                    $password = substr(sha1(mt_rand()), 17, 10);

                    $send_email = $this->email_model->send($email, 'CJCA Forgot Password', "Hi! $full_name, here is your new password. $password");
    
                    if ($send_email)
                    {
                        $update_query = $this->db->where('id', $row->id)->update('users', array('password' => md5($password), 'updated_at' => DATE_TIME));

                        if ($update_query)
                        {
                            return array('status' => 1, 'message' => 'Your new password has been sent to your email.');
                        }
    
                        throw new Exception($this->db->error()['message']);
                    }
                    else
                    {
                        return array('status' => 0, 'message' => 'Send email failed, please try again.');
                    }
                }

                throw new Exception($this->db->error()['message']);
            }
            else
            {
                return array('status' => 0, 'message' => 'Email does not exists. please enter a valid email.');
            }
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
    
            if (self::$check_email->exists)
            {
                $validation['email'] = 'email is already in use, please input a different email.';
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

            if (self::$update_email_changed)
            {
                if (self::$check_email->exists)
                {
                    $validation['email'] = 'email is already in use, please input a different email.';
                }
            }
        }

        if (!isset($posts['code']) || empty($posts['code']))
        {
            $validation['code'] = 'code field is required.';
        }

        if (!isset($posts['email']) || empty($posts['email']))
        {
            $validation['email'] = 'email field is required.';
        }

        if (!isset($posts['first_name']) || empty($posts['first_name']))
        {
            $validation['first_name'] = 'first name date field is required.';
        }

        if (!isset($posts['last_name']) || empty($posts['last_name']))
        {
            $validation['last_name'] = 'last name field is required.';
        }

        // if (!isset($posts['birth_date']) || empty($posts['birth_date']))
        // {
        //     $validation['birth_date'] = 'birth date field is required.';
        // }

        // if (!isset($posts['birth_date']) || !preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/", $posts['birth_date'])) //YYYY-MM-DD
        // {
        //     $validation['birth_date'] = 'birth date format is incorrect.';
        // }

        if (!isset($posts['gender']) || empty($posts['gender']))
        {
            $validation['gender'] = 'gender field is required.';
        }

        if (!isset($posts['contact']) || !preg_match('/^[0-9]+$/', $posts['contact']))
        {
            $validation['contact'] = 'contact field must should contain numbers only.';
        }

        if (!isset($posts['contact']) || empty($posts['contact']))
        {
            $validation['contact'] = 'contact field is required.';
        }
        
        if (!isset($posts['address']) || empty($posts['address']))
        {
            $validation['address'] = 'address field is required.';
        }

        if (!isset($posts['role']) || empty($posts['role']))
        {
            $validation['role'] = 'role field is required.';
        }

        return $validation;
    }
}