
  <?php $session = $this->session->userdata('username');?>
  <?php $user = $this->Xin_model->read_user_info($session['user_id']);?>
  <?php
    $datetime1 = new DateTime($from_date);
    $datetime2 = new DateTime($to_date);
    $interval = $datetime1->diff($datetime2);

    if(strtotime($from_date) == strtotime($to_date)){
      $no_of_days =1;
    } else {
      $no_of_days = $interval->format('%a') +1;
    }
    $leave_user = $this->Xin_model->read_user_info($employee_id);
    $department = $this->Department_model->read_department_information($user[0]->department_id);
  ?>

  <?php $role_resources_ids = $this->Xin_model->user_role_resource(); ?>
  <?php if (isset($error)) { ?>
    <div class="alert alert-danger" role="alert">
        <?php echo $error; ?>
    </div>
  <?php } ?>

  <?php if (isset($success)) { ?>
    <div class="alert alert-success" role="alert">
        <?php echo $success; ?>
    </div>
  <?php } ?>

  <?php
    $userid  = $session['user_id'];
    $unit_id  = $session['unit_id'];

    $gearn = $this->db->where('type', 'cl')->get('xin_leave_type')->row()->days_per_year;
    $gsick = $this->db->where('type', 'sl')->get('xin_leave_type')->row()->days_per_year;
    $earn = $gearn - $used_leave->cl;
    $sick = $gsick - $used_leave->sl;
  ?>

  <!-- replace leave cal -->
  <?php
    $rl_rule = $this->db->where('status', 1)->get('leave_settings')->row()->replace_leave;
    $nfdate = date('Y-m-01', strtotime('-1 months'));
    $nsdate = date('Y-m-t', strtotime($nfdate));

    $this->db->select("SUM(CASE WHEN e_status='Present' AND status='Off Day' THEN 1 ELSE 0 END) AS rl");
    $this->db->where('employee_id', $userid);
    $this->db->where('e_status', 'Present');
    $this->db->where('status', 'Off Day');
    $this->db->where('attendance_date >=', $nfdate);
    $this->db->where('attendance_date <=', $nsdate);
    $query = $this->db->get('xin_attendance_time')->row();
    if (!empty($query) && $query->rl >= $rl_rule) {
        $rlv = floor($query->rl / $rl_rule);
    } else {
        $rlv = 0;
    }
  ?>

<div class="row m-b-1">
  <div class="col-md-5">
    <section id="decimal">
      <div class="row">
        <div class="col-md-12">
          <div class="box">
              <div class="box-header with-border">
                <h3 class="box-title"> <?php echo $this->lang->line('xin_leave_detail');?> </h3>
              </div>
            <div class="box-body">
            <?php $attributes = array('name' => 'update_status', 'autocomplete' => 'off');?>
				        <?php $hidden = array('emp_id' => $employee_id, 'user_id' => $session['user_id'], '_token_status' => $leave_id);?>
                <?php echo form_open('admin/timesheet/update_leave_status/'.$leave_id, $attributes, $hidden);?>
                <div class="table-responsive" data-pattern="priority-columns">
                  <table class="table table-striped m-md-b-0">
                    <tbody>
                      <tr>
                        <th scope="row" style="border-top:0px;"><?php echo $this->lang->line('xin_employee');?></th>
                        <td class="text-right"><?php echo $full_name;?></td>
                      </tr>
                      <tr>
                        <th scope="row" style="border-top:0px;"><?php echo $this->lang->line('left_department');?></th>
                        <td class="text-right"><?php echo $department_name;?></td>
                      </tr>
                      <tr>
                        <th scope="row"><?php echo $this->lang->line('xin_leave_type');?></th>
                        <td class="text-right">

                          <select  id="leave_type" name="leave_type" data-plugin="select_hrm" data-placeholder="
                            <?php echo $this->lang->line('xin_leave_type');?>">
                            <option value="<?= $leave_type_id?> "><?php echo $type;?></option>
                            <option value=""> Select Leave Type </option>
                            <option value="1" <?=($earn == 0)? 'disabled':'' ?>> Casual Leave (<?=$earn?>)</option>
                            <option value="2" <?=($sick == 0)? 'disabled':'' ?>> Sick Leave (<?=$sick?>)</option>
                            <option value="3" <?=($rlv == 0)? 'disabled':'' ?>> Replacement (<?=$rlv?>)</option>
                          </select>
                        </td>
                      </tr>

                      <tr>
                        <th scope="row"><?php echo $this->lang->line('xin_applied_on');?></th>
                        <td class="text-right"><?php echo $this->Xin_model->set_date_format($created_at);?></td>
                      </tr>
                      <tr>
                        <th scope="row">Applied from date</th>
                        <td class="text-right">
                          <input type="text" readonly value="<?php echo date('Y-m-d', strtotime($this->Xin_model->set_date_format($applyed_from_date))); ?>" />
                        </td>
                      </tr>

                      <tr>
                        <th scope="row">Applied to date</th>
                        <td class="text-right">
                          <input type="text" readonly value="<?php echo date('Y-m-d', strtotime($this->Xin_model->set_date_format($applyed_to_date))); ?>" />
                        </td>
                      </tr>
                      <tr>
                        <th scope="row"><?php echo $this->lang->line('xin_start_date');?></th>
                        <td class="text-right">
                          <input type="date" name="start_date"  id="start_date" value="<?php echo date('Y-m-d', strtotime($this->Xin_model->set_date_format($from_date))); ?>" />
                        </td>
                      </tr>

                      <tr>
                        <th scope="row"><?php echo $this->lang->line('xin_end_date');?></th>
                        <td class="text-right">
                          <input type="date" name="end_date" id="end_date" value="<?php echo date('Y-m-d', strtotime($this->Xin_model->set_date_format($to_date))); ?>" />
                        </td>
                      </tr>


                      <tr>
                        <th scope="row"><?php echo $this->lang->line('xin_attachment');?></th>
                        <td class="text-right">
                        <?php if($leave_attachment!='' && $leave_attachment!='NULL'):?>
                        <a href="<?= base_url('/').$leave_attachment ?>" download><?php echo $this->lang->line('xin_download');?></a>
                        <a href="<?= base_url('/').$leave_attachment ?>" > View </a>
                        <?php else:?>

                        <?php endif;?></td>
                      </tr>
                      <tr>
                        <th scope="row"><?php echo $this->lang->line('xin_hrsale_total_days');?></th>
                        <td class="text-right">
                           <label for="leave_half_day">Leave Half Day</label>
                            <input type="checkbox" <?= ($is_half_day == 1)? 'checked':'';?>  value="<?= $is_half_day; ?>" id="leave_half_day" name="leave_half_day">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                           <input type="number" id="day" name="day" value="<?=$day?>" style="width: 60px;">
                        </td>
                      </tr>
                    </tbody>
                  </table>
                  <div style="margin: 6px;width: 90%;border: 1px solid black;padding: 5px;border-radius: 8px;" class="bs-callout-success callout-border-left callout-square callout-transparent mt-1 p-1"> <?php echo $reason;?> </div>
                </div>
            </div>
          </div>
        </div>
      </div>
    </section>
  </div>

  <div class="col-md-4">
    <section id="decimal">
      <div class="row">
        <div class="col-md-12">
          <div class="box">
            <div class="box-header with-border">
              <h3 class="box-title"> <?php echo $this->lang->line('xin_update_status');?> </h3>
            </div>
            <div class="box-body">
              <?php //dd($user[0]->user_role_id); ?>

                <div class="row">
                  <div class="col-md-12">
                    <?php  if($user[0]->user_role_id == 1 || $user[0]->user_role_id == 2 || $user[0]->user_role_id == 4) {?>
                    <div class="form-group">
                      <label for="status"><?php echo $this->lang->line('dashboard_xin_status');?></label>
                      <select class="form-control" name="status" data-plugin="select_hrm" data-placeholder="<?php echo $this->lang->line('dashboard_xin_status');?>">
                        <option value="1" <?php if($status=='1'):?> selected <?php endif; ?>><?php echo $this->lang->line('xin_pending');?></option>
                        <option value="4" <?php if($status=='4'):?> selected <?php endif; ?>><?php echo $this->lang->line('xin_role_first_level_approval');?></option>
                        <?php if ($user[0]->user_role_id != 4) { ?>
                        <option value="2" <?php if($status=='2'):?> selected <?php endif; ?>><?php echo $this->lang->line('xin_approved');?></option>
                        <?php } ?>
                        <option value="3" <?php if($status=='3'):?> selected <?php endif; ?>><?php echo $this->lang->line('xin_rejected');?></option>
                      </select>
                    </div>
                    <?php } else {?>
                    <div class="form-group">
                      <label for="status"><?php echo $this->lang->line('dashboard_xin_status');?></label>
                      <select class="form-control" name="status" disabled >
                        <option value="1" <?php echo ($status=='1')? "selected":""; ?>><?php echo $this->lang->line('xin_pending');?></option>
                        <option value="2" <?php echo ($status=='2')? "selected":""; ?>><?php echo $this->lang->line('xin_approved');?></option>
                        <option value="3" <?php echo ($status=='3')? "selected":""; ?>><?php echo $this->lang->line('xin_rejected');?></option>
                        <option value="4" <?php echo ($status=='4')? "selected":""; ?>><?php echo $this->lang->line('xin_role_first_level_approval');?></option>
                      </select>
                    </div>
                  <?php } ?>
                  </div>
                </div>
                <div class="row">
                  <div class="col-md-12">
                    <div class="form-group">
                      <label for="remarks"><?php echo $this->lang->line('xin_remarks');?></label>
                      <textarea class="form-control textarea" placeholder="<?php echo $this->lang->line('xin_remarks');?>" name="remarks" id="remarks" cols="30" rows="5"><?php echo $remarks;?></textarea>
                    </div>
                  </div>
                </div>
                <?php if($user[0]->user_role_id != 3) {?>
                <div class="form-actions box-footer">
                  <button type="submit" class="btn btn-primary"> <i class="fa fa-check-square-o"></i> <?php echo $this->lang->line('xin_save');?> </button>
                </div>
                <?php }?>
              <?php echo form_close(); ?>
            </div>
          </div>
        </div>
      </div>
    </section>
  </div>

  <div class="col-md-3">
    <section id="decimal">
      <div class="row">
        <div class="col-md-12">
          <div class="box">
            <div class="box-header with-border">
              <h3 class="box-title"> <?php echo $this->lang->line('xin_last_taken_leave_title');?> </h3>
            </div>
            <div class="box-body">
              <div class="box-block card-dashboard">
                <div class="table-responsive" data-pattern="priority-columns">
                  <table class="table table-striped m-md-b-0">
                    <tbody>
                      <?php $show_last_leave = $this->Timesheet_model->employee_show_last_leave($employee_id,$leave_id); ?>
                      <?php foreach($show_last_leave as $last_leave) {
                        // get leave types
                        if($last_leave->leave_type_id == 1){
                          $type_name = 'Casual Leave';
                        } else if ($last_leave->leave_type_id == 2) {
                          $type_name = 'Sick Leave';
                        } else if ($last_leave->leave_type_id == 3) {
                          $type_name = 'Replacement';
                        } else {
                          $type_name = '--';
                        }
                        $datetime1 = new DateTime($last_leave->from_date);
                        $datetime2 = new DateTime($last_leave->to_date);
                        $interval = $datetime1->diff($datetime2);

                        if(strtotime($last_leave->from_date) == strtotime($last_leave->to_date)){
                          $last_leave_no_of_days =1;
                        } else {
                          $last_leave_no_of_days = $interval->format('%a') +1;
                        }

                        if($last_leave->is_half_day == 1){
                          $last_leave_day_info = $this->lang->line('xin_hr_leave_half_day');
                        } else {
                          $last_leave_day_info = $last_leave_no_of_days;
                        }
                			?>

                        <tr>
                          <th scope="row"><?php echo $this->lang->line('xin_leave_type');?></th>
                          <td class="text-right"><?php echo $type_name;?></td>
                        </tr>
                        <tr>
                          <th scope="row"><?php echo $this->lang->line('xin_applied_on');?></th>
                          <td class="text-right"><?php echo $this->Xin_model->set_date_format($last_leave->created_at);?></td>
                        </tr>
                        <tr>
                          <th scope="row"><?php echo $this->lang->line('xin_hrsale_total_days');?></th>
                          <td class="text-right"><?php echo $last_leave_day_info;?></td>
                        </tr>
                      <?php } ?>
                    </tbody>
                  </table>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>
  </div>
</div>

<style type="text/css">
.trumbowyg-editor { min-height:110px !important; }
</style>

<script>
  function calculateDays() {
    var startDate = new Date(document.getElementById('start_date').value);
    var endDate = new Date(document.getElementById('end_date').value);
    var checkpoint = document.getElementById('leave_half_day');



    // Calculate the time difference in milliseconds
    var timeDiff = Math.abs(endDate.getTime() - startDate.getTime());

    // Calculate the number of days
    var days = Math.ceil(timeDiff / (1000 * 3600 * 24));

    document.getElementById('day').value = days + 1;

    if (days+1 > 1) {
      checkpoint.setAttribute('disabled', 'disabled');
    } else {
      checkpoint.removeAttribute('disabled');
    }

  }

  // Event listeners for date input changes
  document.getElementById('start_date').addEventListener('change', calculateDays);
  document.getElementById('end_date').addEventListener('change', calculateDays);
</script>

<script>

function disableInput() {
    var leave_type = document.getElementById('leave_type');
    var start_date = document.getElementById('start_date');
    var end_date = document.getElementById('end_date');
    var day = document.getElementById('day');

    leave_type.setAttribute('disabled', 'disabled');
    start_date.setAttribute('disabled', 'disabled');
    end_date.setAttribute('disabled', 'disabled');
    day.setAttribute('disabled', 'disabled');
  }

var user_roll = <?= $session['role_id'] ?>;
if (user_roll === 3) {
      disableInput();
    }

console.log(user_roll);

</script>
<script>
 const checkbox = document.getElementById('leave_half_day');

checkbox.addEventListener('change', function() {
  if (this.checked) {
    document.getElementById('day').value = 0.5;
    day.setAttribute('disabled', 'disabled');
} else{
  document.getElementById('day').value = 1;
  day.removeAttribute('disabled', 'disabled');
}
});

</script>

