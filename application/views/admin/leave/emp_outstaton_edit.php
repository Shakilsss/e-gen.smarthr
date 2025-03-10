<?php $get_animate = $this->Xin_model->get_content_animate();?>
<?php $session = $this->session->userdata('username');?>

<div class="row">
    <div class="col-md-12">
        <?php if($this->session->flashdata('success')):?>
            <div class="alert alert-success">
                <?=$this->session->flashdata('success');;?>
            </div>
        <?php endif; ?>

        <div class="box mb-4 <?php echo $get_animate;?>">
            <div class="box-header with-border">
                <h3 class="box-title">Out Station Leave</h3>
                <div class="box-tools pull-right">
                    <a class="btn btn-xs btn-primary" href="<?= base_url('admin/leave/emp_outstaton_leave')?>"> back </a>
                </div>
            </div>

            <div class="box-body">
                <?php $attributes = array('autocomplete' => 'off');?>
                <?php $hidden = array('_user' => $session['user_id']);?>
                <?php echo form_open_multipart(current_url(), $attributes, $hidden);?>

                <div class="form-body">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label> From Date <span style="color:red">*</span></label>
                                <input required class="form-control" value="<?= $row->from_date ?>" type="date" name="from_date" >
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label> To Date <span style="color:red">*</span></label>
                                <input required class="form-control" value="<?= $row->to_date ?>" type="date" name="to_date" >
                            </div>
                        </div>
                        <?php $users = $this->db->where('user_id !=', $user['user_id'])->where('status',1)->get('xin_employees')->result(); ?>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label> Approver <span style="color:red">*</span></label>
                                <select name="control_person" class="form-control">
                                    <option value="">select one</option>
                                    <?php foreach($users as $user) { ?>
                                        <option <?= $user->user_id==$row->control_person? 'selected':'' ?> value="<?=$user->user_id?>"><?=$user->first_name.' '.$user->last_name?></option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label> Status <span style="color:red">*</span></label>
                                <select required name="status" class="form-control">
                                    <option value="">select one</option>
                                    <option <?= $row->status==1? 'selected':'' ?> value="1">Draft</option>
                                    <option <?= $row->status==2? 'selected':'' ?> value="2">Forward to Approver</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label> Remark <span style="color:red">*</span></label>
                                <textarea required class="form-control textarea" name="remark"> <?= $row->remark ?> </textarea>
                            </div>
                        </div>
                    </div>

                    <div class="form-actions">
                        <button name="hrsale_form" type="submit" class="btn btn-primary"><i class="fa fa fa-check-square-o"></i> Save</button>
                    </div>
                </div>
                <br>
                <?php echo form_close(); ?>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function() {
        // get designations
        $('[data-plugin="select_hrm"]').select2($(this).attr('data-options'));
        $('[data-plugin="select_hrm"]').select2({
            width: '100%'
        });
    });
</script>
