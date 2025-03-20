<style>
   .header_employees_data {
    border-right: 1px solid #ccc;
    display: flex;
    flex-direction: column;
    gap: 8px;
    padding: 9px;
}
</style>

<?php
$startDate = date('Y-m-d 00:00:00');
$endDate = date('Y-m-d 23:59:59');

$proximityIdsQuery = $this->db
    ->select('proxi_id')
    ->group_by('proxi_id')
    ->where('date_time >=', $startDate)
    ->where('date_time <=', $endDate)
    ->get('xin_att_machine');

$proximityIds = array_column($proximityIdsQuery->result_array(), 'proxi_id');


$total_emp=count($proximityIds);
$in_office=[];
$out_office=[];
    foreach ($proximityIds as $key => $value) {
        $masin = $this->db
            ->select('*')
            ->where('date_time >=', $startDate)
            ->where('date_time <=', $endDate)
            ->where('proxi_id ', $value)
            ->order_by('id', 'DESC')
            ->limit(1)
            ->get('xin_att_machine')
            ->row();
        if ($masin->device_type==1) {
            $in_office[]=$masin;
        }else{
            $out_office[]=$masin;
        }
    }


    $emp_data_l = $this->db->select('profile_picture,first_name,last_name,user_id')->get('xin_employees')->result();

    $leave=[];

    foreach ($emp_data_l as $key => $value) {

        $leavedata=$this->Attendance_model->leave_chech(date('Y-m-d'), $value->user_id);
        if ($leavedata['leave'] == true){
            $leave[]=$value;
        }
    }

    $total_emp+=count($leave);
?>
<div class="container-fluid employee-status">
    <!-- Top row with summary -->
    <div class="row mb-3" style="border-bottom: 1px solid;">
        <div class="col-md-8">
            <div class="col-md-12">
                <div class="col-md-3 header_employees_data">
                    <div class="total-employees">In office</div>
                    <span><?= count($in_office);?></span>
                </div>
                <div class="col-md-3 header_employees_data">
                    <div class="total-employees">Out office</div>
                    <span><?= count($out_office);?></span>
                </div>
                <div class="col-md-3 header_employees_data">
                    <div class="total-employees">On leave</div>
                    <span><?= count($leave) ?></span>
                </div>
                <div class="col-md-3 header_employees_data">
                    <div class="total-employees">Total employees</div>
                    <span class="text-danger"><?= $total_emp;?> In Total</span>
                </div>
            </div>
        </div>

    </div>

    <div class="row">
        <!-- In Offices -->
        <div class="col-md-4" style="height: 65vh;overflow-x: hidden;overflow-y: scroll;">
            <div class="status-header">
                <h5>In Office</h5>
            </div>

            <table class="table table-striped">
                <thead>
                    <tr>
                        <td>Name</td>
                        <td>Image</td>
                        <td>Last Activity</td>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                        foreach ($in_office as $key => $value) { 
                            if (isset($value->proxi_id)) {
                                $emp_data = $this->db->select('profile_picture,first_name,last_name')->where('punch_id', $value->proxi_id)->get('xin_employees')->row();
                                if (isset($emp_data->profile_picture) && isset($emp_data->first_name) && isset($emp_data->last_name)) {
                                    ?>
                                    <tr>
                                        <td><span class="badge badge-danger"><?= $emp_data->first_name.' '.$emp_data->last_name;?> </span></td>
                                        <td>            
                                        <?php if (file_exists(FCPATH . 'uploads/users/' . $emp_data->profile_picture)) { ?>
                                                <img style="height: 31px;border-radius: 50%;" src="<?= base_url() ?>uploads/users/<?= $emp_data->profile_picture ?>" alt="Employee" />
                                            <?php } else { ?>
                                                <img style="height: 31px;border-radius: 50%;" src="<?= base_url() ?>uploads/users/default_male.jpg" alt="Employee" />
                                            <?php } ?>                                        </td>
                                        <td><?= (new DateTime($value->date_time))->format('h:i A');?></td>
                                    </tr>
                                    <?php
                                }
                            }
                        }
                    ?>
                        

                </tbody>
            </table>
        </div>
        <!-- Out Offices -->
        <div class="col-md-4" style="height: 65vh;overflow-x: hidden;overflow-y: scroll;">
            <div class="status-header">
                <h5 class="text-danger">Out Office</h5>
            </div>

            <table class="table table-striped">
                <thead>
                    <tr>
                        <td>Name</td>
                        <td>Image</td>
                        <td>Last Activity</td>
                    </tr>
                </thead>
                <tbody>
                    <?php
                        foreach ($out_office as $key => $value) { 
                            if (isset($value->proxi_id)) {
                                $emp_data = $this->db->select('profile_picture,first_name,last_name')->where('punch_id', $value->proxi_id)->get('xin_employees')->row();
                                if (isset($emp_data) && isset($emp_data->profile_picture) && isset($emp_data->first_name) && isset($emp_data->last_name)) {
                                    ?>
                                    <tr>
                                        <td> <span class="badge badge-danger"><?= $emp_data->first_name.' '.$emp_data->last_name;?></span></td>
                                        <td>
                                            <?php if (file_exists(FCPATH . 'uploads/users/' . $emp_data->profile_picture)) { ?>
                                                <img style="height: 31px;border-radius: 50%;" src="<?= base_url() ?>uploads/users/<?= $emp_data->profile_picture ?>" alt="Employee" />
                                            <?php } else { ?>
                                                <img style="height: 31px;border-radius: 50%;" src="<?= base_url() ?>uploads/users/default_male.jpg" alt="Employee" />
                                            <?php } ?>
                                        </td>
                                        <td><?= (new DateTime($value->date_time))->format('h:i A');?></td>
                                    </tr>
                                    <?php
                                }
                            }
                        }
                    ?>

                </tbody>
            </table>
        </div>
        <!-- Leave -->
        <div class="col-md-4" style="height: 65vh;overflow-x: hidden;overflow-y: scroll;">
            <div class="status-header">
                <h5 class="text-warning">Leave</h5>
            </div>

            <table class="table table-striped">
                <thead>
                    <tr>
                        <td>Name</td>
                        <td>Image</td>
                    </tr>
                </thead>
                <tbody>
                <?php
                        foreach ($leave as $key => $emp_data) { ?>
                                    <tr>
                                        <td> <span class="badge badge-danger"><?= $emp_data->first_name.' '.$emp_data->last_name;?></span></td>
                                        <td>
                                            <?php if (file_exists(FCPATH . 'uploads/users/' . $emp_data->profile_picture)) { ?>
                                                <img style="height: 31px;border-radius: 50%;" src="<?= base_url() ?>uploads/users/<?= $emp_data->profile_picture ?>" alt="Employee" />
                                            <?php } else { ?>
                                                <img style="height: 31px;border-radius: 50%;" src="<?= base_url() ?>uploads/users/default_male.jpg" alt="Employee" />
                                            <?php } ?>
                                        </td>
                                    </tr>
                                    <?php
                               
                        }
                    ?>


                </tbody>
            </table>
        </div>

     
    </div>
</div>

<script>
    setTimeout(() => {
        window.location.reload();
    }, 3000);
</script>
