<?php

// defined('BASEPATH') or exit('No direct script access allowed');

require_once APPPATH . 'libraries/API_Controller.php';

class ZK_device_test extends API_Controller
{
    public function __construct()
    {
        header('Access-Control-Allow-Origin: *');
        // Allow methods: GET, POST, OPTIONS
        header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
        // Allow header Content-Type: application/json
        header("Access-Control-Allow-Headers: Content-Type");

        parent::__construct();
        $this->load->library('Zklibrary');
    }
    public function get_data()
    {
        $date = date('Y-m-d');
        $startTime = strtotime($date . ' 00:00:00');
        $endTime = strtotime($date . ' 23:59:59');

        $devices = array(
            array("ip" => "182.160.110.58", "port" => 4372),
            array("ip" => "182.160.110.58", "port" => 4370),
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
        dd($today_data);
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



}
