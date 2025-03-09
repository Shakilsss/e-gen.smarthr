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

class Schedules extends MY_Controller
{

    public function __construct()
    {
        parent::__construct();
        //load the login model
        $this->load->model('Xin_model');
    }

   /*Function to set JSON output*/
	public function output($Return=array()){
		/*Set response header*/
		header("Access-Control-Allow-Origin: *");
		header("Content-Type: application/json; charset=UTF-8");
		/*Final JSON response*/
		exit(json_encode($Return));
	}

	//leave calendar
	public function index() {

		$session = $this->session->userdata('username');
		if(empty($session)){
			redirect('admin/');
		}

		$this->form_validation->set_rules('unit_id', 'Organization', 'trim|required|xss_clean');
		$this->form_validation->set_rules('sh_type', 'Shift Type', 'trim|required|xss_clean');
		$this->form_validation->set_rules('in_start', 'In Start', 'trim|required|xss_clean');
		$this->form_validation->set_rules('in_time', 'In Time', 'trim|required|xss_clean');
		$this->form_validation->set_rules('late_start', 'Late Start', 'trim|required|xss_clean');
		$this->form_validation->set_rules('in_end', 'In End', 'trim|required|xss_clean');
		$this->form_validation->set_rules('out_start', 'Out Start', 'trim|required|xss_clean');
		$this->form_validation->set_rules('out_time', 'Out Time', 'trim|required|xss_clean');
		$this->form_validation->set_rules('out_end', 'Out End', 'trim|required|xss_clean');

		if ($this->form_validation->run() == TRUE) {
			$data = array(
				'unit_id' => $this->input->post('unit_id'),
				'sh_type' => $this->input->post('sh_type'),
				'in_start' => $this->input->post('in_start'),
				'in_time' => $this->input->post('in_time'),
				'late_start' => $this->input->post('late_start'),
				'in_end' => $this->input->post('in_end'),
				'out_start' => $this->input->post('out_start'),
				'out_time' => $this->input->post('out_time'),
				'out_end' => $this->input->post('out_end'),
				'of_day' => json_encode($this->input->post('of_day')),
			);

			// insert data
			if ($this->db->insert('emp_shift_schedule', $data)) {
				$this->session->set_flashdata('success', 'Inserted successfully.');
			}
		}

		$this->db->select("emp.*, u.name")->from("emp_shift_schedule as emp");
		$this->db->join('xin_companies as u', 'u.company_id = emp.unit_id', 'left');
		$data['results'] = $this->db->get()->result();

		$data['title'] = 'Schedule';
		$data['breadcrumbs'] = 'Schedule';
		$data['path_url'] = 'schedules';

        $data['subview'] = $this->load->view("admin/schedule/index", $data, TRUE);
        $this->load->view('admin/layout/layout_main', $data); //page load
	}

	public function edit($id = null) {
		$session = $this->session->userdata('username');
		if(empty($session)){
			redirect('admin/');
		}

		$this->form_validation->set_rules('unit_id', 'Organization', 'trim|required|xss_clean');
		$this->form_validation->set_rules('sh_type', 'Shift Type', 'trim|required|xss_clean');
		$this->form_validation->set_rules('in_start', 'In Start', 'trim|required|xss_clean');
		$this->form_validation->set_rules('in_time', 'In Time', 'trim|required|xss_clean');
		$this->form_validation->set_rules('late_start', 'Late Start', 'trim|required|xss_clean');
		$this->form_validation->set_rules('in_end', 'In End', 'trim|required|xss_clean');
		$this->form_validation->set_rules('out_start', 'Out Start', 'trim|required|xss_clean');
		$this->form_validation->set_rules('out_time', 'Out Time', 'trim|required|xss_clean');
		$this->form_validation->set_rules('out_end', 'Out End', 'trim|required|xss_clean');

		if ($this->form_validation->run() == TRUE) {
			$data = array(
				'unit_id' => $this->input->post('unit_id'),
				'sh_type' => $this->input->post('sh_type'),
				'in_start' => $this->input->post('in_start'),
				'in_time' => $this->input->post('in_time'),
				'late_start' => $this->input->post('late_start'),
				'in_end' => $this->input->post('in_end'),
				'out_start' => $this->input->post('out_start'),
				'out_time' => $this->input->post('out_time'),
				'out_end' => $this->input->post('out_end'),
				'of_day' => json_encode($this->input->post('of_day')),
			);

			// update data
			if ($this->db->where('id', $id)->update('emp_shift_schedule', $data)) {
				$this->session->set_flashdata('success', 'Update information successfully.');
				redirect('admin/schedules/');
			}
		}

		$data['row'] = $this->db->where("id", $id)->get("emp_shift_schedule")->row();

		$data['title'] = 'Schedule';
		$data['breadcrumbs'] = 'Schedule';
		$data['path_url'] = 'schedules';

        $data['subview'] = $this->load->view("admin/schedule/edit", $data, TRUE);
        $this->load->view('admin/layout/layout_main', $data); //page load
	}

	public function shift_manage() {
		$session = $this->session->userdata('username');
		if(empty($session)){
			redirect('admin/');
		}

		$this->form_validation->set_rules('unit_id', 'Organization', 'trim|required|xss_clean');
		$this->form_validation->set_rules('schedule_id', 'Schedule', 'trim|required|xss_clean');
		$this->form_validation->set_rules('shift_name', 'Shift Name', 'trim|required|xss_clean');

		if ($this->form_validation->run() == TRUE) {
			$data = array(
				'unit_id' => $this->input->post('unit_id'),
				'schedule_id' => $this->input->post('schedule_id'),
				'shift_name' => $this->input->post('shift_name'),
			);

			// insert data
			if ($this->db->insert('emp_shift_manage', $data)) {
				$this->session->set_flashdata('success', 'Inserted information successfully.');
			}
		}

		$this->db->select("emp.*, s.sh_type, u.name")->from("emp_shift_manage as emp");
		$this->db->join('xin_companies as u', 'u.company_id = emp.unit_id', 'left');
		$this->db->join('emp_shift_schedule as s', 's.id = emp.schedule_id', 'left');
		$data['results'] = $this->db->get()->result();

		$data['title'] = 'Manage Shift';
		$data['breadcrumbs'] = 'Manage Shift';
		$data['path_url'] = 'schedules';

        $data['subview'] = $this->load->view("admin/schedule/manage_shift", $data, TRUE);
        $this->load->view('admin/layout/layout_main', $data); //page load
	}

	public function manage_edit($id = null) {
		$session = $this->session->userdata('username');
		if(empty($session)){
			redirect('admin/');
		}

		$this->form_validation->set_rules('unit_id', 'Organization', 'trim|required|xss_clean');
		$this->form_validation->set_rules('schedule_id', 'Schedule', 'trim|required|xss_clean');
		$this->form_validation->set_rules('shift_name', 'Shift Name', 'trim|required|xss_clean');

		if ($this->form_validation->run() == TRUE) {
			$data = array(
				'unit_id' => $this->input->post('unit_id'),
				'schedule_id' => $this->input->post('schedule_id'),
				'shift_name' => $this->input->post('shift_name'),
			);

			// update data
			if ($this->db->where('id', $id)->update('emp_shift_manage', $data)) {
				$this->session->set_flashdata('success', 'Update information successfully.');
				redirect('admin/schedules/shift_manage/');
			}
		}

		$data['row'] = $this->db->where("id", $id)->get("emp_shift_manage")->row();

		$data['title'] = 'Manage Shift';
		$data['breadcrumbs'] = 'Manage Shift';
		$data['path_url'] = 'schedules';

        $data['subview'] = $this->load->view("admin/schedule/manage_edit", $data, TRUE);
        $this->load->view('admin/layout/layout_main', $data); //page load
	}
}
?>
