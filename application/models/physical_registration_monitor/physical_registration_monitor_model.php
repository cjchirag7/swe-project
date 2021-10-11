<?php if (!defined("BASEPATH")) {
    exit("No direct script access allowed");
}

class Physical_registration_monitor_model extends CI_Model {

function __construct() {
    parent::__construct();
}

function get_session_year_list(){
	$sql="select a.session_year from mis_session_year a order by a.id desc ";
	$result=$this->db->query($sql);
	return $result->result();
}

function get_session_list(){
	$sql="select a.`session` from mis_session a";
	$result=$this->db->query($sql);
	return $result->result();
}

function get_department_list(){
	$sql="select a.id, a.name from departments a where a.`type`='academic'";
	$result=$this->db->query($sql);
	return $result->result();
}

function get_branch_list(){
    $sql="select a.id, a.name from cs_branches a";
    $result=$this->db->query($sql);
    return $result->result();
}

function get_course_list(){
    $sql="select a.id, a.name from cs_courses a";
    $result=$this->db->query($sql);
    return $result->result();
}

function get_semester_list(){
    $sql="select distinct a.`semester` from reg_regular_form a where a.semester <> '' order by a.semester desc";
    $result=$this->db->query($sql);
    return $result->result();
}

function get_summary($params) {
    $queryParams = array();
    $sql="SELECT t.fee_status,t.physical_registration_status,COUNT(*) AS cnt FROM(
SELECT a.admn_no,a.course_id,a.branch_id,dc.dept_id,semester,
	CASE when DATEDIFF(b.fee_date,'0000-00-00')=0
		then false
		when b.fee_date IS NULL
		then false
		else true
	END AS fee_status,
CASE when a.status='1'
		then true
		else false
	END AS physical_registration_status
FROM reg_regular_form a
LEFT JOIN reg_regular_fee b
ON a.form_id=b.form_id
LEFT JOIN course_branch cb ON a.course_id=cb.course_id AND a.branch_id=cb.branch_id
LEFT JOIN (SELECT DISTINCT course_branch_id,dept_id FROM dept_course) dc ON dc.course_branch_id=cb.course_branch_id
WHERE 1 ";
    if($params['session_year']!='All')
    {
        $sql.=" AND a.session_year=? ";
        array_push($queryParams,$params['session_year']);
    }
    if($params['session']!='All')
    {
        $sql.=" AND a.session=? ";
        array_push($queryParams,$params['session']);
    }
    if($params['course']!='All')
    {
        $sql.=" AND a.course_id=? ";
        array_push($queryParams,$params['course']);
    }
    if($params['branch']!='All')
    {
        $sql.=" AND a.branch_id=? ";
        array_push($queryParams,$params['branch']);
    }
    if($params['semester']!='All')
    {
        $sql.=" AND a.semester=? ";
        array_push($queryParams,$params['semester']);
    }
    if($params['department']!='All')
    {
        $sql.=" AND dc.dept_id=? ";
        array_push($queryParams,$params['department']);
    }
$sql.=") t
GROUP BY t.fee_status , t.physical_registration_status
";  
    $result=$this->db->query($sql,$queryParams);
    $summary=array();
    foreach($result->result() as $row)
    {
        if($row->fee_status==0 && $row->physical_registration_status==0)
        {
            array_push($summary,array('category'=>'Fee not paid ','count'=>$row->cnt));      
        }
        else if($row->fee_status==1 && $row->physical_registration_status==0)
        {
            array_push($summary,array('category'=>'Fee paid but physical registration not completed ','count'=>$row->cnt));      
        }
        else if($row->fee_status==1 && $row->physical_registration_status==1)
        {
            array_push($summary,array('category'=>'Completed physical registration ','count'=>$row->cnt));      
        }
    }
    return $summary;
}

function get_details($params) {
    $queryParams = array();
    $sql="select a.admn_no,b.`name` AS branch,c.`name` AS course,d.`name` AS department,a.semester,
	CASE when f.fee_date ='0000-00-00'
		then false
		when f.fee_date IS NULL
		then false
		else true
	END AS fee_status,
	CASE when a.status='1'
		then true
		else false
	END AS physical_registration_status
FROM reg_regular_form a
LEFT JOIN reg_regular_fee f
ON a.form_id=f.form_id
LEFT JOIN cs_branches b
ON a.branch_id=b.id
LEFT JOIN cs_courses c
ON a.course_id=c.id
LEFT JOIN course_branch cb ON a.course_id=cb.course_id AND a.branch_id=cb.branch_id
LEFT JOIN  (SELECT DISTINCT course_branch_id,dept_id FROM dept_course) dc ON dc.course_branch_id=cb.course_branch_id
LEFT JOIN departments d ON d.id=dc.dept_id     
WHERE 1 ";
    if($params['session_year']!='All')
    {
        $sql.=" AND a.session_year=? ";
        array_push($queryParams,$params['session_year']);
    }
    if($params['session']!='All')
    {
        $sql.=" AND a.session=? ";
        array_push($queryParams,$params['session']);
    }
    if($params['course']!='All')
    {
        $sql.=" AND a.course_id=? ";
        array_push($queryParams,$params['course']);
    }
    if($params['branch']!='All')
    {
        $sql.=" AND a.branch_id=? ";
        array_push($queryParams,$params['branch']);
    }
    if($params['semester']!='All')
    {
        $sql.=" AND a.semester=? ";
        array_push($queryParams,$params['semester']);
    }
    if($params['department']!='All')
    {
        $sql.=" AND d.id=? ";
        array_push($queryParams,$params['department']);
    }
    $sql.=" ORDER BY physical_registration_status, fee_status DESC";

    $result=$this->db->query($sql,$queryParams);
    return $result->result();
}

}
