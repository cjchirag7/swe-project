<?php
/*
 * Generated by CRUDigniter v3.2
 * www.crudigniter.com
 */

class acc_gpf_report_model extends CI_Model
{
    function __construct()
    {
        parent::__construct();
    }
    /* This method gets months of a given financial year*/
    function getMonths($fy,$type){
    	//echo $type;die();
    	if(strcmp($type,'T')==0){
    		 $cond=" where date(concat(2000+a.YR,'-',a.MON,'-01')) between DATE_ADD(?, INTERVAL 1 MONTH) and DATE_ADD(?, INTERVAL 1 MONTH)";
    	}
    	else{
    		$cond="where date(concat(2000+a.PREVYR,'-',a.PREVMON,'-01')) between ? and ?";
    	}
    	$fy=$fy[0];
        $fy['start_from']=date('Y-m-d',strtotime($fy['start_from']));
        $fy['end_to']=date('Y-m-d',strtotime($fy['end_to']));
        $q="SELECT distinct(a.MON),a.YR,a.PREVMON,a.PREVYR FROM acc_gpf_master a ";
        $q.=$cond;
        if($query=$this->db->query($q,array($fy['start_from'],$fy['end_to']))){
        	if($query->num_rows()>0){
        		return $query->result();
                #return $this->db->last_query();
        	}
        	else{
        		return false;
        	}
        }
        else{
        	return false;
        }
    }

    function getGpfDetails($type,$fy,$MON){

        $tempfy= explode('-', $fy);
        $fy1=substr($tempfy[0], -2);
        $fy2=$tempfy[1];


        if($type=='T'){
        if($MON>=4 and $MON<=12){
            $final_fy=$fy1;
        }
        if($MON>=1 and $MON<=3){
            $final_fy=$fy2;
        }
        $myquery = "select a.* from acc_gpf_master a where a.YR=? and a.MON=?";
        }
        else if($type=='P'){
            if($MON>=4 and $MON<=12){
            $final_fy=$fy1;
        }
        if($MON>=1 and $MON<=3){
            $final_fy=$fy2;
        }
        $myquery = "select a.* from acc_gpf_master a where a.PREVYR=? and a.PREVMON=?";
        }
        $query = $this->db->query($myquery, array($final_fy,$MON));
       # echo $this->db->last_query();die();
        if ($query->num_rows() > 0) {
            return $query->result();
        } else {
            return false;
        }














//    	if(strcmp($type,'T')==0){
//    		 $cond="date(concat(2000+.YR,'-',x.MON,'-01')) between DATE_ADD(?, INTERVAL 1 MONTH) and DATE_ADD(?, INTERVAL 1 MONTH)";
//    	}
//    	else{
//    		$cond="date(concat(2000+.YR,'-',x.MON,'-01')) between ? and ?";
//    	}
//    	$fy=$fy[0];
//        $fy['start_from']=date('Y-m-d',strtotime($fy['start_from']));
//        $fy['end_to']=date('Y-m-d',strtotime($fy['end_to']));
//        //var_dump($fy);die();
//    	$q="SELECT agm.*,aga.GPFNO, UCASE(CONCAT(ud.first_name,' ',ud.middle_name,' ',ud.last_name)) AS NAME, (
//            SELECT dpt.name
//            FROM departments dpt
//            WHERE dpt.id=ud.dept_id) AS DEPT
//            FROM acc_gpf_master agm
//            LEFT JOIN user_details ud ON ud.id=agm.EMPNO
//            LEFT JOIN acc_gpf_account aga ON aga.EMPNO=agm.EMPNO
//            WHERE $cond between DATE_ADD(?, INTERVAL 1 MONTH)   and DATE_ADD(?, INTERVAL 1 MONTH) ";


    }

    function get_gpf_details_to_show_calculation_by_fy($fy,$MON){
    	$fy=$fy[0];
    	$fy['start_from']=date('Y-m-d',strtotime($fy['start_from']));
    	$fy['end_to']=date('Y-m-d',strtotime($fy['end_to']));
    	//var_dump($fy);die();
        //echo $fy['start_from']; echo $fy['end_to'];die();

        if($MON=='All'){
            $q="SELECT A.*,concat_ws(' ',B.first_name,B.middle_name,B.last_name)as empname,C.name
FROM (
SELECT x.*,(
SELECT y.rate
FROM acc_gpf_interest_rate y
WHERE y.date_from<= DATE(CONCAT(2000+x.YR,'-',x.MON,'-','01')) AND y.date_to>= DATE(CONCAT(2000+x.YR,'-',x.MON,'-','01'))) AS rate
FROM acc_gpf_master x
WHERE DATE(CONCAT(2000+x.YR,'-',x.MON,'-01')) BETWEEN DATE_ADD(?, INTERVAL 1 MONTH) AND DATE_ADD(?, INTERVAL 1 MONTH)
ORDER BY DATE(CONCAT(2000+x.YR,'-',x.MON,'-','01')))A
left join user_details B on B.id=A.EMPNO
left join departments C on C.id=B.dept_id";
    	if($query=$this->db->query($q,array($fy['start_from'],$fy['end_to']))){
    		if($query->num_rows()>0){
                	return $query->result();
    		}
    		else{
    			return false;
    		}
    	}
    	else{
    		return false;
    	}


        }else{
            $q="SELECT A.*,concat_ws(' ',B.first_name,B.middle_name,B.last_name)as empname,C.name
FROM (
SELECT x.*,(
SELECT y.rate
FROM acc_gpf_interest_rate y
WHERE y.date_from<= DATE(CONCAT(2000+x.YR,'-',x.MON,'-','01')) AND y.date_to>= DATE(CONCAT(2000+x.YR,'-',x.MON,'-','01'))) AS rate
FROM acc_gpf_master x
WHERE DATE(CONCAT(2000+x.YR,'-',x.MON,'-01')) BETWEEN DATE_ADD(?, INTERVAL 1 MONTH) AND DATE_ADD(?, INTERVAL 1 MONTH)
ORDER BY DATE(CONCAT(2000+x.YR,'-',x.MON,'-','01')))A
left join user_details B on B.id=A.EMPNO
left join departments C on C.id=B.dept_id
WHERE A.Mon=?";
    	if($query=$this->db->query($q,array($fy['start_from'],$fy['end_to'],$MON))){
    		if($query->num_rows()>0){
                	return $query->result();
    		}
    		else{
    			return false;
    		}
    	}
    	else{
    		return false;
    	}

    }
        }

// this function returns employee number of all employees in a given Financial Year

    function getEMPNOFYWise($fy){
        $fy=$fy[0];
        $fy['start_from']=date('Y-m-d',strtotime($fy['start_from']));
        $fy['end_to']=date('Y-m-d',strtotime($fy['end_to']));
        $q="SELECT distinct(agm.EMPNO) as EMPNO
            FROM acc_gpf_master agm
            WHERE DATE(CONCAT(2000+agm.YR,'-',agm.MON,'-01')) BETWEEN DATE_ADD(?, INTERVAL 1 MONTH) AND DATE_ADD(?, INTERVAL 1 MONTH)";
        if($query=$this->db->query($q,array($fy['start_from'],$fy['end_to']))){
            if($query->num_rows()>0){
                return $query->result();
            }
            else{
                return false;
            }
        }
        else{
            return false;
        }
    }

    function getOpeningBalanceForIncividual($fy,$EMPNO){
        //var_dump($fy);die();
        $q="SELECT agm.OPBALANCE
            FROM acc_gpf_master agm
            WHERE DATE(CONCAT(2000+agm.YR,'-',agm.MON,'-01')) BETWEEN DATE_ADD(?, INTERVAL 1 MONTH) AND DATE_ADD(?, INTERVAL 1 MONTH) AND agm.EMPNO=?
            ORDER BY DATE(CONCAT(2000+agm.YR,'-',agm.MON,'-','01')) LIMIT 1";
        if($query=$this->db->query($q,array($fy[0]['start_from'],$fy[0]['end_to'],$EMPNO))){
            if($query->num_rows()>0){
                return $query->result();
            }
            else{
                return false;
            }
        }
        else{
            return false;
        }
    }

    function getAllSumDetails($fy,$EMPNO){
        //var_dump($fy);die();
        $q="SELECT UCASE(CONCAT(ud.first_name,' ',ud.middle_name,' ',ud.last_name)) AS NAME,aga.GPFNO as GPFNO, UCASE(dpt.name) AS DEPT, UCASE(dsg.name) AS DESIG, SUM(agm.PFSUB) AS PFSUB, SUM(agm.ADVREF) AS ADVREF, SUM(agm.ADVWT) AS ADVWT,(select agi.INTEREST from acc_gpf_interest agi where agi.FY=? and agi.EMPNO=?) as INTEREST
            FROM acc_gpf_master agm
            LEFT JOIN user_details ud ON ud.id=agm.EMPNO
            LEFT JOIN departments dpt ON dpt.id=ud.dept_id
            LEFT JOIN emp_basic_details ebd ON ebd.emp_no=agm.EMPNO
            LEFT JOIN designations dsg ON dsg.id=ebd.designation
            LEFT JOIN acc_gpf_account aga on aga.EMPNO=agm.EMPNO
            WHERE DATE(CONCAT(2000+agm.YR,'-',agm.MON,'-01')) BETWEEN DATE_ADD(?, INTERVAL 1 MONTH) AND DATE_ADD(?, INTERVAL 1 MONTH) AND agm.EMPNO=?
            ORDER BY DATE(CONCAT(2000+agm.YR,'-',agm.MON,'-','01'))";
            if($query=$this->db->query($q,array($fy[0]['fy'],$EMPNO,$fy[0]['start_from'],$fy[0]['end_to'],$EMPNO))){
                if($query->num_rows()>0){
					//echo $this->db->last_query();die();
                    return $query->result();

                }
                else{
                    return false;
                }
            }
            else{
                return false;
            }
    }

    function getEmpNoForBP($data){
        //var_dump($data['fy_details']);die();
        #$q="select agm.EMPNO from acc_gpf_master agm,(select * from acc_fyear_details a where a.fy=?) as fy  where date(concat(2000+agm.yr,'-',agm.MON,'-01')) between fy.start_from and fy.end_to group by agm.EMPNO order by cast(agm.EMPNO as INT)  LIMIT ? OFFSET ?";
  # 03-04-19 $q="select agm.EMPNO from acc_gpf_master agm,(select * from acc_fyear_details a where a.fy=?) as fy,(SELECT * FROM acc_gpf_account b WHERE b.GPFNO not like 'C-%') AS gpf  where date(concat(2000+agm.yr,'-',agm.MON,'-01')) between fy.start_from and fy.end_to AND agm.EMPNO=gpf.EMPNO group by agm.EMPNO order by cast(agm.EMPNO as INT)  LIMIT ? OFFSET ?";
$q="SELECT agm.EMPNO,aga.GPFNO
FROM acc_gpf_master agm inner join acc_gpf_account aga on aga.EMPNO=agm.EMPNO,(
SELECT *
FROM acc_fyear_details a
WHERE a.fy=?) AS fy,(
SELECT *
FROM acc_gpf_account b
WHERE b.GPFNO NOT LIKE 'C-%') AS gpf
WHERE DATE(CONCAT(2000+agm.yr,'-',agm.MON,'-01')) BETWEEN fy.start_from AND fy.end_to AND agm.EMPNO=gpf.EMPNO
GROUP BY agm.EMPNO
ORDER BY aga.GPFNO
LIMIT ? OFFSET ?";
        if($query=$this->db->query($q,array($data['FY'],$data['limit'],$data['offset']))){
            if($query->num_rows()>0){
                return $query->result();
            }
            else{
                return false;
            }
        }
        else{
            return false;
        }
    }

     function getEmpNoMergedForBP($data){
        //var_dump($data['fy_details']);die();
    // 03-04-19    $q="select agm.EMPNO from acc_gpf_master agm,(select * from acc_fyear_details a where a.fy=?) as fy,(SELECT * FROM acc_gpf_account b WHERE b.GPFNO like 'C-%') AS gpf  where date(concat(2000+agm.yr,'-',agm.MON,'-01')) between fy.start_from and fy.end_to AND agm.EMPNO=gpf.EMPNO group by agm.EMPNO order by cast(agm.EMPNO as INT)  LIMIT ? OFFSET ?";
$q="SELECT agm.EMPNO,aga.GPFNO
FROM acc_gpf_master agm inner join acc_gpf_account aga on aga.EMPNO=agm.EMPNO,(
SELECT *
FROM acc_fyear_details a
WHERE a.fy=?) AS fy,(
SELECT *
FROM acc_gpf_account b
WHERE b.GPFNO LIKE 'C-%') AS gpf
WHERE DATE(CONCAT(2000+agm.yr,'-',agm.MON,'-01')) BETWEEN fy.start_from AND fy.end_to AND agm.EMPNO=gpf.EMPNO
GROUP BY agm.EMPNO
ORDER BY aga.GPFNO
LIMIT ? OFFSET ?";

        if($query=$this->db->query($q,array($data['FY'],$data['limit'],$data['offset']))){
            if($query->num_rows()>0){
                return $query->result();
            }
            else{
                return false;
            }
        }
        else{
            return false;
        }
    }

    function getGPFDetils($data,$EMPNO){
        $q="SELECT agm.*,(
SELECT y.rate
FROM acc_gpf_interest_rate y
WHERE y.date_from<= DATE(CONCAT(2000+agm.YR,'-',agm.MON,'-','01')) AND y.date_to>= DATE(CONCAT(2000+agm.YR,'-',agm.MON,'-','01'))) AS rate,
(select agi.INTEREST from acc_gpf_interest agi where agi.FY=? and agi.EMPNO=?) as ANNUAL_INTEREST FROM acc_gpf_master agm,
(SELECT * FROM acc_fyear_details a  WHERE a.fy=?) AS fy
WHERE agm.EMPNO=? AND DATE(CONCAT(2000+agm.yr,'-',agm.MON,'-01')) BETWEEN DATE_ADD(fy.start_from, INTERVAL 1 MONTH) AND DATE_ADD(fy.end_to, INTERVAL 1 MONTH)
ORDER BY DATE(CONCAT(2000+agm.YR,'-',agm.MON,'-','01'))";
        if($query=$this->db->query($q,array($data['FY'],$EMPNO,$data['FY'],$EMPNO))){
            if($query->num_rows()>0){
				#echo $this->db->last_query();die();
                return $query->result_array();
            }
            else{
                return false;
            }
        }
        else{
            return false;
        }
    }
    function getEmpDetails($EMPNO){
        $q="SELECT  ebd.emp_no as EMPNO,UCASE(CONCAT(ud.salutation,' ',ud.first_name,' ',ud.middle_name,' ',ud.last_name)) AS NAME,ucase(dpt.name) as DEPT,ucase(dsg.name) as DESIG,aga.GPFNO as GPFNO
            FROM user_details ud
            inner join emp_basic_details ebd on ebd.emp_no=ud.id
            inner join departments dpt on dpt.id=ud.dept_id
            inner join designations dsg on dsg.id=ebd.designation
            inner join acc_gpf_account aga on aga.EMPNO=ud.id
            where ud.id=?";
        if($query=$this->db->query($q,array($EMPNO))){
            if($query->num_rows()>0){
                return $query->result_array();
            }
            else{
                return false;
            }
        }
        else{
            return false;
        }
    }
}
