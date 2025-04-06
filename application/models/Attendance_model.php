<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Attendance_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->load->library('Zklibrary');
    }

    public function attn_process($process_date = null, $emp_ids = null, $status = null){
        // If process date is empty then current date will go to the core process
        if (empty($process_date)) {
            $process_date = date("Y-m-d");
        }
        // Advance Process Not Allowed
        if (strtotime("+1 day", strtotime(date('Y-m-d'))) < strtotime($process_date)) {
            return 'Sorry! advanced process not allowed, Please first process '. date('Y-m-d');
        }
        // dd($emp_ids);
        // Get Employees Id And Check Holiday
        $holiday_day = $this->holiday_check($process_date);
        $employees = $this->get_employees($emp_ids, $status);
        // dd($employees);
        foreach ($employees as $key => $row) {
            $joining_date = $row->date_of_joining;
            $emp_id      = $row->user_id;
            $unit_id     = $row->company_id;
            $shift_id    = $row->shift_id;
            $punch_id    = $row->punch_id;

            // If Punch ID Is Empty Then Will Not Go To The Core Process
            if (empty($punch_id)) {
                continue;
            }

            // If Joining Date Is Greater Then To Process Date Will Not Go To The Core Process`
            if($joining_date > $process_date) {
                $attn_delete = $this->attn_delete_for_eligibility_failed($emp_id, $process_date);
                continue;
            }

            // Check shift change and get shift schedule
            $schedule    = $this->get_shift_schedule($emp_id, $process_date, $shift_id);
            $shift_id    = $schedule->shift_id;
            $schedule_id = $schedule->id;
            $in_start    = $schedule->in_start;
            $in_time     = $schedule->in_time;
            $late_start  = $schedule->late_start;
            $in_end      = $schedule->in_end;

            $out_start   = $schedule->out_start;
            $out_time    = $schedule->out_time;
            $out_end     = $schedule->out_end;
            $of_day      = json_decode($schedule->of_day);

            $in_start_time   = date("Y-m-d H:i:s", strtotime($process_date.' '.$in_start));
            $actual_in_time  = date("Y-m-d H:i:s", strtotime($process_date.' '.$in_time));
            $late_start_time = date("Y-m-d H:i:s", strtotime($process_date.' '.$late_start));
            $in_end_time     = date("Y-m-d H:i:s", strtotime($process_date.' '.$in_end));

            $out_start_time  = date("Y-m-d H:i:s", strtotime($process_date.' '.$out_start));
            $actual_out_time = date("Y-m-d H:i:s", strtotime($process_date.' '.$out_time));
            $out_end_time    = date("Y-m-d H:i:s", strtotime($process_date.' '.$out_end));
            if ($out_time <= '12:00:00') {
                $actual_out_time = date("Y-m-d H:i:s", strtotime($actual_out_time.' +1 day'));
                $out_end_time    = date("Y-m-d H:i:s", strtotime($out_end_time.' +1 day'));
            }

            // get in time and out time of the employee
            $in_time    = $this->check_in_out_time($punch_id, $in_start_time, $in_end_time, 'ASC');
            $out_time   = $this->check_in_out_time($punch_id, $out_start_time, $out_end_time, 'DESC');
            // check leave status
            $leave = $this->leave_chech($process_date, $emp_id);
            $sleave = $this->chech_station_leave($process_date, $emp_id);

            // check attendance status
            $status = ''; $astatus = '';
            if ($leave['leave'] == true) {
                $astatus = 'Leave';
                $status = 'Leave';
            } else if ($sleave['leave'] == true) {
                $astatus = 'sLeave';
                $status = 'sLeave';
                $in_time    = $actual_in_time;
                $out_time   = $actual_out_time;
            } else {
                if ($holiday_day == true) {
                    if (($in_time != '' && strtotime($in_time) < strtotime($out_start_time)) && ($out_time !='' && strtotime($out_time) > strtotime($actual_out_time))) {
                        $astatus = 'Present';
                        $status = 'Holiday';
                    } else {
                        $astatus = 'Holiday';
                        $status = 'Holiday';
                    }
                } else if (in_array(date('D', strtotime($process_date)), $of_day)) {
                    if (($in_time != '' && strtotime($in_time) < strtotime($out_start)) && ($out_time !='' && strtotime($out_time) >= strtotime($actual_out_time))) {
                        $astatus = 'Present';
                        $status = 'Off Day';
                    } else {
                        $astatus = 'Off Day';
                        $status = 'Off Day';
                    }
                } else if ($in_time == '' && $out_time == '') {
                    $astatus = 'Absent';
                    $status = 'Absent';
                } else {
                    $astatus = 'Absent';
                    $status = 'Present';
                }
            }

            // check late & calculation late minute
            $late_status = 0; $late_time = 0;
            if (strtotime($in_time) > strtotime($late_start_time) && strtotime($in_time) != null) {
                $late_status = 1;
                $late_time = round((strtotime($in_time) - strtotime($late_start_time)) / 60);
            }
            // check early out status & early out time
            $early_out_status = 0; $early_out_time = 0;
            if (strtotime($out_time) < strtotime($actual_out_time) && strtotime($out_time) != null) {
                $early_out_status = 1;
                $early_out_time = round((strtotime($actual_out_time) - strtotime($out_time)) / 60);
            }

            // calculation production time
            $production = 0;
            if ($out_time != null && $in_time != null) {
                $production = round((strtotime($out_time) - strtotime($in_time)) / 60);
            }

            $data = array(
                'employee_id'       => $emp_id,
                'unit_id'           => $unit_id,
                'shift_id'          => $shift_id,
                'schedule_id'       => $schedule_id,
                'attendance_date'   => $process_date,
                'clock_in'          => $in_time,
                'clock_out'         => $out_time,
                'late_status'       => $late_status,
                'late_time'         => $late_time,
                'early_status'      => $early_out_status,
                'early_time'        => $early_out_time,
                'production'        => $production,
                'attendance_status' => $astatus,
                'status'            => $status,
            );

            $query = $this->db->where('employee_id', $emp_id)->where('attendance_date', $process_date)->get('xin_attendance_time');
            if($query->num_rows() > 0) {
                $this->db->where('attendance_date', $process_date);
                $this->db->where('employee_id', $emp_id);
                $this->db->update('xin_attendance_time', $data);
            } else {
                $this->db->insert('xin_attendance_time', $data);
            }
        }
        return true;
    }

    public function chech_station_leave($process_date, $emp_id)
    {
        $this->db->where("ap_from_date <=", $process_date);
        $this->db->where("ap_to_date >=", $process_date);
        $this->db->where("emp_id", $emp_id);
        $this->db->where("status", 6);
        $query = $this->db->get("leave_out_station");
        if($query->num_rows() > 0) {
            $leave = array(
                'leave'  => true
            );
        } else {
            $leave = array(
                'leave'  => false
            );
        }
        return $leave;
    }

    public function leave_chech($process_date, $emp_id)
    {
        $this->db->where("from_date <=", $process_date);
        $this->db->where("to_date >=", $process_date);
        $this->db->where("employee_id", $emp_id);
        $this->db->where("status", 2);
        $query = $this->db->get("xin_leave_applications");
        if($query->num_rows() > 0) {
            $leave = array(
                'leave'  => true
            );
        } else {
            $leave = array(
                'leave'  => false
            );
        }
        return $leave;
    }

    public function check_in_out_time($proxi_id, $start_time, $end_time, $order)
    {
        $date_time = '';
        $this->db->select("date_time");
        $this->db->where("date_time BETWEEN '$start_time' and '$end_time'");
        $this->db->where("proxi_id", $proxi_id);
        $this->db->order_by("date_time", $order);
        $this->db->limit("1");
        $query = $this->db->get('xin_att_machine');

        if($query->num_rows() > 0) {
            $date_time = $query->row()->date_time;
        }
        return $date_time;
    }

    public function get_shift_schedule($emp_id, $process_date = null, $shift_id = null)
    {
        $this->db->select("schedule_id");
        $this->db->where("employee_id", $emp_id);
        $this->db->where("attendance_date", $process_date);
        $query = $this->db->get('xin_attendance_time');
        if($query->num_rows() > 0) {
            $schedule_id = $query->row()->schedule_id;
        } else {
            $row = $this->db->where('id', $shift_id)->get('emp_shift_manage')->row();
            $schedule_id = $row->schedule_id;
        }
        $row = $this->db->where('id', $schedule_id)->get('emp_shift_schedule')->row();
        $row->shift_id = $shift_id;
        return $row;
    }

    public function holiday_check($process_date)
    {
        $this->db->where("start_date <=", $process_date);
        $this->db->where("end_date >=", $process_date);
        $query = $this->db->get("xin_holidays");
        if($query->num_rows() > 0) {
            return true;
        } else {
            return false;
        }
    }
    public function get_employees($emp_ids, $status = null)
    {
        $this->db->select('user_id, company_id, office_shift_id as shift_id, punch_id, date_of_joining');
        if (!empty($emp_ids)) {
            $this->db->where_in('user_id', $emp_ids);
        } else {
            $this->db->where_in('status', array(1, 4, 5));
        }
        return $this->db->get('xin_employees')->result();
    }













    public function checking_absent_after_before_holiday($emp_id, $check_day)
    {

        $q = $this->db->where('employee_id', $emp_id)->where('attendance_date', $check_day)->get('xin_attendance_time')->row();
        if($q->status == 'Absent') {
            $cd = date("Y-m-d", strtotime("+2 day", strtotime($check_day)));
            $this->db->where('employee_id', $emp_id)->where('attendance_date', $cd);
            $qs = $this->db->where('employee_id', $emp_id)->get('xin_attendance_time')->row();
            if ($qs->status == 'Absent') {
                $dd = date("Y-m-d", strtotime("+1 day", strtotime($check_day)));
                $this->db->where('attendance_date', $dd);
                $this->db->where('employee_id', $emp_id);
                $this->db->update('xin_attendance_time', array('status' => 'Absent', 'attendance_status' => 'Absent'));
            }
        } elseif ($q->status == 'Leave' && $q->attendance_status == 'Leave') {
            $cd = date("Y-m-d", strtotime("+2 day", strtotime($check_day)));
            $this->db->where('employee_id', $emp_id)->where('attendance_date', $cd);
            $qs = $this->db->where('employee_id', $emp_id)->get('xin_attendance_time')->row();

            if ($qs->status == 'Leave' && $qs->attendance_status == 'Leave') {
                $dd = date("Y-m-d", strtotime("+1 day", strtotime($check_day)));
                $this->db->where('attendance_date', $dd);
                $this->db->where('employee_id', $emp_id);
                $this->db->update('xin_attendance_time', array('status' => 'Absent', 'attendance_status' => 'Absent'));
            }


        } elseif($q->attendance_status == 'HalfDay') {
            $cd = date("Y-m-d", strtotime("+2 day", strtotime($check_day)));
            $this->db->where('employee_id', $emp_id)->where('attendance_date', $cd);
            $qs = $this->db->where('employee_id', $emp_id)->get('xin_attendance_time')->row();

            if ($qs->attendance_status == 'HalfDay') {
                $dd = date("Y-m-d", strtotime("+1 day", strtotime($check_day)));
                $this->db->where('attendance_date', $dd);
                $this->db->where('employee_id', $emp_id);
                $this->db->update('xin_attendance_time', array('status' => 'Absent', 'attendance_status' => 'Absent'));
            }

        }
        return true;
    }

    public function checking_absent_after_offday_holiday($emp_id, $check_day)
    {
		$prev_st = $this->check_off_day_prev($check_day, 'xin_holioff_days');

		if ($prev_st['status'] == true) {
            $query = $this->db->where('employee_id', $emp_id)->where('attendance_date', $prev_st['date'])->get('xin_attendance_time')->row();
            if($query->status == 'Absent') {
                $date1 = new DateTime($check_day);
                $date2 = new DateTime($query->attendance_date);
                $interval = $date1->diff($date2)->days - 1;
                $qqs = $this->db->where('employee_id', $emp_id)->where('attendance_date', $check_day)->get('xin_attendance_time')->row();

                if ($qqs->status != 'Leave' && $qqs->attendance_status != 'Leave') {
                    $this->db->where('attendance_date', $check_day);
                    $this->db->where('employee_id', $emp_id);
                    $this->db->update('xin_attendance_time', array('status' => 'Absent', 'attendance_status' => 'Absent'));
                }

                for ($i=0; $i < $interval; $i++) {
                    $check_day = date("Y-m-d", strtotime("-1 day", strtotime($check_day)));

                    if ($qqs->status != 'Leave' && $qqs->attendance_status != 'Leave') {
                        $this->db->where('attendance_date', $check_day);
                        $this->db->where('employee_id', $emp_id);
                        $this->db->update('xin_attendance_time', array('status' => 'Absent', 'attendance_status' => 'Absent'));
                    }
                }
            }  elseif($query->attendance_status == 'HalfDay') {
                $date1 = new DateTime($check_day);
                $date2 = new DateTime($query->attendance_date);
                $interval = $date1->diff($date2)->days - 1;
                $qqs = $this->db->where('employee_id', $emp_id)->where('attendance_date', $check_day)->get('xin_attendance_time')->row();

                if (!empty($qqs) && $qqs->status != 'Leave' && $qqs->attendance_status != 'Leave') {
                    $this->db->where('attendance_date', $check_day);
                    $this->db->where('employee_id', $emp_id);
                    $this->db->update('xin_attendance_time', array('status' => 'Absent', 'attendance_status' => 'Absent'));
                }

                for ($i=0; $i < $interval; $i++) {
                    $check_day = date("Y-m-d", strtotime("-1 day", strtotime($check_day)));
                    if (!empty($qqs) && $qqs->status != 'Leave' && $qqs->attendance_status != 'Leave') {
                        $this->db->where('attendance_date', $check_day);
                        $this->db->where('employee_id', $emp_id);
                        $this->db->update('xin_attendance_time', array('status' => 'Absent', 'attendance_status' => 'Absent'));
                    }
                }
            }
        }
        return true;
    }

    public function checking_absent_after_before_offday_holiday($emp_id, $check_day)
    {
		$prev_st = $this->check_off_day_prev($check_day, 'xin_holioff_days');
		if ($prev_st['status'] == false) {
            $query = $this->db->where('employee_id', $emp_id)->where('attendance_date', $check_day)->get('xin_attendance_time')->row();
            if ($query->status != 'Present') {
                $check_day = date('Y-m-d', strtotime('+2 days'. $check_day));
                $query = $this->db->where('employee_id', $emp_id)->where('attendance_date', $check_day)->get('xin_attendance_time')->row();
                $check_day = date('Y-m-d', strtotime('-1 days'. $check_day));
               if (!empty($query)) {

                if ($query->status == 'Absent') {
                    $this->db->where('attendance_date', $check_day);
                    $this->db->where('employee_id', $emp_id);
                    $this->db->update('xin_attendance_time', array('status' => 'Absent', 'attendance_status' => 'Absent'));
                } else if ($query->status == 'Leave') {
                    $this->db->where('attendance_date', $check_day);
                    $this->db->where('employee_id', $emp_id);
                    $this->db->update('xin_attendance_time', array('status' => 'Absent', 'attendance_status' => 'Absent'));
                }  else if ($query->attendance_status == 'HalfDay') {
                    $this->db->where('attendance_date', $check_day);
                    $this->db->where('employee_id', $emp_id);
                    $this->db->update('xin_attendance_time', array('status' => 'Absent', 'attendance_status' => 'Absent'));
                }
               }
            }
        }
        return true;
    }

    function check_off_day_prev($date, $table) {
		$check = $this->db->where('start_date', $date)->get($table)->row();
		if(!empty($check)) {
			$date = date('Y-m-d', strtotime('-1 days'. $date));
			$check2 = $this->db->where('start_date', $date)->get($table)->row();
			if(empty($check2)) {
				return array('status' => true, 'date' => $date);
			} else {
				$date = date('Y-m-d', strtotime('-1 days'. $date));
				$check3 = $this->db->where('start_date', $date)->get($table)->row();
				if (empty($check3)) {
					return array('status' => true, 'date' => $date);
				} else {
					$date = date('Y-m-d', strtotime('-1 days'. $date));
					$check4 = $this->db->where('start_date', $date)->get($table)->row();
					if (empty($check4)) {
						return array('status' => true, 'date' => $date);
					} else {
						return array('status' => true, 'date' => $date);
					}
				}
			}
		} else {
			return array('status' => false, 'date' => $date);
		}
	}

    public function leave_process($leave_id) {
        $data= $this->db->where('leave_id', $leave_id)->get('xin_leave_applications')->row();
        $first_date = date('Y-m-d', strtotime($data->from_date));
        $last_date = date('Y-m-d', strtotime($data->to_date));
        $employee_id = $data->employee_id;
        $currentDate = $first_date;
        do {
            $this->attn_process($currentDate, [$employee_id]);
            $currentDate = date('Y-m-d', strtotime($currentDate . ' +1 day'));
        } while ($currentDate <= $last_date);
    }

    public function get_attn_data_from_machine($date){
        $devices = array(
            array("ip" => "192.168.30.14", "port" => 4370),
            array("ip" => "192.168.30.20", "port" => 4370)
        );
        $startTime = strtotime($date.' 00:00:00');
        $endTime = strtotime($date.' 23:59:59');
        $batchData = array(); // Array to store batch insert data
        // Prepare an array to store employee IDs associated with prox IDs
        foreach ($devices as $index => $device){
            $attendance = $this->retrieveAttendance($device["ip"], $device["port"], $startTime, $endTime);
            foreach ($attendance as $at) {
                $proxi_id = $at[1];
                $time = $at[3];
                $in_time = date('Y-m-d H:i:s', strtotime($time));
                $this->db->where("proxi_id", $proxi_id);
                $this->db->where("date_time", $in_time);
                $query1 = $this->db->get("xin_att_machine");
                $num_rows1 = $query1->num_rows();
                if($num_rows1 == 0) {
                    $batchData[] = array(
                        'proxi_id'  => $proxi_id,
                        'date_time' => $in_time,
                        'device_id' => $index,
                    );
                }
            }
        }
        if (!empty($batchData)) {
            $this->db->insert_batch("xin_att_machine", $batchData);
        }
    }
    function retrieveAttendance($ip, $port, $startTime, $endTime)
    {
        $zk = new ZKLibrary($ip, $port);
        $zk->connect();
        $zk->disableDevice();
        $attendance = $zk->getAttendance();
        $zk->enableDevice();
        $zk->disconnect();
        $filteredAttendance = array();
        foreach ($attendance as $at) {
            $dateTime = strtotime($at[3]);
            if ($dateTime >= $startTime && $dateTime <= $endTime) {
                $filteredAttendance[] = $at;
            }
        }
        return $filteredAttendance;
    }

    public function attn_delete_for_eligibility_failed($emp_id, $att_date)
    {
        $this->db->where('employee_id', $emp_id);
        $this->db->where('attendance_date', $att_date);
        $this->db->delete('xin_attendance_time');
        return true;
    }

    public function check_movement_time($emp_id, $process_date, $order)
    {
        $this->db->where('employee_id', $emp_id);
        $this->db->where('date', $process_date);
        $this->db->order_by("id", $order);
        return $this->db->get('xin_employee_move_register');
    }

    public function leaves($emp_ids, $first_date, $second_date, $stutuss)
    {
        $this->db->select('*');
        $this->db->where_in('employee_id', $emp_ids);
        $this->db->where_in('status', $stutuss);
        $this->db->where('from_date >=', $first_date);
        $this->db->where('to_date <=', $second_date);
        $this->db->order_by('employee_id', 'ASC');
        return $this->db->get('xin_leave_applications')->result();
    }
    public function leavesm($emp_ids =null, $first_date, $second_date)
    {
        $this->db->select('*');
        if ( $emp_ids != null) {
            $this->db->where_in('employee_id', $emp_ids);
        }
        $this->db->where('from_date >=', $first_date);
        $this->db->where('to_date <=', $second_date);
        $this->db->order_by('employee_id', 'ASC');
        $this->db->group_by('employee_id');
        return $this->db->get('xin_leave_applications')->result();
    }
    public function leavesm_singale($emp_ids, $first_date, $second_date, $status=null)
    {
        $this->db->select('*');
        $this->db->where('employee_id', $emp_ids);
        $this->db->where('from_date >=', $first_date);
        $this->db->where('to_date <=', $second_date);
        if ($status != null) {
            $this->db->where('status', $status);
        }
        return $this->db->get('xin_leave_applications')->result();
    }

    public function get_emp_info($emp_ids = null)
    {
        $this->db->select('
                xin_employees.user_id,
                xin_employees.notify_incre_prob,
                xin_employees.marital_status,
                xin_employees.employee_id,
                xin_employees.leave_effective,
                xin_employees.office_shift_id as shift_id,
                xin_employees.first_name,
                xin_employees.salary,
                xin_employees.last_name,
                xin_employees.date_of_birth,
                xin_employees.date_of_joining,
                xin_employees.department_id,
                xin_employees.designation_id,
                xin_employees.company_id,
                xin_employees.profile_picture,
                xin_departments.department_name,
                xin_designations.designation_name,
            ');

        $this->db->from('xin_employees');
        $this->db->from('xin_departments');
        $this->db->from('xin_designations');
        $this->db->where('xin_employees.company_id', 1);
        $this->db->where('xin_employees.department_id = xin_departments.department_id');
        $this->db->where('xin_employees.designation_id = xin_designations.designation_id');
        $this->db->where_in('xin_employees.user_id', $emp_ids);
        return $this->db->get()->result();

    }










    public function get_employee($emp_ids = null)
    {
        $this->db->select('*');
        $this->db->from('xin_employees');
        $this->db->where_in('xin_employees.user_id', $emp_ids);
        return $this->db->get()->result();

    }

    public function daily_report($attendance_date, $emp_id=null, $status = null, $late_status=null)
    {

        $this->db->select('
            xin_employees.user_id as emp_id,
            xin_employees.employee_id,
            xin_employees.first_name,
            xin_employees.last_name,
            xin_employees.department_id,
            xin_employees.designation_id,
            xin_employees.date_of_joining,
            xin_departments.department_name,
            xin_designations.designation_name,
            xin_attendance_time.attendance_date,
            xin_attendance_time.clock_in,
            xin_attendance_time.clock_out,
            xin_attendance_time.attendance_status,
            xin_attendance_time.status,
            xin_attendance_time.late_status,
            xin_attendance_time.comment,
        ');

        $this->db->from('xin_employees');
        $this->db->from('xin_departments');
        $this->db->from('xin_designations');
        $this->db->from('xin_attendance_time');

        if ($late_status != null && $late_status != 0 && $late_status != '') {
            $this->db->where("xin_attendance_time.late_status", 1);
        }

        $this->db->where("xin_employees.is_active", 1);
        $this->db->where("xin_attendance_time.attendance_date", $attendance_date);
        if($status[0]!='all') {
            $this->db->where_in("xin_attendance_time.status", $status);
        }

        if (!empty($emp_id)) {
            $this->db->where_in("xin_attendance_time.employee_id", $emp_id);
        }
        $this->db->where('xin_employees.department_id = xin_departments.department_id');
        $this->db->where('xin_employees.designation_id = xin_designations.designation_id');
        $this->db->where('xin_employees.user_id = xin_attendance_time.employee_id');
        $this->db->order_by('xin_attendance_time.clock_in', "ASC");
        $data = $this->db->get()->result();

        if($data) {
            return $data;
        } else {
            return "<h4 style='color:red; text-align:center'>Requested list is empty</h4>";
        }
    }
    public function floor_movement($attendance_date, $emp_id)
    {

        $this->db->select('
            xin_employees.user_id as emp_id,
            xin_employees.employee_id,
            xin_employees.first_name,
            xin_employees.last_name,
            xin_employees.department_id,
            xin_employees.designation_id,
            xin_employees.date_of_joining,
            xin_departments.department_name,
            xin_designations.designation_name,
            xin_employee_floor_move.*,

        ');

        $this->db->from('xin_employees');
        $this->db->from('xin_departments');
        $this->db->from('xin_designations');
        $this->db->from('xin_employee_floor_move');
        $this->db->where("xin_employees.is_active", 1);
        $this->db->where("xin_employee_floor_move.date", $attendance_date);
        $this->db->where_in("xin_employee_floor_move.user_id", $emp_id);
        $this->db->where('xin_employees.department_id = xin_departments.department_id');
        $this->db->where('xin_employees.designation_id = xin_designations.designation_id');
        $this->db->where('xin_employees.user_id = xin_employee_floor_move.user_id');
        $data = $this->db->get()->result();

        if($data) {
            return $data;
        } else {
            return "<h4 style='color:red; text-align:center'>Requested list is empty</h4>";
        }
    }
    public function today_floor_movement($emp_id,$date=null)
    {

        $this->db->select('
            xin_employees.user_id as emp_id,
            xin_employees.employee_id,
            xin_employees.first_name,
            xin_employees.last_name,
            xin_employees.department_id,
            xin_employees.designation_id,
            xin_employees.date_of_joining,
            xin_departments.department_name,
            xin_designations.designation_name,
            xin_employee_floor_move.*,

        ');

        $this->db->from('xin_employees');
        $this->db->from('xin_departments');
        $this->db->from('xin_designations');
        $this->db->from('xin_employee_floor_move');
        $this->db->where("xin_employees.is_active", 1);
        if($date!=null) {
            $this->db->where("xin_employee_floor_move.date", $date);
        }else{
            $this->db->where("xin_employee_floor_move.date", date('Y-m-d'));
        }
        $this->db->where("xin_employee_floor_move.user_id", $emp_id);
        $this->db->where('xin_employees.department_id = xin_departments.department_id');
        $this->db->where('xin_employees.designation_id = xin_designations.designation_id');
        $this->db->where('xin_employees.user_id = xin_employee_floor_move.user_id');
        $data = $this->db->get()->result();

        return $data;

    }
    public function latecomment($attendance_date, $emp_id)
    {
        $this->db->select('
            xin_employees.user_id as emp_id,
            xin_employees.employee_id,
            xin_employees.first_name,
            xin_employees.last_name,
            xin_employees.department_id,
            xin_employees.designation_id,
            xin_employees.date_of_joining,
            xin_departments.department_name,
            xin_designations.designation_name,
            xin_attendance_time.attendance_date,
            xin_attendance_time.clock_in,
            xin_attendance_time.clock_out,
            xin_attendance_time.attendance_status,
            xin_attendance_time.status,
            xin_attendance_time.late_status,
            xin_attendance_time.comment,
        ');

        $this->db->from('xin_employees');
        $this->db->from('xin_departments');
        $this->db->from('xin_designations');
        $this->db->from('xin_attendance_time');
        $this->db->where("xin_employees.is_active", 1);
        $this->db->where("xin_attendance_time.attendance_date", $attendance_date);
        $this->db->where_in("xin_attendance_time.status", 'Absent');
        $this->db->where_in("xin_attendance_time.employee_id", $emp_id);
        $this->db->where('xin_employees.department_id = xin_departments.department_id');
        $this->db->where('xin_employees.designation_id = xin_designations.designation_id');
        $this->db->where('xin_employees.user_id = xin_attendance_time.employee_id');
        $this->db->order_by('xin_attendance_time.clock_in', "ASC");
        $data = $this->db->get()->result();

        if($data) {
            return $data;
        } else {
            return "<h4 style='color:red; text-align:center'>Requested list is empty</h4>";
        }
    }
    public function get_total_present($value, $first_date, $second_date)
    {
        $this->db->select('SUM(CASE WHEN xin_attendance_time.status = "Present" THEN 1 WHEN xin_attendance_time.status = "HalfDay" THEN 0.5 ELSE 0 END) as total_present');
        $this->db->from('xin_attendance_time');
        $this->db->where("xin_attendance_time.attendance_date BETWEEN '$first_date' AND '$second_date'");
        $this->db->where("xin_attendance_time.status", 'Present');
        $this->db->where("xin_attendance_time.employee_id", $value);
        $result = $this->db->get()->row();

        if (empty($result)) {
            return 0;
        }

        $data = $result;
        if (isset($data->total_present)) {
            return $data->total_present;
        } else {
            return 0;
        }
    }
    public function get_total_absent($value, $first_date, $second_date)
    {
        $this->db->select('SUM(CASE WHEN xin_attendance_time.status = "Absent" THEN 1 ELSE 0 END) as total_absent');
        $this->db->from('xin_attendance_time');
        $this->db->where("xin_attendance_time.attendance_date BETWEEN '$first_date' AND '$second_date'");
        $this->db->where("xin_attendance_time.status", 'Absent');
        $this->db->where("xin_attendance_time.employee_id", $value);
        $result = $this->db->get();

        if ($result->num_rows() > 0) {
            $row = $result->row();
            return $row->total_absent;
        } else {
            return 0;
        }
    }

    public function get_total_late($value, $first_date, $second_date)
    {
        $this->db->select('xin_attendance_time.attendance_date');
        $this->db->from('xin_attendance_time');
        $this->db->where("xin_attendance_time.attendance_date BETWEEN '$first_date' AND '$second_date'");
        $this->db->where("xin_attendance_time.late_status", 1);
        $this->db->where("xin_attendance_time.employee_id", $value);
        return count($this->db->get()->result());
    }
    public function get_total_late_monthly($first_date, $second_date)
    {
        $this->db->select('xin_attendance_time.*, xin_employees.first_name, xin_employees.last_name');
        $this->db->from('xin_attendance_time');
        $this->db->join('xin_employees', 'xin_employees.user_id = xin_attendance_time.employee_id');
        $this->db->where("xin_attendance_time.attendance_date BETWEEN '$first_date' AND '$second_date'");
        $this->db->where("xin_attendance_time.late_status", 1);
        return $this->db->get()->result();
    }

	public function leave_cal_all($id,$date)
	{
		$user_info = $this->db->where('user_id', $id)
        ->get('xin_employees')->result();
		foreach ($user_info as $key => $row) {
            if ($row->is_leave_on == 0 ) {
                    $data = array(
                        'emp_id' => $row->user_id,
                        'el_total' => 0,
                        'sl_total' => 0,
                        'el_balanace' => 0,
                        'sl_balanace' => 0,
                        'year' => date('Y', strtotime($date)),
                    );
                }else{
                    $d1 = new DateTime(date('Y-12-31', strtotime($date)));
                    $d2 = new DateTime($row->leave_effective);
                    $d2->modify('-1 day');
                    if ($d1 < $d2) {
                        //dd('hi');
                        $data = array(
                            'emp_id' => $row->user_id,
                            'el_total' => 0,
                            'sl_total' => 0,
                            'el_balanace' => 0,
                            'sl_balanace' => 0,
                            'year' => date('Y', strtotime($date)),
                        );
                    } else{
                        $Months = $d2->diff($d1);
                        $month = $Months->m;
                        if ($Months->y > 0) {
                            $month = 12;
                        }

                        $qty = round(($month / 3), 2);
                        $numberString = (string) $qty;
                        $parts = explode('.', $numberString);
                        $integerPart = $parts[0];
                        if (isset($parts[1])) {
                            $decimalPart = $parts[1];
                        } else {
                            $decimalPart = 0;
                        }

                        if ($decimalPart > 50) {
                            $integerPart += 0.5;
                        }
                        $leave_en = $this->check_leave_balance($row->user_id, date('Y', strtotime($date)));
                        $data = array(
                            'emp_id' => $row->user_id,
                            'el_total' => $month,
                            'sl_total' => $integerPart,
                            'el_balanace' => ($month-$leave_en->el),
                            'sl_balanace' => ($integerPart-$leave_en->sl),
                            'year' => date('Y', strtotime($date)),
                        );
                    }
                }
            $pre=$this->db->where('emp_id', $row->user_id)->where('year', date('Y', strtotime($date)))->get('leave_balanace');
            if($pre->num_rows()==0){
                $this->db->insert('leave_balanace', $data);
            }else{
                $this->db->where('emp_id', $row->user_id)->where('year', date('Y', strtotime($date)))->update('leave_balanace', $data);
            }
		}
	}
    public function check_leave_balance($value, $year) {
        $this->db->select('COALESCE(SUM(CASE WHEN leave_type_id = 1 THEN qty ELSE 0 END), 0) as el, COALESCE(SUM(CASE WHEN leave_type_id = 2 THEN qty ELSE 0 END), 0) as sl', false);
        $this->db->from('xin_leave_applications');
        $this->db->where('employee_id', $value);
        $this->db->where('current_year', $year);
        $this->db->where('status', 2);
        return $this->db->get()->row();
    }

    public function get_total_overtime($value, $first_date, $second_date)
    {
        $this->db->select('xin_attendance_time.attendance_date');
        $this->db->from('xin_attendance_time');
        $this->db->where("xin_attendance_time.attendance_date BETWEEN '$first_date' AND '$second_date'");
        $this->db->where("xin_attendance_time.status", 'Present');
        $this->db->where("xin_attendance_time.ot >= 30");
        $this->db->where("xin_attendance_time.employee_id", $value);
        return count($this->db->get()->result());
    }
    public function get_total_leave($value, $first_date, $second_date)
    {
        $this->db->select('xin_leave_applications.*');
        $this->db->from('xin_leave_applications');
        $this->db->where("xin_leave_applications.from_date BETWEEN '$first_date' AND '$second_date'");
        $this->db->where("xin_leave_applications.status", 2);
        $this->db->where("xin_leave_applications.employee_id", $value);
        $data=$this->db->get()->result();
        $total = 0;
        foreach($data as $dat) {
            $total += $dat->qty;
        }
        return $total;
    }



    public function lunch_report($attendance_date, $emp_id, $status=null, $late_status=null)
    {

        $this->db->select('
            xin_employees.user_id as emp_id,
            xin_employees.employee_id,
            xin_employees.first_name,
            xin_employees.last_name,
            xin_employees.department_id,
            xin_employees.designation_id,
            xin_employees.date_of_joining,
            xin_departments.department_name,
            xin_designations.designation_name,
            xin_attendance_time.attendance_date,
            xin_attendance_time.status,
            xin_attendance_time.lunch_in,
            xin_attendance_time.lunch_out,
            xin_attendance_time.lunch_late_status,
        ');


        $this->db->from('xin_employees');
        $this->db->from('xin_departments');
        $this->db->from('xin_designations');
        $this->db->from('xin_attendance_time');
        $this->db->where("xin_employees.is_active", 1);
        if($late_status=='1') {
            $this->db->where("xin_attendance_time.lunch_late_status", 1 );
        }
        $this->db->where("xin_attendance_time.attendance_date", $attendance_date);

        $this->db->where_in("xin_attendance_time.employee_id", $emp_id);
        $this->db->where("xin_attendance_time.status", "Present");
        $this->db->where('xin_employees.department_id = xin_departments.department_id');
        $this->db->where('xin_employees.designation_id = xin_designations.designation_id');
        $this->db->where('xin_employees.user_id = xin_attendance_time.employee_id');
        $data = $this->db->get()->result();
        if($data) {
            return $data;
        } else {
            return "<h4 style='color:red; text-align:center'>Requested list is empty</h4>";
        }
    }



    public function early_out_report($attendance_date, $emp_id, $status)
    {

        // dd($out_time);
        // $out_time= $this->db->select('ot_start_time')->from('xin_office_shift')->get()->result();
        $this->db->select('
            xin_employees.user_id as emp_id,
            xin_employees.employee_id,
            xin_employees.first_name,
            xin_employees.last_name,
            xin_employees.department_id,
            xin_employees.designation_id,
            xin_employees.date_of_joining,
            xin_departments.department_name,
            xin_designations.designation_name,
            xin_attendance_time.attendance_date,
            xin_attendance_time.attendance_status,
            xin_attendance_time.clock_in,
            xin_attendance_time.clock_out,
            xin_attendance_time.early_out_status,
        ');



        $this->db->from('xin_employees');
        $this->db->from('xin_departments');
        $this->db->from('xin_designations');
        $this->db->from('xin_attendance_time');
        $this->db->where("xin_employees.is_active", 1);
        $this->db->where("xin_attendance_time.attendance_date", $attendance_date);
        $this->db->where_in("xin_attendance_time.employee_id", $emp_id);
        $this->db->where_in("xin_attendance_time.early_out_status", 1);
        $this->db->where("xin_attendance_time.attendance_status", "Present");
        $this->db->where('xin_employees.department_id = xin_departments.department_id');
        $this->db->where('xin_employees.designation_id = xin_designations.designation_id');
        $this->db->where('xin_employees.user_id = xin_attendance_time.employee_id');


        $data = $this->db->get()->result();
        // dd($data);

        if($data) {
            return $data;
        } else {
            return "<h4 style='color:red; text-align:center'>Requested list is empty</h4>";
        }
    }


    public function movement_report($attendance_date, $emp_id)
    {


        $this->db->select('
            xin_employee_move_register.employee_id,
            xin_employee_move_register.date,
            xin_employee_move_register.out_time,
            xin_employee_move_register.in_time,
            xin_employee_move_register.reason,
            xin_employees.employee_id,
            xin_employees.first_name,
            xin_employees.last_name,

        ');

        $this->db->from('xin_employees');
        $this->db->from('xin_employee_move_register');
        $this->db->where_in("xin_employee_move_register.employee_id", $emp_id);
        $this->db->where("xin_employee_move_register.date", $attendance_date);
        $this->db->where("xin_employees.is_active", 1);
        $this->db->where('xin_employee_move_register.employee_id = xin_employees.user_id');
        $data = $this->db->get()->result();
        // dd($data);

        if($data) {
            return $data;
        } else {
            return "<h4 style='color:red; text-align:center'>Requested list is empty</h4>";
        }
    }







    public function movment_status_report($f1_date, $f2_date, $statusC)
    {

        $this->db->select('
        xin_employee_move_register.employee_id,
        xin_employee_move_register.date,
        xin_employee_move_register.out_time,
        xin_employee_move_register.in_time,
        xin_employee_move_register.reason,
        xin_employee_move_register.request_amount,
        xin_employee_move_register.payable_amount,
        xin_employees.department_id,
        xin_employees.designation_id,
        xin_departments.department_name,
        xin_designations.designation_name,
        xin_employees.employee_id,
        xin_employees.first_name,
        xin_employees.last_name
    ');

        $this->db->from('xin_employees');
        $this->db->join('xin_designations', 'xin_designations.designation_id = xin_employees.designation_id');
        $this->db->join('xin_departments', 'xin_departments.department_id = xin_employees.department_id');
        $this->db->join('xin_employee_move_register', 'xin_employee_move_register.employee_id = xin_employees.user_id');
        $this->db->where('xin_employees.is_active', 1);
        $this->db->where("xin_employee_move_register.date BETWEEN '$f1_date' AND '$f2_date'");
        if ($statusC!="all") {
            $this->db->where('xin_employee_move_register.status', $statusC);
        }
        $query = $this->db->get();
        $data = $query->result();



        if ($query->num_rows() > 0) {
            return $data;

        } else {
            return [];
        }
    }

  public function get_movement_register($id = null)
  {
      $this->db->select('
          empm.id, mr.title AS title, em.first_name, em.last_name, empm.employee_id AS emp_id, empm.date, empm.out_time, empm.in_time, empm.status
      ');

      $this->db->from('xin_employee_move_register as empm');

      if ($id != null) {
          $this->db->where('empm.employee_id', $id);
      }

      $this->db->join('xin_employees as em', 'em.user_id = empm.employee_id');
      $this->db->join('xin_employee_move_reason as mr', 'empm.reason = mr.id');

      $this->db->order_by('empm.id', 'DESC');

      return $this->db->get()->result();
  }



    public function apply_for_ta_da($id, $amount, $details)
    {

        $this->db->query("UPDATE  xin_employee_move_register
                       SET     `request_amount`  = '$amount',
                               `reason` = '$details',
                               `status`  = 1
                       WHERE   id        = '$id'
                    ");
        return "ok";
    }
    public function late_id($first_date, $second_date, $emp_id)
    {
        $this->db->select('xin_attendance_time.employee_id');
        $this->db->from('xin_attendance_time');
        $this->db->where('xin_attendance_time.attendance_date >=', $first_date);
        $this->db->where('xin_attendance_time.attendance_date <=', $second_date);
        $this->db->where('xin_attendance_time.late_status', 1);
        $this->db->where_in('xin_attendance_time.employee_id', $emp_id);
        $this->db->group_by('xin_attendance_time.employee_id');
        $subquery = $this->db->get_compiled_select();
        $this->db->select('employee_id');
        $this->db->from("($subquery) as subquery");
        $data = $this->db->get()->result();
        $lateid = array();
        foreach ($data as $row) {
            $lateid[] = $row->employee_id;
        }
       return $lateid;
    }

    public function update_ta_da($id, $amount, $status)
    {

        $this->db->query("UPDATE  xin_employee_move_register
                           SET     `payable_amount`  = '$amount',
                                   `status`  = '$status'
                           WHERE   id        = '$id'
                        ");
        return "update";
    }


    public function modify_for_ta_da($id)
    {
        $this->db->select("request_amount,reason,status")
                ->from('xin_employee_move_register')
                ->where('id', $id);
        return $result = $this->db->get()->result();
    }
    public function view_ta_da($id)
    {
        $this->db->select('emr.*, emd.*, e.first_name, e.last_name')
                ->from('xin_employee_move_register as emr')
                ->join('xin_employee_move_details as emd', 'emr.id = emd.move_id')
                ->join('xin_employees as e', 'emr.employee_id = e.user_id')
                ->where('emr.id', $id);

        $query = $this->db->get();
        return $query->result();
    }
    public function get_employee_ajax_request($status, $salary_month=null)
    {
        $left_employee=[];
        if ($salary_month != null) {
            $first_date=date('Y-m-01', strtotime($salary_month));
            $second_date=date('Y-m-t', strtotime($salary_month));
            $this->db->select('emp_id');
            $this->db->from('xin_employee_left_resign');
            $this->db->where('effective_date >=', $first_date);
            $this->db->where('effective_date <=', $second_date);
            $this->db->group_by('emp_id');
            $left_employee_array = $this->db->get()->result_array();
            $left_employee=array_column($left_employee_array, 'emp_id');
            //dd($left_employee);
        }
        $this->db->select('user_id as emp_id, first_name, last_name');
        if ($status == 1) {
            $this->db->where_in('status', array(1,4,5));
        } else if ($status == 2){
            $this->db->where_in('status', array(2,3));
            $this->db->where_in('user_id', $left_employee);
        }
        // $this->db->where('company_id', 1);
        $this->db->order_by('user_id', 'asc');
        return $result = $this->db->get('xin_employees')->result();
        // dd($result);
    }
    public function gettodaylog($date, $user_id)
    {
        $this->db->select('*');
        $this->db->from('xin_attendance_time');
        $this->db->where('employee_id', $user_id);
        $this->db->where('attendance_date', date('Y-m-d', strtotime($date)));
        $this->db->limit(1); // Limit the result to one row
        $data = $this->db->get()->row();
        return $data;
    }
    public function get_move_place()
    {
        $query = $this->db->order_by('place_id', 'DESC')->get('xin_employee_move_place');
        return $query->result();
    }
    public function add_move_place($data)
    {
        $this->db->insert('xin_employee_move_place', $data);
    }
    public function update_move_place($place_id, $data)
    {
        $this->db->where('place_id', $place_id);
        $this->db->update('xin_employee_move_place', $data);
    }
    public function delete_move_place($place_id)
    {
        $this->db->where('place_id', $place_id);
        $this->db->delete('xin_employee_move_place');
    }
    public function red_alert_check($id){
        // $this->db->where('leave_id', $id);
        // $data=$this->db->get('xin_leave_applications')->row();
        // $from_date=

    }
    public function get_total_meeting_monthly($first_date,$last_date){
        $this->db->select('xin_employee_move_register.*, xin_employees.first_name, xin_employees.last_name');
        $this->db->from('xin_employee_move_register');
        $this->db->join('xin_employees', 'xin_employees.user_id = xin_employee_move_register.employee_id');
        $this->db->where('date BETWEEN "'.$first_date.'" AND "'.$last_date.'"');
        return $this->db->get()->result();


    }
}
