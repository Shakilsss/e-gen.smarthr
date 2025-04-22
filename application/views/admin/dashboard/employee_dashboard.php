<?php
$session = $this->session->userdata('username');
$get_animate = $this->Xin_model->get_content_animate();
$userid = $session['user_id'];
$name = $this->db->select('first_name,last_name')->where('user_id', $userid)->get('xin_employees')->row();
$from = date('Y-01-01');
$to = date('Y-m-t');
$early_leaves = $this->db->where('employee_id', $userid)->where('early_status', 1)->where('attendance_date BETWEEN "' . $from . '" and "' . $to . '"')->get('xin_attendance_time')->result();
$late_ins = $this->db->where('employee_id', $userid)->where('late_status', 1)->where('attendance_date BETWEEN "' . $from . '" and "' . $to . '"')->get('xin_attendance_time')->result();
?>

<style>
    .card {
        box-shadow: 0 6px 12px rgba(0, 0, 0, 0.15);
        background-color: #ffffff;
        border-radius: 12px;
        overflow: hidden;
        margin-bottom: 30px;
        transition: transform 0.2s ease-in-out;
    }

    .card:hover {
        transform: translateY(-5px);
    }

    .card-header {
        background-color: #599AE7;
        color: #fff;
        padding: 20px;
    }

    .card-header h5 {
        margin: 0;
        font-size: 20px;
        font-weight: 600;
    }

    .card-header span {
        font-size: 14px;
        opacity: 0.9;
    }

    .card-body {
        padding: 15px 20px;
        height: 58vh;
        overflow-y: auto;
        scrollbar-width: thin;
        scrollbar-color: #ccc transparent;
    }

    .card-body::-webkit-scrollbar {
        width: 8px;
    }

    .card-body::-webkit-scrollbar-thumb {
        background-color: #ccc;
        border-radius: 10px;
    }

    .card-body ul {
        padding-left: 0;
        margin: 0;
    }

    .card-body li {
        list-style: none;
        padding: 12px 0;
        border-bottom: 1px solid #eee;
        display: flex;
        justify-content: space-between;
        align-items: center;
        font-size: 15px;
    }

    .card-body li:last-child {
        border-bottom: none;
    }

    .badge-info {
        background-color: #17a2b8;
        padding: 5px 10px;
        border-radius: 20px;
        font-size: 13px;
        color: #fff;
    }

   


    
    @media (max-width: 767px) {
        .card-body {
            height: auto;
            max-height: 50vh;
        }
    }
</style>

<div class="<?php echo $get_animate; ?>">
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

    <div class="col-md-12 par_div">
        <div class="row">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title">Late In Details</h5>
                        <span>Total <?= count($late_ins)?> Late In</span>
                    </div>
                    <div class="card-body">
                        <ul class="list-group list-group-flush">
                            <?php 
                            if (count($late_ins) == 0) {
                                echo 'No Late In';
                            };
                                foreach ($late_ins as $late_in) {
                                   
                            ?>
                                <li>
                                <span><?= $late_in->attendance_date?></span>
                                <span class="badge badge-info"><?= floor($late_in->late_time / 60) . 'h ' . ($late_in->late_time % 60) . 'm' ?></span>
                                </li>
                            <?php 
                                }
                            ?>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title">Early Leave Details</h5>
                        <span>Total <?= count($early_leaves)?> Early Leave</span>
                    </div>
                    <div class="card-body">
                        <ul class="list-group list-group-flush">
                            <?php 

                            if (count($early_leaves) == 0) {
                                echo 'No Early Leave';
                            };

                                foreach ($early_leaves as $early_leave) {
                                    
                                
                            ?>
                                <li>
                                    <span><?= $early_leave->attendance_date?></span>
                                    <span class="badge badge-info"><?= floor($early_leave->early_time / 60) . 'h ' . ($early_leave->early_time % 60) . 'm' ?></span>
                                </li>
                            <?php 
                            } 
                            ?>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
