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
        $this->load->model('Attendance_model');
        $this->load->library('Zklibrary');
    }


    public function get_device()
    {
        $devices=$this->db->where('status', 1)->get('attn_device_setup');
        $devices = $query->result_array();
        header('Content-Type: application/json');
        echo json_encode($devices);
    }
    public function get_data()
    {
        $date = date('Y-m-d');
        $startTime = strtotime($date . ' 00:00:00');
        $endTime = strtotime($date . ' 23:59:59');
        $devices=$this->db->where('status', 1)->get('attn_device_setup')->result();
        $today_data = array();
        foreach ($devices as $index => $device) {
            $attendance = $this->retrieveAttendance($device->ip, $device->port, $device->sl);
            foreach ($attendance as  $at) {
                $today_data[] = array(
                    'sl' => $at[0],
                    'punch_id' => $at[1],
                    'time' => $at[3],
                    'device_id' => $device->id,
                );
            }
        }
        return $today_data;
    }

    public function retrieveAttendance($ip, $port, $sl)
    {
        $zk = new zklibrary($ip, $port);
        $zk->testVoice();
        $zk->connect();
        $attendance = $zk->getAttendance();
        $zk->disconnect();
        dd($attendance);

        $filteredAttendance = array();
        foreach ($attendance as $at) {
            $slget = $at[0];
            if ($slget > $sl) {
                $filteredAttendance[] = $at;
            }
        }
        return $filteredAttendance;
    }

    // attn device setup
    
    public function add_attendance() {
        $recent_data = $this->get_data();
        foreach ($recent_data as $key => $value) {
            $device_id = $value['device_id'];
            $device_type = $this->db->where('id', $device_id)->get('attn_device_setup')->row();
            $device_type = $device_type->type;
            dd($device_type);
            $data= [
                'proxi_id'=>$value['punch_id'],
                'date_time'=> $value['time'],
                'device_id'=> $value['device_id'],
            ];
            if($this->db->insert('xin_att_machine', $data)){
                $data = array(
                    'sl' => $value['sl'],
                );
                $this->db->where('id', $value['device_id'])->update('attn_device_setup', $data);
            }
        }
    }

    public function python_add_attendance() {
        $member_id = $this->input->get('member_id');
        $timestamp = $this->input->get('timestamp');
        $ip = $this->input->get('ip');
        $port = $this->input->get('port');
        $device= $this->db->where('ip', $ip)->where('port', $port)->get('attn_device_setup')->row();
        $data = [
            'proxi_id'=> $member_id,
            'date_time'=> $timestamp,
            'device_id'=> $device->id,
            'device_type'=> $device->type,
            'device_ip'=> $ip,
            'device_port'=> $port
        ];

        $this->db->insert('xin_att_machine', $data);
        $emp = $this->db->where('punch_id', $member_id)->get('xin_employees')->row();
        if(!empty($emp)){
            $member_id = [$emp->user_id];
            $this->Attendance_model->attn_process(date('Y-m-d', strtotime($timestamp)), $member_id, null);
        }
    }


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
