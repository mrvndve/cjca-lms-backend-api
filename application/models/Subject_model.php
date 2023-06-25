<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Subject_model extends CI_Model 
{
    function __construct()
    {
        parent::__construct();

        $this->load->database();

        $this->load->model('Audit_model', 'audit_model');

        $this->load->model('File_handler_model', 'file_handler_model');
    }

    public static $check_code = null;

    public static $validation_type = null;

    public static $update_code_changed = false;

    public function fetch($params)
    {
        try
        {
            $this->db->select('*');

            $this->db->from('subject');
            
            if (isset($params['role']))
            {
                if ($params['role'] === 'TEACHER')
                {
                    $user_id = $params['id'];

                    $arr = [];

                    $assigned_subjects = $this->db->query("SELECT * FROM subject_teachers WHERE `user_id` = '$user_id'")->result_array();

                    for ($x = 0; $x < count($assigned_subjects); $x++)
                    {
                       $arr[] = $assigned_subjects[$x]['subject_id']; 
                    }

                    if (count($arr) > 0)
                    {
                        $this->db->where_in('id', $arr);
                    }
                }
            }

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

                for ($x = 0; $x < count($result); $x++)
                {
                    $ids = $result[$x]['school_year_id']; 
                    
                    if (!is_null($ids))
                    {
                        $result[$x]['school_year'] = $this->db->query("SELECT * FROM school_year WHERE id = '$ids'")->row();
                    }
                }

                for ($x = 0; $x < count($result); $x++)
                {
                    $ids = $result[$x]['grade_level_id']; 
                    
                    if (!is_null($ids))
                    {
                        $result[$x]['grade_level'] = $this->db->query("SELECT * FROM grade_level WHERE id = '$ids'")->row();
                    }
                }

                for ($x = 0; $x < count($result); $x++)
                {
                    $ids = $result[$x]['strand_course_id']; 
                    
                    if (!is_null($ids))
                    {
                        $result[$x]['strand_course'] = $this->db->query("SELECT * FROM strand_course WHERE id = '$ids'")->row();
                    }
                }

                for ($x = 0; $x < count($result); $x++)
                {
                    $ids = $result[$x]['section_id']; 
                    
                    if (!is_null($ids))
                    {
                        $result[$x]['section'] = $this->db->query("SELECT * FROM section WHERE id = '$ids'")->row();
                    }
                }

                return array(
                    'status' => 1, 
                    'data' => $result, 
                    'total' => $this->db->count_all_results('subject'),
                );
            }

            throw new Exception($this->db->error()['message']);
        }
        catch (Exception $e)
        {
            return array('status' => 0, 'message' => $e->getMessage());
        }
    }

    public function fetch_teacher_subjects($params)
    {
        try
        {
            $user_id = $params['user_id'];

            $subj_ids = [];

            $assigned_subjects = $this->db->query("SELECT * FROM subject_teachers WHERE `user_id` = '$user_id'")->result_array();

            for ($x = 0; $x < count($assigned_subjects); $x++)
            {
               $subj_ids[] = $assigned_subjects[$x]['subject_id']; 
            }

            $result = [];

            if (count($subj_ids) > 0)
            {
                $query = $this->db->where_in('id', $subj_ids)->order_by('id', 'DESC')->get('subject');

                if ($query)
                {
                    $result = $query->result_array();

                    for ($x = 0; $x < count($result); $x++)
                    {
                        $ids = $result[$x]['school_year_id']; 
                        
                        if (!is_null($ids))
                        {
                            $result[$x]['school_year'] = $this->db->query("SELECT * FROM school_year WHERE id = '$ids'")->row();
                        }
                    }
    
                    for ($x = 0; $x < count($result); $x++)
                    {
                        $ids = $result[$x]['grade_level_id']; 
                        
                        if (!is_null($ids))
                        {
                            $result[$x]['grade_level'] = $this->db->query("SELECT * FROM grade_level WHERE id = '$ids'")->row();
                        }
                    }
    
                    for ($x = 0; $x < count($result); $x++)
                    {
                        $ids = $result[$x]['strand_course_id']; 
                        
                        if (!is_null($ids))
                        {
                            $result[$x]['strand_course'] = $this->db->query("SELECT * FROM strand_course WHERE id = '$ids'")->row();
                        }
                    }

                    for ($x = 0; $x < count($result); $x++)
                    {
                        $ids = $result[$x]['section_id']; 
                        
                        if (!is_null($ids))
                        {
                            $result[$x]['section'] = $this->db->query("SELECT * FROM section WHERE id = '$ids'")->row();
                        }
                    }
                }
            }

            return array('status' => 1, 'data' => $result);
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

            self::$check_code = $this->db->query("SELECT EXISTS(SELECT `code` FROM subject WHERE `code` = '$code') AS `exists`")->row();

            $validation_errors = $this->validate_form_fields($posts);

            if ($validation_errors == null)
            {
                $by_user_id = $posts['by_user_id'];

                unset($posts['by_user_id']);

                $insert_query = $this->db->set($posts)->insert('subject');
    
                if ($insert_query)
                {
                    $message = 'Subject has been added.';

                    $grade_level_id = $posts['grade_level_id'];

                    $students = $this->db->query("SELECT * FROM users WHERE role = 'STUDENT' AND grade_level_id = '$grade_level_id'")->result_array();

                    for ($x = 0; $x < count($students); $x++) {
                        $notif_post = array(
                            'user_id' => $students[$x]['id'],
                            'type' => 'subject',
                            'title' => 'New Subject',
                            'message' => 'A new subject has been added to your student account.',
                            'created_at' => DATE_TIME,
                            'updated_at' => DATE_TIME,
                        );

                        $this->db->set($notif_post)->insert('notifications');
                    }

                    $this->audit_model->log('Subject', 'Add', $message, $by_user_id);

                    return array('status' => 1, 'message' => $message, 'students' => $students);
                }

                throw new Exception($this->db->error()['message']);
            }
            else
            {
                return array('status' => 0, 'message' => 'Add subject failed.', 'validation_errors' => $validation_errors);
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

            $prev_data = $this->db->query("SELECT code, school_year_id from subject WHERE id = '$id'")->row();

            if ($prev_data->code !== $posts['code']) 
            {
                self::$update_code_changed = true;

                self::$check_code = $this->db->query("SELECT EXISTS(SELECT `code` FROM subject WHERE `code` = '$code') AS `exists`")->row();
            }

            $validation_errors = $this->validate_form_fields($posts);

            if ($validation_errors == null)
            {
                $by_user_id = $posts['by_user_id'];

                unset($posts['by_user_id']);

                $gl_id = $posts['grade_level_id'];

                $grade_level = $this->db->query("SELECT * FROM grade_level WHERE id = '$gl_id'")->row();

                if ($grade_level->is_senior_high == 0) 
                {
                    $posts['semester'] = null;
                    $posts['strand_course_id'] = null;
                }
                else
                {
                    $posts['grading'] = null;
                }

                if ($prev_data->school_year_id != $posts['school_year_id'])
                {
                    $this->db->where('subject_id', $id)->delete('subject_teachers');
                }

                $update_query = $this->db->where('id', $id)->update('subject', $posts);

                if ($update_query)
                {
                    $message = "Subject has been updated.";

                    $this->audit_model->log('Subject', 'Update', $message, $by_user_id);

                    return array('status' => 1, 'message' => $message);
                }

                throw new Exception($this->db->error()['message']);
            }
            else
            {
                return array('status' => 0, 'message' => 'Update subject failed.', 'validation_errors' => $validation_errors);
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
                return array('status' => 0, 'message' => 'Delete subject/s failed.');
            }

            $posts_ids = json_decode($posts['ids']);

            $lessons = $this->db->where_in('subject_id', $posts_ids)->get('subject_lessons')->result_array();

            for ($x = 0; $x < count($lessons); $x++) {
                $this->file_handler_model->delete_file($lessons[$x]['file_name']);
            }

            $assignments = $this->db->where_in('subject_id', $posts_ids)->get('subject_assignments')->result_array();

            for ($x = 0; $x < count($assignments); $x++) {
                $this->file_handler_model->delete_file($assignments[$x]['file_name']);
            }

            $delete_query = $this->db->where_in('id', $posts_ids)->delete('subject');

            if ($delete_query)
            {
                $this->audit_model->log('Subject', 'Delete', 'Subject/s has been deleted.', $posts['by_user_id']);

                return array('status' => 1, 'message' => 'Subject/s has been deleted.');
            }

            throw new Exception($this->db->error()['message']);
        }
        catch (Exception $e)
        {
            return array('status' => 0, 'message' => $e->getMessage());
        }
    }

    public function fetch_teachers($params)
    {
        try
        {
            $subject_id = $params['subject_id'];

            $subject = $this->db->where('id', $subject_id)->get('subject')->row();
    
            $teachers = $this->db->where('role', 'TEACHER')->where('school_year_id', $subject->school_year_id)->get('users');

            if ($teachers)
            {    
                $teachers = $teachers->result_array();

                return array('status' => 1, 'data' => $teachers);
            }

            throw new Exception($this->db->error()['message']);
        }
        catch (Exception $e)
        {
            return array('status' => 0, 'message' => $e->getMessage());
        }
    }

    public function fetch_subject_details($params)
    {
        try
        {
            $subject_id = $params['subject_id'];

            $subject = $this->db->query("SELECT * FROM subject WHERE id = '$subject_id'");

            if ($subject)
            {
                $subject_details = $subject->row();

                $subject_details->lessons = $this->db->query("SELECT * FROM subject_lessons WHERE subject_id = '$subject_id' ORDER BY id DESC")->result_array();

                $subject_details->assignments = $this->db->query("SELECT * FROM subject_assignments WHERE subject_id = '$subject_id' ORDER BY id DESC")->result_array();

                $subject_details->teachers = $this->db->query("SELECT * FROM subject_teachers WHERE subject_id = '$subject_id' ORDER BY id DESC")->result_array();

                $subject_details->exams = $this->db->query("SELECT * FROM subject_exams WHERE subject_id = '$subject_id' ORDER BY id DESC")->result_array();

                $subject_details->quizzes = $this->db->query("SELECT * FROM subject_quizzes WHERE subject_id = '$subject_id' ORDER BY id DESC")->result_array();

                $subject_details->task_performances = $this->db->query("SELECT * FROM subject_task_performances WHERE subject_id = '$subject_id' ORDER BY id DESC")->result_array();

                for ($x = 0; $x < count($subject_details->teachers); $x++) 
                {
                    $ids = $subject_details->teachers[$x]['user_id'];

                    if(!is_null($ids))
                    {
                        $subject_details->teachers[$x]['teacher'] = $this->db->query("SELECT id, full_name FROM users WHERE id = '$ids' ORDER BY id DESC")->row(); 
                    } 
                }

                return array('status' => 1, 'data' => $subject_details);
            }

            throw new Exception($this->db->error()['message']);
        }
        catch (Exception $e)
        {
            return array('status' => 0, 'message' => $e->getMessage());
        }
    }

    public function add_lesson($posts)
    {
        try
        {
            $is_uploaded = $this->file_handler_model->upload_file($posts['base64'], $posts['base64_type'], $posts['file_name']);

            if ($is_uploaded['status'])
            {
                $by_user_id = $posts['by_user_id'];

                unset($posts['by_user_id']);
    
                unset($posts['base64']);
    
                unset($posts['base64_type']);

                unset($posts['upload_file']);
    
                $query = $this->db->set($posts)->insert('subject_lessons');
    
                if ($query)
                {
                    $subject_id = $posts['subject_id'];
    
                    $fetch_lessons = $this->db->query("SELECT * FROM subject_lessons WHERE subject_id = '$subject_id'");
    
                    if ($fetch_lessons)
                    {
                        $lessons = $fetch_lessons->result_array();

                        $subject = $this->db->query("SELECT grade_level_id, school_year_id, section_id FROM subject WHERE id ='$subject_id'")->row();

                        $grade_level_id = $subject->grade_level_id;

                        $school_year_id = $subject->school_year_id;

                        $section_id = $subject->section_id;

                        $students = $this->db->query("SELECT * FROM users WHERE role = 'STUDENT' AND grade_level_id = '$grade_level_id' AND school_year_id = '$school_year_id' AND section_id = '$section_id'")->result_array();
    
                        for ($x = 0; $x < count($students); $x++) {
                            $notif_post = array(
                                'user_id' => $students[$x]['id'],
                                'type' => 'lesson',
                                'title' => 'New Lesson',
                                'message' => 'A new lesson has been added to your student account.',
                                'created_at' => DATE_TIME,
                                'updated_at' => DATE_TIME,
                            );
    
                            $this->db->set($notif_post)->insert('notifications');
                        }
        
                        $this->audit_model->log('Subject', 'Add Lesson', 'Add lesson successful.', $by_user_id);
    
                        return array('status' => 1, 'message' => 'Add lesson successful.', 'data' => $lessons, 'students' => $students);
                    }
    
                    throw new Exception($this->db->error()['message']);
                }

                throw new Exception($this->db->error()['message']);
            }
            else
            {
                return $is_uploaded;
            }
        }
        catch (Exception $e)
        {
            return array('status' => 0, 'message' => $e->getMessage());
        }
    }

    public function delete_lesson($posts)
    {
        try 
        {
            $file_name = $posts['file_name'];

            $file_delete = $this->file_handler_model->delete_file($file_name);

            if ($file_delete['status'])
            {
                $delete = $this->db->where('id', $posts['id'])->delete('subject_lessons');

                if ($delete)
                {
                    $subject_id = $posts['subject_id'];

                    $fetch_lessons = $this->db->query("SELECT * FROM subject_lessons WHERE subject_id = '$subject_id'");
    
                    if ($fetch_lessons)
                    {
                        $lessons = $fetch_lessons->result_array();
        
                        $this->audit_model->log('Subject', 'Delete Lesson', "Delete lesson successful.", $posts['by_user_id']);
    
                        return array('status' => 1, 'message' => "Delete lesson successful.", 'data' => $lessons);
                    }
                }

                throw new Exception($this->db->error()['message']);
            }
            else
            {
                return $file_delete;
            }
        }
        catch(Exception $e)
        {
            return array('status' => 0, 'message' => $e->getMessage());
        }
    }

    public function add_assignment($posts)
    {
        try
        {
            $is_uploaded = $this->file_handler_model->upload_file($posts['base64'], $posts['base64_type'], $posts['file_name']);

            if ($is_uploaded['status'])
            {
                $by_user_id = $posts['by_user_id'];

                unset($posts['by_user_id']);
    
                unset($posts['base64']);
    
                unset($posts['base64_type']);

                unset($posts['upload_file']);
    
                $query = $this->db->set($posts)->insert('subject_assignments');
    
                if ($query)
                {
                    $subject_id = $posts['subject_id'];
    
                    $fetch_assignments =  $this->db->query("SELECT * FROM subject_assignments WHERE subject_id = '$subject_id'");
    
                    if ($fetch_assignments)
                    {
                        $assignments = $fetch_assignments->result_array();

                        $subject = $this->db->query("SELECT grade_level_id, school_year_id, section_id FROM subject WHERE id ='$subject_id'")->row();

                        $grade_level_id = $subject->grade_level_id;

                        $school_year_id = $subject->school_year_id;

                        $section_id = $subject->section_id;

                        $students = $this->db->query("SELECT * FROM users WHERE role = 'STUDENT' AND grade_level_id = '$grade_level_id' AND school_year_id = '$school_year_id' AND section_id = '$section_id'")->result_array();
    
                        for ($x = 0; $x < count($students); $x++) {
                            $notif_post = array(
                                'user_id' => $students[$x]['id'],
                                'type' => 'assignment',
                                'title' => 'New Assignment',
                                'message' => 'A new assignment has been added to your student account.',
                                'created_at' => DATE_TIME,
                                'updated_at' => DATE_TIME,
                            );
    
                            $this->db->set($notif_post)->insert('notifications');
                        }
        
                        $this->audit_model->log('Subject', 'Add Assignment', 'Add assignment successful.', $by_user_id);
    
                        return array('status' => 1, 'message' => 'Add assignment successful.', 'data' => $assignments, 'students' => $students);
                    }
    
                    throw new Exception($this->db->error()['message']);
                }

                throw new Exception($this->db->error()['message']);
            }
            else
            {
                return $is_uploaded;
            }
        }
        catch (Exception $e)
        {
            return array('status' => 0, 'message' => $e->getMessage());
        }
    }

    public function delete_assignment($posts)
    {
        try 
        {
            $file_name = $posts['file_name'];

            $file_delete = $this->file_handler_model->delete_file($file_name);

            if ($file_delete['status'])
            {
                $submitted_ass = $this->db->where('assignment_id', $posts['id'])->get('submitted_assignments')->row();

                if ($submitted_ass)
                {
                    $this->file_handler_model->delete_file($submitted_ass->file_name);
                }

                $delete = $this->db->where('id', $posts['id'])->delete('subject_assignments');

                if ($delete)
                {
                    $subject_id = $posts['subject_id'];

                    $fetch_assignments = $this->db->query("SELECT * FROM subject_assignments WHERE subject_id = '$subject_id'");
    
                    if ($fetch_assignments)
                    {
                        $assignments = $fetch_assignments->result_array();

                        $this->audit_model->log('Subject', 'Delete Assignment', "Delete assignment successful.", $posts['by_user_id']);
    
                        return array('status' => 1, 'message' => "Delete assignment successful.", 'data' => $assignments);
                    }
                }

                throw new Exception($this->db->error()['message']);
            }
            else
            {
                return $file_delete;
            }
        }
        catch(Exception $e)
        {
            return array('status' => 0, 'message' => $e->getMessage());
        }
    }

    public function add_teacher($posts)
    {
        try
        {
            $by_user_id = $posts['by_user_id'];

            unset($posts['by_user_id']);

            $query = $this->db->set($posts)->insert('subject_teachers');

            $query = true;

            if ($query)
            {
                $subject_id = $posts['subject_id'];

                $fetch_teachers =  $this->db->query("SELECT * FROM subject_teachers WHERE subject_id = '$subject_id'");

                $notif_post = array(
                    'user_id' => $posts['user_id'],
                    'type' => 'teacher new subject',
                    'title' => 'New Subject',
                    'message' => 'A new subject has been added to your teacher account.',
                    'created_at' => DATE_TIME,
                    'updated_at' => DATE_TIME,
                );

                $this->db->set($notif_post)->insert('notifications');

                if ($fetch_teachers)
                {
                    $teachers = $fetch_teachers->result_array();

                    for ($x = 0; $x < count($teachers); $x++)
                    {
                        $ids = $teachers[$x]['user_id'];

                        $teachers[$x]['teacher'] = $this->db->query("SELECT id, full_name FROM users WHERE id = '$ids'")->row();
                    }
    
                    $this->audit_model->log('Subject', 'Add Teacher', 'Add teacher successful.', $by_user_id);

                    return array('status' => 1, 'message' => 'Add teacher successful.', 'data' => $teachers);
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

    public function delete_teacher($posts)
    {
        try 
        {
            $delete = $this->db->where('id', $posts['id'])->delete('subject_teachers');

            if ($delete)
            {
                $subject_id = $posts['subject_id'];

                $fetch_teachers = $this->db->query("SELECT * FROM subject_teachers WHERE subject_id = '$subject_id'");

                if ($fetch_teachers)
                {
                    $teachers = $fetch_teachers->result_array();

                    for ($x = 0; $x < count($teachers); $x++)
                    {
                        $ids = $teachers[$x]['user_id'];

                        $teachers[$x]['teacher'] = $this->db->query("SELECT id, full_name FROM users WHERE id = '$ids'")->row();
                    }
    
                    $this->audit_model->log('Subject', 'Delete Teacher', "Delete teacher successful.", $posts['by_user_id']);

                    return array('status' => 1, 'message' => "Delete teacher successful.", 'data' => $teachers);
                }
            }

            throw new Exception($this->db->error()['message']);
        }
        catch(Exception $e)
        {
            return array('status' => 0, 'message' => $e->getMessage());
        }
    }

    public function add_exam($posts)
    {
        try
        {
            $by_user_id = $posts['by_user_id'];

            unset($posts['by_user_id']);

            $query = $this->db->set($posts)->insert('subject_exams');

            if ($query)
            {
                $subject_id = $posts['subject_id'];

                $fetch_exams =  $this->db->query("SELECT * FROM subject_exams WHERE subject_id = '$subject_id'");

                if ($fetch_exams)
                {
                    $exams = $fetch_exams->result_array();

                    $subject = $this->db->query("SELECT grade_level_id, school_year_id, section_id FROM subject WHERE id ='$subject_id'")->row();

                    $grade_level_id = $subject->grade_level_id;

                    $school_year_id = $subject->school_year_id;

                    $section_id = $subject->section_id;

                    $students = $this->db->query("SELECT * FROM users WHERE role = 'STUDENT' AND grade_level_id = '$grade_level_id' AND school_year_id = '$school_year_id' AND section_id = '$section_id'")->result_array();

                    for ($x = 0; $x < count($students); $x++) {
                        $notif_post = array(
                            'user_id' => $students[$x]['id'],
                            'type' => 'exam',
                            'title' => 'New Exam',
                            'message' => 'A new exam has been added to your student account.',
                            'created_at' => DATE_TIME,
                            'updated_at' => DATE_TIME,
                        );

                        $this->db->set($notif_post)->insert('notifications');
                    }
    
                    $this->audit_model->log('Subject', 'Add Exam', 'Add exam successful.', $by_user_id);

                    return array('status' => 1, 'message' => 'Add exam successful.', 'data' => $exams, 'students' => $students);
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

    public function delete_exam($posts)
    {
        try 
        {
            $delete = $this->db->where('id', $posts['id'])->delete('subject_exams');

            if ($delete)
            {
                $subject_id = $posts['subject_id'];

                $fetch_exams = $this->db->query("SELECT * FROM subject_exams WHERE subject_id = '$subject_id'");

                if ($fetch_exams)
                {
                    $exams = $fetch_exams->result_array();
    
                    $this->audit_model->log('Subject', 'Delete Exam', "Delete exam successful.", $posts['by_user_id']);

                    return array('status' => 1, 'message' => "Delete exam successful.", 'data' => $exams);
                }
            }

            throw new Exception($this->db->error()['message']);
        }
        catch(Exception $e)
        {
            return array('status' => 0, 'message' => $e->getMessage());
        }
    }

    public function add_quiz($posts)
    {
        try
        {
            $by_user_id = $posts['by_user_id'];

            unset($posts['by_user_id']);

            $query = $this->db->set($posts)->insert('subject_quizzes');

            if ($query)
            {
                $subject_id = $posts['subject_id'];

                $fetch_quizzes =  $this->db->query("SELECT * FROM subject_quizzes WHERE subject_id = '$subject_id'");

                if ($fetch_quizzes)
                {
                    $quizzes = $fetch_quizzes->result_array();

                    $subject = $this->db->query("SELECT grade_level_id, school_year_id, section_id FROM subject WHERE id ='$subject_id'")->row();

                    $grade_level_id = $subject->grade_level_id;

                    $school_year_id = $subject->school_year_id;

                    $section_id = $subject->section_id;

                    $students = $this->db->query("SELECT * FROM users WHERE role = 'STUDENT' AND grade_level_id = '$grade_level_id' AND school_year_id = '$school_year_id' AND section_id = '$section_id'")->result_array();

                    for ($x = 0; $x < count($students); $x++) {
                        $notif_post = array(
                            'user_id' => $students[$x]['id'],
                            'type' => 'quiz',
                            'title' => 'New Quiz',
                            'message' => 'A new quiz has been added to your student account.',
                            'created_at' => DATE_TIME,
                            'updated_at' => DATE_TIME,
                        );

                        $this->db->set($notif_post)->insert('notifications');
                    }
    
                    $this->audit_model->log('Subject', 'Add Quiz', 'Add quiz successful.', $by_user_id);

                    return array('status' => 1, 'message' => 'Add quiz successful.', 'data' => $quizzes, 'students' => $students);
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

    public function delete_quiz($posts)
    {
        try 
        {
            $delete = $this->db->where('id', $posts['id'])->delete('subject_quizzes');

            if ($delete)
            {
                $subject_id = $posts['subject_id'];

                $fetch_quizzes = $this->db->query("SELECT * FROM subject_quizzes WHERE subject_id = '$subject_id'");

                if ($fetch_quizzes)
                {
                    $quizzes = $fetch_quizzes->result_array();
    
                    $this->audit_model->log('Subject', 'Delete Quiz', "Delete quiz successful.", $posts['by_user_id']);

                    return array('status' => 1, 'message' => "Delete quiz successful.", 'data' => $quizzes);
                }
            }

            throw new Exception($this->db->error()['message']);
        }
        catch(Exception $e)
        {
            return array('status' => 0, 'message' => $e->getMessage());
        }
    }

    public function add_task_performance($posts)
    {
        try
        {
            $is_uploaded = $this->file_handler_model->upload_file($posts['base64'], $posts['base64_type'], $posts['file_name']);

            if ($is_uploaded['status'])
            {
                $by_user_id = $posts['by_user_id'];

                unset($posts['by_user_id']);
    
                unset($posts['base64']);
    
                unset($posts['base64_type']);

                unset($posts['upload_file']);
    
                $query = $this->db->set($posts)->insert('subject_task_performances');
    
                if ($query)
                {
                    $subject_id = $posts['subject_id'];
    
                    $fetch_tp =  $this->db->query("SELECT * FROM subject_task_performances WHERE subject_id = '$subject_id'");
    
                    if ($fetch_tp)
                    {
                        $tp = $fetch_tp->result_array();

                        $subject = $this->db->query("SELECT grade_level_id, school_year_id, section_id FROM subject WHERE id ='$subject_id'")->row();

                        $grade_level_id = $subject->grade_level_id;
    
                        $school_year_id = $subject->school_year_id;

                        $section_id = $subject->section_id;

                        $students = $this->db->query("SELECT * FROM users WHERE role = 'STUDENT' AND grade_level_id = '$grade_level_id' AND school_year_id = '$school_year_id' AND section_id = '$section_id'")->result_array();
    
                        for ($x = 0; $x < count($students); $x++) {
                            $notif_post = array(
                                'user_id' => $students[$x]['id'],
                                'type' => 'task performance',
                                'title' => 'New Task Performance',
                                'message' => 'A new task performance has been added to your student account.',
                                'created_at' => DATE_TIME,
                                'updated_at' => DATE_TIME,
                            );
    
                            $this->db->set($notif_post)->insert('notifications');
                        }
        
                        $this->audit_model->log('Subject', 'Add Task Performance', 'Add task performance successful.', $by_user_id);
    
                        return array('status' => 1, 'message' => 'Add task performance successful.', 'data' => $tp, 'students' => $students);
                    }
    
                    throw new Exception($this->db->error()['message']);
                }

                throw new Exception($this->db->error()['message']);
            }
            else
            {
                return $is_uploaded;
            }
        }
        catch (Exception $e)
        {
            return array('status' => 0, 'message' => $e->getMessage());
        }
    }

    public function delete_task_performance($posts)
    {
        try 
        {
            $delete = $this->db->where('id', $posts['id'])->delete('subject_task_performances');

            if ($delete)
            {
                $subject_id = $posts['subject_id'];

                $fetch_tp = $this->db->query("SELECT * FROM subject_task_performances WHERE subject_id = '$subject_id'");

                if ($fetch_tp)
                {
                    $task_performances = $fetch_tp->result_array();
    
                    $this->audit_model->log('Subject', 'Delete Task Performance', "Delete task performance successful.", $posts['by_user_id']);

                    return array('status' => 1, 'message' => "Delete task performance successful.", 'data' => $task_performances);
                }
            }

            throw new Exception($this->db->error()['message']);
        }
        catch(Exception $e)
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

        if (!isset($posts['description']) || empty($posts['description']))
        {
            $validation['description'] = 'description field is required.';
        }

        if (!isset($posts['grade_level_id']) || empty($posts['grade_level_id']))
        {
            $validation['grade_level_id'] = 'grade_level_id field is required.';
        }

        return $validation;
    }
}