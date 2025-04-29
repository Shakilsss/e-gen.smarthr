<?php
class Import extends CI_Controller {

	function __construct()
	{
		parent::__construct();
	}

	function index(){
		date_default_timezone_set('Asia/Dhaka');
		$file_name = "import/egen.txt";

		if (file_exists($file_name)){
			$lines = file($file_name);
			foreach(array_values($lines)  as $line) {
				list($id, $fn, $email, $gen, $bg, $r, $dpt, $des, $unit, $doj, $mt) = preg_split('/\t+/', trim($line));
				$data= array(
                    'punch_id'      	=> $id,
                    'employee_id'   	=> $id,
                    'office_shift_id'	=> 1,
                    'first_name'      	=> $fn,
                    'username'      	=> $email,
                    'email'      		=> $email,
                    'password'      	=> '$2y$12$gGHpt0lBhRlCyH3QCYsIz.cDAKszo.zc6vDhS6w8J0G9Z7aZkvdlO',
                    'gender'      		=> $gen,
                    'blood_group'      	=> $bg,
                    'user_role_id'     	=> $r,
                    'department_id'     => $dpt,
                    'designation_id'    => $des,
                    'company_id'      	=> $unit,
                    'date_of_joining'   => date('Y-m-d', strtotime($doj)),
                    'marital_status'    => $mt,
                );
                $this->db->insert('xin_employees', $data);
				echo "<pre>"; print_r($this->db->last_query());
			}
			exit('check');
			echo "Upload successfully done";
		} else {
			echo "File not found";
		}
	}
	function entry_email(){
		date_default_timezone_set('Asia/Dhaka');

		$file_name = "import/test.txt";
		$imdata='';

		if (file_exists($file_name)){
			$lines = file($file_name);
			foreach(array_values($lines)  as $line) {
				list($row, $id, $email) = preg_split('/\s+/', trim($line));

				$data= array(
                    'email'      => $email,
                );
                $this->db->where('user_id', $id);
                $this->db->update('xin_employees', $data);

				$pass = $this->db->select('user_password')
				->from('xin_employees')
				->where('user_id', $id)->get()->row();

				if ($pass->user_password) {
					$imdata.= $row .' '. $pass->user_password .'<br>';
				}else{
					$imdata.= $row .' '. '-' .'<br>';
				}
			};

			dd($imdata);
			echo "Upload successfully done";
		} else {
			echo "File not found";
		}
	}


}

?>
