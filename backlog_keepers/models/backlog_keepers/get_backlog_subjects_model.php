<?php if (!defined("BASEPATH")) {
    exit("No direct script access allowed");
}

class Get_backlog_subjects_model extends CI_Model {

function __construct() {
    parent::__construct();
}

function get_all_physical_registrations($params)
{
    $queryParams = array();
    $sql='SELECT admn_no, course_id, branch_id, semester, section, session_year,`session` FROM reg_regular_form WHERE admn_no=?';
    array_push($queryParams,$params['admn_no']);
    
    $result=$this->db->query($sql,$queryParams);
    return $result->result();
}
    
function get_common_sem_subjects($params) {
    $queryParams = array();
    $sql='select * 
FROM (select b.sub_code,b.sub_name,b.semester,b.`session`,b.session_year
FROM (select concat(course_component,sequence) as sub_category,cbcs_curriculam_policy_id  from cbcs_comm_coursestructure_policy where status="Mandatory" and sem=?)  a  left join cbcs_subject_offered  as b on a.sub_category=b.sub_category and a.cbcs_curriculam_policy_id=b.sub_group
where session_year=? and semester=? and sub_group=?) subject_offered 
where subject_offered.sub_code not in (
select opted.subject_code
FROM (select *
from cbcs_stu_course
where session_year=? and admn_no=? and `session`=?) opted)';

array_push($queryParams,$params->semester);
array_push($queryParams,$params->session_year);
array_push($queryParams,$params->semester);
array_push($queryParams,$params->section);
array_push($queryParams,$params->session_year);
array_push($queryParams,$params->admn_no);
array_push($queryParams,$params->session);
    
    $result=$this->db->query($sql,$queryParams);
    return $result->result();
}

function get_other_sem_subjects($params) {
    $queryParams = array();
    $sql='select * 
FROM (select b.sub_code,b.sub_name,b.semester,b.`session`,b.session_year
FROM (select concat(course_component,sequence) as sub_category1
from cbcs_coursestructure_policy
where status="Mandatory" and sem=? and course_id=?) a  left join cbcs_subject_offered  as b on a.sub_category1=b.sub_category 
where session_year=? and semester=? and branch_id=? and course_id=? and `session`=?) subject_offered1 
where sub_code not in (
select subject_code
FROM   (select *
from cbcs_stu_course
where session_year=? and admn_no=? and `session`=?) opted1)';
    
    array_push($queryParams,$params->semester);
    array_push($queryParams,$params->course_id);
    array_push($queryParams,$params->session_year);
    array_push($queryParams,$params->semester);
    array_push($queryParams,$params->branch_id);
    array_push($queryParams,$params->course_id);
    array_push($queryParams,$params->session);
    array_push($queryParams,$params->session_year);
    array_push($queryParams,$params->admn_no);
    array_push($queryParams,$params->session);
    
    $result=$this->db->query($sql,$queryParams);
    return $result->result();
}

}
