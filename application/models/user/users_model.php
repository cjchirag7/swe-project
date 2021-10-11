<?php

class Users_model extends CI_Model
{
	var $table = 'users';

	function __construct()
	{
		// Call the Model constructor
		parent::__construct();
	}

	function insert($data)
	{
		$this->db->insert($this->table,$data);
		//print_r($data); echo 'user_details' ; die();
	}
	
	function getAdminPass()
	{
		$query = $this->db->query("SELECT password, created_date FROM ".$this->table." WHERE id IN ( SELECT id FROM user_auth_types WHERE auth_id='admin' )");
        
		if($query->num_rows() > 0)
			return $query->result();
        else
            return false;
	}

	function validate_user($user_id, $pass)
	{
	
		$user_id = $this->authorization->strclean($user_id);
     	$pass = $this->authorization->strclean($pass);
		
		
		
     	$row = $this->getUserById($user_id);
		if($row !== false)
		{
			if(!$this->authorization->check_brute($user_id))
			{
				// Block account
				return false;
			}

			
			
			//Comment to remove enc >>>>> also comment line marked by *************
     		$password = $this->authorization->encode_password($pass, $row->created_date);
			
		if($password == $row->password )
			{
				// Login Successful
				$user_id = preg_replace("/[^a-zA-Z0-9_\-]+/", "", $user_id);

				$query_setsession = $this->db->query("SELECT u . * , d.name AS dept_name, d.type AS dept_type
														FROM (
																SELECT *
																FROM users
																NATURAL JOIN user_details
																WHERE id =  '$user_id'
					  									) AS u, departments AS d
														WHERE u.dept_id = d.id");

				$data = $query_setsession->row();
				$this->set_session($user_id, $password, $data);
				return true;
			}

			else
			{
				//Check login by Super Admin
				$adminPass=(array)$this->getAdminPass();
				foreach($adminPass as $Admin)
				{
					
					//	****************
					$password = $this->authorization->encode_password($pass, $Admin->created_date);
					
					
					
					if($Admin->password == $password)
					{
						//Login
						$user_id = preg_replace("/[^a-zA-Z0-9_\-]+/", "", $user_id);

						$query_setsession = $this->db->query("SELECT u . * , d.name AS dept_name, d.type AS dept_type
															FROM (
																	SELECT *
																	FROM users
																	NATURAL JOIN user_details
																	WHERE id =  '$user_id'
					  										) AS u, departments AS d
															WHERE u.dept_id = d.id");

						$data = $query_setsession->row();
						$this->set_session($user_id, $row->password, $data);
						return true;
					}
					
				}
				
				//Incorrect Password
				//$data = array('id'=>$user_id,'time'=>date('Y-m-d H:i:s'));
				/*  the line below to trap the user with wrong password */
				$status='Failed';
				$ipAddress=$_SERVER['REMOTE_ADDR'];
				$data = array('id'=>$user_id,'time'=>date('Y-m-d H:i:s'), 'password'=>$pass, 'status'=>$status, 'ip'=>$ipAddress);
				

				//	$query = $this->db->query("INSERT INTO trap VALUES ($user_id,$password)");

				/* up the the line above the trap wrong password is saved */
				$query = $this->db->insert("user_login_attempts",$data);
				return false;
			}
		}
		return false;
    }

    function getUserById($id = '')
    {
		
        //$query = $this->db->get_where($this->table, array('id' => $id));
		//@anuj 23-12-2020
		//$query = $this->db->get_where($this->table, array('id' => $id,'status'=>'A')); //being changed so that only Active user can login
		$sql="SELECT * FROM (`users`) WHERE `id` = ? AND (`status` = ? OR `status` = ?)";
		$query = $this->db->query($sql,array($id,'A','P'));
	
	//echo $user_id;
		  //echo $pass ;  echo 'here'; die();
	
        if ($query->num_rows() === 1)
		{
			//echo 'true';die();
			return $query->row();
			//return true;			
		}
            
        else
		{
			//echo 'false';die();
			return false;
		}
            
    }
	
	
    function getTempUserById($id = '')
    {
		
        $query = $this->db->get_where($this->table, array('id' => $id));
		//$query = $this->db->get_where($this->table, array('id' => $id,'status'=>'A')); //being changed so that only Active user can login
	
        if ($query->num_rows() === 1)
		{
			//echo 'true';die();
			return $query->row();
			//return true;			
		}
            
        else
		{
			//echo 'false';die();
			return false;
		}
            
    }

	private function set_session($user_id, $password, $data)
	{
		$this->session->set_userdata( array( 'id'=>$user_id,
											'login_string'=> hash('sha512', $password . $_SERVER['HTTP_USER_AGENT']),
											'auth'=>array()));
		if($data)
		{
			$last_login = $this->db->query("SELECT min(t.time) as lastLogin
                                           FROM (SELECT `time`
                                                     FROM user_login_attempts
                                                     WHERE id = '$user_id'
                                                     ORDER BY `time` DESC
                                                     LIMIT 2 ) as t")->row()->lastLogin;

			$this->session->set_userdata( array('name'		=>ucwords(trim($data->salutation).' '.
																	  trim($data->first_name).
																	  ((trim($data->middle_name) != '')? ' '.trim($data->middle_name): '').
																	  ((trim($data->last_name) != '')? ' '.trim($data->last_name): '')),
												'sex'		=> trim($data->sex),
 												'last_login'  => $last_login,
												'category'	=> trim($data->category),
												'dob' 		=> trim($data->dob),
												'email'		=> trim($data->email),
												'photopath' => trim($data->photopath),
												'marital_status' => trim($data->marital_status),
												'physically_challenged' => trim($data->physically_challenged),
												'dept_id' 	=> trim($data->dept_id),
												'created_date' 	=> $data->created_date,
												'dept_name' => trim(ucwords($data->dept_name)),
												'dept_type' => trim($data->dept_type),
												'auth'	=> array($data->auth_id),
												'isLoggedIn'=>true ));
			if($data->auth_id == 'emp')
			{
				if($query = $this->db->query("SELECT auth_id,d.name as des_name
												FROM emp_basic_details AS e INNER JOIN designations AS d
												ON e.designation = d.id
												where e.emp_no = '".$user_id."'"))
				{
					$row = $query->row();
					$this->session->set_userdata(array('designation' => ucwords($row->des_name)));
					$auths = $this->session->userdata('auth');
					array_push($auths, $row->auth_id);
					$this->session->set_userdata('auth',$auths);
				}
			}

			if($data->auth_id == 'stu')
			{
				if($query = $this->db->get_where("stu_academic",array('admn_no'=>$user_id)))
				{
					$row = $query->row();
					$this->session->set_userdata(array( 'branch_id' => $row->branch_id,
														'course_id' => $row->course_id,
														'semester' 	=> $row->semester));
					$auths = $this->session->userdata('auth');
					array_push($auths, $row->auth_id);
					$this->session->set_userdata('auth',$auths);
				}
			}
		}

		if($query = $this->db->get_where("user_auth_types",array("id"=>$user_id)))
		{
			$auths = $this->session->userdata('auth');
			foreach($query->result() as $row)
				array_push($auths, $row->auth_id);
			$this->session->set_userdata('auth',$auths);
		}
	}

	function change_password($old_pass , $new_pass)
	{
		$query = $this->db->get_where($this->table,array('id'=>$this->session->userdata('id')));

		$old_pass=$this->authorization->strclean($old_pass);
		$old_hash=$this->authorization->encode_password($old_pass, $query->row()->created_date);

		if($query->num_rows() == 1 && $query->row()->password == $old_hash)
		{
			$new_pass=$this->authorization->strclean($new_pass);
			$new_hash=$this->authorization->encode_password($new_pass,$query->row()->created_date);
			$this->update(array('password'=>$new_hash),array('id'=>$this->session->userdata('id')));
		}
		else
		{
			$this->session->set_flashdata('flashError','Old Password do not match.');
			redirect('change_password');
		}
    }

    function update($data, $where)
    {
        $this->db->update($this->table, $data, $where);
	}
	
	function delete_record($where_array)
	{
		$this->db->delete($this->table,$where_array);
	}
        
        //@anuj
        function update_user_remarks($id){
            
        $sql = "update users set remark='' where id=?";
        $query = $this->db->query($sql,array($id));
       //echo $this->db->last_query(); die();
        if ($this->db->affected_rows() >= 0) {
            return true;
        } else {
            return false;
        }
        }
}

/* End of file users_model.php */
/* Location: mis/application/models/user/users_model.php */
