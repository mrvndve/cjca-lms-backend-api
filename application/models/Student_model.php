<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Student_model extends CI_Model 
{
    function __construct()
    {
        parent::__construct();

        $this->load->database();

        $this->load->model('Audit_model', 'audit_model');

        $this->load->model('File_handler_model', 'file_handler_model');
    }
    
    public function fetch_data($params)
    {
        try
        {
            $user_id = $params['user_id'];
            
            $user = $this->db->query("SELECT * FROM users WHERE id = '$user_id'")->row();

            $grade_level_id = $user->grade_level_id;

            $school_year_id = $user->school_year_id;

            $section_id = $user->section_id;

            $subjects = $this->db->query("SELECT * FROM subject WHERE grade_level_id = '$grade_level_id' AND school_year_id = '$school_year_id' AND section_id = '$section_id'")->result_array();

            for ($x = 0; $x < count($subjects); $x++)
            {
                $subj_ids = $subjects[$x]['id'];

                $grade_level_ids = $subjects[$x]['grade_level_id'];

                $strand_course_ids = $subjects[$x]['strand_course_id'];

                date_default_timezone_set('Asia/Taipei');

                $date_now = date('Y-m-d H:i:s');

                $subjects[$x]['lessons'] = $this->db->query("SELECT * FROM subject_lessons WHERE subject_id = '$subj_ids' ORDER BY id DESC")->result_array();
                
                $subjects[$x]['assignments'] = $this->db->where('subject_id', $subj_ids)->order_by('id', 'DESC')->get('subject_assignments')->result_array();

                $subjects[$x]['exams'] = $this->db->where('subject_id', $subj_ids)->order_by('id', 'DESC')->get('subject_exams')->result_array();

                $subjects[$x]['quizzes'] = $this->db->where('subject_id', $subj_ids)->order_by('id', 'DESC')->get('subject_quizzes')->result_array();

                $subjects[$x]['task_performances'] = $this->db->where('subject_id', $subj_ids)->order_by('id', 'DESC')->get('subject_task_performances')->result_array();

                $subjects[$x]['teachers'] = $this->db->query("SELECT * FROM subject_teachers WHERE subject_id = '$subj_ids' ORDER BY id DESC")->result_array();

                $subjects[$x]['grade'] = $this->db->query("SELECT * FROM grade_level WHERE id = '$grade_level_ids'")->row();

                for ($z = 0; $z < count($subjects[$x]['exams']); $z++)
                {
                    $exam_ids = $subjects[$x]['exams'][$z]['id'];
                    
                    $is_submitted = $this->db->query("SELECT * FROM submitted_exams WHERE `exam_id` = '$exam_ids' AND `user_id` = '$user_id'")->row();

                    if (!is_null($is_submitted))
                    {
                        $subjects[$x]['exams'][$z]['is_submitted'] = $is_submitted;
                    }
                }

                for ($z = 0; $z < count($subjects[$x]['quizzes']); $z++)
                {
                    $quiz_ids = $subjects[$x]['quizzes'][$z]['id'];
                    
                    $is_submitted = $this->db->query("SELECT * FROM submitted_quizzes WHERE `quiz_id` = '$quiz_ids' AND `user_id` = '$user_id'")->row();

                    if (!is_null($is_submitted))
                    {
                        $subjects[$x]['quizzes'][$z]['is_submitted'] = $is_submitted;
                    }
                }

                for ($z = 0; $z < count($subjects[$x]['task_performances']); $z++)
                {
                    $tp_ids = $subjects[$x]['task_performances'][$z]['id'];
                    
                    $is_submitted = $this->db->query("SELECT * FROM submitted_task_performances WHERE `task_performance_id` = '$tp_ids' AND `user_id` = '$user_id'")->row();

                    if (!is_null($is_submitted))
                    {
                        $subjects[$x]['task_performances'][$z]['is_submitted'] = $is_submitted;
                    }
                }

                for ($z = 0; $z < count($subjects[$x]['assignments']); $z++)
                {
                    $ass_ids = $subjects[$x]['assignments'][$z]['id'];
                    
                    $is_submitted = $this->db->query("SELECT * FROM submitted_assignments WHERE `assignment_id` = '$ass_ids' AND `user_id` = '$user_id'")->row();

                    if (!is_null($is_submitted))
                    {
                        $subjects[$x]['assignments'][$z]['is_submitted'] = $is_submitted;
                    }
                }

                for ($z = 0; $z < count($subjects[$x]['teachers']); $z++) 
                {
                    $teacher_ids = $subjects[$x]['teachers'][$z]['user_id'];

                    $subjects[$x]['teachers'][$z] = $this->db->query("SELECT id, first_name as name FROM users WHERE id = '$teacher_ids'")->row();
                }

                if (!is_null($strand_course_ids)) 
                {
                    $subjects[$x]['strand_course'] = $this->db->query("SELECT * FROM strand_course WHERE id = '$strand_course_ids'")->row();
                }
            }

            $data = array(
                'subjects' => $subjects,
            );

            return array('status' => 1, 'data' => $data);
        }
        catch (Exception $e)
        {
            return array('status' => 0, 'message' => $e->getMessage());
        }
    }

    public function submit_exam($posts)
    {
        try
        {
            $user = $posts['user'];

            $teachers_ids = json_decode($posts['teacher_ids']);

            $teachers = $this->db->where_in('id', $teachers_ids)->get('users')->result_array();

            for($x = 0; $x < count($teachers); $x++)
            {
                $notif_post = array(
                    'user_id' => $teachers[$x]['id'],
                    'type' => 'teacher exam',
                    'title' => 'Exam',
                    'message' => "$user submitted an exam.",
                    'created_at' => DATE_TIME,
                    'updated_at' => DATE_TIME,
                );

                $this->db->set($notif_post)->insert('notifications');
            }

            unset($posts['user']);

            unset($posts['teacher_ids']);

            $insert = $this->db->set($posts)->insert('submitted_exams');

            if ($insert)
            {
                $message = 'Your answers to your exam has been submitted.';

                $this->audit_model->log('Exam', 'Submit Exam', $message, $posts['user_id']);

                return array('status' => 1, 'message' => $message);
            }

            throw new Exception($this->db->error()['message']);
        }
        catch (Exception $e)
        {
            return array('status' => 0, 'message' => $e->getMessage());
        }
    }

    public function submit_quiz($posts)
    {
        try
        {
            $user = $posts['user'];

            $teachers_ids = json_decode($posts['teacher_ids']);

            $teachers = $this->db->where_in('id', $teachers_ids)->get('users')->result_array();

            for($x = 0; $x < count($teachers); $x++)
            {
                $notif_post = array(
                    'user_id' => $teachers[$x]['id'],
                    'type' => 'teacher quiz',
                    'title' => 'Quiz',
                    'message' => "$user submitted a quiz.",
                    'created_at' => DATE_TIME,
                    'updated_at' => DATE_TIME,
                );

                $this->db->set($notif_post)->insert('notifications');
            }

            unset($posts['user']);

            unset($posts['teacher_ids']);

            $insert = $this->db->set($posts)->insert('submitted_quizzes');

            if ($insert)
            {
                $message = 'Your answers to your quiz has been submitted.';

                $this->audit_model->log('Quiz', 'Submit Quiz', $message, $posts['user_id']);

                return array('status' => 1, 'message' => $message);
            }

            throw new Exception($this->db->error()['message']);
        }
        catch (Exception $e)
        {
            return array('status' => 0, 'message' => $e->getMessage());
        }
    }

    public function submit_assignment($posts)
    {
        try
        {  
            $user = $posts['user'];

            $teachers_ids = json_decode($posts['teacher_ids']);

            $teachers = $this->db->where_in('id', $teachers_ids)->get('users')->result_array();

            for($x = 0; $x < count($teachers); $x++)
            {
                $notif_post = array(
                    'user_id' => $teachers[$x]['id'],
                    'type' => 'teacher assignment',
                    'title' => 'Assignment',
                    'message' => "$user submitted an assignment.",
                    'created_at' => DATE_TIME,
                    'updated_at' => DATE_TIME,
                );

                $this->db->set($notif_post)->insert('notifications');
            }

            if (isset($posts['base64']) && isset($posts['base64_type']) && isset($posts['file_name']))
            {
                $this->file_handler_model->upload_file($posts['base64'], $posts['base64_type'], $posts['file_name']);
            }

            $data = array(
                'assignment_id' => $posts['assignment_id'],
                'user_id' => $posts['user_id'],
                'score' => 0,
                'status' => 'Pending',
                'answer' => $posts['answer'],
                'file_name' => isset($posts['file_name']) ? $posts['file_name'] : '',
                'created_at' => DATE_TIME,
                'updated_at' => DATE_TIME,
            );

            $insert = $this->db->set($data)->insert('submitted_assignments');

            if ($insert)
            {
                $message = 'Your assignment has been submitted.';

                $this->audit_model->log('Assignment', 'Submit Assignment', $message, $posts['user_id']);

                return array('status' => 1, 'message' => $message);
            }

            throw new Exception($this->db->error()['message']);
        }
        catch (Exception $e)
        {
            return array('status' => 0, 'message' => $e->getMessage());
        }
    }

    public function submit_task_performance($posts)
    {
        try
        {  
            $user = $posts['user'];

            $teachers_ids = json_decode($posts['teacher_ids']);

            $teachers = $this->db->where_in('id', $teachers_ids)->get('users')->result_array();

            for($x = 0; $x < count($teachers); $x++)
            {
                $notif_post = array(
                    'user_id' => $teachers[$x]['id'],
                    'type' => 'teacher task performance',
                    'title' => 'Task Performance',
                    'message' => "$user submitted a task performance.",
                    'created_at' => DATE_TIME,
                    'updated_at' => DATE_TIME,
                );

                $this->db->set($notif_post)->insert('notifications');
            }

            if (isset($posts['base64']) && isset($posts['base64_type']) && isset($posts['file_name']))
            {
                $this->file_handler_model->upload_file($posts['base64'], $posts['base64_type'], $posts['file_name']);
            }

            $data = array(
                'task_performance_id' => $posts['task_performance_id'],
                'user_id' => $posts['user_id'],
                'score' => 0,
                'status' => 'Pending',
                'answer' => $posts['answer'],
                'file_name' => isset($posts['file_name']) ? $posts['file_name'] : '',
                'created_at' => DATE_TIME,
                'updated_at' => DATE_TIME,
            );

            $insert = $this->db->set($data)->insert('submitted_task_performances');

            if ($insert)
            {
                $message = 'Your task performance has been submitted.';

                $this->audit_model->log('Task Performance', 'Submit Task Performance', $message, $posts['user_id']);

                return array('status' => 1, 'message' => $message);
            }

            throw new Exception($this->db->error()['message']);
        }
        catch (Exception $e)
        {
            return array('status' => 0, 'message' => $e->getMessage());
        }
    }

    public function fetch_scores($params) 
    {
        try 
        {
            $user_id = $params['user_id'];

            $assigned_subjects = $this->db->query("SELECT * FROM subject_teachers WHERE `user_id` = '$user_id'")->result_array();

            $submitted_exam = [];

            $submitted_quizzes = [];

            $submitted_task_performances = [];

            $submitted_assignments = [];

            if (count($assigned_subjects) > 0)
            {
                $subject_ids = [];

                for ($x = 0; $x < count($assigned_subjects); $x++)
                {
                    $subject_ids[] = $assigned_subjects[$x]['subject_id'];
                }

                $exams = $this->db->select('*')->from('subject_exams')->where_in('subject_id', $subject_ids)->get()->result_array();

                $exam_ids = [];

                for ($x = 0; $x < count($exams); $x++)
                {
                    $exam_ids[] = $exams[$x]['id'];
                }

                if (count($exam_ids) > 0)
                {
                    $submitted_exam = $this->db->select('*')->from('submitted_exams')->where_in('exam_id', $exam_ids)->order_by('id', 'DESC')->get()->result_array();

                    for ($x = 0; $x < count($submitted_exam); $x++)
                    {
                        $exam_ids = $submitted_exam[$x]['exam_id'];
        
                        $user_ids = $submitted_exam[$x]['user_id'];
        
                        $submitted_exam[$x]['exam'] = $this->db->select('*')->from('subject_exams')->where('id', $exam_ids)->get()->row();
        
                        $submitted_exam[$x]['user'] = $this->db->select('id, full_name, grade_level_id, section_id')->from('users')->where('id', $user_ids)->get()->row();

                        $submitted_exam[$x]['user']->grade = $this->db->select('name')->from('grade_level')->where('id', $submitted_exam[$x]['user']->grade_level_id)->get()->row();

                        $submitted_exam[$x]['user']->section = $this->db->select('name')->from('section')->where('id', $submitted_exam[$x]['user']->section_id)->get()->row();
                    }
                }

                $quizzes = $this->db->select('*')->from('subject_quizzes')->where_in('subject_id', $subject_ids)->get()->result_array();

                $quiz_ids = [];

                for ($x = 0; $x < count($quizzes); $x++)
                {
                    $quiz_ids[] = $quizzes[$x]['id'];
                }

                if (count($quiz_ids) > 0)
                {
                    $submitted_quizzes = $this->db->select('*')->from('submitted_quizzes')->where_in('quiz_id', $quiz_ids)->order_by('id', 'DESC')->get()->result_array();

                    for ($x = 0; $x < count($submitted_quizzes); $x++)
                    {
                        $quiz_ids = $submitted_quizzes[$x]['quiz_id'];
        
                        $user_ids = $submitted_quizzes[$x]['user_id'];
        
                        $submitted_quizzes[$x]['quiz'] = $this->db->select('*')->from('subject_quizzes')->where('id', $quiz_ids)->get()->row();
        
                        $submitted_quizzes[$x]['user'] = $this->db->select('id, full_name, grade_level_id, section_id')->from('users')->where('id', $user_ids)->get()->row();

                        $submitted_quizzes[$x]['user']->grade = $this->db->select('name')->from('grade_level')->where('id', $submitted_quizzes[$x]['user']->grade_level_id)->get()->row();

                        $submitted_quizzes[$x]['user']->section = $this->db->select('name')->from('section')->where('id', $submitted_quizzes[$x]['user']->section_id)->get()->row();
                    }
                }

                $task_performances = $this->db->select('*')->from('subject_task_performances')->where_in('subject_id', $subject_ids)->get()->result_array();

                $tp_ids = [];

                for ($x = 0; $x < count($task_performances); $x++)
                {
                    $tp_ids[] = $task_performances[$x]['id'];
                }

                if (count($tp_ids) > 0)
                {
                    $submitted_task_performances = $this->db->select('*')->from('submitted_task_performances')->where_in('task_performance_id', $tp_ids)->order_by('id', 'DESC')->get()->result_array();

                    for ($x = 0; $x < count($submitted_task_performances); $x++)
                    {
                        $tp_ids = $submitted_task_performances[$x]['task_performance_id'];
        
                        $user_ids = $submitted_task_performances[$x]['user_id'];
        
                        $submitted_task_performances[$x]['task_performance'] = $this->db->select('*')->from('subject_task_performances')->where('id', $tp_ids)->get()->row();
        
                        $submitted_task_performances[$x]['user'] = $this->db->select('id, full_name, grade_level_id, section_id')->from('users')->where('id', $user_ids)->get()->row();

                        $submitted_task_performances[$x]['user']->grade = $this->db->select('name')->from('grade_level')->where('id', $submitted_task_performances[$x]['user']->grade_level_id)->get()->row();

                        $submitted_task_performances[$x]['user']->section = $this->db->select('name')->from('section')->where('id', $submitted_task_performances[$x]['user']->section_id)->get()->row();
                    }
                }

                $assignments = $this->db->select('*')->from('subject_assignments')->where_in('subject_id', $subject_ids)->get()->result_array();

                $ass_ids = [];

                for ($x = 0; $x < count($assignments); $x++)
                {
                    $ass_ids[] = $assignments[$x]['id'];
                }

                if (count($ass_ids) > 0)
                {
                    $submitted_assignments = $this->db->select('*')->from('submitted_assignments')->where_in('assignment_id', $ass_ids)->order_by('id', 'DESC')->get()->result_array();

                    for ($x = 0; $x < count($submitted_assignments); $x++)
                    {
                        $ass_ids = $submitted_assignments[$x]['assignment_id'];
        
                        $user_ids = $submitted_assignments[$x]['user_id'];
        
                        $submitted_assignments[$x]['assignment'] = $this->db->select('*')->from('subject_assignments')->where('id', $ass_ids)->get()->row();
        
                        $submitted_assignments[$x]['user'] = $this->db->select('id, full_name, grade_level_id, section_id')->from('users')->where('id', $user_ids)->get()->row();

                        $submitted_assignments[$x]['user']->grade = $this->db->select('name')->from('grade_level')->where('id', $submitted_assignments[$x]['user']->grade_level_id)->get()->row();

                        $submitted_assignments[$x]['user']->section = $this->db->select('name')->from('section')->where('id', $submitted_assignments[$x]['user']->section_id)->get()->row();
                    }
                }
            }
            
            $data = array(
                'exams' => $submitted_exam,
                'quizzes' => $submitted_quizzes,
                'task_performances' => $submitted_task_performances,
                'assignments' => $submitted_assignments,
            );

            return array('status' => 1, 'data' => $data);
        }        
        catch (Exception $e)
        {
            return array('status' => 0, 'message' => $e->getMessage());
        }
    }

    public function set_score($posts)
    {
        try
        {  
            $type = $posts['type'];

            $table = '';

            if ($posts['type'] == 'assignment')
            {
                $table = 'submitted_assignments';
            }
            else if ($posts['type'] == 'task performance') 
            {
                $table = 'submitted_task_performances';
            }
            else if ($posts['type'] == 'exam') 
            {
                $table = 'submitted_exams';
            }
            else
            {
                $table = 'submitted_quizzes';
            }

            $data = array(
                'score' => $posts['score'],
                'status' => $posts['status'],
                'updated_at' => DATE_TIME,
            );

            $update = $this->db->where('id', $posts['id'])->update($table, $data);

            if ($update)
            {
                $student = $this->db->where('id', $posts['student_id'])->get('users')->row();

                $notif_post = array(
                    'user_id' => $posts['student_id'],
                    'type' => "set $type score",
                    'title' => 'Score',
                    'message' => "Your $type has been scored.",
                    'created_at' => DATE_TIME,
                    'updated_at' => DATE_TIME,
                );

                $this->db->set($notif_post)->insert('notifications');

                $message = 'Score submitted.';

                $this->audit_model->log('Grade Book', 'Set Score', $message, $posts['user_id']);

                return array('status' => 1, 'message' => $message, 'student' => $student);
            }

            throw new Exception($this->db->error()['message']);
        }
        catch (Exception $e)
        {
            return array('status' => 0, 'message' => $e->getMessage());
        }
    }

    public function retake($posts)
    {
        try
        {  
            $type = $posts['type'];
            
            $submitted_table = '';

            $subject_table = '';

            if ($type == 'assignment')
            {
                $submitted_table = 'submitted_assignments';
                $subject_table = 'subject_assignments';
            }
            else if ($type == 'task performance') 
            {
                $submitted_table = 'submitted_task_performances';
                $subject_table = 'subject_task_performances';
            }
            else if ($type == 'exam') 
            {
                $submitted_table = 'submitted_exams';
                $subject_table = 'subject_exams';
            }
            else
            {
                $submitted_table = 'submitted_quizzes';
                $subject_table = 'subject_quizzes';
            }

            date_default_timezone_set('Asia/Taipei');

            $due_date = date('Y-m-d H:i:s', strtotime('+1 day'));

            $update_data = array(
                'due_date' => $due_date,
                'updated_at' => DATE_TIME,
            );

            $this->db->where('id', $posts['access_id'])->update($subject_table, $update_data);

            $delete = $this->db->where('id', $posts['id'])->delete($submitted_table);

            if ($delete)
            {
                $student = $this->db->where('id', $posts['student_id'])->get('users')->row();

                $notif_post = array(
                    'user_id' => $posts['student_id'],
                    'type' => "retake $type",
                    'title' => 'Retake',
                    'message' => "You have given a permission to retake $type",
                    'created_at' => DATE_TIME,
                    'updated_at' => DATE_TIME,
                );

                $this->db->set($notif_post)->insert('notifications');
                
                $message = "Student permitted to retake his/her $type";

                $this->audit_model->log('Grade Book', 'Retake', $message, $posts['user_id']);

                return array('status' => 1, 'message' => $message, 'student' => $student);
            }

            throw new Exception($this->db->error()['message']);
        }
        catch (Exception $e)
        {
            return array('status' => 0, 'message' => $e->getMessage());
        }
    }
}