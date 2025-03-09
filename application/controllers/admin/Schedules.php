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

		$this->form_validation->set_rules('unit_id', 'Unit', 'trim|required|xss_clean');
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
			if (!empty($id)) {
				if ($this->db->where('id', $id)->update('emp_shift_schedule', $data)) {
					$this->session->set_flashdata('success', 'Update information successfully.');
				}
			} else {
				if ($this->db->insert('emp_shift_schedule', $data)) {
					$this->session->set_flashdata('success', 'Inserted successfully.');
				}
			}
		}

		$data['results'] = $this->db->get("emp_shift_schedule")->result();

		$data['title'] = 'Schedule';
		$data['breadcrumbs'] = 'Schedule';
		$data['path_url'] = 'schedules';

        $data['subview'] = $this->load->view("admin/schedule/index", $data, TRUE);
        $this->load->view('admin/layout/layout_main', $data); //page load
	}
}
?>
