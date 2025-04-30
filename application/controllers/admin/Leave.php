<?php
 /**
 * NOTICE OF LICENSE
 *
 * This source file is subject to the HRSALE License
 * that is bundled with this package in the file license.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.hrsale.com/license.txt
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to hrsalesoft@gmail.com so we can send you a copy immediately.
 *
 * @author   HRSALE
 * @author-email  hrsalesoft@gmail.com
 * @copyright  Copyright © hrsale.com. All Rights Reserved
 */
if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Leave extends MY_Controller
{

	public function __construct()
   	{
      	parent::__construct();
      	//load the login model
      	$this->load->model('Company_model');
		$this->load->model('Xin_model');
		$this->load->model('Timesheet_model');
   	}

   	/*Function to set JSON output*/
	public function output($Return=array()){
		/*Set response header*/
		header("Access-Control-Allow-Origin: *");
		header("Content-Type: application/json; charset=UTF-8");
		/*Final JSON response*/
		exit(json_encode($Return));
	}

	// emp_outstaton_leave
	function emp_outstaton_leave() {
		$session = $this->session->userdata('username');
		if(empty($session)){
			redirect('admin/');
		}

		$this->form_validation->set_rules('from_date', 'Apply From Date', 'trim|required|xss_clean');
		$this->form_validation->set_rules('to_date', 'Apply To Date', 'trim|required|xss_clean');
		$this->form_validation->set_rules('status', 'Status', 'trim|required|xss_clean');

		if ($this->form_validation->run() == TRUE) {
			$tl = $this->db->where('user_id', $session['user_id'])->get('xin_employees')->row();
			$data = array(
				'from_date' 		=> $this->input->post('from_date'),
				'to_date' 			=> $this->input->post('to_date'),
				'control_person' 	=> !empty($tl->lead_user_id)?$tl->lead_user_id:0,
				'ap_date' 			=> date('Y-m-d'),
				'status' 			=> $this->input->post('status'),
				'remark' 			=> $this->input->post('remark'),
				'emp_id' 			=> $session['user_id'],
				'unit_id' 			=> $session['unit_id'],
			);

			// insert data
			if ($this->db->insert('leave_out_station', $data)) {
				$this->session->set_flashdata('success', 'Inserted information successfully.');
				redirect('admin/leave/emp_outstaton_leave/');
			}
		}

		$data['title'] = 'Out Station Leave';
		$data['breadcrumbs'] = 'Out Station Leave';
		$data['path_url'] = 'leave';
		$data['user'] = $session;

		$data['results'] = $this->db->where('status !=',5)->where('emp_id',$session['user_id'])->order_by('id', 'DESC')->get('leave_out_station')->result();

        $data['subview'] = $this->load->view("admin/leave/emp_outstaton_leave", $data, TRUE);
        $this->load->view('admin/layout/layout_main', $data); //page load
	}

	// emp_outstaton_edit
	public function emp_outstaton_edit($id = null) {
		$session = $this->session->userdata('username');
		if(empty($session)){
			redirect('admin/');
		}

		$this->form_validation->set_rules('from_date', 'Apply From Date', 'trim|required|xss_clean');
		$this->form_validation->set_rules('to_date', 'Apply To Date', 'trim|required|xss_clean');
		$this->form_validation->set_rules('status', 'Status', 'trim|required|xss_clean');

		if ($this->form_validation->run() == TRUE) {
			$data = array(
				'from_date' 		=> $this->input->post('from_date'),
				'to_date' 			=> $this->input->post('to_date'),
				'status' 			=> $this->input->post('status'),
				'remark' 			=> $this->input->post('remark'),
			);

			// update data
			if ($this->db->where('id', $id)->update('leave_out_station', $data)) {
				$this->session->set_flashdata('success', 'Update information successfully.');
				redirect('admin/leave/emp_outstaton_leave');
			}
		}

		$data['row'] = $this->db->where("id", $id)->get('leave_out_station')->row();

		$data['title'] = 'Out Station Leave';
		$data['breadcrumbs'] = 'Out Station Leave';
		$data['path_url'] = 'leave';
		$data['user'] = $session;

        $data['subview'] = $this->load->view("admin/leave/emp_outstaton_edit", $data, TRUE);
        $this->load->view('admin/layout/layout_main', $data); //page load
	}

	// approve os leave
	function approve_os_leave() {
		$session = $this->session->userdata('username');
		if(empty($session)){
			redirect('admin/');
		}

		$data['title'] = 'Out Station Leave';
		$data['breadcrumbs'] = 'Out Station Leave';
		$data['path_url'] = 'leave';
		$data['user'] = $session;

		$this->db->select('os.*, e.first_name,e.last_name');
		$this->db->from('leave_out_station as os');
		$this->db->join('xin_employees as e', 'e.user_id = os.emp_id');
		if ($session['role_id'] == 3) {
			$this->db->where_not_in('os.status', array(1,5))->where('os.control_person',$session['user_id']);
		} else {
			$this->db->where_not_in('os.status', array(1,2));
		}
		$data['results'] = $this->db->get()->result();

        $data['subview'] = $this->load->view("admin/leave/approve_os_leave", $data, TRUE);
        $this->load->view('admin/layout/layout_main', $data); //page load
	}

	// change os leave status
	function os_leave_change($id = null) {
		$session = $this->session->userdata('username');
		if(empty($session)){
			redirect('admin/');
		}

		$this->form_validation->set_rules('ap_from_date', 'Apply From Date', 'trim|required|xss_clean');
		$this->form_validation->set_rules('ap_to_date', 'Apply To Date', 'trim|required|xss_clean');
		$this->form_validation->set_rules('status', 'Status', 'trim|required|xss_clean');

		$fdate = date('Y-m-d', strtotime($this->input->post('ap_from_date')));
		$tdate = date('Y-m-d', strtotime($this->input->post('ap_to_date')));
		if ($this->form_validation->run() == TRUE && $fdate < $tdate) {
			if ($this->input->post('status') == 3) {
				$from_date = new DateTime($fdate);
				$to_date = new DateTime($tdate);
				$interval = $from_date->diff($to_date);
				$ap_day = $interval->days + 1; // +1 to include both start and end dates
			} else {
				$ap_day = 0;
			}

			$data = array(
				'ap_from_date' 	=> $this->input->post('ap_from_date'),
				'ap_to_date' 	=> $this->input->post('ap_to_date'),
				'status' 		=> $this->input->post('status'),
				'ap_day' 		=> $ap_day,
				'updated_at' 	=> date('Y-m-d'),
			);

			// update data
			if ($this->db->where('id', $id)->update('leave_out_station', $data)) {
				$this->session->set_flashdata('success', 'Update information successfully.');
				redirect('admin/leave/approve_os_leave');
			}
		}

		$data['title'] = 'Out Station Leave';
		$data['breadcrumbs'] = 'Out Station Leave';
		$data['path_url'] = 'leave';
		$data['user'] = $session;

		$data['row'] = $this->db->where('id', $id)->get('leave_out_station')->row();

		$this->db->select('e.*, d.department_name, de.designation_name');
		$this->db->from('xin_employees as e');
		$this->db->join('xin_departments as d', 'e.department_id = d.department_id');
		$this->db->join('xin_designations as de', 'e.designation_id = de.designation_id');
		$data['info'] = $this->db->where('e.user_id', $data['row']->emp_id)->get()->row();

        $data['subview'] = $this->load->view("admin/leave/os_leave_change", $data, TRUE);
        $this->load->view('admin/layout/layout_main', $data); //page load
	}

	// delete or reject os leave
	function os_leave_del_rej($statu = null, $id = null) {
		$session = $this->session->userdata('username');
		if(empty($session)){
			redirect('admin/');
		}

		if (!empty($statu) && !empty($id)) {
			$this->db->where('id', $id)->update('leave_out_station', array('status' => $statu));
			$this->session->set_flashdata('success', 'Information updated successfully.');
			redirect('admin/leave/approve_os_leave');
		} else {
			redirect('admin/leave/approve_os_leave');
		}
	}

	// approve out off leave
	function out_of_office() {
		$session = $this->session->userdata('username');
		if(empty($session)){
			redirect('admin/');
		}

		$this->form_validation->set_rules('date', 'Date', 'trim|required');
		$this->form_validation->set_rules('remark', 'Remark', 'trim|required');
		if ($this->form_validation->run() == TRUE) {
			$in = $this->input->post('in_time');
			$ot = $this->input->post('out_time');
			$data = array(
				'date' 				=> $this->input->post('date'),
				'in_time' 			=> $in ? date('H:i:s', strtotime($in)) : '',
				'out_time' 			=> $ot ? date('H:i:s', strtotime($ot)) : '',
				'status' 			=> 1,
				'updated_at' 		=> date('Y-m-d'),
				'remark' 			=> $this->input->post('remark'),
				'emp_id' 			=> $session['user_id'],
				'unit_id' 			=> $session['unit_id'],
			);

			// insert data
			if ($this->db->insert('leave_out_off_office', $data)) {
				$this->session->set_flashdata('success', 'Inserted information successfully.');
				redirect('admin/leave/out_of_office/');
			}
		}

		$data['results'] = $this->db->order_by('id', 'DESC')->get('leave_out_off_office')->result();
		$data['title'] = 'Out Off Offie';
		$data['breadcrumbs'] = 'Out Off Offie';
		$data['path_url'] = 'leave';
		$data['user'] = $session;

        $data['subview'] = $this->load->view("admin/leave/out_of_office", $data, TRUE);
        $this->load->view('admin/layout/layout_main', $data); //page load
	}

	//leave calendar
	public function calendar() {

		$session = $this->session->userdata('username');
		if(empty($session)){
			redirect('admin/');
		}
		$data['title'] = $this->lang->line('xin_hr_leave_calendar');
		$data['breadcrumbs'] = $this->lang->line('xin_hr_leave_calendar');
		$data['path_url'] = 'calendar_leave';
		$role_resources_ids = $this->Xin_model->user_role_resource();
		if(in_array('102',$role_resources_ids)) {
			$data['subview'] = $this->load->view("admin/leave/leave_calendar", $data, TRUE);
			$this->load->view('admin/layout/layout_main', $data); //page load
		} else {
			redirect('admin/dashboard');
		}
	}

	// attandance view code here
	public function emp_leave(){
		$session = $this->session->userdata( 'username' );
		$userid  = $session[ 'user_id' ];

		$this->db->select("
            SUM(CASE WHEN leave_type = 'cl' THEN qty ELSE 0 END) AS cl,
            SUM(CASE WHEN leave_type = 'sl' THEN qty ELSE 0 END) AS sl
        ");
        $this->db->where('employee_id', $userid)->where('status', 2);
        $this->db->where('to_date >=', date('Y-01-01'));
        $this->db->where('to_date <=', date('Y-12-31'));
        $data['used_leave'] = $this->db->get('xin_leave_applications')->row();

		$firstdate = $this->input->post('firstdate');
		$seconddate = $this->input->post('seconddate');
		$this->db->select("*");
		$this->db->where("employee_id", $userid);
		if ($firstdate!=null && $seconddate!=null){
			$f1_date = date('Y-m-d',strtotime($firstdate));
			$f2_date = date('Y-m-d',strtotime($seconddate));
			$this->db->where("from_date BETWEEN '$f1_date' AND '$f2_date'");
			$this->db->order_by("from_date", "desc");
			$data['alldata'] = $this->db->get('xin_leave_applications')->result();
			$data['tablebody'] 		= $this->load->view("admin/leave/emp_leave_table", $data, TRUE);
			echo $data['tablebody'] ;
		}else{
			$this->db->order_by("from_date", "desc");
			$data['alldata'] = $this->db->get('xin_leave_applications')->result();

			$data['session'] 		= $session;
			$data['title'] 			= 'Leave | '.$this->Xin_model->site_title();
			$data['breadcrumbs']	= 'Leave | Employee Leave';
			$data['tablebody'] 		= $this->load->view("admin/leave/emp_leave_table", $data, TRUE);
			$data['subview'] 		= $this->load->view("admin/leave/emp_leave", $data, TRUE);
									$this->load->view('admin/layout/layout_main', $data);
	    }
   	}

	// Validate and add info in database
	public function add_leave() {

		$start_date = $this->input->post('start_date');
		$end_date = $this->input->post('end_date');
		$remarks = $this->input->post('remarks');
		$emp_id = $this->input->post('employee_id');
		// validate apply date check
		if($start_date == '' || $end_date == ''){
			$this->session->set_flashdata('error', 'Please select from & to date.');
			redirect('admin/leave/emp_leave');
		}
		$prev_day = date('Y-m-d', strtotime('-1 days'. $start_date));
		$next_day = date('Y-m-d', strtotime('+1 days'. $end_date));

		// validate leave type
		if($this->input->post('leave_type')==='') {
			$this->session->set_flashdata('error', 'Please select leave type.');
			redirect('admin/leave/emp_leave');
		}
		//get leave date of a employee ...
		$leave_date = $this->db->select('*')->where('status !=',3)->where('employee_id',$emp_id)->get('xin_leave_applications')->result();
		//check duplicate leave date
		foreach($leave_date as $date){
			if($date->from_date == $start_date || $date->to_date == $end_date) {
				$this->session->set_flashdata('error', 'Leave date already exists.');
				redirect('admin/leave/emp_leave');
			}
		};

		$datetime1 = new DateTime($this->input->post('start_date'));
		$datetime2 = new DateTime($this->input->post('end_date'));
		$interval = $datetime1->diff($datetime2);
		$no_of_days = $interval->format('%a') + 1;
		// check half day leave
		if($this->input->post('leave_half_day') == 1 && $no_of_days > 1 ) {
			$this->session->set_flashdata('error', 'Please select only one day for half day leave.');
			redirect('admin/leave/emp_leave');
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
		// $total = $this->cal_emp_leave($emp_id, $lt);
		// if($total < $no_of_days){
		// 	$this->session->set_flashdata('error', 'You have only '.$total.' '.$type_name.' left.');
		// 	redirect('admin/leave/emp_leave');
		// }

		// attachment upload
		if($_FILES['attachment']['tmp_name']!='') {
			$config['upload_path'] = './uploads/leave/'; // Modify this path as needed
			$config['allowed_types'] = 'gif|jpg|png|pdf';// Add more allowed file types as needed
			$config['encrypt_name'] = true; // Generate a unique encrypted filename
			$config['max_size'] = 10048; // Set maximum file size in kilobytes (2MB in this case)
			$this->upload->initialize($config);
			$this->upload->do_upload('attachment');
				$fileData = $this->upload->data();
				$fileLocation ='uploads/leave/'.$fileData['file_name'];
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
			$this->session->set_flashdata('success', 'Successfully Added');
			redirect('admin/leave/emp_leave');
		} else {
			$this->session->set_flashdata('error', 'There is an error');
			redirect('admin/leave/emp_leave');
		}
	}

	function cal_emp_leave($emp_id, $type) {
		$sql = 'SELECT SUM(qty) as qty FROM xin_leave_applications WHERE employee_id = ? and leave_type_id = ? and status = ? and current_year = ?';
        $binds = array($emp_id,$type,2,date("Y"));
        $query = $this->db->query($sql, $binds);

		if ($type != 'rl') {
			$dleave = $this->db->where('type', $type)->get('leave_type')->row()->days_per_year;
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

   public function leave_delete($id)
   {
		$this->db->where('leave_id', $id);
		$this->db->delete('xin_leave_applications');
		$this->session->set_flashdata('error', 'Successfully Delete Done');
		redirect('admin/leave/emp_leave');
   }

   	public function emp_holyday(){
		$session = $this->session->userdata('username');
		//  dd($session['user_id']);
		if(empty($session)){
			redirect('admin/');
		}
		$session = $this->session->userdata( 'username' );
		$userid  = $session[ 'user_id' ];
		$firstdate = $this->input->post('firstdate');
		$seconddate = $this->input->post('seconddate');

		$this->db->select("*");
		if ($firstdate!=null && $seconddate!=null){
			$f1_date=date('Y-m-d',strtotime($firstdate));
			$f2_date=date('Y-m-d',strtotime($seconddate));
			$this->db->where("start_date BETWEEN '$f1_date' AND '$f2_date'");
			$this->db->order_by("holiday_id", "desc");
			$data['allevent']   = $this->db->get('xin_holidays')->result();
			$data['tablebody'] = $this->load->view("admin/leave/emp_holyday_table", $data, TRUE);
			echo $data['tablebody'] ;
		}else{
			$this->db->order_by("holiday_id", "desc");
			$data['allevent'] = $this->db->get('xin_holidays')->result();
			$data['session']     = $session;
			$data['title'] 		 = 'Holyday | '.$this->Xin_model->site_title();
			$data['breadcrumbs'] = 'Holyday';
			$data['tablebody'] 	 = $this->load->view("admin/leave/emp_holyday_table", $data, TRUE);


			$data['subview'] 	 = $this->load->view("admin/leave/emp_holyday", $data, TRUE);
								   $this->load->view('admin/layout/layout_main', $data);
		}
	}
}
?>
