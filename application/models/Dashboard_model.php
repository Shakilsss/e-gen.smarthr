<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Dashboard_model extends CI_Model {

    public function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

	// get total number of attendance
    function count_attendance_status_wise($date, $unit_id = null)
    {
        $this->db->select("
                COUNT(*) AS counts,
                SUM(CASE WHEN status = 'Present' AND office_out = 0 THEN 1 ELSE 0 END ) AS office_in,
                SUM(CASE WHEN status = 'Present' AND office_out = 1 THEN 1 ELSE 0 END) AS office_out,
                SUM(CASE WHEN status = 'Absent' THEN 1 ELSE 0 END ) AS absent,
                SUM(CASE WHEN status = 'Leave' THEN 1 ELSE 0 END ) AS leaves,
                SUM(CASE WHEN status = 'sLeave' THEN 1 ELSE 0 END ) AS sLeave,
                SUM(CASE WHEN late_status = '1' THEN 1 ELSE 0 END ) AS late_status,
                SUM(CASE WHEN early_status = '1' THEN 1 ELSE 0 END ) AS early_status
            ");
        if (!empty($unit_id)) {
            $this->db->where('unit_id', $unit_id);
        }
        $this->db->where("attendance_date", $date);
        $query = $this->db->get('xin_attendance_time');
        $result = $query->row();
        return $result;
    }

    // get attendance log
    function get_attn_logs($date, $unit_id = null)
    {
        $this->db->select("log.*, e.first_name, e.last_name, d.designation_name, c.name");
        $query = $this->db->from('xin_attendance_time as log');
        $this->db->join('xin_employees as e', 'log.employee_id = e.user_id');
        $this->db->join('xin_designations as d', 'd.designation_id = e.designation_id');
        $this->db->join('xin_companies as c', 'c.company_id = e.company_id');

        $this->db->where("log.attendance_date", $date);
        if (!empty($unit_id)) {
            $this->db->where('log.unit_id', $unit_id);
        }

        $query = $this->db->get();
        $result = $query->result();
        return $result;
    }

}
