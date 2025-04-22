<?php

defined('BASEPATH') or exit('No direct script access allowed');

require_once APPPATH . 'libraries/API_Controller.php';

class Leave extends API_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->helper('api_helper');
        $this->load->model("Timesheet_model");
		$this->load->library('upload');
    }
    /**
     * demo method
     *
     * @link [api/user/demo]
     * @method POST
     * @return Response|void
     */
    public function test()
    {
        $authorization = $this->input->get_request_header('Authorization');
        $user_info = api_auth($authorization);
        if ($user_info['status'] == true) {
            echo json_encode($user_info);
            exit();




        } else {
            echo json_encode($user_info);
            exit();
        }
    }
    public function index()
    {
        $authorization = $this->input->get_request_header('Authorization');
        $user_info = api_auth($authorization);
        if ($user_info['status'] == true) {
            $user_data=$user_info['user_info'];                 
            $userid=$user_data->user_id;
            $this->db->select("*");
            $this->db->where("employee_id", $userid);
            $this->db->order_by("from_date", "desc");
            $data['leavedata'] = $this->db->get('xin_leave_applications')->result();   
            
       

            $userid  = $user_data->user_id;
            $unit_id  = $user_data->unit_id;


            $date = date('Y-m-01');
            $date1 = date('Y-12-31');
            $latededuct = 0;


            $this->db->select("
            SUM(CASE WHEN leave_type = 'cl' THEN qty ELSE 0 END) AS cl,
            SUM(CASE WHEN leave_type = 'sl' THEN qty ELSE 0 END) AS sl
        ");
        $this->db->where('employee_id', $userid)->where('status', 2);
        $this->db->where('to_date >=', date('Y-01-01'));
        $this->db->where('to_date <=', date('Y-12-31'));
        $used_leave = $this->db->get('xin_leave_applications')->row();
        
    
            $gearn = $this->db->where('type', 'cl')->get('xin_leave_type')->row()->days_per_year;
            $gsick = $this->db->where('type', 'sl')->get('xin_leave_type')->row()->days_per_year;
    
            $this->db->where('month >=', $date)->where('month <=', $date1);
            $query = $this->db->where('emp_id', $userid)->get('leave_late_deduct');
            if ($query->num_rows() > 0) {
                $latededuct = $query->num_rows();
            }
    
            $earn = $gearn - $used_leave->cl - $latededuct;
            $sick = $gsick - $used_leave->sl;

            $rl_rule = $this->db->where('status', 1)->get('leave_settings')->row()->replace_leave;
            $nfdate = date('Y-m-01', strtotime('-1 months'));
            $nsdate = date('Y-m-t', strtotime($nfdate));
    
            $this->db->select("SUM(CASE WHEN e_status='Present' AND status='Off Day' THEN 1 ELSE 0 END) AS rl");
            $this->db->where('employee_id', $userid);
            $this->db->where('e_status', 'Present');
            $this->db->where('status', 'Off Day');
            $this->db->where('attendance_date >=', $nfdate);
            $this->db->where('attendance_date <=', $nsdate);
            $query = $this->db->get('xin_attendance_time')->row();
            if (!empty($query) && $query->rl >= $rl_rule) {
                $rlv = floor($query->rl / $rl_rule);
            } else {
                $rlv = 0;
            }

            $data['leave_cal']=array(
                'available_earn_leave'=>$earn,
                'available_sick_leave'=>$sick,
                'available_replacement_leave'=>$rlv

            );
         
            $data['leave_type']=array(
                'Earn_leave'=>1,
                'Sick_leave'=>2,
                'Replacement'=>3
            );

            $this->api_return([
                'status'    =>  true,
                'message'    =>  'successful',
                'data'       =>  $data,
            ], 200);
           
        } else {
            $this->api_return([
                'status' => false,
                'message' => 'Unauthorized User',
                'data' => [],
            ], 401);
        }
    }
    public function add_leave()
    {
        $authorization = $this->input->get_request_header('Authorization');
        $user_info = api_auth($authorization);
        if ($user_info['status'] == true) {
            $user_data=$user_info['user_info'];
            $userid=$user_data->user_id;
            $start_date = $this->input->post('start_date');
		$end_date = $this->input->post('end_date');
		$remarks = $this->input->post('remarks');
		$emp_id = $this->input->post('employee_id');
		// validate apply date check
		if($start_date == '' || $end_date == ''){
			$this->api_return([
				'status' => false,
				'message' => 'Please select from & to date.',
				'data' => [],
			], 200);
		}
		$prev_day = date('Y-m-d', strtotime('-1 days'. $start_date));
		$next_day = date('Y-m-d', strtotime('+1 days'. $end_date));

		// validate leave type
		if($this->input->post('leave_type') === '') {
			$this->api_return([
				'status' => false,
				'message' => 'Please select leave type.',
				'data' => [],
			], 200);
		}
		//get leave date of a employee ...
		$leave_date = $this->db->select('*')->where('status !=',3)->where('employee_id',$emp_id)->get('xin_leave_applications')->result();
		//check duplicate leave date
		foreach($leave_date as $date){
			if($date->from_date == $start_date || $date->to_date == $end_date) {
				$this->api_return([
					'status' => false,
					'message' => 'Leave date already exists.',
					'data' => [],
				], 200);
			}
		};

		$datetime1 = new DateTime($this->input->post('start_date'));
		$datetime2 = new DateTime($this->input->post('end_date'));
		$interval = $datetime1->diff($datetime2);
		$no_of_days = $interval->format('%a') + 1;
		// check half day leave
		if($this->input->post('leave_half_day') == 1 && $no_of_days > 1 ) {
			$this->api_return([
				'status' => false,
				'message' => 'Please select only one day for half day leave.',
				'data' => [],
			], 200);
		}
		//  half day leave set
		if($this->input->post('leave_half_day') == 1 && $no_of_days == 1 ) {
			$no_of_days = 0.5;
		}
		// half day leave yes or no
		if($this->input->post('leave_half_day') != 1){
			$leave_half_day_opt = 0;
		} else {
			$leave_half_day_opt = $this->input->post('leave_half_day');
		}

		$lt = 'rl';
		$type_name = " Replacement leave";
		if ($this->input->post('leave_type') == 2) {
			$type_name = " Sick leave";
			$lt = 'sl';
		} else {
			$type_name = " Casual leave";
			$lt = 'cl';
		}

		// check balance
		$total = $this->cal_emp_leave($emp_id, $lt);
		if($total < $no_of_days){
			$this->api_return([
				'status' => false,
				'message' => 'You have only '.$total.' '.$type_name.' left.',
				'data' => [],
			], 200);
		}

		// attachment upload

		if (isset($_POST['attachment'])) {
            // Get the base64 encoded image string
            $base64String = $_POST['attachment'];
            // dd($base64String);
            // Extract file type from base64 string
            preg_match('/^data:image\/(.*);base64,/', $base64String, $output_array);      
            // dd($output_array);
            $fileExtension = $output_array[1];
            // Remove data:image/...;base64, from the beginning of the string
            $base64String = preg_replace('/^data:image\/(.*);base64,/', '', $base64String);
            // Decode the base64 string
            $imageData = base64_decode($base64String);
            // Generate a unique filename for the image
            $filename = 'image_' . time() . '.' . $fileExtension;
            // Specify the path where you want to save the image
            $imagePath = FCPATH . 'uploads/leave/' . $filename;
            // Save the image to the specified path
            file_put_contents($imagePath, $imageData);
            $fileLocation = 'uploads/leave/' . $filename;
        } else {
            $fileLocation = '';
        }


		$data = array(
			'employee_id' => $this->input->post('employee_id'),
			'company_id' => $this->input->post('company_id'),
			'leave_type_id' => $this->input->post('leave_type'),
			'leave_type' => $lt,
			'from_date' => $this->input->post('start_date'),
			'to_date' => $this->input->post('end_date'),
			'applyed_from_date' => $this->input->post('start_date'),
			'applyed_to_date' => $this->input->post('end_date'),
			'applied_on' => date('Y-m-d h:i:s'),
			'reason' => $this->input->post('remarks'),
			'qty' => $no_of_days,
			'leave_attachment' => $fileLocation,
			'status' => '1',
			'notify_leave' => '1',
			'is_half_day' => $leave_half_day_opt,
			'created_at' => date('Y-m-d h:i:s'),
			'current_year' => date('Y'),
		);
		$result = $this->Timesheet_model->add_leave_record($data);

		if ($result == TRUE) {
			$this->api_return([
				'status' => true,
				'message' => 'Successfully Added',
				'data' => [],
			], 200);
		} else {
			$this->api_return([
				'status' => false,
				'message' => 'There is an error',
				'data' => [],
			], 200);
		}
        }else{
            $this->api_return([
                'status' => false,
                'message' => 'Unauthorized User',
                'data' => [],
            ], 401);
        }
    
    }
    function cal_emp_leave($emp_id, $type) {
		$sql = 'SELECT SUM(qty) as qty FROM xin_leave_applications WHERE employee_id = ? and leave_type_id = ? and status = ? and current_year = ?';
        $binds = array($emp_id,$type,2,date("Y"));
        $query = $this->db->query($sql, $binds);

		if ($type != 'rl') {
			$dleave = $this->db->where('type', $type)->get('xin_leave_type')->row()->days_per_year;
			$qty = $dleave - $query->row()->qty;
		} else {
			$rl_rule = $this->db->where('status', 1)->get('leave_settings')->row()->replace_leave;
			$nfdate = date('Y-m-01', strtotime('-1 months'));
			$nsdate = date('Y-m-t', strtotime($nfdate));

			$this->db->select("
					SUM(CASE WHEN e_status='Present' AND status='Off Day' THEN 1 ELSE 0 END) AS rl
				");
			$this->db->where('employee_id', $emp_id);
			$this->db->where('e_status', 'Present');
			$this->db->where('status', 'Off Day');
			$this->db->where('attendance_date >=', $nfdate);
			$this->db->where('attendance_date <=', $nsdate);
			$qqs = $this->db->get('xin_attendance_time')->row();
			if (!empty($qqs) && $qqs->rl >= $rl_rule) {
				$rlv = floor($qqs->rl / $rl_rule);
				$qty = $rlv - $query->row()->qty;
			} else {
				$qty = 0;
			}
		}
		return $qty;
	}

    public function out_of_office_add()
    {
        $authorization = $this->input->get_request_header('Authorization');
        $user_info = api_auth($authorization);
        if ($user_info['status'] == true) {
            $user_data=$user_info['user_info'];
            $userid=$user_data->user_id;
            $unit_id=$user_data->unit_id;
            $in = $this->input->post('in_time');
			$ot = $this->input->post('out_time');
			$data = array(
				'date' 				=> date('Y-m-d', strtotime($this->input->post('date'))),//$this->input->post('date'),
				'in_time' 			=> $in ? date('H:i:s', strtotime($in)) : '',
				'out_time' 			=> $ot ? date('H:i:s', strtotime($ot)) : '',
				'status' 			=> 1,
				'updated_at' 		=> date('Y-m-d'),
				'remark' 			=> $this->input->post('remark'),
				'emp_id' 			=> $userid,
				'unit_id' 			=> $unit_id,
			);
			// insert data
			if ($this->db->insert('leave_out_off_office', $data)) {
                $this->api_return([
                    'status'    =>  true,
                    'message'    =>  'successful',
                    'data'       =>  null,
                ], 200);
            }else{
                $this->api_return([
                    'status'  =>   false,
                    'message'  =>   'Unsuccessful',
                    'data'     =>   null,
                ], 404);
            }
        } else {
            $this->api_return([
                'status' => false,
                'message' => 'Unauthorized User',
                'data' => [],
            ], 401);
        }
    }
    public function out_of_office_list()
    {
        $authorization = $this->input->get_request_header('Authorization');
        $user_info = api_auth($authorization);
        if ($user_info['status'] == true) {
            $user_data=$user_info['user_info'];
            $userid=$user_data->user_id;
            $unit_id=$user_data->unit_id;
            $leave_out_off_office=$this->db->order_by('id', 'desc')->get_where('leave_out_off_office', array('emp_id' => $userid))->result();
                $this->api_return([
                    'status'    =>  true,
                    'message'    =>  'successful',
                    'data'       =>  $leave_out_off_office,
                ], 200);
        } else {
            $this->api_return([
                'status' => false,
                'message' => 'Unauthorized User',
                'data' => [],
            ], 401);
        }
    }
    public function emp_outstaton_leave_add()
    {
        $authorization = $this->input->get_request_header('Authorization');
        $user_info = api_auth($authorization);
        if ($user_info['status'] == true) {
            $user_data=$user_info['user_info'];
            $userid=$user_data->user_id;
            $unit_id=$user_data->unit_id;
            $tl = $this->db->where('user_id', $userid)->get('xin_employees')->row();
			$data = array(
				'from_date' 		=> date('Y-m-d', strtotime($this->input->post('from_date'))),//$this->input->post('from_date'),
				'to_date' 			=> date('Y-m-d', strtotime($this->input->post('to_date'))),//$this->input->post('to_date'),
				'control_person' 	=> !empty($tl->lead_user_id)?$tl->lead_user_id:0,
				'ap_date' 			=> date('Y-m-d'),
				'status' 			=> $this->input->post('status'),
				'remark' 			=> $this->input->post('remark'),
				'emp_id' 			=> $userid,
				'unit_id' 			=> $unit_id,
			);
			// insert data
			if ($this->db->insert('leave_out_station', $data)) {
                $this->api_return([
                    'status'    =>  true,
                    'message'    =>  'successful',
                    'data'       =>  null,
                ], 200);
            }else{
                $this->api_return([
                    'status'  =>   false,
                    'message'  =>   'Unsuccessful',
                    'data'     =>   null,
                ], 404);
            }
        } else {
            $this->api_return([
                'status' => false,
                'message' => 'Unauthorized User',
                'data' => null,
            ], 401);
        }
    }
    public function emp_outstaton_leave_list()
    {
        $authorization = $this->input->get_request_header('Authorization');
        $user_info = api_auth($authorization);
        if ($user_info['status'] == true) {
            $user_data=$user_info['user_info'];
            $userid=$user_data->user_id;
            $unit_id=$user_data->unit_id;
            $leave_out_off_office=$this->db->order_by('id', 'desc')->get_where('leave_out_station', array('emp_id' => $userid))->result();
                $this->api_return([
                    'status'    =>  true,
                    'message'    =>  'successful',
                    'data'       =>  $leave_out_off_office,
                ], 200);
        } else {
            $this->api_return([
                'status' => false,
                'message' => 'Unauthorized User',
                'data' => [],
            ], 401);
        }
    }
}