<?php

if ( ! defined('BASEPATH'))
    exit('No direct script access allowed');
    
    class Get_backlog_subjects extends MY_Controller {
        function __construct() {
            parent::__construct(array('emp'));
            $this->load->model ('backlog_keepers/get_backlog_subjects_model');
        }
        
        function index(){
            if($_SERVER['REQUEST_METHOD'] == 'POST')
            {
                $data['submitted']=TRUE;
                $data['admn_no'] = $this->input->post('admn_no');
                $data['physical_registrations']=$this->get_backlog_subjects_model->get_all_physical_registrations($data);
                $data['subjects']=array();
                if(count($data['physical_registrations'])==0)
                    $data['invalid_admn_no']=TRUE;
                foreach($data['physical_registrations'] as $reg)
                {
                    $section='section';
                    $subject_list=array();
                    if($reg->$section=='1' && $reg->$section=='2')
                        $subject_list=$this->get_backlog_subjects_model->get_common_sem_subjects($reg);
                    else
                        $subject_list=$this->get_backlog_subjects_model->get_other_sem_subjects($reg);
                    foreach($subject_list as $subject)
                        array_push($data['subjects'],$subject);
                }
            }
            $this->drawHeader('Backlog keepers');
            $this->load->view('backlog_keepers/get_backlog_subjects',$data);
            $this->drawFooter();
        }
        
    }
    
    
    