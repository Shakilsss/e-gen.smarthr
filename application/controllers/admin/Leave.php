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
		$this->form_validation->set_rules('control_person', 'Leave Approver', 'trim|required|xss_clean');
		$this->form_validation->set_rules('status', 'Status', 'trim|required|xss_clean');

		if ($this->form_validation->run() == TRUE) {
			$data = array(
				'from_date' 		=> $this->input->post('from_date'),
				'to_date' 			=> $this->input->post('to_date'),
				'control_person' 	=> $this->input->post('control_person'),
				'ap_date' 			=> date('Y-m-d'),
				'status' 			=> $this->input->post('status'),
				'remark' 			=> $this->input->post('remark'),
				'emp_id' 			=> $session['user_id'],
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

		$data['results'] = $this->db->where('status !=',5)->where('emp_id',$session['user_id'])->get('leave_out_station')->result();

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
		$this->form_validation->set_rules('control_person', 'Leave Approver', 'trim|required|xss_clean');
		$this->form_validation->set_rules('status', 'Status', 'trim|required|xss_clean');

		if ($this->form_validation->run() == TRUE) {
			$data = array(
				'from_date' 		=> $this->input->post('from_date'),
				'to_date' 			=> $this->input->post('to_date'),
				'control_person' 	=> $this->input->post('control_person'),
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

		$this->form_validation->set_rules('from_date', 'Apply From Date', 'trim|required|xss_clean');
		$this->form_validation->set_rules('to_date', 'Apply To Date', 'trim|required|xss_clean');
		$this->form_validation->set_rules('control_person', 'Leave Approver', 'trim|required|xss_clean');
		$this->form_validation->set_rules('status', 'Status', 'trim|required|xss_clean');

		if ($this->form_validation->run() == TRUE) {
			$data = array(
				'from_date' 		=> $this->input->post('from_date'),
				'to_date' 			=> $this->input->post('to_date'),
				'control_person' 	=> $this->input->post('control_person'),
				'ap_date' 			=> date('Y-m-d'),
				'status' 			=> $this->input->post('status'),
				'remark' 			=> $this->input->post('remark'),
				'emp_id' 			=> $session['user_id'],
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

		$data['results'] = $this->db->where('status !=',1)->where('control_person',$session['user_id'])->get('leave_out_station')->result();

        $data['subview'] = $this->load->view("admin/leave/emp_outstaton_leave", $data, TRUE);
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
      	$session = $this->session->userdata('username');
		// if(empty($session)){
		// 	redirect('admin/');
		// }

		$session = $this->session->userdata( 'username' );
		$userid  = $session[ 'user_id' ];
		$firstdate = $this->input->post('firstdate');
		$seconddate = $this->input->post('seconddate');

		$this->db->select("*");
		$this->db->where("employee_id", $userid);
		if ($firstdate!=null && $seconddate!=null){
				$f1_date=date('Y-m-d',strtotime($firstdate));
				$f2_date=date('Y-m-d',strtotime($seconddate));
				$this->db->where("from_date BETWEEN '$f1_date' AND '$f2_date'");
				$this->db->order_by("from_date", "desc");
			$data['alldata'] = $this->db->get('xin_leave_applications')->result();
			$data['tablebody'] 		= $this->load->view("admin/leave/emp_leave_table", $data, TRUE);
			echo $data['tablebody'] ;
		}else{

			$this->db->order_by("from_date", "desc");
			$data['alldata'] = $this->db->get('xin_leave_applications')->result();
			// dd($data['alldata'] );

			$data['session'] 			= $session;
			$data['title'] 			= 'Leave | '.$this->Xin_model->site_title();

			$data['breadcrumbs']	= 'Leave | Employee Leave';
			$data['tablebody'] 		= $this->load->view("admin/leave/emp_leave_table", $data, TRUE);
			$data['subview'] 		= $this->load->view("admin/leave/emp_leave", $data, TRUE);
									$this->load->view('admin/layout/layout_main', $data);
	    }
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
