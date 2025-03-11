<?php

// defined('BASEPATH') or exit('No direct script access allowed');

require_once APPPATH . 'libraries/API_Controller.php';

class ZK_device extends API_Controller
{
    public function __construct()
    {
        header('Access-Control-Allow-Origin: *');
        // Allow methods: GET, POST, OPTIONS
        header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
        // Allow header Content-Type: application/json
        header("Access-Control-Allow-Headers: Content-Type");

        parent::__construct();
        $this->load->library('zklibrary');
    }

    public function get_data()
    {
        $date = date('Y-m-d');
        $startTime = strtotime($date . ' 00:00:00');
        $endTime = strtotime($date . ' 23:59:59');

        $devices = array(
            array("ip" => "182.160.110.58", "port" => 4372),
            array("ip" => "182.160.110.58", "port" => 4370),
            // Add more devices as needed
        );


        $today_data = array();
        foreach ($devices as $index => $device) {
            $attendance = $this->retrieveAttendance($device["ip"], $device["port"], $startTime, $endTime);
            foreach ($attendance as $at) {
                $today_data[] = array(
                    'sl' => $at[0],
                    'punch_id' => $at[1],
                    'state' => $index,
                    'time' => $at[3]
                );
            }
        }
        return $today_data;
    }

    public function retrieveAttendance($ip, $port, $startTime, $endTime)
    {
        $zk = new zklibrary($ip, $port);
        // $zk->testVoice();
        $zk->connect();
        $attendance = $zk->getAttendance();
        $zk->disconnect();

        // Filter attendance data based on the time range
        $filteredAttendance = array();
        foreach ($attendance as $at) {
            $dateTime = strtotime($at[3]);
            if ($dateTime >= $startTime && $dateTime <= $endTime) {
                $filteredAttendance[] = $at;
            }
        }

        return $filteredAttendance;
    }

    // attn device setup
    function attn_device() {
        $session = $this->session->userdata('username');
        if(empty($session)){
            redirect('admin/');
        }

        $this->load->model('Xin_model');
        $this->load->library('form_validation');
        $this->form_validation->set_rules('name', 'Device name', 'trim|required');
        $this->form_validation->set_rules('ip', 'Device ip', 'trim|required');
        $this->form_validation->set_rules('port', 'Device port', 'trim|required');
        $this->form_validation->set_rules('type', 'Device type', 'trim|required');

        if ($this->form_validation->run() == TRUE) {
            $data = array(
                'name' => $this->input->post('name'),
                'location' => $this->input->post('location'),
                'model' => $this->input->post('model'),
                'ip' => $this->input->post('ip'),
                'port' => $this->input->post('port'),
                'type' => $this->input->post('type'),
                'status' => $this->input->post('status'),
            );

            // insert data
            if ($this->db->insert('attn_device_setup', $data)) {
                $this->session->set_flashdata('success', 'Inserted information successfully.');
                redirect('admin/zk_device/attn_device/');
            }
        }

        $data['results'] = $this->db->get('attn_device_setup')->result();

        $data['title'] = 'Device Setup';
        $data['breadcrumbs'] = 'Device Setup';
        $data['path_url'] = 'ZK_device';

        $data['subview'] = $this->load->view("admin/zk_device/attn_device", $data, TRUE);
        $this->load->view('admin/layout/layout_main', $data); //page load
    }

    function attn_device_edit($id = null) {
        $session = $this->session->userdata('username');
        if(empty($session)){
            redirect('admin/');
        }

        $this->load->model('Xin_model');
        $this->load->library('form_validation');
        $this->form_validation->set_rules('name', 'Device name', 'trim|required');
        $this->form_validation->set_rules('ip', 'Device ip', 'trim|required');
        $this->form_validation->set_rules('port', 'Device port', 'trim|required');
        $this->form_validation->set_rules('type', 'Device type', 'trim|required');

        if ($this->form_validation->run() == TRUE) {
            $data = array(
                'name' => $this->input->post('name'),
                'location' => $this->input->post('location'),
                'model' => $this->input->post('model'),
                'ip' => $this->input->post('ip'),
                'port' => $this->input->post('port'),
                'type' => $this->input->post('type'),
                'status' => $this->input->post('status'),
            );

            // insert data
            if ($this->db->where('id', $id)->update('attn_device_setup', $data)) {
                $this->session->set_flashdata('success', 'Inserted information successfully.');
                redirect('admin/zk_device/attn_device/');
            }
        }

        $data['info'] = $this->db->where('id', $id)->get('attn_device_setup')->row();

        $data['title'] = 'Device Setup';
        $data['breadcrumbs'] = 'Device Setup';
        $data['path_url'] = 'ZK_device';

        $data['subview'] = $this->load->view("admin/zk_device/attn_device_edit", $data, TRUE);
        $this->load->view('admin/layout/layout_main', $data); //page load
    }



}
