<?php $get_animate = $this->Xin_model->get_content_animate();?>
<?php $session = $this->session->userdata('username');?>

<div class="row">
  <div class="col-md-12">
    <div class="card">

        <div class="box mb-4 <?php echo $get_animate;?>">
            <div id="accordion">
                <div class="box-header  with-border">
                    <h3 class="box-title">Create Schedule</h3>
                    <div class="box-tools pull-right">
                        <a class="text-dark collapsed" data-toggle="collapse" href="#add_form" aria-expanded="false">
                            <button type="button" class="btn btn-xs btn-primary"> <span class="ion ion-md-add"></span>
                                <?php echo $this->lang->line('xin_add_new');?>
                            </button>
                        </a>
                    </div>
                </div>

                <div id="add_form" class="collapse add-form <?php echo $get_animate;?>" data-parent="#accordion" style="">
                    <div class="box-body">
                        <?php $attributes = array('name' => 'add_employee', 'id' => 'xin-form', 'autocomplete' => 'off');?>
                        <?php $hidden = array('_user' => $session['user_id']);?>
                        <?php echo form_open_multipart('admin/employees/add_employee', $attributes, $hidden);?>

                        <div class="form-body">
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="first_name"><?php echo $this->lang->line('xin_employee_first_name');?><i
                                                class="hrsale-asterisk"><span style="color:red">*</span></i></label>
                                        <input class="form-control"
                                            placeholder="<?php echo $this->lang->line('xin_employee_first_name');?>"
                                            name="first_name" type="text" value="">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="last_name"
                                            class="control-label"><?php echo $this->lang->line('xin_employee_last_name');?><span
                                                style="color:red" class="hrsale-asterisk">*</span></label>
                                        <input class="form-control"
                                            placeholder="<?php echo $this->lang->line('xin_employee_last_name');?>"
                                            name="last_name" type="text" value="">
                                    </div>
                                </div>

                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label for="employee_id"
                                            class="control-label"><?php echo $this->lang->line('dashboard_employee_id');?><i
                                                class="hrsale-asterisk"><span style="color:red">*</span></i></label>
                                        <input class="form-control"
                                            placeholder="<?php echo $this->lang->line('dashboard_employee_id');?>"
                                            name="employee_id" type="text" value="">
                                    </div>
                                </div>

                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label for="proxi_id" class="control-label">Punch id<i class="hrsale-asterisk"><span
                                                    style="color:red">*</span></i></label>
                                        <input class="form-control" placeholder="Punch device id" name="proxi_id" type="text"
                                            value="" required>
                                    </div>
                                </div>

                                <input type="hidden" name="company_id" value="1">
                                <input type="hidden" name="location_id" value="1">
                            </div>

                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="date_of_joining"
                                            class="control-label"><?php echo $this->lang->line('xin_employee_doj');?><i
                                                class="hrsale-asterisk"></i></label>
                                        <input class="form-control date_of_joining" readonly
                                            placeholder="<?php echo $this->lang->line('xin_employee_doj');?>"
                                            name="date_of_joining" type="text" value="">
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="probition" class="control-label">Intern/Probation month<i
                                                class="hrsale-asterisk"><span style="color:red">*</span></i></label>
                                        <input class="form-control" placeholder="Number of probition month" name="probation"
                                            type="text" value="">
                                    </div>
                                </div>

                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label for="status"><?php echo $this->lang->line('dashboard_xin_status');?><i
                                                class="hrsale-asterisk"><span style="color:red">*</span></i></label>
                                        <select name="status" id="status" class="form-control" data-plugin="select_hrm">
                                            <option> Select Status </option>
                                            <option value="4">Internship</option>
                                            <option value="5">Probation</option>
                                            <option value="1">Regular</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label for="floor_status">Floor Set<i class="hrsale-asterisk"><span
                                                    style="color:red">*</span></i></label>
                                        <select name="floor_status" id="floor_status" class="form-control">
                                            <option>Select Floor</option>
                                            <option value="3">3 <sup>rd</sup></option>
                                            <option value="5">5 <sup>th</sup></option>
                                        </select>
                                    </div>
                                </div>

                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label for="date_of_birth"><?php echo $this->lang->line('xin_employee_dob');?><i
                                                class="hrsale-asterisk"><span style="color:red">*</span></i></label>
                                        <input class="form-control date_of_birth" readonly
                                            placeholder="<?php echo $this->lang->line('xin_employee_dob');?>"
                                            name="date_of_birth" type="text" value="">
                                    </div>
                                </div>

                            </div>

                            <div class="row">
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label for="xin_hr_leave_cat">Employee or Lead</label>
                                        <select class="form-control" name="is_emp_lead">
                                            <option value="1">Employee</option>
                                            <option value="2">Team Lead</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="contact_no"
                                            class="control-label"><?php echo $this->lang->line('xin_contact_number');?><i
                                                class="hrsale-asterisk"><span style="color:red">*</span></i></label>
                                        <input class="form-control"
                                            placeholder="<?php echo $this->lang->line('xin_contact_number');?>"
                                            name="contact_no" type="text" value="">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="username">Username<i class="hrsale-asterisk"><span
                                                    style="color:red">*</span></i></label>
                                        <input class="form-control" placeholder="Username" name="username" type="text" value="">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label
                                            for="xin_employee_password"><?php echo $this->lang->line('xin_employee_password');?><i
                                                class="hrsale-asterisk"><span style="color:red">*</span></i></label>
                                        <input class="form-control"
                                            placeholder="<?php echo $this->lang->line('xin_employee_password');?>"
                                            name="password" type="text" value="">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="confirm_password"
                                            class="control-label"><?php echo $this->lang->line('xin_employee_cpassword');?><i
                                                class="hrsale-asterisk"><span style="color:red">*</span></i></label>
                                        <input class="form-control"
                                            placeholder="<?php echo $this->lang->line('xin_employee_cpassword');?>"
                                            name="confirm_password" type="text" value="">
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="address"><?php echo $this->lang->line('xin_employee_address');?></label>
                                        <input type="text" class="form-control"
                                            placeholder="<?php echo $this->lang->line('xin_employee_address');?>"
                                            name="address">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="address">Permanent Address</label>
                                        <input type="text" class="form-control" placeholder="Enter Permanent Address"name="per_address">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php echo form_close(); ?>
                    </div>
                </div>
            </div>
        </div>

    </div>
  </div>
</div>
