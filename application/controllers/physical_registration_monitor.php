<?php 

if ( ! defined('BASEPATH'))
    exit('No direct script access allowed');

    class Physical_Registration_Monitor extends MY_Controller {
        function __construct() {
            parent::__construct();
            $this->load->model ('physical_registration_monitor/physical_registration_monitor_model');
        }
        
        function index(){   
            if($_SERVER['REQUEST_METHOD'] == 'POST')
            {
                $data['submitted']=TRUE;
                $data['session'] = $this->input->post('session');
                $data['session_year'] = $this->input->post('session_year');
                $data['branch'] = $this->input->post('branch');
                $data['course'] = $this->input->post('course');
                $data['department'] = $this->input->post('department');
                $data['semester'] = $this->input->post('semester');
                if($data['session']=='All' && $data['session_year']=='All' && $data['branch']=='All'
                    && $data['department'] =='All' && $data['course']=='All' && $data['semester']=='All')
                {
                    $data['no_select']=TRUE;
                }
                else 
                {
                    $data['summary']=$this->physical_registration_monitor_model->get_summary($data);
                    $data['details']=$this->physical_registration_monitor_model->get_details($data);
                }
            }
            $data['sessionYears']=$this->physical_registration_monitor_model->get_session_year_list();
            $data['sessions']=$this->physical_registration_monitor_model->get_session_list();
            $data['branches']=$this->physical_registration_monitor_model->get_branch_list();
            $data['courses']=$this->physical_registration_monitor_model->get_course_list();
            $data['departments']=$this->physical_registration_monitor_model->get_department_list();
            $data['semesters']=$this->physical_registration_monitor_model->get_semester_list();
            $this->drawHeader('Physical Registration Monitor');
            $this->load->view('physical_registration_monitor/query_form',$data);
            $this->drawFooter();
        }
        
    }
    
    
