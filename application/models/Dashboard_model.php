<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Dashboard_model extends CI_Model 
{
    function __construct()
    {
        parent::__construct();

        $this->load->database();

        $this->load->model('Audit_model', 'audit_model');
    }

    public function fetch_admin_dashboard() 
    {
        try
        {
            $data = array();

            $active_students = $this->db->where('role', 'STUDENT')->where('is_active', 1)->count_all_results('users');
            $active_teachers = $this->db->where('role', 'TEACHER')->where('is_active', 1)->count_all_results('users');
            $active_admins = $this->db->where('role', 'ADMIN')->where('is_active', 1)->count_all_results('users');
            $inactive_students = $this->db->where('role', 'STUDENT')->where('is_active', 0)->count_all_results('users');
            $inactive_teachers = $this->db->where('role', 'TEACHER')->where('is_active', 0)->count_all_results('users');
            $inactive_admins = $this->db->where('role', 'ADMIN')->where('is_active', 0)->count_all_results('users');

            $users[] = array(
                'name' => 'Students',
                'active' => $active_students,
                'inactive' => $inactive_students,
            );

            $users[] = array(
                'name' => 'Teachers',
                'active' => $active_teachers,
                'inactive' => $inactive_teachers,
            );

            $users[] = array(
                'name' => 'Admins',
                'active' => $active_admins,
                'inactive' => $inactive_admins,
            );

            $subjects = $this->db->query("SELECT id, name FROM subject")->result_array();

            for ($x = 0; $x < count($subjects); $x++)
            {
                $subject_ids = $subjects[$x]['id'];

                $subjects[$x]['exams'] = $this->db->where('subject_id', $subject_ids)->count_all_results('subject_exams');

                $subjects[$x]['quizzes'] = $this->db->where('subject_id', $subject_ids)->count_all_results('subject_quizzes');

                $subjects[$x]['lessons'] = $this->db->where('subject_id', $subject_ids)->count_all_results('subject_lessons');

                $subjects[$x]['assignments'] = $this->db->where('subject_id', $subject_ids)->count_all_results('subject_assignments');

                $subjects[$x]['task_performances'] = $this->db->where('subject_id', $subject_ids)->count_all_results('subject_task_performances');
            }

            $grade_levels = $this->db->query("SELECT * FROM grade_level")->result_array();

            for ($x = 0; $x < count($grade_levels); $x++)
            {
                $grade_level_ids = $grade_levels[$x]['id'];

                $grade_levels[$x]['students'] = $this->db->where('grade_level_id', $grade_level_ids)->where('role', 'STUDENT')->count_all_results('users');

                $grade_levels[$x]['subjects'] = $this->db->where('grade_level_id', $grade_level_ids)->count_all_results('subject');
            }

            $departments = $this->db->query("SELECT * FROM department")->result_array();

            for ($x = 0; $x < count($departments); $x++)
            {
                $department_ids = $departments[$x]['id'];

                $departments[$x]['teachers'] = $this->db->where('department_id', $department_ids)->where('role', 'TEACHER')->count_all_results('users');
            }

            $school_years = $this->db->query("SELECT * FROM school_year")->result_array();

            for ($x = 0; $x < count($school_years); $x++)
            {
                $school_year_ids = $school_years[$x]['id'];

                $school_years[$x]['students'] = $this->db->where('school_year_id', $school_year_ids)->where('role', 'STUDENT')->count_all_results('users');

                $school_years[$x]['teachers'] = $this->db->where('school_year_id', $school_year_ids)->where('role', 'TEACHER')->count_all_results('users');
            }

            $data = array(
                'grade_levels' => $grade_levels,
                'departments' => $departments,
                'subjects' => $subjects,
                'users' => $users,
                'school_years' => $school_years,
            );

            return array('status' => 1, 'data' => $data);
        }
        catch (Exception $e)
        {
            return array('status' => 0, 'message' => $e->getMessage());
        }
    }

    public function fetch_teacher_dashboard($params)
    {
        try
        {
            $data = array();

            $user_id = $params['user_id'];

            $assigned_subjects = $this->db->query("SELECT * FROM subject_teachers WHERE `user_id` = '$user_id'")->result_array();

            $subject_ids = [];

            for ($x = 0; $x < count($assigned_subjects); $x++)
            {
                $subject_ids[] = $assigned_subjects[$x]['subject_id'];
            }

            $subjects = [];

            if(count($subject_ids) > 0)
            {
                $subjects = $this->db->where_in('id', $subject_ids)->get('subject')->result_array();
            }

            for ($x = 0; $x < count($subjects); $x++)
            {
                $subject_ids = $subjects[$x]['id'];

                $subjects[$x]['exams'] = $this->db->where('subject_id', $subject_ids)->count_all_results('subject_exams');

                $subjects[$x]['quizzes'] = $this->db->where('subject_id', $subject_ids)->count_all_results('subject_quizzes');

                $subjects[$x]['lessons'] = $this->db->where('subject_id', $subject_ids)->count_all_results('subject_lessons');

                $subjects[$x]['assignments'] = $this->db->where('subject_id', $subject_ids)->count_all_results('subject_assignments');

                $subjects[$x]['task_performances'] = $this->db->where('subject_id', $subject_ids)->count_all_results('subject_task_performances');
            }

            $grade_levels = $this->db->query("SELECT * FROM grade_level")->result_array();

            for ($x = 0; $x < count($grade_levels); $x++)
            {
                $grade_level_ids = $grade_levels[$x]['id'];

                $grade_levels[$x]['students'] = $this->db->where('grade_level_id', $grade_level_ids)->where('role', 'STUDENT')->count_all_results('users');

                $grade_levels[$x]['subjects'] = $this->db->where('grade_level_id', $grade_level_ids)->count_all_results('subject');
            }

            $departments = $this->db->query("SELECT * FROM department")->result_array();

            for ($x = 0; $x < count($departments); $x++)
            {
                $department_ids = $departments[$x]['id'];

                $departments[$x]['teachers'] = $this->db->where('department_id', $department_ids)->where('role', 'TEACHER')->count_all_results('users');
            }

            $school_years = $this->db->query("SELECT * FROM school_year")->result_array();

            for ($x = 0; $x < count($school_years); $x++)
            {
                $school_year_ids = $school_years[$x]['id'];

                $school_years[$x]['students'] = $this->db->where('school_year_id', $school_year_ids)->where('role', 'STUDENT')->count_all_results('users');

                $school_years[$x]['teachers'] = $this->db->where('school_year_id', $school_year_ids)->where('role', 'TEACHER')->count_all_results('users');
            }

            $data = array(
                'grade_levels' => $grade_levels,
                'departments' => $departments,
                'subjects' => $subjects,
                'school_years' => $school_years,
            );

            return array('status' => 1, 'data' => $data);
        }
        catch (Exception $e)
        {
            return array('status' => 0, 'message' => $e->getMessage());
        }
    }

    public function fetch_student_dashboard($params)
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

                $subjects[$x]['lessons'] = $this->db->query("SELECT * FROM subject_lessons WHERE subject_id = '$subj_ids'")->result_array();

                $subjects[$x]['assignments'] = $this->db->query("SELECT * FROM subject_assignments WHERE subject_id = '$subj_ids'")->result_array();

                $subjects[$x]['teachers'] = $this->db->query("SELECT * FROM subject_teachers WHERE subject_id = '$subj_ids'")->result_array();

                $subjects[$x]['grade'] = $this->db->query("SELECT * FROM grade_level WHERE id = '$grade_level_ids'")->row();

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

            $announcements = $this->db->query("SELECT * from announcement ORDER BY id DESC")->result_array();

            $data = array(
                'subjects' => $subjects,
                'announcements' => $announcements,
            );

            return array('status' => 1, 'data' => $data);
        }
        catch (Exception $e)
        {
            return array('status' => 0, 'message' => $e->getMessage());
        }
    }
}