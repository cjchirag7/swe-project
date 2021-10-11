<?php 

if ( ! defined('BASEPATH'))
    exit('No direct script access allowed');

    class Analyze_report extends MY_Controller {
        function __construct() {
            parent::__construct(array('emp'));
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
            $this->addCSS("physical_registration_monitor/buttons.dataTables.min.css");
            $this->addCSS("physical_registration_monitor/searchPanes.dataTables.min.css");
            $this->addCSS("physical_registration_monitor/select.dataTables.min.css");
            $this->addJS("physical_registration_monitor/chart_loader.js");
            $this->addJS("physical_registration_monitor/dataTables.buttons.min.js");
            $this->addJS("physical_registration_monitor/dataTables.searchPanes.min.js");
            $this->addJS("physical_registration_monitor/dataTables.select.min.js");
            $this->addJS("physical_registration_monitor/jszip.min.js");
            $this->addJS("physical_registration_monitor/pdfmake.min.js");
            $this->addJS("physical_registration_monitor/vfs_fonts.js");
            $this->addJS("physical_registration_monitor/buttons.html5.min.js");
            $this->addJS("physical_registration_monitor/buttons.print.min.js");
            $this->drawHeader('Physical Registration Monitor');
            $this->load->view('physical_registration_monitor/query_form',$data);
            $this->drawFooter();
        }
        
    }
    
    
