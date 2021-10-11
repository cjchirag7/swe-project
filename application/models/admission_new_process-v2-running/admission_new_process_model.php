<?php

class Admission_new_process_model extends CI_Model {

    function __construct() {
        parent::__construct();
    }

        
    function get_student_list()
    {
     
       $sql="select b.admission_no,a.* from temp_jee_users a
inner join jee_new_admission_number b on b.id=a.id
where b.admission_no in('18JE0989','18JE0990','18JE0972')
order by b.admission_no";
        $query = $this->db->query($sql);

       
        if ($this->db->affected_rows() > 0) {
            return $query->result();
        } else {
            return false;
        }
    }
    
    
    function get_education_details_list($id){
               $sql="select * from temp_jee_users_educational_details where temp_jee_users_id=?";
        $query = $this->db->query($sql,array($id));

       
        if ($this->db->affected_rows() > 0) {
            return $query->result();
        } else {
            return false;
        }
    }
    
    //=====================MTECH Starts==================
    function get_student_list_mtech(){
        $sql="select b.admission_no,a.* from temp_mtech_users a
inner join mtech_new_admission_number b on b.id=a.id
order by b.admission_no";
        $query = $this->db->query($sql);

       
        if ($this->db->affected_rows() > 0) {
            return $query->result();
        } else {
            return false;
        }
    }
    function get_education_details_list_mtech($id){
        $sql="select * from temp_mtech_users_educational_details where temp_mtech_users_id=?";
        $query = $this->db->query($sql,array($id));
        if ($this->db->affected_rows() > 0) {
            return $query->result();
        } else {
            return false;
        }
    }
    //=====================MTECH Ends==================
    //===================MSC Starts=====================
    function get_student_list_msc(){
         $sql="select b.admission_no,a.* from temp_msc_users a
inner join msc_new_admission_number b on b.id=a.id
order by b.admission_no limit 1";
        $query = $this->db->query($sql);

       
        if ($this->db->affected_rows() > 0) {
            return $query->result();
        } else {
            return false;
        }
    }
    function get_education_details_list_msc(){
        $sql="select * from temp_msc_users_educational_details where temp_msc_users_id=?";
        $query = $this->db->query($sql,array($id));

       
        if ($this->db->affected_rows() > 0) {
            return $query->result();
        } else {
            return false;
        }
    }
    
    
    //===================MSC ENDS========================
    
    function insert_student_status($data)
	{
		if($this->db->insert('jee_test_error',$data))
			return TRUE;
		else
			return FALSE;
	}
    
        function insert_enroll_passout($data){
            if($this->db->insert('stu_enroll_passout',$data))
			return TRUE;
		else
			return FALSE;
        }
        
        function insert_batch_certificate($data)
	{
		if($this->db->insert_batch('stu_prev_certificate',$data))
			return TRUE;
		else
			return FALSE;
	}

}

?>