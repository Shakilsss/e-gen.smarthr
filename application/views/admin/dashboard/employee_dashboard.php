
<?php
    $session = $this->session->userdata('username');
    $get_animate = $this->Xin_model->get_content_animate();
    $userid  = $session[ 'user_id' ];
    $result = $this->db->order_by('id', 'desc')->get('lunch_payment', 1)->row();

    $empdata = $this->db
        ->select('lunch_payment.*, xin_employees.first_name, xin_employees.last_name,
                  xin_designations.designation_name')
        ->from('lunch_payment')
        ->join('xin_employees', 'lunch_payment.emp_id = xin_employees.user_id')
        ->join('xin_designations', 'xin_employees.designation_id = xin_designations.designation_id')
        ->where('lunch_payment.end_date', $result->end_date)
        ->where('lunch_payment.emp_id', $session['user_id'])
        ->order_by('lunch_payment.id', 'desc')
        ->get()
        ->result();

    $data1 = isset($empdata[0]) ? $empdata[0] : 0;
    $taken_meal = 0;
    $paymeal = 0;
    $balanceMeal = 0;

    if (!empty($data1)) {
        $this->load->model("Lunch_model");
        $emp_data = $this->Lunch_model->get_data_date_wise($data1->end_date, $data1->next_date, $data1->emp_id);

        foreach ($emp_data['emp_data'] as $r) {
            $taken_meal += $r->meal_amount;
        }

        $paymeal = isset($data1->pay_amount) ? $data1->pay_amount / 45 : 0;
        $balanceMeal = $paymeal - $taken_meal;
    }



    // get 12 month to current month
    $i = date("Y-m", strtotime("-11 months"));
    $monthNames = array();
    while ($i <=  date("Y-m")) {
        $monthNames[] = date('F', strtotime($i));
        $i = date('Y-m', strtotime('+1 months'. $i));
    }

    // get employee shift schedule information
    $schedule = $this->db->get('xin_office_shift')->row();

    // get employee salary from january to current month
    $salarys = $this->db->select('salary_month,modify_salary,grand_net_salary,advanced_salary')
                        ->where('employee_id', $userid)
                        ->limit(12)
                        ->order_by('payslip_id', 'desc')
                        ->get('xin_salary_payslips')
                        ->result();
    $salarys=array_reverse($salarys);
    $salary = array();
    $salary_month = array();
    foreach ($salarys as $salaryObj) {
        $salary[] = floor($salaryObj->grand_net_salary + $salaryObj->modify_salary+$salaryObj->advanced_salary);
        $salary_month[] = date('F', strtotime($salaryObj->salary_month));
    }
    // dd($salary_month);
    // end
    // dd($salary_month);
    // punch time
    $in_time = "00:00";
    $out_time = "00:00";
    $punch_time = $this->db->select('clock_in, clock_out')
                          ->where('employee_id', $userid)
                          ->where('attendance_date', date('Y-m-d'))
                          ->get('xin_attendance_time')
                          ->row();
    // dd($punch_time);
    if (!empty($punch_time)) {
        $in_time = date('h.i A', strtotime($punch_time->clock_in));
        $out_time = date('h.i A', strtotime($punch_time->clock_out));
    }
    // end punch time

    //get employee name
    $name = $this->db->select('first_name,last_name')
                ->where('user_id', $userid)
                ->get('xin_employees')
                ->row();
    // dd($in_time);

    // Get the current Unix timestamp
    $currentTimestamp = time();

    // Specify the other specific time
    $inTime = strtotime($in_time);
    $outTime = strtotime($out_time);
    $out_Time = strtotime(date('h.i A', strtotime('6.30')));

    // dd($outTime);

    // Calculate the time difference in seconds
    $workingHour = $currentTimestamp - $inTime;
    $over_time   = $outTime - $out_Time;
    // dd($over_time);

    $hours = floor($workingHour / 3600);
    $minutes = floor(($workingHour % 3600) / 60);


    $hourss = floor($over_time / 3600);
    $minutess = floor(($over_time % 3600) / 60);
    // $seconds = $workingHour % 60;

    // Output the time difference
    $total_working_hour = $hours . "." . $minutes;
    $total_over_time_hour = $hourss . "." . $minutess;
    $first_date = date('Y-m-01');
    $second_date = date('Y-m-d');

    $count_p = $this->db->select('COUNT(status) AS count_p')
                    ->where('employee_id', $userid)
                    ->where('status', 'present')
                    ->where("attendance_date BETWEEN '".$first_date."' AND '".$second_date."'")
                    ->get('xin_attendance_time')
                    ->row()->count_p;
    $count_a = $this->db->select('COUNT(status) AS count_a')
              ->where('employee_id', $userid)
              ->where('status', 'absent')
              ->where("attendance_date BETWEEN '".$first_date."' AND '".$second_date."'")
              ->get('xin_attendance_time')
              ->row()->count_a;
    $count_late = $this->db->select('COUNT(status) AS count_late')
              ->where('employee_id', $userid)
              ->where('late_status', '1')
              ->where("attendance_date BETWEEN '".$first_date."' AND '".$second_date."'")
              ->get('xin_attendance_time')
              ->row()->count_late;


    $holidays= $this->db->select('*')->get('xin_holidays')->result();
    $holidayss= $this->db->select('*')->limit(5)->where("start_date > '".date('Y-m-d')."'")->get('xin_holidays')->result();
    // dd($this->db->last_query());
    $leave_calel=get_cal_leave($userid, 1);
    $leave_calsl=get_cal_leave($userid, 2);
    $totaluseleave=$leave_calel+$leave_calsl;
    $all_notice = $this->db->select('*')->get('xin_events')->result();

    $leavemonth=$this->Salary_model->leave_count_status($userid,date('Y-m-01'),date('Y-m-t'), 2);
    $totalleavem=$leavemonth->el+$leavemonth->sl;

    ?>

<style>
    .layout {
        background: var(--bg-2, #F8FBFF);
        margin-left: -56px;
        margin-top: -17px;
        padding-left: 45px;
        padding-top: 22px;
        border-radius: 0px;
    }

    .equal-height-row {
        display: flex;
        flex-wrap: wrap;
        /* margin-top:10px; */
    }

    .equal-height-row [class^="col-"] {
        display: flex;
        flex-direction: column;
    }

    .equal-height-row [class^="col-"]>* {
        flex-grow: 1;
    }

    .col_style {
        padding-right: 5px;
        padding-left: 10px;
    }

    .flex-container {
        display: flex;
    }

    .flex-item {
        flex: 1;
        /* padding-left: 10px; */
    }

    .card_layout {
        display: flex;
        padding: 10px 5px 10px 10px;
        flex-direction: column;
        align-items: flex-start;
        gap: -1px;
        border-radius: 0px;
        border: 1px solid #E3E3E3;
        background: #F9F9F9;
    }

    .containers {
        display: flex;
        justify-content: center;
        align-items: center;
        /* height: 100vh; */
    }

    .circle {
        width: 120px;
        height: 120px;
        background-color: #f1f1f1;
        border-radius: 50%;
        position: relative;
        border: 5px solid var(--b, #599AE7);
    }

    .text {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        text-align: center;


        color: #1F1F1F;
        /* font-family: Roboto; */
        font-size: 16px;
        font-style: normal;
        font-weight: 400;
        line-height: 27px;
    }


    .stats-box {
        display: flex;
        padding: 30px 0px;
        flex-direction: column;
        align-items: center;
        gap: -1px;
        border: 1px solid #E3E3E3;
        background: #F9F9F9;
        border-radius: 5px;
    }

    .stats-box-row {
        display: flex;
        padding: 0px 12px;
        align-items: flex-start;
        gap: 24px;
        margin-top: 14px;
        margin-bottom: 16px;
    }

    hr {
        border: none;
        height: 2px;
        background-color: #F3F3F3;
        margin: 8px 0;
        width: 94%;
        margin-left: 10px;
    }

    .text_s {
        color: #5442A8;
        text-align: center;
        /* font-family: Roboto; */
        font-size: 12px;
        font-style: normal;
        font-weight: 500;
        line-height: 18px;
        letter-spacing: -0.28px;
    }

    /* progress bar style */
    .step-wizard {
        /*background-color: #21d4fd;
        background-image: linear-gradient(19deg, #21d4fd 0%, #b721ff 100%);
        /*height: 100vh;
        width: 100%;
        display: flex;*/
        justify-content: center;
        align-items: center;
    }

    .step-wizard-list {
        /* background: #fff; */
        /* box-shadow: 0 15px 25px rgba(0,0,0,0.1); */
        color: #333;
        list-style-type: none;
        /* border-radius: 10px; */
        display: flex;
        padding: 0px 0px;
        position: relative;
        z-index: 10;
    }

    .step-wizard-item {
        padding: 0 0px;
        flex-basis: 0;
        -webkit-box-flex: 1;
        -ms-flex-positive: 1;
        flex-grow: 1;
        max-width: 100%;
        display: flex;
        flex-direction: column;
        text-align: center;
        min-width: 162px;
        position: relative;
    }

    .step-wizard-item+.step-wizard-item:after {
        content: "";
        position: absolute;
        left: -20%;
        top: 50px;
        background: #21d4fd;
        width: 100%;
        height: 2px;
        transform: translateX(-50%);
        z-index: -10;
    }

    .step-wizard-item+.step-wizard-item:before {
        content: "";
        position: absolute;
        right: -20%;
        top: 50px;
        background: #21d4fd;
        width: 100%;
        height: 2px;
        transform: translateX(-50%);
        z-index: -10;
    }

    .progress-label-top {
        font-size: 14px;
        font-weight: 600;
        margin-bottom: 10px;
    }

    .progress-count {
        height: 40px;
        width: 40px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 50%;
        font-weight: 600;
        margin: 0 auto;
        position: relative;
        z-index: 10;
        color: transparent;
    }

    .progress-count:after {
        content: "";
        height: 40px;
        width: 40px;
        background: #21d4fd;
        position: absolute;
        left: 50%;
        top: 50%;
        transform: translate(-50%, -50%);
        border-radius: 50%;
        z-index: -10;
    }

    .progress-count:before {
        content: "";
        height: 10px;
        width: 20px;
        border-left: 3px solid #fff;
        border-bottom: 3px solid #fff;
        position: absolute;
        left: 50%;
        top: 50%;
        transform: translate(-50%, -60%) rotate(-45deg);
        transform-origin: center center;
    }

    .progress-label {
        font-size: 14px;
        font-weight: 600;
        margin-top: 10px;
    }

    .current-item .progress-count:before,
    .current-item~.step-wizard-item .progress-count:before {
        display: none;
    }

    .current-item~.step-wizard-item .progress-count:after {
        height: 10px;
        width: 10px;
    }

    .current-item~.step-wizard-item .progress-label {
        opacity: 0.5;
    }

    .current-item .progress-count:after {
        background: #fff;
        /*      border: 2px solid #21d4fd;*/
    }

    .current-item .progress-count {
        color: #21d4fd;
    }

    /* progress bar style */
    .sticky-heading {
        position: sticky;
        top: 0;
        z-index: 1000;
        background-color: #f8f8f8;
        padding: 10px 0;
    }

    @media screen and (max-width: 1234px) {
        .equal-height-row {
            flex-direction: column;
        }

        .todayac {
            margin-left: 0 !important;
            margin: 1px 2px 23px 1px;
        }

        .timeac {
            width: 314px !important;
        }

        .col_style {
            width: 606px
        }
    }

    @media screen and (max-width: 992px) {
        .widght {
            width: 47% !important;
        }
    }

    @media screen and (max-width: 595px) {
        .widght {
            width: 100% !important;
        }

        .timeshet {}
    }
</style>

<div class=" <?php echo $get_animate;?>">
    <!-- breadcrumb section -->
    <div class="box-widget widget-user-2 container">
        <!-- Add the bg col-md-3or to the header using any of the bg-* classes  -->
        <div class="widget-user-header layout">
            <h4 class="widget-user-username welcome-hrsale-user" style="margin-top:5px;">
                Welcome back, <span style="color:#599AE7 "><?php echo $name->first_name.' '.$name->last_name?></span>
            </h4>
            <div class="breadcrumbs-hr-top">
                <div class="breadcrumb-wrapper col-xs-12">
                    <ol class="breadcrumb" style="margin-bottom: 10px; margin-left: -25px; margin-top: -5px;">
                        <li class="breadcrumb-item"><a
                                href="<?php echo site_url('admin/dashboard/');?>"><?php echo $this->lang->line('xin_e_details_home');?></a>
                        </li>
                        <li class="breadcrumb-item active">Dashboard</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <!-- Today attendance info -->
    <div class="row equal-height-row " style="margin-top:10px">
        <div class="col-sm-4 col_style">
            <div class="card timeac" style="width: 250px;">
                <div class="card-body flex-container" style="margin-top: -8px;margin-bottom: -20px;">
                    <h5 class="card-title flex-item " style="font-weight: 600;">Timesheet</h5>
                    <h6 class="card-title flex-item " style="margin-left: 83px;"><?php echo date('d M Y')?></h6>
                </div>
                <div class="card-body">
                    <div style="border-radius: 4px;border: 1px solid #E3E3E3;background: #0177bccf;color:white; padding: 5px 0px;">
                        <span style="padding: 0px 0px 0px 30px"><b>Punch In At</b></span>
                        <?php if ($in_time == '00:00' || $in_time == '' || $in_time == null) {
                            $in_time = "<span class='text-danger' >00:00</span>";
                            $total_working_hour = '0.0';
                        } ?>
                        <span style="padding: 0px 0px 0px 0px"><b><?php echo $in_time?></b><span>
                    </div>
                </div>

                <div class="containers">
                    <div class="circle">
                        <div class="text"><?= $total_working_hour." hrs"?></div>
                    </div>
                </div>
                <br>
            </div>
        </div>

        <!-- step bar -->
        <div class="col-sm-8 timeshet">
            <div class="col-sm-12 col_style">
                <div class="card todayac" style="margin-left: -82px;">
                    <div class="card-header" style="border-bottom: 0px !important;">
                        <h5 class="card-title flex-item " style="font-weight: 600;margin-left:10px;">Today Activity<i
                                class="fa icon-time"></i></h5>
                    </div>
                    <div class="card-body">
                        <?php
                            $lunch_end = date('h:i A', strtotime("$schedule->lunch_minute minutes", strtotime($schedule->lunch_time)));
                            $in_time_class = '';
                            $lunch_start_class = '';
                            $lunch_end_class = '';
                            $out_time_class = '';
                            if (date('H:i:s') > $schedule->in_start_time && date('H:i:s') < $schedule->in_time) {
                                $in_time_class = 'current-item';
                            } elseif (date('H:i:s') > $schedule->in_time && date('H:i:s') < $schedule->lunch_time) {
                                $lunch_start_class = 'current-item';
                            } elseif (date('H:i:s') > $schedule->lunch_time && date('H:i:s') < date("H:i:s", strtotime($lunch_end))) {
                                $lunch_end_class = 'current-item';
                            } elseif (date('H:i:s') > date("H:i:s", strtotime($lunch_end)) && date('H:i:s') < $schedule->ot_start_time) {
                                $out_time_class = 'current-item';
                            }
                            ?>
                        <section class="step-wizard" style="margin-top: -15px;">
                            <ul class="step-wizard-list" style="margin-left: -48px;">
                                <li class="step-wizard-item <?= $in_time_class?>">
                                    <span class="progress-label-top">Punch In</span>
                                    <span class="progress-count"><i class="icon-time"></i></span>
                                    <span class="progress-label"><?php echo !empty($punch_time) ? $in_time : "Not punch yet"?></span>
                                </li>
                                <li class="step-wizard-item <?=$lunch_start_class?>">
                                    <span class="progress-label-top">Lunch Time</span>
                                    <span class="progress-count"><i class="fa fa-clock-o"  aria-hidden="true"></i></i></span>
                                    <span
                                        class="progress-label">0</span>
                                </li>
                                <li class="step-wizard-item <?=$lunch_end_class?>">
                                    <span class="progress-label-top">Lunch End</span>
                                    <span class="progress-count"><i class="fa fa-clock-o" aria-hidden="true"></i></span>
                                    <span class="progress-label">0</span>
                                </li>
                                <li class="step-wizard-item <?=$out_time_class?>">
                                    <span class="progress-label-top">Punch Out</span>
                                    <span class="progress-count"><i class="fa fa-clock-o" aria-hidden="true"></i></span>
                                    <?php if(isset($punch_time) && isset($punch_time->out_time)) : ?>
                                        <span class="progress-label"><?php echo $punch_time->out_time; ?></span>
                                    <?php else: ?>
                                        <span class="progress-label">Not punch yet</span>
                                    <?php endif; ?>
                                </li>
                            </ul>
                        </section>

                        <div class="row" style="margin-left: -8px;margin-top: 45px;">
                            <div class="col-sm-3">
                                <p><span style="font-weight:600;font-size: 14px;">Status : </span><span style="font-size: 14px;"><?php echo $in_time >='09:35:00' ? '<span class="text-danger"><b>Late</b></span>' : '<span class="success"><b>On Time</b></span>'?></span></p>
                            </div>
                            <div class="col-sm-4">
                                <p><span style="font-weight:600;font-size: 14px;">Lunch Time : </span><span style="font-size: 14px;">1 Hour</span></p>
                            </div>
                            <div class="col-sm-5">
                                <?php $in = date("h:i A", strtotime($schedule->in_time));
                                $out = date("h:i A", strtotime($schedule->ot_start_time)); ?>
                                <p>
                                    <span style="font-weight:600;font-size: 14px;">Working Time: </span>
                                    <span style="font-size: 14px;"><?= $in .'  -  '. $out; ?></span>
                                </p>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- notice modal -->
<!-- Modal -->
<div id="myModals" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Notice</h4>
            </div>
            <div class="modal-body">
                <p id="title" class="h4 text-center"></p>
                <p id="description" class="text-justify"></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger btn-sm " data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- end notice modal -->


<!-- upcomming holidays modal -->

<div class="modal fade bs-example-modal-lg" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <br>
            <h4 class="modal-title text-center sticky-heading"><b>Upcoming Holidays</b></h4><br>
            <table class="table table-bordered table-striped">
                <thead class="text-center">
                    <tr>
                        <th class="text-center" scope="col">Sl. No.</th>
                        <th class="text-center" scope="col">Name</th>
                        <th class="text-center" scope="col">Date</th>
                        <th class="text-center" scope="col">Day</th>
                        <th class="text-center" scope="col">Day Left</th>
                        <!-- <th class="text-center" scope="col">Total Day</th> -->
                    </tr>
                </thead>
                <tbody>
                    <?php
      $i=1;
// $holidays= $this->db->select('*')->get('xin_holidays')->result();
foreach($holidays as $holiday) {
    $today = new DateTime();  // Current date
    $date = $holiday->start_date;
    $futureDate = new DateTime($date);  // Future date

    if ($futureDate < $today) {
        $daysLeft = 0;
    } else {
        $interval = date_diff($today, $futureDate);
        $daysLeft = $interval->format('%a');
    }

    ?>
                    <tr class="text-center">
                        <td><?php echo $i++ ?></td>
                        <td><?php echo $holiday->event_name?></td>
                        <td><?php echo $holiday->start_date?></td>
                        <td><?php echo date("l", strtotime($holiday->start_date))?></td>
                        <td><?php echo $daysLeft?></td>
                        <!-- <td><?php echo $daysLeft?></td> -->
                    </tr>
                    <?php }?>
                </tbody>
            </table>
            <br> <button type="button" class="btn btn-danger btn-sm" style="float:right;margin-right: 10px;"
                data-dismiss="modal">Close</button><br><br><br>
        </div>
    </div>
</div>
<!-- end upcomming holidays modal -->





<script src="https://cdn.jsdelivr.net/npm/chart.js@3.5.1"></script>

<script>
function myfunc(e) {
    var title = e.getAttribute('data-title');
    var description = e.getAttribute('data-description');
    $("#title").text(title);
    $("#description").text(description);
}

$('#year_id').on('change', function() {
    var year = $('#year_id').find(":selected").val();
    if (year) {
        var url = "<?php echo base_url()?>" + "admin/dashboard/chart";
        $("#my_div").load(url);
    } else {
        alert("KO");
    }
})
$(document).ready(function() {
    $('#datatbale').DataTable();
});
</script>



<script>
const monthNames = <?php echo json_encode($monthNames); ?>;
const dataValues = <?php echo json_encode($salary); ?>;
console.log(dataValues);
console.log(monthNames);

const ctx = document.getElementById('myChart').getContext('2d');
const zerosToPad = monthNames.length - dataValues.length;
console.log(zerosToPad);
const zeroArray = new Array(zerosToPad).fill(0);
const paddedDataValues = zeroArray.concat(dataValues);


function customTooltip({ dataIndex }) {

    if (dataIndex >= 1) { // Check if the dataIndex is greater than or equal to 1
        const previousMonth = monthNames[dataIndex - 1];
        const previousSalary = paddedDataValues[dataIndex ];
        return `${previousMonth}\nSalary: ${previousSalary}`;
    }
    return ''; // Return an empty string for the first month (January)
}

// Create the chart
const myChart = new Chart(ctx, {
type: 'bar',
data: {
    labels: monthNames,
    datasets: [{
        label: 'Salary',
        data: paddedDataValues,
        backgroundColor: 'rgba(75, 192, 192, 0.8)'
    }]
},
options: {
    scales: {
        y: {
            beginAtZero: true
        }
    },
    plugins: {
        tooltip: {
            callbacks: {
                label: customTooltip // Use the custom tooltip function
            }
        }
    }
}
});
</script>
